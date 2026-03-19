<?php

namespace App\Http\Livewire\Components\Vehicle;

use App\TIRAClient\Scripts\Classes\EsbClient;
use Livewire\Component;

class Details extends Component
{
    public $vehicle;
    public $approval_datils;
    public $status;

    public function render()
    {
        return view('livewire.components.vehicle.details');
    }
    
    public function loanInitialApproval()
    {
        dd('helo');
        
    }
    public function liqudateLoanAccount($loanid)
    {
            $endPoint="liquidate/$loanid";
            $response = EsbClient::SendesbRequesturl($endPoint); 
            $status = $response->status;
            $statusDescription = $response->statusDescription;
            if ($response->statusCode === 200) 
                {
                    return to_route('staff-profile',['id' => $loanid])->with('success', ' <b>SUCCESS</b>, '.$statusDescription.' ');
                }
            else
                {
                  return to_route('staff-profile',['id' => $loanid])->with('success', ' <b>FAILED</b>, '.$statusDescription.' ');
                }
    }
}
