<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

test('the login page uses a guest layout without the admin shell', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Employee sign in')
        ->assertSee('Show password')
        ->assertDontSee('main-sidebar')
        ->assertDontSee('main-header');
});

test('invalid employee credentials show the existing authentication error', function () {
    Livewire::test('pages::auth.login')
        ->set('email', 'employee@example.com')
        ->set('password', 'incorrect-password')
        ->call('login')
        ->assertHasErrors(['email'])
        ->assertSee('The provided credentials are incorrect.');
});

test('successful employee login still redirects to the dashboard', function () {
    $employee = Employee::factory()->create([
        'status' => 'active',
    ]);
    $employee->givePermissionTo(Permission::findOrCreate('view dashboard', 'employee'));

    $page = $this->get(route('login'))->assertOk();

    preg_match('/wire:snapshot="([^"]+)"/', $page->getContent(), $snapshotMatch);

    expect($snapshotMatch)->toHaveKey(1);

    $snapshot = html_entity_decode($snapshotMatch[1], ENT_QUOTES);
    preg_match('/<meta name="csrf-token" content="([^"]+)"/', $page->getContent(), $csrfMatch);

    expect($csrfMatch)->toHaveKey(1);

    $livewireResponse = $this->postJson(route('default-livewire.update'), [
        'components' => [[
            'snapshot' => $snapshot,
            'updates' => [
                'email' => $employee->email,
                'password' => 'password',
            ],
            'calls' => [[
                'method' => 'login',
                'params' => [],
            ]],
        ]],
    ], [
        'X-Livewire' => 'true',
        'X-CSRF-TOKEN' => html_entity_decode($csrfMatch[1], ENT_QUOTES),
    ]);

    $livewireResponse
        ->assertOk()
        ->assertJsonPath('components.0.effects.redirect', route('dashboard'));
});

test('authenticated admin pages retain the shared header and sidebar', function () {
    $employee = Employee::factory()->create([
        'status' => 'active',
    ]);

    $this->actingAs($employee, 'employee')
        ->get(route('admin.roles'))
        ->assertOk()
        ->assertSee('main-header')
        ->assertSee('main-sidebar')
        ->assertSee('logout');
});

test('html authorization failures render the custom forbidden page with status 403', function () {
    $employee = Employee::factory()->create([
        'status' => 'active',
    ]);

    $this->actingAs($employee, 'employee')
        ->get(route('dashboard'))
        ->assertForbidden()
        ->assertSee('Access Forbidden')
        ->assertSee('Go back')
        ->assertDontSee('main-sidebar');
});

test('employees with dashboard access can return there from a forbidden page', function () {
    $employee = Employee::factory()->create([
        'status' => 'active',
    ]);
    $employee->givePermissionTo(Permission::findOrCreate('view dashboard', 'employee'));

    $this->actingAs($employee, 'employee')
        ->get(route('products.index'))
        ->assertForbidden()
        ->assertSee('Return to dashboard')
        ->assertSee(route('dashboard'));
});

test('json authorization failures remain json responses with status 403', function () {
    $employee = Employee::factory()->create([
        'status' => 'active',
    ]);

    $this->actingAs($employee, 'employee')
        ->getJson(route('dashboard'))
        ->assertForbidden()
        ->assertHeader('content-type', 'application/json')
        ->assertJsonStructure(['message']);
});

test('unauthenticated dashboard access remains an authentication redirect', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('unauthenticated json access remains a 401 response', function () {
    $this->getJson(route('dashboard'))
        ->assertUnauthorized()
        ->assertJsonStructure(['message']);
});

test('missing pages retain the not found status', function () {
    $this->get('/missing-page')
        ->assertNotFound();
});

test('server errors retain the internal server error status', function () {
    Route::get('/test-server-error', function () {
        abort(500);
    });

    $this->get('/test-server-error')
        ->assertServerError();
});
