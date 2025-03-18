<?php
// Check if the 'url' parameter is provided via $_REQUEST
if (isset($_REQUEST['url'])) {
    $urlToShorten = $_REQUEST['url'];

    // Initialize cURL session
    $ch = curl_init();

    // Set the cURL options
    curl_setopt($ch, CURLOPT_URL, 'https://ulvis.net/API/write/post');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the transfer as a string
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36',
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    // Set the form data
    $postData = [
        'url' => $urlToShorten,
        'custom' => '',
        'password' => '',
        'uses' => '',
        'expire' => '',
        'is_private' => 'false',
        'via' => 'web'
    ];

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

    // Execute cURL and get the response
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        echo json_encode(['error' => 'cURL Error: ' . curl_error($ch)]);
    } else {
        // Decode the JSON response
        $decodedResponse = json_decode($response, true);

        // Check if decoding succeeded
        if (json_last_error() === JSON_ERROR_NONE) {
            // Send JSON response
            header('Content-Type: application/json');
            echo json_encode($decodedResponse, JSON_PRETTY_PRINT);
        } else {
            // If the response is not valid JSON, return it as-is
            echo json_encode(['error' => 'Invalid JSON response', 'response' => $response]);
        }
    }

    // Close cURL session
    curl_close($ch);
} else {
    // If no URL parameter is provided, return an error in JSON format
    echo json_encode(['error' => 'No URL provided in the request']);
}
?>
