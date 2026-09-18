 





@include('layout.head')
@include('layout.header')
@include('layout.nav')
@include('layout.sidebar')


<div class="content-wrapper">
 
  <section class="content">
  {{ $slot }}
  </section>
</div>
   @livewireScripts
@include('layout.footer')