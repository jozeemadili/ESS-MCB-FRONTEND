<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>
    <?php $__env->startSection('content'); ?>
      <!-- Container-fluid starts-->
      <div class="container-fluid dashboard-default-sec">

      <div class="row">
      
      </div>

      <div class="row">
      <div class="col-lg-7">
          <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('components.reports.salescharts')->html();
} elseif ($_instance->childHasBeenRendered('hxppU4z')) {
    $componentId = $_instance->getRenderedChildComponentId('hxppU4z');
    $componentTag = $_instance->getRenderedChildComponentTagName('hxppU4z');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('hxppU4z');
} else {
    $response = \Livewire\Livewire::mount('components.reports.salescharts');
    $html = $response->html();
    $_instance->logRenderedChild('hxppU4z', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
        </div>
        <div class="col-lg-5">
            <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('components.reports.quotationstatus')->html();
} elseif ($_instance->childHasBeenRendered('uDcDOV5')) {
    $componentId = $_instance->getRenderedChildComponentId('uDcDOV5');
    $componentTag = $_instance->getRenderedChildComponentTagName('uDcDOV5');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('uDcDOV5');
} else {
    $response = \Livewire\Livewire::mount('components.reports.quotationstatus');
    $html = $response->html();
    $_instance->logRenderedChild('uDcDOV5', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
        </div>
      </div>
      

      </div>
      <!-- Container-fluid Ends-->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/dashboard/home.blade.php ENDPATH**/ ?>