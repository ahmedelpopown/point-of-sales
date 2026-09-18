<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EmployeeAuthService
{
    public function login(
        string $email,
        string $password,
        bool $remember = false
    ): void {
        $credentials = [
            'email' => $email,
            'password' => $password,
            'status' => 'active',
        ];

        $success = Auth::guard('employee')->attempt(
            $credentials,
            $remember
        );

        if (! $success) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        request()->session()->regenerate();
    }

    public function logout(): void
    {
        Auth::guard('employee')->logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();
    }
}