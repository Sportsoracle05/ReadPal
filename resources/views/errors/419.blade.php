@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')

@section('message')
    {{ __('Page Expired. Redirecting you back...') }}
    
    <script>
        // Automatically reload the previous page after 2 seconds
        setTimeout(function() {
            window.location.href = "{{ url()->previous() }}";
        }, 2000);
    </script>
@endsection

