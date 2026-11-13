<?php

namespace App\Http\Controllers\API\Loans;
use App\Exports\AcceptedExport;
use App\Exports\ApprovedCaExport;
use App\Exports\ApprovedCmExport;
use App\Exports\ApprovedExport;
use App\Exports\ApprovedQaExport;
use App\Exports\CanceledExport;
use App\Exports\DisubursedExport;
use App\Exports\PostedcbsExport;
use App\Exports\QuatationExport;
use App\Exports\RejectedExport;
use App\Http\Controllers\Controller;
use App\Models\CUSTOMER;
use App\Models\DisburmentApprovalStage;
use App\Models\LOAN;
use App\Models\LOANAPPLICATION;
use App\Models\LoanDeduction;
use App\Models\PayrollBatch;
use App\Models\PayrollDeduction;
use App\Models\TakeoverBalanceRequest;
use App\Models\PAYMENT;
use App\Models\PAYMENTADVICE;
use App\Models\INCOMMINGPAYMENT;
use App\TIRAClient\Scripts\Classes\EsbClient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
class loanController extends Controller
{
    /** Reports > Customise Report (All Loans). Filters come in the query string so paging and Excel keep them. */
    public function get(Request $request)
    {
        return $this->report($request, 'all');
    }

    /**
     * Loan reports: all (Customise Report), restructuring, takeover, topup.
     * Search options and columns are those of the original report; Excel exports exactly what is filtered on screen.
     */
    public function report(Request $request, $type = 'all')
    {
        abort_unless(\App\Support\LoanReport::exists($type), 404);
        session()->forget(['start_date', 'end_date']);

        $filters = \App\Support\LoanReport::filtersFrom($request);
        // The Excel buttons of the other loan pages use the last search
        session(['search_filters' => $filters]);

        $query = \App\Support\LoanReport::query($type, $filters);
        $perPage = in_array((int) $request->input('per_page'), [15, 25, 50, 100]) ? (int) $request->input('per_page') : 15;
        $loans = $query->paginate($perPage)->withQueryString();

        return view('admin.reports.loans', [
            'type'     => $type,
            'loans'    => $loans,
            'statuses' => \App\Support\LoanReport::statuses($type),
            'filters'  => $filters,
            'searchBy' => \App\Support\LoanReport::searchBy($filters),
            'summary'  => \App\Support\LoanReport::summary($query),
            'payments' => $type === 'takeover' ? \App\Support\LoanReport::takeoverPayments($loans->items()) : [],
            'perPage'  => $perPage,
        ]);
    }

