<?php

namespace App\Http\Controllers\API\Products;

use App\Http\Controllers\Controller;

use App\Models\BRANCH;
use App\Models\Product;
use App\Models\PRODUCTCONDITION;
use App\Models\User;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function get()
    {
        $products = PRODUCT::orderBy('id', 'desc')->paginate(10);
        return view('admin.products.products-registration',['products' =>$products]);
    }
    public function getProductCondition($id)
    {
        $product = PRODUCT::find($id);
        $productCondtions = PRODUCTCONDITION ::where('PRODUCT_ID', $id)->orderBy('ID','desc')->get();
        return view('admin.products.risks-registration',['responseData' => $productCondtions, 'id'=>(int)$id, 'products'=>$product]);
    }
    public function addCondition(Request $request)
    {
          dd($request);
    }

    public function updateProdyctConditionStatus(Request $request)
    {
        $ProductCondition = PRODUCTCONDITION::find($request->id);
        PRODUCTCONDITION::find($request->id)->delete();  
        return to_route('products-condtions',['id' => $ProductCondition->PRODUCT_ID]);
    }
    public function updateUserStatus(Request $request)
    {
        $User = User::find($request->id);
        $User->status    = $request->status;
        $User->save();
        return to_route('portal-users');
    }
    
    public function updateBranchConditionStatus(Request $request)
    {
        // // dd($request->id);
        // $branch = BRANCH::find($request->id);
        // $branch->forceDelete();
        // $branch = BRANCH::where('ID', $request->id)->first();
        // // dd($branch);
        
        // if ($branch) {
        //     // Update the status to 'Inactive'
        //     $branch->STATUS = 'Inactive';
        //     $saved = $branch->save();  // Save the changes
        
        //     dd($saved); // Check if save was successful
        // }
        $updated = BRANCH::where('ID', $request->id)->update(['STATUS' => $request->status]);

if ($updated) {
    // return response()->json(['message' => 'Branch status updated successfully.']);
    // return to_route('branches-list');
    return to_route('branches-list')->with('message', 'Branch status updated successfully.');

}

      
        // dd($details) ;
       
    }
    public function registerProductCondition(Request $request)
    {
       $cnumber="MCB".date('YmdHis');
      $user = PRODUCTCONDITION::create(
        [
            'CONDITION_NUMBER'         => $cnumber,
            'DATE'                     => date('Y-m-d H:i:s'),
            'DESCRIPTION'              => $request->description,
            'EFFECTIVE_DATE'           => date('Y-m-d H:i:s'),
            'PRODUCT_ID'               => $request->id,
            'STATUS'                   => 'Active'
        ]);

        return to_route('products-condtions',['id' => $request->id])->with('success', 'Condition with Condition number <b>'.strtoupper($cnumber).'</b> Successfully registered');    
    }

    public function editProduct(Request $request)
    {
          // Validate the request data
          $request->validate([
            'name' => 'required|string|max:255',
            'minimumTenure' => 'required|integer|min:1',
            'maximumTenure' => 'required|integer|min:1',
            'interestRate' => 'required|numeric',
            'processingFeeRate' => 'required|numeric',
            'minimumAmount' => 'required|numeric',
            'maximumAmount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        // Find the product by ID
        $product = PRODUCT::findOrFail($request->productId);

        // Update the product with validated data
        $product->update([
            'NAME' => $request->input('name'),
            'MINIMUM_TENURE' => $request->input('minimumTenure'),
            'MAXIMUM_TENURE' => $request->input('maximumTenure'),
            'INTEREST_RATE' => $request->input('interestRate'),
            'PROCESSING_FEE_RATE' => $request->input('processingFeeRate'),
            'MINIMUM_AMOUNT' => $request->input('minimumAmount'),
            'MAXIMUM_AMOUNT' => $request->input('maximumAmount'),
            'DESCRIPTION' => $request->input('description'),
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Product updated successfully.');
    
    }
    public function register(Request $request)
    {
        $user = PRODUCT::create(
            [
                'NAME'                  => $request->name,
                'INTEREST_RATE'         => $request->InterestRate,
                'DATE'                  => date('Y-m-d H:i:s'),
                'CURRENCY'              => $request->ccy,
                'CODE'                  => $request->code,
                'DESCRIPTION'           => $request->description,
                'INSURANCE_RATE'        => $request->insuranceRate,
                'IS_EXECUTIVE'          => intval($request->isExecutive),
                'MAXIMUM_AMOUNT'        => $request->maximumAmount,
                'MAXIMUM_TENURE'        => $request->maximumTenure,
                'MINIMUM_AMOUNT'        => $request->minimumAmount,
                'MINIMUM_TENURE'        => $request->minimumTenure,
                'PROCESSING_FEE_RATE'   => $request->processingFeeRate,
                'REPAYMENT_TYPE'     => $request->repaymentType,
                'STATUS'     => 'Not Published'
            ]);
            return redirect()->route('products-registration')->with('success', 'Product <b>'.strtoupper($request->name).'</b> Successfully registered');  
    }
}
