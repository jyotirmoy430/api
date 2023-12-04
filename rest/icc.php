<?php
error_reporting(0);
$URL = 'http://10.16.100.244';
//$keyword = ($_GET["keyword"]) ? str_replace(" ", "%20", $_GET["keyword"]) : "";
$keyword = "money heist";


$tokenValue = getToken();


$data = array(
    'token' => $tokenValue,
    'psearch' => $keyword
);


$htmlResponseString = getContent($URL, $data);

$array = getItems($htmlResponseString);

$items = getItems($htmlResponseString);
$finalItems = ($items && count($items) > 0) ? $items: [];

echo json_encode($finalItems, JSON_PRETTY_PRINT);





function getItems($html){
    global $URL;
    $doc = new DOMDocument();
    $doc->loadHTML($html);

    $xpath = new DOMXPath($doc);

    $postElements = $xpath->query('//div[@class="post post-height hover-img-scale wow fadeInUp"]');

    $array = [];

    foreach ($postElements as $index => $postElement) {
        $poster = $xpath->evaluate('string(.//img/@src)', $postElement);
        $title = $xpath->evaluate('string(.//div[@class="title"])', $postElement);
        $player = $xpath->evaluate('string(.//a[@class="image"]/@href)', $postElement);

        $postArray = [
            "poster" => $URL. '/' . trim($poster),
            "title" => trim($title),
            "player" => $URL. '/' . trim($player),
        ];
        $array[] = $postArray;
    }

    return $array;
}
function getContent($url, $data){
    $postData = http_build_query($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return false;
    }
    curl_close($ch);
    return $response;
}

function getToken() {
    global $URL;

    // Create a stream context with the specified IP address
    $context = stream_context_create([
        'socket' => [
            'bindto' => '45.120.96.164', // Set the IP address here
        ],
    ]);

    // Use the context when making the request
    $html = file_get_contents($URL, false, $context);

    if ($html === FALSE) {
        return false;
    }

    $pattern = '/<input type="hidden" name="token" value="([^"]+)">/';
    if (preg_match($pattern, $html, $matches)) {
        return $matches[1];
    }

    return false;
}

