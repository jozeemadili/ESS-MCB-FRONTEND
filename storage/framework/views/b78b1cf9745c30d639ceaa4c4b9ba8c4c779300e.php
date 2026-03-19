<?php $__env->startSection('title'); ?>
<?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/date-picker.css')); ?>">
<?php $__env->stopPush(); ?>
<?php
use Carbon\Carbon;
?>
<?php $__env->startSection('content'); ?>
  <?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('breadcrumb_title'); ?>
      <h3><?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?></h3>
     
    <?php $__env->endSlot(); ?>
    
    <?php $__env->slot('breadcrumb_action_buttons'); ?>
    <?php if(Route::currentRouteName()=='loans-disbursed'): ?>
    <li>   <a  class="btn btn-outline-secondary" href='<?php echo e(route('disbursed-reports-download-Excel')); ?>'>Download Excel Disbursed</a></li>
    <?php elseif(Route::currentRouteName()=='loans-approved'): ?>
    <li>   <a  class="btn btn-outline-secondary" href='<?php echo e(route('approved-reports-download-Excel')); ?>'>Download Excel Approved</a></li>
    <?php elseif(Route::currentRouteName()=='loans-approved-cm'): ?>
    <li>   <a  class="btn btn-outline-secondary" href='<?php echo e(route('cm-reports-download-Excel')); ?>'>Download Excel CM list</a></li>
    <?php elseif(Route::currentRouteName()=='loans-approved-ca'): ?>
    <li>   <a  class="btn btn-outline-secondary" href='<?php echo e(route('ca-reports-download-Excel')); ?>'>Download Excel CA list</a></li>
    <?php elseif(Route::currentRouteName()=='loans-pending'): ?>
    <li>   <a  class="btn btn-outline-secondary" href='<?php echo e(route('qa-reports-download-Excel')); ?>'>Download Excel QA list</a></li>
    <?php elseif(Route::currentRouteName()=='loans-accepted'): ?>
    <li>   <a  class="btn btn-outline-secondary" href='<?php echo e(route('accepted-reports-download-Excel')); ?>'>Download Accepted By Bank</a></li>
    <?php elseif(Route::currentRouteName()=='loans-rejected'): ?>
    <li>   <a  class="btn btn-outline-secondary" href='<?php echo e(route('rejected-reports-download-Excel')); ?>'>Download Rejected Loans</a></li>
    <?php elseif(Route::currentRouteName()=='loans-cancelled'): ?>
    <li>   <a  class="btn btn-outline-secondary" href='<?php echo e(route('cancelled-reports-download-Excel')); ?>'>Download Cancelled Loans</a></li>
    <?php elseif(Route::currentRouteName()=='posted-cbs'): ?>
    <li>   <a  class="btn btn-outline-secondary" href='<?php echo e(route('posted-cbs-download-Excel')); ?>'>Download Posted To CBS</a></li>
   
    <?php else: ?>
    <li>   <a  class="btn btn-outline-secondary" href='<?php echo e(route('policies-reports-download-Excel')); ?>'>Download Excel All Loans</a></li>
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Search <i class="icofont icofont-search-alt-1"></i></button></li>

    <?php endif; ?>

    <!-- <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li> -->
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
                       
                      
						<table class="table table-xs">
							<thead>
								<tr>
                               
                                <th scope="col">#</th>
        
									<th scope="col">Names</th>
                                    <th scope="col">Application Date</th>
                                    <th scope="col">APPLICATION NUMBER</th>
                                    <th scope="col">mobile</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Loan ID</th>
                                    <th scope="col">transaction Status</th>
                                    
                                    <th scope="col">loan Status</th> 
                                    <th scope="col">loan Type</th> 
                                    <th scope="col">proccesing Fee</th> 
                                    <th scope="col">Approved Date</th> 
								</tr>
							</thead>
							<tbody>
                          
                            <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<th scope="row"><?php echo e($loop->index + 1); ?>.</th>
									<td><a href="<?php echo e(Route('staff-profile', ['id' => $loan->ID])); ?>"><small><?php echo e(strtoupper($loan['FIRST_NAME'])); ?>  <?php echo e(strtoupper($loan['MIDDLE_NAME'])); ?> <?php echo e(strtoupper($loan['LAST_NAME'])); ?></small></a></td>
                                    <?php if(isset($loan['APPLICATION_DATE'])): ?>
                                    <td><?php echo e($loan['APPLICATION_DATE']->format('d M Y H:i:s')); ?> | <i><?php echo e(Carbon::parse($loan['APPLICATION_DATE'])->diffForHumans()); ?></i></td>   
                                    <?php else: ?>
                                    <td></td>
                                    <?php endif; ?>
                                    <td><?php echo e($loan['APPLICATION_NUMBER']); ?></td>
                                    <td><?php echo e($loan['MSISDN']); ?></td>
                                    <td><?php echo e(number_format($loan['REQUESTED_AMOUNT'], 2,'.',',')); ?></td>
                                    <td><?php echo e($loan['LOAN_ID']); ?></td>
                                    <td>
                                    <?php if($loan['TRANSACTION_STATUS'] === 'Approved'): ?>
                                    <span class="badge badge-primary rounded-pill"><?php echo e($loan['TRANSACTION_STATUS']); ?></span> | 
                                    <?php if(isset($loan['APPROVED_DATE'])): ?>
                                    <?php echo e($loan['APPROVED_DATE']->format('d M Y H:i:s')); ?>

                                    <?php else: ?>
                                    Not Set
                                    <?php endif; ?>
                                    <?php elseif($loan['TRANSACTION_STATUS'] === 'Disbursed'): ?>
                                    <span class="badge badge-info rounded-pill"><?php echo e($loan['TRANSACTION_STATUS']); ?></span>
                                    <?php elseif($loan['TRANSACTION_STATUS'] === 'Pending'): ?>
                                    <span class="badge badge-secondary rounded-pill"><?php echo e($loan['TRANSACTION_STATUS']); ?></span>
                                    <?php else: ?>
                                    <span class="badge badge-danger rounded-pill"><?php echo e($loan['TRANSACTION_STATUS']); ?></span>
                                    <?php endif; ?>
                                    </td>
                                    <td><?php echo e($loan['LOAN_STATUS']); ?></td>
                                    <td><?php echo e($loan['LOAN_TYPE']); ?></td>
                                    <td><?php echo e(number_format($loan['PROCESSING_FEE'], 2,'.',',')); ?></td>
                                 
                                    
                                    <td> <?php if(isset($loan['APPROVED_DATE'])): ?>
                                    <?php echo e($loan['APPROVED_DATE']->format('d M Y H:i:s')); ?>

                                    <?php else: ?>
                                    Not Set
                                    <?php endif; ?></td>
                                   
                                          
								</tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
