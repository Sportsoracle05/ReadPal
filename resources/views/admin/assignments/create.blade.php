{{--
    Admin-only: create a new assignment with dynamic sections
--}}
@extends('layouts.admin')

@section('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root {
    --ink-950:#030712;--ink-900:#0c1120;--ink-800:#111827;--ink-700:#1c2639;
    --ink-600:#243044;--ink-500:#374151;--ink-400:#4b5563;--ink-300:#6b7280;
    --ink-200:#9ca3af;--ink-100:#d1d5db;
    --forest-950:#052e16;--forest-900:#14532d;--forest-800:#166534;
    --forest-700:#15803d;--forest-600:#16a34a;--forest-500:#22c55e;
    --forest-400:#4ade80;--forest-300:#86efac;
    --bg:var(--ink-950);--surface:var(--ink-900);--surface-2:var(--ink-800);
    --border:var(--ink-600);--border-sub:var(--ink-700);
    --text:#f0f4f8;--text-m:var(--ink-200);--text-d:var(--ink-300);
    --accent:var(--forest-500);--accent-dim:rgba(34,197,94,.10);
    --accent-border:rgba(34,197,94,.22);--danger:#ef4444;
    --font-d:'Instrument Serif',Georgia,serif;
    --font-b:'Geist',system-ui,sans-serif;
    --font-m:'JetBrains Mono',monospace;
    --r:8px;--r-sm:5px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:var(--bg);color:var(--text);font-family:var(--font-b);min-height:100vh}

.page-wrap{max-width:780px;margin:0 auto;padding:24px 16px 80px}

/* Header */
.page-head{margin-bottom:28px}
.page-head h1{font-family:var(--font-d);font-size:26px;letter-spacing:-.02em;margin-bottom:5px}
.page-head p{font-size:13px;color:var(--text-m)}
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-d);font-family:var(--font-m);margin-bottom:14px}
.breadcrumb a{color:var(--forest-600);text-decoration:none}
.breadcrumb a:hover{color:var(--forest-400)}

/* Card */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:20px;margin-bottom:16px;position:relative}
.card-title{font-size:11px;font-family:var(--font-m);font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--forest-600);margin-bottom:16px;display:flex;align-items:center;gap:6px}
.card-title span{width:18px;height:18px;background:var(--accent-dim);border:1px solid var(--accent-border);border-radius:50%;display:grid;place-items:center;font-size:10px;color:var(--forest-400)}

