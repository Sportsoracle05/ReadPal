@extends('layouts.admin')
@section('title', 'Analytics')
@section('page_title', 'Live Analytics')
@section('page_sub', 'Super Admin · Real-time traffic & engagement')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5 fu">
  <div>
    <h2 class="font-display text-2xl font-bold text-white">Live Analytics</h2>
    <p class="text-xs text-ink-500 mt-0.5">
      Auto-refreshes every 10s &nbsp;·&nbsp;
      <span id="last-refresh" class="font-mono text-ink-600">—</span>
    </p>
  </div>
  <div class="flex items-center gap-2.5">
    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-forest-950 border border-forest-900">
      <div class="live-dot w-2 h-2"></div>
      <span class="text-xs text-forest-400 font-semibold">Live</span>
    </div>
    <span class="text-xs text-ink-600 font-mono px-3 py-1.5 rounded-lg bg-ink-800 border border-ink-800" id="vis-count">
      — online
    </span>
  </div>
</div>

{{-- ── Top stat row ────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
  @foreach([
    ['Unique Visitors',  number_format($uniqueVisitors),  $visitorChange,  true,  '#4ade80'],
    ['Total Pageviews',  number_format($totalPageviews),  $pageviewChange, true,  '#38bdf8'],
    ['Bounce Rate',      $bounceRate . '%',               $bounceChange,   false, '#fbbf24'],
    ['Avg Duration',     $duration . 's',                 $durationChange, true,  '#a78bfa'],
  ] as $i => [$label, $val, $change, $up, $color])
  <div class="a-card fu" style="animation-delay:{{ $i*.06 }}s">
    <p class="text-3xl font-display font-bold" style="color:{{ $color }}">{{ $val }}</p>
    <p class="text-xs text-ink-600 mt-0.5 mb-2">{{ $label }}</p>
    <span class="{{ $up ? 'stat-up' : 'stat-dn' }}">
      {{ $up ? '+' : '' }}{{ number_format($change, 1) }}% vs last month
    </span>
  </div>
  @endforeach
</div>

{{-- ── Main chart ──────────────────────────────────────────────── --}}
<div class="a-card mb-4 fu1">
  <div class="flex items-center justify-between mb-4">
    <div>
      <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider">Traffic Trend</p>
      <h3 class="font-display text-base font-bold text-white mt-0.5">Visitors & Pageviews – Last 30 Days</h3>
    </div>
    <div class="flex items-center gap-3 text-xs text-ink-500">
      <span class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-forest-500 inline-block"></span>Visitors
      </span>
      <span class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-sky-500 inline-block"></span>Pageviews
      </span>
    </div>
  </div>
  <div id="mainChart" style="min-height:260px;"></div>
</div>

{{-- ── Mid row: user averages + top pages + top materials ─────── --}}
<div class="grid lg:grid-cols-3 gap-4 mb-4">

  {{-- User Averages + Live sparkline --}}
  <div class="a-card fu2">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display text-sm font-bold text-white">Active Users</h3>
      <div class="flex items-center gap-1.5">
        <div class="live-dot w-1.5 h-1.5"></div>
        <span class="text-xs text-forest-500">Live</span>
      </div>
    </div>

    <div class="flex items-end gap-2 mb-3">
      <p class="font-display text-4xl font-bold text-white" id="live-num">
        {{ number_format($liveVisitors) }}
      </p>
      <p class="text-xs text-ink-500 mb-1">online now</p>
    </div>

    <div id="sparkChart" class="rounded-xl overflow-hidden" style="height:80px;margin:0 -4px;"></div>

    <div class="grid grid-cols-3 gap-2 mt-4 pt-3 border-t border-ink-800">
      @foreach([
        [$avgDailyUsers,   'Daily'],
        [$avgWeeklyUsers,  'Weekly'],
        [$avgMonthlyUsers, 'Monthly'],
      ] as [$v, $l])
      <div class="text-center">
        <p class="font-display text-lg font-bold text-white">{{ number_format($v,0) }}</p>
        <p class="text-xs text-ink-600">{{ $l }}</p>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Top Materials --}}
  <div class="a-card fu2">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display text-sm font-bold text-white">Top Materials</h3>
      <a href="{{ route('admin.materials.index') }}"
         class="text-xs text-forest-600 hover:text-forest-400 transition-colors">All →</a>
    </div>
    <div>
      <div class="flex justify-between pb-2 mb-1 border-b border-ink-800">
        <span class="text-xs text-ink-700 uppercase tracking-wider">Material</span>
        <span class="text-xs text-ink-700 uppercase tracking-wider">Views</span>
      </div>
      @forelse($topMaterials as $i => $m)
      <div class="flex items-center justify-between py-2.5 border-b border-ink-800/40 last:border-0 group">
        <div class="flex items-center gap-2 min-w-0">
          <span class="font-mono text-xs text-ink-700 w-4 flex-shrink-0">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span>
          <span class="text-xs text-ink-300 group-hover:text-ink-100 transition-colors truncate max-w-[140px]">
            {{ $m->title }}
          </span>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <div class="progress-track w-14 h-1.5">
            @php $max = $topMaterials->max('total_views') ?: 1; @endphp
            <div class="progress-fill" style="width:{{ round($m->total_views/$max*100) }}%;height:100%;"></div>
          </div>
          <span class="font-mono text-xs text-forest-400">{{ number_format($m->total_views) }}</span>
        </div>
      </div>
      @empty
      <p class="text-xs text-ink-700 text-center py-6">No data yet.</p>
      @endforelse
    </div>
  </div>

  {{-- Top Pages --}}
  <div class="a-card fu3">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display text-sm font-bold text-white">Top Pages</h3>
    </div>
    <div>
      <div class="flex justify-between pb-2 mb-1 border-b border-ink-800">
        <span class="text-xs text-ink-700 uppercase tracking-wider">Route</span>
        <span class="text-xs text-ink-700 uppercase tracking-wider">Visits</span>
      </div>
      @forelse($topPages as $i => $page)
      <div class="flex items-center justify-between py-2.5 border-b border-ink-800/40 last:border-0 group">
        <div class="flex items-center gap-2 min-w-0">
          <span class="font-mono text-xs text-ink-700 w-4 flex-shrink-0">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span>
          <span class="text-xs font-mono text-ink-300 group-hover:text-ink-100 transition-colors truncate max-w-[130px]">
            /{{ $page->page }}
          </span>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <div class="progress-track w-14 h-1.5">
            @php $maxP = $topPages->max('total_visits') ?: 1; @endphp
            <div class="h-full rounded-full" style="width:{{ round($page->total_visits/$maxP*100) }}%;background:linear-gradient(90deg,#0369a1,#38bdf8);"></div>
          </div>
          <span class="font-mono text-xs text-sky-400">{{ number_format($page->total_visits) }}</span>
        </div>
      </div>
      @empty
      <p class="text-xs text-ink-700 text-center py-6">No data yet.</p>
      @endforelse
    </div>
  </div>
