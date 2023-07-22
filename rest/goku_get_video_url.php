<?php
error_reporting(0);
function getFinalURL($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    $response = curl_exec($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);

    return isset($headers['url']) ? $headers['url'] : $url;
}

try{
    $url = ($_GET["url"]) ? str_replace(" ", "%20", $_GET["url"]) : "";
}catch (\Exception $e){
}

if($url){
    $finalURL = getFinalURL($url);
    echo $finalURL;
}
