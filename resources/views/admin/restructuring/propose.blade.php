@extends('layouts.admin.master')
@section('title', 'Propose Restructure')
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Propose Restructure</h3>
    @endslot
    @slot('breadcrumb_action_buttons')
      <li><a class="btn btn-outline-secondary" href="{{ route('staff-profile', ['id' => $loan->ID]) }}"><i class="fa fa-arrow-left"></i> Back to loan</a></li>
    @endslot
    <li class="breadcrumb-item">Restructuring</li>
    <li class="breadcrumb-item active">Propose</li>
  @endcomponent

  <div class="container-fluid">
    @livewire('components.restructure.calculator', ['loan' => $loan])
  </div>
@endsection
