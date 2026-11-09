@extends('layouts.admin.master')
@section('title', 'Takeover Incoming Payments')
@php
    use Carbon\Carbon;
    $money = fn ($x) => ($x === null || $x === '') ? '—' : number_format((float) str_replace(',', '', $x), 2, '.', ',');
    $now = now('Africa/Dar_es_Salaam');
    $tone = ['pending' => '#f28c28', 'settled' => '#16a34a', 'unsettled' => '#dc2626', 'all' => '#0d47a1'];
    $local = fn ($d) => $d ? Carbon::parse($d->format('Y-m-d H:i:s'), 'Africa/Dar_es_Salaam') : null;
@endphp
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Takeover · Incoming Payments · {{ $filters[$filter] }}</h3>
    @endslot
    <li class="breadcrumb-item">Take Over Loans</li>
    <li class="breadcrumb-item active">Incoming Payments</li>
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
    The bank that took over an <b>MCB loan</b> has paid (<i>TAKEOVER_PAYMENT_NOTIFICATION</i>). Check the money in core banking, then confirm
    <b>Settled</b> or <b>Not settled</b> — ESS expects the confirmation within <b>2 hours</b>. Settled loans get loan status <b>Taken Over</b>; liquidate them in Profits as usual.
  </p>
  <div class="tk-kpis">
    @foreach($filters as $key => $label)
      <a class="tk-kpi {{ $filter === $key ? 'on' : '' }}" style="--a:{{ $tone[$key] }}" href="{{ route('incoming-payment-request', array_filter(['filter' => $key, 'q' => $search])) }}"><span>{{ $label }}</span><b>{{ number_format($counts[$key]) }}</b></a>
    @endforeach
  </div>

  <div class="tk-card">
    <div class="tk-bar">
      <div><b>{{ $filters[$filter] }}</b><span class="tk-count">{{ number_format($payments->total()) }} record{{ $payments->total() == 1 ? '' : 's' }}</span></div>
      <form class="tk-search" method="get" action="{{ route('incoming-payment-request') }}">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <select name="per_page" onchange="this.form.submit()">@foreach([15, 25, 50, 100] as $n)<option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }} / page</option>@endforeach</select>
        <input type="text" name="q" value="{{ $search }}" placeholder="Loan no., application no. or payment reference">
        <button class="btn btn-primary tk-btn" type="submit"><i class="fa fa-search"></i></button>
      </form>
    </div>
    @if($payments->isEmpty())
      <div class="tk-empty"><i class="fa fa-check-circle fa-2x d-block mb-2" style="color:#86efac"></i>No payments{{ $filter === 'pending' ? ' waiting for confirmation' : '' }}{{ $search !== '' ? ' matching "'.$search.'"' : '' }}.</div>
    @else
      <div class="tk-scroll">
        <table class="tk-table">
          <thead><tr><th>Received at MCB</th><th>Employee / loan</th><th class="r">Paid</th><th>References</th><th>Advice</th><th>Status</th><th>Confirmation</th></tr></thead>
          <tbody>
          @foreach($payments as $p)
            @php
              $loan = $p->loan; $quote = $p->quote;
              $received = $local($p->REQUEST_DATE);
              $pending = $p->isPending();
              $deadline = $received ? $received->copy()->addHours(2) : null;
              $hasPdf = !empty($p->PAYMENT_ADVICE_ATTACHMENT);
            @endphp
            <tr>
              <td style="white-space:nowrap">
                {{ $received ? $received->format('d M Y H:i') : ($p->PAYMENT_DATE ?: '—') }}
                @if($pending && $deadline)
                  @if($deadline->gt($now))<span class="tk-pill wait" style="margin-top:4px">{{ $now->diff($deadline)->format('%hh %Im') }} left</span>
                  @else<span class="tk-pill bad" style="margin-top:4px">Overdue {{ $deadline->diffForHumans($now, true) }}</span>@endif
                @elseif($received)<span class="tk-sub">{{ $received->diffForHumans() }}</span>@endif
              </td>
              <td style="min-width:190px">
                @if($loan)
                  <a href="{{ route('staff-profile', ['id' => $loan->ID]) }}"><b>{{ strtoupper(trim($loan->FIRST_NAME.' '.$loan->LAST_NAME)) }}</b></a>
                  <span class="tk-sub">Loan <span class="tk-mono">{{ $p->LOAN_NUMBER }}</span> · {{ $loan->LOAN_STATUS }}</span>
                @else
                  <span class="tk-mono">{{ $p->LOAN_NUMBER }}</span><span class="tk-sub">Loan not found at MCB</span>
                @endif
                @if($p->APPLICATION_NUMBER)<span class="tk-sub">ESS {{ $p->APPLICATION_NUMBER }}</span>@endif
              </td>
              <td class="r">{{ $money($p->TOTAL_PAYOFF_AMOUNT) }}
                @if($quote)<span class="tk-sub" style="font-weight:500">Quoted {{ $money($quote->TOTAL_PAYOFF_AMOUNT) }}</span>@endif
                <span class="tk-sub" style="font-weight:500">{{ $p->PAYMENT_DATE }}</span>
              </td>
              <td style="min-width:170px"><span class="tk-mono">{{ $p->PAYMENT_REFERENCE_NUMBER }}</span><span class="tk-sub">FSP ref {{ $p->FSP_REFERENCE_NUMBER ?: '—' }}</span></td>
              <td style="white-space:nowrap">
                @if($p->PAYMENT_ADVICE)
                  <button type="button" class="btn btn-outline-secondary tk-btn" data-bs-toggle="modal" data-bs-target="#mtModal" data-advice="{{ $p->PAYMENT_ADVICE }}" onclick="tkShowMt(this)">MT</button>
                @endif
                @if($hasPdf)
                  <a class="btn btn-outline-secondary tk-btn" target="_blank" href="{{ route('takeover-payment-advice-pdf', ['id' => $p->ID]) }}">PDF</a>
                @endif
                @if(!$p->PAYMENT_ADVICE && !$hasPdf)<span class="tk-sub">—</span>@endif
              </td>
              <td style="min-width:150px">
                @if($p->ACK_STATUS === 'SETTLED')<span class="tk-pill ok">Settled</span>
                @elseif($p->ACK_STATUS === 'UNSETTLED')<span class="tk-pill bad">Not settled</span>
                @elseif($p->STATUS === 'ACK_FAILED')<span class="tk-pill bad">Confirmation failed</span>
                @elseif($pending)<span class="tk-pill wait">To confirm</span>
                @else<span class="tk-pill grey">{{ empty($p->MESSAGE_ID) ? 'Legacy record' : $p->STATUS }}</span>@endif
                @if($p->STATUS_DESCRIPTION)<span class="tk-note">{{ $p->STATUS_DESCRIPTION }}</span>@endif
              </td>
              <td style="min-width:190px">
                @if($p->ACK_STATUS)
                  <b>{{ $p->ACK_BY }}</b><span class="tk-sub">{{ $p->ACK_DATE ? $p->ACK_DATE->format('d M Y H:i') : '' }}</span><span class="tk-sub" style="color:#374151">{{ $p->ACK_REMARKS }}</span>
                @elseif($pending && $canConfirm)
                  <button class="btn btn-success tk-btn" type="button" data-bs-toggle="modal" data-bs-target="#confirmModal" data-id="{{ $p->ID }}" data-status="SETTLED" data-loan="{{ $p->LOAN_NUMBER }}" data-amount="{{ $money($p->TOTAL_PAYOFF_AMOUNT) }}" onclick="tkConfirm(this)"><i class="fa fa-check"></i> Settled</button>
                  <button class="btn btn-outline-danger tk-btn" type="button" data-bs-toggle="modal" data-bs-target="#confirmModal" data-id="{{ $p->ID }}" data-status="UNSETTLED" data-loan="{{ $p->LOAN_NUMBER }}" data-amount="{{ $money($p->TOTAL_PAYOFF_AMOUNT) }}" onclick="tkConfirm(this)">Not settled</button>
                @elseif($pending)
                  <span class="tk-sub">Credit Operation to confirm</span>
                @endif
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
      <div class="modal-header bg-primary text-white"><h5 class="modal-title">Payment advice (MT / ISO 20022)</h5><button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body"><pre class="tk-mt" id="tkMtText"></pre></div>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true" style="display:none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" id="tkConfirmHead"><h5 class="modal-title" id="tkConfirmTitle">Confirm payment</h5><button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form method="post" action="{{ route('takeover-payment-confirm') }}">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="id" id="tkConfirmId">
          <input type="hidden" name="status" id="tkConfirmStatus">
          <p class="mb-2">Loan <b id="tkConfirmLoan"></b> · paid <b id="tkConfirmAmount"></b></p>
          <p class="mb-2" style="font-size:12.5px" id="tkConfirmHint"></p>
          <label class="col-form-label">Remarks (sent to ESS)</label>
          <textarea class="form-control" name="remarks" required minlength="3" maxlength="255" rows="2" id="tkConfirmRemarks"></textarea>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
          <button class="btn" type="submit" id="tkConfirmBtn">Send to ESS</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function tkShowMt(btn) { document.getElementById('tkMtText').textContent = btn.dataset.advice.replace(/ :/g, '\n:'); }
  function tkConfirm(btn) {
    var settled = btn.dataset.status === 'SETTLED';
    document.getElementById('tkConfirmId').value = btn.dataset.id;
    document.getElementById('tkConfirmStatus').value = btn.dataset.status;
    document.getElementById('tkConfirmLoan').textContent = btn.dataset.loan;
    document.getElementById('tkConfirmAmount').textContent = 'TZS ' + btn.dataset.amount;
    document.getElementById('tkConfirmTitle').textContent = settled ? 'Confirm payment SETTLED' : 'Confirm payment NOT SETTLED';
    document.getElementById('tkConfirmHead').className = 'modal-header text-white ' + (settled ? 'bg-success' : 'bg-danger');
    document.getElementById('tkConfirmBtn').className = 'btn ' + (settled ? 'btn-success' : 'btn-danger');
    document.getElementById('tkConfirmHint').textContent = settled
      ? 'Only confirm after the money is seen in core banking. The loan will get status "Taken Over".'
      : 'The other bank and ESS will be told the payment is not settled. Give the reason.';
    document.getElementById('tkConfirmRemarks').value = settled ? 'Funds received in settlement account' : '';
  }
</script>
@endpush
@endsection