/* Form */
.form-group{margin-bottom:14px}
.form-label{display:block;font-size:11px;font-family:var(--font-m);font-weight:500;letter-spacing:.06em;text-transform:uppercase;color:var(--text-d);margin-bottom:5px}
.form-label .req{color:var(--forest-500)}
.form-input,.form-select,.form-textarea{
    width:100%;background:var(--ink-800);border:1px solid var(--border);border-radius:var(--r-sm);
    padding:10px 12px;font-family:var(--font-b);font-size:13px;color:var(--text);outline:none;
    transition:border-color .14s,box-shadow .14s
}
.form-input::placeholder,.form-textarea::placeholder{color:var(--ink-400)}
.form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--accent-border);box-shadow:0 0 0 3px var(--accent-dim)}
.form-textarea{resize:vertical;min-height:80px;line-height:1.6}
.form-select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:30px;cursor:pointer}
.form-hint{font-size:11px;color:var(--ink-400);font-family:var(--font-m);margin-top:4px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media(max-width:500px){.form-row{grid-template-columns:1fr}}
.form-error{font-size:11px;color:#f87171;font-family:var(--font-m);margin-top:4px}

/* Toggle */
.toggle-group{display:flex;align-items:center;gap:10px}
.toggle-label{display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--text-m)}
.toggle-label input[type=checkbox]{accent-color:var(--forest-500);width:16px;height:16px}

/* Section builder */
.sections-list{display:flex;flex-direction:column;gap:12px}

.section-item{
    background:var(--ink-800);border:1px solid var(--border-sub);border-radius:var(--r);
    padding:16px;position:relative;
    border-left:3px solid var(--forest-900);
    transition:border-color .15s
}
.section-item:focus-within{border-left-color:var(--forest-600)}

.section-header{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.section-drag{cursor:grab;color:var(--ink-500);font-size:14px;flex-shrink:0;padding:2px 4px;user-select:none}
.section-num{font-family:var(--font-m);font-size:10px;color:var(--forest-600);background:var(--accent-dim);border:1px solid var(--accent-border);border-radius:3px;padding:2px 7px;flex-shrink:0}
.section-title-flex{flex:1;min-width:0}
.section-remove{width:28px;height:28px;background:none;border:1px solid var(--border);border-radius:var(--r-sm);color:var(--ink-400);cursor:pointer;display:grid;place-items:center;flex-shrink:0;transition:all .14s}
.section-remove:hover{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.3);color:#f87171}

.questions-area{margin-top:10px}
.questions-label{font-size:10px;font-family:var(--font-m);letter-spacing:.08em;text-transform:uppercase;color:var(--text-d);margin-bottom:5px}

/* Add section button */
.add-section-btn{
    display:flex;align-items:center;justify-content:center;gap:8px;
    width:100%;padding:12px;background:none;
    border:1px dashed var(--border);border-radius:var(--r);
    color:var(--forest-600);font-family:var(--font-b);font-size:13px;font-weight:500;
    cursor:pointer;transition:all .15s;margin-top:4px
}
.add-section-btn:hover{border-color:var(--forest-700);background:var(--accent-dim);color:var(--forest-400)}

/* Submit bar */
.submit-bar{
    position:fixed;bottom:0;left:0;right:0;
    background:rgba(12,17,32,.95);backdrop-filter:blur(10px);
    border-top:1px solid var(--border);padding:12px 16px;
    display:flex;align-items:center;justify-content:space-between;gap:10px;
    z-index:100
}
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:var(--r-sm);font-family:var(--font-b);font-size:13px;font-weight:500;cursor:pointer;text-decoration:none;transition:all .14s;border:1px solid;white-space:nowrap}
.btn-primary{background:var(--forest-800);border-color:var(--forest-700);color:var(--forest-300)}
.btn-primary:hover{background:var(--forest-700);border-color:var(--forest-600);color:var(--text)}
.btn-ghost{background:none;border-color:var(--border);color:var(--text-m)}
.btn-ghost:hover{background:var(--surface-2);color:var(--text)}

/* Flash */
.flash{padding:10px 14px;border-radius:var(--r-sm);font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.flash.success{background:rgba(34,197,94,.06);border:1px solid rgba(34,197,94,.18);color:var(--forest-400)}
.flash.error{background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.18);color:#f87171}
</style>
@endsection

@section('content')
<div class="page-wrap">

    <div class="breadcrumb">
        <a href="{{ route('admin.assignments.index') }}">Assignments</a>
        <span>/</span>
        <span>{{ isset($assignment) ? 'Edit' : 'Create' }}</span>
    </div>

    <div class="page-head">
        <h1>{{ isset($assignment) ? 'Edit Assignment' : 'Create Assignment' }}</h1>
        <p>Define the structure, sections, and questions. Students will write guided answers.</p>
    </div>

    @if($errors->any())
    <div class="flash error">
        <span>⚠</span>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <form id="assignmentForm"
          method="POST"
          action="{{ isset($assignment) ? route('admin.assignments.update', $assignment) : route('admin.assignments.store') }}">
        @csrf
        @if(isset($assignment)) @method('PUT') @endif

        {{-- ── Basic Info ──────────────────────────────────── --}}
        <div class="card">
            <div class="card-title"><span>1</span> Assignment Details</div>

            <div class="form-group">
                <label class="form-label" for="title">Title <span class="req">*</span></label>
                <input type="text" id="title" name="title" class="form-input"
                       value="{{ old('title', $assignment->title ?? '') }}"
                       placeholder="e.g. Youth and Society in Contemporary Nigeria"
                       required maxlength="200">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="topic">Topic <span class="req">*</span></label>
                    <input type="text" id="topic" name="topic" class="form-input"
                           value="{{ old('topic', $assignment->topic ?? '') }}"
                           placeholder="e.g. Youth Sociology" required maxlength="200">
                </div>
                <div class="form-group">
                    <label class="form-label" for="course">Course Code</label>
                    <input type="text" id="course" name="course" class="form-input"
                           value="{{ old('course', $assignment->course ?? '') }}"
                           placeholder="e.g. SOC302" maxlength="100">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description / Instructions</label>
                <textarea id="description" name="description" class="form-textarea"
                          placeholder="Give students context about this assignment…" maxlength="2000">{{ old('description', $assignment->description ?? '') }}</textarea>
            </div>

            <div class="toggle-group">
                <label class="toggle-label">
                    <input type="checkbox" name="is_published" value="1"
                           {{ old('is_published', $assignment->is_published ?? false) ? 'checked' : '' }}>
                    Publish immediately (visible to students)
                </label>
            </div>
        </div>

        {{-- ── Sections ─────────────────────────────────────── --}}
        <div class="card">
            <div class="card-title"><span>2</span> Sections & Questions</div>

            <div class="sections-list" id="sectionsList">
                {{-- Existing sections (edit mode) --}}
                @if(isset($assignment) && $assignment->sections->isNotEmpty())
                    @foreach($assignment->sections as $i => $sec)
                    <div class="section-item" data-index="{{ $i }}">
                        <div class="section-header">
                            <span class="section-drag">⠿</span>
                            <span class="section-num">#{{ $i + 1 }}</span>
                            <input type="text" name="sections[{{ $i }}][title]"
                                   class="section-title-flex form-input"
                                   value="{{ $sec->title }}"
                                   placeholder="Section title…" required>
                            <button type="button" class="section-remove" onclick="removeSection(this)">×</button>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Guidance note <span style="font-style:italic;text-transform:none;letter-spacing:0;">(optional)</span></label>
                            <input type="text" name="sections[{{ $i }}][guidance_note]"
                                   class="form-input" value="{{ $sec->guidance_note ?? '' }}"
                                   placeholder="Tip shown to student under this section…" maxlength="500">
                        </div>

                        <div class="questions-area">
                            <div class="questions-label">Questions (one per line)</div>
                            <textarea name="sections[{{ $i }}][questions]" class="form-textarea"
                                      style="min-height:90px;font-family:var(--font-m);font-size:12px;"
                                      placeholder="What is youth?&#10;Why is youth important in society?&#10;How does society define youth?">{{ implode("\n", $sec->questions ?? []) }}</textarea>
                            <div class="form-hint">Each line = one question the AI must answer in this section.</div>
                        </div>
                    </div>
                    @endforeach
                @else
                {{-- Default first section for new assignment --}}
                <div class="section-item" data-index="0">
                    <div class="section-header">
                        <span class="section-drag">⠿</span>
                        <span class="section-num">#1</span>
                        <input type="text" name="sections[0][title]"
                               class="section-title-flex form-input"
                               placeholder="e.g. Introduction" required>
                        <button type="button" class="section-remove" onclick="removeSection(this)">×</button>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Guidance note</label>
                        <input type="text" name="sections[0][guidance_note]" class="form-input"
                               placeholder="Tip shown to student…" maxlength="500">
                    </div>
                    <div class="questions-area">
                        <div class="questions-label">Questions (one per line)</div>
                        <textarea name="sections[0][questions]" class="form-textarea"
                                  style="min-height:90px;font-family:var(--font-m);font-size:12px;"
                                  placeholder="What is the definition of this topic?&#10;Why is it important?"></textarea>
                        <div class="form-hint">Each line = one question.</div>
                    </div>
                </div>
                @endif
            </div>

            <button type="button" class="add-section-btn" onclick="addSection()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Add Section
            </button>
        </div>

    </form>
</div>

{{-- Fixed submit bar --}}
<div class="submit-bar">
    <a href="{{ route('admin.assignments.index') }}" class="btn btn-ghost">← Cancel</a>
    <div style="display:flex;gap:8px;">
        <button type="button" onclick="document.getElementById('assignmentForm').submit()" class="btn btn-primary">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ isset($assignment) ? 'Update Assignment' : 'Create Assignment' }}
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
let sectionCount = {{ isset($assignment) ? $assignment->sections->count() : 1 }};

