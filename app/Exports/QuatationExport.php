<?php

namespace App\Exports;

/** All Loans Excel (Reports > Customise Report). Uses the filters of the last search when none are passed. */
class QuatationExport extends LoanReportExport
{
    public function __construct(?array $filters = null)
    {
        parent::__construct('all', $filters);
    }
}
