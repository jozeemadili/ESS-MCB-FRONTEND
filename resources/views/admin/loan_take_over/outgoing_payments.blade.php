@extends('layouts.admin.master')
@section('title', 'Takeover Outgoing Payments')
@php
    use Carbon\Carbon;
    $money = fn ($x) => ($x === null || $x === '') ? '—' : number_format((float) $x, 2, '.', ',');
    $now = now('Africa/Dar_es_Salaam');
    $tone = ['awaiting' => '#f28c28', 'paid' => '#0d47a1', 'notified' => '#16a34a', 'closed' => '#dc2626', 'all' => '#64748b'];
    // Dates may come back as Carbon or, in older rows, as plain text
    $local = function ($d) { if (empty($d)) return null; try { return Carbon::parse($d instanceof \DateTimeInterface ? $d->format('Y-m-d H:i:s') : (string) $d, 'Africa/Dar_es_Salaam'); } catch (\Throwable $e) { return null; } };
@endphp
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Takeover · Outgoing Payments · {{ $filters[$filter] }}</h3>
    @endslot
    <li class="breadcrumb-item">Take Over Loans</li>
    <li class="breadcrumb-item active">Outgoing Payments</li>
  @endcomponent
  @include('admin.loan_take_over._style')

<div class="container-fluid tk">
  @foreach ($errors->all() as $error)
    <div class="alert alert-danger outline alert-dismissible fade show" role="alert">{{ $error }}<button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button></div>
  @endforeach
  @if($message = Session::get('success'))
    <div class="alert alert-success outline alert-dismissible fade show" role="alert">{!! $message !!}<button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button></div>
  @endif
  <p class="text-muted" style="font-size:12.5px;margin-top:-6px">
    <b>MCB takes over</b> these loans from another bank (FSP1) (<i>LOAN_TAKEOVER_OFFER_REQUEST</i>). They follow the normal approval flow; at the
    <b>Credit Operation</b> step the transfer to FSP1 is recorded (reference, MT advice, PDF) and ESS is notified with <i>TAKEOVER_DISBURSEMENT_NOTIFICATION</i>.
  </p>
  <div class="tk-kpis">
    @foreach($filters as $key => $label)
      <a class="tk-kpi {{ $filter === $key ? 'on' : '' }}" style="--a:{{ $tone[$key] }}" href="{{ route('outgoing-payment-request', array_filter(['filter' => $key, 'q' => $search])) }}"><span>{{ $label }}</span><b>{{ number_format($counts[$key]) }}</b></a>
    @endforeach
  </div>

  <div class="tk-card">
    <div class="tk-bar">
      <div><b>{{ $filters[$filter] }}</b><span class="tk-count">{{ number_format($payments->total()) }} record{{ $payments->total() == 1 ? '' : 's' }}</span></div>
      <form class="tk-search" method="get" action="{{ route('outgoing-payment-request') }}">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <select name="per_page" onchange="this.form.submit()">@foreach([15, 25, 50, 100] as $n)<option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }} / page</option>@endforeach</select>
        <input type="text" name="q" value="{{ $search }}" placeholder="Loan, application, FSP1 loan or FSP1 code">
        <button class="btn btn-primary tk-btn" type="submit"><i class="fa fa-search"></i></button>
      </form>
    </div>
    @if($payments->isEmpty())
      <div class="tk-empty"><i class="fa fa-inbox fa-2x d-block mb-2" style="color:#c7d2e3"></i>No takeovers{{ $search !== '' ? ' matching "'.$search.'"' : '' }}.</div>
    @else
      <div class="tk-scroll">
        <table class="tk-table">
          <thead><tr><th>Received</th><th>Employee</th><th>FSP1 (pay to)</th><th class="r">Takeover amount</th><th class="r">To employee</th><th>Pay by</th><th>Transfer to FSP1</th><th>Status</th></tr></thead>
          <tbody>
          @foreach($payments as $p)
            @php
              $loan = $p->loan;
              $net = $loan ? (float) $loan->TOTAL_AMOUNT - (float) $p->TAKE_OVER_AMOUNT : null;
              $deadline = $local($p->FSP1_FINAL_PAYMENT_DATE);
              $received = $local($p->TAKE_OVER_DATE ?: $p->CREATED_AT);
            @endphp
            <tr>
              <td style="white-space:nowrap">{{ $received ? $received->format('d M Y H:i') : '—' }}<span class="tk-sub">{{ $received ? $received->diffForHumans() : '' }}</span></td>
              <td style="min-width:180px">
                @if($loan)
                  <a href="{{ route('staff-profile', ['id' => $loan->ID]) }}"><b>{{ strtoupper(trim($loan->FIRST_NAME.' '.$loan->LAST_NAME)) }}</b></a>
                  <span class="tk-sub">{{ $p->APPLICATION_NUMBER }} · {{ $loan->TRANSACTION_STATUS }} / {{ $loan->LOAN_STATUS }}</span>
                @else
                  <span class="tk-mono">{{ $p->LOAN_ID }}</span><span class="tk-sub">{{ $p->APPLICATION_NUMBER }}</span>
                @endif
              </td>
              <td style="min-width:200px"><b>{{ $p->FSP1_CODE }}</b> · loan <span class="tk-mono">{{ $p->FSP1_LOAN_NUMBER }}</span>
                <span class="tk-sub"><span class="tk-mono">{{ $p->FSP1_BANK_ACCOUNT }}</span> {{ $p->FSP1_BANK_ACCOUNT_NAME }} · {{ $p->FSP1_SWIFT_CODE }}</span>
                <span class="tk-sub">Ref <span class="tk-mono">{{ $p->FSP1_PAYMENT_REFERENCE_NUMBER }}</span></span></td>
              <td class="r">{{ $money($p->TAKE_OVER_AMOUNT) }}</td>
              <td class="r">{{ $net !== null ? $money($net) : '—' }}<span class="tk-sub" style="font-weight:500">of {{ $loan ? $money($loan->TOTAL_AMOUNT) : '—' }}</span></td>
              <td style="white-space:nowrap">{{ $deadline ? $deadline->format('d M Y H:i') : '—' }}
                @if($deadline && $p->STATUS === 'RECEIVED')
                  @if($deadline->lt($now))<span class="tk-pill bad" style="margin-top:4px">Expired</span>@else<span class="tk-sub">{{ $deadline->diffForHumans() }}</span>@endif
                @endif
              </td>
              <td style="min-width:170px">
                @if($p->CBS_REFERENCE)
                  <span class="tk-mono">{{ $p->CBS_REFERENCE }}</span> · {{ $money($p->PAID_AMOUNT) }}
                  <span class="tk-sub">{{ $p->PAID_BY }} · {{ optional($local($p->PAID_DATE))->format('d M Y H:i') }}</span>
                  <span class="tk-sub">
                    @if($p->PAYMENT_ADVICE)<button type="button" class="btn btn-outline-secondary tk-btn" data-bs-toggle="modal" data-bs-target="#mtModal" data-advice="{{ $p->PAYMENT_ADVICE }}" onclick="tkShowMt(this)">MT</button>@endif
                    @if($p->PAYMENT_ADVICE_ATTACHMENT)<a class="btn btn-outline-secondary tk-btn" target="_blank" href="{{ route('takeover-outgoing-advice-pdf', ['id' => $p->ID]) }}">PDF</a>@endif
                  </span>
                @else<span class="tk-sub">Not recorded yet</span>@endif
                @php $canPay = $canRecord && $loan && $loan->TRANSACTION_STATUS === 'Approved' && $p->STATUS !== 'NOTIFIED'; @endphp
                @if($canPay)
                  <span class="tk-sub" style="margin-top:6px"><button type="button" class="btn {{ $p->CBS_REFERENCE ? 'btn-outline-primary' : 'btn-primary' }} tk-btn" data-bs-toggle="modal" data-bs-target="#transferModal"
                    data-app="{{ $p->APPLICATION_NUMBER }}" data-amount="{{ (float) $p->TAKE_OVER_AMOUNT }}" data-ref="{{ $p->CBS_REFERENCE }}"
                    data-payto="{{ $p->FSP1_BANK_ACCOUNT_NAME }} · {{ $p->FSP1_BANK_ACCOUNT }} · {{ $p->FSP1_SWIFT_CODE }}" data-fspref="{{ $p->FSP1_PAYMENT_REFERENCE_NUMBER }}"
                    onclick="tkTransfer(this)"><i class="fa fa-upload"></i> {{ $p->CBS_REFERENCE ? 'Update transfer' : 'Record transfer' }}</button></span>
                @elseif($loan && $p->STATUS === 'RECEIVED' && !in_array($loan->TRANSACTION_STATUS, ['Approved', 'Rejected']) && !in_array($loan->LOAN_STATUS, ['Cancelled', 'Rejected']))
                  <span class="tk-sub">Pay after employer approval</span>
                @endif
              </td>
              <td style="min-width:140px">
                @if($p->STATUS === 'NOTIFIED')<span class="tk-pill ok">Completed</span><span class="tk-sub">ESS notified {{ optional($local($p->NOTIFIED_DATE))->format('d M H:i') }}</span>
                @elseif($p->STATUS === 'PAID')<span class="tk-pill blue">Paid</span><span class="tk-sub">ESS not yet notified</span>
                @elseif($loan && (in_array($loan->LOAN_STATUS, ['Cancelled', 'Rejected']) || $loan->TRANSACTION_STATUS === 'Rejected'))<span class="tk-pill grey">{{ $loan->TRANSACTION_STATUS === 'Rejected' ? 'Rejected' : $loan->LOAN_STATUS }}</span><span class="tk-sub">Nothing to pay</span>
                @else<span class="tk-pill wait">Awaiting payment</span>@endif
                @if($p->STATUS_DESCRIPTION)<span class="tk-note">{{ $p->STATUS_DESCRIPTION }}</span>@endif
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="tk-pager">
        <span>Showing <b>{{ number_format($payments->firstItem()) }}</b>–<b>{{ number_format($payments->lastItem()) }}</b> of <b>{{ number_format($payments->total()) }}</b></span>
        <div>{{ $payments->onEachSide(1)->links() }}</div>
      </div>
    @endif
  </div>
