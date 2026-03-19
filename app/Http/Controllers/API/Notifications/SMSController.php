<?php

namespace App\Http\Controllers\API\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class SMSController extends Controller
{
    // public static function sendTest()
    // {
    //     $AT       = new AfricasTalking(Config::get('custom.constants.notifications.sms.username'), Config::get('custom.constants.notifications.sms.apiKey'));
    //     $sms      = $AT->sms();
    //     $result   = $sms->send(['to'      => '+255766192332', 'from' => Config::get('custom.constants.notifications.sms.from'), 'message' => 'Hi Amani, Your Insurance Sticker No. is 212-222-2222']);
    //     dd(array('result'=>$result, 'sendId'=> Config::get('custom.constants.notifications.sms.from')));
    // }

    public static function passwordHash()
    {
        //return "hollay";
        return Hash::make("Jose@Tuli96!");

    }

    public static function sendRequesttwo()
    {
        
    }
    // public static function send($to, $message)
    // {
    //     $AT       = new AfricasTalking(Config::get('custom.constants.notifications.sms.username'), Config::get('custom.constants.notifications.sms.apiKey'));
    //     $sms      = $AT->sms();
    //     $sms->send(['from' => Config::get('custom.constants.notifications.sms.from'), 'to' => $to, 'message' => $message]);
    // }

    // public static function sendSandBox($to, $message)
    // {
    //     $AT       = new AfricasTalking('sandbox', '435ef8001ece81c07929748932d3928746136ab15bb9c105aa552730b080384e');
    //     $sms      = $AT->sms();
    //     $sms->send(['to' => $to, 'message' => $message]);
    // }

    // public static function newCoverTemplate($customer, $Quotation)
    // {
    //     return "Dear ".$customer->first_name.",\nYou have successfully purchased Insurance from us.\nYour sticker number is : ". $Quotation->sticker_number;
    // }
}
