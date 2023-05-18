<?php
$URL = 'http://bdiptv.net/';

function init(){
    global $URL;
    return getChannelsFromUrl($URL);
}


function getChannelsFromUrl($siteUrl){

    $takeArr = [];
    $counter = 0;

    $html = file_get_contents($siteUrl);

    if(!$html)
        return [];


    $dom = new DOMDocument();
    libxml_use_internal_errors(true);  // Disable error reporting for invalid HTML


    $dom->loadHTML($html);
    libxml_clear_errors();  // Clear any parsing errors


    $xpath = new DOMXPath($dom);


    $divClassName = "item_content";
    $divQuery = "//div[contains(@class, '$divClassName')]";
    $divNodes = $xpath->query($divQuery);


    foreach ($divNodes as $divNode) {
        $object = new stdClass();
        $anchorTags = $divNode->getElementsByTagName('a');


        foreach ($anchorTags as $anchorTag) {
            $href = $anchorTag->getAttribute('onclick');
            $text = $anchorTag->nodeValue;
            echo "Link: $href, Text: $text" . PHP_EOL;

            $explodedLink = explode("tv.location.href='play.php?stream=", $href);

            if($explodedLink && isset($explodedLink[1])){
                $object->id = $counter;
                if (strpos($explodedLink[1], "'") !== false) {
                    $object->url = substr($explodedLink[1], 0, -1);
                }else{
                    $object->url = $explodedLink[1];
                }

                $object->title = formatTitle($explodedLink[1]);
                $object->thumb = getThumbLink($divNode);


                $takeArr[] = $object;
                $counter++;
            }
        }
    }

    return $takeArr;

}

function formatTitle($slug)
{
    if(!$slug || $slug == '')
        return 'N/A';
    return ucwords(str_replace('-', ' ', str_replace("'", '', $slug)));
}

function getThumbLink($divNode){
    global $URL;

    $imageTags = $divNode->getElementsByTagName('img');
    foreach ($imageTags as $imageTag) {
        $src = $imageTag->getAttribute('src');
        if($src){
            return $URL.$src;
        }
    }

    return $src;
}
