<?php

function webviewItems(){
    $SITES = [
        'https://mlsbd.vip/category/bengali/',
        'https://mlsbd.vip/category/tv-series/',
        'https://mlsbd.vip/category/hindi-dubbed/',
        'https://mlsbd.vip/category/hoichoi-originals/',
        'https://mlsbd.vip/category/bengali-dubbed/'
    ];
    $CATEGORY = [
        'Bangla',
        'English',
        'Dual%20Audio',
        'Bangla',
        'Dual%20Audio'
    ];

    $FULL_FINAL_LIST = [];
    $counter = 0;

    foreach($SITES as $key=>$site){
        $videoSiteUrls = getDataFromArticleUsingUrl($site);

        if($videoSiteUrls && !empty($videoSiteUrls)){

            foreach($videoSiteUrls as $videoSiteUrl){
                $movieUrlAndTimestamp = getAnchor($videoSiteUrl, 'dood.yt');

                if($movieUrlAndTimestamp){
                    echo "got movie url on :::".$movieUrlAndTimestamp['url']."\n\n";

                    //"id":0,"video":"http:\/\/10.16.100.213\/iccftps13\/iccftps13sasd1\/Movies\/English\/Transformers%20Revenge%20of%20the%20Fallen%20(2009)%201080p%20BluRay.mp4","timestamp":1683449220,"size":3972844748.8,"cat":"English","name":"Transformers Revenge of the Fallen (2009) 1080p BluRay.mp4","date":"2023-05-07 08:47  ","year":0}
                    $FULL_FINAL_LIST[$counter]["video"] = $videoSiteUrl;
                    $FULL_FINAL_LIST[$counter]["webview"] = $movieUrlAndTimestamp['url'];
                    $FULL_FINAL_LIST[$counter]["cat"] = $CATEGORY[$key];


                    if(isset($movieUrlAndTimestamp['timestamp'])){
                        $FULL_FINAL_LIST[$counter]["timestamp"] = $movieUrlAndTimestamp['timestamp'];
                        $FULL_FINAL_LIST[$counter]['year'] = date("Y", $movieUrlAndTimestamp['timestamp']);
                    }else{
                        if(strpos($videoSiteUrl, "2023") !== false){
                            $year = 2023;
                        } elseif(strpos($videoSiteUrl, "2022") !== false){
                            $year = 2022;
                        } elseif(strpos($videoSiteUrl, "2021") !== false){
                            $year = 2021;
                        } elseif(strpos($videoSiteUrl, "2020") !== false){
                            $year = 2020;
                        } elseif(strpos($videoSiteUrl, "2019") !== false){
                            $year = 2019;
                        } elseif(strpos($videoSiteUrl, "2018") !== false){
                            $year = 2018;
                        } elseif(strpos($videoSiteUrl, "2017") !== false){
                            $year = 2017;
                        } elseif(strpos($videoSiteUrl, "2016") !== false){
                            $year = 2016;
                        } else{
                            $year = 0;
                        }
                        $FULL_FINAL_LIST[$counter]["year"] = $year;
                    }


                    echo "$counter<pre>";
                    print_r($FULL_FINAL_LIST[$counter]);
                    echo "</pre>";
                    $counter++;

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
        $object->timestamp = $itemGet['timestamp'];
        $object->year = $itemGet['year'];
        $object->cat = $itemGet['cat'];

        $FULL_FINAL[] = $object;

    }
    return $FULL_FINAL;


}



function getAnchor($url, $pattern='dood.yt')
{
    echo "Generating movie url from :::".$url."\n\n";
    $html = file_get_contents($url);

    $takeUrl = [];

    $dom = new DOMDocument();
    $dom->loadHTML($html);


    $xpath = new DOMXPath($dom);
    $spanList = $xpath->query('//span[@itemprop="datePublished"]');

    foreach ($spanList as $span) {
        $datePublished = trim($span->nodeValue);

        if($datePublished){
            $takeUrl['timestamp'] = strtotime($datePublished);
        }

    }

    $anchors = $dom->getElementsByTagName('a');

    foreach ($anchors as $anchor) {
        $href = $anchor->getAttribute('href');

        if (
            (strpos($href, 'dood.yt') !== false)
        )
        {
            $takeUrl['url'] = $href;
            break;
        }
        if (
            (strpos($href, 'dood.re') !== false)
        )
        {
            $takeUrl['url'] = $href;
            break;
        }

        if (
            (strpos($href, 'lvturbo.com') !== false)
        )
        {
            $takeUrl['url'] = $href;
            break;
        }


        if (
            (strpos($href, 'ok.ru') !== false)
        )
        {
            $takeUrl['url'] = $href;
            break;
        }
    }
    return $takeUrl;
}

function getDataFromArticleUsingUrl($url){
    echo "Generating from site:::".$url."\n\n";

    //return ['https://mlsbd.vip/pattathu-arasan-2022-hindi-dubbed/'];

    $finalArr = [];

    try {
        $html = file_get_contents($url);

        if (!$html)
            return [];

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        $articles = $dom->getElementsByTagName('article');

        foreach ($articles as $article) {
            $anchors = $article->getElementsByTagName('a');
            foreach ($anchors as $anchor) {
                $href = $anchor->getAttribute('href');
                if (
                    (strpos($href, 'category') == false) &&
                    (strpos($href, 'featured') == false) &&
                    (strpos($href, 'genre') == false) &&
                    (strpos($href, '18-') == false) &&
                    (strpos($href, 'release') == false)
                )
                {
                    $finalArr[] = $href;
                }
            }
        }
        return array_values(array_unique($finalArr));
    }catch (\Exception $e){
    }
}
