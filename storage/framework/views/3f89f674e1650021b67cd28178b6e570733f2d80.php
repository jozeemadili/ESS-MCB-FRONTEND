<table>
    <thead>
        <tr>
        <th>#</th>

        <th> Customer name</th>
        <th> Loan amount</th>
        <th> Loan Account</th>
        <th> Period</th>
        <th> Bank Branch</th>
        <th> Region</th>
        <th> District</th>
        <th> Loan Purpose</th>
        <th> Application Date</th>
        <th> Disbursement date</th>
        <th> Application Number</th>
        <th> Deposit Account number</th>
        <th> Phone number</th>
        <th> Vote number</th>
        <th> Vote name</th>
        <th> Deductible Amount</th>
        <th> Loan processing fee</th>
        <th> Insurance</th>
        <th> Loan Status</th>
    </tr>
    </thead>
    <tbody>
    
    <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <th scope="row"><?php echo e($loop->index + 1); ?>.</th>
            <td><?php echo e(strtoupper($loan['FIRST_NAME'])); ?>  <?php echo e(strtoupper($loan['MIDDLE_NAME'])); ?> <?php echo e(strtoupper($loan['LAST_NAME'])); ?></td>
            <td><?php echo e(number_format($loan['REQUESTED_AMOUNT'], 2,'.',',')); ?></td>
            <td><?php echo e($loan['DISBURSEMENT_RECEIPT']); ?></td>
            <td><?php echo e($loan['TENURE']); ?></td>
            <td><?php echo e($loan['INIT_BRANCH']); ?></td>
            <td></td>
            <td></td>
            <td><?php echo e($loan['LOAN_PURPOSE']); ?></td>
            <?php if(isset($loan['APPLICATION_DATE'])): ?>
            <td><?php echo e($loan['APPLICATION_DATE']->format('d M Y H:i:s')); ?></td>   
            <?php else: ?>
            <td></td>
            <?php endif; ?>
            <?php if(isset($loan['LOAN_DISBURSED_DATE'])): ?>
            <td><?php echo e($loan['LOAN_DISBURSED_DATE']->format('d M Y H:i:s')); ?></td>   
            <?php else: ?>
            <td></td>
            <?php endif; ?>
            <td><?php echo e($loan['APPLICATION_NUMBER']); ?></td>
            <td><?php echo e($loan['BANK_ACCOUNT_NUMBER']); ?></td>
            <td><?php echo e($loan['MSISDN']); ?></td>
            <td><?php echo e($loan['VOTE_CODE']); ?></td>
            <td><?php echo e($loan['VOTE_NAME']); ?></td>
            <td><?php echo e(number_format($loan['MONTHLY_DEDUCTION'], 2,'.',',')); ?></td>
            <td><?php echo e(number_format($loan['PROCESSING_FEE'], 2,'.',',')); ?></td>
            <td><?php echo e(number_format($loan['INSURANCE'], 2,'.',',')); ?></td>
            <td><?php echo e($loan['LOAN_STATUS']); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<?php /**PATH /var/www/html/laravel/resources/views/admin/intermediaries/branches_excel.blade.php ENDPATH**/ ?>