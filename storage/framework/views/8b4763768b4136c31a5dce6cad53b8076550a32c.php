<?php use Carbon\Carbon; ?>                      <style>
                        .lt-wrap{--lt-blue:#0d47a1;--lt-orange:#f28c28;--lt-ink:#1f2937;--lt-muted:#6b7280;--lt-line:#e5e9f2;--lt-head:#f4f7fc;}
                        .lt-toolbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;margin-bottom:14px;}
                        .lt-toolbar h5{margin:0;font-weight:700;color:var(--lt-ink);}
                        .lt-toolbar .lt-count{background:#e8effb;color:var(--lt-blue);border-radius:20px;padding:3px 12px;font-size:12px;font-weight:600;}
                        .lt-legend{font-size:11.5px;color:var(--lt-muted);}
                        .lt-legend b{color:var(--lt-ink);}
                        .lt-scroll{border:1px solid var(--lt-line);border-radius:10px;overflow:auto;max-height:72vh;}
                        .lt-table{width:100%;margin:0;border-collapse:separate;border-spacing:0;font-size:12.5px;color:var(--lt-ink);}
                        .lt-table thead th{position:sticky;top:0;z-index:2;background:var(--lt-head);color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:11px 12px;border-bottom:2px solid var(--lt-line);white-space:nowrap;}
                        .lt-table tbody td,.lt-table tbody th{padding:10px 12px;border-bottom:1px solid var(--lt-line);vertical-align:middle;white-space:nowrap;}
                        .lt-table tbody tr:nth-child(even){background:#fafbfd;}
                        .lt-table tbody tr:hover{background:#eef4ff;}
                        .lt-table .lt-num{color:var(--lt-muted);font-weight:600;}
                        .lt-table .lt-right{text-align:right;}
                        .lt-person{display:flex;align-items:center;gap:10px;}
                        .lt-avatar{flex:0 0 34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--lt-blue),#1976d2);color:#fff;font-weight:700;font-size:12px;display:flex;align-items:center;justify-content:center;}
                        .lt-person a{color:var(--lt-blue);font-weight:600;}
                        .lt-person a:hover{color:var(--lt-orange);}
                        .lt-sub{display:block;color:var(--lt-muted);font-size:11.5px;margin-top:2px;}
                        .lt-mono{font-family:SFMono-Regular,Menlo,Consolas,monospace;font-size:12px;}
                        .lt-date{font-weight:600;}
                        .lt-time{color:var(--lt-muted);font-weight:400;}
                        .lt-wait{display:inline-block;margin-top:3px;font-size:10.5px;padding:1px 7px;border-radius:10px;background:#fff4e8;color:#b45f06;}
                        .lt-amount{font-weight:700;font-variant-numeric:tabular-nums;}
                        .lt-amount small{color:var(--lt-muted);font-weight:500;margin-right:3px;}
                        .lt-pill{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;}
                        .lt-pill:before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor;}
                        .lt-pill.approved{background:#e3f2fd;color:#0d47a1;}
                        .lt-pill.disbursed{background:#e6f6ee;color:#137a43;}
                        .lt-pill.pending{background:#f1f3f6;color:#55606e;}
                        .lt-pill.other{background:#fdecec;color:#c62828;}
                        .lt-chip{display:inline-block;padding:2px 8px;border-radius:6px;background:#f1f5f9;color:#334155;font-size:11px;font-weight:600;}
                        .lt-chip.type{background:#fff4e8;color:#b45f06;}
                        .lt-muted{color:#9aa3af;font-style:italic;}
                        .lt-empty{text-align:center;padding:40px 10px !important;color:var(--lt-muted);}
                        .lt-pager{margin-top:14px;}
                      </style>

                      <?php
                        $tz = 'Africa/Dar_es_Salaam';
                        $isPaged = $loans instanceof \Illuminate\Pagination\AbstractPaginator;
                        $startNo = $isPaged ? (int) $loans->firstItem() : 1;
                        $total = $loans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $loans->total() : count($loans);
                      ?>

                      <div class="lt-wrap">
                        <div class="lt-toolbar">
                          <div class="d-flex align-items-center gap-2">
                            <h5>Loan Applications</h5>
                            <span class="lt-count"><?php echo e(number_format($total)); ?> record<?php echo e($total == 1 ? '' : 's'); ?></span>
                          </div>
                          <div class="lt-legend">
                            <b>Applied (ESS)</b> = when the employee applied on ESS Utumishi &nbsp;·&nbsp;
                            <b>Received at MCB</b> = when the application reached MCB
                          </div>
                        </div>

                        <div class="lt-scroll">
                          <table class="lt-table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Applicant</th>
                                <th>Application No. / Loan ID</th>
                                <th>Applied (ESS)</th>
                                <th>Received at MCB</th>
                                <th class="lt-right">Amount</th>
                                <th class="lt-right">Processing Fee</th>
                                <th>Loan Type</th>
                                <th>Loan Status</th>
                                <th>Transaction Status</th>
                                <th>Approved Date</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                              <?php
                                $fullName = trim(preg_replace('/\s+/', ' ', strtoupper(($loan['FIRST_NAME'] ?? '').' '.($loan['MIDDLE_NAME'] ?? '').' '.($loan['LAST_NAME'] ?? ''))));
                                $initials = strtoupper(substr($loan['FIRST_NAME'] ?? '', 0, 1).substr($loan['LAST_NAME'] ?? '', 0, 1));

                                // ESS does not send a separate "applied" date; ESS application numbers are "ESS" + the epoch time (ms) of the application
                                $appliedAt = null;
                                if (preg_match('/^ESS(\d{13})$/', (string) $loan['APPLICATION_NUMBER'], $m)) {
                                    $appliedAt = Carbon::createFromTimestampMs((int) $m[1])->setTimezone($tz);
                                }

                                // APPLICATION_DATE is stored by the gateway in local (Dar es Salaam) time when the request reaches MCB
                                $receivedAt = !empty($loan['APPLICATION_DATE'])
                                    ? Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($loan['APPLICATION_DATE'])->format('Y-m-d H:i:s'), $tz)
                                    : null;

                                $wait = null;
                                if ($appliedAt && $receivedAt && $receivedAt->gte($appliedAt)) {
                                    $mins = $appliedAt->diffInMinutes($receivedAt);
                                    $wait = $mins < 1 ? 'same minute' : ($mins < 60 ? $mins.' min later' : ($mins < 1440 ? floor($mins / 60).' hr later' : floor($mins / 1440).' day(s) later'));
                                }

                                $txStatus = $loan['TRANSACTION_STATUS'];
                                $pillClass = match ($txStatus) { 'Approved' => 'approved', 'Disbursed' => 'disbursed', 'Pending' => 'pending', default => 'other' };
                              ?>
                              <tr>
                                <th scope="row" class="lt-num"><?php echo e($startNo + $loop->index); ?></th>
                                <td>
                                  <div class="lt-person">
                                    <span class="lt-avatar"><?php echo e($initials ?: '?'); ?></span>
                                    <div>
                                      <a href="<?php echo e(Route('staff-profile', ['id' => $loan->ID])); ?>"><?php echo e($fullName); ?></a>
                                      <span class="lt-sub"><i class="fa fa-phone"></i> <?php echo e($loan['MSISDN'] ?: '—'); ?></span>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <span class="lt-mono"><?php echo e($loan['APPLICATION_NUMBER']); ?></span>
                                  <span class="lt-sub lt-mono"><?php echo e($loan['LOAN_ID'] ?: 'No loan ID yet'); ?></span>
                                </td>
                                <td>
                                  <?php if($appliedAt): ?>
                                    <span class="lt-date"><?php echo e($appliedAt->format('d M Y')); ?></span> <span class="lt-time"><?php echo e($appliedAt->format('H:i')); ?></span>
                                    <span class="lt-sub"><?php echo e($appliedAt->diffForHumans()); ?></span>
                                  <?php else: ?>
                                    <span class="lt-muted">Not provided</span>
                                  <?php endif; ?>
                                </td>
                                <td>
                                  <?php if($receivedAt): ?>
                                    <span class="lt-date"><?php echo e($receivedAt->format('d M Y')); ?></span> <span class="lt-time"><?php echo e($receivedAt->format('H:i:s')); ?></span>
                                    <span class="lt-sub"><?php echo e($receivedAt->diffForHumans()); ?></span>
                                    <?php if($wait): ?><span class="lt-wait" title="Time between applying on ESS and reaching MCB"><?php echo e($wait); ?></span><?php endif; ?>
                                  <?php else: ?>
                                    <span class="lt-muted">Not set</span>
                                  <?php endif; ?>
                                </td>
                                <td class="lt-right"><span class="lt-amount"><small>TZS</small><?php echo e(number_format((float) $loan['REQUESTED_AMOUNT'], 2, '.', ',')); ?></span></td>
                                <td class="lt-right"><span class="lt-amount"><small>TZS</small><?php echo e(number_format((float) $loan['PROCESSING_FEE'], 2, '.', ',')); ?></span></td>
                                <td><?php if($loan['LOAN_TYPE']): ?><span class="lt-chip type"><?php echo e($loan['LOAN_TYPE']); ?></span><?php else: ?><span class="lt-muted">—</span><?php endif; ?></td>
                                <td><?php if($loan['LOAN_STATUS']): ?><span class="lt-chip"><?php echo e($loan['LOAN_STATUS']); ?></span><?php else: ?><span class="lt-muted">—</span><?php endif; ?></td>
                                <td><span class="lt-pill <?php echo e($pillClass); ?>"><?php echo e($txStatus ?: 'Unknown'); ?></span></td>
                                <td>
                                  <?php if(!empty($loan['APPROVED_DATE'])): ?>
                                    <span class="lt-date"><?php echo e(Carbon::parse($loan['APPROVED_DATE'])->format('d M Y')); ?></span> <span class="lt-time"><?php echo e(Carbon::parse($loan['APPROVED_DATE'])->format('H:i:s')); ?></span>
                                  <?php else: ?>
                                    <span class="lt-muted">Not set</span>
                                  <?php endif; ?>
                                </td>
                              </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                              <tr><td colspan="11" class="lt-empty"><i class="fa fa-inbox fa-2x d-block mb-2"></i>No loan applications found.</td></tr>
                            <?php endif; ?>
                            </tbody>
                          </table>
                        </div>

                        <?php if($isPaged): ?>
                        <div class="lt-pager"><?php echo e($loans->links()); ?></div>
                        <?php endif; ?>
                      </div>
<?php /**PATH /Users/josephatwilliammadili/Desktop/UPDATED PROJECTS/EXTERNAL/MCB ESS/FRONT END/ESS-MCB-FRONTEND/storage/framework/views/10d143de3fbdf451bd0734cc72691acfeb266e2b.blade.php ENDPATH**/ ?>