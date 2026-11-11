@extends('layouts.admin.master')
@section('title', 'Products')
@php
  $canAdd = Auth::user()->role == 'ADMIN' && optional(Auth::user()->company)->id == 1;
  $counts = \App\Models\Product::selectRaw('STATUS, COUNT(*) c')->groupBy('STATUS')->pluck('c', 'STATUS');
  $stClass = function ($s) { return $s == 'Published' ? 'ok' : ($s == 'Decommissioned' ? 'bad' : 'wait'); };
@endphp
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Products</h3>
    @endslot
    @slot('breadcrumb_action_buttons')
    @endslot
    <li class="breadcrumb-item">Products</li>
    <li class="breadcrumb-item active">Registration</li>
  @endcomponent

@include('admin.products.partials.style')

<div class="container-fluid pd">
  @foreach ($errors->all() as $error)
    <div class="alert alert-danger outline alert-dismissible fade show" role="alert"><i class="icon-info-alt txt-danger"></i> {{ $error }}
      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button></div>
  @endforeach
  @if($message = Session::get('success'))
    <div class="alert alert-success outline alert-dismissible fade show" role="alert"><i class="icofont icofont-check-circled"></i> {!! $message !!}
      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button></div>
  @endif

  <div class="pd-stats">
    <div class="pd-stat"><i class="fa fa-cubes"></i><div><b>{{ number_format($counts->sum()) }}</b><span>Total products</span></div></div>
    <div class="pd-stat ok"><i class="fa fa-check-circle"></i><div><b>{{ number_format($counts['Published'] ?? 0) }}</b><span>Published to ESS</span></div></div>
    <div class="pd-stat wait"><i class="fa fa-pencil-square-o"></i><div><b>{{ number_format($counts['Not Published'] ?? 0) }}</b><span>Not published</span></div></div>
    <div class="pd-stat bad"><i class="fa fa-ban"></i><div><b>{{ number_format($counts['Decommissioned'] ?? 0) }}</b><span>Decommissioned</span></div></div>
  </div>

  <div class="pd-card">
    <div class="pd-head">
      <i class="fa fa-cube"></i>
      <div class="flex-grow-1"><b>Loan products</b><small>Open a product to see its details, conditions and to publish it to ESS.</small></div>
      <input type="search" class="form-control pd-search" placeholder="Search code or name" oninput="pdFilter(this.value)">
      @if($canAdd)
        <button class="btn pd-btn" data-bs-toggle="modal" data-bs-target="#newModal"><i class="fa fa-plus"></i> New product</button>
      @endif
    </div>

    @if(count($products) > 0)
      <div class="table-responsive">
        <table class="table pd-table mb-0" id="pdTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Product</th>
              <th>Tenure</th>
              <th>Amount</th>
              <th class="text-end">Interest</th>
              <th class="text-end">Processing fee</th>
              <th class="text-end">Insurance</th>
              <th>Repayment</th>
              <th>Status</th>
              <th>Registered</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($products as $product)
              @php $url = route('products-condtions', ['id' => $product['ID']]); @endphp
              <tr data-q="{{ strtolower($product['CODE'].' '.$product['NAME']) }}" onclick="location.href='{{ $url }}'">
                <td class="pd-mut">{{ $products->firstItem() + $loop->index }}</td>
                <td>
                  <a href="{{ $url }}" class="pd-name">{{ strtoupper($product['NAME']) }}</a>
                  <small class="pd-mut d-block">Code {{ $product['CODE'] }}@if($product['IS_EXECUTIVE']) · Executive @endif</small>
                </td>
                <td class="text-nowrap">{{ $product['MINIMUM_TENURE'] }} – {{ $product['MAXIMUM_TENURE'] }} <small class="pd-mut">months</small></td>
                <td class="text-nowrap">{{ number_format($product['MINIMUM_AMOUNT'], 2) }} – {{ number_format($product['MAXIMUM_AMOUNT'], 2) }} <small class="pd-mut">{{ $product['CURRENCY'] }}</small></td>
                <td class="text-end"><b>{{ $product['INTEREST_RATE'] }}%</b></td>
                <td class="text-end">{{ $product['PROCESSING_FEE_RATE'] }}%</td>
                <td class="text-end">{{ $product['INSURANCE_RATE'] }}%</td>
                <td>{{ $product['REPAYMENT_TYPE'] }}</td>
                <td><span class="pd-pill {{ $stClass($product['STATUS']) }}">{{ $product['STATUS'] ?: 'Not Published' }}</span></td>
                <td class="text-nowrap pd-mut" title="{{ $product['DATE'] }}">{{ Carbon\Carbon::parse($product['DATE'])->diffForHumans() }}</td>
                <td class="text-end"><a href="{{ $url }}" class="pd-open" aria-label="Open product"><i class="fa fa-angle-right"></i></a></td>
              </tr>
            @endforeach
            <tr id="pdNone" style="display:none"><td colspan="11" class="text-center pd-mut py-4">No product on this page matches your search.</td></tr>
          </tbody>
        </table>
      </div>
      @if($products->hasPages())
        <div class="pd-foot">
          <span class="pd-mut">Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}</span>
          {{ $products->links() }}
        </div>
      @endif
    @else
      <div class="pd-empty"><i class="fa fa-cube"></i><b>No products yet</b>
        <span>@if($canAdd) Use <b>New product</b> to register the first one. @else Products registered by the administrator will appear here. @endif</span></div>
    @endif
  </div>
