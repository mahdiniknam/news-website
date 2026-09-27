<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\BaleLinkCode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * تست وب‌هوک ربات بله: خوش‌آمدگویی /start، راهنما برای پیام غیرکد و اتصال با کد ۸ رقمی.
 */
class BaleWebhookTest extends TestCase
{
    use DatabaseTransactions;

    protected string $secret = 'test-webhook-secret';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.bale.webhook_secret' => $this->secret]);

        foreach (['super-admin', 'admin', 'author'] as $roleName) {
            Role::findOrCreate($roleName, 'admin');
        }
    }

    protected function webhookPayload(string $text): array
    {
        return [
            'message' => [
                'message_id' => 1,
                'date' => time(),
                'chat' => ['id' => 555000111, 'type' => 'private'],
                'from' => ['id' => 555000111, 'is_bot' => false, 'first_name' => 'کاربر'],
                'text' => $text,
            ],
        ];
    }

    protected function postUpdate(array $payload)
    {
        // وب‌هوک بله همیشه JSON می‌فرستد
        return $this->postJson("/bale-webhook/{$this->secret}", $payload);
    }

    public function test_invalid_secret_returns_404(): void
    {
        $this->postJson('/bale-webhook/wrong-secret', $this->webhookPayload('/start'))
            ->assertStatus(404);
    }

    public function test_start_command_triggers_welcome(): void
    {
        // اگر ربات ارسال واقعی انجام دهد، نادیده گرفته می‌شود (توکن تنظیم نیست)
        $this->postUpdate($this->webhookPayload('/start'))->assertOk();
    }

    public function test_valid_code_links_account(): void
    {
        $admin = Admin::create([
            'name' => 'خبرنگار اتصال',
            'email' => 'link@test.local',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $admin->assignRole('author');

        $code = BaleLinkCode::generateFor($admin);

        $this->postUpdate($this->webhookPayload($code->code))->assertOk();

        $admin->refresh();

        $this->assertEquals('555000111', $admin->bale_chat_id, 'چت آیدی باید ذخیره شود');

        $code->refresh();
        $this->assertNotNull($code->used_at, 'کد باید مصرف شود');
        $this->assertEquals('555000111', $code->linked_chat_id);
    }

    public function test_code_cannot_be_used_twice(): void
    {
        $admin = Admin::create([
            'name' => 'خبرنگار اتصال دوم',
            'email' => 'link2@test.local',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $admin->assignRole('author');

        $code = BaleLinkCode::generateFor($admin);

        // اولین استفاده
        $this->postUpdate($this->webhookPayload($code->code))->assertOk();

        // چت آیدی متفاوت — استفاده مجدد باید شکست بخورد
        $payload = $this->webhookPayload($code->code);
        $payload['message']['chat']['id'] = 777888999;

        $this->postUpdate($payload)->assertOk();

        $code->refresh();
        $this->assertEquals(555000111, (int) $code->linked_chat_id, 'چت آیدی اول باید حفظ شود');
    }

    public function test_non_code_message_does_not_crash(): void
    {
        $this->postUpdate($this->webhookPayload('سلام ربات جانی'))
            ->assertOk();
    }

    public function test_non_message_update_is_ignored(): void
    {
        $this->postUpdate(['update_id' => 1])->assertOk();
    }
}
