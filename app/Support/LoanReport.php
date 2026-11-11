<?php

namespace App\Support;

use App\Models\LOANAPPLICATION;
use App\Models\PAYMENT;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Loan reports (Reports > Customise Report and the Restructuring / Takeover / Top-up reports).
 * One place for the search filters, the columns and the Excel rows, so the screen and the export always match.
 */
class LoanReport
{
    /** report key => [title, LOAN_TYPE (null = every loan), short description] */
    public const TYPES = [
        'all'           => ['All Loans Report', null, 'Every loan application received from ESS Utumishi.'],
        'new'           => ['New Loans Report', 'NEW_LOAN', 'New loans (LOAN_OFFER_REQUEST), including older loans saved before the loan type was recorded.'],
        'restructuring' => ['Restructuring Report', 'RESTRUCTURING', 'Loans re-spread over a new tenure or instalment (LOAN_RESTRUCTURE_REQUEST).'],
        'takeover'      => ['Takeover Report', 'TAKEOVER', 'Loans MCB buys from another bank (LOAN_TAKEOVER_OFFER_REQUEST) with the transfer to FSP1.'],
        'topup'         => ['Top-up Report', 'TOP_UP', 'Top-ups on existing MCB loans (TOP_UP_OFFER_REQUEST) with the settled balance.'],
    ];

    /** The search options kept from the original report: search by one field, or by date range / status */
    public const SEARCH_BY = [
        'id_number'    => 'Application No.',
        'loan_id'      => 'Loan ID',
        'phone_number' => 'Phone No',
        'name'         => 'Name',
        'date_wise'    => 'Date Wise',
        'status'       => 'Status',
    ];

    private const FILTER_KEYS = ['start_date', 'end_date', 'status', 'phone', 'loan_id', 'id_number', 'cname'];

    public static function exists(string $type): bool
    {
        return array_key_exists($type, self::TYPES);
    }

    public static function title(string $type): string
    {
        return self::TYPES[$type][0];
    }

    public static function loanType(string $type): ?string
    {
        return self::TYPES[$type][1];
    }

    /** Filters from the search form (same field names as before: id_number, loan_id, phone_number, cname, status, dates). */
    public static function filtersFrom(Request $request): array
    {
        $f = [
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
            'status'     => $request->input('status'),
            'phone'      => $request->input('phone_number', $request->input('phone')),
            'loan_id'    => $request->input('loan_id'),
            'id_number'  => $request->input('id_number'),
            'cname'      => $request->input('cname'),
        ];
        return array_map(fn ($v) => is_string($v) ? trim($v) : $v, $f);
    }

    public static function hasFilters(array $f): bool
    {
        foreach (self::FILTER_KEYS as $k) {
            if (!empty($f[$k])) {
                return true;
            }
        }
        return false;
    }

    /** Query string to carry the filters on pagination and on the Excel link. */
    public static function queryString(array $f): array
    {
        $q = [];
        foreach ($f as $k => $v) {
            if (!empty($v)) {
                $q[$k === 'phone' ? 'phone_number' : $k] = $v;
            }
        }
        return $q;
    }

    /** Which "Search by" option the filters correspond to (to re-open the form as it was). */
    public static function searchBy(array $f): string
    {
        foreach (['id_number' => 'id_number', 'loan_id' => 'loan_id', 'phone' => 'phone_number', 'cname' => 'name', 'status' => 'status'] as $k => $by) {
            if (!empty($f[$k])) {
                return $by;
            }
        }
        return !empty($f['start_date']) ? 'date_wise' : 'id_number';
    }

    /** Limits a query to the report's loan type. New loans also include old rows saved without a LOAN_TYPE. */
    private static function applyType(Builder $q, string $type): Builder
    {
        $loanType = self::loanType($type);
        if ($type === 'new') {
            $q->where(fn ($w) => $w->where('LOAN_TYPE', $loanType)->orWhereNull('LOAN_TYPE')->orWhere('LOAN_TYPE', ''));
        } elseif ($loanType) {
            $q->where('LOAN_TYPE', $loanType);
        }
        return $q;
    }

