@extends('layouts.app')

@section('title', 'Results – ' . ($material->title ?? 'Assessment'))
@section('page_title', 'Assessment Results')
@section('page_sub', ($material->resource->course_code ?? '') . ' · Performance Summary')

@section('content')
<div class="max-w-2xl mx-auto">
    
    {{-- Main Results Card --}}
    <div class="app-card text-center fade-up">
        <div class="mb-6">
            @if($material->resource)
                <span class="rp-badge badge-green mb-2 inline-block">
                    {{ $material->resource->course_code }}
                </span>
            @endif
            <h2 class="font-display text-2xl font-bold text-white leading-tight">
                {{ $material->title }}
            </h2>
        </div>

        {{-- Score Visualization --}}
        <div class="relative py-10 px-6 rounded-2xl bg-ink-900/50 border border-ink-700/50 mb-8">
            <p class="text-xs uppercase tracking-widest text-ink-500 font-bold mb-2">Final Score</p>
            
            <div class="flex items-baseline justify-center gap-2">
                <span class="text-6xl font-display font-black text-forest-400 leading-none">
                    {{ $score }}
                </span>
                <span class="text-2xl font-mono text-ink-600">
                    / {{ $total }}
                </span>
            </div>

            @php
                $percentage = ($score / $total) * 100;
                $status = $percentage >= 50 ? 'Passed' : 'Needs Review';
                $statusClass = $percentage >= 50 ? 'badge-green' : 'bg-red-950/30 text-red-400 border-red-900/50';
            @endphp

            <div class="mt-6 flex justify-center">
                <span class="rp-badge {{ $statusClass }} px-4 py-1 text-sm">
                    {{ $status }} ({{ round($percentage) }}%)
                </span>
            </div>
        </div>

        {{-- Navigation Actions --}}
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <a href="{{ url()->previous() }}" 
               class="w-full sm:flex-1 px-6 py-3 rounded-xl bg-forest-800 border border-forest-700/50 
                      text-forest-100 text-sm font-bold hover:bg-forest-700 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Material
            </a>
            
            <a href="{{ route('materials.index') }}" 
               class="w-full sm:w-auto px-6 py-3 rounded-xl border border-ink-700 text-ink-400 text-sm font-semibold 
                      hover:border-ink-600 hover:text-ink-200 transition-colors">
                Dashboard
            </a>
        </div>
    </div>

    {{-- Motivational Footer --}}
    <p class="mt-8 text-center text-ink-600 text-xs font-mono fade-up-d2">
        Completed on {{ now()->format('M d, Y • h:i A') }}
    </p>
</div>
@endsection
