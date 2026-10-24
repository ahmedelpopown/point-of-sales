@extends('layouts.guest')

@section('content')
    @php
        $employee = auth()->guard('employee')->user();
        $canViewDashboard = $employee?->can('view dashboard') && \Illuminate\Support\Facades\Route::has('dashboard');
        $previousUrl = url()->previous();
        $previousHost = parse_url($previousUrl, PHP_URL_HOST);
        $safePreviousUrl = $previousHost === request()->getHost() && $previousUrl !== url()->current()
            ? $previousUrl
            : route('login');
    @endphp

    <main class="flex min-h-screen items-center justify-center px-5 py-12">
        <section class="w-full max-w-xl rounded-2xl border border-slate-200 bg-white p-7 text-center shadow-sm sm:p-10">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 10V7a6 6 0 0 1 12 0v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <rect x="4" y="10" width="16" height="11" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M12 14v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>

            <p class="mt-6 text-sm font-semibold uppercase tracking-[0.18em] text-indigo-600">Error 403</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Access Forbidden</h1>
            <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-600">
                You do not have permission to access this resource. If you believe this is a mistake, contact your administrator.
            </p>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                @if ($canViewDashboard)
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                        Return to dashboard
                    </a>
                    <a href="{{ $safePreviousUrl }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">
                        Return to previous page
                    </a>
                @elseif ($employee)
                    <a href="{{ $safePreviousUrl }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                        Go back
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                        Go to sign in
                    </a>
                    <a href="{{ $safePreviousUrl }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">
                        Return to previous page
                    </a>
                @endif
            </div>
        </section>
    </main>
@endsection
