<?php

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


// Initialize cURL
$ch = curl_init();

// Set the URL
curl_setopt($ch, CURLOPT_URL, "https://fibwatch.online/");

// Set User-Agent to mimic a real browser
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3");

// Enable cookie handling
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

// Return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL
$response = curl_exec($ch);

// Check for errors
if(curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
} else {
    echo $response; // Output the response
}

// Close cURL session
curl_close($ch);
?>
