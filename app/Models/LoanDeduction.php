<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Deduction stop received from ESS (DEDUCTION_STOP_NOTIFICATION, API doc 2.7.3), saved by the ESS gateway.
 *
 * STATUS: RECEIVED (loan found and its LOAN_STATUS set to "Deduction Stopped") or LOAN_NOT_FOUND.
 * A stop is reviewed by MCB staff when REVIEWED_DATE is set.
 */
class LoanDeduction extends Model
{
    protected $table = 'LOAN_DEDUCTIONS';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $casts = [
        'BALANCE_AMOUNT'      => 'float',
        'DEDUCTION_AMOUNT'    => 'float',
        'LOAN_APPLICATION_ID' => 'int',
    ];

    protected $dates = ['RECEIVED_DATE', 'REVIEWED_DATE'];

    protected $fillable = ['REVIEWED_BY', 'REVIEWED_DATE', 'REVIEW_REMARKS'];

    public function loan()
    {
        return $this->belongsTo(LOANAPPLICATION::class, 'LOAN_APPLICATION_ID', 'ID');
    }

    /** Stops not yet reviewed, scoped to the user's branch for Branch Operations. */
    public static function pendingReviewCount($user = null): int
    {
        if (!self::tableReady()) {
            return 0;
        }
        $q = self::whereNull('REVIEWED_DATE');
        if ($user && $user->role == 'Branch Operations') {
            $q->whereHas('loan', fn ($l) => $l->where('INIT_BRANCH', $user->branch_id));
        }
        return $q->count();
    }

    /** The table and its review columns are created by the ESS gateway; until it is updated the portal hides this feature. */
    public static function tableReady(): bool
    {
        static $ready = null;
        if ($ready === null) {
            try {
                $ready = Schema::hasTable('LOAN_DEDUCTIONS') && Schema::hasColumn('LOAN_DEDUCTIONS', 'REVIEWED_DATE');
            } catch (\Throwable $e) {
                $ready = false;
            }
        }
        return $ready;
    }
}
