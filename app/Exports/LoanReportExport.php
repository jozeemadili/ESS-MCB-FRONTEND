<?php

namespace App\Exports;

use App\Support\LoanReport;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

/** Excel for a loan report, with the same filters and columns as the report on screen. */
class LoanReportExport implements FromView
{
    public function __construct(private string $type = 'all', private ?array $filters = null)
    {
    }

    public function view(): View
    {
        $filters = $this->filters ?? session('search_filters', []);
        $loans = LoanReport::query($this->type, $filters)->get();

        return view('admin.reports.loans_excel', [
            'loans'    => $loans,
            'columns'  => LoanReport::columns($this->type),
            'payments' => $this->type === 'takeover' ? LoanReport::takeoverPayments($loans) : [],
        ]);
    }
}
