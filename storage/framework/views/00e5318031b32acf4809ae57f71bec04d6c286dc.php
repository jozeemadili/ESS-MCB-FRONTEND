<?php $__env->startSection('title'); ?>Login
 | <?php echo e(Config('custom.constants.solution.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/sweetalert2.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <section>
	    <div class="container-fluid">
	        <div class="row">
	            <div class="col-xl-5"><img class="bg-img-cover bg-center" src="<?php echo e(asset('assets/images/login/lbg.png')); ?>" alt="looginpage" /></div>
	            <div class="col-xl-7 p-0">
	                <div class="login-card">
	                    <div class="theme-form login-form">
						<!-- <center><img width="40%" src="<?php echo e(asset('assets/images/logo/mwalimu2.png')); ?>" alt="e-Mikopo" /></center> -->
						
							<div>
								<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('components.auth.resetpassword')->html();
} elseif ($_instance->childHasBeenRendered('DbWhQ8e')) {
    $componentId = $_instance->getRenderedChildComponentId('DbWhQ8e');
    $componentTag = $_instance->getRenderedChildComponentTagName('DbWhQ8e');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('DbWhQ8e');
} else {
    $response = \Livewire\Livewire::mount('components.auth.resetpassword');
    $html = $response->html();
    $_instance->logRenderedChild('DbWhQ8e', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
							</div>
							<div class="login-social-title" style="margin-top: 180px;">
							<h5>&copy; <?php echo e(Config('custom.constants.solution.name')); ?> <?php echo e(Config('custom.constants.solution.version')); ?></h5>
	                        </div>
	                        <p>Have Password ?<a class="ms-2" href="<?php echo e(route('/')); ?>">Sigin here</a></p>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</section>

    <?php $__env->startPush('scripts'); ?>
	<script src="<?php echo e(asset('assets/js/sweet-alert/sweetalert.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/sweet-alert/sweetalert.min.js')); ?>"></script>
	<script>
	
	  window.addEventListener('swal:modal', event => { 
		  swal({
			title: event.detail.message,
			text: event.detail.text,
			icon: event.detail.type,
			buttons:false,
			customClass:'swal-wide'
		  });
	  });
		
	  window.addEventListener('swal:confirm', event => { 
		  swal({
			title: event.detail.message,
			text: event.detail.text,
			icon: event.detail.type,
			buttons: true,
			dangerMode: true,
		  })
		  .then((willDelete) => {
			if (willDelete) {
			  window.livewire.emit('remove');
			}
		  });
	  });
	   </script>
    <?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.authentication.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/authentication/forget-password.blade.php ENDPATH**/ ?>