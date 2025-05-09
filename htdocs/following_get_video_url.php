<?php
error_reporting(0);

session_start();

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


try{
    //add log - movie url
    if($finalURL){
        $parts = explode('[Fibwatch.Com]', $finalURL);
        $filename = end($parts);
        logInfo('watched -> '.$filename);
    }
}catch (\Exception $e){
}


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
    
    $final = str_replace("https://fb45.b-cdn.net", "https://er56.b-cdn.net", $videoSource);
    $final = str_replace("https://thn45.b-cdn.net", "https://er56.b-cdn.net", $final);

    return $final;

    // $oldDomain = parse_url($videoSource, PHP_URL_HOST);
    // $newDomain = "er56.b-cdn.net";
    // $newUrl = str_replace($oldDomain, $newDomain, $videoSource);

    // return $newUrl;
}
try{
    $url = ($_GET["url"]) ? str_replace(" ", "%20", $_GET["url"]) : "";
}catch (\Exception $e){
}



function logInfo($message = '', $fileName = 'log.txt')
{
    try{
        $dt = new DateTime("now", new DateTimeZone("Asia/Dhaka"));
        $time = $dt->format("d M Y H:i:s"); // Format: 05 May 2025 14:10:59
        $mapLink = 'https://www.google.com/maps/@'.$_SESSION['lat'].','.$_SESSION['lon'];
        $formattedMsg = $time. ' -> '. $_SERVER['REMOTE_ADDR'] . ' -> ' . $_SESSION['isp'] . ' -> '.$_SESSION['user']. ' -> '. $_SESSION['country']. ' -> ' . $mapLink . (($message) ? ' -> '. $message : '');
        file_put_contents($fileName, $formattedMsg . "\n\n", FILE_APPEND);
    }catch(\Exception $e){}
}