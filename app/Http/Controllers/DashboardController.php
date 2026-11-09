<?php

namespace App\Http\Controllers;

use App\Models\LOANAPPLICATION;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Portal dashboard: KPIs, the live work queue and the charts on /v1/dashboard.
 *
 * Only applications that carry a TRANSACTION_STATUS are counted (rows without one are the imported loan book).
 * Branch Operations users see their own branch (INIT_BRANCH) only, as on the loan lists.
 */
class DashboardController extends Controller
{
    private const TZ = 'Africa/Dar_es_Salaam';
    private const DISBURSED = ['Disbursed', 'Posted to cbs'];

    public const PERIODS = [
        '30d' => 'Last 30 days',
        '90d' => 'Last 90 days',
        'ytd' => 'This year',
        '12m' => 'Last 12 months',
        'all' => 'All time',
    ];

    public function index(Request $request)
    {
        $period = array_key_exists($request->query('period'), self::PERIODS) ? $request->query('period') : '12m';
        [$from, $to, $prevFrom, $prevTo] = $this->range($period);

        $branchOnly = Auth::user()->role == 'Branch Operations' ? Auth::user()->branch_id : null;

        // All applications with a status, scoped to the branch when needed
        $scoped = function () use ($branchOnly) {
            $q = LOANAPPLICATION::query()->whereNotNull('TRANSACTION_STATUS')->where('TRANSACTION_STATUS', '<>', '');
            if ($branchOnly) {
                $q->where('INIT_BRANCH', $branchOnly);
            }
            return $q;
        };
        $inRange = function ($q, $start, $end) {
            if ($start) {
                $q->where('APPLICATION_DATE', '>=', $start);
            }
            if ($end) {
                $q->where('APPLICATION_DATE', '<', $end);
            }
            return $q;
        };
        $current = fn () => $inRange($scoped(), $from, $to);

        // ---- KPIs for the selected period
        $totals = $current()->selectRaw('COUNT(*) apps, COALESCE(SUM(REQUESTED_AMOUNT),0) requested, COALESCE(AVG(REQUESTED_AMOUNT),0) avg_amount, COALESCE(AVG(TENURE),0) avg_tenure')->first();
        $disbursed = $current()->whereIn('TRANSACTION_STATUS', self::DISBURSED)
            ->selectRaw('COUNT(*) cnt, COALESCE(SUM(REQUESTED_AMOUNT),0) amount, COALESCE(SUM(PROCESSING_FEE),0) fees, COALESCE(SUM(TOTAL_AMOUNT_TO_PAY),0) repayable')->first();
        $byStatus = $current()->selectRaw('TRANSACTION_STATUS s, COUNT(*) c, COALESCE(SUM(REQUESTED_AMOUNT),0) a')->groupBy('s')->get();

        $statusCount = fn (array $names) => (int) $byStatus->filter(fn ($r) => in_array(strtolower($r->s), array_map('strtolower', $names)))->sum('c');
        $won = $statusCount(['Approved', 'Disbursed', 'Posted to cbs', 'Restructured']);
        $lost = $statusCount(['Rejected']) + (int) $current()->where('LOAN_STATUS', 'Cancelled')->whereNotIn('TRANSACTION_STATUS', ['Rejected'])->count();
        $approvalRate = ($won + $lost) > 0 ? round($won * 100 / ($won + $lost), 1) : null;

        $turnaroundHours = $current()->whereIn('TRANSACTION_STATUS', self::DISBURSED)
            ->whereNotNull('LOAN_DISBURSED_DATE')->whereColumn('LOAN_DISBURSED_DATE', '>=', 'APPLICATION_DATE')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, APPLICATION_DATE, LOAN_DISBURSED_DATE)) / 60 h')->value('h');

        $pipeline = $current()->whereIn('TRANSACTION_STATUS', ['Pending', 'Approved'])
            ->selectRaw('COUNT(*) cnt, COALESCE(SUM(REQUESTED_AMOUNT),0) amount')->first();

        // Change against the previous period of the same length
        $prev = null;
        if ($prevFrom) {
            $p = $inRange($scoped(), $prevFrom, $prevTo);
            $prevApps = (clone $p)->count();
            $prevDisb = (float) (clone $p)->whereIn('TRANSACTION_STATUS', self::DISBURSED)->sum('REQUESTED_AMOUNT');
            $prev = [
                'apps'      => $this->change($totals->apps, $prevApps),
                'disbursed' => $this->change($disbursed->amount, $prevDisb),
            ];
        }

        // ---- Live work queue (current state, not limited by period)
        $live = fn () => $scoped();
        $caWaiting = $live()->where('TRANSACTION_STATUS', 'Approved')
            ->whereNotExists(fn ($q) => $q->select(DB::raw(1))->from('disburment_approval_stages')->whereColumn('disburment_approval_stages.loan_id', 'LOAN_APPLICATION.ID'))->count();
        $copWaiting = $live()->where('TRANSACTION_STATUS', 'Approved')
            ->whereExists(fn ($q) => $q->select(DB::raw(1))->from('disburment_approval_stages')->whereColumn('disburment_approval_stages.loan_id', 'LOAN_APPLICATION.ID')->whereIn('disburment_approval_stages.status', ['INITIATED', 'INITIATED_REJECTION']))->count();
        $queue = [
            ['label' => 'Waiting CO',       'count' => $live()->where('TRANSACTION_STATUS', 'Pending')->where('LOAN_STATUS', 'Pending')->count(), 'route' => route('loans-pending'),     'icon' => 'inbox',        'tone' => 'warning'],
            ['label' => 'Accepted by Bank', 'count' => $live()->where('LOAN_STATUS', 'Accepted')->count(),                                         'route' => route('loans-accepted'),    'icon' => 'check-square', 'tone' => 'info'],
            ['label' => 'Waiting CA',       'count' => $caWaiting,                                                                                  'route' => route('loans-approved-ca'), 'icon' => 'user-check',   'tone' => 'warning'],
            ['label' => 'Waiting COP',      'count' => $copWaiting,                                                                                 'route' => route('loans-approved-cm'), 'icon' => 'shield',       'tone' => 'warning'],
            ['label' => 'Disbursed',        'count' => $live()->where('TRANSACTION_STATUS', 'Disbursed')->count(),                                  'route' => route('loans-disbursed'),   'icon' => 'send',         'tone' => 'success'],
            ['label' => 'Posted CBS',       'count' => $live()->where('TRANSACTION_STATUS', 'Posted to cbs')->count(),                              'route' => route('posted-cbs'),        'icon' => 'database',     'tone' => 'success'],
            ['label' => 'Restructuring',    'count' => $live()->where('LOAN_TYPE', 'RESTRUCTURING')->whereIn('TRANSACTION_STATUS', ['Pending', 'Approved'])->count(), 'route' => route('loans-restructuring', ['stage' => 'all']), 'icon' => 'repeat', 'tone' => 'info'],
            ['label' => 'Rejected',         'count' => $live()->where('TRANSACTION_STATUS', 'Rejected')->count(),                                   'route' => route('loans-rejected'),    'icon' => 'x-octagon',    'tone' => 'danger'],
        ];

        if (\App\Models\LoanDeduction::tableReady()) {
            $queue[] = ['label' => 'Deduction stopped', 'count' => \App\Models\LoanDeduction::pendingReviewCount(Auth::user()),
                        'route' => route('deductions-stopped', ['filter' => 'pending']), 'icon' => 'alert-octagon', 'tone' => 'danger'];
        }

        // Ageing of applications still waiting for the Credit Officer
        $now = Carbon::now(self::TZ);
        $waitingCo = fn () => $live()->where('TRANSACTION_STATUS', 'Pending')->where('LOAN_STATUS', 'Pending');
        $ageing = [
            'Under 24 hrs' => $waitingCo()->where('APPLICATION_DATE', '>=', $now->copy()->subDay())->count(),
            '1 – 3 days'   => $waitingCo()->where('APPLICATION_DATE', '<', $now->copy()->subDay())->where('APPLICATION_DATE', '>=', $now->copy()->subDays(3))->count(),
            '3 – 7 days'   => $waitingCo()->where('APPLICATION_DATE', '<', $now->copy()->subDays(3))->where('APPLICATION_DATE', '>=', $now->copy()->subDays(7))->count(),
            'Over 7 days'  => $waitingCo()->where('APPLICATION_DATE', '<', $now->copy()->subDays(7))->count(),
        ];
        $oldestPending = $waitingCo()->whereNotNull('APPLICATION_DATE')->orderBy('APPLICATION_DATE')->limit(6)
            ->get(['ID', 'FIRST_NAME', 'LAST_NAME', 'APPLICATION_NUMBER', 'REQUESTED_AMOUNT', 'APPLICATION_DATE', 'LOAN_TYPE']);

        // ---- Charts
        $trend = $this->trend($current, $period, $from, $to);
        $statusMix = $byStatus->map(fn ($r) => ['name' => $r->s, 'y' => (int) $r->c, 'amount' => (float) $r->a])->sortByDesc('y')->values();
        $typeMix = $current()->selectRaw("COALESCE(NULLIF(LOAN_TYPE,''),'NEW_LOAN') t, COUNT(*) c, COALESCE(SUM(REQUESTED_AMOUNT),0) a")->groupBy('t')->orderByDesc('c')->get()
            ->map(fn ($r) => ['name' => ucwords(strtolower(str_replace('_', ' ', $r->t))), 'y' => (int) $r->c, 'amount' => (float) $r->a]);
        $topEmployers = $current()->selectRaw("COALESCE(NULLIF(VOTE_NAME,''),'Unknown') n, COUNT(*) c, COALESCE(SUM(REQUESTED_AMOUNT),0) a")->groupBy('n')->orderByDesc('a')->limit(8)->get();
        $topBranches = $current()->selectRaw("COALESCE(NULLIF(BRANCH_NAME,''), NULLIF(NEAREST_BRANCH_NAME,''), 'Unassigned') n, COUNT(*) c, COALESCE(SUM(REQUESTED_AMOUNT),0) a")->groupBy('n')->orderByDesc('a')->limit(8)->get();
        $bands = $current()->selectRaw("
                SUM(REQUESTED_AMOUNT < 1000000) b1,
                SUM(REQUESTED_AMOUNT >= 1000000 AND REQUESTED_AMOUNT < 5000000) b2,
                SUM(REQUESTED_AMOUNT >= 5000000 AND REQUESTED_AMOUNT < 10000000) b3,
                SUM(REQUESTED_AMOUNT >= 10000000 AND REQUESTED_AMOUNT < 20000000) b4,
                SUM(REQUESTED_AMOUNT >= 20000000) b5")->first();

        $recent = $scoped()->whereNotNull('APPLICATION_DATE')->orderByDesc('APPLICATION_DATE')->limit(6)
            ->get(['ID', 'FIRST_NAME', 'LAST_NAME', 'APPLICATION_NUMBER', 'REQUESTED_AMOUNT', 'APPLICATION_DATE', 'TRANSACTION_STATUS', 'LOAN_TYPE']);

        return view('admin.dashboard.home', [
            'period'        => $period,
            'periods'       => self::PERIODS,
            'rangeLabel'    => $this->rangeLabel($period, $from, $to),
            'branchOnly'    => $branchOnly,
            'kpi'           => [
                'apps'            => (int) $totals->apps,
                'requested'       => (float) $totals->requested,
                'avg_amount'      => (float) $totals->avg_amount,
                'avg_tenure'      => (float) $totals->avg_tenure,
                'disbursed_count' => (int) $disbursed->cnt,
                'disbursed'       => (float) $disbursed->amount,
                'fees'            => (float) $disbursed->fees,
                'repayable'       => (float) $disbursed->repayable,
                'approval_rate'   => $approvalRate,
                'won'             => $won,
                'lost'            => $lost,
                'turnaround'      => $turnaroundHours !== null ? (float) $turnaroundHours : null,
                'pipeline_count'  => (int) $pipeline->cnt,
                'pipeline'        => (float) $pipeline->amount,
            ],
            'prev'          => $prev,
            'queue'         => $queue,
            'ageing'        => $ageing,
            'oldestPending' => $oldestPending,
            'recent'        => $recent,
            'charts'        => [
                'trend'     => $trend,
                'status'    => $statusMix,
                'types'     => $typeMix,
                'employers' => $topEmployers->map(fn ($r) => ['name' => $r->n, 'count' => (int) $r->c, 'amount' => round($r->a / 1000000, 2)]),
                'branches'  => $topBranches->map(fn ($r) => ['name' => $r->n, 'count' => (int) $r->c, 'amount' => round($r->a / 1000000, 2)]),
                'bands'     => [
                    ['< 1M', (int) $bands->b1], ['1–5M', (int) $bands->b2], ['5–10M', (int) $bands->b3],
                    ['10–20M', (int) $bands->b4], ['20M+', (int) $bands->b5],
                ],
            ],
        ]);
    }

    /** [from, to, previousFrom, previousTo] as local date-time strings; null means open-ended. */
    private function range(string $period): array
    {
        $now = Carbon::now(self::TZ);
        $to = $now->copy()->addMinute()->format('Y-m-d H:i:s');
        switch ($period) {
            case '30d':
                $from = $now->copy()->subDays(30)->startOfDay();
                return [$from->format('Y-m-d H:i:s'), $to, $from->copy()->subDays(30)->format('Y-m-d H:i:s'), $from->format('Y-m-d H:i:s')];
            case '90d':
                $from = $now->copy()->subDays(90)->startOfDay();
                return [$from->format('Y-m-d H:i:s'), $to, $from->copy()->subDays(90)->format('Y-m-d H:i:s'), $from->format('Y-m-d H:i:s')];
            case 'ytd':
                $from = $now->copy()->startOfYear();
                return [$from->format('Y-m-d H:i:s'), $to, $from->copy()->subYear()->format('Y-m-d H:i:s'), $now->copy()->subYear()->format('Y-m-d H:i:s')];
            case 'all':
                return [null, null, null, null];
            default: // 12m
                $from = $now->copy()->startOfMonth()->subMonths(11);
                return [$from->format('Y-m-d H:i:s'), $to, $from->copy()->subMonths(12)->format('Y-m-d H:i:s'), $from->format('Y-m-d H:i:s')];
        }
    }

    private function rangeLabel(string $period, ?string $from, ?string $to): string
    {
        if (!$from) {
            return 'All applications received';
        }
        return Carbon::parse($from)->format('d M Y').' – '.Carbon::now(self::TZ)->format('d M Y');
    }

    private function change($current, $previous): ?float
    {
        if ((float) $previous == 0.0) {
            return null;
        }
        return round(((float) $current - (float) $previous) * 100 / (float) $previous, 1);
    }

    /** Applications (count) and disbursed amount (TZS millions) per day for 30 days, otherwise per month. */
    private function trend(callable $current, string $period, ?string $from, ?string $to): array
    {
        $daily = $period === '30d';
        $fmt = $daily ? '%Y-%m-%d' : '%Y-%m';
        $rows = $current()->whereNotNull('APPLICATION_DATE')
            ->selectRaw("DATE_FORMAT(APPLICATION_DATE, '$fmt') k, COUNT(*) apps, COALESCE(SUM(CASE WHEN TRANSACTION_STATUS IN ('Disbursed','Posted to cbs') THEN REQUESTED_AMOUNT END),0) disb, COALESCE(SUM(REQUESTED_AMOUNT),0) req")
            ->groupBy('k')->orderBy('k')->get()->keyBy('k');

        if ($from) {
            $start = Carbon::parse($from);
        } elseif ($rows->isNotEmpty()) {
            $start = Carbon::parse($rows->keys()->first().($daily ? '' : '-01'));
        } else {
            $start = Carbon::now(self::TZ)->startOfMonth();
        }
        $end = $to ? Carbon::parse($to) : Carbon::now(self::TZ);
        if (!$from && $rows->isNotEmpty()) {
            $end = Carbon::parse($rows->keys()->last().($daily ? '' : '-01'))->endOfMonth();
        }

        $labels = $apps = $disb = $req = [];
        for ($d = $daily ? $start->copy()->startOfDay() : $start->copy()->startOfMonth(); $d->lte($end); $daily ? $d->addDay() : $d->addMonth()) {
            $k = $d->format($daily ? 'Y-m-d' : 'Y-m');
            $labels[] = $d->format($daily ? 'd M' : 'M Y');
            $apps[] = (int) ($rows[$k]->apps ?? 0);
            $disb[] = round(((float) ($rows[$k]->disb ?? 0)) / 1000000, 2);
            $req[] = round(((float) ($rows[$k]->req ?? 0)) / 1000000, 2);
        }
        return ['labels' => $labels, 'apps' => $apps, 'disbursed' => $disb, 'requested' => $req];
    }
}