    public static function query(string $type, array $f): Builder
    {
        $q = self::applyType(LOANAPPLICATION::query(), $type);
        // Branch Operations only see their own branch, on screen and in Excel
        $user = Auth::user();
        if ($user && $user->role == 'Branch Operations') {
            $q->where('INIT_BRANCH', $user->branch_id);
        }

        if (!empty($f['status']))    { $q->where('LOAN_STATUS', $f['status']); }
        if (!empty($f['phone']))     { $q->where('MSISDN', $f['phone']); }
        if (!empty($f['loan_id']))   { $q->where('LOAN_ID', $f['loan_id']); }
        if (!empty($f['id_number'])) { $q->where('APPLICATION_NUMBER', $f['id_number']); }
        if (!empty($f['cname'])) {
            $names = preg_split('/\s+/', $f['cname']);
            $q->where(function ($w) use ($names) {
                foreach ($names as $name) {
                    $w->orWhere('FIRST_NAME', 'like', "%$name%")
                      ->orWhere('MIDDLE_NAME', 'like', "%$name%")
                      ->orWhere('LAST_NAME', 'like', "%$name%");
                }
            });
        }
        if (!empty($f['start_date']) && !empty($f['end_date'])) {
            $q->whereBetween('APPLICATION_DATE', [$f['start_date'], date('Y-m-d', strtotime($f['end_date'])) . ' 23:59:59']);
        }

        return $q->orderBy('ID', 'desc');
    }

    /** Loan statuses for the Status dropdown (for the report's loan type only). */
    public static function statuses(string $type)
    {
        $q = self::applyType(LOANAPPLICATION::query()->whereNotNull('LOAN_STATUS')->where('LOAN_STATUS', '<>', ''), $type);
        return $q->select('LOAN_STATUS')->distinct()->orderBy('LOAN_STATUS')->pluck('LOAN_STATUS');
    }

    /** Summary cards: records, total amount, total instalment and the count per loan status. */
    public static function summary(Builder $query): array
    {
        $base = (clone $query)->reorder();
        $totals = (clone $base)->selectRaw('COUNT(*) AS n, COALESCE(SUM(REQUESTED_AMOUNT),0) AS amount, COALESCE(SUM(MONTHLY_DEDUCTION),0) AS instalment, COALESCE(SUM(PROCESSING_FEE),0) AS fee')->first();
        $byStatus = (clone $base)->selectRaw("COALESCE(NULLIF(LOAN_STATUS,''),'Not set') AS s, COUNT(*) AS n")->groupBy('s')->orderByDesc('n')->pluck('n', 's');

        return [
            'count'      => (int) ($totals->n ?? 0),
            'amount'     => (float) ($totals->amount ?? 0),
            'instalment' => (float) ($totals->instalment ?? 0),
            'fee'        => (float) ($totals->fee ?? 0),
            'byStatus'   => $byStatus,
        ];
    }

    /** Takeover payments (PAYMENT, MCB as FSP2) for the given loans, keyed by loan ID. */
    public static function takeoverPayments($loans): array
    {
        if (!PAYMENT::takeoverReady()) {
            return [];
        }
        $ids = collect($loans)->pluck('ID')->filter()->all();
        if (!$ids) {
            return [];
        }
        $map = [];
        foreach (PAYMENT::whereIn('LOAN_APPLICATION_ID', $ids)->orderBy('ID')->get() as $p) {
            $map[$p->LOAN_APPLICATION_ID] = $p; // newest wins
        }
        return $map;
    }

    private static function money($v): string
    {
        return number_format((float) $v, 2, '.', ',');
    }

    private static function dt($v, string $format = 'd M Y H:i:s'): string
    {
        if (empty($v)) {
            return '';
        }
        try {
            return ($v instanceof \DateTimeInterface ? Carbon::instance($v) : Carbon::parse($v))->format($format);
        } catch (\Throwable $e) {
            return (string) $v;
        }
    }

