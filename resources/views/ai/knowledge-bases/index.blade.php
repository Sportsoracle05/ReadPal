{{--
    resources/views/ai/knowledge-bases/index.blade.php (v2)
    Forest Green × Ink — SaaS Dashboard
--}}
@extends('layouts.admin')

@section('title', 'Knowledge Bases')

@section('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@300;400;500;600&family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
.kb-page {
    padding: 32px 36px;
    overflow-y: auto;
    height: 100%;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}
.kb-page::-webkit-scrollbar { width: 3px; }
.kb-page::-webkit-scrollbar-thumb { background: var(--border); }

/* ── Page header ─────────────────────────────────────────── */
.kb-page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 28px;
    gap: 16px;
    flex-wrap: wrap;
}

.kb-page-header h1 {
    font-family: var(--font-display);
    font-size: 26px;
    color: var(--text);
    margin: 0 0 5px;
    letter-spacing: -0.02em;
}

.kb-page-header p {
    font-size: 13px;
    color: var(--text-muted);
    margin: 0;
}

/* ── Stats row ───────────────────────────────────────────── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}

@media (max-width: 640px) { .stats-row { grid-template-columns: 1fr 1fr; } }

.stat-tile {
    background: rgba(28,38,57,0.8);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 18px;
    position: relative;
    overflow: hidden;
}

.stat-tile::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, var(--forest-900), var(--forest-700), transparent);
}

.stat-value {
    font-family: var(--font-mono);
    font-size: 28px;
    font-weight: 600;
    color: var(--forest-400);
    line-height: 1;
    margin-bottom: 4px;
}

.stat-label {
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--ink-400);
}

/* ── Flash ───────────────────────────────────────────────── */
.flash-success {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: rgba(34,197,94,0.06);
    border: 1px solid rgba(34,197,94,0.18);
    border-radius: var(--radius-sm);
    font-size: 12.5px;
    color: var(--forest-400);
    margin-bottom: 20px;
}

/* ── Grid ────────────────────────────────────────────────── */
.kb-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(272px, 1fr));
    gap: 14px;
}

/* ── Card ────────────────────────────────────────────────── */
.kb-card {
    background: rgba(17,24,39,0.9);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: border-color 0.15s, transform 0.15s, box-shadow 0.15s;
    position: relative;
    overflow: hidden;
    animation: cardIn 0.3s ease both;
}

@keyframes cardIn {
    from { opacity:0; transform:translateY(8px); }
    to   { opacity:1; transform:translateY(0); }
}

.kb-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, var(--forest-800), var(--forest-600), transparent);
    opacity: 0;
    transition: opacity 0.2s;
}

.kb-card:hover {
    border-color: rgba(34,197,94,0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.4), 0 0 20px rgba(34,197,94,0.05);
}

.kb-card:hover::before { opacity: 1; }

/* Card header */
.kb-card-head {
    display: flex;
    align-items: flex-start;
    gap: 11px;
}

.kb-icon {
    width: 36px;
    height: 36px;
    background: var(--forest-950);
    border: 1px solid var(--forest-900);
    border-radius: var(--radius-sm);
    display: grid;
    place-items: center;
    font-size: 16px;
    flex-shrink: 0;
}

.kb-title-block { flex: 1; min-width: 0; }

.kb-title {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text);
    margin: 0 0 3px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.kb-subtitle {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--ink-400);
}

/* Description */
.kb-desc {
    font-size: 12px;
    color: var(--text-muted);
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
}

/* Chips */
.kb-chips {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.kb-chip {
    font-family: var(--font-mono);
    font-size: 9.5px;
    letter-spacing: 0.04em;
    padding: 2px 7px;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border-subtle);
    border-radius: 3px;
    color: var(--ink-400);
}

.kb-chip.public {
    background: rgba(34,197,94,0.06);
    border-color: rgba(34,197,94,0.18);
    color: var(--forest-500);
}

