@extends('layouts.admin.master')
@section('title', 'Product Conditions')
@php
  $editable = $products->STATUS == 'Not Published' || $products->STATUS == 'Decommissioned';
@endphp
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Product Details</h3>
    @endslot
    @slot('breadcrumb_action_buttons')
      <li><a class="btn btn-outline-primary" href="{{ route('products-registration') }}"><i class="fa fa-arrow-left"></i> All products</a></li>
    @endslot
    <li class="breadcrumb-item"><a href="{{ route('products-registration') }}">Products</a></li>
    <li class="breadcrumb-item active">{{ strtoupper($products->NAME) }}</li>
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

  <div class="row">
    <div class="col-xl-4">
      @livewire('product-manage', ['products' => $products, 'productId' => $id])
    </div>

    <div class="col-xl-8">
      <div class="pd-card">
        <div class="pd-head">
          <i class="fa fa-list-ol"></i>
          <div class="flex-grow-1"><b>Product conditions</b>
            <small>{{ count($responseData) }} {{ \Illuminate\Support\Str::plural('condition', count($responseData)) }} sent to ESS with the product.</small></div>
          @if($editable)
            <button class="btn pd-btn" data-bs-toggle="modal" data-bs-target="#newModal"><i class="fa fa-plus"></i> New condition</button>
          @else
            <span class="pd-pill ok"><i class="fa fa-lock"></i> Locked while published</span>
          @endif
        </div>

        @forelse($responseData as $condition)
          <div class="pd-cond">
            <div class="n">{{ $loop->iteration }}</div>
            <div class="t">{{ $condition['DESCRIPTION'] }}
              <small>{{ $condition['CONDITION_NUMBER'] }}@if($condition['DATE']) · added {{ \Carbon\Carbon::parse($condition['DATE'])->format('d M Y, H:i') }}@endif</small></div>
            @if($editable)
              <a href="{!! route('product-condition-update', ['id' => $condition->ID, 'status' => 'Inactive']) !!}" class="btn btn-outline-danger btn-xs"
                 onclick="return confirm('Delete condition {{ $condition['CONDITION_NUMBER'] }}?')"><i class="icofont icofont-ui-delete"></i> Delete</a>
            @else
              <span class="pd-pill ok">Published</span>
            @endif
          </div>
        @empty
          <div class="pd-empty"><i class="fa fa-list-ol"></i><b>No conditions yet</b>
            <span>@if($editable) Add the terms ESS should show for this product with <b>New condition</b>. @else This product was published without conditions. @endif</span></div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@if($editable)
<!-- NEW MODAL START -->
<div class="modal fade pd" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content pd-modal">
      <form method="post" action="/v1/products/condition/add" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
        @csrf
        <input type="hidden" name="id" value="{{ $id }}">
        <div class="modal-header">
          <div><h5 class="modal-title">New condition</h5><small class="pd-mut">For {{ strtoupper($products->NAME) }} ({{ $products->CODE }})</small></div>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="pd-sec">Condition</div>
          <label>Description</label>
          <textarea class="form-control" rows="4" required name="description" placeholder="e.g. Applicant must be a permanent and pensionable employee">{{ old('description') }}</textarea>
        </div>
        <div class="modal-footer">
          <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn pd-btn" type="submit">Confirm &amp; add</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- NEW MODAL END -->
@endif
@endsection
