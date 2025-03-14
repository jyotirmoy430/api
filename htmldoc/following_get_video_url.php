<?php
error_reporting(0);

$allowed_origins = ["http://jbmovies.rf.gd", "https://jbmovies.rf.gd"];

// Check the Origin header
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = $_SERVER['HTTP_ORIGIN'];
    
    // Allow only requests from http://jbmovies.rf.gd
    if(in_array($origin, $allowed_origins)){
        header("Access-Control-Allow-Origin: $origin");
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

$url = ($_GET["url"]) ? str_replace(" ", "%20", $_GET["url"]) : "";
$finalURL = '';

if($url){
    $finalURL = getFinalURL($url);
}
echo $finalURL;

function getFinalURL($url) {
    $html = file_get_contents($url);

    $dom = new DOMDocument;
    $dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $videoTags = $xpath->query('//source');
    $videoSource = '';
    if ($videoTags->length > 0) {
        $videoSource = $videoTags->item(0)->getAttribute('src');
    }
    return str_replace("https://fb45.b-cdn.net", "https://er56.b-cdn.net", $videoSource);
}
try{
    $url = ($_GET["url"]) ? str_replace(" ", "%20", $_GET["url"]) : "";
}catch (\Exception $e){
}
