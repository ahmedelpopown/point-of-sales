<?php

use App\Services\EmployeeAuthService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.guest')]
#[Title('Employee Login')]
class extends Component
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

<div class="login-page min-h-screen bg-slate-50">
    <main class="login-layout min-h-screen w-full">
        {{-- Branding panel --}}
        <section class="login-brand-panel flex min-w-0 flex-col justify-between bg-slate-950 px-6 py-5 text-white sm:px-10 sm:py-8 lg:px-12 lg:py-10">
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

            <div class="login-brand-content mt-5 max-w-md lg:mx-auto lg:mt-0 lg:w-full lg:max-w-lg lg:py-2">
                <h2 class="max-w-sm text-2xl font-bold leading-tight tracking-tight sm:text-4xl lg:text-[42px]">
                    Your business, <span class="text-indigo-300">in one workspace.</span>
                </h2>
                <p class="mt-3 max-w-sm text-sm leading-6 text-slate-300 sm:text-base sm:leading-7">
                    Sign in to manage sales, stock, and daily operations.
                </p>
            </div>

            <p class="mt-5 hidden text-xs text-slate-400 lg:block">A simpler way to run your day.</p>
        </section>

        {{-- Login form --}}
        <section class="login-form-panel flex min-w-0 items-center justify-center bg-white px-6 py-8 sm:px-10 sm:py-9 lg:px-12 lg:py-8 xl:px-16">
            <div class="login-form-content w-full max-w-md lg:max-w-lg">
                <div class="mb-5">
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
                        <div class="group relative" x-data="{ showPassword: false }">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition group-focus-within:text-indigo-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="10.5" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M12 14v2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </div>
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                wire:model="password"
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                aria-describedby="password-error"
                                class="login-input block w-full rounded-xl border bg-slate-50/70 py-3 pl-12 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:bg-white focus:ring-4 {{ $errors->has('password') ? 'border-red-300 focus:border-red-400 focus:ring-red-100' : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-100' }}"
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                            >
                                <svg x-show="!showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/>
                                    <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/>
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6.1 0 9.5 6 9.5 6a15.4 15.4 0 0 1-3 3.4M6.2 6.7C3.8 8.2 2.5 12 2.5 12s3.4 6 9.5 6c1 0 1.9-.2 2.7-.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                </svg>
                            </button>
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
    .login-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        align-items: stretch;
    }

    .login-brand-panel,
    .login-form-panel {
        min-width: 0;
    }

    @media screen and (min-width: 1024px) {
        .login-layout {
            grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr);
        }

        .login-brand-panel,
        .login-form-panel {
            min-height: 100svh;
        }
    }

    @media screen and (max-width: 1023px) {
        .login-brand-panel {
            min-height: 160px;
        }
    }
</style>
</div>
