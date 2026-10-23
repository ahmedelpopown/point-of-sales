<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EmployeeNotificationController extends Controller
{
    public function markRead(string $notification): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($employee && method_exists($employee, 'notifications'), 403);

        $record = $employee->notifications()->whereKey($notification)->firstOrFail();
        $record->markAsRead();

        return redirect()->to($this->safeDestination($record->data['url'] ?? null));
    }

    public function markAllRead(): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($employee && method_exists($employee, 'unreadNotifications'), 403);

        $employee->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }

    private function safeDestination(mixed $destination): string
    {
        // Only accept local relative URLs from notification payloads to avoid open redirects.
        if (is_string($destination) && str_starts_with($destination, '/') && !str_starts_with($destination, '//')) {
            return $destination;
        }

        return route('dashboard');
    }
}
