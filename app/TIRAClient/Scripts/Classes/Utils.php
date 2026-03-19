<?php
namespace App\TIRAClient\Scripts\Classes;

use Carbon\Carbon;
use Exception;
use PhpParser\Node\Expr\Cast\String_;

class Utils
{
    public static function saveLogs($fileName, $logs)
    {
      $myfile = fopen("_logs_".$fileName.".txt", "w") or die("Unable to open file!");
      fwrite($myfile, $logs);
      fclose($myfile);
    }

    public static function dateDifference($start, $end, $whatToReturn = "m")
    {
        try{
            $toDate      = Carbon::parse($start);
            $fromDate    = Carbon::parse($end);
            $days        = $toDate->diffInDays($fromDate);
            $months      = $toDate->diffInMonths($fromDate);
            $years       = $toDate->diffInYears($fromDate);
            return ($whatToReturn == "m" ? $months : ($whatToReturn == "y" ? $years : $days));
        }
        catch(Exception $e)
        {
            return 12;
        }
    }

    

    public static function timeGreeting()
    {
        date_default_timezone_set('Africa/Dar_es_Salaam');
        $time = date("H");
        if ($time < "12") {
            return "Good morning";
        } 
        else if ($time >= "12" && $time < "17") {
            return "Good afternoon";
        } 
        else if ($time >= "17" && $time < "19") {
            return "Good evening";
        } else if ($time >= "19") {
            return "Good night";
        }
    }


    

}
