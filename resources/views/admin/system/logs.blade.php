@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 max-w-6xl mx-auto fade-up">
    
    {{-- ══ SCHEDULER STATUS ════════════════════ --}}
    <div class="app-card">
        <h3 class="font-display text-white font-bold mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-forest-500 animate-pulse"></span>
            Active Cron Jobs
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-ink-600 border-b border-ink-800">
                    <tr>
                        <th class="pb-3 px-2">COMMAND</th>
                        <th class="pb-3 px-2">INTERVAL</th>
                        <th class="pb-3 px-2">LAST RUN</th>
                        <th class="pb-3 px-2 text-right">NEXT RUN</th>
                        <th class="pb-3 px-2 text-right">ACTION</th> 
                    </tr>
                </thead>
                <tbody class="text-ink-300 font-mono">
                    @foreach($tasks as $task)
                    <tr class="border-b border-ink-800/50 hover:bg-ink-900/50">
                        <td class="py-3 px-2 text-forest-400">{{ $task['command'] }}</td>
                        <td class="py-3 px-2">{{ $task['interval'] }}</td>
                        <td class="py-3 px-2">{{ $task['last'] }}</td>
                        <td class="py-3 px-2 text-right text-white">{{ $task['next'] }}</td>
                        <td class="py-3 px-2 text-right">
                            <form method="POST" action="{{ route('admin.run.task') }}">
                                @csrf
                                <input type="hidden" name="command" value="{{ $task['command'] }}">
                                <button type="submit"
                                    class="px-2 py-1 rounded bg-forest-900/30 border border-forest-800 text-[10px] text-forest-400 hover:bg-forest-800 hover:text-white">
                                    Run Now
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- ══ RECENT LOGINS ════════════════════ --}}
        <div class="app-card">
            <h3 class="font-display text-white font-bold mb-4">User Login History</h3>
            <div class="space-y-2 max-h-[400px] overflow-y-auto pr-2">
                @forelse($logins as $line)
                    <div class="p-2.5 rounded-lg bg-ink-900/40 border border-ink-800 text-[10px] font-mono text-ink-500 leading-relaxed">
                        {{ $line }}
                    </div>
                @empty
                    <p class="text-xs text-ink-700 text-center py-8">No login records yet.</p>
                @endforelse
            </div>
        </div>

        {{-- ══ SYSTEM ERRORS ════════════════════ --}}
        <div class="app-card">
            <h3 class="font-display text-white font-bold mb-4 text-red-400">System Error Feed</h3>
            <div class="space-y-2 max-h-[400px] overflow-y-auto pr-2">
                @forelse($systemErrors as $error)
                    <div class="p-2.5 rounded-lg bg-red-950/10 border border-red-900/20 text-[10px] font-mono text-red-300/60 overflow-hidden text-ellipsis whitespace-nowrap hover:whitespace-normal">
                        {{ $error }}
                    </div>
                @empty
                    <p class="text-xs text-ink-700 text-center py-8">System is healthy. No errors.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
