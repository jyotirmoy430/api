<?php
error_reporting(0);

$allowed_origin = "http://jbmovies.rf.gd";

// Check the Origin header
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = $_SERVER['HTTP_ORIGIN'];
    
    // Allow only requests from http://jbmovies.rf.gd
    if ($origin === $allowed_origin) {
        header("Access-Control-Allow-Origin: $allowed_origin");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
    } else {
        // If the request comes from an unauthorized origin, block it
        header("HTTP/1.1 403 Forbidden");
        echo json_encode(['error' => 'Unauthorized origin']);
        exit;
    }
} else {
    // Block requests with no Origin header
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(['error' => '403 Forbidden']);
    exit;
}

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
