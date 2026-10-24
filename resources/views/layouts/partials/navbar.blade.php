@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;

    $navbarEmployee = Auth::guard('employee')->user();
    $navbarNotifications = collect();
    $navbarMessages = collect();
    $navbarUnreadCount = 0;
    $navbarUnreadMessagesCount = 0;

    // Notifications are optional until the Employee model uses Laravel's Notifiable trait
    // and the notifications table exists. The navbar should never break if not configured yet.
    if ($navbarEmployee && method_exists($navbarEmployee, 'notifications')) {
        try {
            $navbarNotifications = $navbarEmployee->notifications()->latest()->limit(8)->get();
            $navbarMessages = $navbarEmployee->notifications()
                ->where('data->category', 'message')
                ->latest()
                ->limit(5)
                ->get();
            $navbarUnreadCount = $navbarEmployee->unreadNotifications()->count();
            $navbarUnreadMessagesCount = $navbarEmployee->unreadNotifications()
                ->where('data->category', 'message')
                ->count();
        } catch (\Throwable $exception) {
            // Notifications are not installed/migrated yet. Render useful empty states instead.
            $navbarNotifications = collect();
            $navbarMessages = collect();
            $navbarUnreadCount = 0;
            $navbarUnreadMessagesCount = 0;
        }
    }
@endphp

<nav class="main-header navbar navbar-expand navbar-white navbar-light" aria-label="Main navigation">
    <ul class="navbar-nav align-items-center">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Toggle sidebar" title="Toggle sidebar">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </a>
        </li>

        @if (Route::has('contact.index'))
            <li class="nav-item d-none d-lg-inline-block">
                <a href="{{ route('contact.index') }}" class="nav-link">Contact</a>
            </li>
        @elseif (Route::has('support.index'))
            <li class="nav-item d-none d-lg-inline-block">
                <a href="{{ route('support.index') }}" class="nav-link">Support</a>
            </li>
        @endif

        <li class="nav-item d-inline-block ml-2">
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="nav-link btn btn-link border-0 bg-transparent d-flex align-items-center" style="gap:.45rem;">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                    <span class="d-none d-md-inline">Logout</span>
                </button>
            </form>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">
        {{-- Global search --}}
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button" aria-label="Open search" title="Search">
                <i class="fas fa-search" aria-hidden="true"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline" method="GET" action="{{ route('global-search.index') }}" role="search">
                    <div class="input-group input-group-sm w-100">
                        <input
                            class="form-control form-control-navbar"
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search products, orders, customers..."
                            aria-label="Search products, orders, customers"
                            minlength="2"
                            required
                        >
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit" aria-label="Submit search">
                                <i class="fas fa-search" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search" aria-label="Close search">
                                <i class="fas fa-times" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        {{-- Message notifications. These are real database notifications with category=message, not demo people. --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-label="Messages" title="Messages">
                <i class="far fa-comments" aria-hidden="true"></i>
                @if ($navbarUnreadMessagesCount > 0)
                    <span class="badge badge-danger navbar-badge">{{ $navbarUnreadMessagesCount > 99 ? '99+' : $navbarUnreadMessagesCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">Messages{{ $navbarUnreadMessagesCount ? ' (' . $navbarUnreadMessagesCount . ' unread)' : '' }}</span>
                <div class="dropdown-divider"></div>

                @forelse ($navbarMessages as $message)
                    @php
                        $messageData = is_array($message->data) ? $message->data : [];
                        $messageTitle = $messageData['title'] ?? 'New message';
                        $messageBody = $messageData['message'] ?? $messageData['body'] ?? 'You have a new message.';
                    @endphp
                    <form method="POST" action="{{ route('employee.notifications.read', $message->id) }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-left text-wrap">
                            <div class="d-flex align-items-start">
                                <span class="mr-3 mt-1 text-primary"><i class="far fa-comment-dots"></i></span>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong class="text-sm">{{ $messageTitle }}</strong>
                                        @if (!$message->read_at)
                                            <span class="badge badge-primary ml-2">New</span>
                                        @endif
                                    </div>
                                    <div class="text-muted text-sm text-truncate">{{ $messageBody }}</div>
                                    <div class="text-muted text-xs mt-1">{{ $message->created_at?->diffForHumans() }}</div>
                                </div>
                            </div>
                        </button>
                    </form>
                    <div class="dropdown-divider"></div>
                @empty
                    <div class="dropdown-item text-muted text-sm py-3">
                        <i class="far fa-comment-dots mr-2"></i>No messages yet.
                        <div class="small mt-1">Message items appear here when your app sends a notification with category <code>message</code>.</div>
                    </div>
                    <div class="dropdown-divider"></div>
                @endforelse

                @if (Route::has('messages.index'))
                    <a href="{{ route('messages.index') }}" class="dropdown-item dropdown-footer">Open messages</a>
                @endif
            </div>
        </li>

        {{-- Real Laravel database notifications --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-label="Notifications" title="Notifications">
                <i class="far fa-bell" aria-hidden="true"></i>
                @if ($navbarUnreadCount > 0)
                    <span class="badge badge-warning navbar-badge">{{ $navbarUnreadCount > 99 ? '99+' : $navbarUnreadCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">{{ $navbarUnreadCount }} unread notifications</span>
                <div class="dropdown-divider"></div>

                @forelse ($navbarNotifications as $notification)
                    @php
                        $notificationData = is_array($notification->data) ? $notification->data : [];
                        $notificationTitle = $notificationData['title'] ?? 'Notification';
                        $notificationBody = $notificationData['message'] ?? $notificationData['body'] ?? '';
                    @endphp
                    <form method="POST" action="{{ route('employee.notifications.read', $notification->id) }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-left text-wrap">
                            <div class="d-flex align-items-start">
                                <span class="mr-3 mt-1 {{ $notification->read_at ? 'text-muted' : 'text-warning' }}"><i class="fas fa-bell"></i></span>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong class="text-sm">{{ $notificationTitle }}</strong>
                                        @if (!$notification->read_at)
                                            <span class="badge badge-warning ml-2">New</span>
                                        @endif
                                    </div>
                                    @if ($notificationBody !== '')
                                        <div class="text-muted text-sm text-truncate">{{ $notificationBody }}</div>
                                    @endif
                                    <div class="text-muted text-xs mt-1">{{ $notification->created_at?->diffForHumans() }}</div>
                                </div>
                            </div>
                        </button>
                    </form>
                    <div class="dropdown-divider"></div>
                @empty
                    <div class="dropdown-item text-muted text-sm py-3">
                        <i class="far fa-bell-slash mr-2"></i>No notifications yet.
                    </div>
                    <div class="dropdown-divider"></div>
                @endforelse

                <form method="POST" action="{{ route('employee.notifications.read-all') }}" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item dropdown-footer" {{ $navbarUnreadCount === 0 ? 'disabled' : '' }}>
                        Mark all as read
                    </button>
                </form>
            </div>
        </li>

        {{-- AdminLTE fullscreen widget --}}
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button" aria-label="Toggle fullscreen" title="Fullscreen">
                <i class="fas fa-expand-arrows-alt" aria-hidden="true"></i>
            </a>
        </li>

    </ul>
</nav>