</div>

{{-- ── Bounce / Duration metrics ────────────────────────────────── --}}
<div class="grid lg:grid-cols-2 gap-4 fu4">
  <div class="a-card">
    <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider mb-3">Bounce Rate Trend</p>
    <div id="bounceChart" style="min-height:120px;"></div>
  </div>
  <div class="a-card">
    <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider mb-3">Session Duration Trend</p>
    <div id="durationChart" style="min-height:120px;"></div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
/* ── Shared dark theme defaults ─────────────────────────── */
const darkTheme = {
  chart:{ toolbar:{show:false}, background:'transparent',
          fontFamily:"'DM Sans',sans-serif" },
  theme:{ mode:'dark' },
  grid:{ borderColor:'#1e293b', strokeDashArray:3 },
  xaxis:{ labels:{ style:{ colors:'#475569', fontSize:'11px' } }, axisBorder:{show:false}, axisTicks:{show:false} },
  yaxis:{ labels:{ style:{ colors:'#475569', fontSize:'11px' } } },
  tooltip:{ theme:'dark', style:{ fontSize:'12px' } },
};

/* ── Main traffic chart ──────────────────────────────────── */
const mainChart = new ApexCharts(document.querySelector('#mainChart'), {
  ...darkTheme,
  chart:{ ...darkTheme.chart, type:'area', height:260, animations:{enabled:true,speed:500} },
  stroke:{ curve:'smooth', width:[2,2] },
  fill:{ type:'gradient', gradient:{ shadeIntensity:.7, opacityFrom:.25, opacityTo:0, stops:[0,80,100] } },
  colors:['#22c55e','#38bdf8'],
  series:[ {name:'Visitors',data:[]}, {name:'Pageviews',data:[]} ],
  xaxis:{ ...darkTheme.xaxis, categories:[] },
  legend:{ show:false },
  dataLabels:{ enabled:false },
  noData:{ text:'Loading…', style:{ color:'#334155', fontSize:'12px' } },
});
mainChart.render();

