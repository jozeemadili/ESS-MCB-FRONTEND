<div class="lp">
@php
    use Carbon\Carbon;
    $TZ = 'Africa/Dar_es_Salaam';
    $v = fn ($x) => ($x === null || $x === '') ? '—' : $x;
    $money = fn ($x) => number_format((float) $x, 2, '.', ',');
    $dt = function ($x, $withTime = false) use ($TZ) {
        if ($x === null || $x === '') return '—';
        try { $c = $x instanceof \DateTimeInterface ? Carbon::instance($x) : Carbon::parse($x); } catch (\Throwable $e) { return $x; }
        return $c->format($withTime ? 'd M Y, H:i' : 'd M Y');
    };
    // Gateway (Java) dates are saved in local time; portal approval-stage dates are saved in UTC.
    $local = function ($x) use ($TZ) {
        if (empty($x)) return null;
        $raw = $x instanceof \DateTimeInterface ? $x->format('Y-m-d H:i:s') : (string) $x;
        return Carbon::parse(substr($raw, 0, 19), $TZ);
    };
    $utc = fn ($x) => empty($x) ? null : Carbon::parse(($x instanceof \DateTimeInterface ? $x->format('Y-m-d H:i:s') : substr((string) $x, 0, 19)), 'UTC')->setTimezone($TZ);
    $dur = function (?int $mins) {
        if ($mins === null) return null;
        if ($mins < 1) return 'under 1 min';
        if ($mins < 60) return $mins.' min';
        if ($mins < 1440) return intdiv($mins, 60).'h '.($mins % 60).'m';
        return intdiv($mins, 1440).'d '.intdiv($mins % 1440, 60).'h';
    };
    $tone = fn (?int $mins) => $mins === null ? 'mut' : ($mins <= 1440 ? 'ok' : ($mins <= 4320 ? 'warn' : 'bad'));

    $tx = strtolower((string) $vehicle->TRANSACTION_STATUS);
    $ls = strtolower((string) $vehicle->LOAN_STATUS);
    $pill = fn ($s) => match (strtolower((string) $s)) {
        'approved', 'accepted' => 'blue', 'disbursed', 'posted to cbs', 'active', 'complete' => 'green',
        'pending', 'initiated' => 'grey', 'restructured' => 'purple', default => 'red'
    };

    // ---------- Approval timeline (read only: built from the dates already saved on the loan) ----------
    $stages = \App\Models\DisburmentApprovalStage::where('loan_id', $vehicle->ID)->orderBy('id')->get();
    $userIds = $stages->pluck('initiator_id')->merge($stages->pluck('checker_id'))->filter()->unique();
    $users = $userIds->isEmpty() ? collect() : \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
    $who = function ($id) use ($users) {
        if (!$id || !isset($users[$id])) return null;
        $f = trim((string) $users[$id]->first_name); $l = trim((string) $users[$id]->last_name);
        return ($l === '' || stripos($f, $l) !== false) ? $f : trim($f.' '.$l);
    };

    $caStage = $stages->first(fn ($s) => in_array($s->status, ['INITIATED', 'INITIATED_REJECTION', 'DISBURSED']));
    $copReject = $stages->first(fn ($s) => $s->status === 'REJECTED');

    $appliedAt = preg_match('/^ESS(\d{13})$/', (string) $vehicle->APPLICATION_NUMBER, $m) ? Carbon::createFromTimestampMs((int) $m[1])->setTimezone($TZ) : null;
    $receivedAt = $local($vehicle->APPLICATION_DATE);
    if ($appliedAt && $receivedAt && $appliedAt->gt($receivedAt)) { $appliedAt = null; } // number issued after MCB received it
    $employerAt = $local($vehicle->APPROVED_DATE);
    $disbursedAt = $local($vehicle->LOAN_DISBURSED_DATE);
    $coAt = $local($vehicle->CO_DECISION_DATE); // set by the gateway when ESS accepts the CO decision

    $rejected = $tx === 'rejected' || $ls === 'rejected';
    // Who rejected: ESS rejections (employer final approval, or employee declining a restructure) set TRANSACTION_STATUS
    // to Rejected; a Credit Officer rejection only sets LOAN_STATUS.
    $essRejected = $tx === 'rejected';
    $coRejected = $ls === 'rejected' && !$essRejected;
    $reasonRaw = trim((string) $vehicle->REASON);
    $employeeDeclined = $essRejected && ($vehicle->APPROVED_BY === 'Employee (ESS)'
        || ($vehicle->APPROVED_BY !== 'Employer (ESS)' && !$vehicle->APPROVAL_DESCRIPTION && $reasonRaw !== '' && !in_array(strtoupper($reasonRaw), ['REJECTED', 'APPROVED', 'OK'])));
    $essReason = trim((string) $vehicle->APPROVAL_DESCRIPTION)
        ?: (!in_array(strtoupper($reasonRaw), ['', 'REJECTED', 'APPROVED', 'OK']) ? $reasonRaw : null);
    $cancelled = $ls === 'cancelled' || $tx === 'cancelled';
    $coDone = !in_array($ls, ['pending', '']) || !in_array($tx, ['pending', '']);
    // Employee cancellation on ESS (LOAN_CANCELLATION_NOTIFICATION), allowed until the employer approves. The Credit
    // Officer decided only if a decision time was recorded (a CO rejection keeps the loan Rejected, not Cancelled).
    $cancelledAt = $cancelled && $vehicle->APPROVED_BY === 'Employee (ESS)' ? $local($vehicle->APPROVED_DATE) : null;
    $cancelReason = $cancelled && !in_array(strtoupper($reasonRaw), ['', 'REJECTED', 'APPROVED', 'OK']) ? $reasonRaw : null;
    if ($cancelled) { $coDone = (bool) $coAt; }
    $employerDone = in_array($tx, ['approved', 'disbursed', 'posted to cbs', 'restructured']);
    $copDone = ($caStage && $caStage->status === 'DISBURSED') || in_array($tx, ['disbursed', 'posted to cbs', 'restructured']);
    $isRestructure = $vehicle->LOAN_TYPE === 'RESTRUCTURING';

    // state: done | rejected | current | waiting
    $steps = [
        ['key' => 'applied',  'title' => 'Applied on ESS',            'who' => 'Employee (ESS Utumishi)', 'at' => $appliedAt,  'state' => 'done', 'note' => $appliedAt ? null : 'Date not provided by ESS'],
        ['key' => 'received', 'title' => 'Received at MCB',           'who' => 'ESS gateway',              'at' => $receivedAt, 'state' => 'done'],
        ['key' => 'co',       'title' => 'Credit Officer review'.($coDone ? ($coRejected ? ' – rejected' : ' – accepted') : ''),
            'who' => $vehicle->CO_DECISION_BY ?: 'Credit Officer', 'at' => $coAt,
            'state' => $coDone ? ($coRejected ? 'rejected' : 'done') : ($cancelled || $rejected ? 'rejected' : 'current'),
            'note' => ($coDone && !$coAt) ? 'Decision time not recorded (decided before the portal started recording it)' : null],
        ['key' => 'employer', 'title' => $employeeDeclined ? 'Employee declined the restructure (ESS)' : 'Employer approval (ESS)'.($essRejected ? ' – rejected' : ''),
            'who' => $employeeDeclined ? 'Employee via ESS' : 'Employer / HR via ESS', 'at' => $cancelled ? null : $employerAt,
            'state' => $employerDone ? 'done' : ($essRejected ? 'rejected' : ($coDone && !$coRejected && !$cancelled ? 'current' : 'waiting')),
            'note' => $essRejected ? 'Reason: '.($essReason ?: 'not saved (rejected before the portal recorded ESS reasons)').(!$employerAt ? ' · Time not recorded' : '')
                : (($employerDone && !$employerAt) ? 'Approval time not recorded' : null)],
        ['key' => 'ca',       'title' => $isRestructure ? 'Credit Admin – restructure initiation' : 'Credit Admin – disbursement initiation',
            'who' => $who($caStage->initiator_id ?? null) ?? ($vehicle->LOAN_REQUEST_INITIATOR ?: 'Credit Admin'), 'at' => $utc($caStage->initiator_date ?? null),
            'state' => $caStage ? ($caStage->status === 'INITIATED_REJECTION' ? 'rejected' : 'done') : ($employerDone && !$copDone ? 'current' : ($copDone ? 'done' : 'waiting')),
            'note' => $caStage && $caStage->status === 'INITIATED_REJECTION' ? 'Rejection initiated' : (($copDone && !$caStage) ? 'Time not recorded' : null)],
        ['key' => 'cop',      'title' => $isRestructure ? 'Credit Operation – restructure completed' : 'Credit Operation – approval',
            'who' => $who($caStage->checker_id ?? null) ?? $who($copReject->initiator_id ?? null) ?? ($vehicle->LOAN_REQUEST_CHECKER ?: 'Credit Operation'),
            'at' => $utc($caStage->checker_date ?? null) ?? $utc($copReject->initiator_date ?? null),
            'state' => $copReject ? 'rejected' : ($copDone ? 'done' : ($caStage && $caStage->status !== 'DISBURSED' ? 'current' : 'waiting')),
            'note' => $copReject ? 'Rejected' : (($copDone && !$caStage) ? 'Time not recorded' : null)],
        ['key' => 'disbursed','title' => $isRestructure ? 'Restructure completed in core banking' : 'Disbursed to customer', 'who' => 'Core banking (Profits)', 'at' => $disbursedAt,
            'state' => in_array($tx, ['disbursed', 'posted to cbs', 'restructured']) || $disbursedAt ? 'done' : ($copDone ? 'current' : 'waiting'),
            'note' => (in_array($tx, ['disbursed', 'posted to cbs']) && !$disbursedAt) ? 'Time not recorded' : null],
    ];
    if ($cancelled) {
        $cancelStep = ['key' => 'cancelled', 'title' => 'Cancelled by employee (ESS)', 'who' => 'Employee via ESS', 'at' => $cancelledAt,
            'state' => 'rejected', 'note' => 'Reason: '.($cancelReason ?: 'not given').(!$cancelledAt ? ' · Time not recorded' : '')];
        $at = $coDone ? 3 : 2; // after the Credit Officer step if the officer had decided, otherwise before it
        array_splice($steps, $at, 0, [$cancelStep]);
    }
    // Takeover (MCB = FSP2): after disbursement, staff upload the proof of payment to FSP1 and the gateway notifies ESS
    $isTakeover = $vehicle->LOAN_TYPE === 'TAKEOVER';
    $tkProof = $isTakeover ? \App\Models\PAYMENT::forLoan($vehicle) : null;
    if ($isTakeover) {
        $disbDone = in_array($tx, ['disbursed', 'posted to cbs']) || $disbursedAt;
        $proofDone = $tkProof && $tkProof->CBS_REFERENCE && $tkProof->PAYMENT_ADVICE_ATTACHMENT;
        $essDone = ($tkProof->STATUS ?? null) === 'NOTIFIED' || $tx === 'disbursed';
        $steps[] = ['key' => 'proof', 'title' => 'Proof of payment to FSP1 uploaded',
            'who' => ($proofDone && $tkProof->PAID_BY) ? $tkProof->PAID_BY : 'Credit Operation / Credit Admin', 'at' => $proofDone ? $local($tkProof->PAID_DATE) : null,
            'state' => $proofDone ? 'done' : ($disbDone ? 'current' : 'waiting'),
            'note' => $proofDone ? 'Reference '.$tkProof->CBS_REFERENCE.' · TZS '.number_format((float) $tkProof->PAID_AMOUNT, 2) : null];
        $steps[] = ['key' => 'ess', 'title' => 'ESS notified (TAKEOVER_DISBURSEMENT_NOTIFICATION)', 'who' => 'ESS gateway',
            'at' => $essDone ? $local($tkProof->NOTIFIED_DATE ?? null) : null,
            'state' => $essDone ? 'done' : ($proofDone ? 'current' : 'waiting'),
            'note' => (!$essDone && $proofDone && $tkProof->STATUS_DESCRIPTION) ? $tkProof->STATUS_DESCRIPTION.' · retried automatically' : null];
    }
    // Once rejected/cancelled, the remaining steps did not happen
    $stopped = false;
    foreach ($steps as $i => $s) {
        if ($stopped) { $steps[$i]['state'] = 'skipped'; continue; }
        if ($s['state'] === 'rejected') { $stopped = true; }
    }
    // TAT for each step = time since the previous step that has a recorded time
    $now = Carbon::now($TZ);
    $lastAt = null; $lastTitle = null;
    foreach ($steps as $i => $s) {
        $steps[$i]['tat'] = null; $steps[$i]['from'] = null; $steps[$i]['waiting'] = null;
        if ($s['at'] && $lastAt && $s['at']->gte($lastAt)) {
            $steps[$i]['tat'] = $lastAt->diffInMinutes($s['at']);
            $steps[$i]['from'] = $lastTitle;
        } elseif ($s['state'] === 'current' && $lastAt) {
            $steps[$i]['waiting'] = $lastAt->diffInMinutes($now);
            $steps[$i]['from'] = $lastTitle;
        }
        if ($s['at']) { $lastAt = $s['at']; $lastTitle = $s['title']; }
    }
    $endAt = collect($steps)->filter(fn ($s) => $s['at'] && $s['state'] !== 'skipped')->last()['at'] ?? null;
    $finished = ($isTakeover ? $tx === 'disbursed' : in_array($tx, ['disbursed', 'posted to cbs', 'restructured'])) || $rejected || $cancelled;
    $totalMins = $receivedAt ? $receivedAt->diffInMinutes($finished && $endAt ? $endAt : $now) : null;
    if ($finished && (!$endAt || ($receivedAt && $endAt->eq($receivedAt)))) { $totalMins = null; } // end time not recorded

    $instalment = \App\Http\Livewire\Components\Vehicle\Details::instalment($vehicle);
    $initials = strtoupper(substr((string) $vehicle->FIRST_NAME, 0, 1).substr((string) $vehicle->LAST_NAME, 0, 1));
