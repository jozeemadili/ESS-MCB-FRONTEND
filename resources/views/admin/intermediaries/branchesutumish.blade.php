@extends('layouts.admin.master')
@section('title', 'Branches')
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Branches &amp; ESS Districts</h3>
    @endslot
    @slot('breadcrumb_action_buttons')
    @endslot
    <li class="breadcrumb-item">Security</li>
    <li class="breadcrumb-item active">Branches</li>
  @endcomponent
  <div class="container-fluid">
    @include('admin.loan_take_over._style')
    @livewire('branch-submit')
  </div>
@endsection
