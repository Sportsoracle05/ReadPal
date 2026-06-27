
@extends('layouts.admin')

@section('title', 'Upload Material')


@section('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@300;400;500;600&family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* ─── Page ───────────────────────────────────────────────────── */
.upload-page {
    padding: 36px 40px;
    overflow-y: auto;
    height: 100%;
    scrollbar-width: thin;
    scrollbar-color: var(--ai-border) transparent;
}

.upload-page::-webkit-scrollbar { width: 4px; }
.upload-page::-webkit-scrollbar-thumb { background: var(--ai-border); border-radius: 2px; }

/* ─── Two-column layout ──────────────────────────────────────── */
.upload-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 28px;
    max-width: 1000px;
    align-items: start;
}

@media (max-width: 840px) { .upload-layout { grid-template-columns: 1fr; } }

/* ─── Section Headers ────────────────────────────────────────── */
.upload-section-title {
    font-family: var(--ai-font-display);
    font-size: 22px;
    color: var(--ai-text);
    margin: 0 0 6px;
}

.upload-section-sub {
    font-size: 13px;
    color: var(--ai-text-muted);
    margin: 0 0 24px;
    line-height: 1.6;
}

/* ─── Card ───────────────────────────────────────────────────── */
.upload-card {
    background: var(--ai-surface);
    border: 1px solid var(--ai-border);
    border-radius: var(--ai-radius);
    padding: 24px;
    margin-bottom: 20px;
}

.upload-card-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--ai-text);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: var(--ai-font-body);
}

.upload-card-title span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    background: var(--ai-accent-bg);
    border: 1px solid var(--ai-accent-border);
    border-radius: 50%;
    font-size: 10px;
    font-family: var(--ai-font-mono);
    color: var(--ai-accent);
    font-weight: 600;
}

/* ─── Form Fields ────────────────────────────────────────────── */
.form-group {
    margin-bottom: 18px;
}

.form-group:last-child { margin-bottom: 0; }

.form-label {
    display: block;
    font-size: 12px;
    font-weight: 500;
    color: var(--ai-text-muted);
    margin-bottom: 6px;
    font-family: var(--ai-font-mono);
    letter-spacing: 0.03em;
    text-transform: uppercase;
}

.form-label .required {
    color: var(--ai-accent);
    margin-left: 2px;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    background: var(--ai-bg);
    border: 1px solid var(--ai-border);
    border-radius: var(--ai-radius-sm);
    padding: 10px 14px;
    font-family: var(--ai-font-body);
    font-size: 13px;
    color: var(--ai-text);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    box-sizing: border-box;
    appearance: none;
    -webkit-appearance: none;
}

.form-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238b949e' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
    cursor: pointer;
}

.form-input::placeholder, .form-textarea::placeholder { color: var(--ai-text-dim); }

.form-input:focus, .form-select:focus, .form-textarea:focus {
    border-color: var(--ai-accent-border);
    box-shadow: 0 0 0 3px var(--ai-accent-bg);
}

.form-textarea {
    resize: vertical;
    min-height: 200px;
    line-height: 1.65;
    font-size: 13px;
}

/* Big content textarea */
.form-textarea.large {
    min-height: 320px;
    font-family: var(--ai-font-mono);
    font-size: 12.5px;
}

