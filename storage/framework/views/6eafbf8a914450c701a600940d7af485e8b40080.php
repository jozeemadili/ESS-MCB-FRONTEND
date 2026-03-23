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
                                <th scope="col">CUSTOMER_NAME</th>
                                <th scope="col">FSP BANK ACCOUNT</th>
                                <th scope="col">FSP BANK ACCOUNT NAME</th>
                                <th scope="col">FSP LOAN NUMBER</th>
                                <th scope="col">LOAN ID</th>
                                <th scope="col">ATTACHMENT</th>
                                <th scope="col">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php $__currentLoopData = $payment_datails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                        <!-- SELECT `ID`, ``, ``, `FSP_CODE`, `FSP_FINAL_PAYMENT_DATE`, ``, `FSP_PAYMENT_REFERENCE_NUMBER`, `CURRENCY`, `CREATED_AT`, `CUST_ACC_BRN`, `CUST_ACC_NUMBER`, `DATE_PROCESSED`, `FSP_END_DATE`, `FSP_NAME`, `SWIFT_CODE`, `FUNDING`, ``, `NARRATION`, `STATUS`, `TAKE_OVER_AMOUNT`, `TAKE_OVER_DATE`, `TAKE_OVER_ID`, `TYPE`, `ADDRESS`, ``, `MT_MESSAGE`, `RECEIPT_NUMBER`, `REMMITANCE_INFO`, `APPLICATION_NUMBER`, `` FROM `PAYMENT` WHERE 1 -->
                        <th scope="row"><?php echo e($loop->index + 1); ?>.</th>
                            <td><?php echo e($p->CUSTOMER_NAME ?? '-'); ?></td>
                            <td><?php echo e($p->FSP_BANK_ACCOUNT ?? '-'); ?></td>
                            <td><?php echo e($p->FSP_BANK_ACCOUNT_NAME ?? '-'); ?></td>
                            <td><?php echo e($p->FSP_LOAN_NUMBER ?? '-'); ?></td>
                            <td><?php echo e($p->LOAN_ID ?? '-'); ?></td>

                            <td>
                                <?php if(!empty($p->MT_MESSAGE_BASE64)): ?>
                                    <button 
                                        class="btn btn-outline-primary btn-xs"
                                        data-bs-toggle="modal"
                                        data-bs-target="#pdfPreviewModal"
                                        onclick="showPDFInModal(`<?php echo e($p->MT_MESSAGE_BASE64); ?>`)">
                                        Preview
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">No Attachment</span>
                                <?php endif; ?>
                            </td>
                            <td>
                            <button class="btn btn-outline-info btn-xs" data-bs-toggle="modal" data-bs-target="#approvalModal<?php echo e($p->ID); ?>">More Details</button>
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

 <!-- NEW MODAL END -->
 <!-- <?php $__currentLoopData = $payment_datails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="approvalModal<?php echo e($p->ID); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Approval Stages for Request #<?php echo e($p->ID); ?></h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                   <?php echo e($p); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> -->
<?php $__currentLoopData = $payment_datails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="approvalModal<?php echo e($p->ID); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">More Details #<?php echo e($p->ID); ?></h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Payment ID</strong></td>
                                <td><?php echo e($p->ID); ?></td>
                            </tr>
                            <tr>
                                <td><strong>FSP Bank Account</strong></td>
                                <td><?php echo e($p->FSP_BANK_ACCOUNT); ?></td>
                            </tr>
                            <tr>
                                <td><strong>FSP Bank Account Name</strong></td>
                                <td><?php echo e($p->FSP_BANK_ACCOUNT_NAME); ?></td>
                            </tr>
                            <tr>
                                <td><strong>FSP Code</strong></td>
                                <td><?php echo e($p->FSP_CODE); ?></td>
                            </tr>
                            <tr>
                                <td><strong>FSP Loan Number</strong></td>
                                <td><?php echo e($p->FSP_LOAN_NUMBER); ?></td>
                            </tr>
                            <tr>
                                <td><strong>FSP Payment Reference Number</strong></td>
                                <td><?php echo e($p->FSP_PAYMENT_REFERENCE_NUMBER); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Currency</strong></td>
                                <td><?php echo e($p->CURRENCY); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Customer Account Number</strong></td>
                                <td><?php echo e($p->CUST_ACC_NUMBER); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Date Processed</strong></td>
                                <td><?php echo e($p->DATE_PROCESSED); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Loan ID</strong></td>
                                <td><?php echo e($p->LOAN_ID); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Narration</strong></td>
                                <td><?php echo e($p->NARRATION); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td><?php echo e($p->STATUS); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Takeover Amount</strong></td>
                                <td><?php echo e(number_format($p->TAKE_OVER_AMOUNT, 2)); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Takeover Date</strong></td>
                                <td><?php echo e($p->TAKE_OVER_DATE); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Customer Address</strong></td>
                                <td><?php echo e($p->ADDRESS); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Customer Name</strong></td>
                                <td><?php echo e($p->CUSTOMER_NAME); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Remittance Info</strong></td>
                                <td><?php echo e($p->REMMITANCE_INFO); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Application Number</strong></td>
                                <td><?php echo e($p->APPLICATION_NUMBER); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/UPDATED PROJECTS/EXTERNAL/MCB ESS/FRONT END/ESS-MCB-FRONTEND/resources/views/admin/loan_take_over/out_going_payment_request.blade.php ENDPATH**/ ?>