<?php $__env->startSection('title'); ?>
<?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/date-picker.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  <?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('breadcrumb_title'); ?>
      <h3><?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?></h3>
    <?php $__env->endSlot(); ?>

    <?php $__env->slot('breadcrumb_action_buttons'); ?>
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
    <?php $__env->endSlot(); ?>
    
    <li class="breadcrumb-item"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[0])); ?></li>
    <li class="breadcrumb-item active"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[1])); ?></li>
  <?php echo $__env->renderComponent(); ?>
  
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              <div class="card">

                  <div class="card-body">
                  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                        <?php echo e($error); ?>

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  
                  <?php if($message = Session::get('success')): ?>
                    <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                        <?php echo $message; ?>

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <br />
					<?php endif; ?>
                    
                      <p>
                      <div class="table-responsive">
                      <?php echo e($payment_datails); ?>

                      <?php if(count($payment_datails)>0): ?>
                      <table class="table table-xs">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">FSP Reference Number</th>
                                <th scope="col">Loan Number</th>
                                <th scope="col">Payment Date</th>
                                <th scope="col">Payment Reference Number</th>
                                <th scope="col">Total Payoff Amount</th>
                                <th scope="col">Payement Advice Attachemnt</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php $__currentLoopData = $payment_datails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                        <th scope="row"><?php echo e($loop->index + 1); ?>.</th>
                            <td><?php echo e($p->FSP_REFERENCE_NUMBER ?? '-'); ?></td>
                            <td><?php echo e($p->LOAN_NUMBER ?? '-'); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($p->PAYMENT_DATE)->format('Y-m-d H:i') ?? '-'); ?></td>
                            <td><?php echo e($p->PAYMENT_REFERENCE_NUMBER ?? '-'); ?></td>
                            <td><?php echo e(number_format($p->TOTAL_PAYOFF_AMOUNT, 2)); ?></td>
                            <td>
                                <?php if(!empty($p->PAYMENT_ADVICE_ATTACHMENT)): ?>
                                    <button 
                                        class="btn btn-outline-primary btn-xs"
                                        data-bs-toggle="modal"
                                        data-bs-target="#pdfPreviewModal"
                                        onclick="showPDFInModal(`<?php echo e($p->PAYMENT_ADVICE_ATTACHMENT); ?>`)">
                                        Preview
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">No Attachment</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>

                        <?php else: ?> 
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
								No Record Found yet
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                       	</div>
                        <?php endif; ?>
					</div>
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </div>



  <?php $__env->startPush('scripts'); ?>
  <script>
    function showPDFInModal(base64) {
        const pdfViewer = document.getElementById('pdfViewer');
        pdfViewer.src = `data:application/pdf;base64,${base64}`;
    }
</script>

  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<!-- PDF Preview Modal -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pdfPreviewModalLabel">Payment Advice Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="height: 80vh;">
        <iframe id="pdfViewer" src="" width="100%" height="100%" style="border: none;"></iframe>
      </div>
    </div>
  </div>
</div>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/UPDATED PROJECTS/EXTERNAL/MCB ESS/FRONT END/ESS-MCB-FRONTEND/resources/views/admin/loan_take_over/payment_balance_request.blade.php ENDPATH**/ ?>