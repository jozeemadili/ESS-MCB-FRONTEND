@extends('layouts.admin.master')
@section('title', 'Branches')
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2.css') }}">
@endpush
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
@push('scripts')
<script src="{{ asset('assets/js/select2/select2.full.min.js') }}"></script>
<script>
  // Searchable district pickers (select2) inside the Livewire branch component. The selects sit in wire:ignore blocks;
  // their value is pushed to the component field named in data-field, and new blocks are set up after each update.
  document.addEventListener('livewire:load', function () {
    function component(el) { return Livewire.find(el.closest('[wire\\:id]').getAttribute('wire:id')); }
    function init() {
      $('select.br-s2').each(function () {
        var el = this, $s = $(this);
        if ($s.hasClass('select2-hidden-accessible')) return;
        var $modal = $s.closest('.modal');
        $s.select2({ width: '100%', placeholder: $s.data('placeholder'), allowClear: !el.multiple,
          dropdownParent: $modal.length ? $modal : $(document.body), closeOnSelect: !el.multiple });
        $s.on('change', function () { component(el).set(el.dataset.field, $s.val() || (el.multiple ? [] : ''), true); });
      });
    }
    init();
    Livewire.hook('message.processed', init);
    window.addEventListener('branch-mapping-saved', function () { $('select.br-s2[multiple]').val(null).trigger('change.select2'); });
  });
</script>
@endpush
@endsection
