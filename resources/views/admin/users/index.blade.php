@extends('layouts.admin')
@section('title','Users')
@section('page_title','User Management')
@section('page_sub','Super Admin · All registered students')

@section('content')

<div class="flex items-center justify-between mb-5 fu">
  <div>
    <h2 class="font-display text-xl font-bold text-white">Students</h2>
    <p class="text-xs text-ink-600 mt-0.5">{{ $users->total() }} registered users</p>
  </div>
  <a href="{{ route('admin.users.create') }}" class="btn-primary btn-sm">
    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
    Add User
  </a>
</div>

{{-- Search --}}
<form method="GET" class="flex gap-2 mb-4 fu1">
  <div class="relative max-w-xs flex-1">
    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-ink-600 pointer-events-none"
         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
    </svg>
    <input type="text" name="search" placeholder="Search users…"
           value="{{ request('search') }}" class="form-input pl-9 py-2 text-xs"/>
  </div>
  <button type="submit" class="btn-outline btn-sm">Search</button>
  @if(request('search'))<a href="{{ route('admin.users.index') }}" class="btn-outline btn-sm">Clear</a>@endif
</form>

<div class="a-card fu2 overflow-x-auto p-0">
  <table class="w-full">
    <thead>
      <tr class="border-b border-ink-800">
        <th class="tbl-head text-left">Name</th>
        <th class="tbl-head text-left">Email</th>
        <th class="tbl-head text-left">Matric No.</th>
        <th class="tbl-head text-center">Role</th>
        <th class="tbl-head text-right">Joined</th>
        <th class="tbl-head text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $user)
      <tr class="tbl-row">
        <td class="tbl-cell">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full bg-forest-950 border border-forest-900
                        flex items-center justify-center text-forest-400 text-xs font-bold flex-shrink-0">
              {{ strtoupper(substr($user->firstname ?? 'U', 0, 1)) }}
            </div>
            <div>
              <p class="text-xs font-medium text-ink-200">{{ $user->firstname }} {{ $user->lastname ?? '' }}</p>
            </div>
          </div>
        </td>
        <td class="tbl-cell text-xs">{{ $user->email }}</td>
        <td class="tbl-cell text-xs font-mono text-ink-500">{{ $user->matric_number ?? '—' }}</td>
        <td class="tbl-cell text-center">
          @php $r = $user->role ?? 'student'; @endphp
          <span class="role-chip {{ $r==='super'?'chip-super':($r==='rep'?'chip-rep':($r==='admin'?'chip-admin':'')) }}"
                style="{{ !in_array($r,['super','rep','admin']) ? 'background:rgba(71,85,105,.15);border:1px solid #334155;color:#64748b;' : '' }}">
            {{ ucfirst($r) }}
          </span>
        </td>
        <td class="tbl-cell text-right text-xs font-mono text-ink-600">
          {{ $user->created_at?->format('M j, Y') }}
        </td>
        <td class="tbl-cell text-right">
          <div class="flex items-center justify-end gap-1.5">
            <a href="{{ route('admin.users.edit', $user->username) }}" class="btn-outline btn-sm !px-2 !py-1 text-xs">Edit</a>
            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                  onsubmit="return confirm('Delete user {{ addslashes($user->firstname ?? '') }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn-danger btn-sm !px-2 !py-1 text-xs">Del</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="tbl-cell text-center text-ink-700 py-10">No users found.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($users->hasPages())
  <div class="flex items-center justify-between px-4 py-3 border-t border-ink-800">
    <p class="text-xs text-ink-600">{{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</p>
    <div class="flex gap-1">
      @if(!$users->onFirstPage())<a href="{{ $users->previousPageUrl() }}" class="btn-outline btn-sm">← Prev</a>@endif
      @if($users->hasMorePages())<a href="{{ $users->nextPageUrl() }}" class="btn-outline btn-sm">Next →</a>@endif
    </div>
  </div>
  @endif
</div>

@endsection