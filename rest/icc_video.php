<?php
error_reporting(0);
//$url = ($_GET["url"]) ? str_replace(" ", "%20", $_GET["url"]) : "";
$url = 'http://10.16.100.244/player.php?play=28624';

$videos = getVideos($url);
$finalVideos = ($videos && count($videos) > 0) ? $videos: [];

echo json_encode($videos, JSON_PRETTY_PRINT);


function getVideos($url){
    $html = file_get_contents($url);

    if ($html === FALSE) {
        die('Error fetching content');
    }

    $doc = new DOMDocument();
    $doc->loadHTML($html);

    $xpath = new DOMXPath($doc);

    $videoElement = $xpath->query('//video[@id="video-id"]')->item(0);

    if ($videoElement) {
        $sourceElements = $xpath->query('.//source', $videoElement);
        $sources = [];

        foreach ($sourceElements as $sourceElement) {
            $src = $sourceElement->getAttribute('src');
            $title = $sourceElement->getAttribute('title');
            $type = $sourceElement->getAttribute('type');

            $source = [
                'src' => $src,
                'title' => $title,
                'type' => $type,
            ];

            $sources[] = $source;
        }

        return $sources;
    } else {
        return "No vide";
    }
}