.form-hint {
    font-size: 11px;
    color: var(--ai-text-dim);
    margin-top: 6px;
    line-height: 1.5;
    font-family: var(--ai-font-mono);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

@media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

/* ─── Toggle: paste vs URL ───────────────────────────────────── */
.input-mode-toggle {
    display: flex;
    gap: 0;
    background: var(--ai-bg);
    border: 1px solid var(--ai-border);
    border-radius: var(--ai-radius-sm);
    padding: 3px;
    margin-bottom: 16px;
    width: fit-content;
}

.input-mode-btn {
    padding: 6px 14px;
    border: none;
    background: none;
    border-radius: 4px;
    font-family: var(--ai-font-body);
    font-size: 12px;
    color: var(--ai-text-muted);
    cursor: pointer;
    transition: all 0.15s;
}

.input-mode-btn.active {
    background: var(--ai-surface-3);
    color: var(--ai-text);
}

/* ─── Tag Input ─────────────────────────────────────────────── */
.tag-input-wrap {
    position: relative;
}

.tag-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    min-height: 40px;
    background: var(--ai-bg);
    border: 1px solid var(--ai-border);
    border-radius: var(--ai-radius-sm);
    padding: 6px 10px;
    cursor: text;
    transition: border-color 0.15s, box-shadow 0.15s;
}

.tag-pills.focused {
    border-color: var(--ai-accent-border);
    box-shadow: 0 0 0 3px var(--ai-accent-bg);
}

.tag-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    background: var(--ai-accent-bg);
    border: 1px solid var(--ai-accent-border);
    border-radius: 20px;
    font-size: 11px;
    font-family: var(--ai-font-mono);
    color: var(--ai-accent);
    cursor: default;
}

.tag-pill-remove {
    cursor: pointer;
    opacity: 0.6;
    font-size: 12px;
    line-height: 1;
    transition: opacity 0.1s;
}
.tag-pill-remove:hover { opacity: 1; }

.tag-type-input {
    border: none;
    outline: none;
    background: none;
    font-family: var(--ai-font-mono);
    font-size: 12px;
    color: var(--ai-text);
    min-width: 80px;
    flex: 1;
    padding: 3px 2px;
}
.tag-type-input::placeholder { color: var(--ai-text-dim); }

/* ─── Word Count ─────────────────────────────────────────────── */
.word-counter {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 6px;
}

.word-count-badge {
    font-family: var(--ai-font-mono);
    font-size: 11px;
    color: var(--ai-text-dim);
}

.word-count-badge.good  { color: var(--ai-green); }
.word-count-badge.warn  { color: var(--ai-accent); }
.word-count-badge.limit { color: var(--ai-red); }

/* ─── Submit Button ─────────────────────────────────────────── */
.upload-submit-btn {
    width: 100%;
    padding: 13px;
    background: var(--ai-accent);
    border: none;
    border-radius: var(--ai-radius-sm);
    font-family: var(--ai-font-body);
    font-size: 14px;
    font-weight: 600;
    color: #000;
    cursor: pointer;
    transition: background 0.15s, transform 0.1s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 8px;
}

.upload-submit-btn:hover { background: var(--ai-accent-hover); transform: translateY(-1px); }
.upload-submit-btn:disabled { background: var(--ai-surface-3); color: var(--ai-text-dim); cursor: not-allowed; transform: none; }

/* ─── Sidebar Tips ───────────────────────────────────────────── */
.tips-card {
    background: var(--ai-surface);
    border: 1px solid var(--ai-border);
    border-radius: var(--ai-radius);
    padding: 20px;
    position: sticky;
    top: 0;
}

.tips-header {
    font-size: 12px;
    font-weight: 600;
    color: var(--ai-accent);
    font-family: var(--ai-font-mono);
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.tip-item {
    display: flex;
    gap: 10px;
    margin-bottom: 14px;
    font-size: 12px;
    line-height: 1.6;
    color: var(--ai-text-muted);
}

.tip-item:last-child { margin-bottom: 0; }

.tip-icon {
    font-size: 14px;
    flex-shrink: 0;
    margin-top: 1px;
}

/* ─── Errors ─────────────────────────────────────────────────── */
.form-error {
    font-size: 11px;
    color: var(--ai-red);
    margin-top: 5px;
    font-family: var(--ai-font-mono);
}

.error-banner {
    background: rgba(248,81,73,0.08);
    border: 1px solid rgba(248,81,73,0.2);
    border-radius: var(--ai-radius-sm);
    padding: 12px 16px;
    margin-bottom: 20px;
    font-size: 13px;
    color: var(--ai-red);
}

/* ─── Progress overlay ───────────────────────────────────────── */
.upload-progress {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.7);
    display: none;
    place-items: center;
    z-index: 200;
    backdrop-filter: blur(4px);
}
.upload-progress.visible { display: grid; }

