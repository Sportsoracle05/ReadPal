
<style>
/* ═══════════════════════════════════════════════════════════
   READPAL AI — HYBRID v2
   Palette: Forest Green × Ink Depths × Crisp Monospace
═══════════════════════════════════════════════════════════ */
:root {
    /* ── Ink scale ─────────────────────────────── */
    --ink-950:    #030712;
    --ink-900:    #0c1120;
    --ink-800:    #111827;
    --ink-700:    #1c2639;
    --ink-600:    #243044;
    --ink-500:    #374151;
    --ink-400:    #4b5563;
    --ink-300:    #6b7280;
    --ink-200:    #9ca3af;
    --ink-100:    #d1d5db;

    /* ── Forest Green scale ────────────────────── */
    --forest-950: #052e16;
    --forest-900: #14532d;
    --forest-800: #166534;
    --forest-700: #15803d;
    --forest-600: #16a34a;
    --forest-500: #22c55e;
    --forest-400: #4ade80;
    --forest-300: #86efac;
    --forest-100: #dcfce7;

    /* ── Semantic aliases ──────────────────────── */
    --bg:           var(--ink-950);
    --surface:      var(--ink-900);
    --surface-2:    var(--ink-800);
    --surface-3:    var(--ink-700);
    --border:       var(--ink-600);
    --border-subtle:var(--ink-700);
    --text:         #f0f4f8;
    --text-muted:   var(--ink-200);
    --text-dim:     var(--ink-300);
    --accent:       var(--forest-500);
    --accent-hover: var(--forest-400);
    --accent-dim:   rgba(34, 197, 94, 0.12);
    --accent-border:rgba(34, 197, 94, 0.25);
    --accent-glow:  rgba(34, 197, 94, 0.08);
    --danger:       #ef4444;
    --warning:      #f59e0b;
    --info:         #3b82f6;

    /* ── Typography ────────────────────────────── */
    --font-display: 'Instrument Serif', Georgia, serif;
    --font-body:    'Geist', system-ui, sans-serif;
    --font-mono:    'JetBrains Mono', 'Cascadia Code', monospace;

    /* ── Layout ────────────────────────────────── */
    --sidebar-w:    268px;
    --topbar-h:     54px;
    --radius:       8px;
    --radius-sm:    5px;
    --radius-lg:    12px;
    --shadow:       0 8px 32px rgba(0,0,0,0.5);
    --shadow-sm:    0 2px 12px rgba(0,0,0,0.35);
    --shadow-green: 0 0 24px rgba(34, 197, 94, 0.12);
}

/* ── Reset ──────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }

/* ── Shell ──────────────────────────────────────────────── */
.ai-shell {
    display: flex;
    height: calc(100vh - 60px); /* adjust to your nav height */
    background: var(--bg);
    font-family: var(--font-body);
    color: var(--text);
    overflow: hidden;
    position: relative;
}

/* ═══════════════════════════════════════════════════════════
   SIDEBAR
═══════════════════════════════════════════════════════════ */
.ai-sidebar {
    width: var(--sidebar-w);
    min-width: var(--sidebar-w);
    background: rgba(12, 17, 32, 0.95);
    backdrop-filter: blur(12px);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: width 0.22s cubic-bezier(0.4,0,0.2,1),
                min-width 0.22s cubic-bezier(0.4,0,0.2,1);
    position: relative;
    z-index: 10;
}

/* Green top accent line */
.ai-sidebar::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, var(--forest-700), var(--forest-500), transparent);
    opacity: 0.6;
}

.ai-sidebar.collapsed {
    width: 0;
    min-width: 0;
    border-right: none;
}

/* ── Sidebar Header ─────────────────────────────────────── */
.sidebar-header {
    padding: 18px 16px 14px;
    border-bottom: 1px solid var(--border-subtle);
    flex-shrink: 0;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    margin-bottom: 14px;
}

.brand-mark {
    width: 30px;
    height: 30px;
    background: var(--accent-dim);
    border: 1px solid var(--accent-border);
    border-radius: var(--radius-sm);
    display: grid;
    place-items: center;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}

.brand-mark::after {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 50% 0%, rgba(34,197,94,0.2), transparent 70%);
}

