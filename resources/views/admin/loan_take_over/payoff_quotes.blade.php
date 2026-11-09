@extends('layouts.admin.master')
@section('title', 'Takeover Payoff Quotes')
@php
    $money = fn ($x) => $x === null ? '—' : number_format((float) $x, 2, '.', ',');
    $now = now('Africa/Dar_es_Salaam');
    $tone = ['quoted' => '#f28c28', 'paid' => '#16a34a', 'expired' => '#dc2626', 'all' => '#0d47a1'];
@endphp
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Takeover · Payoff Quotes · {{ $filters[$filter] }}</h3>
    @endslot
    <li class="breadcrumb-item">Take Over Loans</li>
    <li class="breadcrumb-item active">Payoff Quotes</li>
  @endcomponent
  @include('admin.loan_take_over._style')

<div class="container-fluid tk">
  <p class="text-muted" style="font-size:12.5px;margin-top:-6px">
    Another bank (FSP2) asked to take over these <b>MCB loans</b>. MCB answered with the payoff amount, MCB's settlement account and a payment reference
    (<i>TAKEOVER_PAY_OFF_BALANCE_REQUEST</i>). The quote turns <b>Paid</b> when the other bank's payment notification arrives.
  </p>
  <div class="tk-kpis">
    @foreach($filters as $key => $label)
      <a class="tk-kpi {{ $filter === $key ? 'on' : '' }}" style="--a:{{ $tone[$key] }}" href="{{ route('outstanding-balance-requests', array_filter(['filter' => $key, 'q' => $search])) }}"><span>{{ $label }}</span><b>{{ number_format($counts[$key]) }}</b></a>
    @endforeach
  </div>

  <div class="tk-card">
    <div class="tk-bar">
      <div><b>{{ $filters[$filter] }}</b><span class="tk-count">{{ number_format($quotes->total()) }} record{{ $quotes->total() == 1 ? '' : 's' }}</span></div>
      <form class="tk-search" method="get" action="{{ route('outstanding-balance-requests') }}">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <select name="per_page" onchange="this.form.submit()">@foreach([15, 25, 50, 100] as $n)<option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }} / page</option>@endforeach</select>
        <input type="text" name="q" value="{{ $search }}" placeholder="Loan no., check no., name or payment reference">
        <button class="btn btn-primary tk-btn" type="submit"><i class="fa fa-search"></i></button>
      </form>
    </div>
    @if($quotes->isEmpty())
      <div class="tk-empty"><i class="fa fa-inbox fa-2x d-block mb-2" style="color:#c7d2e3"></i>No payoff quotes{{ $search !== '' ? ' matching "'.$search.'"' : '' }}.</div>
    @else
      <div class="tk-scroll">
        <table class="tk-table">
          <thead><tr><th>Requested</th><th>Employee</th><th>Loan</th><th class="r">Payoff amount</th><th>Pay to</th><th>Valid until</th><th>Status</th></tr></thead>
          <tbody>
          @foreach($quotes as $q)
            @php
              $expired = $q->STATUS === 'QUOTED' && $q->FINAL_PAYMENT_DATE && \Carbon\Carbon::parse($q->FINAL_PAYMENT_DATE->format('Y-m-d H:i:s'), 'Africa/Dar_es_Salaam')->lt($now);
            @endphp
            <tr>
              <td style="white-space:nowrap">{{ $q->REQUEST_DATE ? $q->REQUEST_DATE->format('d M Y H:i') : '—' }}<span class="tk-sub">{{ $q->REQUEST_DATE ? \Carbon\Carbon::parse($q->REQUEST_DATE->format('Y-m-d H:i:s'), 'Africa/Dar_es_Salaam')->diffForHumans() : '' }}</span></td>
              <td style="min-width:180px">
                @if($q->loan)<a href="{{ route('staff-profile', ['id' => $q->loan->ID]) }}"><b>{{ strtoupper($q->EMPLOYEE_NAME ?: trim($q->loan->FIRST_NAME.' '.$q->loan->LAST_NAME)) }}</b></a>@else<b>{{ strtoupper($q->EMPLOYEE_NAME) }}</b>@endif
                <span class="tk-sub">Check No. {{ $q->CHECK_NUMBER }} · {{ $q->VOTE_NAME }}</span>
              </td>
              <td><span class="tk-mono">{{ $q->LOAN_NUMBER }}</span><span class="tk-sub">{{ $q->PAYMENT_OPTION }}</span></td>
              <td class="r">{{ $money($q->TOTAL_PAYOFF_AMOUNT) }}<span class="tk-sub" style="font-weight:500">Principal {{ $money($q->PRINCIPAL_BALANCE) }}</span></td>
              <td style="min-width:190px"><span class="tk-mono">{{ $q->SETTLEMENT_ACCOUNT }}</span><span class="tk-sub">Ref <span class="tk-mono">{{ $q->PAYMENT_REFERENCE_NUMBER }}</span> · {{ $q->FSP_REFERENCE_NUMBER }}</span></td>
              <td style="white-space:nowrap">{{ $q->FINAL_PAYMENT_DATE ? $q->FINAL_PAYMENT_DATE->format('d M Y H:i') : '—' }}</td>
              <td>
                @if($q->STATUS === 'PAID')<span class="tk-pill ok">Paid</span>
                  <span class="tk-sub"><a href="{{ route('incoming-payment-request', ['filter' => 'all', 'q' => $q->LOAN_NUMBER]) }}">View payment</a></span>
                @elseif($expired)<span class="tk-pill bad">Expired</span>
                @else<span class="tk-pill wait">Awaiting payment</span>@endif
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="tk-pager">
        <span>Showing <b>{{ number_format($quotes->firstItem()) }}</b>–<b>{{ number_format($quotes->lastItem()) }}</b> of <b>{{ number_format($quotes->total()) }}</b></span>
        <div>{{ $quotes->onEachSide(1)->links() }}</div>
      </div>
    @endif
  </div>
</div>
@endsection