/* Footer */
.kb-card-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 12px;
    border-top: 1px solid var(--border-subtle);
}

.kb-para-count {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--ink-400);
}

.kb-para-count strong {
    color: var(--forest-500);
    font-weight: 600;
}

.kb-actions { display: flex; gap: 5px; }

.kb-action {
    width: 27px;
    height: 27px;
    display: grid;
    place-items: center;
    background: none;
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-sm);
    color: var(--ink-400);
    cursor: pointer;
    font-size: 12px;
    text-decoration: none;
    transition: all 0.14s;
}

.kb-action:hover { background: var(--surface-2); color: var(--text); border-color: var(--border); }
.kb-action.del:hover  { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.25); color: #f87171; }
.kb-action.chat:hover { background: var(--accent-dim); border-color: var(--accent-border); color: var(--forest-400); }

/* ── Empty ───────────────────────────────────────────────── */
.kb-empty {
    grid-column: 1/-1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 72px 24px;
    text-align: center;
    background: rgba(255,255,255,0.01);
    border: 1px dashed var(--border);
    border-radius: var(--radius);
}

.kb-empty-icon { font-size: 42px; margin-bottom: 16px; opacity: 0.4; }
.kb-empty-title { font-family: var(--font-display); font-size: 22px; color: var(--text); margin-bottom: 8px; }
.kb-empty-text  { font-size: 13px; color: var(--text-muted); max-width: 300px; line-height: 1.65; margin-bottom: 24px; }

.cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    background: var(--forest-800);
    border: 1px solid var(--forest-700);
    border-radius: var(--radius-sm);
    color: var(--forest-300);
    font-family: var(--font-body);
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s;
}

.cta-btn:hover { background: var(--forest-700); border-color: var(--forest-600); color: var(--text); }

/* ── Modal ───────────────────────────────────────────────── */
.modal-back {
    position: fixed;
    inset: 0;
    background: rgba(3,7,18,0.8);
    backdrop-filter: blur(6px);
    display: none;
    place-items: center;
    z-index: 100;
    animation: fadeIn 0.15s ease;
}

.modal-back.open { display: grid; }

@keyframes fadeIn { from{opacity:0;} to{opacity:1;} }

.modal-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 28px;
    max-width: 360px;
    width: 90%;
    box-shadow: var(--shadow);
    animation: modalPop 0.2s ease;
}

@keyframes modalPop { from{opacity:0;transform:scale(0.96);} to{opacity:1;transform:scale(1);} }

.modal-icon { font-size: 28px; margin-bottom: 12px; }
.modal-title { font-family: var(--font-display); font-size: 19px; color: var(--text); margin-bottom: 8px; }
.modal-body  { font-size: 13px; color: var(--text-muted); line-height: 1.65; margin-bottom: 20px; }

.modal-actions { display: flex; gap: 8px; justify-content: flex-end; }

.modal-btn {
    padding: 7px 16px;
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.14s;
    border: 1px solid;
}

.modal-btn.cancel  { background: none; border-color: var(--border); color: var(--text-muted); }
.modal-btn.cancel:hover  { background: var(--surface-2); color: var(--text); }
.modal-btn.confirm { background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.3); color: #f87171; }
.modal-btn.confirm:hover { background: rgba(239,68,68,0.2); }
</style>
@endsection

