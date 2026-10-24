 





@include('layout.head')
@include('layout.header')
@include('layouts.partials.navbar')
@include('layout.sidebar')
@push('script')
  <script>
        function themeManager() {
            return {
                theme: localStorage.getItem('theme') ?? 'light',

                setTheme(value) {
                    this.theme = value;

                    localStorage.setItem('theme', value);

                    document.documentElement.classList.toggle(
                        'dark',
                        value === 'dark'
                    );
                },

                toggle() {
                    this.setTheme(
                        this.theme === 'dark'
                            ? 'light'
                            : 'dark'
                    );
                }
            }
        }
    </script>
@endpush('script')

<div class="content-wrapper">
    <section class="content">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </section>
</div>
   @livewireScripts
@include('layout.footer')