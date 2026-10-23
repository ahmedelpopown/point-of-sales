@include('layout.head')
@include('layout.header')
@include('layouts.partials.navbar')
@include('layout.sidebar')


<div class="content-wrapper">
  <section class="content">
    @yield('content')
  </section>
</div>
@include('layout.footer')