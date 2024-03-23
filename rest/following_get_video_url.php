<?php
error_reporting(0);

$url = ($_GET["url"]) ? str_replace(" ", "%20", $_GET["url"]) : "";
$finalURL = '';


if($url){
    $finalURL = getFinalURL($url);
}
echo $finalURL;

function getFinalURL($url) {
    $html = file_get_contents($url);
    file_put_contents('output.html', $html);
    $dom = new DOMDocument;
    $dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $videoTags = $xpath->query('//source');
    $videoSource = '';
    if ($videoTags->length > 0) {
        $videoSource = $videoTags->item(0)->getAttribute('src');
    }
    return $videoSource;
}
try{
    $url = ($_GET["url"]) ? str_replace(" ", "%20", $_GET["url"]) : "";
}catch (\Exception $e){
}
