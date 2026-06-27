@extends('layouts.admin')

@section('head')
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--ink-950:#030712;--ink-900:#0c1120;--ink-800:#111827;--ink-700:#1c2639;--ink-600:#243044;--ink-400:#4b5563;--ink-300:#6b7280;--ink-200:#9ca3af;--forest-900:#14532d;--forest-800:#166534;--forest-700:#15803d;--forest-600:#16a34a;--forest-500:#22c55e;--forest-400:#4ade80;--bg:var(--ink-950);--surface:var(--ink-900);--surface-2:var(--ink-800);--border:var(--ink-600);--border-sub:var(--ink-700);--text:#f0f4f8;--text-m:var(--ink-200);--text-d:var(--ink-300);--accent:var(--forest-500);--accent-dim:rgba(34,197,94,.10);--accent-border:rgba(34,197,94,.22);--font-d:'Instrument Serif',Georgia,serif;--font-b:'Geist',system-ui,sans-serif;--font-m:'JetBrains Mono',monospace;--r:8px;--r-sm:5px}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:var(--bg);color:var(--text);font-family:var(--font-b)}
.page-wrap{max-width:900px;margin:0 auto;padding:28px 16px}
.page-head{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;gap:12px;flex-wrap:wrap}
.page-head h1{font-family:var(--font-d);font-size:26px;letter-spacing:-.02em;margin-bottom:4px}
.page-head p{font-size:13px;color:var(--text-m)}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--r-sm);font-family:var(--font-b);font-size:13px;font-weight:500;cursor:pointer;text-decoration:none;border:1px solid;transition:all .14s;white-space:nowrap}
.btn-primary{background:var(--forest-800);border-color:var(--forest-700);color:var(--forest-300)}
.btn-primary:hover{background:var(--forest-700);color:var(--text)}
.btn-ghost{background:none;border-color:var(--border);color:var(--text-m)}
.btn-ghost:hover{background:var(--surface-2);color:var(--text)}
.btn-danger{background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.2);color:#f87171}
.btn-danger:hover{background:rgba(239,68,68,.15)}
.btn-sm{padding:5px 10px;font-size:11px}
.flash{padding:10px 14px;border-radius:var(--r-sm);font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.flash.success{background:rgba(34,197,94,.06);border:1px solid rgba(34,197,94,.18);color:var(--forest-400)}

/* Table */
.table-wrap{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);overflow:hidden}
table{width:100%;border-collapse:collapse}
th{padding:11px 14px;background:var(--surface-2);font-family:var(--font-m);font-size:10px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--text-d);text-align:left;border-bottom:1px solid var(--border)}
td{padding:12px 14px;border-bottom:1px solid var(--border-sub);font-size:13px;vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(255,255,255,.015)}
.td-title{font-weight:500;color:var(--text);max-width:220px}
.td-title small{display:block;font-family:var(--font-m);font-size:10px;color:var(--text-d);margin-top:2px;font-weight:400}
.actions{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
.pub-badge{padding:2px 7px;border-radius:3px;font-family:var(--font-m);font-size:10px;border:1px solid}
.pub-badge.on{background:rgba(34,197,94,.08);border-color:rgba(34,197,94,.2);color:var(--forest-400)}
.pub-badge.off{background:rgba(107,114,128,.08);border-color:rgba(107,114,128,.2);color:var(--ink-300)}

/* Mobile cards (< 640px) */
@media(max-width:640px){
    table,thead,tbody,tr,th,td{display:block}
    thead{display:none}
    tr{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);margin-bottom:10px;padding:14px}
    td{padding:4px 0;border:none}
    td::before{content:attr(data-label);display:block;font-family:var(--font-m);font-size:9.5px;letter-spacing:.08em;text-transform:uppercase;color:var(--text-d);margin-bottom:3px}
    .table-wrap{background:none;border:none;padding:0}
    table{background:none}
}

/* Empty */
.empty-state{display:flex;flex-direction:column;align-items:center;padding:60px 20px;text-align:center}
.empty-icon{font-size:40px;opacity:.4;margin-bottom:14px}
.empty-title{font-family:var(--font-d);font-size:20px;margin-bottom:6px}
.empty-text{font-size:13px;color:var(--text-m);max-width:280px;line-height:1.6;margin-bottom:20px}
</style>
@endsection

@section('content')
<div class="page-wrap">

    <div class="page-head">
        <div>
            <h1>Assignments</h1>
            <p>Only admin can create. Students see published assignments only.</p>
        </div>
        <a href="{{ route('admin.assignments.create') }}" class="btn btn-primary">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            New Assignment
        </a>
    </div>

    @if(session('success'))
    <div class="flash success">✓ {{ session('success') }}</div>
    @endif

    @if($assignments->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">◈</div>
        <h2 class="empty-title">No assignments yet</h2>
        <p class="empty-text">Create the first assignment. Students will see it once published.</p>
        <a href="{{ route('admin.assignments.create') }}" class="btn btn-primary">Create Assignment</a>
    </div>
    @else

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Assignment</th>
                    <th>Sections</th>
                    <th>Submissions</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $a)
                <tr>
                    <td data-label="Assignment">
                        <div class="td-title">
                            {{ $a->title }}
                            <small style="font-family:var(--font-m)">{{ $a->course ?? $a->topic }}</small>
                        </div>
                    </td>
                    <td data-label="Sections">
                        <span style="font-family:var(--font-m);font-size:12px;color:var(--forest-400)">{{ $a->sections_count }}</span>
                    </td>
                    <td data-label="Submissions">
                        <a href="{{ route('admin.assignments.submissions', $a) }}"
                           style="font-family:var(--font-m);font-size:12px;color:var(--text-d);text-decoration:none;">
                            {{ $a->user_assignments_count }} student{{ $a->user_assignments_count !== 1 ? 's' : '' }}
                        </a>
                    </td>
                    <td data-label="Status">
                        <span class="pub-badge {{ $a->is_published ? 'on' : 'off' }}">
                            {{ $a->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td data-label="Actions">
                        <div class="actions">
                            <a href="{{ route('admin.assignments.edit', $a) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.assignments.toggle-publish', $a) }}" style="margin:0">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm">
                                    {{ $a->is_published ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.assignments.destroy', $a) }}"
                                  onsubmit="return confirm('Delete this assignment?')" style="margin:0">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif
</div>
@endsection