@section('content')
@include('ai.knowledge-bases.structure')
<div class="kb-page">

    <div class="kb-page-header">
        <div>
            <h1>Knowledge Bases</h1>
            <p>Your indexed study materials. The AI draws exclusively from these.</p>
        </div>
        <a href="{{ route('ai.knowledge-bases.create') }}" class="cta-btn">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Upload Material
        </a>
    </div>

    @if($knowledgeBases->isNotEmpty())
    <div class="stats-row">
        <div class="stat-tile">
            <div class="stat-value">{{ $knowledgeBases->count() }}</div>
            <div class="stat-label">Bases</div>
        </div>
        <div class="stat-tile">
            <div class="stat-value">{{ number_format($knowledgeBases->sum('paragraphs_count')) }}</div>
            <div class="stat-label">Paragraphs</div>
        </div>
        <div class="stat-tile">
            <div class="stat-value">{{ number_format($totalConversations ?? 0) }}</div>
            <div class="stat-label">Questions</div>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="flash-success">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="kb-grid">
        @forelse($knowledgeBases as $base)
        <div class="kb-card" style="animation-delay:{{ $loop->index * 0.04 }}s">
            <div class="kb-card-head">
                <div class="kb-icon">
                    {{ match(strtolower($base->subject ?? '')) {
                        'sociology'        => '🏛',
                        'research methods' => '🔬',
                        'anthropology'     => '🌐',
                        'history'          => '📜',
                        'philosophy'       => '💡',
                        default            => '◈'
                    } }}
                </div>
                <div class="kb-title-block">
                    <div class="kb-title" title="{{ $base->title }}">{{ $base->title }}</div>
                    <div class="kb-subtitle mono">
                        @if($base->course_code){{ $base->course_code }} · @endif
                        {{ $base->subject ?? 'General' }}
                    </div>
                </div>
            </div>

            @if($base->description)
            <div class="kb-desc">{{ $base->description }}</div>
            @endif

            <div class="kb-chips">
                @if($base->course_code)
                <span class="kb-chip">{{ $base->course_code }}</span>
                @endif
                <span class="kb-chip {{ $base->is_public ? 'public' : '' }}">
                    {{ $base->is_public ? 'Public' : 'Private' }}
                </span>
                <span class="kb-chip">{{ $base->created_at->format('M Y') }}</span>
            </div>

            <div class="kb-card-foot">
                <span class="kb-para-count mono">
                    <strong>{{ $base->paragraphs_count ?? 0 }}</strong> paragraphs indexed
                </span>
                <div class="kb-actions">
                    <a href="{{ route('ai.knowledge-bases.create', ['add_to' => $base->id]) }}" class="kb-action" title="Add content">+</a>
                    <a href="{{ route('ai.chat', ['base' => $base->id]) }}" class="kb-action chat" title="Ask questions">›</a>
                    <button class="kb-action del" title="Delete"
                            onclick="confirmDelete({{ $base->id }}, '{{ addslashes($base->title) }}')">×</button>
                </div>
            </div>
        </div>

        @empty
        <div class="kb-empty">
            <div class="kb-empty-icon">◈</div>
            <h2 class="kb-empty-title">No materials yet</h2>
            <p class="kb-empty-text">Upload your lecture notes or textbook content. The AI will index and search them.</p>
            <a href="{{ route('ai.knowledge-bases.create') }}" class="cta-btn">Upload first material</a>
        </div>
        @endforelse
    </div>

</div>

{{-- Delete modal --}}
<div id="deleteModal" class="modal-back" onclick="if(event.target===this)closeModal()">
    <div class="modal-box">
        <div class="modal-icon">⚠</div>
        <div class="modal-title">Delete Knowledge Base?</div>
        <p class="modal-body" id="deleteModalBody">This will remove all paragraphs and tags. Cannot be undone.</p>
        <div class="modal-actions">
            <button class="modal-btn cancel" onclick="closeModal()">Cancel</button>
            <form id="deleteForm" method="POST" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="modal-btn confirm">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id, title) {
    document.getElementById('deleteModalBody').textContent = `"${title}" and all its indexed paragraphs will be permanently deleted.`;
    document.getElementById('deleteForm').action = `{{ url('ai/knowledge-bases') }}/${id}`;
    document.getElementById('deleteModal').classList.add('open');
}

function closeModal() { document.getElementById('deleteModal').classList.remove('open'); }

document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModal(); });
</script>
@endpush
