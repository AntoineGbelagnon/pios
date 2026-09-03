<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_requires_a_valid_email_code_before_authentication(): void
    {
        Notification::fake();
        // Une ancienne configuration globale ne doit jamais détourner le code
        // destiné à l'utilisateur qui vient de saisir ses identifiants.
        config(['auth.two_factor.default_email' => 'wrong-recipient@example.com']);

        $user = User::withoutGlobalScopes()->where('email', 'celinbell195@gmail.com')->firstOrFail();
        $user->update([
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('two-factor.create'));
        $this->assertGuest();

        $code = null;
        Notification::assertSentOnDemand(
            TwoFactorCodeNotification::class,
            function (TwoFactorCodeNotification $notification, array $channels, object $notifiable) use (&$code, $user): bool {
                $message = $notification->toMail($notifiable);
                foreach ($message->introLines as $line) {
                    if (is_string($line) && preg_match('/^\d{6}$/', $line)) {
                        $code = $line;
                    }
                }

                return in_array('mail', $channels, true)
                    && ($notifiable->routes['mail'] ?? null) === $user->email;
            }
        );

        $this->assertNotNull($code);
        $this->get(route('two-factor.create'))
            ->assertOk()
            ->assertSee('ce**********@gmail.com');

        $this->post(route('two-factor.verify'), ['code' => $code])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        // Force le rechargement depuis la session, comme sur la requete /dashboard du navigateur.
        $this->app->make('auth')->forgetGuards();
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_an_invalid_code_does_not_authenticate_the_user(): void
    {
        Notification::fake();
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->post(route('two-factor.verify'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_inactive_users_cannot_start_a_two_factor_challenge(): void
    {
        Notification::fake();
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
        Notification::assertNothingSent();
    }

    public function test_authenticated_default_administrator_can_open_the_dashboard(): void
    {
        $user = User::withoutGlobalScopes()
            ->where('email', 'celinbell195@gmail.com')
            ->firstOrFail();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('admin.dashboard');
    }
}
