@extends('layouts.admin')
@section('title','Admin Dashboard')
@section('page_title','Dashboard')
@section('page_sub','ReadPal Analytics Overview')

@section('content')

{{-- ── Stat Cards ─────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
  @foreach([
    ['Unique Visitors',  $uniqueVisitors,   $visitorChange,  true,  'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',   '#4ade80'],
    ['Total Pageviews',  $totalPageviews,   $pageviewChange, true,  'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z',                                                                                                                                                                                                                                                                             '#38bdf8'],
    ['Bounce Rate %',    $bounceRate.'%',   $bounceChange,   false, 'M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5M8.288 14.212A5.25 5.25 0 1117.25 10.5',                                                                                                                                                                                                                                                                                                                        '#fbbf24'],
    ['Visit Duration',   $duration.'s',     $durationChange, true,  'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                                                                                                                                                                                                                                                                                                   '#a78bfa'],
  ] as $i => [$label, $value, $change, $up, $path, $accent])
  <div class="a-card fu" style="animation-delay:{{ $i*.06 }}s">
    <div class="flex items-start justify-between mb-3">
      <div class="w-9 h-9 rounded-lg border flex items-center justify-center flex-shrink-0"
           style="background:rgba(2,6,23,.7);border-color:rgba(51,65,85,.8);">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="{{ $accent }}" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
          <path d="{{ $path }}"/>
        </svg>
      </div>
      <span class="{{ $up ? 'stat-up' : 'stat-dn' }}">
        {{ $up ? '+' : '' }}{{ number_format($change, 1) }}%
      </span>
    </div>
    <p class="font-display text-2xl font-bold text-white">{{ is_numeric($value) ? number_format($value) : $value }}</p>
    <p class="text-xs text-ink-600 mt-0.5">{{ $label }} <span class="text-ink-700">· vs last month</span></p>
  </div>
  @endforeach
</div>

{{-- ── Analytics Chart ──────────────────────────────────────────────── --}}
<div class="a-card mb-5 fu1">
  <div class="flex items-center justify-between mb-4">
    <div>
      <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider">Traffic Overview</p>
      <h3 class="font-display text-base font-bold text-white mt-0.5">Visitors & Pageviews</h3>
    </div>
    <div class="flex items-center gap-3 text-xs text-ink-500">
      <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-forest-500 inline-block"></span>Visitors</span>
      <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sky-500 inline-block"></span>Pageviews</span>
    </div>
  </div>
  <x-analytics-chart :dates="$dates" :visitors="$visitors" :pageviews="$pageviews" />
</div>

{{-- ── Middle Grid ─────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-4">

  {{-- Top Materials ──────────────────────────────────────────────── --}}
  <div class="lg:col-span-4 a-card fu2">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display text-sm font-bold text-white">Top Materials</h3>
      <a href="{{ route('admin.materials.index') }}"
         class="text-xs text-forest-600 hover:text-forest-400 transition-colors">View all →</a>
    </div>
    <div class="space-y-0">
      <div class="flex items-center justify-between pb-2.5 mb-1 border-b border-ink-800">
        <span class="text-xs text-ink-600 uppercase tracking-wider">Material</span>
        <span class="text-xs text-ink-600 uppercase tracking-wider">Views</span>
      </div>
      @forelse($topMaterials as $i => $material)
      <div class="flex items-center justify-between py-2.5 border-b border-ink-800/50 last:border-0 group">
        <div class="flex items-center gap-2 min-w-0 flex-1">
          <span class="font-mono text-xs text-ink-700 w-4 flex-shrink-0">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span>
          <span class="text-xs text-ink-300 group-hover:text-ink-100 transition-colors truncate max-w-[140px]">
            {{ $material->title }}
          </span>
        </div>
        <span class="font-mono text-xs text-forest-400 flex-shrink-0">{{ number_format($material->total_views) }}</span>
      </div>
      @empty
      <p class="text-xs text-ink-600 text-center py-6">No material data yet.</p>
      @endforelse
    </div>
    <a href="{{ route('admin.materials.index') }}"
       class="btn-outline btn-sm w-full justify-center mt-4">Materials Report</a>
  </div>

  {{-- Top Pages ──────────────────────────────────────────────────── --}}
  <div class="lg:col-span-4 a-card fu2">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display text-sm font-bold text-white">Top Pages</h3>
    </div>
    <div class="space-y-0">
      <div class="flex items-center justify-between pb-2.5 mb-1 border-b border-ink-800">
        <span class="text-xs text-ink-600 uppercase tracking-wider">Page</span>
        <span class="text-xs text-ink-600 uppercase tracking-wider">Visits</span>
      </div>
      @forelse($topPages as $i => $page)
      <div class="flex items-center justify-between py-2.5 border-b border-ink-800/50 last:border-0 group">
        <div class="flex items-center gap-2 min-w-0">
          <span class="font-mono text-xs text-ink-700 w-4 flex-shrink-0">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span>
          <span class="text-xs text-ink-300 font-mono group-hover:text-ink-100 transition-colors truncate max-w-[150px]">
            /{{ $page->page }}
          </span>
        </div>
        <span class="font-mono text-xs text-sky-400 flex-shrink-0">{{ number_format($page->total_visits) }}</span>
      </div>
      @empty
      <p class="text-xs text-ink-600 text-center py-6">No page visit data yet.</p>
      @endforelse
    </div>
    <a href="#" class="btn-outline btn-sm w-full justify-center mt-4">Channels Report</a>
  </div>

  {{-- Live / Active Users ─────────────────────────────────────────── --}}
  <div class="lg:col-span-4 a-card fu3">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-display text-sm font-bold text-white">Active Users</h3>
      <div class="flex items-center gap-1.5">
        <div class="live-dot w-2 h-2"></div>
        <span class="text-xs text-forest-500 font-semibold">Live</span>
      </div>
    </div>

    {{-- Live count --}}
    <div class="flex items-end gap-2 mb-4 pb-4 border-b border-ink-800">
      <p class="font-display text-4xl font-bold text-white">{{ number_format($liveVisitors) }}</p>
      <p class="text-xs text-ink-500 mb-1">online right now</p>
    </div>

    {{-- Chart --}}
    <div class="rounded-xl bg-ink-800/40 border border-ink-800 overflow-hidden mb-4" style="height:130px;">
      <div id="chartAna" style="height:130px;margin:-2px -4px;"></div>
    </div>

    {{-- Averages --}}
    <div class="grid grid-cols-1 grid-cols-3 gap-2 text-center">
      @foreach([
        [$avgDailyUsers,   'Daily Avg'],
        [$avgWeeklyUsers,  'Weekly Avg'],
        [$avgMonthlyUsers, 'Monthly Avg'],
      ] as [$val, $lbl])
      <div class="a-card-sm">
        <p class="font-display text-lg font-bold text-white">{{ number_format($val,0) }}</p>
        <p class="text-xs text-ink-600 mt-0.5">{{ $lbl }}</p>
      </div>
      @endforeach
    </div>
  </div>
</div>

{{-- ── Bottom Row: Recent Materials + Quick Stats ───────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 a-card fu4">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display text-sm font-bold text-white">Recent Uploads</h3>
      <a href="{{ route('admin.materials.create') }}"
         class="btn-primary btn-sm">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M12 4.5v15m7.5-7.5h-15"/>
        </svg> Upload
      </a>
    </div>
    <table class="w-full">
      <thead>
        <tr>
          <th class="tbl-head text-left">Title</th>
          <th class="tbl-head text-left">Course</th>
          <th class="tbl-head text-center">PDF</th>
          <th class="tbl-head text-right">Added</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentMaterials ?? [] as $mat)
        <tr class="tbl-row">
          <td class="tbl-cell">
            <a href="{{ route('admin.materials.edit', $mat->id) }}"
               class="text-ink-200 hover:text-forest-300 transition-colors text-xs font-medium">
              {{ Str::limit($mat->title, 36) }}
            </a>
          </td>
          <td class="tbl-cell">
            <span class="rp-badge badge-green">{{ $mat->resource->course_code ?? '—' }}</span>
          </td>
          <td class="tbl-cell text-center">
            @if($mat->pdf_path)
            <span class="rp-badge badge-blue">✓</span>
            @else
            <span class="text-ink-700 text-xs">—</span>
            @endif
          </td>
          <td class="tbl-cell text-right text-xs font-mono text-ink-600">
            {{ $mat->created_at?->diffForHumans() }}
          </td>
        </tr>
        @empty
        <tr><td colspan="4" class="tbl-cell text-center text-ink-700 py-6">No materials yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="space-y-3 fu4">
    @foreach([
      ['Total Users',      $totalUsers ?? 0,     '#4ade80', 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
      ['Total Materials',  $totalMaterials ?? 0, '#38bdf8', 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
      ['Active Lectures',  $totalLectures ?? 0,  '#fbbf24', 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
      ['Quiz Results',     $totalQuizzes ?? 0,   '#a78bfa', 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z'],
    ] as [$lbl, $val, $accent, $path])
    <div class="a-card-sm flex items-center gap-3">
      <div class="w-9 h-9 rounded-lg bg-ink-800 border border-ink-700 flex items-center justify-center flex-shrink-0">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="{{ $accent }}"
             stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
          <path d="{{ $path }}"/>
        </svg>
      </div>
      <div class="flex-1 min-w-0">
        <p class="font-display text-xl font-bold text-white">{{ number_format($val) }}</p>
        <p class="text-xs text-ink-600">{{ $lbl }}</p>
      </div>
    </div>
    @endforeach
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const container = document.querySelector("#chartAna");
  if (!container) return;
  if (window._rpChart) { window._rpChart.destroy(); window._rpChart = null; }

  const isDark = true;
  const options = {
    chart: { type:"area", height:130, toolbar:{show:false}, sparkline:{enabled:true}, animations:{enabled:true,speed:600} },
    stroke: { curve:"smooth", width:2 },
    fill:   { type:"gradient", gradient:{ shadeIntensity:.8, opacityFrom:.3, opacityTo:0, stops:[0,90,100] } },
    colors: ["#22c55e","#38bdf8"],
    series: [
      {name:"Visitors", data:[]},
      {name:"Pageviews",data:[]},
    ],
    tooltip:{ theme:"dark" },
    xaxis:  { categories:[], labels:{show:false}, axisBorder:{show:false}, axisTicks:{show:false} },
    yaxis:  { show:false },
    grid:   { show:false },
    noData: { text:"Loading…", style:{ color:"#334155", fontSize:"11px" } },
  };

  const chart = new ApexCharts(container, options);
  window._rpChart = chart;
  chart.render();

  async function fetchLive() {
    try {
      const res  = await fetch("{{ route('admin.analytics.live') }}");
      const data = await res.json();
      if (!data || !Array.isArray(data.labels)) return;
      chart.updateOptions({ xaxis:{ categories: data.labels } });
      chart.updateSeries([
        { name:"Visitors",  data: data.uniqueVisitors ?? [] },
        { name:"Pageviews", data: data.pageViews ?? [] },
      ]);
    } catch(e) { console.warn("Analytics fetch failed:", e); }
  }

  fetchLive();
  setInterval(fetchLive, 10000);
});
</script>
@endpush

@endsection