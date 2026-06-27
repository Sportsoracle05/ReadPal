{{--
  karls/index.blade.php
  Landing page — simply renders the general thread inside the karls shell.
  The $general thread and $karls are injected by KarlsController::index().
--}}
@php
  // Pass the general thread as $thread so karls/thread.blade.php works
  $thread = $general;
@endphp

@include('karls.thread')