</div>

<div class="modal fade" id="mtModal" tabindex="-1" aria-hidden="true" style="display:none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white"><h5 class="modal-title">Payment advice to FSP1 (MT / ISO 20022)</h5><button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body"><pre class="tk-mt" id="tkMtText"></pre></div>
    </div>
  </div>
</div>
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true" style="display:none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white"><h5 class="modal-title">Record transfer to FSP1</h5><button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form method="post" action="{{ route('takeover-transfer-record') }}" enctype="multipart/form-data" onsubmit="return tkBusy(this, 'Saving transfer…')">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="application_number" id="tkTrApp">
          <div class="alert alert-light-primary" style="font-size:12.5px">
            Make the fund transfer manually in Profits/TISS first, then record it here.<br>
            Application <b id="tkTrAppText"></b> · pay to <b id="tkTrPayTo"></b> · their reference <b id="tkTrFspRef"></b>
          </div>
          <div class="row">
            <div class="col-md-7"><label class="col-form-label">Payment reference (TISS / transfer)</label>
              <input class="form-control" type="text" name="transfer_reference" id="tkTrRef" maxlength="60" required></div>
            <div class="col-md-5"><label class="col-form-label">Amount paid (TZS)</label>
              <input class="form-control" type="number" step="0.01" min="1" name="paid_amount" id="tkTrAmount" required></div>
          </div>
          <label class="col-form-label">SWIFT copy (PDF, max 5 MB)</label>
          <input class="form-control" type="file" name="swift_copy" accept="application/pdf" required>
          <details class="mt-3"><summary style="font-size:12.5px;cursor:pointer">Payment advice text (optional — MT103 is generated from the reference if left empty)</summary>
            <textarea class="form-control mt-2" name="payment_advice" rows="6" style="font-family:SFMono-Regular,Menlo,Consolas,monospace;font-size:12px" placeholder="{1:F01MWCOTZTZAXXX}{2:I103...}{4: ... -}"></textarea>
          </details>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
          <button class="btn btn-primary" type="submit"><i class="fa fa-upload"></i> Save transfer</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function tkShowMt(btn) { document.getElementById('tkMtText').textContent = btn.dataset.advice; }
  function tkTransfer(btn) {
    document.getElementById('tkTrApp').value = btn.dataset.app;
    document.getElementById('tkTrAppText').textContent = btn.dataset.app;
    document.getElementById('tkTrPayTo').textContent = btn.dataset.payto;
    document.getElementById('tkTrFspRef').textContent = btn.dataset.fspref;
    document.getElementById('tkTrAmount').value = btn.dataset.amount;
    document.getElementById('tkTrRef').value = btn.dataset.ref || '';
  }
</script>
@endpush
@endsection
