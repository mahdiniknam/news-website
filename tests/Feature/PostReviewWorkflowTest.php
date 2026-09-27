<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostReviewToken;
use App\Services\BaleBotService;
use App\Services\SendBaleMessage;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostReviewWorkflowTest extends TestCase
{
    // از DatabaseTransactions استفاده می‌کنیم تا دیتابیس پروژه پاک نشود
    // (کاربر دیتابیس فقط به یک schema دسترسی دارد و RefreshDatabase آن را از نو می‌سازد)
    use DatabaseTransactions;

    protected Admin $author;

    protected Admin $manager;

    protected function setUp(): void
    {
        parent::setUp();

        // صف را fake می‌کنیم تا پیام واقعی به API بله ارسال نشود
        Queue::fake();

        // نقش‌ها ممکن است از قبل (seed شده) موجود باشند
        foreach (['super-admin', 'admin', 'author'] as $roleName) {
            Role::findOrCreate($roleName, 'admin');
        }

        $this->author = Admin::create([
            'name' => 'خبرنگار تست',
            'email' => 'author@test.local',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'bale_chat_id' => '111222333',
        ]);
        $this->author->assignRole('author');

        $this->manager = Admin::create([
            'name' => 'مدیر تست',
            'email' => 'manager@test.local',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'bale_chat_id' => '999888777',
        ]);
        $this->manager->assignRole('admin');
    }

    protected function createPendingPost(): Post
    {
        $category = Category::create([
            'name' => 'اخبار شهر',
            'slug' => 'city-news-'.uniqid(),
            'status' => 1,
        ]);

        $post = new Post([
            'title' => 'خبر تستی برای بررسی',
            'content' => '<p>متن کامل خبر تستی</p>',
            'status' => 'pending',
            'type' => 'news',
            'category_id' => $category->id,
        ]);
        $post->author_id = $this->author->id;
        $post->save();

        return $post;
    }

    /** ساخت لینک بررسی شبیه کاری که PostNotifier انجام می‌دهد */
    protected function createReviewLink(Post $post): string
    {
        $token = \Illuminate\Support\Str::random(48);

        $post->reviewTokens()->create([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDays(3),
        ]);

        return $token;
    }

    public function test_review_page_renders_with_valid_token(): void
    {
        $post = $this->createPendingPost();
        $token = $this->createReviewLink($post);

        $response = $this->get(route('posts.review.show', ['token' => $token]));

        $response->assertOk();
        $response->assertSee('خبر تستی برای بررسی');
        $response->assertSee($this->author->name);
    }

    public function test_invalid_token_returns_gone(): void
    {
        $this->get(route('posts.review.show', ['token' => str_repeat('x', 48)]))
            ->assertStatus(410);
    }

    public function test_token_cannot_be_reused_after_decision(): void
    {
        $post = $this->createPendingPost();
        $token = $this->createReviewLink($post);

        // اولین تصمیم
        $this->post(route('posts.review.decide', ['token' => $token]), [
            'action' => 'approve',
        ])->assertRedirect(route('posts.review.result'));

        $post->refresh();
        $this->assertEquals('approved', $post->status);
        $this->assertNotNull($post->approved_at);

        // استفاده مجدد از همان لینک باید شکست بخورد
        $this->post(route('posts.review.decide', ['token' => $token]), [
            'action' => 'approve',
        ])->assertStatus(410);
    }

    public function test_expired_token_is_rejected(): void
    {
        $post = $this->createPendingPost();
        $token = \Illuminate\Support\Str::random(48);

        $post->reviewTokens()->create([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->subDay(),
        ]);

        $this->get(route('posts.review.show', ['token' => $token]))
            ->assertStatus(410);
    }

    public function test_reject_requires_reason(): void
    {
        $post = $this->createPendingPost();
        $token = $this->createReviewLink($post);

        $this->post(route('posts.review.decide', ['token' => $token]), [
            'action' => 'reject',
        ])->assertSessionHasErrors('rejection_reason');

        $this->assertEquals('pending', $post->fresh()->status);
    }

    public function test_reject_with_reason_changes_status(): void
    {
        $post = $this->createPendingPost();
        $token = $this->createReviewLink($post);

        $this->post(route('posts.review.decide', ['token' => $token]), [
            'action' => 'reject',
            'rejection_reason' => 'سند خبر کافی نیست؛ لطفاً منبع اضافه کنید',
        ])->assertRedirect(route('posts.review.result'));

        $post->refresh();
        $this->assertEquals('rejected', $post->status);
        $this->assertStringContainsString('سند خبر', $post->rejection_reason);
    }

    public function test_only_pending_posts_can_be_decided(): void
    {
        $post = $this->createPendingPost();
        $post->status = 'published';
        $post->save();

        $token = $this->createReviewLink($post);

        $this->post(route('posts.review.decide', ['token' => $token]), [
            'action' => 'approve',
        ])->assertRedirect();

        $this->assertEquals('published', $post->fresh()->status);
    }

    public function test_token_hash_is_stored_not_plain_token(): void
    {
        $post = $this->createPendingPost();
        $token = $this->createReviewLink($post);

        $this->assertDatabaseMissing('post_review_tokens', [
            'token_hash' => $token,
        ]);

        $this->assertDatabaseHas('post_review_tokens', [
            'token_hash' => hash('sha256', $token),
        ]);
    }

    public function test_bale_bot_service_reports_unconfigured(): void
    {
        config(['services.bale.bot_token' => null]);

        $bot = new BaleBotService();

        $this->assertFalse($bot->isConfigured());
        $this->assertFalse($bot->sendMessage('123', 'test'));
    }
}
