<?php

namespace App\Http\Controllers\API\Companies;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\LOANAPPLICATION;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\TIRAClient\Scripts\Classes\ESBClient;
use Illuminate\Support\Facades\DB;

class BranchesController extends Controller
{
    public function get()
    {
        $loans_datails= LOANAPPLICATION::orderBy('id','desc')->get();
        return view('admin.intermediaries.branches', ['loans' => $loans_datails]);
       
    }
    
    public function getBranch()
    {
        $loans_datails= BRANCH::orderBy('ID','asc')->paginate(10);
        // $loans_datails = BRANCH::orderBy('ID', 'desc')->get()->groupBy('DISTRICT_CODE');
        // dd($loans_datails);
        return view('admin.intermediaries.branchesutumish', ['loans' => $loans_datails]);
       
    }
    public function getBranchBySearch(Request $request)
    {
        // dd($request->DISTRICT_CODE);
        // $loans_datails= BRANCH::where('DISTRICT_CODE',$request->DISTRICT_CODE)->orderBy('ID','asc')->paginate(10);
        // $loans_datails = BRANCH::where('DISTRICT_CODE', 'DIS16')->orderBy('ID','asc')->paginate(10);
        // dd($loans_datails);

// dd($loans_datails->toSql(), $loans_datails->getBindings());
        // $loans_datails = BRANCH::orderBy('ID', 'desc')->get()->groupBy('DISTRICT_CODE');
        // dd($loans_datails);
//         $query = BRANCH::where('DISTRICT_CODE', $request->DISTRICT_CODE)
//     ->orderBy('ID', 'asc');

// dd($query->toSql(), $query->getBindings()); // Debug the query
// $query = DB::select("SELECT * FROM `BRANCH` WHERE `DISTRICT_CODE` = ? ORDER BY `ID` ASC", ['DIS1']);
// $query = DB::select("SELECT * FROM BRANCH WHERE DISTRICT_CODE = ?", [$request->DISTRICT_CODE]);
// dd($query);
// $loans_datails = $query->paginate(10);
        // return view('admin.intermediaries.branchesutumish', ['loans' => $loans_datails]);

        $perPage = 10; // Number of items per page
        $currentPage = $request->input('page', 1); // Current page from the request
        $offset = ($currentPage - 1) * $perPage;
    
        // Step 1: Get total count
        $total = DB::table('BRANCH')
                    ->where('DISTRICT_CODE', $request->DISTRICT_CODE)
                    ->count();
    
        // Step 2: Fetch paginated results
        $results = DB::select(
            "SELECT * FROM `BRANCH` WHERE `DISTRICT_CODE` = ? LIMIT ? OFFSET ?", 
            [$request->DISTRICT_CODE, $perPage, $offset]
        );
    
        // Step 3: Create an array with pagination metadata
        $data = [
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'last_page' => ceil($total / $perPage),
        ];
    
        // Return the array directly
        // return $data;
    return view('admin.intermediaries.branchesutumish', ['loans' => $data]);

       
    }
    public function search(Request $request)
    {
        return Branch::where('name', 'like', '%' . $request->name. '%')->orderBy('id','desc')->paginate(10);
    }
    public function branchMapping(Request $request)
    {
         $branch = BRANCH::where('BRANCH_CODE',$request->BRANCH_CODE)->first();
         $branch->DISTRICT_CODE  =  $request->DISTRICT_CODE; 
         $branch->save();
            return redirect()->route('branches-list')->with('success', 'Branch <b>'.strtoupper($branch->BRANCH_CODE).'</b> Successfully registered');
    }
    public function branchSearch(Request $request)
    {
        dd($request->BRANCH_CODE);
         $loans_datails = BRANCH::where('BRANCH_CODE',$request->BRANCH_CODE)->paginate(10);
        //  $loans_datails= BRANCH::orderBy('ID','asc')->paginate(10);
         // $loans_datails = BRANCH::orderBy('ID', 'desc')->get()->groupBy('DISTRICT_CODE');
         // dd($loans_datails);
         return view('admin.intermediaries.branchesutumish', ['loans' => $loans_datails]);
        
    }
    
    public function register(Request $request)
    {
        $branch = BRANCH::where('BRANCH_CODE', $request->BRANCH_CODE)->first();
        $branch_name = $branch ? $branch->BRANCH_NAME : null;
        $branch = BRANCH::create(
            [
                'BRANCH_CODE' => $request->BRANCH_CODE,
                'BRANCH_NAME' => $branch_name,
                'DISTRICT_CODE' => $request->DISTRICT_CODE,
                'STATUS' => 'Active'  
            ]);

            return redirect()->route('branches-list')->with('success', 'Branch <b>'.strtoupper($branch->BRANCH_NAME).'</b> Successfully registered');
        }
}