/* ── Spark chart (live users) ────────────────────────────── */
const sparkChart = new ApexCharts(document.querySelector('#sparkChart'), {
  chart:{ type:'line', height:80, sparkline:{enabled:true}, toolbar:{show:false}, background:'transparent',
          animations:{enabled:true,speed:300} },
  stroke:{ curve:'smooth', width:2 },
  fill:{ type:'gradient', gradient:{ shadeIntensity:.8, opacityFrom:.3, opacityTo:0 } },
  colors:['#22c55e'],
  series:[{name:'Online',data:[]}],
  tooltip:{ theme:'dark', fixed:{enabled:false} },
});
sparkChart.render();

/* ── Bounce rate chart ───────────────────────────────────── */
const bounceChart = new ApexCharts(document.querySelector('#bounceChart'), {
  ...darkTheme,
  chart:{ ...darkTheme.chart, type:'bar', height:120, sparkline:{enabled:false} },
  colors:['#fbbf24'],
  series:[{name:'Bounce %',data:[]}],
  xaxis:{ ...darkTheme.xaxis, categories:[] },
  yaxis:{ ...darkTheme.yaxis, max:100 },
  plotOptions:{ bar:{ borderRadius:4, columnWidth:'60%' } },
  dataLabels:{ enabled:false },
  noData:{ text:'Loading…', style:{ color:'#334155', fontSize:'12px' } },
});
bounceChart.render();

/* ── Duration chart ──────────────────────────────────────── */
const durationChart = new ApexCharts(document.querySelector('#durationChart'), {
  ...darkTheme,
  chart:{ ...darkTheme.chart, type:'area', height:120 },
  stroke:{ curve:'smooth', width:2 },
  fill:{ type:'gradient', gradient:{ shadeIntensity:.8, opacityFrom:.2, opacityTo:0 } },
  colors:['#a78bfa'],
  series:[{name:'Duration (s)',data:[]}],
  xaxis:{ ...darkTheme.xaxis, categories:[] },
  dataLabels:{ enabled:false },
  noData:{ text:'Loading…', style:{ color:'#334155', fontSize:'12px' } },
});
durationChart.render();

/* ── Live sparkline data buffer ──────────────────────────── */
const sparkBuffer = Array(20).fill(0);

/* ── Fetch + update all charts ───────────────────────────── */
async function fetchAnalytics() {
  try {
    const res  = await fetch("{{ route('admin.analytics.live') }}");
    if (!res.ok) throw new Error(res.statusText);
    const data = await res.json();

    if (!data || !Array.isArray(data.labels)) return;

    /* Main chart */
    mainChart.updateOptions({ xaxis:{ categories: data.labels } });
    mainChart.updateSeries([
      { name:'Visitors',  data: data.uniqueVisitors ?? [] },
      { name:'Pageviews', data: data.pageViews      ?? [] },
    ]);

    /* Bounce & duration */
    if (data.bounceRates) {
      bounceChart.updateOptions({ xaxis:{ categories: data.labels } });
      bounceChart.updateSeries([{ name:'Bounce %', data: data.bounceRates }]);
    }
    if (data.durations) {
      durationChart.updateOptions({ xaxis:{ categories: data.labels } });
      durationChart.updateSeries([{ name:'Duration (s)', data: data.durations }]);
    }

    /* Live visitors */
    const live = data.liveVisitors ?? 0;
    sparkBuffer.push(live);
    sparkBuffer.shift();
    sparkChart.updateSeries([{ name:'Online', data:[...sparkBuffer] }]);

    document.getElementById('live-num').textContent = live.toLocaleString();
    document.getElementById('vis-count').textContent = live + ' online';
    document.getElementById('last-refresh').textContent =
      new Date().toLocaleTimeString('en-NG', {hour:'2-digit', minute:'2-digit', second:'2-digit'});

  } catch(e) {
    console.warn('Analytics refresh failed:', e);
  }
}

fetchAnalytics();
setInterval(fetchAnalytics, 10000);
</script>
@endpush

@endsection