    /**
     * Excel columns: header => value(loan, takeover payment).
     * The All Loans columns are exactly the ones exported before; the other reports add their own columns after them.
     */
    public static function columns(string $type): array
    {
        $cols = [
            'Customer name'          => fn ($l) => trim(strtoupper($l->FIRST_NAME) . '  ' . strtoupper($l->MIDDLE_NAME) . ' ' . strtoupper($l->LAST_NAME)),
            'Loan amount'            => fn ($l) => self::money($l->REQUESTED_AMOUNT),
            'Loan Account'           => fn ($l) => $l->DISBURSEMENT_RECEIPT,
            'Period'                 => fn ($l) => $l->TENURE,
            'Bank Branch'            => fn ($l) => $l->INIT_BRANCH,
            'Region'                 => fn ($l) => '',
            'District'               => fn ($l) => '',
            'Loan Purpose'           => fn ($l) => $l->LOAN_PURPOSE,
            'Application Date'       => fn ($l) => self::dt($l->APPLICATION_DATE),
            'Disbursement date'      => fn ($l) => self::dt($l->LOAN_DISBURSED_DATE),
            'Application Number'     => fn ($l) => $l->APPLICATION_NUMBER,
            'Deposit Account number' => fn ($l) => $l->BANK_ACCOUNT_NUMBER,
            'Phone number'           => fn ($l) => $l->MSISDN,
            'Vote number'            => fn ($l) => $l->VOTE_CODE,
            'Vote name'              => fn ($l) => $l->VOTE_NAME,
            'Deductible Amount'      => fn ($l) => self::money($l->MONTHLY_DEDUCTION),
            'Loan processing fee'    => fn ($l) => self::money($l->PROCESSING_FEE),
            'Insurance'              => fn ($l) => self::money($l->INSURANCE),
            'Loan Status'            => fn ($l) => $l->LOAN_STATUS,
        ];

        if ($type === 'new') {
            $cols += [
                'Loan ID'                => fn ($l) => $l->LOAN_ID,
                'Product Code'           => fn ($l) => $l->PRODUCT_CODE,
                'Interest Rate (%)'      => fn ($l) => $l->PRODUCT_INTEREST_RATE,
                'Total Amount To Pay'    => fn ($l) => self::money($l->TOTAL_AMOUNT_TO_PAY),
                'Approved Date'          => fn ($l) => self::dt($l->APPROVED_DATE),
                'Transaction Status'     => fn ($l) => $l->TRANSACTION_STATUS,
            ];
        } elseif ($type === 'restructuring') {
            $cols += [
                'Loan ID'                => fn ($l) => $l->LOAN_ID,
                'Original Loan ID'       => fn ($l) => $l->ORIGINAL_LOAN_ID,
                'Balance Restructured'   => fn ($l) => self::money($l->SETTLEMENT_AMOUNT),
                'New Instalment'         => fn ($l) => self::money($l->MONTHLY_DEDUCTION),
                'New Tenure (months)'    => fn ($l) => $l->TENURE,
                'Total Amount To Pay'    => fn ($l) => self::money($l->TOTAL_AMOUNT_TO_PAY),
                'First Instalment Date'  => fn ($l) => self::dt($l->INSTALMENT_START_DATE, 'd M Y'),
                'Maturity Date'          => fn ($l) => self::dt($l->MATURITY_DATE, 'd M Y'),
                'Transaction Status'     => fn ($l) => $l->TRANSACTION_STATUS,
            ];
        } elseif ($type === 'topup') {
            $cols += [
                'Loan ID'                => fn ($l) => $l->LOAN_ID,
                'Original Loan ID'       => fn ($l) => $l->ORIGINAL_LOAN_ID,
                'Settlement Amount'      => fn ($l) => self::money($l->SETTLEMENT_AMOUNT),
                'Top-up Above Settlement'=> fn ($l) => self::money((float) $l->REQUESTED_AMOUNT - (float) $l->SETTLEMENT_AMOUNT),
                'Total Amount To Pay'    => fn ($l) => self::money($l->TOTAL_AMOUNT_TO_PAY),
                'Transaction Status'     => fn ($l) => $l->TRANSACTION_STATUS,
            ];
        } elseif ($type === 'takeover') {
            $cols += [
                'Loan ID'                => fn ($l) => $l->LOAN_ID,
                'FSP1 Code'              => fn ($l, $p) => $p->FSP1_CODE ?? '',
                'FSP1 Loan Number'       => fn ($l, $p) => $p->FSP1_LOAN_NUMBER ?? '',
                'Takeover Amount'        => fn ($l, $p) => $p ? self::money($p->TAKE_OVER_AMOUNT) : '',
                'FSP1 Account'           => fn ($l, $p) => trim(($p->FSP1_BANK_ACCOUNT ?? '') . ' ' . ($p->FSP1_BANK_ACCOUNT_NAME ?? '')),
                'FSP1 Payment Reference' => fn ($l, $p) => $p->FSP1_PAYMENT_REFERENCE_NUMBER ?? '',
                'Transfer Reference'     => fn ($l, $p) => $p->CBS_REFERENCE ?? '',
                'Amount Transferred'     => fn ($l, $p) => ($p && $p->PAID_AMOUNT !== null) ? self::money($p->PAID_AMOUNT) : '',
                'Transferred On'         => fn ($l, $p) => self::dt($p->PAID_DATE ?? null),
                'Transfer Status'        => fn ($l, $p) => $p ? ($p->CBS_REFERENCE ? 'Transferred' : 'Not transferred') : 'No payment record',
                'Transaction Status'     => fn ($l) => $l->TRANSACTION_STATUS,
            ];
        }

        return $cols;
    }

    /** Excel file name, e.g. Restructuring-Report-2026-11-11.xlsx (the All Loans file keeps its old name). */
    public static function fileName(string $type): string
    {
        return $type === 'all' ? 'All-Loans.xlsx' : str_replace(' ', '-', self::title($type)) . '-' . date('Y-m-d') . '.xlsx';
    }
}
