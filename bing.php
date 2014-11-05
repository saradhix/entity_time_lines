<?php
$query = "Mahatma Gandhi";


$accountKey = 'nYgtyPLwow/1W0XWJETMMQSRCwFxHyXmThFW23WOvY8';
/*
$request = "http://api.search.live.net/json.aspx?Appid=<$accountKey>
&sources=image&query=" . urlencode( $query);
$response  = file_get_contents($request);
$jsonobj  = json_decode($response);
print_r($jsonobj);
exit();
*/
$accountKey = 'nYgtyPLwow/1W0XWJETMMQSRCwFxHyXmThFW23WOvY8';
$serviceRootURL =  'https://api.datamarket.azure.com/Bing/Search/';  
$webSearchURL = $serviceRootURL . 'Web?$format=json&Query=';

$request = $webSearchURL . "%27" . urlencode( "$query" ) . "%27";

$process = curl_init($request);
curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($process, CURLOPT_USERPWD,  "$accountKey:$accountKey");
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($process);
print_r($response);
$response = json_decode($response);
echo "Printing response\n";
print_r($response);

foreach( $response->d->results as $result ) {
  $url = $result->Url;
  $title = $result->Title;

  echo "URL=$url title=$title\n";
}
?>
