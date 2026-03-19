<?php
class DB
{
  public static function gr($urlParams,$dataToBeSent, $baseUrl)
  {
      //self::saveLogRequest($urlParams."\n".json_encode($dataToBeSent));
       
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $baseUrl.$urlParams,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_SSL_VERIFYHOST=>FALSE,
      CURLOPT_SSL_VERIFYPEER=>FALSE,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => self::wellFormat($dataToBeSent),
      CURLOPT_HTTPHEADER => array(
        "Accept: application/json",
        "Content-Type: application/x-www-form-urlencoded"
      ),
    ));

    $response = curl_exec($curl);
    
      //self::saveLogResponse(json_encode($response));

    curl_close($curl);
    return $response;

    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://mobile.ubx.co.tz/api/mobile/?=',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'service_id=0&udid=a0d4bd2fab25afa7&app_version=1.6.0',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded',
    'Cookie: webfarm=app-server-1'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
  }

  public static function wellFormat($a)
  {
    $output="";
    foreach ($a as $key => $value)
    {
      $output .=$key."=".$value."&";
    }
    return substr($output,0,-1);
  }
  
  
    public static function saveLogResponse($contents)
  {
        $filename = date("YmdHis")."_Response_mbLOgs.txt";
        $myfile = fopen($filename, "w") or die("Unable to open file!");
        $txt = $contents."\n";
        fwrite($myfile, $txt);
        $txt = "getUserPCName : ".self::getUserPCName()."\nIpAddress : ".self::getUserIpAddr();
        fwrite($myfile, $txt);
        fclose($myfile);
  }
  
      public static function saveLogRequest($contents)
  {
        $filename = date("YmdHis")."_Request_mbLOgs.txt";
        $myfile = fopen($filename, "w") or die("Unable to open file!");
        $txt = $contents."\n";
        fwrite($myfile, $txt);
        $txt = "getUserPCName : ".self::getUserPCName()."\nIpAddress : ".self::getUserIpAddr();
        fwrite($myfile, $txt);
        fclose($myfile);
  }
  
  	public static function getUserPCName()
	{
		return php_uname('n');
	}

	public static function getUserIpAddr()
	{
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else
		{
				try
				{
					//$ip = $_SERVER['REMOTE_ADDR'];
					$ip=isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 'SELF';
				} catch (Exception $e)
				{
					$ip = "CRONE JOB";
				}

    }

    return $ip;
 }

}
