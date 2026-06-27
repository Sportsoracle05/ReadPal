@extends('layouts.admin')

@section('title', 'Add Google Calendar Event')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-2xl font-semibold mb-4">Add Event to Google Calendar</h1>

    {{-- Show authorization button if token not set --}}
    @if (!session('google_access_token'))
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-4">
            <p>You need to connect your Google Calendar first.</p>
            <a href="{{ url('/admin/google/auth') }}" class="text-blue-600 underline">Authorize with Google</a>
        </div>
    @endif

    <form action="{{ url('/admin/google/add-event') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block mb-2 font-medium">Title</label>
            <input type="text" name="title" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-medium">Description</label>
            <textarea name="description" class="w-full border rounded p-2" rows="3"></textarea>
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-medium">Start Date & Time</label>
            <input type="datetime-local" name="start" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-medium">End Date & Time</label>
            <input type="datetime-local" name="end" class="w-full border rounded p-2" required>
        </div>

        <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Add to Google Calendar
        </button>
    </form>

    {{-- Embedded Google Calendar --}}
    <div class="mt-10">
        <h2 class="text-xl font-semibold mb-2">Your Google Calendar</h2>
        <iframe
            src="https://calendar.google.com/calendar/embed?src=youremail%40gmail.com&ctz=Africa%2FLagos"
            style="border:0" width="100%" height="600" frameborder="0" scrolling="no">
        </iframe>
    </div>
</div>
@endsection
