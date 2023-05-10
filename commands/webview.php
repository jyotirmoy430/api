<?php

function webviewItems(){

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
        'https://mlsbd.vip/category/bengali/',
        'https://mlsbd.vip/category/tv-series/',
        'https://mlsbd.vip/category/hindi-dubbed/',
        'https://mlsbd.vip/category/hoichoi-originals/',
        'https://mlsbd.vip/category/bengali-dubbed/',
        'https://mlsbd.vip/category/animation-movies/',
        'https://mlsbd.vip/release/2022/',
        'https://mlsbd.vip/release/2021/',
        'https://mlsbd.vip/release/2020/',
        'https://mlsbd.vip/release/2019/',
        'https://mlsbd.vip/release/2018/',
        'https://mlsbd.vip/release/2017/',
        'https://mlsbd.vip/release/2016/',
        'https://mlsbd.vip/release/2015/',
        'https://mlsbd.vip/release/2014/',
        'https://mlsbd.vip/release/2013/',
        'https://mlsbd.vip/release/2012/',
        'https://mlsbd.vip/release/2011/',
        'https://mlsbd.vip/release/2010/',
        'https://mlsbd.vip/release/2009/',
        'https://mlsbd.vip/release/2008/',
        'https://mlsbd.vip/release/2007/',
        'https://mlsbd.vip/release/2006/',
        'https://mlsbd.vip/release/2005/',
        'https://mlsbd.vip/release/2004/',
        'https://mlsbd.vip/release/2003/',
        'https://mlsbd.vip/release/2002/',
        'https://mlsbd.vip/release/2001/',
        'https://mlsbd.vip/release/2000/',
    ];
    $CATEGORY = [
        'Bangla',
        'English',
        'Dual%20Audio',
        'Bangla',
        'Dual%20Audio',
        'Animation',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
        'All',
    ];

    $PAGES = [
        7,
        6,
        13,
        0,
        0,
        0,
        18,
        3,
        2,
        2,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
    ];

    $FULL_FINAL_LIST = [];
    $counter = 0;

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

            if($videoSiteUrls && !empty($videoSiteUrls)){

                foreach($videoSiteUrls as $videoSiteUrl){
                    if(in_array($videoSiteUrl, $CHECK_EXISTING_URL_ARR)){
                       echo "in array:::".$videoSiteUrl."\n\n";
                       continue;
                    }
                    $movieUrlAndTimestamp = getAnchor($videoSiteUrl, 'dood.yt');

                    if(
                        $movieUrlAndTimestamp &&
                        $movieUrlAndTimestamp['url'] &&
                        $movieUrlAndTimestamp['url'] != ''
                    )
                    {
                        echo "got movie url on :::".$movieUrlAndTimestamp['url']."\n\n";

                        //"id":0,"video":"http:\/\/10.16.100.213\/iccftps13\/iccftps13sasd1\/Movies\/English\/Transformers%20Revenge%20of%20the%20Fallen%20(2009)%201080p%20BluRay.mp4","timestamp":1683449220,"size":3972844748.8,"cat":"English","name":"Transformers Revenge of the Fallen (2009) 1080p BluRay.mp4","date":"2023-05-07 08:47  ","year":0}
                        $FULL_FINAL_LIST[$counter]["video"] = $videoSiteUrl;
                        $FULL_FINAL_LIST[$counter]["webview"] = $movieUrlAndTimestamp['url'];
                        if($movieUrlAndTimestamp && $movieUrlAndTimestamp['poster']){
                            $FULL_FINAL_LIST[$counter]["poster"] = $movieUrlAndTimestamp['poster'];
                        }

                        $FULL_FINAL_LIST[$counter]["cat"] = $CATEGORY[$key];

                        if(strpos(strtolower($videoSiteUrl), "bengali") !== false){
                            $FULL_FINAL_LIST[$counter]["cat"] = 'Bangla';
                        }

                        if(strpos(strtolower($videoSiteUrl), "hindi") !== false){
                            $FULL_FINAL_LIST[$counter]["cat"] = 'Hindi';
                        }

                        if(strpos(strtolower($videoSiteUrl), "english") !== false){
                            $FULL_FINAL_LIST[$counter]["cat"] = 'English';
                        }


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

                    }else{
                        echo "already on file\n\n";
                    }
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
        if($itemGet && $itemGet['poster']){
            $object->poster = $itemGet['poster'];
        }

        $EXISTING[] = $object;

    }
    return $EXISTING;


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
    $images = $xpath->query('//img[contains(@class, "size-full")]');



    if($images){
        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            if($src){
                $takeUrl['poster'] = $src;
            }
        }
    }




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
            (strpos($href, 'dooood.com') !== false)
        )
        {
            $takeUrl['url'] = $href;
            break;
        }
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
            (strpos($href, 'dood.wf') !== false)
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
