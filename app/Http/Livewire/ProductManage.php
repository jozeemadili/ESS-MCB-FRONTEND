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
        return $this->sendToEss('product/', 'Publish');
    }

    /**
     * Calls the gateway (product/{id} = PRODUCT_DETAIL, decommission/{id} = PRODUCT_DECOMMISSION). The gateway saves the
     * new status only when ESS replies 8000; on any other reply it answers HTTP 400/500 with ESS's reason in the body.
     */
    private function sendToEss(string $path, string $action)
    {
        $id = $this->products->ID;
        try {
            $response = EsbClient::SendesbRequesturl($path.$id);
            $ok = $response && (int) ($response->statusCode ?? 0) === 200;
            $text = $response->statusDescription ?? 'No reply from the gateway';
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $body = $e->hasResponse() ? json_decode((string) $e->getResponse()->getBody()) : null;
            $ok = false;
            $text = $body->statusDescription ?? 'The gateway could not be reached';
            \Log::warning("Product {$id} {$action} failed: ".$e->getMessage());
        } catch (\Throwable $e) {
            $ok = false;
            $text = 'The gateway could not be reached';
            \Log::warning("Product {$id} {$action} failed: ".$e->getMessage());
        }
        return $ok
            ? to_route('products-condtions', ['id' => $id])->with('success', '<b>SUCCESS</b>, '.e($text))
            : to_route('products-condtions', ['id' => $id])->with('error', '<b>'.e($action).' FAILED</b>, '.e($text));
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
        return $this->sendToEss('decommission/', 'Decommission');
    }
}