.brand-text {
    font-family: var(--font-display);
    font-size: 16px;
    color: var(--text);
    white-space: nowrap;
    letter-spacing: -0.01em;
}

.brand-badge {
    font-family: var(--font-mono);
    font-size: 8px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--forest-500);
    background: var(--accent-dim);
    border: 1px solid var(--accent-border);
    border-radius: 3px;
    padding: 1px 5px;
    margin-left: 2px;
    vertical-align: middle;
}

.new-chat-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 8px 12px;
    background: var(--accent-dim);
    border: 1px solid var(--accent-border);
    border-radius: var(--radius-sm);
    color: var(--forest-400);
    font-family: var(--font-body);
    font-size: 12.5px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
    letter-spacing: 0.01em;
    white-space: nowrap;
    overflow: hidden;
}

.new-chat-btn:hover {
    background: rgba(34,197,94,0.18);
    border-color: rgba(34,197,94,0.45);
    color: var(--forest-300);
    box-shadow: 0 0 12px rgba(34,197,94,0.1);
}

/* ── Sidebar Body ───────────────────────────────────────── */
.sidebar-body {
    flex: 1;
    overflow-y: auto;
    padding: 6px 0;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}

.sidebar-body::-webkit-scrollbar { width: 3px; }
.sidebar-body::-webkit-scrollbar-thumb { background: var(--border); }

.sidebar-section {
    padding: 10px 16px 4px;
    font-family: var(--font-mono);
    font-size: 9.5px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--ink-400);
}

.sidebar-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 7px 16px;
    font-size: 12.5px;
    color: var(--text-muted);
    text-decoration: none;
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    transition: background 0.1s, color 0.1s;
    white-space: nowrap;
    overflow: hidden;
    line-height: 1.4;
    position: relative;
}

.sidebar-item::before {
    content: '';
    position: absolute;
    left: 0; top: 50%; transform: translateY(-50%);
    width: 2px;
    height: 0;
    background: var(--accent);
    border-radius: 0 2px 2px 0;
    transition: height 0.15s;
}

.sidebar-item:hover {
    background: rgba(255,255,255,0.03);
    color: var(--text);
}

.sidebar-item.active {
    background: rgba(34,197,94,0.06);
    color: var(--text);
}

.sidebar-item.active::before { height: 60%; }

.sidebar-icon {
    font-size: 13px;
    flex-shrink: 0;
    opacity: 0.75;
    width: 16px;
    text-align: center;
}

.sidebar-label {
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
}

.sidebar-chip {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--ink-400);
    flex-shrink: 0;
}

/* ── Sidebar Footer ─────────────────────────────────────── */
.sidebar-footer {
    padding: 12px 16px;
    border-top: 1px solid var(--border-subtle);
    flex-shrink: 0;
}

.user-tile {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-sm);
}

.user-avatar {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: var(--forest-900);
    border: 1px solid var(--forest-800);
    display: grid;
    place-items: center;
    font-family: var(--font-mono);
    font-size: 11px;
    font-weight: 600;
    color: var(--forest-400);
    flex-shrink: 0;
}

.user-info { min-width: 0; }
.user-name { font-size: 12px; font-weight: 500; color: var(--text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.user-meta { font-family: var(--font-mono); font-size: 10px; color: var(--ink-400); }

/* ═══════════════════════════════════════════════════════════
   MAIN AREA
═══════════════════════════════════════════════════════════ */
.ai-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: var(--bg);
    min-width: 0;
}

/* ── Topbar ─────────────────────────────────────────────── */
.ai-topbar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 20px;
    height: var(--topbar-h);
    background: rgba(12, 17, 32, 0.80);
    backdrop-filter: blur(8px);
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
    position: relative;
    z-index: 5;
}

.topbar-toggle {
    width: 30px;
    height: 30px;
    display: grid;
    place-items: center;
    background: none;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.15s;
    flex-shrink: 0;
}

.topbar-toggle:hover {
    background: var(--surface-2);
    color: var(--text);
    border-color: var(--ink-400);
}

.topbar-title {
    font-family: var(--font-display);
    font-size: 16px;
    color: var(--text);
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    letter-spacing: -0.01em;
}

.topbar-actions {
    display: flex;
    align-items: center;
    gap: 7px;
}