.progress-card {
    background: var(--ai-surface);
    border: 1px solid var(--ai-border);
    border-radius: var(--ai-radius);
    padding: 36px;
    text-align: center;
    min-width: 260px;
    box-shadow: var(--ai-shadow);
}

.progress-spinner {
    width: 40px; height: 40px;
    border: 3px solid var(--ai-border);
    border-top-color: var(--ai-accent);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 16px;
}

@keyframes spin { to { transform: rotate(360deg); } }

.progress-label {
    font-size: 14px;
    color: var(--ai-text-muted);
}
</style>
@endsection

@section('content')
@include('ai.knowledge-bases.structure')
<div class="upload-page">

    <div style="margin-bottom:28px;">
        <h1 style="font-family:var(--ai-font-display); font-size:28px; color:var(--ai-text); margin:0 0 6px;">
            Upload Study Material
        </h1>
        <p style="font-size:13px; color:var(--ai-text-muted); margin:0;">
            Paste your notes, textbook excerpts, or lecture content.
            The AI will index it and answer questions from it.
        </p>
    </div>

    @if($errors->any())
    <div class="error-banner">
        @foreach($errors->all() as $error)
        <div>⚠ {{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form id="uploadForm" method="POST" 
      action="{{ isset($selectedBase) 
                 ? route('ai.knowledge-bases.store-content', $selectedBase->id) 
                 : route('ai.knowledge-bases.store') }}"
      onsubmit="showProgress()">
        @csrf

        <div class="upload-layout">

            {{-- ── Left: Main Form ──────────────────────────────── --}}
            <div>

                {{-- Step 1: Base details (only shown if creating new) --}}
                @if(!isset($selectedBase))
                <div class="upload-card">
                    <div class="upload-card-title">
                        <span>1</span> Name your knowledge base
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="title">Title <span class="required">*</span></label>
                        <input type="text" id="title" name="title" class="form-input"
                               placeholder="e.g. Classical Sociological Theory Notes"
                               value="{{ old('title') }}" required maxlength="200">
                        @error('title')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="subject">Subject</label>
                            <select id="subject" name="subject" class="form-select">
                                <option value="">Select subject…</option>
                                <option value="Sociology" {{ old('subject') === 'Sociology' ? 'selected' : '' }}>Sociology</option>
                                <option value="Research Methods" {{ old('subject') === 'Research Methods' ? 'selected' : '' }}>Research Methods</option>
                                <option value="Anthropology" {{ old('subject') === 'Anthropology' ? 'selected' : '' }}>Anthropology</option>
                                <option value="History" {{ old('subject') === 'History' ? 'selected' : '' }}>History</option>
                                <option value="Philosophy" {{ old('subject') === 'Philosophy' ? 'selected' : '' }}>Philosophy</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="course_code">Course Code</label>
                            <input type="text" id="course_code" name="course_code" class="form-input"
                                   placeholder="e.g. SOC301"
                                   value="{{ old('course_code') }}" maxlength="20">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Short description</label>
                        <input type="text" id="description" name="description" class="form-input"
                               placeholder="What is this material about?"
                               value="{{ old('description') }}" maxlength="500">
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:var(--ai-text-muted);">
                            <input type="checkbox" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}
                                   style="accent-color:var(--ai-accent); width:15px; height:15px;">
                            Make visible to other students
                        </label>
                    </div>
                </div>
                @else
                <input type="hidden" name="title" value="{{ $selectedBase->title }}">
                @endif

                {{-- Step 2: Content --}}
                <div class="upload-card">
                    <div class="upload-card-title">
                        <span>{{ isset($selectedBase) ? '1' : '2' }}</span> Paste your content
                    </div>

                    {{-- Section heading (optional) --}}
                    <div class="form-group">
                        <label class="form-label" for="section_heading">Section heading <em style="font-style:normal;color:var(--ai-text-dim)">(optional)</em></label>
                        <input type="text" id="section_heading" name="section_heading" class="form-input"
                               placeholder="e.g. Chapter 3: Weber's Theory of Bureaucracy"
                               value="{{ old('section_heading') }}" maxlength="200">
                        <div class="form-hint">Helps the AI tell users which section an answer came from.</div>
                    </div>

                    {{-- The main content textarea --}}
                    <div class="form-group">
                        <label class="form-label" for="text">
                            Content <span class="required">*</span>
                        </label>
                        <textarea id="textContent" name="text" class="form-textarea large"
                                  placeholder="Paste your lecture notes, textbook content, or study material here.

