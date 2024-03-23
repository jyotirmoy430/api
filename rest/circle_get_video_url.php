<?php
error_reporting(0);

$host = "http://15.1.1.50:5000/api/posts";


$url = ($_GET["url"]) ? str_replace(" ", "%20", $_GET["url"]) : "";
//$url = "98562";
$finalURL = '';

if($url){
    $finalURL = getFinalURL($host.'/'.$url);
}
echo $finalURL;

function getFinalURL($url) {
    $result = [];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false); // Exclude headers from response
    $response = curl_exec($curl);

    if(curl_errno($curl)) {
        return [];
    } else {
        // Decode the JSON response
        $data = json_decode($response, true);

        // Check if JSON decoding was successful
        if ($data === null) {
            return [];
        } else {
            $result = $data;
        }
    }

    curl_close($curl);

    if($result && isset($result["content"])){
        return $result["content"];
    }
    return $result;
}
try{
    $url = ($_GET["url"]) ? str_replace(" ", "%20", $_GET["url"]) : "";
}catch (\Exception $e){
}
