@extends('layouts.admin.master')
@section('title', 'Dashboard')
@php
    use Carbon\Carbon;
    $tzs = fn ($v) => number_format((float) $v, 0, '.', ',');
    $short = function ($v) {
        $v = (float) $v;
        if ($v >= 1e9) return number_format($v / 1e9, 2).'B';
        if ($v >= 1e6) return number_format($v / 1e6, 1).'M';
        if ($v >= 1e3) return number_format($v / 1e3, 1).'K';
        return number_format($v, 0);
    };
    $turn = function ($h) {
        if ($h === null) return '—';
        if ($h < 1) return round($h * 60).' min';
        if ($h < 48) return number_format($h, 1).' hrs';
        return number_format($h / 24, 1).' days';
    };
    $pillFor = fn ($s) => match (strtolower((string) $s)) {
        'approved' => 'approved', 'disbursed', 'posted to cbs' => 'disbursed', 'pending' => 'pending', default => 'other'
    };
    $hour = (int) Carbon::now('Africa/Dar_es_Salaam')->format('H');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp

@section('content')
<style>
  .db{--b:#0d47a1;--b2:#1976d2;--o:#f28c28;--g:#16a34a;--r:#dc2626;--ink:#1f2937;--mut:#6b7280;--line:#e7ebf3;--soft:#f5f8fd;color:var(--ink);}
  .db .card{border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 10px rgba(15,35,80,.04);margin-bottom:22px;}
  .db .card-header{background:transparent;border-bottom:1px solid var(--line);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;}
  .db .card-header h5{margin:0;font-size:15px;font-weight:700;}
  .db .card-header small{color:var(--mut);}
  .db .card-body{padding:18px 20px;}
  .db-hero{background:linear-gradient(120deg,#0b3c8c 0%,#0d47a1 45%,#1565c0 100%);border-radius:16px;color:#fff;padding:22px 26px;margin-bottom:22px;position:relative;overflow:hidden;}
  .db-hero:after{content:"";position:absolute;right:-60px;top:-60px;width:240px;height:240px;border-radius:50%;background:rgba(242,140,40,.18);}
  .db-hero h3{color:#fff;margin:0 0 4px;font-weight:700;font-size:22px;}
  .db-hero p{margin:0;color:#cfe0ff;font-size:13px;}
  .db-period{position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:6px;margin-top:14px;}
  .db-period a{color:#dbe7ff;border:1px solid rgba(255,255,255,.25);padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;}
  .db-period a.on,.db-period a:hover{background:#fff;color:var(--b);border-color:#fff;}
  .db-kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px;}
  @media (max-width:1199px){.db-kpis{grid-template-columns:repeat(2,1fr);}}
  @media (max-width:575px){.db-kpis{grid-template-columns:1fr;}}
  .db-kpi{background:#fff;border:1px solid var(--line);border-radius:14px;padding:16px 18px;position:relative;overflow:hidden;}
  .db-kpi:before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--accent,var(--b));}
  .db-kpi .lbl{font-size:11.5px;text-transform:uppercase;letter-spacing:.05em;color:var(--mut);font-weight:700;display:flex;align-items:center;gap:6px;}
  .db-kpi .lbl svg{width:15px;height:15px;color:var(--accent,var(--b));}
  .db-kpi .val{font-size:24px;font-weight:800;margin:6px 0 2px;line-height:1.15;font-variant-numeric:tabular-nums;}
  .db-kpi .val small{font-size:12px;color:var(--mut);font-weight:600;margin-right:3px;}
  .db-kpi .sub{font-size:12px;color:var(--mut);}
  .db-delta{display:inline-block;font-size:11px;font-weight:700;padding:1px 7px;border-radius:10px;margin-left:4px;}
  .db-delta.up{background:#e7f7ee;color:var(--g);} .db-delta.down{background:#fdecec;color:var(--r);}
  .db-meter{height:6px;border-radius:6px;background:#eef2f8;margin-top:8px;overflow:hidden;}
  .db-meter span{display:block;height:100%;background:linear-gradient(90deg,var(--g),#4ade80);border-radius:6px;}
  .db-queue{display:grid;grid-template-columns:repeat(auto-fit,minmax(118px,1fr));gap:12px;}
  @media (max-width:1399px){.db-queue{grid-template-columns:repeat(4,1fr);}}
  @media (max-width:575px){.db-queue{grid-template-columns:repeat(2,1fr);}}
  .db-q{display:block;border:1px solid var(--line);border-radius:12px;padding:12px 14px;background:var(--soft);transition:.15s;color:var(--ink);}
  .db-q:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(13,71,161,.12);border-color:#c9d8f3;color:var(--ink);}
  .db-q .n{font-size:22px;font-weight:800;line-height:1.1;}
  .db-q .t{font-size:12px;color:var(--mut);font-weight:600;display:flex;align-items:center;gap:5px;}
  .db-q .t svg{width:14px;height:14px;}
  .db-q.warning .n{color:#c2410c;} .db-q.info .n{color:var(--b);} .db-q.success .n{color:var(--g);} .db-q.danger .n{color:var(--r);}
  .db-chart{height:320px;} .db-chart.sm{height:280px;}
  .db-list{width:100%;font-size:12.5px;border-collapse:collapse;}
  .db-list td{padding:9px 6px;border-bottom:1px solid var(--line);vertical-align:middle;}
  .db-list tr:last-child td{border-bottom:0;}
  .db-list a{color:var(--b);font-weight:600;}
  .db-list .r{text-align:right;font-weight:700;white-space:nowrap;font-variant-numeric:tabular-nums;}
  .db-list .m{color:var(--mut);font-size:11.5px;display:block;}
  .db-pill{display:inline-block;padding:2px 9px;border-radius:20px;font-size:10.5px;font-weight:700;}
  .db-pill.approved{background:#e3f2fd;color:#0d47a1;} .db-pill.disbursed{background:#e6f6ee;color:#137a43;}
  .db-pill.pending{background:#f1f3f6;color:#55606e;} .db-pill.other{background:#fdecec;color:#c62828;}
  .db-age{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:14px;}
  .db-age div{border-radius:10px;padding:10px;text-align:center;background:var(--soft);}
  .db-age b{display:block;font-size:20px;} .db-age span{font-size:11px;color:var(--mut);font-weight:600;}
  .db-age .a3 b{color:#c2410c;} .db-age .a4{background:#fdecec;} .db-age .a4 b{color:var(--r);}
  .db-empty{color:var(--mut);text-align:center;padding:24px 0;font-size:13px;}
  .db-note{font-size:11.5px;color:var(--mut);}
  @media (max-width:575px){.db-hero{padding:18px;} .db-age{grid-template-columns:repeat(2,1fr);} .db-kpi .val{font-size:21px;}}
</style>

<div class="container-fluid db">

  {{-- Header with period filter --}}
  <div class="db-hero">
    <h3>{{ $greeting }}, {{ ucfirst(Auth::user()->first_name) }}</h3>
    <p>
      ESS e-Mkopo loan performance · {{ $periods[$period] }} ({{ $rangeLabel }})
      @if($branchOnly) · Branch {{ $branchOnly }} only @endif
    </p>
    <div class="db-period">
      @foreach($periods as $key => $label)
        <a href="{{ route('home', ['period' => $key]) }}" class="{{ $period === $key ? 'on' : '' }}">{{ $label }}</a>
      @endforeach
    </div>
  </div>

  {{-- KPI tiles --}}
  <div class="db-kpis">
    <div class="db-kpi" style="--accent:#0d47a1">
      <div class="lbl"><i data-feather="file-text"></i> Applications received</div>
      <div class="val">{{ number_format($kpi['apps']) }}
        @if($prev && $prev['apps'] !== null)<span class="db-delta {{ $prev['apps'] >= 0 ? 'up' : 'down' }}">{{ $prev['apps'] >= 0 ? '▲' : '▼' }} {{ abs($prev['apps']) }}%</span>@endif
      </div>
      <div class="sub">TZS {{ $short($kpi['requested']) }} requested @if($prev && $prev['apps'] !== null) · vs previous period @endif</div>
    </div>

    <div class="db-kpi" style="--accent:#16a34a">
      <div class="lbl"><i data-feather="send"></i> Disbursed</div>
      <div class="val"><small>TZS</small>{{ $short($kpi['disbursed']) }}
        @if($prev && $prev['disbursed'] !== null)<span class="db-delta {{ $prev['disbursed'] >= 0 ? 'up' : 'down' }}">{{ $prev['disbursed'] >= 0 ? '▲' : '▼' }} {{ abs($prev['disbursed']) }}%</span>@endif
      </div>
      <div class="sub">{{ number_format($kpi['disbursed_count']) }} loans · TZS {{ $tzs($kpi['disbursed']) }}</div>
    </div>

    <div class="db-kpi" style="--accent:#f28c28">
      <div class="lbl"><i data-feather="clock"></i> In pipeline</div>
      <div class="val"><small>TZS</small>{{ $short($kpi['pipeline']) }}</div>
      <div class="sub">{{ number_format($kpi['pipeline_count']) }} pending or approved, not yet disbursed</div>
    </div>

    <div class="db-kpi" style="--accent:#16a34a">
      <div class="lbl"><i data-feather="check-circle"></i> Approval rate</div>
      <div class="val">{{ $kpi['approval_rate'] !== null ? $kpi['approval_rate'].'%' : '—' }}</div>
      <div class="sub">{{ number_format($kpi['won']) }} approved · {{ number_format($kpi['lost']) }} rejected/cancelled</div>
      <div class="db-meter"><span style="width:{{ $kpi['approval_rate'] ?? 0 }}%"></span></div>
    </div>

    <div class="db-kpi" style="--accent:#7c3aed">
      <div class="lbl"><i data-feather="dollar-sign"></i> Processing fees</div>
      <div class="val"><small>TZS</small>{{ $short($kpi['fees']) }}</div>
      <div class="sub">On disbursed loans · repayable TZS {{ $short($kpi['repayable']) }}</div>
    </div>

    <div class="db-kpi" style="--accent:#0891b2">
      <div class="lbl"><i data-feather="bar-chart-2"></i> Average loan</div>
      <div class="val"><small>TZS</small>{{ $short($kpi['avg_amount']) }}</div>
      <div class="sub">Average tenure {{ number_format($kpi['avg_tenure'], 0) }} months</div>
    </div>

    <div class="db-kpi" style="--accent:#db2777">
      <div class="lbl"><i data-feather="zap"></i> Avg. turnaround</div>
      <div class="val">{{ $turn($kpi['turnaround']) }}</div>
      <div class="sub">From reaching MCB to disbursement</div>
    </div>

    <div class="db-kpi" style="--accent:#0f766e">
      <div class="lbl"><i data-feather="trending-up"></i> Expected interest</div>
      <div class="val"><small>TZS</small>{{ $short(max(0, $kpi['repayable'] - $kpi['disbursed'])) }}</div>
      <div class="sub">Total repayable minus principal on disbursed loans</div>
    </div>
  </div>

  {{-- Live work queue --}}
  <div class="card">
    <div class="card-header">
      <h5>Work queue <small class="ms-1">— live, all dates</small></h5>
      <small>Click a tile to open the list</small>
    </div>
    <div class="card-body">
      <div class="db-queue">
        @foreach($queue as $q)
          <a class="db-q {{ $q['tone'] }}" href="{{ $q['route'] }}">
            <div class="n">{{ number_format($q['count']) }}</div>
            <div class="t"><i data-feather="{{ $q['icon'] }}"></i> {{ $q['label'] }}</div>
          </a>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Trend + status --}}
  <div class="row">
    <div class="col-xl-8">
      <div class="card">
        <div class="card-header"><h5>Applications &amp; disbursement trend</h5><small>Bars: applications · Lines: TZS millions</small></div>
        <div class="card-body"><div id="chTrend" class="db-chart"></div></div>
      </div>
    </div>
    <div class="col-xl-4">
      <div class="card">
        <div class="card-header"><h5>Status breakdown</h5><small>{{ $periods[$period] }}</small></div>
        <div class="card-body"><div id="chStatus" class="db-chart"></div></div>
      </div>
    </div>
  </div>

  {{-- Employers + branches --}}
  <div class="row">
    <div class="col-xl-6">
      <div class="card">
        <div class="card-header"><h5>Top employers (votes)</h5><small>By amount requested, TZS M</small></div>
        <div class="card-body"><div id="chEmployers" class="db-chart"></div></div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="card">
        <div class="card-header"><h5>Top branches</h5><small>By amount requested, TZS M</small></div>
        <div class="card-body"><div id="chBranches" class="db-chart"></div></div>
      </div>
    </div>
  </div>

  {{-- Loan type + size bands + ageing --}}
  <div class="row">
    <div class="col-xl-4 col-md-6">
      <div class="card">
        <div class="card-header"><h5>Loan type mix</h5></div>
        <div class="card-body"><div id="chTypes" class="db-chart sm"></div></div>
      </div>
    </div>
    <div class="col-xl-4 col-md-6">
      <div class="card">
        <div class="card-header"><h5>Loan size distribution</h5><small>Number of applications</small></div>
        <div class="card-body"><div id="chBands" class="db-chart sm"></div></div>
      </div>
    </div>
    <div class="col-xl-4">
      <div class="card">
        <div class="card-header"><h5>Waiting CO — ageing</h5><a href="{{ route('loans-pending') }}" class="small">View all</a></div>
        <div class="card-body">
          <div class="db-age">
            @foreach($ageing as $label => $count)
              <div class="a{{ $loop->iteration }}"><b>{{ $count }}</b><span>{{ $label }}</span></div>
            @endforeach
          </div>
          @if($oldestPending->isEmpty())
            <div class="db-empty">Nothing waiting for the Credit Officer 🎉</div>
          @else
            <table class="db-list">
              @foreach($oldestPending as $l)
                <tr>
                  <td><a href="{{ route('staff-profile', ['id' => $l->ID]) }}">{{ strtoupper($l->FIRST_NAME.' '.$l->LAST_NAME) }}</a>
                      <span class="m">Waiting {{ Carbon::parse($l->APPLICATION_DATE)->diffForHumans(null, true) }}</span></td>
                  <td class="r">{{ $short($l->REQUESTED_AMOUNT) }}</td>
                </tr>
              @endforeach
            </table>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Recent applications --}}
  <div class="card">
    <div class="card-header"><h5>Latest applications</h5><a href="{{ route('loans-applications') }}" class="small">All loans</a></div>
    <div class="card-body">
      @if($recent->isEmpty())
        <div class="db-empty">No applications yet.</div>
      @else
        <div class="table-responsive">
          <table class="db-list">
            @foreach($recent as $l)
              <tr>
                <td><a href="{{ route('staff-profile', ['id' => $l->ID]) }}">{{ strtoupper($l->FIRST_NAME.' '.$l->LAST_NAME) }}</a>
                    <span class="m">{{ $l->APPLICATION_NUMBER }}</span></td>
                <td>{{ Carbon::parse($l->APPLICATION_DATE)->format('d M Y H:i') }}<span class="m">{{ Carbon::parse($l->APPLICATION_DATE)->diffForHumans() }}</span></td>
                <td>{{ ucwords(strtolower(str_replace('_', ' ', $l->LOAN_TYPE ?: 'NEW_LOAN'))) }}</td>
                <td><span class="db-pill {{ $pillFor($l->TRANSACTION_STATUS) }}">{{ $l->TRANSACTION_STATUS }}</span></td>
                <td class="r">TZS {{ $tzs($l->REQUESTED_AMOUNT) }}</td>
              </tr>
            @endforeach
          </table>
        </div>
      @endif
      <p class="db-note mt-3 mb-0">Figures count applications received through ESS (with a status). Dates are when the application reached MCB.</p>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/highcharts/highcharts.js') }}"></script>
<script src="{{ asset('assets/js/highcharts/exporting.js') }}"></script>
<script src="{{ asset('assets/js/highcharts/accessibility.js') }}"></script>
<script>
(function () {
  const C = @json($charts);
  const palette = ['#0d47a1', '#f28c28', '#16a34a', '#7c3aed', '#0891b2', '#dc2626', '#db2777', '#64748b'];
  const statusColor = {pending: '#94a3b8', approved: '#1976d2', disbursed: '#16a34a', 'posted to cbs': '#0f766e', rejected: '#dc2626', restructured: '#7c3aed'};
  const fmt = (v, d = 0) => Highcharts.numberFormat(v, d, '.', ',');

  Highcharts.setOptions({
    lang: {thousandsSep: ','},
    colors: palette,
    chart: {style: {fontFamily: 'inherit'}, backgroundColor: 'transparent', spacing: [26, 4, 8, 4]},
    title: {text: null}, credits: {enabled: false},
    legend: {itemStyle: {fontWeight: '600', color: '#475569', fontSize: '11.5px'}},
    xAxis: {lineColor: '#e5e9f2', tickColor: '#e5e9f2', labels: {style: {color: '#6b7280', fontSize: '11px'}}},
    yAxis: {gridLineColor: '#eef2f7', labels: {style: {color: '#6b7280', fontSize: '11px'}}, title: {style: {color: '#6b7280'}}},
    tooltip: {backgroundColor: '#0f172a', borderWidth: 0, borderRadius: 8, style: {color: '#fff', fontSize: '12px'}, shadow: false},
    navigation: {buttonOptions: {y: -6}},
    exporting: {buttons: {contextButton: {menuItems: ['downloadPNG', 'downloadPDF', 'downloadCSV', 'downloadXLS'], symbolStroke: '#94a3b8'}}},
  });
  const empty = (id) => { document.getElementById(id).innerHTML = '<div class="db-empty" style="padding-top:110px">No data for this period</div>'; };

  // Trend
  if (C.trend.apps.some(v => v > 0)) {
    Highcharts.chart('chTrend', {
      xAxis: {categories: C.trend.labels, crosshair: {color: 'rgba(13,71,161,.06)'}},
      yAxis: [{title: {text: 'Applications'}, allowDecimals: false}, {title: {text: 'TZS millions'}, opposite: true}],
      tooltip: {shared: true, formatter: function () {
        return '<b>' + this.x + '</b><br/>' + this.points.map(p => '<span style="color:' + p.color + '">●</span> ' + p.series.name + ': <b>' +
          (p.series.index === 0 ? fmt(p.y) : 'TZS ' + fmt(p.y, 2) + 'M') + '</b>').join('<br/>');
      }},
      plotOptions: {column: {borderRadius: 4, pointPadding: 0.08, groupPadding: 0.12, borderWidth: 0}, areaspline: {fillOpacity: 0.12, marker: {radius: 3}}, spline: {marker: {radius: 3}}},
      series: [
        {type: 'column', name: 'Applications', data: C.trend.apps, color: {linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1}, stops: [[0, '#1e63c4'], [1, '#9cc3f5']]}},
        {type: 'spline', name: 'Requested', yAxis: 1, data: C.trend.requested, color: '#f28c28', dashStyle: 'ShortDash'},
        {type: 'areaspline', name: 'Disbursed', yAxis: 1, data: C.trend.disbursed, color: '#16a34a'},
      ],
    });
  } else { empty('chTrend'); }

  // Status donut
  if (C.status.length) {
    const total = C.status.reduce((s, p) => s + p.y, 0);
    Highcharts.chart('chStatus', {
      chart: {type: 'pie'},
      subtitle: {text: '<div style="text-align:center"><span style="font-size:24px;font-weight:800;color:#1f2937">' + fmt(total) + '</span><br/><span style="font-size:11px;color:#6b7280">applications</span></div>', useHTML: true, align: 'center', verticalAlign: 'middle', y: -18},
      tooltip: {pointFormatter: function () { return '<b>' + fmt(this.y) + '</b> (' + fmt(this.percentage, 1) + '%)<br/>TZS ' + fmt(this.amount); }},
      plotOptions: {pie: {innerSize: '68%', borderWidth: 2, borderColor: '#fff', dataLabels: {enabled: false}, showInLegend: true, center: ['50%', '42%']}},
      legend: {layout: 'horizontal', align: 'center'},
      series: [{name: 'Status', data: C.status.map(p => Object.assign({}, p, {color: statusColor[(p.name || '').toLowerCase()] || '#f59e0b'}))}],
    });
  } else { empty('chStatus'); }

  // Horizontal bars
  const bars = (id, rows, color) => {
    if (!rows.length) { return empty(id); }
    Highcharts.chart(id, {
      chart: {type: 'bar'},
      xAxis: {categories: rows.map(r => r.name), labels: {style: {fontSize: '11px', color: '#374151', textOverflow: 'ellipsis'}}},
      yAxis: {title: {text: null}, labels: {format: '{value}M'}, maxPadding: 0.18, endOnTick: false},
      legend: {enabled: false},
      tooltip: {formatter: function () { const r = rows[this.point.index]; return '<b>' + r.name + '</b><br/>TZS ' + fmt(r.amount, 2) + 'M<br/>' + fmt(r.count) + ' applications'; }},
      plotOptions: {bar: {borderRadius: 4, borderWidth: 0, pointPadding: 0.1, groupPadding: 0.08, dataLabels: {enabled: true, format: '{y}M', style: {fontSize: '10.5px', fontWeight: '600', color: '#475569', textOutline: 'none'}}}},
      series: [{name: 'TZS M', data: rows.map(r => r.amount), color: color}],
    });
  };
  bars('chEmployers', C.employers, {linearGradient: {x1: 0, y1: 0, x2: 1, y2: 0}, stops: [[0, '#0d47a1'], [1, '#42a5f5']]});
  bars('chBranches', C.branches, {linearGradient: {x1: 0, y1: 0, x2: 1, y2: 0}, stops: [[0, '#e8730c'], [1, '#fbbf72']]});

  // Loan types
  if (C.types.length) {
    Highcharts.chart('chTypes', {
      chart: {type: 'pie'},
      tooltip: {pointFormatter: function () { return '<b>' + fmt(this.y) + '</b> (' + fmt(this.percentage, 1) + '%)<br/>TZS ' + fmt(this.amount); }},
      plotOptions: {pie: {innerSize: '55%', borderWidth: 2, borderColor: '#fff', dataLabels: {enabled: true, format: '{point.percentage:.0f}%', distance: -22, style: {color: '#fff', textOutline: 'none', fontWeight: '700'}}, showInLegend: true}},
      series: [{name: 'Loan type', data: C.types}],
    });
  } else { empty('chTypes'); }

  // Size bands
  if (C.bands.some(b => b[1] > 0)) {
    Highcharts.chart('chBands', {
      chart: {type: 'column'},
      xAxis: {categories: C.bands.map(b => b[0])},
      yAxis: {title: {text: null}, allowDecimals: false},
      legend: {enabled: false},
      tooltip: {pointFormat: '<b>{point.y}</b> applications'},
      plotOptions: {column: {borderRadius: 5, borderWidth: 0, colorByPoint: true, dataLabels: {enabled: true, style: {fontWeight: '700', textOutline: 'none', color: '#334155'}}}},
      colors: ['#bbdefb', '#64b5f6', '#1e88e5', '#1565c0', '#0d47a1'],
      series: [{name: 'Applications', data: C.bands.map(b => b[1])}],
    });
  } else { empty('chBands'); }

  if (window.feather) { feather.replace(); }
})();
</script>
@endpush
