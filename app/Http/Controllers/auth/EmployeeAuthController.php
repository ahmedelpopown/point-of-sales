<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Services\EmployeeAuthService;
use Illuminate\Http\RedirectResponse;

class EmployeeAuthController extends Controller
{
    public function logout(
        EmployeeAuthService $authService
    ): RedirectResponse {
        $authService->logout();

        return redirect()->route('login');
    }
}