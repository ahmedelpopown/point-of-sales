<?php

use App\Services\EmployeeAuthService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Employee Login')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login(EmployeeAuthService $authService): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $authService->login(
            email: $this->email,
            password: $this->password,
            remember: $this->remember,
        );

        $employee = Auth::guard('employee')->user();

        if ($employee->can('view dashboard')) {
            $this->redirectRoute('dashboard');

            return;
        }

        Auth::guard('employee')->logout();

        $this->addError(
            'email',
            'Your account does not have access to the POS system.'
        );
    }
};
?>

<div class="login-page relative min-h-screen overflow-hidden bg-white">
    {{-- Soft page accents --}}
    <div aria-hidden="true" class="login-orb login-orb--one pointer-events-none absolute -left-28 -top-28 h-80 w-80 rounded-full bg-indigo-200/40 blur-3xl"></div>
    <div aria-hidden="true" class="login-orb login-orb--two pointer-events-none absolute -bottom-32 -right-24 h-96 w-96 rounded-full bg-blue-200/40 blur-3xl"></div>

    <main class="login-layout relative min-h-screen w-full bg-white">
        {{-- Branding panel --}}
        <section class="login-brand-panel relative isolate flex min-w-0 min-h-[250px] flex-col justify-between overflow-hidden bg-gradient-to-br from-slate-950 via-indigo-950 to-indigo-800 px-6 py-6 text-white sm:px-10 sm:py-8 lg:min-h-[100svh] lg:px-10 lg:py-9 xl:px-16 xl:py-10">
            <div aria-hidden="true" class="absolute -right-24 -top-20 -z-10 h-72 w-72 rounded-full border-[46px] border-white/[0.06]"></div>
            <div aria-hidden="true" class="absolute -bottom-28 -left-20 -z-10 h-80 w-80 rounded-full bg-blue-400/10 blur-2xl"></div>
            <div aria-hidden="true" class="absolute inset-0 -z-10 opacity-[0.08] [background-image:linear-gradient(rgba(255,255,255,0.45)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.45)_1px,transparent_1px)] [background-size:34px_34px]"></div>

            <div class="login-brand-logo flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-indigo-950 shadow-lg shadow-black/10">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="m8.2 12 2.4 2.4 5.3-5.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-bold tracking-tight">POS<span class="text-indigo-300">.Suite</span></p>
                    <p class="mt-0.5 text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-200/75">Employee workspace</p>
                </div>
            </div>

            <div class="login-brand-content mt-8 max-w-md lg:mx-auto lg:mt-0 lg:w-full lg:max-w-lg lg:py-2">
                <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/[0.07] px-3.5 py-2 text-xs font-medium text-indigo-100 backdrop-blur-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 ring-4 ring-emerald-400/15"></span>
                    Your workspace, all in one place
                </div>
                <h2 class="max-w-sm text-3xl font-bold leading-tight tracking-tight sm:text-4xl lg:text-[42px]">
                    Everything your business needs. <span class="text-indigo-300">One workspace.</span>
                </h2>
                <p class="mt-5 max-w-sm text-sm leading-7 text-indigo-100/75 sm:text-base">
                    Sign in to manage sales, keep track of stock, and stay on top of daily operations.
                </p>

                <div class="login-feature-grid mt-7 max-w-lg grid-cols-2 gap-3">
                    <div class="login-feature-card rounded-2xl border border-white/10 bg-white/[0.07] p-4 backdrop-blur-sm">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-indigo-200">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V5m0 14h16M7 15l4-4 3 2 5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold">Sales overview</p>
                        <p class="mt-1 text-xs leading-5 text-indigo-100/65">See your business at a glance.</p>
                    </div>
                    <div class="login-feature-card rounded-2xl border border-white/10 bg-white/[0.07] p-4 backdrop-blur-sm">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-indigo-200">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M4.5 7.8 12 12l7.5-4.2M12 12v8.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold">Stock control</p>
                        <p class="mt-1 text-xs leading-5 text-indigo-100/65">Keep products organized.</p>
                    </div>
                </div>
            </div>

            <p class="mt-8 text-xs text-indigo-100/50 lg:mt-0">A simpler way to run your day.</p>
        </section>

        {{-- Login form --}}
        <section class="login-form-panel relative z-10 flex min-w-0 min-h-[560px] items-center justify-center px-6 py-8 sm:px-10 sm:py-9 lg:min-h-[100svh] lg:px-10 lg:py-8 xl:px-16 2xl:px-20">
            <div class="login-form-content w-full max-w-md lg:max-w-lg">
                <div class="mb-6">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-600">Employee sign in</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-[34px]">Welcome back</h1>
                    <p class="mt-1.5 text-sm leading-6 text-slate-500">Enter your account details to access your workspace.</p>
                </div>

                <form wire:submit="login" class="space-y-4">
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email address</label>
                        <div class="group relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition group-focus-within:text-indigo-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="5" width="17" height="14" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="m5 7 7 5.5L19 7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <input
                                id="email"
                                type="email"
                                wire:model.blur="email"
                                autocomplete="email"
                                placeholder="you@company.com"
                                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                aria-describedby="email-error"
                                class="login-input block w-full rounded-xl border bg-slate-50/70 py-3 pl-12 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:bg-white focus:ring-4 {{ $errors->has('email') ? 'border-red-300 focus:border-red-400 focus:ring-red-100' : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-100' }}"
                            >
                        </div>
                        @error('email')
                            <p id="email-error" role="alert" class="mt-2 flex items-start gap-1.5 text-sm text-red-600">
                                <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-1-11a1 1 0 1 1 2 0v3a1 1 0 1 1-2 0V7Zm1 7a1.1 1.1 0 1 0 0-2.2A1.1 1.1 0 0 0 10 14Z" clip-rule="evenodd"/></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                        </div>
                        <div class="group relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition group-focus-within:text-indigo-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="10.5" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M12 14v2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </div>
                            <input
                                id="password"
                                type="password"
                                wire:model="password"
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                aria-describedby="password-error"
                                class="login-input block w-full rounded-xl border bg-slate-50/70 py-3 pl-12 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:bg-white focus:ring-4 {{ $errors->has('password') ? 'border-red-300 focus:border-red-400 focus:ring-red-100' : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-100' }}"
                            >
                        </div>
                        @error('password')
                            <p id="password-error" role="alert" class="mt-2 flex items-start gap-1.5 text-sm text-red-600">
                                <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-1-11a1 1 0 1 1 2 0v3a1 1 0 0 1-2 0V7Zm1 7a1.1 1.1 0 1 0 0-2.2A1.1 1.1 0 0 0 10 14Z" clip-rule="evenodd"/></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-0">
                        <label for="remember" class="inline-flex cursor-pointer items-center gap-2.5">
                            <input
                                id="remember"
                                type="checkbox"
                                wire:model="remember"
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 shadow-sm transition focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                            >
                            <span class="text-sm text-slate-600">Remember me</span>
                        </label>
                        <span class="text-xs font-medium text-slate-400">Authorized employees only</span>
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="login"
                        class="login-submit group flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 hover:shadow-indigo-700/25 focus:outline-none focus:ring-4 focus:ring-indigo-200 disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        <span wire:loading.remove wire:target="login">Sign in to your account</span>
                        <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity="0.3" stroke-width="3"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                            Signing in...
                        </span>
                        <svg wire:loading.remove wire:target="login" class="h-4 w-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </form>

                <div class="mt-5 flex items-start gap-3 rounded-xl border border-slate-200/80 bg-slate-50 px-4 py-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3 5 6v5c0 4.5 3 7.8 7 10 4-2.2 7-5.5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <p class="text-xs leading-5 text-slate-500">Use your assigned employee credentials. Access to the POS workspace depends on your account permissions.</p>
                </div>

                <p class="mt-5 text-center text-xs text-slate-400">&copy; {{ now()->year }} POS Suite. All rights reserved.</p>
            </div>
        </section>
    </main>


