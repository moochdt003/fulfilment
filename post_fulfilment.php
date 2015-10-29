<?php
include_once('fulfilment.php');


//API Url
$url = 'http://elogx.v2.project-stage.com/api/incoming.aspx?format=xml';
 
//API credentials
$auth = array("guid"=>"DEFA57A5-3823-4A3A-96FF-0C969CD18DA4", "username"=>"orders@mallofamerica.co.za", "password"=>"testPASS123");

// We encode an array into JSON
$data = array("auth" => $auth, 
              "inbound-shipment"=>"",
              "final-shipment"=>"",
            );  

$data_string = json_encode($data); 
/*
//IMPORTANT - the API takes only one POST parameter - data 
$postdata="data=$data_string";


// Using curl here

$ch = curl_init($url);                                                                      
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); 
    
//Attach our encoded JSON string to the POST fields
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    
//Tell cURL that we want to send a POST request.    
curl_setopt($ch, CURLOPT_POST, 1);         

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      $result=curl_error($ch);
    } else {
      curl_close($ch);
    }
*/

echo $data_string;

//set order id variable
