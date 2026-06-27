{{-- ============================================================
  admin/resources/index.blade.php  (Super Admin)
  ============================================================ --}}
@extends('layouts.admin')
@section('title','Resources')
@section('page_title','Course Resources')
@section('page_sub','Super Admin · Manage course resource folders')

@section('content')

<div class="flex items-center justify-between mb-5 fu">
  <div>
    <h2 class="font-display text-xl font-bold text-white">Resources</h2>
    <p class="text-xs text-ink-600 mt-0.5">{{ $resources->count() }} courses configured</p>
  </div>
  <a href="{{ route('admin.resources.create') }}" class="btn-primary btn-sm">
    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
    New Resource
  </a>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 fu1">
  @forelse($resources as $i => $res)
  <div class="a-card" style="animation-delay:{{ $i*.04 }}s">
    <div class="flex items-start justify-between mb-3">
      <div class="w-10 h-10 rounded-xl bg-forest-950 border border-forest-900 flex items-center justify-center flex-shrink-0">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
        </svg>
      </div>
      <span class="rp-badge badge-green text-xs">{{ $res->materials_count ?? 0 }} materials</span>
    </div>
    <h3 class="font-display text-base font-bold text-white leading-tight mb-0.5">
      {{ $res->course_code }}
    </h3>
    <p class="text-xs text-ink-500 mb-4 line-clamp-1">{{ $res->name }}</p>
    <div class="flex gap-2 border-t border-ink-800/60 pt-3">
      <a href="{{ route('admin.resources.edit', $res->slug) }}" class="btn-outline btn-sm flex-1 justify-center text-xs">Edit</a>
      <form method="POST" action="{{ route('admin.resources.destroy', $res->id) }}"
            onsubmit="return confirm('Delete {{ addslashes($res->course_code) }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn-danger btn-sm !px-3 text-xs">Del</button>
      </form>
    </div>
  </div>
  @empty
  <div class="a-card text-center py-10 col-span-full">
    <p class="text-ink-600">No resources created yet.</p>
  </div>
  @endforelse
</div>

@endsection