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

    public const FILTERS = [
        'pending'  => 'Pending Review',
        'reviewed' => 'Reviewed',
        'notfound' => 'Loan Not Found',
        'all'      => 'All Stopped',
    ];

    /** Stops visible to the user: Branch Operations see their own branch only. */
    public static function visibleTo($user)
    {
        $q = self::query();
        if ($user && $user->role == 'Branch Operations') {
            $q->whereHas('loan', fn ($l) => $l->where('INIT_BRANCH', $user->branch_id));
        }
        return $q;
    }

    /** Applies one of FILTERS to a query. */
    public static function applyFilter($query, string $filter)
    {
        if ($filter === 'pending') {
            $query->whereNull('REVIEWED_DATE');
        } elseif ($filter === 'reviewed') {
            $query->whereNotNull('REVIEWED_DATE');
        } elseif ($filter === 'notfound') {
            $query->where('STATUS', 'LOAN_NOT_FOUND');
        }
        return $query;
    }

    /** Count per filter for the user (computed once per request; used by the menu and the page). */
    public static function counts($user = null): array
    {
        static $cache = [];
        $key = $user ? $user->id : 0;
        if (!isset($cache[$key])) {
            $cache[$key] = array_fill_keys(array_keys(self::FILTERS), 0);
            if (self::tableReady()) {
                foreach (array_keys(self::FILTERS) as $f) {
                    $cache[$key][$f] = self::applyFilter(self::visibleTo($user), $f)->count();
                }
            }
        }
        return $cache[$key];
    }

    public static function pendingReviewCount($user = null): int
    {
        return self::counts($user)['pending'];
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
