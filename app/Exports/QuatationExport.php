<?php

// namespace App\Exports;

// use App\Models\LOANAPPLICATION;
// use Illuminate\Contracts\View\View;
// use Maatwebsite\Excel\Concerns\FromView;

// class QuatationExport implements FromView
// {

//     public function view(): View
//     {
//         // Check if session values are set for start_date and end_date
//         if (session()->has('start_date') && session()->has('end_date')) {
//             $start_date = session('start_date');
//             $end_date = session('end_date');
//             $end_date = date('Y-m-d', strtotime($end_date)) . ' 23:59:59';
//             $loans_datails = LOANAPPLICATION::whereBetween('APPLICATION_DATE', [$start_date, $end_date])->get();
//         } else {
//             $loans_datails = LOANAPPLICATION::get();
//         }
//         // session()->forget(['start_date', 'end_date']);
//         // $loans_datails= LOANAPPLICATION::orderBy('id','desc')->get();
//         return view('admin.intermediaries.branches_excel', ['loans' => $loans_datails]);   
//     }
// }


namespace App\Exports;

use App\Models\LOANAPPLICATION;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class QuatationExport implements FromView
{
    public function view(): View
    {
        $filters = session('search_filters', []); // get all search filters

        $query = LOANAPPLICATION::query();

        // Apply filters if they exist
        if (!empty($filters['id_number'])) {
            $query->where('APPLICATION_NUMBER', $filters['id_number']);
        }

        if (!empty($filters['loan_id'])) {
            $query->where('LOAN_ID', $filters['loan_id']);
        }

        if (!empty($filters['phone'])) {
            $query->where('MSISDN', $filters['phone']);
        }

        if (!empty($filters['cname'])) {
            $names = explode(' ', $filters['cname']);
            $query->where(function ($q) use ($names) {
                foreach ($names as $name) {
                    $q->orWhere('FIRST_NAME', 'like', "%$name%")
                      ->orWhere('MIDDLE_NAME', 'like', "%$name%")
                      ->orWhere('LAST_NAME', 'like', "%$name%");
                }
            });
        }

        if (!empty($filters['status'])) {
            $query->where('LOAN_STATUS', $filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $end_date_adj = date('Y-m-d', strtotime($filters['end_date'])) . ' 23:59:59';
            $query->whereBetween('APPLICATION_DATE', [$filters['start_date'], $end_date_adj]);
        }

        $loans_datails = $query->get();

        return view('admin.intermediaries.branches_excel', ['loans' => $loans_datails]);
    }
}