You can paste entire sections — the system will automatically split it into searchable paragraphs (split on blank lines between paragraphs).

The more content you add, the better and more specific the AI answers will be."
                                  oninput="updateWordCount(this)" required>{{ old('text') }}</textarea>

                        <div class="word-counter">
                            <div class="form-hint">Separate paragraphs with a blank line for best results.</div>
                            <div class="word-count-badge" id="wordCountBadge">0 words</div>
                        </div>
                        @error('text')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Tags --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Keywords / Tags</label>
                        <div class="tag-pills" id="tagPills" onclick="document.getElementById('tagTypeInput').focus()">
                            <input type="text" id="tagTypeInput" class="tag-type-input"
                                   placeholder="Type a keyword, press Enter…"
                                   maxlength="80">
                        </div>
                        <div id="tagHiddenInputs"></div>
                        <div class="form-hint">
                            Add terms like "Weber", "stratification", "Durkheim" to boost exact-match accuracy.
                        </div>
                    </div>

                </div>

                {{-- Submit --}}
                <button type="submit" class="upload-submit-btn" id="submitBtn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    Upload & Index Content
                </button>

            </div>

            {{-- ── Right: Tips ──────────────────────────────────── --}}
            <div>
                <div class="tips-card">
                    <div class="tips-header">💡 Tips for best results</div>

                    <div class="tip-item">
                        <span class="tip-icon">📄</span>
                        <span><strong style="color:var(--ai-text)">Separate paragraphs</strong> with a blank line. Each paragraph becomes its own searchable chunk.</span>
                    </div>
                    <div class="tip-item">
                        <span class="tip-icon">🏷</span>
                        <span><strong style="color:var(--ai-text)">Add keywords</strong> like concept names, theorists, and key terms. These create an exact-match shortcut for the AI.</span>
                    </div>
                    <div class="tip-item">
                        <span class="tip-icon">📌</span>
                        <span><strong style="color:var(--ai-text)">Use section headings</strong> when uploading different chapters separately. This appears in answers as a source reference.</span>
                    </div>
                    <div class="tip-item">
                        <span class="tip-icon">📝</span>
                        <span><strong style="color:var(--ai-text)">Plain text works best.</strong> Remove tables, bullet lists, and formatting before pasting. The AI reads sentences, not structure.</span>
                    </div>
                    <div class="tip-item">
                        <span class="tip-icon">🔁</span>
                        <span><strong style="color:var(--ai-text)">Upload in batches.</strong> Add one chapter or topic at a time and label each with a section heading.</span>
                    </div>

                    @if(isset($selectedBase))
                    <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--ai-border-subtle);">
                        <div style="font-size:11px; color:var(--ai-text-dim); font-family:var(--ai-font-mono); margin-bottom:8px; text-transform:uppercase; letter-spacing:0.06em;">Adding to</div>
                        <div style="font-size:13px; font-weight:600; color:var(--ai-text);">{{ $selectedBase->title }}</div>
                        <div style="font-size:11px; color:var(--ai-text-muted); margin-top:2px; font-family:var(--ai-font-mono);">
                            {{ $selectedBase->paragraphs_count }} paragraphs already indexed
                        </div>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </form>

