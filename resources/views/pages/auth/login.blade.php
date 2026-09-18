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
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
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

<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">

    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">

        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold text-gray-900">
                POS Login
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Sign in to your employee account
            </p>
        </div>

        <form wire:submit="login" class="space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Email
                </label>

                <input
                    type="email"
                    wire:model.blur="email"
                    autocomplete="email"
                    class="mt-1 block w-full rounded-lg border-gray-300"
                >

                @error('email')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Password
                </label>

                <input
                    type="password"
                    wire:model="password"
                    autocomplete="current-password"
                    class="mt-1 block w-full rounded-lg border-gray-300"
                >

                @error('password')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <label class="flex items-center gap-2">

                <input
                    type="checkbox"
                    wire:model="remember"
                    class="rounded border-gray-300"
                >

                <span class="text-sm text-gray-600">
                    Remember me
                </span>

            </label>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-3 font-semibold text-white hover:bg-indigo-700"
            >
                Login
            </button>

        </form>

    </div>

</div>