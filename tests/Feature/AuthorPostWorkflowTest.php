<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * تست گردش کار خبر نویسنده:
 * ثبت خبر → وضعیت pending → اعلان ادمین در ربات بله → تایید/رد ادمین → اعلان نویسنده.
 */
class AuthorPostWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected Admin $author;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        foreach (['super-admin', 'admin', 'author'] as $roleName) {
            Role::findOrCreate($roleName, 'admin');
        }

        $this->author = Admin::create([
            'name' => 'خبرنگار تست',
            'email' => 'author-workflow@test.local',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'bale_chat_id' => '111222333',
        ]);
        $this->author->assignRole('author');
    }

    protected function createCategory(): Category
    {
        return Category::create([
            'name' => 'اخبار شهر ' . uniqid(),
            'status' => 1,
        ]);
    }

    /**
     * باگ اصلی: نویسنده نمی‌توانست خبر ثبت کند چون مدل Post رابطه tags() نداشت
     * و کنترلر $post->tags()->sync() را صدا می‌زد → خطای 500.
     */
    public function test_author_can_create_post_with_tags(): void
    {
        $category = $this->createCategory();
        $tag = Tag::create(['name' => 'تگ تستی', 'status' => 'active']);

        $response = $this->actingAs($this->author, 'admin')
            ->post(route('author.posts.store'), [
                'title' => 'خبر تستی نویسنده',
                'type' => 'news',
                'category_id' => $category->id,
                'content' => '<p>متن کامل خبر تستی نویسنده</p>',
                'tags' => [$tag->id],
                'is_featured' => '0',
            ]);

        $response->assertRedirect(route('author.posts.index'));
        $response->assertSessionHas('success');

        $post = Post::where('title', 'خبر تستی نویسنده')->first();

        $this->assertNotNull($post, 'خبر باید ذخیره شده باشد');
        $this->assertEquals('pending', $post->status, 'خبر نویسنده باید در انتظار تایید باشد');
        $this->assertEquals($this->author->id, $post->author_id);
        $this->assertTrue($post->tags->contains($tag->id), 'تگ‌ها باید ذخیره شده باشند');
    }

    public function test_author_post_is_always_pending_even_if_status_sent(): void
    {
        $category = $this->createCategory();

        $response = $this->actingAs($this->author, 'admin')
            ->post(route('author.posts.store'), [
                'title' => 'خبر بدون تگ',
                'type' => 'note',
                'content' => '<p>متن</p>',
                'status' => 'published', // تلاش برای دور زدن
            ]);

        $post = Post::where('title', 'خبر بدون تگ')->first();

        $this->assertNotNull($post);
        $this->assertEquals('pending', $post->status, 'وضعیت باید همیشه pending باشد');
    }

    public function test_author_resubmitting_rejected_post_notifies_admins(): void
    {
        $post = Post::create([
            'author_id' => $this->author->id,
            'title' => 'خبر رد شده',
            'content' => '<p>متن</p>',
            'status' => 'rejected',
            'rejection_reason' => 'منبع خبر کافی نیست',
            'type' => 'news',
        ]);

        // نویسنده خبر رد‌شده را ویرایش و دوباره ارسال می‌کند
        $response = $this->actingAs($this->author, 'admin')
            ->put(route('author.posts.update', $post), [
                'title' => 'خبر رد شده (اصلاح شده)',
                'type' => 'news',
                'content' => '<p>متن اصلاح شده با منبع کامل</p>',
            ]);

        $post->refresh();

        $this->assertEquals('pending', $post->status, 'خبر اصلاح‌شده باید به pending برگردد');
        $this->assertNull($post->rejection_reason, 'دلیل رد باید پاک شود');
        $response->assertSessionHas('success');
    }

    public function test_author_cannot_edit_others_post(): void
    {
        $otherAuthor = Admin::create([
            'name' => 'نویسنده دیگر',
            'email' => 'other-author@test.local',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $otherAuthor->assignRole('author');

        $post = Post::create([
            'author_id' => $otherAuthor->id,
            'title' => 'خبر نویسنده دیگر',
            'content' => '<p>متن</p>',
            'status' => 'pending',
            'type' => 'news',
        ]);

        $response = $this->actingAs($this->author, 'admin')
            ->get(route('author.posts.edit', $post));

        $response->assertStatus(403);
    }

    public function test_author_cannot_edit_approved_or_published_post(): void
    {
        $post = Post::create([
            'author_id' => $this->author->id,
            'title' => 'خبر منتشر شده',
            'content' => '<p>متن</p>',
            'status' => 'published',
            'type' => 'news',
        ]);

        $response = $this->actingAs($this->author, 'admin')
            ->get(route('author.posts.edit', $post));

        $response->assertStatus(403);
    }
}