@endphp

<style>
  .lp{--b:#0d47a1;--b2:#1976d2;--o:#f28c28;--ink:#1f2937;--mut:#6b7280;--line:#e7ebf3;--soft:#f6f8fc;color:var(--ink);}
  .lp-card{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 10px rgba(15,35,80,.04);margin-bottom:20px;}
  .lp-card-h{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;padding:14px 20px;border-bottom:1px solid var(--line);}
  .lp-card-h h6{margin:0;font-size:14.5px;font-weight:700;display:flex;align-items:center;gap:8px;}
  .lp-card-h h6 i{color:var(--b);}
  .lp-card-h small{color:var(--mut);}
  .lp-card-b{padding:16px 20px;}
  .lp-hero{background:linear-gradient(120deg,#0b3c8c,#0d47a1 50%,#1565c0);border-radius:16px;color:#fff;padding:22px 24px;margin-bottom:20px;position:relative;overflow:hidden;}
  .lp-hero:after{content:"";position:absolute;right:-70px;top:-70px;width:250px;height:250px;border-radius:50%;background:rgba(242,140,40,.18);}
  .lp-hero-top{display:flex;align-items:center;gap:16px;flex-wrap:wrap;position:relative;z-index:1;}
  .lp-av{width:58px;height:58px;border-radius:50%;background:#fff;color:var(--b);font-weight:800;font-size:20px;display:flex;align-items:center;justify-content:center;flex:0 0 58px;}
  .lp-hero h4{color:#fff;margin:0;font-weight:700;font-size:20px;}
  .lp-hero .ids{color:#cfe0ff;font-size:12.5px;margin-top:3px;}
  .lp-hero .ids b{color:#fff;font-weight:600;}
  .lp-tags{margin-left:auto;display:flex;gap:6px;flex-wrap:wrap;}
  .lp-figs{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin-top:18px;position:relative;z-index:1;}
  .lp-fig{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.16);border-radius:12px;padding:10px 14px;}
  .lp-fig span{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#cfe0ff;font-weight:700;}
  .lp-fig b{font-size:17px;font-weight:800;font-variant-numeric:tabular-nums;}
  .lp-fig b small{font-size:11px;font-weight:600;color:#cfe0ff;margin-right:3px;}
  .lp-pill{display:inline-flex;align-items:center;gap:6px;padding:4px 11px;border-radius:20px;font-size:11.5px;font-weight:700;background:#fff;}
  .lp-pill:before{content:"";width:7px;height:7px;border-radius:50%;background:currentColor;}
  .lp-pill.blue{color:#0d47a1;} .lp-pill.green{color:#137a43;} .lp-pill.grey{color:#55606e;} .lp-pill.red{color:#c62828;} .lp-pill.purple{color:#6d28d9;} .lp-pill.orange{color:#b45309;}
  .lp-pill.soft.blue{background:#e3f2fd;} .lp-pill.soft.green{background:#e6f6ee;} .lp-pill.soft.grey{background:#f1f3f6;} .lp-pill.soft.red{background:#fdecec;} .lp-pill.soft.purple{background:#f1e9fe;} .lp-pill.soft.orange{background:#fff4e5;}
  .lp-chip{display:inline-block;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;background:var(--o);color:#fff;}
  .lp-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:0 28px;}
  .lp-grid.three{grid-template-columns:repeat(3,1fr);}
  .lp-f{display:flex;justify-content:space-between;align-items:baseline;gap:12px;padding:9px 0;border-bottom:1px dashed var(--line);font-size:13px;}
  .lp-f dt{color:var(--mut);font-weight:500;flex:0 0 auto;}
  .lp-f dd{margin:0;font-weight:600;text-align:right;word-break:break-word;font-variant-numeric:tabular-nums;}
  .lp-f dd small{color:var(--mut);font-weight:500;}
  .lp-f dd.mono{font-family:SFMono-Regular,Menlo,Consolas,monospace;font-size:12.5px;}
  /* timeline */
  .lp-tat-sum{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
  .lp-tat-sum .tot{font-size:13px;color:var(--mut);}
  .lp-tat-sum .tot b{font-size:15px;color:var(--ink);}
  .lp-tl{list-style:none;margin:0;padding:0;position:relative;}
  .lp-tl li{display:grid;grid-template-columns:34px 1fr auto;gap:14px;position:relative;padding:0 0 18px;}
  .lp-tl li:last-child{padding-bottom:0;}
  .lp-tl li:before{content:"";position:absolute;left:16px;top:30px;bottom:-2px;width:2px;background:var(--line);}
  .lp-tl li:last-child:before{display:none;}
  .lp-tl li.done:before{background:#a7d7b9;}
  .lp-dot{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;position:relative;z-index:1;border:2px solid var(--line);background:#fff;color:var(--mut);}
  .lp-tl li.done .lp-dot{background:#16a34a;border-color:#16a34a;color:#fff;}
  .lp-tl li.current .lp-dot{background:#fff7ed;border-color:var(--o);color:var(--o);box-shadow:0 0 0 4px rgba(242,140,40,.15);}
  .lp-tl li.rejected .lp-dot{background:#dc2626;border-color:#dc2626;color:#fff;}
  .lp-tl li.skipped{opacity:.45;}
  .lp-tl .t{font-weight:700;font-size:13.5px;}
  .lp-tl .s{font-size:12px;color:var(--mut);margin-top:2px;}
  .lp-tl .s b{color:var(--ink);font-weight:600;}
  .lp-tl .n{font-size:11.5px;color:#9a6b1f;margin-top:3px;font-style:italic;}
  .lp-tat{text-align:right;white-space:nowrap;}
  .lp-tat .v{display:inline-block;font-size:12px;font-weight:800;padding:3px 10px;border-radius:20px;}
  .lp-tat .v.ok{background:#e6f6ee;color:#137a43;} .lp-tat .v.warn{background:#fff4e5;color:#b45309;} .lp-tat .v.bad{background:#fdecec;color:#c62828;} .lp-tat .v.mut{background:#f1f3f6;color:#64748b;}
  .lp-tat .k{display:block;font-size:10.5px;color:var(--mut);margin-top:3px;}
  .lp-legend{font-size:11px;color:var(--mut);display:flex;gap:12px;flex-wrap:wrap;}
  .lp-legend i{display:inline-block;width:9px;height:9px;border-radius:50%;margin-right:4px;vertical-align:middle;}
  /* actions area */
  .lp-actions .btn{border-radius:10px;font-weight:600;padding:9px 16px;}
  .lp-actions .list-group-item{border:1px solid var(--line);border-radius:10px;margin-bottom:8px;list-style:none;}
  .lp-actions .alert{border-radius:10px;}
  .lp .table-sm th{font-size:11px;text-transform:uppercase;color:#64748b;background:var(--soft);}
  @media (max-width:1199px){.lp-figs{grid-template-columns:repeat(3,1fr);} .lp-grid.three{grid-template-columns:repeat(2,1fr);}}
  @media (max-width:767px){.lp-grid,.lp-grid.three{grid-template-columns:1fr;} .lp-figs{grid-template-columns:repeat(2,minmax(0,1fr));} .lp-fig{padding:9px 11px;} .lp-fig b{font-size:15px;white-space:nowrap;} .lp-hero{padding:18px 16px;} .lp-tags{margin-left:0;} .lp-tl li{grid-template-columns:30px 1fr;} .lp-tat{grid-column:2;text-align:left;}}
  @media (max-width:430px){.lp-figs{grid-template-columns:1fr;}}
</style>

{{-- ===== Summary header ===== --}}
<div class="lp-hero">
  <div class="lp-hero-top">
    <div class="lp-av">{{ $initials ?: '?' }}</div>
    <div>
      <h4>{{ strtoupper(trim(preg_replace('/\s+/', ' ', $vehicle->FIRST_NAME.' '.$vehicle->MIDDLE_NAME.' '.$vehicle->LAST_NAME))) }}</h4>
      <div class="ids">Application <b>{{ $v($vehicle->APPLICATION_NUMBER) }}</b> · Loan ID <b>{{ $v($vehicle->LOAN_ID) }}</b> · Check No. <b>{{ $v($vehicle->CHECK_NUMBER) }}</b></div>
      <div class="ids">{{ $v($vehicle->VOTE_NAME) }} · {{ $v($vehicle->DESIGNATION_NAME) }}</div>
    </div>
    <div class="lp-tags">
      <span class="lp-chip">{{ ucwords(strtolower(str_replace('_', ' ', $vehicle->LOAN_TYPE ?: 'NEW_LOAN'))) }}</span>
      @if($cancelled)
        <span class="lp-pill red" title="Transaction status {{ $vehicle->TRANSACTION_STATUS }} · Loan status {{ $vehicle->LOAN_STATUS }}">Cancelled by employee (ESS)</span>
      @elseif($essRejected || $coRejected)
        <span class="lp-pill red" title="Transaction status {{ $vehicle->TRANSACTION_STATUS }} · Loan status {{ $vehicle->LOAN_STATUS }}">{{ $coRejected ? 'Rejected by Credit Officer' : ($employeeDeclined ? 'Declined by employee (ESS)' : 'Rejected by employer (ESS)') }}</span>
      @else
      <span class="lp-pill {{ $pill($vehicle->TRANSACTION_STATUS) }}" title="Transaction status">{{ $v($vehicle->TRANSACTION_STATUS) }}</span>
      <span class="lp-pill {{ $pill($vehicle->LOAN_STATUS) }}" title="Loan status">Loan: {{ $v($vehicle->LOAN_STATUS) }}</span>
      @endif
    </div>
  </div>
  <div class="lp-figs">
    <div class="lp-fig"><span>Requested amount</span><b><small>TZS</small>{{ $money($vehicle->REQUESTED_AMOUNT) }}</b></div>
    <div class="lp-fig"><span>Tenure</span><b>{{ $v($vehicle->TENURE) }} <small>months</small></b></div>
    <div class="lp-fig"><span>Monthly instalment</span><b><small>TZS</small>{{ $money($instalment) }}</b></div>
    <div class="lp-fig"><span>Total to pay</span><b><small>TZS</small>{{ $money($vehicle->TOTAL_AMOUNT_TO_PAY) }}</b></div>
    <div class="lp-fig"><span>Total TAT {{ $finished ? '' : '(so far)' }}</span><b>{{ $dur($totalMins) ?? '—' }}</b></div>
  </div>
</div>

{{-- ===== Deduction stop from ESS (DEDUCTION_STOP_NOTIFICATION) ===== --}}
@php
    $deductionStops = \App\Models\LoanDeduction::tableReady()
        ? \App\Models\LoanDeduction::where('LOAN_APPLICATION_ID', $vehicle->ID)->orderByDesc('ID')->get()
        : collect();
@endphp
@foreach($deductionStops as $stop)
<div class="lp-card" style="border-color:#fca5a5;background:#fff7f7">
  <div class="lp-card-b" style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap">
    <div style="width:42px;height:42px;border-radius:50%;background:#dc2626;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;flex:0 0 42px"><i class="fa fa-ban"></i></div>
    <div style="flex:1;min-width:240px">
      <div style="font-weight:800;color:#b91c1c;font-size:15px">Employer has stopped the salary deduction for this loan</div>
      <div style="font-size:12.5px;color:#4b5563;margin-top:3px">
        Reason: <b>{{ $stop->STOP_REASON ?: 'Not given' }}</b> ·
        Stop date: <b>{{ $stop->STOP_DATE ? \Illuminate\Support\Str::of($stop->STOP_DATE)->before('T') : '—' }}</b> ·
        Balance: <b>TZS {{ number_format((float) $stop->BALANCE_AMOUNT, 2) }}</b> ·
        Deduction: <b>TZS {{ number_format((float) $stop->DEDUCTION_AMOUNT, 2) }}</b>
      </div>
      <div style="font-size:12px;color:#6b7280;margin-top:3px">
        Received from ESS {{ $stop->RECEIVED_DATE ? $stop->RECEIVED_DATE->format('d M Y H:i') : '' }}
        @if($stop->PREVIOUS_LOAN_STATUS) · loan status was {{ $stop->PREVIOUS_LOAN_STATUS }} @endif
      </div>
    </div>
    <div style="text-align:right">
      @if($stop->REVIEWED_DATE)
        <span class="lp-pill soft green">Reviewed</span>
        <div style="font-size:11.5px;color:#6b7280;margin-top:4px">{{ $stop->REVIEWED_BY }} · {{ $stop->REVIEWED_DATE->format('d M Y') }}</div>
        <div style="font-size:11.5px;color:#374151;max-width:260px">{{ $stop->REVIEW_REMARKS }}</div>
      @else
        <span class="lp-pill soft red">Pending review</span>
        <div style="margin-top:6px"><a class="btn btn-sm btn-outline-danger" style="border-radius:8px" href="{{ route('deductions-stopped', ['filter' => 'pending', 'q' => $vehicle->LOAN_ID]) }}">Review</a></div>
      @endif
    </div>
  </div>
</div>
@endforeach

{{-- ===== Takeover of this MCB loan by another bank (MCB = FSP1) ===== --}}
@php
    $tkQuotes = \App\Models\TakeoverBalanceRequest::tableReady()
        ? \App\Models\TakeoverBalanceRequest::where('LOAN_APPLICATION_ID', $vehicle->ID)->orderByDesc('ID')->get() : collect();
    $tkPayments = \App\Models\INCOMMINGPAYMENT::ackReady()
        ? \App\Models\INCOMMINGPAYMENT::where('LOAN_APPLICATION_ID', $vehicle->ID)->orderByDesc('ID')->get() : collect();
@endphp
@if($tkQuotes->isNotEmpty() || $tkPayments->isNotEmpty())
<div class="lp-card" style="border-color:#fcd9b0">
  <div class="lp-card-h" style="background:#fff8f0;border-radius:14px 14px 0 0">
    <h6><i class="fa fa-random" style="color:#f28c28"></i> Takeover by another bank</h6>
    <small>{{ $tkQuotes->count() }} payoff quote(s) · {{ $tkPayments->count() }} payment(s)</small>
  </div>
  <div class="lp-card-b">
    <div class="row">
      <div class="col-lg-6">
        <h6 class="mb-1" style="font-size:12px;color:#6b7280">LATEST PAYOFF QUOTE</h6>
        @php $q = $tkQuotes->first(); @endphp
        @if($q)
        <dl class="m-0">
          <div class="lp-f"><dt>Payoff amount</dt><dd>{{ $money($q->TOTAL_PAYOFF_AMOUNT) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt>Pay to / reference</dt><dd class="mono">{{ $q->SETTLEMENT_ACCOUNT }} · {{ $q->PAYMENT_REFERENCE_NUMBER }}</dd></div>
          <div class="lp-f"><dt>Quoted / valid until</dt><dd>{{ $dt($q->REQUEST_DATE, true) }} → {{ $dt($q->FINAL_PAYMENT_DATE, true) }}</dd></div>
          <div class="lp-f"><dt>Status</dt><dd><span class="lp-pill soft {{ $q->STATUS === 'PAID' ? 'green' : 'grey' }}">{{ $q->STATUS === 'PAID' ? 'Paid' : 'Awaiting payment' }}</span></dd></div>
        </dl>
        @else<div class="text-muted small">No quote recorded.</div>@endif
      </div>
      <div class="col-lg-6">
        <h6 class="mb-1" style="font-size:12px;color:#6b7280">PAYMENTS FROM THE OTHER BANK</h6>
        @forelse($tkPayments as $tp)
          <div class="lp-f"><dt>{{ $tp->PAYMENT_REFERENCE_NUMBER }}<br><small>{{ $tp->PAYMENT_DATE }}</small></dt>
            <dd>{{ $money($tp->TOTAL_PAYOFF_AMOUNT) }} <small>TZS</small><br>
              @if($tp->ACK_STATUS === 'SETTLED')<span class="lp-pill soft green">Settled</span>
              @elseif($tp->ACK_STATUS === 'UNSETTLED')<span class="lp-pill soft red">Not settled</span>
              @else<a href="{{ route('incoming-payment-request', ['filter' => 'pending', 'q' => $vehicle->LOAN_ID]) }}" class="lp-pill soft grey">To confirm</a>@endif
            </dd></div>
        @empty<div class="text-muted small">No payment received yet.</div>@endforelse
      </div>
    </div>
  </div>
</div>
@endif

{{-- ===== MCB takes over this loan from another bank (MCB = FSP2) ===== --}}
@if($vehicle->LOAN_TYPE === 'TAKEOVER')
@php
    $tkOut = \App\Models\PAYMENT::forLoan($vehicle);
    $tkNet = (float) $vehicle->TOTAL_AMOUNT - (float) ($tkOut->TAKE_OVER_AMOUNT ?? $vehicle->SETTLEMENT_AMOUNT ?? 0);
@endphp
<div class="lp-card" style="border-color:#bfdbfe">
  <div class="lp-card-h" style="background:#f0f7ff;border-radius:14px 14px 0 0">
    <h6><i class="fa fa-random" style="color:#0d47a1"></i> Takeover of a loan from another bank (FSP1)</h6>
    <small>
      @if(($tkOut->STATUS ?? null) === 'NOTIFIED' || $vehicle->TRANSACTION_STATUS === 'Disbursed')<span class="lp-pill soft green">Completed · ESS notified</span>
      @elseif($vehicle->TRANSACTION_STATUS === 'Posted to CBS' && $tkOut && $tkOut->CBS_REFERENCE)<span class="lp-pill soft blue">Proof uploaded · ESS not yet notified</span>
      @elseif($vehicle->TRANSACTION_STATUS === 'Posted to CBS')<span class="lp-pill soft orange">Disbursed · upload proof of payment</span>
      @else<span class="lp-pill soft grey">Awaiting disbursement</span>@endif
    </small>
  </div>
  <div class="lp-card-b">
    <div class="row">
      <div class="col-lg-6">
        <dl class="m-0">
          <div class="lp-f"><dt>FSP1 / loan number</dt><dd>{{ $v($tkOut->FSP1_CODE ?? null) }} · <span class="mono">{{ $v($tkOut->FSP1_LOAN_NUMBER ?? $vehicle->ORIGINAL_LOAN_ID) }}</span></dd></div>
          <div class="lp-f"><dt>Pay to</dt><dd class="mono">{{ $v($tkOut->FSP1_BANK_ACCOUNT ?? null) }} · {{ $v($tkOut->FSP1_SWIFT_CODE ?? null) }}</dd></div>
          <div class="lp-f"><dt>Account name</dt><dd>{{ $v($tkOut->FSP1_BANK_ACCOUNT_NAME ?? null) }}</dd></div>
          <div class="lp-f"><dt>FSP1 payment reference</dt><dd class="mono">{{ $v($tkOut->FSP1_PAYMENT_REFERENCE_NUMBER ?? null) }}</dd></div>
          <div class="lp-f"><dt>Pay by</dt><dd>{{ $dt($tkOut->FSP1_FINAL_PAYMENT_DATE ?? null, true) }}</dd></div>
        </dl>
      </div>
      <div class="col-lg-6">
        <dl class="m-0">
          <div class="lp-f"><dt>New loan amount</dt><dd>{{ $money($vehicle->TOTAL_AMOUNT) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt>Takeover amount (to FSP1)</dt><dd>{{ $money($tkOut->TAKE_OVER_AMOUNT ?? $vehicle->SETTLEMENT_AMOUNT) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt><b>Net to employee</b></dt><dd><b>{{ $money($tkNet) }}</b> <small>TZS</small></dd></div>
          <div class="lp-f"><dt>Proof of payment to FSP1</dt><dd>
            @if($tkOut && $tkOut->CBS_REFERENCE)
              <span class="mono">{{ $tkOut->CBS_REFERENCE }}</span> · {{ $money($tkOut->PAID_AMOUNT) }} · {{ $tkOut->PAID_BY }}
              @if($tkOut->PAYMENT_ADVICE_ATTACHMENT)<a href="{{ route('takeover-outgoing-advice-pdf', ['id' => $tkOut->ID]) }}" target="_blank">PDF</a>@endif
            @else — not uploaded yet{{ $vehicle->TRANSACTION_STATUS === 'Posted to CBS' ? '' : ' (after Credit Operation disburses)' }} @endif
            @if($vehicle->TRANSACTION_STATUS === 'Posted to CBS' && ($tkOut->STATUS ?? null) !== 'NOTIFIED')
              <a class="btn btn-sm {{ ($tkOut && $tkOut->CBS_REFERENCE) ? 'btn-outline-primary' : 'btn-primary' }}" style="border-radius:8px;margin-left:6px" href="{{ route('outgoing-payment-request', ['filter' => 'all', 'q' => $vehicle->APPLICATION_NUMBER]) }}"><i class="fa fa-upload"></i> {{ ($tkOut && $tkOut->CBS_REFERENCE) ? 'Update proof of payment' : 'Upload proof of payment' }}</a>
            @endif</dd></div>
          @if($tkOut && $tkOut->STATUS_DESCRIPTION)<div class="lp-f"><dt>Note</dt><dd style="color:#b45309">{{ $tkOut->STATUS_DESCRIPTION }}</dd></div>@endif
        </dl>
      </div>
    </div>
  </div>
</div>
@endif

{{-- ===== Approval journey & TAT ===== --}}
<div class="lp-card">
  <div class="lp-card-h">
    <h6><i class="fa fa-clock-o"></i> Approval journey &amp; turnaround time (TAT)</h6>
    <div class="lp-legend">
      <span><i style="background:#16a34a"></i>≤ 24 hrs</span><span><i style="background:#f59e0b"></i>1 – 3 days</span><span><i style="background:#dc2626"></i>&gt; 3 days</span>
    </div>
  </div>
  <div class="lp-card-b">
    <ul class="lp-tl">
      @foreach($steps as $s)
        <li class="{{ $s['state'] }}">
          <div class="lp-dot">
            @if($s['state'] === 'done') ✓ @elseif($s['state'] === 'rejected') ✕ @elseif($s['state'] === 'current') … @else {{ $loop->iteration }} @endif
          </div>
          <div>
            <div class="t">{{ $s['title'] }}</div>
            <div class="s">
              @if($s['at'])<b>{{ $s['at']->format('d M Y, H:i') }}</b> · @endif
              {{ $s['who'] }}
              @if($s['state'] === 'current') · <b style="color:#c2410c">In progress</b>@endif
              @if($s['state'] === 'skipped') · Not reached @endif
              @if($s['state'] === 'waiting') · Waiting for previous step @endif
            </div>
            @if(!empty($s['note']))<div class="n">{{ $s['note'] }}</div>@endif
          </div>
          <div class="lp-tat">
            @if($s['tat'] !== null)
              <span class="v {{ $tone($s['tat']) }}">{{ $dur($s['tat']) }}</span>
              <span class="k">after {{ $s['from'] }}</span>
            @elseif($s['waiting'] !== null)
              <span class="v {{ $tone($s['waiting']) }}">{{ $dur($s['waiting']) }}</span>
              <span class="k">waiting since {{ $s['from'] }}</span>
            @elseif($loop->first)
              <span class="k">Start</span>
            @endif
          </div>
        </li>
      @endforeach
    </ul>
  </div>
</div>

{{-- ===== Payroll deductions from ESS (FSP_MONTHLY_DEDUCTIONS) ===== --}}
@php
    $payrollRows = \App\Models\PayrollDeduction::tableReady()
        ? \App\Models\PayrollDeduction::where('LOAN_APPLICATION_ID', $vehicle->ID)->orderByDesc('CHECK_DATE')->orderByDesc('ID')->limit(24)->get()
        : collect();
@endphp
@if($payrollRows->isNotEmpty())
<div class="lp-card">
  <div class="lp-card-h">
    <h6><i class="fa fa-calendar-check-o"></i> Payroll deductions (ESS)</h6>
    <small>{{ $payrollRows->count() }} payroll{{ $payrollRows->count() == 1 ? '' : 's' }} · total deducted TZS {{ $money($payrollRows->sum('DEDUCTION_AMOUNT')) }} · latest balance TZS {{ $money($payrollRows->first()->BALANCE_AMOUNT) }}</small>
  </div>
  <div class="lp-card-b" style="padding:0;overflow:auto">
    <table class="table mb-0" style="font-size:12.5px">
      <thead><tr style="background:#f6f8fc"><th>Payroll date</th><th class="text-end">Deducted</th><th class="text-end">Balance left</th><th>Stop pay</th><th>Received</th></tr></thead>
      <tbody>
      @foreach($payrollRows as $p)
        <tr>
          <td><b>{{ $dt($p->CHECK_DATE) }}</b></td>
          <td class="text-end" style="font-variant-numeric:tabular-nums">{{ $money($p->DEDUCTION_AMOUNT) }}</td>
          <td class="text-end" style="font-variant-numeric:tabular-nums">{{ $money($p->BALANCE_AMOUNT) }}</td>
          <td>@if($p->HAS_STOP_PAY)<span class="lp-pill soft red">Stop pay</span> <small style="color:#6b7280">{{ $p->HAS_STOP_PAY_REASON }}</small>@else<span style="color:#6b7280">No</span>@endif</td>
          <td style="color:#6b7280">{{ $dt($p->REQUEST_DATE, true) }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

{{-- ===== Details ===== --}}
<div class="row">
  <div class="col-xl-6">
    <div class="lp-card">
      <div class="lp-card-h"><h6><i class="fa fa-id-card-o"></i> Identification</h6></div>
      <div class="lp-card-b">
        <dl class="m-0">
          <div class="lp-f"><dt>ID</dt><dd>{{ $vehicle->ID }}</dd></div>
          <div class="lp-f"><dt>Loan ID</dt><dd class="mono">{{ $v($vehicle->LOAN_ID) }}</dd></div>
          <div class="lp-f"><dt>Application No.</dt><dd class="mono">{{ $v($vehicle->APPLICATION_NUMBER) }}</dd></div>
          <div class="lp-f"><dt>Reference No.</dt><dd class="mono">{{ $v($vehicle->REFERENCE_NUMBER) }}</dd></div>
          <div class="lp-f"><dt>Customer ID</dt><dd>{{ $v($vehicle->CUSTOMER_ID) }}</dd></div>
          <div class="lp-f"><dt>Check No.</dt><dd>{{ $v($vehicle->CHECK_NUMBER) }}</dd></div>
          <div class="lp-f"><dt>Vote Code</dt><dd>{{ $v($vehicle->VOTE_CODE) }}</dd></div>
          <div class="lp-f"><dt>Vote Name</dt><dd>{{ $v($vehicle->VOTE_NAME) }}</dd></div>
        </dl>
      </div>
    </div>
  </div>
  <div class="col-xl-6">
    <div class="lp-card">
      <div class="lp-card-h"><h6><i class="fa fa-user-o"></i> Applicant information</h6></div>
      <div class="lp-card-b">
        <dl class="m-0">
          <div class="lp-f"><dt>First Name</dt><dd>{{ $v($vehicle->FIRST_NAME) }}</dd></div>
          <div class="lp-f"><dt>Phone</dt><dd>{{ $v($vehicle->MSISDN) }}</dd></div>
          <div class="lp-f"><dt>Middle Name</dt><dd>{{ $v($vehicle->MIDDLE_NAME) }}</dd></div>
          <div class="lp-f"><dt>National ID</dt><dd class="mono">{{ $v($vehicle->NATIONAL_ID) }}</dd></div>
          <div class="lp-f"><dt>Last Name</dt><dd>{{ $v($vehicle->LAST_NAME) }}</dd></div>
          <div class="lp-f"><dt>Gender</dt><dd>{{ $v($vehicle->SEX) }}</dd></div>
          <div class="lp-f"><dt>Email</dt><dd>{{ $v($vehicle->EMAIL_ADDRESS) }}</dd></div>
          <div class="lp-f"><dt>Marital Status</dt><dd>{{ $v($vehicle->MARITAL_STATUS) }}</dd></div>
          <div class="lp-f"><dt>Physical Address</dt><dd>{{ $v($vehicle->PHYSICAL_ADDRESS) }}</dd></div>
        </dl>
      </div>
    </div>
  </div>
</div>

<div class="lp-card">
  <div class="lp-card-h"><h6><i class="fa fa-briefcase"></i> Employment details</h6></div>
  <div class="lp-card-b">
    <dl class="lp-grid three m-0">
      <div class="lp-f"><dt>Employment Date</dt><dd>{{ $dt($vehicle->EMPLOYMENT_DATE) }}</dd></div>
      <div class="lp-f"><dt>Confirmation Date</dt><dd>{{ $dt($vehicle->CONFIRMATION_DATE) }}</dd></div>
      <div class="lp-f"><dt>Months to Retire</dt><dd>{{ $v($vehicle->RETIREMENT_DATE) }} <small>months</small></dd></div>
      <div class="lp-f"><dt>Terms of Employment</dt><dd>{{ $v($vehicle->TERMS_OF_EMPLOYMENT) }}</dd></div>
      <div class="lp-f"><dt>Designation Code</dt><dd>{{ $v($vehicle->DESIGNATION_CODE) }}</dd></div>
      <div class="lp-f"><dt>Designation Name</dt><dd>{{ $v($vehicle->DESIGNATION_NAME) }}</dd></div>
      <div class="lp-f"><dt>Basic Salary</dt><dd>{{ $money($vehicle->BASIC_SALARY) }} <small>TZS</small></dd></div>
      <div class="lp-f"><dt>Net Salary</dt><dd>{{ $money($vehicle->NET_SALARY) }} <small>TZS</small></dd></div>
      <div class="lp-f"><dt>One Third Salary</dt><dd>{{ $money($vehicle->ONE_THIRD_AMOUNT) }} <small>TZS</small></dd></div>
      <div class="lp-f"><dt>Total Employment Deduction</dt><dd>{{ $money($vehicle->TOTAL_EMPLOYMENT_DEDUCTION) }} <small>TZS</small></dd></div>
      <div class="lp-f"><dt>Contract Start Date</dt><dd>{{ $dt($vehicle->CONTRACT_START_DATE) }}</dd></div>
      <div class="lp-f"><dt>Contract End Date</dt><dd>{{ $dt($vehicle->CONTRACT_END_DATE) }}</dd></div>
    </dl>
  </div>
</div>

<div class="lp-card">
  <div class="lp-card-h"><h6><i class="fa fa-money"></i> Loan details</h6></div>
  <div class="lp-card-b">
    <dl class="lp-grid three m-0">
      <div class="lp-f"><dt>Loan Type</dt><dd>{{ $v($vehicle->LOAN_TYPE) }}</dd></div>
      <div class="lp-f"><dt>Loan Status</dt><dd><span class="lp-pill soft {{ $pill($vehicle->LOAN_STATUS) }}">{{ $v($vehicle->LOAN_STATUS) }}</span></dd></div>
      <div class="lp-f"><dt>Loan Purpose</dt><dd>{{ $v($vehicle->LOAN_PURPOSE) }}</dd></div>
      <div class="lp-f"><dt>Requested Amount</dt><dd>{{ $money($vehicle->REQUESTED_AMOUNT) }} <small>TZS</small></dd></div>
      <div class="lp-f"><dt>Total Amount</dt><dd>{{ $money($vehicle->TOTAL_AMOUNT) }} <small>TZS</small></dd></div>
      <div class="lp-f"><dt>Total Amount To Pay</dt><dd>{{ $money($vehicle->TOTAL_AMOUNT_TO_PAY) }} <small>TZS</small></dd></div>
      <div class="lp-f"><dt>Outstanding Balance</dt><dd>{{ $money($vehicle->OUT_STANDING_BALANCE) }} <small>TZS</small></dd></div>
      <div class="lp-f"><dt>Desired Deduction</dt><dd>{{ $money($vehicle->DESIRED_DEDUCTION_AMOUNT) }} <small>TZS</small></dd></div>
      <div class="lp-f"><dt>Loan Tenure</dt><dd>{{ $v($vehicle->TENURE) }} <small>months</small></dd></div>
      <div class="lp-f"><dt>Loan Reference</dt><dd class="mono">{{ $v($vehicle->LOAN_REFERENCE) }}</dd></div>
      <div class="lp-f"><dt>Application Date</dt><dd>{{ $dt($vehicle->APPLICATION_DATE, true) }}</dd></div>
      <div class="lp-f"><dt>Instalment Start Date</dt><dd>{{ $dt($vehicle->INSTALMENT_START_DATE) }}</dd></div>
    </dl>
  </div>
</div>

@if($vehicle->LOAN_TYPE === 'RESTRUCTURING')
@php
   $oldInstalment = \App\Http\Livewire\Components\Vehicle\Details::instalment($original_loan);
   $newInstalment = \App\Http\Livewire\Components\Vehicle\Details::instalment($vehicle);
   $principal = !empty($vehicle->SETTLEMENT_AMOUNT) ? (float) $vehicle->SETTLEMENT_AMOUNT : (float) $vehicle->REQUESTED_AMOUNT;
@endphp
<div class="lp-card" style="border-color:#fcd9b0">
  <div class="lp-card-h" style="background:#fff8f0;border-radius:14px 14px 0 0"><h6><i class="fa fa-refresh" style="color:#f28c28"></i> Restructure: old vs new terms</h6></div>
  <div class="lp-card-b">
    <div class="row">
      <div class="col-lg-6">
        <h6 class="mb-1" style="font-size:13px;color:#6b7280">EXISTING LOAN</h6>
        <dl class="m-0">
          @if($original_loan)
          <div class="lp-f"><dt>Original Loan</dt><dd><a href="{{ Route('staff-profile', ['id' => $original_loan->ID]) }}">{{ $original_loan->LOAN_ID }}</a></dd></div>
          <div class="lp-f"><dt>Original Amount</dt><dd>{{ $money($original_loan->TOTAL_AMOUNT) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt>Original Tenure</dt><dd>{{ $original_loan->TENURE }} <small>months</small></dd></div>
          <div class="lp-f"><dt>Current Instalment</dt><dd>{{ $money($oldInstalment) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt>Current Maturity</dt><dd>{{ $original_loan->MATURITY_DATE ? $original_loan->MATURITY_DATE->format('d M Y') : '—' }}</dd></div>
          <div class="lp-f"><dt>Original Loan Status</dt><dd>{{ $original_loan->TRANSACTION_STATUS }}</dd></div>
          @else
          <div class="lp-f"><dt>Original Loan</dt><dd><span class="lp-pill soft red">{{ $vehicle->ORIGINAL_LOAN_ID ?? 'Not set' }} (not found)</span></dd></div>
          @endif
        </dl>
      </div>
      <div class="col-lg-6">
        <h6 class="mb-1" style="font-size:13px;color:#6b7280">AFTER RESTRUCTURE</h6>
        <dl class="m-0">
          <div class="lp-f"><dt>Principal Restructured</dt><dd>{{ $money($principal) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt>New Tenure</dt><dd>{{ $vehicle->TENURE }} <small>months</small></dd></div>
          <div class="lp-f"><dt>New Instalment</dt><dd>{{ $money($newInstalment) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt>Processing Fee</dt><dd>{{ $money($vehicle->PROCESSING_FEE) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt>Total Amount To Pay (incl. fee)</dt><dd>{{ $money($vehicle->TOTAL_AMOUNT_TO_PAY) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt>Loan Account (Profits)</dt><dd class="mono">{{ $vehicle->DISBURSEMENT_RECEIPT }}{{ $vehicle->CHECK_DIGIT !== null ? ' / ' . $vehicle->CHECK_DIGIT : '' }}</dd></div>
        </dl>
      </div>
    </div>
    <div class="alert alert-light-warning mt-3 mb-0" role="alert">
      A restructure does not pay out money. Credit Operation must change the loan in Profits (new tenure / instalment)
      before completing it here; completing marks the original loan as <b>Restructured</b> and notifies ESS.
    </div>
  </div>
</div>
@endif

@if($restructures->count() > 0)
<div class="lp-card">
  <div class="lp-card-h"><h6><i class="fa fa-history" style="color:#f28c28"></i> Restructure history</h6></div>
  <div class="lp-card-b">
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead><tr><th>Application</th><th>Date</th><th>Principal</th><th>Tenure</th><th>Instalment</th><th>Status</th></tr></thead>
        <tbody>
        @foreach($restructures as $restructure)
        <tr>
          <td><a href="{{ Route('staff-profile', ['id' => $restructure->ID]) }}">{{ $restructure->APPLICATION_NUMBER }}</a></td>
          <td>{{ $restructure->APPLICATION_DATE ? $restructure->APPLICATION_DATE->format('d M Y') : '' }}</td>
          <td>{{ number_format(!empty($restructure->SETTLEMENT_AMOUNT) ? (float) $restructure->SETTLEMENT_AMOUNT : (float) $restructure->REQUESTED_AMOUNT, 2,'.',',') }}</td>
          <td>{{ $restructure->TENURE }}</td>
          <td>{{ number_format(\App\Http\Livewire\Components\Vehicle\Details::instalment($restructure), 2,'.',',') }}</td>
          <td>{{ $restructure->TRANSACTION_STATUS }} / {{ $restructure->LOAN_STATUS }}</td>
        </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

<div class="row">
  <div class="col-xl-6">
    <div class="lp-card">
      <div class="lp-card-h"><h6><i class="fa fa-percent"></i> Charges and fees</h6></div>
      <div class="lp-card-b">
        <dl class="m-0">
          <div class="lp-f"><dt>Processing Fee</dt><dd>{{ $money($vehicle->PROCESSING_FEE) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt>Insurance</dt><dd>{{ $money($vehicle->INSURANCE) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt>Other Charges</dt><dd>{{ $money($vehicle->OTHER_CHARGES) }} <small>TZS</small></dd></div>
          <div class="lp-f"><dt>Product Interest Rate</dt><dd>{{ $money($vehicle->PRODUCT_INTEREST_RATE) }}%</dd></div>
          <div class="lp-f"><dt>Product Processing Fee</dt><dd>{{ $money($vehicle->PRODUCT_PROCESSING_FEE) }}%</dd></div>
        </dl>
      </div>
    </div>
  </div>
  <div class="col-xl-6">
    <div class="lp-card">
      <div class="lp-card-h"><h6><i class="fa fa-university"></i> Bank and branch</h6></div>
      <div class="lp-card-b">
        <dl class="m-0">
          <div class="lp-f"><dt>Bank Account Number</dt><dd class="mono">{{ $v($vehicle->BANK_ACCOUNT_NUMBER) }}</dd></div>
          <div class="lp-f"><dt>Branch Name</dt><dd>{{ $v($vehicle->BRANCH_NAME) }}</dd></div>
          <div class="lp-f"><dt>Nearest Branch</dt><dd>{{ $v($vehicle->NEAREST_BRANCH_NAME) }}</dd></div>
          <div class="lp-f"><dt>Initiating Branch</dt><dd>{{ $v($vehicle->INIT_BRANCH) }}</dd></div>
          <div class="lp-f"><dt>Beneficiary Branch</dt><dd>{{ $v($vehicle->BEN_BRANCH) }}</dd></div>
        </dl>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-6">
    <div class="lp-card">
      <div class="lp-card-h"><h6><i class="fa fa-check-square-o"></i> Approval and confirmation</h6></div>
      <div class="lp-card-b">
        <dl class="m-0">
          <div class="lp-f"><dt>Transaction Status</dt><dd><span class="lp-pill soft {{ $pill($vehicle->TRANSACTION_STATUS) }}">{{ $v($vehicle->TRANSACTION_STATUS) }}</span></dd></div>
          <div class="lp-f"><dt>{{ $employeeDeclined ? 'Employee (ESS) reason' : 'Employer (ESS) reason' }}</dt><dd>{{ $v($essReason) }}</dd></div>
          <div class="lp-f"><dt>Credit Officer Decision</dt><dd>{{ $v($vehicle->CO_DECISION_BY) }}{{ $vehicle->CO_DECISION_DATE ? ' · '.$dt($vehicle->CO_DECISION_DATE, true) : '' }}</dd></div>
          <div class="lp-f"><dt>Approved By</dt><dd>{{ $v($vehicle->APPROVED_BY) }}</dd></div>
          <div class="lp-f"><dt>Approved Date</dt><dd>{{ $dt($vehicle->APPROVED_DATE, true) }}</dd></div>
          <div class="lp-f"><dt>Reason</dt><dd>{{ $v($vehicle->REASON) }}</dd></div>
          <div class="lp-f"><dt>Loan Request Initiator</dt><dd>{{ $v($vehicle->LOAN_REQUEST_INITIATOR) }}</dd></div>
          <div class="lp-f"><dt>Loan Request Checker</dt><dd>{{ $v($vehicle->LOAN_REQUEST_CHECKER) }}</dd></div>
        </dl>
      </div>
    </div>
  </div>
  <div class="col-xl-6">
    <div class="lp-card">
      <div class="lp-card-h"><h6><i class="fa fa-server"></i> Core banking (CBS) response</h6></div>
      <div class="lp-card-b">
        <dl class="m-0">
          <div class="lp-f"><dt>CBS Response Code</dt><dd class="mono">{{ $v($vehicle->CBS_RESPONSE_CODE) }}</dd></div>
          <div class="lp-f"><dt>CBS Response Description</dt><dd>{{ $v($vehicle->CBS_RESPONSE_DESCRIPTION) }}</dd></div>
          <div class="lp-f"><dt>Disbursement Receipt / Loan Account</dt><dd class="mono">{{ $v($vehicle->DISBURSEMENT_RECEIPT) }}</dd></div>
          <div class="lp-f"><dt>Loan Disbursed Date</dt><dd>{{ $dt($vehicle->LOAN_DISBURSED_DATE, true) }}</dd></div>
        </dl>
      </div>
    </div>
  </div>
</div>

{{-- ===== Actions (logic unchanged) ===== --}}
<div class="lp-card lp-actions">
  <div class="lp-card-h"><h6><i class="fa fa-bolt"></i> Actions</h6></div>
  <div class="lp-card-b">
                    <div class="row">

                @if($vehicle->TRANSACTION_STATUS === 'Pending' && !$cancelled)
                @if(Auth::user()->role == 'Credit Officer')
                <div class="row">  
                        <div class="col-lg-6">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal"> Credit Officer Approve Request <i class="icofont icofont-tick-boxed"></i></button>
                        </div>
                        <div class="col-lg-6">
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#newModalReject"> Credit Officer Reject Request <i class="icofont icofont-trash"></i></button>
                        </div>
                </div>
                @else
                <div class="alert alert-info outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                    Awaiting CO review and verification
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                </div>
                @endif
                    @endif
         
                        @if($cancelled)
                            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                <i class="icofont icofont-minus-circle"></i>
                                    Cancelled by the employee on ESS{{ $cancelledAt ? ' on '.$cancelledAt->format('d M Y, H:i') : '' }}. Reason: <b>{{ $cancelReason ?: 'not given' }}</b>. No further action is needed.
                                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                                </div>
                        @endif   

            @if($vehicle->TRANSACTION_STATUS === 'Approved' )
           
                @if(Auth::user()->role == 'Credit Admin' && !isset($approval_datils))

                @if(!empty($vehicle->DISBURSEMENT_RECEIPT))
                         <!-- The LINAC_ACCOUNT_NUMBER is not null or empty -->
                         <div class="col-lg-12">
                            <br/> <br/> <br/>
                         <li class="list-group-item d-flex justify-content-between align-items-center">Loan Account<span class="badge badge-primary rounded-pill">{{ $vehicle->DISBURSEMENT_RECEIPT ?? 'Not set' }}</span></li>
                         <br/> <br/> <br/>
                         </div>
                    @else
                    <div class="row">  
                    <div class="col-lg-2">
                        </div>
                            <div class="col-lg-8">
                                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newModalUpdateLoanDetails">Update Loan Account Details </button>
                            </div>
                            <div class="col-lg-2">
                        </div>
                    </div>
                    <br/><br/>
                    <br/>
                    @endif
                    @if(!empty($vehicle->DISBURSEMENT_RECEIPT))

                    <div class="row">  
                            <div class="col-lg-6">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal">{{ $vehicle->LOAN_TYPE === 'RESTRUCTURING' ? 'Credit Admin Restructure Completion Initiation' : 'Credit Admin  Disbursement Initiation' }} <i class="icofont icofont-tick-boxed"></i></button>
                            </div>
                            <div class="col-lg-6">
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#newModalReject"> {{ $vehicle->LOAN_TYPE === 'RESTRUCTURING' ? 'Credit Admin Reject Restructure' : 'Credit Admin Reject Disbursement Initiation' }} <i class="icofont icofont-trash"></i></button>
                            </div>
                    </div>
                    @endif
                    @elseif($approval_datils && $approval_datils->status == 'INITIATED')
                    <div class="alert alert-info outline alert-dismissible fade show" role="alert">
                        <i class="icofont icofont-check-circled"></i>
                            Awaiting Credit Operation Action For Approval
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    @elseif($approval_datils && $approval_datils->status == 'INITIATED_REJECTION')
                    <div class="alert alert-info outline alert-dismissible fade show" role="alert">
                        <i class="icofont icofont-check-circled"></i>
                            Awaiting Credit Operation Action For Rejection
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    @elseif($approval_datils && $approval_datils->status == 'DISBURSED')
                    @else
                @endif 

            @if(isset($approval_datils))
                @if(Auth::user()->role == 'Credit Operation' && $approval_datils->status=='INITIATED')

                <div class="row"> 
                            <div class="col-lg-6">
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newModalUpdateLoanDetailsLoanAcc">Update Loan Account Details </button>
                            </div>
                </div>
<br/><br/>
                    <div class="row">  
                            <div class="col-lg-6">
                                <button class="btn btn-primary  w-100" data-bs-toggle="modal" data-bs-target="#newModal"> {{ $vehicle->LOAN_TYPE === 'RESTRUCTURING' ? 'Complete Restructure (after updating Profits)' : 'Credit Operation Approve Request' }} <i class="icofont icofont-tick-boxed"></i></button>
                            </div>
                            <div class="col-lg-6">
                                <button class="btn btn-danger  w-100" data-bs-toggle="modal" data-bs-target="#newModalReject"> Credit Operation Reject Request <i class="icofont icofont-trash"></i></button>
                            </div>
                    </div>
                  
                    @elseif(Auth::user()->role == 'Credit Operation' && $approval_datils->status=='INITIATED_REJECTION')
                    <div class="row">  
                            
                            <div class="col-lg-12">
                                <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#newModalReject"> Credit Operation Reject Request <i class="icofont icofont-trash"></i></button>
                            </div>
                           
                    </div>
                    @elseif($approval_datils->status=='DISBURSED' || $vehicle->TRANSACTION_STATUS =='Disbursed')
                    <div class="card-body">
                    <div class="row">
                            <div class="col-lg-12">
                                <ul class="list-group"> 
                                    <!-- <li class="list-group-item d-flex justify-content-between align-items-center">Initated At<span class="badge badge-primary rounded-pill">{{ $approval_datils->initiator_date->format('d M Y H:i:s') }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center"> Disbursed At<span class="badge badge-primary rounded-pill">{{ $approval_datils->checker_date->format('d M Y H:i:s')  }}</span></li>  -->
                                    <li class="list-group-item d-flex justify-content-between align-items-center">Request Completed<span class="badge badge-primary rounded-pill">{{ $approval_datils->status  }}</span></li> 
                            </ul>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info outline alert-dismissible fade show" role="alert">
                        <i class="icofont icofont-check-circled"></i>
                            Awaiting Credit Operation Action
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                @endif
                <!-- CO  INITIATE LIQUIDATA
TO CA  COMOT
WITH POPUP -->
            @endif
            @endif
            @if($vehicle->TRANSACTION_STATUS === 'Disbursed' )
            
        @if(Auth::user()->role == 'Credit Officer')
        <div class="col-lg-12">    
            <!-- <button  class="btn btn-outline-primary w-100"  type="button"> Initiate Liqudation <i class="icofont icofont-ui-rate-add"></i></button> -->
            @if($vehicle->LIQUIDATION_STATUS === 'Initiated' )
            <li class="list-group-item d-flex justify-content-between align-items-center">Liquidataion Status<span class="badge badge-primary rounded-pill">{{ $vehicle->LIQUIDATION_STATUS  }}</span></li> 
            @else
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newModalLiqudation"> Initiate Liqudation </button>
            @endif
            <br/><br/>
            <a class="btn btn-outline-warning w-100" href="{{ route('loans-restructuring-calculator', ['id' => $vehicle->ID]) }}"><i class="fa fa-calculator"></i> Propose Restructure to Employee </a>
                         
        </div>
        @elseif(Auth::user()->role == 'Credit Admin' )
        @if($vehicle->LIQUIDATION_STATUS === 'Initiated' )
        <div class="col-lg-12">    
            <!-- <button  class="btn btn-outline-primary w-100"  type="button" > Complete Liqudation <i class="icofont icofont-ui-rate-add"></i></button> -->
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newModalLiqudationComplete"> Complete Liqudation </button>
        </div>
        @elseif($vehicle->LIQUIDATION_STATUS === 'Complete')
        <li class="list-group-item d-flex justify-content-between align-items-center">Liquidataion Initiator<span class="badge badge-primary rounded-pill">{{ $vehicle->LIQUIDATION_MAKER  }}</span></li> 
        <li class="list-group-item d-flex justify-content-between align-items-center">Liquidataion Initiator Remarks<span class="badge badge-primary rounded-pill">{{ $vehicle->LIQUIDATION_MAKER_REMARKS  }}</span></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">Liquidataion Checker<span class="badge badge-primary rounded-pill">{{ $vehicle->LIQUIDATION_CHECKER  }}</span></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">Liquidataion Checker Remarks<span class="badge badge-primary rounded-pill">{{ $vehicle->LIQUIDATION_CHECKER_REMARKS  }}</span></li>

        <button wire:click="liqudateLoanAccount({{$vehicle->ID}})" class="btn btn-outline-primary w-100"  type="button" wire:loading.remove> Notify Utumish <i class="icofont icofont-ui-rate-add"></i></button>
        @else
        <li class="list-group-item d-flex justify-content-between align-items-center">Request Completed / You can not Liquidate This Loan Contact Credit Officer To initiate<span class="badge badge-primary rounded-pill">{{ $approval_datils->status ?? 'Not set' }}</span></li> 
        @endif
        @else
                 
        @endif

            <br/>
             <div wire:loading.delay>
             <div class="loader-box">
                 <div class="loader-7" style="width: 50px; height:50px;"></div>
                 <br/>
                     <h5 class="f-w-100">Processing ...</h5>
                 </div>
             </div>
             <br/>
             <br/>
             @endif
            <!-- ------ -->
                    </div>
  </div>
</div>
     <!-- end ribon -->
    

   <!-- PROPOSE RESTRUCTURE MODAL START -->
   <div class="modal fade" id="newModalProposeRestructure" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Propose Restructure</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form action="{{ Route('loans-restructuring-propose') }}" method="post">
                    @csrf
                    <input type="hidden" value="{{ $vehicle->ID }}" name="id">
                    <p class="mb-2">Loan <b>{{ $vehicle->LOAN_ID }}</b>. The new terms are calculated from the loan's principal balance
                        and sent to ESS; the employee accepts or rejects in the ESS portal.
                        Enter <b>either</b> the new tenure <b>or</b> the new monthly instalment.</p>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">New Tenure (Months)</label>
                                <input class="form-control" type="number" min="1" name="tenure" value="{{ old('tenure') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">New Monthly Instalment (TZS)</label>
                                <input class="form-control" type="number" min="1" step="0.01" name="installment" value="{{ old('installment') }}">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-form-label">Reason</label>
                                <textarea class="form-control" required maxlength="255" name="reason">{{ old('reason') }}</textarea>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-warning" type="submit" > Send Proposal to ESS</button>
                </form>
            </div>
        </div>
    </div>
    </div>
   <!-- PROPOSE RESTRUCTURE MODAL END -->

       <!-- SEARCH MODAL START -->
   <div class="modal fade" id="newModalLiqudation" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Descriptions</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff-liqiudation') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="Initiated"  required  name="status">
                            </div>
                        <div class="col-lg-12">
                        <div class="form-group">
                                    <label class="col-form-label" >Details</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="reason"></textarea>
                                </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" > Submit Liqudation Initiation</button>
                </form>
            </div>
        </div>
    </div>
    </div>

          <!-- SEARCH MODAL START -->
   <div class="modal fade" id="newModalLiqudationComplete" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Descriptions</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff-liqiudation') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="Complete"  required  name="status">
                            </div>
                        <div class="col-lg-12">
                        <div class="form-group">
                                    <label class="col-form-label" >Details</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="reason"></textarea>
                                </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" > Submit </button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->
   <!-- SEARCH MODAL START -->
   <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Approve</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="Approved"  required  name="status">
                            </div>
                        @if($vehicle->LOAN_TYPE === 'TAKEOVER' && Auth::user()->role == 'Credit Operation' && isset($approval_datils) && $approval_datils->status == 'INITIATED')
                        <div class="col-lg-12">
                            <div class="alert alert-light-primary" style="font-size:12.5px">
                              <b>Takeover:</b> approving disburses the loan as normal. Then pay FSP1 in Profits/TISS and
                              <b>upload the proof of payment</b> (payment reference + SWIFT copy) under
                              <a href="{{ route('outgoing-payment-request', ['filter' => 'all', 'q' => $vehicle->APPLICATION_NUMBER]) }}">Take Over Loans → Outgoing Payments</a>;
                              ESS is notified then (TAKEOVER_DISBURSEMENT_NOTIFICATION).
                            </div>
                        </div>
                        @endif
                        <div class="col-lg-12">
                        <div class="form-group">
                                    <label class="col-form-label" >Approve Reason</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="reason"></textarea>
                                </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm To Submit</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

   <!-- SEARCH MODAL START -->
   <div class="modal fade" id="newModalReject" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-danger text-white">
                <h5 class="modal-title">REJECT</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="REJECTED"  required  name="status">
                            </div>
                        <div class="col-lg-12">
                        <div class="form-group">
                                    <label class="col-form-label" >Reject Reason</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="reason"></textarea>
                                </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>

                <!-- <button wire:click="loanInitialApproval" class="btn btn-outline-primary  pull-right"  type="button" wire:loading.remove> Confirm To Submit <i class="icofont icofont-ui-rate-add"></i></button> -->
                       
                <button class="btn btn-primary" type="submit" >Submit Rejection</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

 <!-- SEARCH MODAL START -->
 <div class="modal fade" id="newModalReject" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-danger text-white">
                <h5 class="modal-title">Reject</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form action="{{ Route('update-staff') }}" method="post">
               
                    @csrf

                    <div class="row">
                        
                        <div class="col-lg-12">
                        <div class="form-group">
                                    <label class="col-form-label" >Reject Reason</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="conditions"></textarea>
                                </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-danger" type="submit" >Confirm To Reject</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

     <!-- SEARCH MODAL START -->
     <div class="modal fade" id="newModalUpdateLoanDetails" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Update Loan Account Details</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff-loanAccount') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                        <label class="col-form-label">Loan Account</label>
                        <input class="form-control" type="text" name="loanAccount" required pattern="\d{10}" maxlength="10" title="Please enter exactly 10 digits">
                    </div>
                    <div class="form-group">
    <label class="col-form-label">Check Digit</label>
    <input class="form-control" type="number" name="checkDigit" required min="0" max="99" title="Please enter a number between 0 and 99">
</div>



                           
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="Approved"  required  name="status">
                            </div>
                        
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm To Submit</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

  <!-- SEARCH MODAL START -->
  <div class="modal fade" id="newModalUpdateLoanDetailsLoanAcc" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Update Loan Account </h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff-loanAccountonly') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                        <label class="col-form-label">Loan Account</label>
                        <input class="form-control" type="text" name="loanAccount" required pattern="\d{10}" maxlength="10" title="Please enter exactly 10 digits">
                    </div>
                   
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="Approved"  required  name="status">
                            </div>
                        
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm To Submit</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->
    						
</div>