.topbar-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 11px;
    background: none;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-muted);
    font-family: var(--font-body);
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s;
    white-space: nowrap;
}

.topbar-btn:hover {
    background: var(--surface-2);
    border-color: var(--ink-400);
    color: var(--text);
}

.topbar-btn.primary {
    background: var(--accent-dim);
    border-color: var(--accent-border);
    color: var(--forest-400);
}

.topbar-btn.primary:hover {
    background: rgba(34,197,94,0.18);
    border-color: rgba(34,197,94,0.4);
    color: var(--forest-300);
}

/* provider select in topbar */
.topbar-select {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 5px 28px 5px 10px;
    font-family: var(--font-body);
    font-size: 12px;
    color: var(--text-muted);
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 8px center;
    transition: border-color 0.15s;
}

.topbar-select:focus { outline: none; border-color: var(--accent-border); }

/* ── Content Slot ───────────────────────────────────────── */
.ai-content {
    flex: 1;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

/* ═══════════════════════════════════════════════════════════
   SHARED COMPONENTS
═══════════════════════════════════════════════════════════ */

/* Provider Badge */
.provider-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    border-radius: 3px;
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.04em;
    border: 1px solid;
}

.provider-badge.gemini      { background: rgba(66,133,244,0.1);  color: #60a5fa; border-color: rgba(66,133,244,0.25); }
.provider-badge.groq        { background: rgba(245,158,11,0.1);  color: #fbbf24; border-color: rgba(245,158,11,0.25); }
.provider-badge.openrouter  { background: rgba(167,139,250,0.1); color: #a78bfa; border-color: rgba(167,139,250,0.25); }
.provider-badge.huggingface { background: rgba(251,191,36,0.1);  color: #fcd34d; border-color: rgba(251,191,36,0.25); }
.provider-badge.db_only     { background: rgba(107,114,128,0.1); color: #9ca3af; border-color: rgba(107,114,128,0.25); }

/* Confidence Badge */
.conf-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    border-radius: 3px;
    font-family: var(--font-mono);
    font-size: 10px;
    border: 1px solid;
}

.conf-badge.high   { background: rgba(34,197,94,0.1);  color: var(--forest-400); border-color: rgba(34,197,94,0.25); }
.conf-badge.medium { background: rgba(245,158,11,0.1); color: #fbbf24; border-color: rgba(245,158,11,0.25); }
.conf-badge.low    { background: rgba(239,68,68,0.1);  color: #f87171; border-color: rgba(239,68,68,0.25); }

/* Monospace data display */
.mono { font-family: var(--font-mono); }

/* ── Mobile ─────────────────────────────────────────────── */
@media (max-width: 768px) {
    .ai-sidebar { 
        position: absolute;
        top: 0; left: 0; bottom: 0;
        z-index: 50;
        transition: left 0.3s ease;
        box-shadow: var(--shadow);
    }

    .ai-sidebar.collapsed {
        transform: translateX(-100%);
        width: var(--sidebar-w);
        min-width: var(--sidebar-w);
    }

    .sidebar-overlay {
        display: none;
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.6);
        z-index: 49;
    }

    .sidebar-overlay.visible { display: block; }
}

/* Hide by default (Desktop) */
.sidebar-close-btn {
    display: none;
}

@media (max-width: 768px) {
    .sidebar-close-btn {
        display: block;
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        color: var(--ink-400);
        cursor: pointer;
        z-index: 10;
    }

    /* Make the header relative so the button sticks to the corner */
    .sidebar-header {
        position: relative;
    }
    
    /* Ensure the brand text doesn't overlap the button */
    .sidebar-brand {
        padding-right: 30px;
    }
}
</style>

<script>
function toggleSidebar() {
    const s = document.getElementById('aiSidebar');
    const o = document.getElementById('sidebarOverlay');
    const collapsed = s.classList.toggle('collapsed');
    o.classList.toggle('visible', !collapsed);
    localStorage.setItem('ai_sidebar_v2', collapsed ? '1' : '0');
}

// Restore state
if (localStorage.getItem('ai_sidebar_v2') === '1') {
    document.getElementById('aiSidebar').classList.add('collapsed');
}
</script>
