<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const SESSION_KEY = 'auth.two_factor';

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'login:'.strtolower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->withErrors([
                'email' => 'Trop de tentatives. Reessayez dans '.RateLimiter::availableIn($throttleKey).' secondes.',
            ])->onlyInput('email');
        }

        /** @var User|null $user */
        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if (! $user || ! Auth::getProvider()->validateCredentials($user, $credentials) || ! $user->is_active) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors(['email' => 'Ces identifiants sont invalides.'])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        try {
            $this->sendTwoFactorCode($request, $user, $request->boolean('remember'));
        } catch (\Throwable $exception) {
            report($exception);
            $request->session()->forget(self::SESSION_KEY);

            return back()->withErrors([
                'email' => 'Le code de securite n\'a pas pu etre envoye. Verifiez la configuration e-mail.',
            ])->onlyInput('email');
        }

        return redirect()->route('two-factor.create');
    }

    public function createTwoFactor(Request $request): View|RedirectResponse
    {
        $challenge = $request->session()->get(self::SESSION_KEY);

        if (! is_array($challenge) || empty($challenge['user_id'])) {
            return redirect()->route('login')->withErrors([
                'email' => 'Votre demande de connexion a expire. Veuillez recommencer.',
            ]);
        }

        return view('auth.two-factor-challenge', [
            'recipient' => $this->maskEmail($challenge['recipient'] ?? ''),
            'expiresInMinutes' => (int) config('auth.two_factor.expires_minutes', 10),
        ]);
    }

    public function verifyTwoFactor(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $challenge = $request->session()->get(self::SESSION_KEY);

        if (! is_array($challenge) || empty($challenge['user_id']) || empty($challenge['code_hash'])) {
            return redirect()->route('login')->withErrors([
                'email' => 'Votre demande de connexion a expire. Veuillez recommencer.',
            ]);
        }

        if (now()->timestamp > ($challenge['expires_at'] ?? 0)) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('login')->withErrors([
                'email' => 'Le code de securite a expire. Veuillez vous reconnecter.',
            ]);
        }

        $maxAttempts = (int) config('auth.two_factor.max_attempts', 5);
        $attempts = ((int) ($challenge['attempts'] ?? 0)) + 1;
        $challenge['attempts'] = $attempts;
        $request->session()->put(self::SESSION_KEY, $challenge);

        if ($attempts > $maxAttempts || ! Hash::check($data['code'], $challenge['code_hash'])) {
            if ($attempts >= $maxAttempts) {
                $request->session()->forget(self::SESSION_KEY);

                return redirect()->route('login')->withErrors([
                    'email' => 'Trop de codes incorrects. Veuillez vous reconnecter.',
                ]);
            }

            return back()->withErrors(['code' => 'Le code de securite est incorrect.']);
        }

        $user = User::withoutGlobalScopes()->find($challenge['user_id']);

        if (! $user || ! $user->is_active) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('login')->withErrors([
                'email' => 'Ce compte n\'est plus disponible.',
            ]);
        }

        Auth::login($user, (bool) ($challenge['remember'] ?? false));
        $request->session()->forget(self::SESSION_KEY);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function resendTwoFactor(Request $request): RedirectResponse
    {
        $challenge = $request->session()->get(self::SESSION_KEY);

        if (! is_array($challenge) || empty($challenge['user_id'])) {
            return redirect()->route('login');
        }

        $resendAfter = (int) config('auth.two_factor.resend_after_seconds', 60);
        $elapsed = now()->timestamp - (int) ($challenge['sent_at'] ?? 0);

        if ($elapsed < $resendAfter) {
            return back()->withErrors([
                'code' => 'Patientez encore '.($resendAfter - $elapsed).' secondes avant de demander un nouveau code.',
            ]);
        }

        $user = User::withoutGlobalScopes()->find($challenge['user_id']);

        if (! $user || ! $user->is_active) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('login');
        }

        try {
            $this->sendTwoFactorCode($request, $user, (bool) ($challenge['remember'] ?? false));
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors(['code' => 'Le nouveau code n\'a pas pu etre envoye.']);
        }

        return back()->with('status', 'Un nouveau code de securite a ete envoye.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->forget(self::SESSION_KEY);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function sendTwoFactorCode(Request $request, User $user, bool $remember): void
    {
        $code = (string) random_int(100000, 999999);
        $recipient = (string) $user->email;
        $expiresMinutes = (int) config('auth.two_factor.expires_minutes', 10);

        Notification::route('mail', $recipient)
            ->notify(new TwoFactorCodeNotification($code, $expiresMinutes));

        $request->session()->put(self::SESSION_KEY, [
            'user_id' => $user->getKey(),
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes($expiresMinutes)->timestamp,
            'attempts' => 0,
            'remember' => $remember,
            'recipient' => $recipient,
            'sent_at' => now()->timestamp,
        ]);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        if ($domain === '') {
            return $email;
        }

        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));

        return $visible.str_repeat('*', max(3, mb_strlen($local) - mb_strlen($visible))).'@'.$domain;
    }
}
