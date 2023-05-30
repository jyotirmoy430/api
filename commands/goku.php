<?php
$SITE_URL = 'https://goku.sx';
function gokuItems(){

    $jsonFilePath = 'listnwebview.json';

    $jsonData = file_get_contents($jsonFilePath);

    $decodedExisting = json_decode($jsonData);

    $CHECK_EXISTING_URL_ARR = [];

    $EXISTING = [];

    if($decodedExisting){
        foreach($decodedExisting as $decoded){
            if($decoded && $decoded->webview){
                $EXISTING[] = $decoded;
                $CHECK_EXISTING_URL_ARR[] = $decoded->video;
            }
        }
    }



    $SITES = [
        'https://goku.sx/genre/action-10/'
    ];
    $CATEGORY = [
        '',
    ];

    $PAGES = [
        0
    ];

    $FULL_FINAL_LIST = [];
    $counter = 0;
    $NEW_GENERATED_URLS = [];

    foreach($SITES as $key=>$site){
        $PAGE_FOR_SPECIFIC_CAT = $PAGES[$key];


        for($i = 0; $i<=$PAGE_FOR_SPECIFIC_CAT; $i++){
            $page = $i;

            if($page == 1)
                continue;

            if($page == 0){
                $siteTake = $site;
            }else{
                $siteTake = $site.'page/'.$page;
            }






            $videoSiteUrls = getDataFromArticleUsingUrl($siteTake);

            if($videoSiteUrls && !empty($videoSiteUrls)) {

                foreach ($videoSiteUrls as $videoSiteUrl) {
                    if (in_array($videoSiteUrl["webview"], $CHECK_EXISTING_URL_ARR)) {
                        echo "in array:::" . $videoSiteUrl . "\n\n";
                        continue;
                    }

                    if(in_array($videoSiteUrl['url'], $NEW_GENERATED_URLS)){
                        echo "in array:::".$videoSiteUrl."\n\n";
                        continue;
                    }

                    echo "got movie url on :::".$videoSiteUrl['webview']."\n\n";

                    $FULL_FINAL_LIST[$counter] = $videoSiteUrl;
                    $counter++;
                    $NEW_GENERATED_URLS[]=$videoSiteUrl['webview'];
                }
            }
        }

    }




    $FULL_FINAL = [];
    foreach($FULL_FINAL_LIST as $key=>$itemGet){
        $object = new stdClass();
        $object->id = $key;
        $object->video = $itemGet['video'];
        $object->webview = $itemGet['webview'];
        //$object->timestamp = $itemGet['timestamp'];
        $object->year = $itemGet['year'];
        $object->cat = "English";
        $object->goku = 1;

        if($itemGet && $itemGet['poster']){
            $object->poster = $itemGet['poster'];
        }

        $EXISTING[] = $object;

    }


    return $EXISTING;


}



function getDataFromArticleUsingUrl($url){
    global $SITE_URL;
    echo "Generating from site:::".$url."\n\n";


    $finalArr = [];
    $counter = -1;

    try {
        $html = file_get_contents($url);

        if (!$html)
            return [];

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        $divs = $dom->getElementsByTagName('div');

        $className = 'item ';

        foreach ($divs as $div) {
            // Check if the div has the specified class name
            if ($div->hasAttribute('class') && strpos($div->getAttribute('class'), $className) !== false) {

                $anchors = $div->getElementsByTagName('a');
                foreach ($anchors as $anchor) {
                    $href = $anchor->getAttribute('href');
                    if (
                        (strpos($href, 'watch-movie') != false) &&
                        (strpos($href, 'featured') == false) &&
                        (strpos($href, 'genre') == false) &&
                        (strpos($href, '18-') == false) &&
                        (strpos($href, 'release') == false)
                    )
                    {
                        $counter++;
                        $finalArr[$counter]["webview"] = $SITE_URL.$href;
                        $finalArr[$counter]["goku"] = 1;
                    }

                    $headers = get_headers($finalArr[$counter]["webview"], 1);

                    if (isset($headers['Location'])) {
                        $redirectUrl = is_array($headers['Location']) ? end($headers['Location']) : $headers['Location'];
                        $finalArr[$counter]["webview"] = $SITE_URL.$redirectUrl;
                    }


                    $h3s = $anchor->getElementsByTagName('h3');
                    foreach ($h3s as $h3) {
                        $finalArr[$counter]["video"] = $h3->nodeValue;
                    }

                    $images = $anchor->getElementsByTagName('img');
                    foreach ($images as $image) {
                        $src = $image->getAttribute('src');
                        $finalArr[$counter]["poster"] = $src;
                    }

                    $otherDivs = $div->getElementsByTagName('div');
                    foreach ($otherDivs as $otherDiv) {
                        if($otherDiv && $otherDiv->nodeValue && ctype_digit($otherDiv->nodeValue)){
                            $finalArr[$counter]["year"] = $otherDiv->nodeValue;
                        }
                    }
                }
            }
        }
        return $finalArr;
    }catch (\Exception $e){
    }
}