function addSection() {
    const i = sectionCount;
    const list = document.getElementById('sectionsList');

    const div = document.createElement('div');
    div.className = 'section-item';
    div.dataset.index = i;
    div.innerHTML = `
        <div class="section-header">
            <span class="section-drag">⠿</span>
            <span class="section-num">#${i+1}</span>
            <input type="text" name="sections[${i}][title]"
                   class="section-title-flex form-input"
                   placeholder="Section title…" required>
            <button type="button" class="section-remove" onclick="removeSection(this)">×</button>
        </div>
        <div class="form-group">
            <label class="form-label">Guidance note</label>
            <input type="text" name="sections[${i}][guidance_note]" class="form-input"
                   placeholder="Tip shown to student…" maxlength="500">
        </div>
        <div class="questions-area">
            <div class="questions-label">Questions (one per line)</div>
            <textarea name="sections[${i}][questions]" class="form-textarea"
                      style="min-height:90px;font-family:var(--font-m);font-size:12px;"
                      placeholder="Type a question…"></textarea>
            <div class="form-hint">Each line = one question.</div>
        </div>`;
    list.appendChild(div);
    sectionCount++;
    div.querySelector('input[type=text]').focus();
}

function removeSection(btn) {
    const items = document.querySelectorAll('.section-item');
    if (items.length <= 1) {
        alert('An assignment must have at least one section.');
        return;
    }

    btn.closest('.section-item').remove();

    // Re-number remaining sections
    document.querySelectorAll('.section-item').forEach((el, i) => {
        el.querySelector('.section-num').textContent = `#${i+1}`;
        // Update all name attributes
        el.querySelectorAll('[name]').forEach(inp => {
            inp.name = inp.name.replace(/sections\[\d+\]/, `sections[${i}]`);
        });
    });
}
</script>
@endpush