</div>

{{-- Upload progress overlay --}}
<div class="upload-progress" id="uploadProgress">
    <div class="progress-card">
        <div class="progress-spinner"></div>
        <div class="progress-label">Indexing your content…</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ─── Tag pill system ──────────────────────────────────────────
const tags = [];
const tagInput    = document.getElementById('tagTypeInput');
const tagPills    = document.getElementById('tagPills');
const hiddenInputs = document.getElementById('tagHiddenInputs');

tagInput.addEventListener('focus', () => tagPills.classList.add('focused'));
tagInput.addEventListener('blur',  () => tagPills.classList.remove('focused'));

tagInput.addEventListener('keydown', (e) => {
    if (['Enter', ',', 'Tab'].includes(e.key)) {
        e.preventDefault();
        addTag(tagInput.value.trim());
    } else if (e.key === 'Backspace' && !tagInput.value && tags.length > 0) {
        removeTag(tags.length - 1);
    }
});

function addTag(value) {
    if (!value || value.length < 2) return;
    const clean = value.toLowerCase().replace(/[^a-z0-9\s\-]/g, '').trim();
    if (!clean || tags.includes(clean) || tags.length >= 20) return;

    tags.push(clean);
    tagInput.value = '';

    const pill = document.createElement('span');
    pill.className = 'tag-pill';
    pill.dataset.index = tags.length - 1;
    pill.innerHTML = `${escapeHtml(clean)} <span class="tag-pill-remove" onclick="removeTag(${tags.length - 1})">×</span>`;
    tagPills.insertBefore(pill, tagInput);

    const hidden = document.createElement('input');
    hidden.type  = 'hidden';
    hidden.name  = 'tags[]';
    hidden.value = clean;
    hidden.id    = `tag-hidden-${tags.length - 1}`;
    hiddenInputs.appendChild(hidden);
}

function removeTag(index) {
    tags.splice(index, 1);
    // Re-render all pills (simpler than individual removal)
    renderTags();
}

function renderTags() {
    // Remove all pill elements (not the input)
    tagPills.querySelectorAll('.tag-pill').forEach(el => el.remove());
    hiddenInputs.innerHTML = '';

    tags.forEach((tag, i) => {
        const pill = document.createElement('span');
        pill.className = 'tag-pill';
        pill.innerHTML = `${escapeHtml(tag)} <span class="tag-pill-remove" onclick="removeTag(${i})">×</span>`;
        tagPills.insertBefore(pill, tagInput);

        const hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = 'tags[]';
        hidden.value = tag;
        hiddenInputs.appendChild(hidden);
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

// ─── Word counter ─────────────────────────────────────────────
function updateWordCount(textarea) {
    const count = textarea.value.trim()
        ? textarea.value.trim().split(/\s+/).length
        : 0;

    const badge = document.getElementById('wordCountBadge');
    badge.textContent = `${count.toLocaleString()} words`;

    badge.className = 'word-count-badge';
    if (count > 5000)     badge.classList.add('limit');
    else if (count > 500) badge.classList.add('good');
}

// ─── Upload progress ──────────────────────────────────────────
function showProgress() {
    document.getElementById('uploadProgress').classList.add('visible');
    document.getElementById('submitBtn').disabled = true;
}

// ─── Handle if adding to existing base via ?add_to= ──────────
const urlParams  = new URLSearchParams(window.location.search);
const addToBase  = urlParams.get('add_to');

if (addToBase) {
    const form = document.getElementById('uploadForm');
    form.action = form.action.replace('/0/', `/${addToBase}/`);
}
</script>
@endpush
