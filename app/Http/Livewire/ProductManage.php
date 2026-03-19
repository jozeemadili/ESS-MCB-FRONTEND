<?php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\PRODUCTCONDITION;
use Livewire\Component;
use App\TIRAClient\Scripts\Classes\EsbClient;

class ProductManage extends Component
{
    public $products;
    public $productId;
    public $name, $code, $minimumTenure, $maximumTenure, $interestRate, $processingFeeRate, $insuranceRate;
    public $minimumAmount, $maximumAmount, $currency, $repaymentType, $description, $isExecutive;

    // Rules for validation
    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:255',
        'minimumTenure' => 'required|integer',
        'maximumTenure' => 'required|integer',
        'interestRate' => 'required|numeric',
        'processingFeeRate' => 'required|numeric',
        'insuranceRate' => 'required|numeric',
        'minimumAmount' => 'required|numeric',
        'maximumAmount' => 'required|numeric',
        'currency' => 'required|string',
        'repaymentType' => 'required|string',
        'description' => 'nullable|string',
        'isExecutive' => 'required|boolean',
    ];
    public function render()
    {
        return view('livewire.product-manage');
    }
    public function mount($productId)
    {
        $product = Product::find($productId);

        $this->productId = $product->ID;
        $this->name = $product->NAME;
        $this->code = $product->CODE;
        $this->minimumTenure = $product->MINIMUM_TENURE;
        $this->maximumTenure = $product->MAXIMUM_TENURE;
        $this->interestRate = $product->INTEREST_RATE;
        $this->processingFeeRate = $product->PROCESSING_FEE_RATE;
        $this->insuranceRate = $product->INSURANCE_RATE;
        $this->minimumAmount = $product->MINIMUM_AMOUNT;
        $this->maximumAmount = $product->MAXIMUM_AMOUNT;
        $this->currency = $product->CURRENCY;
        $this->repaymentType = $product->REPAYMENT_TYPE;
        $this->description = $product->DESCRIPTION;
        $this->isExecutive = $product->IS_EXECUTIVE;

        // dd($this->name);
    }
    public function publishProduct()
    {
        // dd("jose");
            // $endPoint='http://172.16.3.198:30002/api/v2/hmcis/product/'.$this->products->ID.'';
            $endPoint='product/'.$this->products->ID.'';
            $response = EsbClient::SendesbRequesturl($endPoint); 
            $status = $response->status;
            $statusDescription = $response->statusDescription;
            if ($response->statusCode === 200) 
                {
                    return to_route('products-condtions',['id' => $this->products->ID])->with('success', ' <b>SUCCESS</b>, '.$statusDescription.' ');
                }
            elseif($response->statusCode === 200)
                {
                    return to_route('products-condtions',['id' => $this->products->ID])->with('success', ' <b>FAILED</b>, '.$statusDescription.' ');
      
                }
            else
                {
                    return to_route('products-condtions',['id' => $this->products->ID])->with('success', ' <b>FAILED</b>, '.$statusDescription.' ');
                }
    }
     // Update product method
     public function updateProduct()
     {
         // Validate input fields
        //  $this->validate();
//  dd("jose madili");
         // Update product
        //  $product = Product::find($this->productId);
        //  $product->NAME = $this->name;
        //  $product->CODE = $this->code;
        //  $product->MINIMUM_TENURE = $this->minimumTenure;
        //  $product->MAXIMUM_TENURE = $this->maximumTenure;
        //  $product->INTEREST_RATE = $this->interestRate;
        //  $product->PROCESSING_FEE_RATE = $this->processingFeeRate;
        //  $product->INSURANCE_RATE = $this->insuranceRate;
        //  $product->MINIMUM_AMOUNT = $this->minimumAmount;
        //  $product->MAXIMUM_AMOUNT = $this->maximumAmount;
        //  $product->CURRENCY = $this->currency;
        //  $product->REPAYMENT_TYPE = $this->repaymentType;
        //  $product->DESCRIPTION = $this->description;
        //  $product->IS_EXECUTIVE = $this->isExecutive;
 
        //  $product->save();
 
        //  // Flash message and close modal
        //  session()->flash('message', 'Product updated successfully!');
        //  $this->emit('closeModal'); // Trigger modal close
     }
    public function decamisionProduct()
    {
        // http://172.16.3.198:30002/api/v2/hmcis/
        $endPoint='decommission/'.$this->products->ID.'';
        $response = EsbClient::SendesbRequesturl($endPoint);
        $status = $response->status;
        $statusDescription = $response->statusDescription;
        if ($response->statusCode === 200) 
            {
                return to_route('products-condtions',['id' => $this->products->ID])->with('success', ' <b>SUCCESS</b>, '.$statusDescription.' ');
            }
        else
            {
                return to_route('products-condtions',['id' => $this->products->ID])->with('success', ' <b>FAILED</b>, '.$statusDescription.' ');
            }
    }
}
