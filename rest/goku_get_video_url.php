<?php
error_reporting(0);
$SITE_URL = "https://goku.sx";
function getFinalURL($url) {
    $headers = get_headers($url, 1);

    // Check if "Location" header is present
    if (isset($headers['Location'])) {
        // If it's an array, use the last element as the final URL
        if (is_array($headers['Location'])) {
            return end($headers['Location']);
        } else {
            return $headers['Location'];
        }
    } else {
        // If "Location" header is not present, use the original URL
        return $url;
    }
}

try{
    $url = ($_GET["url"]) ? str_replace(" ", "%20", $_GET["url"]) : "";
}catch (\Exception $e){
}

if($url){
    $finalURL = getFinalURL($url);
    echo $SITE_URL.$finalURL;
}