    public function exportReport(Request $request, $type)
    {
        abort_unless(\App\Support\LoanReport::exists($type), 404);
        return Excel::download(new \App\Exports\LoanReportExport($type, \App\Support\LoanReport::filtersFrom($request)),
            \App\Support\LoanReport::fileName($type));
    }
    public function getCancelled()
    { 
        if(Auth::user()->role == 'Branch Operations')
        {
            $loans_datails = LOANAPPLICATION::where('LOAN_STATUS', 'Cancelled')->where('INIT_BRANCH', Auth::user()->branch_id)->orderBy('ID', 'desc')->paginate(15);
        }else{
            $loans_datails = LOANAPPLICATION::where('LOAN_STATUS', 'Cancelled')->orderBy('ID', 'desc')->paginate(15);
      
        }
         // $loans_datails= LOANAPPLICATION::whereTransaction_status('Pending')->orderBy('id','desc')->paginate(15);
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);   
    }
    public function getRejected()
    {
        if(Auth::user()->role == 'Branch Operations')
        {
            $loans_datails = LOANAPPLICATION::where('TRANSACTION_STATUS', 'Rejected')->where('INIT_BRANCH', Auth::user()->branch_id)->orderBy('ID', 'desc')->paginate(15);
        }else{
        $loans_datails = LOANAPPLICATION::where('TRANSACTION_STATUS', 'Rejected')->orderBy('ID', 'desc')->paginate(15);
        }
        // $loans_datails= LOANAPPLICATION::whereTransaction_status('Pending')->orderBy('id','desc')->paginate(15);
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);   
    }
    public function getPending()
    {
        if(Auth::user()->role == 'Branch Operations')
        {
            $loans_datails = LOANAPPLICATION::where('TRANSACTION_STATUS', 'Pending')->where('LOAN_STATUS', 'Pending')->where('INIT_BRANCH', Auth::user()->branch_id)->orderBy('APPLICATION_DATE', 'asc')->paginate(15);
        }else{
        $loans_datails = LOANAPPLICATION::where('TRANSACTION_STATUS', 'Pending')->where('LOAN_STATUS', 'Pending')->orderBy('APPLICATION_DATE', 'asc')->paginate(15);
        }
        // $loans_datails= LOANAPPLICATION::whereTransaction_status('Pending')->orderBy('id','desc')->paginate(15);
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);   
    }
    /** Takeover, MCB = FSP1: payoff quotes MCB gave ESS for MCB loans another bank wants to take over. */
    public function getOutstandingBalanceRequest(Request $request)
    {
        if (!TakeoverBalanceRequest::tableReady()) {
            $payment_datails = PAYMENTADVICE::orderBy('PAYMENT_DATE', 'asc')->paginate(15);
            return view('admin.loan_take_over.payment_balance_request', ['payment_datails' => $payment_datails]);
        }
        $filters = ['quoted' => 'Awaiting payment', 'paid' => 'Paid', 'expired' => 'Expired', 'all' => 'All quotes'];
        $filter = array_key_exists($request->query('filter'), $filters) ? $request->query('filter') : 'all';
        $search = trim((string) $request->query('q', ''));
        $perPage = in_array((int) $request->query('per_page'), [15, 25, 50, 100]) ? (int) $request->query('per_page') : 15;
        $now = now('Africa/Dar_es_Salaam')->format('Y-m-d H:i:s');

        $base = TakeoverBalanceRequest::query();
        if (Auth::user()->role == 'Branch Operations') {
            $base->whereHas('loan', fn ($l) => $l->where('INIT_BRANCH', Auth::user()->branch_id));
        }
        $apply = function ($q, $f) use ($now) {
            if ($f === 'quoted') { $q->where('STATUS', 'QUOTED')->where('FINAL_PAYMENT_DATE', '>=', $now); }
            elseif ($f === 'paid') { $q->where('STATUS', 'PAID'); }
            elseif ($f === 'expired') { $q->where('STATUS', 'QUOTED')->where('FINAL_PAYMENT_DATE', '<', $now); }
            return $q;
        };
        $counts = [];
        foreach (array_keys($filters) as $f) { $counts[$f] = $apply(clone $base, $f)->count(); }
        $query = $apply((clone $base)->with('loan'), $filter);
        if ($search !== '') {
            $query->where(fn ($q) => $q->where('LOAN_NUMBER', 'like', "%$search%")->orWhere('CHECK_NUMBER', 'like', "%$search%")
                ->orWhere('EMPLOYEE_NAME', 'like', "%$search%")->orWhere('PAYMENT_REFERENCE_NUMBER', 'like', "%$search%"));
        }
        $quotes = $query->orderByDesc('ID')->paginate($perPage)->withQueryString();
        return view('admin.loan_take_over.payoff_quotes', compact('quotes', 'filters', 'filter', 'search', 'perPage', 'counts'));
    }
    /** Takeover, MCB = FSP2: loans MCB takes over and the payments to the other bank (FSP1). */
    public function getOutgoingPaymentDetails(Request $request)
    {
        if (!PAYMENT::takeoverReady()) {
            $payment_datails = PAYMENT::orderBy('CREATED_AT', 'asc')->paginate(15);
            return view('admin.loan_take_over.out_going_payment_request', ['payment_datails' => $payment_datails]);
        }
        $filters = ['awaiting' => 'Awaiting proof of payment', 'paid' => 'Proof uploaded, ESS not yet told', 'notified' => 'Completed', 'closed' => 'Cancelled / rejected', 'all' => 'All takeovers'];
        $filter = array_key_exists($request->query('filter'), $filters) ? $request->query('filter') : 'all';
        $search = trim((string) $request->query('q', ''));
        $perPage = in_array((int) $request->query('per_page'), [15, 25, 50, 100]) ? (int) $request->query('per_page') : 15;

        $base = PAYMENT::query()->whereNotNull('FSP1_LOAN_NUMBER');
        if (Auth::user()->role == 'Branch Operations') {
            $base->whereHas('loan', fn ($l) => $l->where('INIT_BRANCH', Auth::user()->branch_id));
        }
        $apply = function ($q, $f) {
            $closed = fn ($l) => $l->whereIn('LOAN_STATUS', ['Cancelled', 'Rejected'])->orWhere('TRANSACTION_STATUS', 'Rejected');
            if ($f === 'awaiting') { $q->where('STATUS', 'RECEIVED')->whereDoesntHave('loan', $closed); }
            elseif ($f === 'closed') { $q->where('STATUS', 'RECEIVED')->whereHas('loan', $closed); }
            elseif ($f === 'paid') { $q->where('STATUS', 'PAID'); }
            elseif ($f === 'notified') { $q->where('STATUS', 'NOTIFIED'); }
            return $q;
        };
        $counts = [];
        foreach (array_keys($filters) as $f) { $counts[$f] = $apply(clone $base, $f)->count(); }
        $query = $apply((clone $base)->with('loan'), $filter);
        if ($search !== '') {
            $query->where(fn ($q) => $q->where('LOAN_ID', 'like', "%$search%")->orWhere('APPLICATION_NUMBER', 'like', "%$search%")
                ->orWhere('FSP1_LOAN_NUMBER', 'like', "%$search%")->orWhere('FSP1_CODE', 'like', "%$search%"));
        }
        $payments = $query->orderByDesc('ID')->paginate($perPage)->withQueryString();
        $canRecord = in_array(Auth::user()->role, ['Credit Operation', 'Credit Admin', 'ADMIN']);
        return view('admin.loan_take_over.outgoing_payments', compact('payments', 'filters', 'filter', 'search', 'perPage', 'counts', 'canRecord'));
    }

    /**
     * Takeover (MCB = FSP2): after Credit Operation disburses and FSP1 is paid in Profits/TISS, upload the proof of payment
     * (payment reference + SWIFT copy). Saved through the gateway (takeover/outgoing-payment), which notifies ESS straight away.
     */
    public function recordTakeoverTransfer(Request $request)
    {
        $request->validate([
            'application_number' => ['required', 'string', 'max:30'],
            'transfer_reference' => ['required', 'string', 'max:60'],
            'paid_amount'        => ['required', 'numeric', 'min:1'],
            'swift_copy'         => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'payment_advice'     => ['nullable', 'string', 'max:20000'],
        ], [], ['transfer_reference' => 'payment reference', 'swift_copy' => 'SWIFT copy (PDF)']);
        if (!in_array(Auth::user()->role, ['Credit Operation', 'Credit Admin', 'ADMIN'])) {
            return back()->with('success', '<b>FAILED</b>, your role cannot upload takeover proof of payment.');
        }
        $body = json_encode([
            'applicationNumber' => $request->application_number,
            'transferReference' => $request->transfer_reference,
            'paidAmount'        => (float) $request->paid_amount,
            'swiftCopyBase64'   => base64_encode(file_get_contents($request->file('swift_copy')->getRealPath())),
            'paymentAdvice'     => (string) $request->payment_advice,
            'recordedBy'        => self::userDisplayName(),
        ]);
        try {
            $response = EsbClient::SendesbRequestbody($body, 'takeover/outgoing-payment');
        } catch (Exception $e) {
            return back()->with('success', '<b>FAILED</b>, the ESS gateway is not reachable: ' . e($e->getMessage()));
        }
        if ($response != null && isset($response->statusCode) && $response->statusCode === 200) {
            return back()->with('success', '<b>SUCCESS</b>, ' . e($response->statusDescription));
        }
        $description = ($response != null && isset($response->statusDescription)) ? $response->statusDescription : 'No response from the ESS gateway';
        return back()->with('success', '<b>FAILED</b>, ' . e($description));
    }

    /** PDF payment advice of MCB's transfer to FSP1. */
    public function takeoverOutgoingAdvicePdf($id)
    {
        $payment = PAYMENT::find($id);
        $pdf = $payment ? base64_decode((string) $payment->PAYMENT_ADVICE_ATTACHMENT, true) : false;
        if (!$pdf || strncmp($pdf, '%PDF', 4) !== 0) {
            abort(404, 'No PDF payment advice for this payment');
        }
        return response($pdf, 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="takeover-advice-' . $payment->ID . '.pdf"']);
    }
    /** Takeover, MCB = FSP1: payments from the bank that took over an MCB loan, to confirm to ESS (SETTLED / UNSETTLED). */
    public function getIncomingPaymentRequest(Request $request)
    {
        if (!INCOMMINGPAYMENT::ackReady()) {
            $payment_datails = INCOMMINGPAYMENT::orderBy('PAYMENT_DATE', 'asc')->paginate(15);
            return view('admin.loan_take_over.incoming_payment_request', ['payment_datails' => $payment_datails]);
        }
        $filters = ['pending' => 'To confirm', 'settled' => 'Settled', 'unsettled' => 'Unsettled', 'all' => 'All payments'];
        $filter = array_key_exists($request->query('filter'), $filters) ? $request->query('filter') : 'pending';
        $search = trim((string) $request->query('q', ''));
        $perPage = in_array((int) $request->query('per_page'), [15, 25, 50, 100]) ? (int) $request->query('per_page') : 15;

        $base = INCOMMINGPAYMENT::query();
        if (Auth::user()->role == 'Branch Operations') {
            $base->whereHas('loan', fn ($l) => $l->where('INIT_BRANCH', Auth::user()->branch_id));
        }
        $apply = function ($q, $f) {
            if ($f === 'pending') { INCOMMINGPAYMENT::pending($q); }
            elseif ($f === 'settled') { $q->where('ACK_STATUS', 'SETTLED'); }
            elseif ($f === 'unsettled') { $q->where('ACK_STATUS', 'UNSETTLED'); }
            return $q;
        };
        $counts = [];
        foreach (array_keys($filters) as $f) { $counts[$f] = $apply(clone $base, $f)->count(); }
        $query = $apply((clone $base)->with(['loan', 'quote']), $filter);
        if ($search !== '') {
            $query->where(fn ($q) => $q->where('LOAN_NUMBER', 'like', "%$search%")->orWhere('APPLICATION_NUMBER', 'like', "%$search%")
                ->orWhere('PAYMENT_REFERENCE_NUMBER', 'like', "%$search%"));
        }
        $payments = $query->orderByDesc('ID')->paginate($perPage)->withQueryString();
        $canConfirm = in_array(Auth::user()->role, ['Credit Operation', 'ADMIN']);
        return view('admin.loan_take_over.incoming_payments', compact('payments', 'filters', 'filter', 'search', 'perPage', 'counts', 'canConfirm'));
    }

    /** The payment advice PDF (base64 in PAYMENT_ADVICE_ATTACHMENT) sent by the other bank. */
    public function takeoverPaymentAdvicePdf($id)
    {
        $payment = INCOMMINGPAYMENT::find($id);
        $pdf = $payment ? base64_decode((string) $payment->PAYMENT_ADVICE_ATTACHMENT, true) : false;
        if (!$pdf || strncmp($pdf, '%PDF', 4) !== 0) {
            abort(404, 'No PDF payment advice for this payment');
        }
        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="payment-advice-' . $payment->ID . '.pdf"',
        ]);
    }

    /** Sends PAYMENT_ACKNOWLEDGMENT_NOTIFICATION through the gateway after the money was checked in core banking. */
    public function confirmTakeoverPayment(Request $request)
    {
        $request->validate([
            'id'      => ['required', 'integer'],
            'status'  => ['required', 'in:SETTLED,UNSETTLED'],
            'remarks' => ['required', 'string', 'min:3', 'max:255'],
        ]);
        if (!in_array(Auth::user()->role, ['Credit Operation', 'ADMIN'])) {
            return back()->with('success', '<b>FAILED</b>, only Credit Operation can confirm takeover payments.');
        }
        $body = json_encode([
            'incomingPaymentId' => (int) $request->id,
            'status'            => $request->status,
            'remarks'           => $request->remarks,
            'decidedBy'         => self::userDisplayName(),
        ]);
        try {
            $response = EsbClient::SendesbRequestbody($body, 'takeover/payment-acknowledgment');
        } catch (Exception $e) {
            return back()->with('success', '<b>FAILED</b>, the ESS gateway is not reachable: ' . e($e->getMessage()));
        }
        if ($response != null && isset($response->statusCode) && $response->statusCode === 200) {
            return back()->with('success', '<b>SUCCESS</b>, ' . e($response->statusDescription));
        }
        $description = ($response != null && isset($response->statusDescription)) ? $response->statusDescription : 'No response from the ESS gateway';
        return back()->with('success', '<b>FAILED</b>, ' . e($description));
    }
    public function incomingPaymentprofile($load_id)
    {
        // dd($load_id);
        $loan_details= LOANAPPLICATION::where('LOAN_ID',$load_id)->get();
        $payment_details= INCOMMINGPAYMENT::where('LOAN_NUMBER',$load_id)->get();
        $payment_new= PAYMENT::where('LOAN_ID',$load_id)->get();


        return view('admin.properties.incoming-payment-profile',['loan_details' => $loan_details,'payment_details' => $payment_details, 'load_id' => $load_id,'payment_new'=>$payment_new]);
        
        
    }
    public function getAllChapchapLoans()
    {
        
        $loans_datails = LOAN::orderBy('CREATED_AT', 'asc')->paginate(15);
       
        return view('admin.intermediaries.chapchap_loans', ['loans' => $loans_datails]);   
    }
    public function searchCustomer()
    {
       
            $loans_datails = CUSTOMER::paginate(15);
    
        return view('admin.intermediaries.customer-details', ['loans' => $loans_datails]);   
    }
    public function getAllCustomers(Request $request)
    {
       
            // $loans_datails = CUSTOMER::paginate(15);

            $loans_details = CUSTOMER::query();

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            session(['start_date' => $start_date]);
            session(['end_date' => $end_date]);
            // Adjust end_date to cover the entire day
            if (!empty($start_date) && !empty($end_date)) {
                $end_date = date('Y-m-d', strtotime($end_date)) . ' 23:59:59';
                $loans_details->whereBetween('CREATED_AT', [$start_date, $end_date]);
            }
            // // Check if the request includes a date range (start_date and end_date)
            // if (!empty($request->start_date) && !empty($request->end_date)) {
            //     $loans_details->whereBetween('APPLICATION_DATE', [$request->start_date, $request->end_date]);
            // }
    
            // If all details are provided, prioritize finding an exact match for all details
            if (!empty($request->phone_number) && !empty($request->acc_no) && !empty($request->cname) && !empty($request->check_number)) {
                $loans_details->where('MOBILE_NUMBER', $request->phone_number)
                    ->where('CHECK_NUMBER', $request->check_number)
                    ->where('ACCOUNT_NUMBER', $request->acc_no)
                    ->where(function ($query) use ($request) {
                        $names = explode(' ', $request->cname); // Split name into parts
                        foreach ($names as $name) {
                            $query->orWhere('FIRST_NAME', 'like', '%' . $name . '%')
                                ->orWhere('MIDDLE_NAME', 'like', '%' . $name . '%')
                                ->orWhere('LAST_NAME', 'like', '%' . $name . '%');
                        }
                    });
            } else {
                // Apply conditions based on non-empty inputs
                if (!empty($request->phone_number)) {
                    $loans_details->where('MOBILE_NUMBER', $request->phone_number);
                }
    
                if (!empty($request->acc_no)) {
                    $loans_details->where('ACCOUNT_NUMBER', $request->acc_no);
                }
    
                if (!empty($request->check_number)) {
                    $loans_details->where('CHECK_NUMBER', $request->check_number);
                }
    
                if (!empty($request->cname)) {
                    $loans_details->where(function ($query) use ($request) {
                        $names = explode(' ', $request->cname); // Split name into parts
                        foreach ($names as $name) {
                            $query->orWhere('FIRST_NAME', 'like', '%' . $name . '%')
                                ->orWhere('MIDDLE_NAME', 'like', '%' . $name . '%')
                                ->orWhere('LAST_NAME', 'like', '%' . $name . '%');
                        }
                    });
                }
                // if (!empty($start_date) && !empty($end_date)) {
                //     $end_date = date('Y-m-d', strtotime($end_date)) . ' 23:59:59';
                //     $loans_details->whereBetween('APPLICATION_DATE', [$start_date, $end_date]);
                // }
                // Apply date range if start_date and end_date are provided
                if (!empty($request->start_date) && !empty($request->end_date)) {
                    $end_date = date('Y-m-d', strtotime($end_date)) . ' 23:59:59';
                    $loans_details->whereBetween('CREATED_AT', [$start_date, $end_date]);
                    // $loans_details->whereBetween('APPLICATION_DATE', [$request->start_date, $request->end_date]);
                }
            }
    
            // Add pagination to the query (you can specify the number of items per page)
            if (!empty($request->start_date) && !empty($request->end_date)){
    
            }else
            {
                
            }
            $loans_details2 = $loans_details->paginate(10); 
    
        return view('admin.intermediaries.customer-details', ['loans' => $loans_details2]);   
    }
    /** Search form posts here (old links and bookmarks); the report itself is a GET so paging keeps the filters. */
    public function search(Request $request)
    {
        $type = \App\Support\LoanReport::exists((string) $request->input('report')) ? $request->input('report') : 'all';
        $query = \App\Support\LoanReport::queryString(\App\Support\LoanReport::filtersFrom($request));
        return $type === 'all'
            ? redirect()->route('loans-applications', $query)
            : redirect()->route('loans-report', ['type' => $type] + $query);
    }

    public function searchOld(Request $request)
    {
        
        if (!empty($request->date_wise))
        {

            $loans_details = LOANAPPLICATION::query();
            $loans_details->where('MSISDN', $request->phone_number);
        }else
        {
            $loans_details = LOANAPPLICATION::query();
        // 
            // If all details are provided, prioritize finding an exact match for all details
            if (!empty($request->phone_number) && !empty($request->id_number) && !empty($request->cname) && !empty($request->loan_id)) {
                $loans_details->where('MSISDN', $request->phone_number)
                    ->where('APPLICATION_NUMBER', $request->id_number)
                    ->where('LOAN_ID', $request->loan_id)
                    ->where(function ($query) use ($request) {
                        $names = explode(' ', $request->cname); // Split name into parts
                        foreach ($names as $name) {
                            $query->orWhere('FIRST_NAME', 'like', '%' . $name . '%')
                                ->orWhere('MIDDLE_NAME', 'like', '%' . $name . '%')
                                ->orWhere('LAST_NAME', 'like', '%' . $name . '%');
                        }
                    });
            } 
            else 
            {
                // Apply conditions based on non-empty inputs
                if (!empty($request->phone_number)) {
                    $loans_details->where('MSISDN', $request->phone_number);
                }
        
                if (!empty($request->loan_id)) {
                    // dd("jose".$request->loan_id);
                    $loans_details->where('LOAN_ID', $request->loan_id);
                }
        
                if (!empty($request->id_number)) {
                    $loans_details->where('APPLICATION_NUMBER', $request->id_number);
                }
        
                if (!empty($request->cname)) {
                    $loans_details->where(function ($query) use ($request) {
                        $names = explode(' ', $request->cname); // Split name into parts
                        foreach ($names as $name) {
                            $query->orWhere('FIRST_NAME', 'like', '%' . $name . '%')
                                ->orWhere('MIDDLE_NAME', 'like', '%' . $name . '%')
                                ->orWhere('LAST_NAME', 'like', '%' . $name . '%');
                        }
                    });
                }
            }

        }
        
    
        $loans_details = $loans_details->orderBy('ID', 'desc')->paginate(10);
    
        return view('admin.intermediaries.branches', ['loans' => $loans_details]);
    }
    
    public function getPendingAll()
    {
        if(Auth::user()->role == 'Branch Operations')
        {
            $loans_datails = LOANAPPLICATION::where('TRANSACTION_STATUS', 'Pending')->where('INIT_BRANCH', Auth::user()->branch_id)->orderBy('APPLICATION_DATE', 'asc')->paginate(15);
        }else{
        $loans_datails = LOANAPPLICATION::where('TRANSACTION_STATUS', 'Pending')->orderBy('APPLICATION_DATE', 'desc')->paginate(15);
        }
        // $loans_datails= LOANAPPLICATION::whereTransaction_status('Pending')->orderBy('id','desc')->paginate(15);
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);   
    }
    public function getAccepted()
    {
        if(Auth::user()->role == 'Branch Operations')
        {
            $loans_datails = LOANAPPLICATION::where('LOAN_STATUS', 'Accepted')->where('INIT_BRANCH', Auth::user()->branch_id)->orderBy('ID', 'desc')->paginate(15);
        }else{
        $loans_datails= LOANAPPLICATION::whereLoan_status('Accepted')->orderBy('id','desc')->paginate(15);
        }
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);   
    }
    public function getApproved()
    {if(Auth::user()->role == 'Branch Operations')
        {
            $loans_datails = LOANAPPLICATION::where('TRANSACTION_STATUS', 'Approved')->where('INIT_BRANCH', Auth::user()->branch_id)->orderBy('APPROVED_DATE', 'asc')->paginate(15);
        }else{
        $loans_datails= LOANAPPLICATION::whereTransaction_status('Approved')->orderBy('APPROVED_DATE','desc')->paginate(15);
        }
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);   
    }
    public function getApprovedforCm()
    {
        // $loans_datails= LOANAPPLICATION::whereTransaction_status('Approved')->orderBy('id','desc')->paginate(15);
        $loans_datails = LoanApplication::where('Transaction_status', 'Approved')
        ->join('disburment_approval_stages', 'LOAN_APPLICATION.id', '=', 'disburment_approval_stages.loan_id')
        // ->where('disburment_approval_stages.status', 'INITIATED')
        ->whereIn('disburment_approval_stages.status', ['INITIATED', 'INITIATED_REJECTION'])
        ->orderBy('LOAN_APPLICATION.APPROVED_DATE', 'desc')
        ->select('LOAN_APPLICATION.*') // To avoid selecting columns from disburment_approval_stages
        ->paginate(15);
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);   
    }
    public function getApprovedforCa()
    {
        $loans_details = LOANAPPLICATION::where('TRANSACTION_STATUS','Approved')
        ->leftJoin('disburment_approval_stages', 'LOAN_APPLICATION.ID', '=', 'disburment_approval_stages.loan_id')
        ->whereNull('disburment_approval_stages.id')
        ->orderBy('LOAN_APPLICATION.APPROVED_DATE', 'desc')
        ->select('LOAN_APPLICATION.*') // To avoid selecting columns from disburment_approval_stages
        ->paginate(15);
        // $loans_datails= LOANAPPLICATION::whereTransaction_status('Approved')->orderBy('id','desc')->paginate(15);
        return view('admin.intermediaries.branches', ['loans' => $loans_details]);   
    }
    public function getDisbursed()
    {   if(Auth::user()->role == 'Branch Operations')
        {
            $loans_datails = LOANAPPLICATION::where('TRANSACTION_STATUS', 'Disbursed')->where('INIT_BRANCH', Auth::user()->branch_id)->orderBy('ID', 'desc')->paginate(15);
        }else{
        $loans_datails= LOANAPPLICATION::whereTransaction_status('Disbursed')->orderBy('id','desc')->paginate(15);
        }
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);   
    }
    public function getPostCbs()
    {   if(Auth::user()->role == 'Branch Operations')
        {
            $loans_datails = LOANAPPLICATION::where('TRANSACTION_STATUS', 'Posted to cbs')->where('INIT_BRANCH', Auth::user()->branch_id)->orderBy('ID', 'desc')->paginate(15);
        }else{
        $loans_datails= LOANAPPLICATION::whereTransaction_status('Posted to cbs')->orderBy('id','desc')->paginate(15);
        }
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);   
    }
    public function geticon()
    {
        return view('admin.icons.ico-icon');   
    }
    public function profile($id)
    {
        try
        {
            $vehicle= LOANAPPLICATION::find($id);
            $approval_datils = DisburmentApprovalStage::where('loan_id', $id)->first();
            return view('admin.properties.staff-profile',['vehicle' => $vehicle,'approval_datils' => $approval_datils]);
        }
        catch(Exception $e){
            return array("responseCode" => "FAILED", "message" => $e->getMessage());
        }
    }
    public function exportData()
    {
        return self::export();
    }
    
    public function export() 
    {
        // With filters in the link (Customise Report) they are used; otherwise the last search, as before
        $filters = \App\Support\LoanReport::filtersFrom(request());
        return Excel::download(new QuatationExport(\App\Support\LoanReport::hasFilters($filters) ? $filters : null), 'All-Loans.xlsx');
    }
    public function exportDisbursed() 
    {
        return Excel::download(new DisubursedExport, 'All-Loans-Disbursed.xlsx');
    }
    public function exportApproved() 
    {
        return Excel::download(new ApprovedExport, 'All-Loans-Approved.xlsx');
    }
    public function exportApprovedcm() 
    {
        return Excel::download(new ApprovedCmExport, 'All-Loans-Approvedcm.xlsx');
    }
    public function exportApprovedca() 
    {
        return Excel::download(new ApprovedCaExport, 'All-Loans-WaitinCA.xlsx');
    }
    public function exportApprovedqa() 
    {
        return Excel::download(new ApprovedQaExport, 'All-Loans-WaitinQA.xlsx');
    }
    public function exportAcceptedbyBank() 
    {
        return Excel::download(new AcceptedExport, 'AccepeteByBak.xlsx');
    }
    public function exportRejected() 
    {
        return Excel::download(new RejectedExport, 'RejectedLoans.xlsx');
    }
    public function exportCancelled() 
    {
        return Excel::download(new CanceledExport, 'CanceledLoans.xlsx');
    }
    public function exportPostedCbs() 
    {
        return Excel::download(new PostedcbsExport, 'PostedToCBSLoans.xlsx');
    }
    public function updateLoanAccountDetailsLoanAcc(Request $request)
    {
        // dd($request->loanAccount."-".$request->checkDigit);
       $loan_detail = LOANAPPLICATION::where('ID', $request->id)->first(); // Retrieve the model instance
       if ($loan_detail) 
       {
        //    $loan_detail->LINAC_ACCOUNT_NUMBER = $request->loanAccount; // Set the account number
           $loan_detail->DISBURSEMENT_RECEIPT = $request->loanAccount;
           
           $loan_detail->save(); // Save the changes checkDigit
       
           return redirect('v1/properties/staff/registration/profile/' . $request->id)
               ->with('success', '<b>SUCCESS</b>, Details Updated successfully.');
       } else {
           return redirect()->back()->with('error', 'Loan details not found.');
       }
    }  
    public function updateLoanAccountDetails(Request $request)
    {
        // dd($request->loanAccount."-".$request->checkDigit);
       $loan_detail = LOANAPPLICATION::where('ID', $request->id)->first(); // Retrieve the model instance
       if ($loan_detail) 
       {
        //    $loan_detail->LINAC_ACCOUNT_NUMBER = $request->loanAccount; // Set the account number
           $loan_detail->DISBURSEMENT_RECEIPT = $request->loanAccount;
           $loan_detail->CHECK_DIGIT = $request->checkDigit;
           $loan_detail->save(); // Save the changes checkDigit
       
           return redirect('v1/properties/staff/registration/profile/' . $request->id)
               ->with('success', '<b>SUCCESS</b>, Details saved successfully.');
       } else {
           return redirect()->back()->with('error', 'Loan details not found.');
       }
    }  
    public function updateLoanLiquidataionStatus(Request $request)
    {
        // dd($request->id);
        if($request->status === 'Complete')
        {
        $loan_detail = LOANAPPLICATION::find($request->id);
        $loan_detail->LIQUIDATION_CHECKER    = Auth::user()->first_name;
        $loan_detail->LIQUIDATION_CHECKER_REMARKS    = $request->reason;
        $loan_detail->LIQUIDATION_STATUS    = $request->status;
        $loan_detail->save();
        return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', 'You Have Successfully '.$request->status.' The Request <font color="red"><b> Note : </b> </font> Remember to Click Notify Button To submit your request Utumish');

        }else
        {
        $loan_detail = LOANAPPLICATION::find($request->id);
        $loan_detail->LIQUIDATION_MAKER    = Auth::user()->first_name;
        $loan_detail->LIQUIDATION_MAKER_REMARKS    = $request->reason;
        $loan_detail->LIQUIDATION_STATUS    = $request->status;
        $loan_detail->save();

        return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', 'You Have Successfully '.$request->status.' The Request');


        }
        
    }
    public function updateLoanStatus(Request $request)
    {
        if($request->status === 'REJECTED')
        {
            if(Auth::user()->role == 'Credit Officer')
            {
                // $endPoint='http://172.16.3.198:30002/api/v2/hmcis/initial-loan-approval';
                $endPoint='initial-loan-approval';
                // decidedBy: the gateway saves it with the decision time (CO_DECISION_BY / CO_DECISION_DATE)
                $body = json_encode([
                    'loanApplication' => (int) $request->id,
                    'status'          => $request->status,
                    'remarks'         => $request->reason,
                    'decidedBy'       => self::userDisplayName(),
                ]);
            
                $response = EsbClient::SendesbRequestbody($body,$endPoint); 
                $status = $response->status;
                $statusDescription = $response->statusDescription;
                if ($response->statusCode === 200) 
                    {   
                      return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', ' <b>SUCCESS</b>, '.$statusDescription.' ');
                    }
                else
                    {
                      return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', ' <b>FAILED</b>, '.$statusDescription.' ');
                    }
            }
            elseif(Auth::user()->role == 'Credit Admin')
            {
                $DisburmentApprovalStage = DisburmentApprovalStage::create(
                    [   
                        'loan_id'            => $request->id,
                        'status'             => 'INITIATED_REJECTION',
                        'initiator_id'       => Auth::user()->id,
                        'initiator_date'     => date('Y-m-d H:i:s')
                    ]);
                $loan_detail = LOANAPPLICATION::find($request->id);
                $loan_detail->LOAN_REQUEST_INITIATOR    = Auth::user()->first_name;
                $loan_detail->save();
                return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', 'You Have Successfully '.$request->status.' The Request');
            }
            elseif(Auth::user()->role == 'Credit Operation')
            {
                $endPoint='disbursement-reject';
                $body='{
                    "loanApplication":'.$request->id.',
                    "status":"'.$request->status.'",
                    "remarks":"'.$request->reason.'"
                }
               ';
            //    var_dump($body);
            // dd($body);
                $response = EsbClient::SendesbRequestbody($body,$endPoint); 
                $status = $response->status;
                $statusDescription = $response->statusDescription;
                if ($response->statusCode === 200) 
                    {   
                        $DisburmentApprovalStage = DisburmentApprovalStage::create(
                            [   
                                'loan_id'            => $request->id,
                                'status'             => 'REJECTED',
                                'initiator_id'       => Auth::user()->id,
                                'initiator_date'     => date('Y-m-d H:i:s')
                            ]);
                        $loan_detail = LOANAPPLICATION::find($request->id);
                        $loan_detail->LOAN_REQUEST_INITIATOR    = Auth::user()->first_name;
                        $loan_detail->save();
                      return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', ' <b>SUCCESS</b>, '.$statusDescription.' ');
                    }
                else
                    {
                      return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', ' <b>FAILED</b>, '.$statusDescription.' ');
                    }

            }
        }
        elseif($request->status === 'Approved')
        {
            if(Auth::user()->role == 'Credit Officer')
            {
                // $endPoint='http://172.16.3.198:30002/api/v2/hmcis/initial-loan-approval';
                $endPoint='initial-loan-approval';
                // decidedBy: the gateway saves it with the decision time (CO_DECISION_BY / CO_DECISION_DATE)
                $body = json_encode([
                    'loanApplication' => (int) $request->id,
                    'status'          => $request->status,
                    'remarks'         => $request->reason,
                    'decidedBy'       => self::userDisplayName(),
                ]);
                $response = EsbClient::SendesbRequestbody($body,$endPoint); 
                $status = $response->status;
                $statusDescription = $response->statusDescription;
                if ($response->statusCode === 200) 
                    {   
                      return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', ' <b>SUCCESS</b>, '.$statusDescription.' ');
                    }
                else
                    {
                      return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', ' <b>FAILED</b>, '.$statusDescription.' ');
                    }
            }
            elseif(Auth::user()->role == 'Credit Admin')
            {
                $DisburmentApprovalStage = DisburmentApprovalStage::create(
                    [   
                        'loan_id'            => $request->id,
                        'status'             => 'INITIATED',
                        'initiator_id'       => Auth::user()->id,
                        'initiator_date'     => date('Y-m-d H:i:s')
                    ]);
                $loan_detail = LOANAPPLICATION::find($request->id);
                $loan_detail->LOAN_REQUEST_INITIATOR    = Auth::user()->first_name;
                $loan_detail->save();
                return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', 'You Have Successfully '.$request->status.' The Request');
            }
            elseif(Auth::user()->role == 'Credit Operation')
            {
                // $endPoint='http://172.16.3.198:30002/api/v2/hmcis/disbursement/'.$request->id.'';
                $endPoint='disbursement/'.$request->id.'';
                $response = EsbClient::SendesbRequesturl($endPoint); 
                $status = $response->status;
                $statusDescription = $response->statusDescription;
                if ($response->statusCode === 200) 
                    {
                        $details = DisburmentApprovalStage::where('loan_id', $request->id)->first();
                        if($details != null)
                        {
                            $DisburmentApprovalStageDetails = DisburmentApprovalStage::where('loan_id', $request->id)->first();
                            $DisburmentApprovalStageDetails->status        = 'DISBURSED';
                            $DisburmentApprovalStageDetails->checker_id    = Auth::user()->id;
                            $DisburmentApprovalStageDetails->checker_date  = date('Y-m-d H:i:s'); 
                            $DisburmentApprovalStageDetails->save();
    
                            $loan_detail = LOANAPPLICATION::find($request->id);
                            $loan_detail->LOAN_REQUEST_CHECKER    = Auth::user()->first_name;
                            $loan_detail->save();
                            return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', ' <b>SUCCESS</b>, '.$statusDescription.' ');   
                        }
                        else
                        {
                            return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', 'Failed to update Approval Status');
                        }
                    }
                else
                    {
                        return redirect('v1/properties/staff/registration/profile/'.$request->id.'')->with('success', ' <b>FAILED</b>, '.$statusDescription.' ');
                    }
        }else
        {

        }
        
        }
        else
        {

        }

    }

    /**
     * Restructure applications (LOAN_TYPE = RESTRUCTURING) by stage:
     * pending (waiting CO), accepted (by bank), approved (by employer), completed, rejected, all.
     */
    public function getRestructuring(Request $request)
    {
        $stage = $request->query('stage', 'pending');
        $query = LOANAPPLICATION::where('LOAN_TYPE', 'RESTRUCTURING');

        switch ($stage) {
            case 'pending':
                $query->where('TRANSACTION_STATUS', 'Pending')->where('LOAN_STATUS', 'Pending');
                break;
            case 'accepted':
                $query->where('TRANSACTION_STATUS', 'Pending')->where('LOAN_STATUS', 'Accepted');
                break;
            case 'approved':
                $query->where('TRANSACTION_STATUS', 'Approved');
                break;
            case 'completed':
                $query->whereIn('TRANSACTION_STATUS', ['Posted to CBS', 'Disbursed']);
                break;
            case 'rejected':
                $query->whereIn('LOAN_STATUS', ['Rejected', 'Cancelled']);
                break;
        }

        if (Auth::user()->role == 'Branch Operations') {
            $query->where('INIT_BRANCH', Auth::user()->branch_id);
        }

        $loans_datails = $query->orderBy('ID', 'desc')->paginate(15)->withQueryString();
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);
    }

    /** Original loans that were replaced by a completed restructure (TRANSACTION_STATUS = Restructured). */
    public function getRestructuredOriginals()
    {
        $query = LOANAPPLICATION::where('TRANSACTION_STATUS', 'Restructured');
        if (Auth::user()->role == 'Branch Operations') {
            $query->where('INIT_BRANCH', Auth::user()->branch_id);
        }
        $loans_datails = $query->orderBy('ID', 'desc')->paginate(15);
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);
    }

    /**
     * Bank-initiated restructure proposal (LOAN_RESTRUCTURE_REQUEST_FSP) for a disbursed loan.
     * The Java gateway calculates the new terms from the principal balance and sends the proposal to ESS.
     */
    /** Logged-in user's name; some users have the full name in first_name, so the last name is not repeated. */
    private static function userDisplayName(): string
    {
        $first = trim((string) Auth::user()->first_name);
        $last = trim((string) Auth::user()->last_name);
        return ($last === '' || stripos($first, $last) !== false) ? $first : trim($first . ' ' . $last);
    }

    /**
     * Deduction stops received from ESS (DEDUCTION_STOP_NOTIFICATION). Filter: pending (not reviewed), reviewed,
     * notfound (loan number unknown to MCB) or all.
     */
    /** Monthly payroll deductions received from ESS (FSP_MONTHLY_DEDUCTIONS), by payroll date. */
    public function getPayrollDeductions(Request $request)
    {
        if (!PayrollDeduction::tableReady()) {
            return redirect()->route('home')->with('success', '<b>NOT AVAILABLE</b>, the ESS gateway has not received any payroll yet.');
        }
        $user = Auth::user();
        $dates = PayrollDeduction::visibleTo($user)->whereNotNull('CHECK_DATE')->distinct()->orderByDesc('CHECK_DATE')->limit(36)->pluck('CHECK_DATE');
        $payroll = (string) $request->query('payroll', '');
        if ($payroll !== 'all' && !$dates->contains($payroll)) {
            $payroll = (string) ($dates->first() ?? 'all');
        }
        $stopOnly = $request->boolean('stop');
        $search = trim((string) $request->query('q', ''));
        $perPage = in_array((int) $request->query('per_page'), [15, 25, 50, 100]) ? (int) $request->query('per_page') : 25;

        $base = fn () => PayrollDeduction::visibleTo($user)->when($payroll !== 'all', fn ($q) => $q->where('CHECK_DATE', $payroll));
        $summary = [
            'records'  => $base()->count(),
            'deducted' => (float) $base()->sum('DEDUCTION_AMOUNT'),
            'balance'  => (float) $base()->sum('BALANCE_AMOUNT'),
            'stop'     => $base()->where('HAS_STOP_PAY', 1)->count(),
        ];

        $query = $base()->with('loan')
            ->when($stopOnly, fn ($q) => $q->where('HAS_STOP_PAY', 1))
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('CHECK_NUMBER', 'like', "%$search%")->orWhere('LOAN_ID', 'like', "%$search%")
                  ->orWhere('FIRST_DATE', 'like', "%$search%")->orWhere('LAST_NAME', 'like', "%$search%")
                  ->orWhere('VOTE_NAME', 'like', "%$search%");
            }));
        $deductions = $query->orderByDesc('CHECK_DATE')->orderByDesc('ID')->paginate($perPage)->withQueryString();

        $batches = PayrollBatch::withCount('deductions')
            ->when($payroll !== 'all', fn ($q) => $q->where('BATCH_PAYMENT_DATE', $payroll))
            ->orderByDesc('ID')->limit(20)->get();

        return view('admin.deductions.payroll', compact('deductions', 'dates', 'payroll', 'stopOnly', 'search', 'perPage', 'summary', 'batches'));
    }

    public function getStoppedDeductions(Request $request)
    {
        if (!LoanDeduction::tableReady()) {
            return redirect()->route('home')->with('success', '<b>NOT AVAILABLE</b>, update the ESS gateway first (it creates the LOAN_DEDUCTIONS columns).');
        }
        $filter = array_key_exists($request->query('filter'), LoanDeduction::FILTERS) ? $request->query('filter') : 'pending';
        $search = trim((string) $request->query('q', ''));
        $perPage = in_array((int) $request->query('per_page'), [15, 25, 50, 100]) ? (int) $request->query('per_page') : 15;

        $counts = LoanDeduction::counts(Auth::user());
        $atRisk = (float) LoanDeduction::applyFilter(LoanDeduction::visibleTo(Auth::user()), 'pending')->sum('BALANCE_AMOUNT');

        $query = LoanDeduction::applyFilter(LoanDeduction::visibleTo(Auth::user())->with('loan'), $filter);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('CHECK_NUMBER', 'like', "%$search%")
                  ->orWhere('LOAN_NUMBER', 'like', "%$search%")
                  ->orWhere('APPLICATION_NUMBER', 'like', "%$search%")
                  ->orWhereHas('loan', fn ($l) => $l->where('FIRST_NAME', 'like', "%$search%")->orWhere('LAST_NAME', 'like', "%$search%"));
            });
        }
        $deductions = $query->orderByDesc('ID')->paginate($perPage)->withQueryString();

        return view('admin.deductions.stopped', [
            'deductions' => $deductions,
            'filter'     => $filter,
            'search'     => $search,
            'counts'     => $counts,
            'atRisk'     => $atRisk,
            'perPage'    => $perPage,
        ]);
    }

    /** Marks a deduction stop as reviewed by MCB staff, with remarks (e.g. the recovery action taken). */
    public function reviewStoppedDeduction(Request $request)
    {
        $request->validate([
            'id'      => ['required', 'integer'],
            'remarks' => ['required', 'string', 'min:5', 'max:500'],
        ]);
        if (Auth::user()->role == 'Reports') {
            return back()->with('success', '<b>FAILED</b>, your role cannot review deduction stops.');
        }
        $stop = LoanDeduction::find($request->id);
        if ($stop == null) {
            return back()->with('success', '<b>FAILED</b>, deduction stop not found.');
        }
        if (Auth::user()->role == 'Branch Operations' && optional($stop->loan)->INIT_BRANCH != Auth::user()->branch_id) {
            return back()->with('success', '<b>FAILED</b>, this loan belongs to another branch.');
        }
        if ($stop->REVIEWED_DATE) {
            return back()->with('success', '<b>FAILED</b>, this deduction stop was already reviewed by ' . e($stop->REVIEWED_BY) . '.');
        }
        $stop->REVIEWED_BY = self::userDisplayName();
        $stop->REVIEWED_DATE = now('Africa/Dar_es_Salaam')->format('Y-m-d H:i:s'); // same local time as RECEIVED_DATE
        $stop->REVIEW_REMARKS = $request->remarks;
        $stop->save();
        return back()->with('success', '<b>SUCCESS</b>, deduction stop for loan ' . e($stop->LOAN_NUMBER) . ' marked as reviewed.');
    }

    /** Restructure calculator page: the officer tries tenures / instalments before sending the proposal to ESS. */
    public function restructureCalculator($id)
    {
        $loan = LOANAPPLICATION::find($id);
        if ($loan == null) {
            return redirect()->route('loans-disbursed')->with('success', '<b>FAILED</b>, loan not found.');
        }
        if ($loan->TRANSACTION_STATUS !== 'Disbursed') {
            return redirect('v1/properties/staff/registration/profile/' . $id)->with('success', '<b>FAILED</b>, only a disbursed loan can be restructured.');
        }
        return view('admin.restructuring.propose', ['loan' => $loan]);
    }

    public function proposeRestructure(Request $request)
    {
        $request->validate([
            'id'          => ['required', 'integer'],
            'tenure'      => ['nullable', 'integer', 'min:1'],
            'installment' => ['nullable', 'numeric', 'min:1'],
            'reason'      => ['required', 'string', 'max:255'],
        ]);

        $profile = 'v1/properties/staff/registration/profile/' . $request->id;

        if (Auth::user()->role != 'Credit Officer') {
            return redirect($profile)->with('success', '<b>FAILED</b>, only a Credit Officer can propose a restructure.');
        }
        if (empty($request->tenure) == empty($request->installment)) {
            return redirect($profile)->with('success', '<b>FAILED</b>, enter either the new tenure or the new monthly instalment (not both).');
        }

        $loan = LOANAPPLICATION::find($request->id);
        if ($loan == null || $loan->TRANSACTION_STATUS !== 'Disbursed') {
            return redirect($profile)->with('success', '<b>FAILED</b>, only a disbursed loan can be restructured.');
        }

        $body = json_encode([
            'loanApplication' => $loan->LOAN_ID,
            'tenure'          => empty($request->tenure) ? null : (int) $request->tenure,
            'installment'     => empty($request->installment) ? 0 : (float) $request->installment,
            'reason'          => $request->reason,
        ]);

        try {
            $response = EsbClient::SendesbRequestbody($body, 'restructure');
            if ($response != null && isset($response->statusCode) && $response->statusCode === 200) {
                return redirect($profile)->with('success', '<b>SUCCESS</b>, ' . e($response->statusDescription));
            }
            $description = ($response != null && isset($response->statusDescription)) ? $response->statusDescription : 'No response from the ESS gateway';
            return redirect($profile)->with('success', '<b>FAILED</b>, ' . e($description));
        } catch (Exception $e) {
            return redirect($profile)->with('success', '<b>FAILED</b>, the ESS gateway is not reachable: ' . e($e->getMessage()));
        }
    }
}
