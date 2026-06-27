@extends('layouts.app')
@section('title', 'Settings')
@section('page_title', 'Settings')
@section('page_sub', 'Notifications & Preferences')

@section('content')

<style>
  /* Toggle switch - Forest & Ink Style */
  .rp-toggle { position:relative; display:inline-block; width:42px; height:24px; flex-shrink:0; }
  .rp-toggle input { opacity:0; width:0; height:0; }
  .rp-slider {
    position:absolute; inset:0; border-radius:24px; cursor:pointer;
    background: #0f172a; /* Deep Ink */
    border:1px solid #1e293b; /* Ink-800 */
    transition: all .22s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .rp-slider::before {
    content:''; position:absolute; left:3px; top:50%; transform:translateY(-50%);
    width:16px; height:16px; border-radius:50%; background:#94a3b8; /* Ink-400 */
    transition: all .22s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  /* Active State: Forest Green */
  input:checked + .rp-slider { 
    background: rgba(21, 128, 61, 0.2); 
    border-color: #16a34a; /* Forest-600 */
  }
  input:checked + .rp-slider::before { 
    transform: translateY(-50%) translateX(18px); 
    background: #4ade80; /* Forest-400 */
    box-shadow: 0 0 8px rgba(34, 197, 94, 0.4);
  }
  input:disabled + .rp-slider { opacity:.3; cursor:not-allowed; }

  /* Setting row */
  .setting-row {
    display:flex; align-items:center; justify-content:space-between;
    gap:1.25rem; padding:1.25rem;
    border-bottom:1px solid rgba(30, 41, 59, 0.5); /* Ink-800/50 */
  }
  .setting-row:last-child { border-bottom:none; }

  /* Section card - Dark Mode Glass */
  .settings-card {
    background: #0f172a; /* Ink-900 */
    border:1px solid #1e293b; /* Ink-800 */
    border-radius:20px;
    overflow:hidden; margin-bottom:1.5rem;
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
  }
  .settings-card-header {
    display:flex; align-items:center; gap:.85rem;
    padding:1rem 1.25rem; border-bottom:1px solid #1e293b;
    background: rgba(15, 23, 42, 0.6);
  }
  .settings-card-header .hdr-icon {
    width:36px; height:36px; border-radius:10px;
    background: rgba(21, 128, 61, 0.1); 
    border:1px solid rgba(21, 128, 61, 0.2);
    display:flex; align-items:center; justify-content:center; 
    color: #4ade80; /* Forest-400 */
    flex-shrink:0;
  }

  /* Permission status badge */
  .perm-badge {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.3rem .75rem; border-radius:10px; font-size:.65rem; font-weight:700;
    font-family: 'JetBrains Mono', 'Fira Code', monospace; 
    letter-spacing:.05em; text-transform: uppercase;
  }
  .perm-badge .dot { width:6px; height:6px; border-radius:50%; }
  
  .perm-granted { background:rgba(34, 197, 94, 0.1); border:1px solid rgba(34, 197, 94, 0.2); color:#4ade80; }
  .perm-denied  { background:rgba(239, 68, 68, 0.1); border:1px solid rgba(239, 68, 68, 0.2); color:#f87171; }
  .perm-default { background:rgba(30, 41, 59, 0.5); border:1px solid #334155; color:#94a3b8; }
</style>



{{-- ── Page Header ─────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5 fade-up">
  <div>
    <p class="text-xs font-semibold tracking-widest uppercase text-forest-600 mb-1">Account</p>
    <h2 class="font-display text-2xl font-bold text-white">Settings</h2>
    <p class="text-xs text-ink-500 mt-0.5">Manage notifications and app preferences.</p>
  </div>
</div>


{{-- ══ PUSH NOTIFICATIONS SECTION ═══════════════════════════════ --}}
<div class="settings-card fade-up">
  <div class="settings-card-header">
    <div class="hdr-icon">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
      </svg>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-sm font-bold text-white font-display">Push Notifications</p>
      <p class="text-xs text-ink-600 mt-0.5">Get notified about lectures even when ReadPal is closed.</p>
    </div>
    {{-- Browser permission status badge --}}
    <div id="perm-status" class="perm-badge perm-default flex-shrink-0">
      <span class="dot" style="background:#F0B050;"></span>
      <span id="perm-label">Checking…</span>
    </div>
  </div>

  {{-- Master enable/disable row --}}
<div class="setting-row flex items-center justify-between p-4 border border-ink-800 rounded-2xl mb-4" id="master-row">
  <div class="setting-label">
    <p class="text-sm font-semibold text-ink-100">Push Notifications</p>
    <p id="notif-status-text" class="text-[0.7rem] text-ink-600 mt-0.5 leading-relaxed max-w-sm">
       Allow ReadPal to send you notifications on this device — even when the browser is closed.
        You must grant permission in your browser for this to work.
    </p>
  </div>

  {{-- Dynamic Action Button / Toggle --}}
  <div id="notif-action-container">
     <button onclick="handleNotifAction('allow')" 
             id="notif-master-btn"
             class="px-4 py-2 rounded-xl text-xs font-bold transition-all bg-forest-600 hover:bg-forest-500 text-white shadow-lg shadow-forest-900/20">
        Enable
     </button>
  </div>
</div>


  {{-- Notification type preferences --}}
{{-- Notification type preferences --}}
<form method="POST" action="{{ route('push.preferences') }}" id="pref-form">
  @csrf

  {{-- 1. Master Toggle Fallback --}}
  <input type="hidden" name="push_enabled" value="0">
  <input type="hidden" name="push_enabled" id="push_enabled_input" value="{{ Auth::user()->push_enabled ? '1' : '0' }}"/>

  {{-- 2. Lecture Alerts --}}
  <div class="setting-row" id="row-alerts">
    <div class="setting-label">
      <p class="text-sm font-semibold text-ink-100">New Lecture Alerts</p>
      <p class="text-xs text-ink-600">Immediate notifications for new posts.</p>
    </div>
    <label class="rp-toggle mt-0.5">
      {{-- HIDDEN FALLBACK MUST BE FIRST --}}
      <input type="hidden" name="push_lecture_alerts" value="0">
      <input type="checkbox" name="push_lecture_alerts" value="1"
             id="alert-toggle"
             {{ Auth::user()->push_lecture_alerts ? 'checked' : '' }}
             onchange="this.form.submit()"/>
      <span class="rp-slider"></span>
    </label>
  </div>

  {{-- 3. Lecture Reminders --}}
  <div class="setting-row mt-4" id="row-reminders">
    <div class="setting-label">
      <p class="text-sm font-semibold text-ink-100">Lecture Reminders</p>
      <p class="text-xs text-ink-600">Reminders 15 mins before lectures.</p>
    </div>
    <label class="rp-toggle mt-0.5">
      {{-- HIDDEN FALLBACK MUST BE FIRST --}}
      <input type="hidden" name="push_lecture_reminders" value="0">
      <input type="checkbox" name="push_lecture_reminders" value="1"
             id="reminder-toggle"
             {{ Auth::user()->push_lecture_reminders ? 'checked' : '' }}
             onchange="this.form.submit()"/>
      <span class="rp-slider"></span>
    </label>
  </div>
</form>


</div>


{{-- ══ BROWSER PERMISSION GUIDE ══════════════════════════════════ --}}
<div class="settings-card fade-up-d1" id="permission-guide" style="display:none;">
  <div class="settings-card-header">
    <div class="hdr-icon" style="background:rgba(212,136,42,.1);border-color:rgba(212,136,42,.22);color:#F0B050;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
      </svg>
    </div>
    <div>
      <p class="text-sm font-bold text-white font-display">Browser Permission Required</p>
      <p class="text-xs text-ink-600 mt-0.5">Notifications are blocked or not yet allowed in your browser.</p>
    </div>
  </div>
  <div class="p-5">
    <div style="background:rgba(212,136,42,.05);border:1px solid rgba(212,136,42,.12);border-radius:10px;
                padding:.9rem 1.1rem;margin-bottom:1.1rem;">
      <p class="text-xs text-ink-400 leading-relaxed">
        To receive push notifications from ReadPal, your browser must grant permission.
        Click the button below to request it. If you've previously denied notifications,
        you'll need to re-enable them manually in your browser settings.
      </p>
    </div>
    <button onclick="requestPermissionManually()"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                   text-sm font-semibold transition-all duration-150"
            style="background:#15803d;border:1px solid rgba(21,128,61,.5);color:#fff;">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
      </svg>
      Enable Browser Notifications
    </button>
    <p class="text-xs text-ink-700 mt-2.5 leading-relaxed">
      If blocked: in Chrome click the lock icon in your address bar → Site settings → Notifications → Allow.
      In Firefox: Preferences → Privacy &amp; Security → Permissions → Notifications.
    </p>
  </div>
</div>


{{-- ══ NOTIFICATION PREVIEW ═══════════════════════════════════════ --}}
<div class="settings-card fade-up-d2">
  <div class="settings-card-header">
    <div class="hdr-icon">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
    </div>
    <p class="text-sm font-bold text-white font-display">What Notifications Look Like</p>
  </div>
  <div class="p-5 space-y-3">

    {{-- Preview: New Lecture --}}
    <div class="notif-preview">
      <div class="notif-icon-wrap">
        <span style="font-size:1rem;">📚</span>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-ink-100">New Lecture Posted — SOC 303</p>
        <p class="text-xs text-ink-500 mt-0.5">Dr. Adewale · LT 1 · Mon, Jan 13 · 10:00 AM</p>
        <div class="flex items-center gap-2 mt-1.5">
          <span style="padding:.1rem .5rem;border-radius:6px;font-size:.62rem;
                       background:rgba(21,128,61,.12);color:#15803d;border:1px solid rgba(21,128,61,.2);">
            View Schedule
          </span>
          <span style="padding:.1rem .5rem;border-radius:6px;font-size:.62rem;
                       background:rgba(51,65,85,.5);color:#64748b;border:1px solid #334155;">
            Dismiss
          </span>
        </div>
      </div>
      <div style="text-align:right;flex-shrink:0;">
        <p class="text-xs text-ink-600 font-mono">Immediate</p>
        <p class="text-xs text-ink-700">ReadPal</p>
      </div>
    </div>

    {{-- Preview: Reminder --}}
    <div class="notif-preview">
      <div class="notif-icon-wrap">
        <span style="font-size:1rem;">🔔</span>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-ink-100">Lecture in 15 Minutes — SOC 303</p>
        <p class="text-xs text-ink-500 mt-0.5">Dr. Adewale · LT 1 · Starting soon</p>
        <div class="flex items-center gap-2 mt-1.5">
          <span style="padding:.1rem .5rem;border-radius:6px;font-size:.62rem;
                       background:rgba(96,165,250,.1);color:#60a5fa;border:1px solid rgba(96,165,250,.2);">
            View Details
          </span>
          <span style="padding:.1rem .5rem;border-radius:6px;font-size:.62rem;
                       background:rgba(51,65,85,.5);color:#64748b;border:1px solid #334155;">
            Dismiss
          </span>
        </div>
      </div>
      <div style="text-align:right;flex-shrink:0;">
        <p class="text-xs text-ink-600 font-mono">15 min before</p>
        <p class="text-xs text-ink-700">ReadPal</p>
      </div>
    </div>

    {{-- Send test button --}}
    <div class="pt-1">
      <button onclick="sendTestNotification()"
              class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl
                     border text-xs font-semibold transition-colors duration-150"
              style="border-color:#334155;color:#64748b;background:transparent;"
              onmouseover="this.style.borderColor='rgba(21,128,61,.4)';this.style.color='#15803d'"
              onmouseout="this.style.borderColor='#334155';this.style.color='#64748b'">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
        Send Test Notification
      </button>
      <p class="text-xs text-ink-700 mt-1.5">
        Sends a sample notification to verify your browser permission and subscription are working.
      </p>
    </div>
  </div>
</div>


{{-- ══ DEVICE INFO ════════════════════════════════════════════════ --}}
<div class="settings-card fade-up-d3">
  <div class="settings-card-header">
    <div class="hdr-icon">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/>
      </svg>
    </div>
    <div>
      <p class="text-sm font-bold text-white font-display">Active Subscriptions</p>
      <p class="text-xs text-ink-600 mt-0.5">Devices registered to receive push notifications.</p>
    </div>
  </div>
  <div class="p-5">
    @if(!Auth::user()->fcm_token)
    <div class="text-center py-6">
      <p class="text-xs text-ink-600">
        No active device registered.
      </p>
      <p class="text-[10px] text-ink-700 mt-1">Enable notifications above to register this browser.</p>
    </div>
    @else
    <div class="space-y-2">
      <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-forest-900/20 bg-forest-950/10">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 bg-forest-900/20 border border-forest-800/30">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2">
            <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-medium text-ink-300 truncate">
            Current Device (Registered)
          </p>
          <p class="text-[10px] text-ink-700 mt-0.5 font-mono">
            Active Token: {{ substr(Auth::user()->fcm_token, 0, 8) }}...{{ substr(Auth::user()->fcm_token, -4) }}
          </p>
        </div>
        {{-- Status Dot --}}
        <div class="notif-dot flex-shrink-0"></div>
      </div>
    </div>
    @endif
</div>

</div>

@push('scripts')
<script>
// ── Permission status UI ─────────────────────────────────────────
async function updatePermissionUI() {
  const badge = document.getElementById('perm-status');
  const label = document.getElementById('perm-label');
  const guide = document.getElementById('permission-guide');

  if (!('Notification' in window)) {
    badge.className = 'perm-badge perm-denied flex-shrink-0';
    label.textContent = 'Not Supported';
    return;
  }

  const permission = Notification.permission;

  if (permission === 'granted') {
    badge.className = 'perm-badge perm-granted flex-shrink-0';
    label.textContent = 'Allowed';
    badge.querySelector('.dot').style.background = '#22c55e';
    if (guide) guide.style.display = 'none';
  } else if (permission === 'denied') {
    badge.className = 'perm-badge perm-denied flex-shrink-0';
    label.textContent = 'Blocked';
    badge.querySelector('.dot').style.background = '#ef4444';
    if (guide) guide.style.display = 'block';
    // Disable toggles
    document.getElementById('master-toggle').disabled = true;
    document.getElementById('alert-toggle').disabled = true;
    document.getElementById('reminder-toggle').disabled = true;
  } else {
    badge.className = 'perm-badge perm-default flex-shrink-0';
    label.textContent = 'Not Set';
    if (guide) guide.style.display = 'block';
  }
}

// ── Master toggle handler ────────────────────────────────────────
async function handleMasterToggle(checkbox) {
  const enabled = checkbox.checked;
  // Update the hidden input that the controller actually reads
  document.getElementById('push_enabled_input').value = enabled ? '1' : '0';

  if (enabled) {
    // Request permission + subscribe
    if (!window.ReadPalPush) {
      checkbox.checked = false;
      return;
    }
    const success = await window.ReadPalPush.enable();
    if (!success) {
      checkbox.checked = false;
      updatePermissionUI();
      return;
    }
  } else {
    if (window.ReadPalPush) {
      await window.ReadPalPush.disable();
    }
  }

  // Save preference to server
  document.getElementById('pref-form').submit();
}

// ── Request permission manually (from guide card button) ─────────
async function requestPermissionManually() {
  const permission = await Notification.requestPermission();
  updatePermissionUI();

  if (permission === 'granted') {
    // Auto-subscribe
    const reg = await navigator.serviceWorker.ready;
    const sub = await window.ReadPalPush?.enable();
    if (sub) {
      document.getElementById('master-toggle').checked = true;
      document.getElementById('push_enabled_input').value = '1';
      document.getElementById('pref-form').submit();
    }
  }
}

// ── Send test notification ───────────────────────────────────────
function sendTestNotification() {
  if (Notification.permission !== 'granted') {
    alert('Please enable browser notifications first.');
    return;
  }
  if (!navigator.serviceWorker.controller) {
    new Notification('ReadPal Test', {
      body:  'Push notifications are working! 🎉',
      icon:  '/icons/icon-192.png',
      badge: '/icons/badge-72.png',
    });
  } else {
    navigator.serviceWorker.ready.then(reg => {
      reg.showNotification('ReadPal Test Notification', {
        body:    'Push notifications are working on ReadPal! 🎉',
        icon:    '/icons/icon-192.png',
        badge:   '/icons/badge-72.png',
        tag:     'readpal-test',
        vibrate: [200, 100, 200],
        actions: [
          { action: 'view',    title: 'Open ReadPal' },
          { action: 'dismiss', title: 'Dismiss' },
        ],
      });
    });
  }
}

// Run on load
document.addEventListener('DOMContentLoaded', updatePermissionUI);
</script>
@endpush

@endsection