<style>
    .login-page {
        isolation: isolate;
    }

    /* Use plain CSS for the main layout so it does not depend on Tailwind's
       responsive-class generation. Mobile stacks; desktop uses exact halves. */
    .login-layout {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) !important;
        align-items: stretch;
    }

    .login-brand-panel,
    .login-form-panel {
        display: flex;
        width: auto !important;
        min-width: 0;
        max-width: none;
        opacity: 1;
    }

    .login-feature-grid {
        display: none;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    @media screen and (min-width: 1024px) {
        .login-layout {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
            min-height: 100vh;
            min-height: 100svh;
        }

        .login-brand-panel,
        .login-form-panel {
            min-height: 100vh;
            min-height: 100svh;
        }

        .login-feature-grid {
            display: grid !important;
        }
    }

    @media screen and (max-width: 1023.98px) {
        .login-brand-panel {
            min-height: 250px;
        }

        .login-form-panel {
            min-height: 560px;
        }
    }

    .login-brand-panel {
        animation: login-panel-in-left 700ms cubic-bezier(.22, 1, .36, 1) both;
    }

    .login-form-panel {
        animation: login-panel-in-right 700ms cubic-bezier(.22, 1, .36, 1) 100ms both;
    }

    /* Keep the panels visible if animations are unavailable or interrupted. */
    @supports not (animation-name: login-panel-in-left) {
        .login-brand-panel,
        .login-form-panel,
        .login-brand-logo,
        .login-brand-content,
        .login-form-content {
            opacity: 1;
            transform: none;
        }
    }

    .login-brand-logo {
        animation: login-fade-up 650ms cubic-bezier(.22, 1, .36, 1) 180ms both;
    }

    .login-brand-content {
        animation: login-fade-up 750ms cubic-bezier(.22, 1, .36, 1) 260ms both;
    }

    .login-form-content {
        animation: login-fade-up 750ms cubic-bezier(.22, 1, .36, 1) 220ms both;
    }

    .login-orb--one {
        animation: login-orb-drift 12s ease-in-out infinite alternate;
    }

    .login-orb--two {
        animation: login-orb-drift 15s ease-in-out 1s infinite alternate-reverse;
    }

    .login-feature-card {
        transition: transform 220ms ease, border-color 220ms ease, background-color 220ms ease;
    }

    .login-feature-card:hover {
        transform: translateY(-4px);
        border-color: rgb(255 255 255 / 22%);
        background-color: rgb(255 255 255 / 10%);
    }

    .login-input {
        transition: border-color 180ms ease, background-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
    }

    .login-input:focus {
        transform: translateY(-1px);
    }

    .login-submit {
        transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
    }

    .login-submit:not(:disabled):hover {
        transform: translateY(-2px);
    }

    .login-submit:not(:disabled):active {
        transform: translateY(0) scale(.99);
    }

    @keyframes login-panel-in-left {
        from { opacity: 0; transform: translateX(-22px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes login-panel-in-right {
        from { opacity: 0; transform: translateX(22px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes login-fade-up {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes login-orb-drift {
        from { transform: translate3d(0, 0, 0) scale(1); }
        to { transform: translate3d(20px, -14px, 0) scale(1.05); }
    }

    @media (prefers-reduced-motion: reduce) {
        .login-brand-panel,
        .login-form-panel,
        .login-brand-logo,
        .login-brand-content,
        .login-form-content,
        .login-orb--one,
        .login-orb--two {
            animation: none !important;
        }

        .login-feature-card,
        .login-input,
        .login-submit {
            transition: none !important;
        }
    }
</style>
</div>
