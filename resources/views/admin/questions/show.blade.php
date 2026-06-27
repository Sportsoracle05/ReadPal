@extends('layouts.app')
@section('title', 'Questions')
@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 shadow rounded">
    <h2 class="text-2xl font-bold mb-4">Generated Questions</h2>

    @if($questions)
        <ol class="list-decimal pl-6 space-y-2">
            @foreach($questions as $question)
                <li class="border-b pb-2">{{ $question['question'] ?? $question }}</li>
            @endforeach
        </ol>
    @else
        <p>No questions found.</p>
    @endif
</div>
@endsection