<br/>

                        <?php echo e($loans->links()); ?>

                        </div>
                        
					</div>
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </div>

 <!-- NEW MODAL START -->
 <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Branch Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="v1/intermediary/branches/add">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label" >Branch Name</label>
                                <input class="form-control" type="text" value="<?php echo e(old('name')); ?>" required  name="name">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Branch Type</label><br>
                                <input type="radio" checked class="radio_animated" value="Branch" name="type"> Branch
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="radio_animated" value="Head Office" name="type"> Head Office
                            </div>
                            <div class="form-group">
                                <div class="form-group">
                                    <label class="col-form-label" >Branch Street</label>
                                    <textarea class="form-control" type="text" value="<?php echo e(old('street')); ?>" required  name="street"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                           
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Register</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- NEW MODAL END -->

  <!-- SEARCH MODAL START -->
  <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true" style="display: none;">
 
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Search Applicants</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <!-- <form method="post" action="aplications/search"> -->
                <form method="post" action="<?php echo e(url()->current()); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="col-form-label">Search By</label><br>
                            <input type="radio" checked class="radio_animated" value="id_number" name="search_by" id="search_by" onChange="searchBy(this)"> Application No.
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio_animated" value="loan_id" name="search_by" id="search_by" onChange="searchBy(this)"> Loan ID.
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio_animated" value="phone_number" name="search_by" id="search_by" onChange="searchBy(this)"> Phone No.
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio_animated" value="name" name="search_by" id="search_by" onChange="searchBy(this)"> Name
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio_animated" value="date_wise" name="search_by" id="search_by" onChange="searchBy(this)"> Date wise
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                        <div class="col-lg-12" id="start_date_container" style="display: none;">
                        <label class="col-form-label">Start Date</label><br>
                        </div>
                            <input class="form-control" type="text" value="<?php echo e(old('reference_number')); ?>" maxlength="20" required id="reference_number">
                        </div>
                    </div>
                    <div class="col-lg-12" id="end_date_container" style="display: none;">
                        <div class="form-group">
                        <label class="col-form-label">End Date</label><br>
                            <input class="form-control" type="date" id="end_date" placeholder="Enter End Date" name="end_date">
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Search</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

 <?php $__env->startPush('scripts'); ?>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.en.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.custom.js')); ?>"></script>
  <script>
    document.getElementById("reference_number").placeholder = "Enter user's ID Number ...";
    document.getElementById('reference_number').name = 'id_number';

    function searchBy(search_by) {
        // Hide the end date container by default
        document.getElementById("end_date_container").style.display = "none";
        document.getElementById("start_date_container").style.display = "none";
       
        
        document.getElementById("end_date").required = false;  // Make the end date optional unless needed

        if (search_by.value == "id_number") {
            document.getElementById("reference_number").type = "text";
            document.getElementById("reference_number").placeholder = "Enter Loan Application Number ...";
            document.getElementById('reference_number').name = 'id_number';
        } else if (search_by.value == "loan_id") {
            document.getElementById("reference_number").type = "text";
            document.getElementById("reference_number").placeholder = "Enter Loan ID (e.g MCB ...";
            document.getElementById('reference_number').name = 'loan_id';
        } else if (search_by.value == "phone_number") {
            document.getElementById("reference_number").maxlength = "10";
            document.getElementById("reference_number").type = "number";
            document.getElementById("reference_number").placeholder = "Enter user's Phone (e.g. 0745821080) ...";
            document.getElementById('reference_number').name = 'phone_number';
        } else if (search_by.value == "date_wise") {
            document.getElementById("reference_number").type = "date";
            document.getElementById("reference_number").placeholder = "Enter Start Date ...";
            document.getElementById('reference_number').name = 'start_date';

            // Show the end date input field
            document.getElementById("end_date_container").style.display = "block";
            document.getElementById("start_date_container").style.display = "block";
            
            document.getElementById("end_date").required = true;  // Make the end date required
        } else {
            document.getElementById("reference_number").type = "text";
            document.getElementById("reference_number").placeholder = "Enter user's First Name or Middle Name or Last Name ...";
            document.getElementById('reference_number').name = 'cname';
        }
}

 </script>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/intermediaries/branches.blade.php ENDPATH**/ ?>