@extends('layouts.guest')

@section('content')
    <div class="pios-auth-container">
        <div class="auth-login-layout">
            {{-- LEFT: Form Panel --}}
            <main class="auth-login-card">
                <div class="auth-login-panel">
                    <div class="auth-brand mb-4">
                        <span class="pios-brand-mark">P</span>
                        <span class="auth-brand-text">PIOS</span>
                    </div>

                    <h1 class="auth-login-heading">Gérez votre activité avec <span class="text-primary">confiance.</span></h1>
                    <p class="auth-login-subtitle">Welcome back! Please login to your account.</p>

                    <form method="POST" action="{{ route('login.store') }}" class="auth-login-form">
                        @csrf

                        <div class="auth-field mb-3">
                            <label for="email" class="auth-field-label">Email Address</label>
                            <input class="form-control" type="email" id="email" name="email"
                                value="{{ old('email') }}" placeholder="halworm@digital.com" required autofocus>
                        </div>

                        <div class="auth-field mb-3">
                            <label for="password" class="auth-field-label">Password</label>
                            <input class="form-control" type="password" id="password" name="password"
                                placeholder="••••••••••" required>
                        </div>

                        <div class="auth-login-options">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>
                            <a href="#" class="auth-login-link">Forgot Password?</a>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger auth-login-alert" role="alert">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="auth-login-actions">
                            <button class="btn btn-primary auth-login-button" type="submit">Login</button>
                            <a href="#" class="btn btn-outline-primary auth-login-button-outline" type="button">Sign Up</a>
                        </div>
                    </form>

                    <div class="auth-social-login">
                        <span class="auth-social-label">Or login with:</span>
                        <div class="auth-social-links">
                            <a href="#" class="auth-social-link">Facebook</a>
                            <a href="#" class="auth-social-link">LinkedIn</a>
                            <a href="#" class="auth-social-link">Google</a>
                        </div>
                    </div>
                </div>
            </main>

            {{-- RIGHT: Visual Panel --}}
            <aside class="auth-login-visual" aria-label="Présentation de la plateforme">
                <div class="auth-visual-illustration">
                    <svg viewBox="0 0 480 340" fill="none" xmlns="http://www.w3.org/2000/svg" class="auth-illustration-svg">
                        <circle cx="240" cy="170" r="120" fill="#E8ECFF" opacity="0.5"/>
                        <circle cx="180" cy="130" r="80" fill="#D6DEFF" opacity="0.4"/>
                        <circle cx="310" cy="150" r="60" fill="#C7D4FF" opacity="0.3"/>
                        <rect x="140" y="160" width="200" height="120" rx="16" fill="#3B5BDB" opacity="0.12"/>
                        <circle cx="240" cy="180" r="35" fill="#3B5BDB" opacity="0.25"/>
                        <path d="M220 170 L240 140 L260 170 L250 170 L250 200 L230 200 L230 170 Z" fill="#3B5BDB" opacity="0.35"/>
                        <circle cx="240" cy="220" r="20" fill="#3B5BDB" opacity="0.15"/>
                    </svg>
                </div>
            </aside>
        </div>
    </div>
@endsection
