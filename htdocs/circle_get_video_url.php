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

$host = "http://15.1.1.50:5000/api/posts";
$url = ($_GET["url"]) ? str_replace(" ", "%20", $_GET["url"]) : "";
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