</div>

@if($canAdd)
<!-- NEW MODAL START -->
<div class="modal fade pd" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content pd-modal">
      <form method="post" action="/v1/products/add" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
        @csrf
        <div class="modal-header">
          <div><h5 class="modal-title">New loan product</h5><small class="pd-mut">It is saved as <b>Not Published</b>. Publish it to ESS from the product page.</small></div>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="pd-sec">Product</div>
          <div class="row g-3">
            <div class="col-md-6"><label>Product name</label><input class="form-control" type="text" required name="name" value="{{ old('name') }}"></div>
            <div class="col-md-3"><label>Product code</label><input class="form-control" type="text" required name="code" value="{{ old('code') }}"></div>
            <div class="col-md-3"><label>Executive product?</label>
              <div class="pd-seg">
                <input type="radio" id="exYes" value="1" name="isExecutive" {{ old('isExecutive', '1') == '1' ? 'checked' : '' }}><label for="exYes">Yes</label>
                <input type="radio" id="exNo" value="0" name="isExecutive" {{ old('isExecutive') === '0' ? 'checked' : '' }}><label for="exNo">No</label>
              </div>
            </div>
          </div>

          <div class="pd-sec">Limits</div>
          <div class="row g-3">
            <div class="col-md-3"><label>Minimum tenure (months)</label><input class="form-control" type="number" min="1" required name="minimumTenure" value="{{ old('minimumTenure') }}"></div>
            <div class="col-md-3"><label>Maximum tenure (months)</label><input class="form-control" type="number" min="1" required name="maximumTenure" value="{{ old('maximumTenure') }}"></div>
            <div class="col-md-3"><label>Minimum amount</label><input class="form-control" type="number" step="any" required name="minimumAmount" value="{{ old('minimumAmount') }}"></div>
            <div class="col-md-3"><label>Maximum amount</label><input class="form-control" type="number" step="any" required name="maximumAmount" value="{{ old('maximumAmount') }}"></div>
          </div>

          <div class="pd-sec">Pricing &amp; repayment</div>
          <div class="row g-3">
            <div class="col-md-4"><label>Interest rate (% a year)</label><input class="form-control" type="number" step="any" required name="InterestRate" value="{{ old('InterestRate') }}"></div>
            <div class="col-md-4"><label>Processing fee rate (%)</label><input class="form-control" type="number" step="any" required name="processingFeeRate" value="{{ old('processingFeeRate') }}"></div>
            <div class="col-md-4"><label>Insurance rate (%)</label><input class="form-control" type="number" step="any" required name="insuranceRate" value="{{ old('insuranceRate') }}"></div>
            <div class="col-md-4"><label>Currency</label>
              <select class="form-select" required name="ccy">
                <option value="">Choose currency</option>
                @foreach(['TZS', 'USD', 'GBP'] as $c)<option {{ old('ccy', 'TZS') == $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-4"><label>Repayment type</label>
              <select class="form-select" required name="repaymentType">
                <option value="">Choose repayment type</option>
                <option selected>Salary</option>
              </select>
            </div>
            <div class="col-12"><label>Description</label><textarea class="form-control" rows="3" required name="description">{{ old('description') }}</textarea></div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn pd-btn" type="submit">Confirm &amp; register</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- NEW MODAL END -->
@endif

@push('scripts')
<script>
  function pdFilter(q) {
    q = q.trim().toLowerCase();
    var shown = 0;
    document.querySelectorAll('#pdTable tbody tr[data-q]').forEach(function (tr) {
      var ok = !q || tr.dataset.q.indexOf(q) !== -1;
      tr.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });
    var none = document.getElementById('pdNone');
    if (none) none.style.display = shown ? 'none' : '';
  }
</script>
@endpush
@endsection
