<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

/** Payroll deductions received from ESS (FSP_MONTHLY_DEDUCTIONS) as an Excel sheet. */
class PayrollDeductionsExport extends DefaultValueBinder implements FromView, WithCustomValueBinder, ShouldAutoSize
{
    private $rows;
    private $title;

    public function __construct($rows, string $title)
    {
        $this->rows = $rows;
        $this->title = $title;
    }

    public function view(): View
    {
        return view('admin.deductions.payroll_excel', ['rows' => $this->rows, 'title' => $this->title]);
    }

    /** Long digit strings (National ID, loan numbers) stay text so Excel does not round them. */
    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value) && preg_match('/^\d{12,}$/', trim($value))) {
            $cell->setValueExplicit(trim($value), DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }
}
