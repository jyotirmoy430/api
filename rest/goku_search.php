<?php

error_reporting(0);
$SITE_URL = "https://goku.sx";


try{
    $keyword = ($_GET["keyword"]) ? str_replace(" ", "%20", $_GET["keyword"]) : "";
    $category = ($_GET["category"] && $_GET["category"] !== ' ') ? $_GET["category"] : "";
    $offset = ($_GET["offset"]) ? $_GET["offset"] : 0;
    $limit = ($_GET["limit"]) ? $_GET["limit"] : 100;
    $sort = ($_GET["sort"] === 'asc') ? SORT_ASC : SORT_DESC;
}catch (\Exception $e){
}
if($limit & $limit > 100){
    $limit = 100;
}


if(!$keyword || $keyword==''){
    $searchUrl = "$SITE_URL/home";
    $items = getSearchItems($searchUrl);

    $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

    echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);
    /*
    $data = get_web_page("https://raw.githubusercontent.com/jyotirmoy430/api/main/listngoku.json");
    $decoded_json = json_decode($data, false);


    $takeArr = [];

    foreach($decoded_json as $decoded){
        if($category){
            if (strpos(strtolower($decoded->cat), strtolower($category)) !== false) {
                if($keyword == ''){
                    $takeArr[] = $decoded;
                }
                if (strpos(strtolower($decoded->video), strtolower($keyword)) !== false) {
                    $takeArr[] = $decoded;
                }
            }
        }else{
            if($keyword == ''){
                $takeArr[] = $decoded;
            }
            if (strpos(strtolower($decoded->video), strtolower($keyword)) !== false) {
                $takeArr[] = $decoded;
            }
        }
    }

    $year = array();
    foreach ($takeArr as $key => $row)
    {
        $year[$key] = $row->year;
    }
    array_multisort($year, $sort, $takeArr);

    echo json_encode(array_slice($takeArr, $offset, $limit), JSON_PRETTY_PRINT);*/

}else{
    if($keyword && $keyword=='c=comedy'){
        $searchUrl = "$SITE_URL/genre/comedy-7";
        $items = getSearchItems($searchUrl);

        $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

        echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);
    }
    else if($keyword && $keyword=='c=action'){
        $searchUrl = "$SITE_URL/genre/action-10";
        $items = getSearchItems($searchUrl);

        $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

        echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);
    }
    else if($keyword && $keyword=='c=animation'){
        $searchUrl = "$SITE_URL/genre/animation-3";
        $items = getSearchItems($searchUrl);

        $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

        echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);
    }
    else if($keyword && ($keyword=='c=scifi' || $keyword=='c=sifi')){
        $searchUrl = "$SITE_URL/genre/sci-fi-fantasy-31";
        $items = getSearchItems($searchUrl);

        $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

        echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);
    }
    else if($keyword && $keyword=='c=kids'){
        $searchUrl = "$SITE_URL/genre/kids-27";
        $items = getSearchItems($searchUrl);

        $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

        echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);
    }
    else if($keyword && $keyword=='c=fantasy'){
        $searchUrl = "$SITE_URL/genre/fantasy-13";
        $items = getSearchItems($searchUrl);

        $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

        echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);
    }
    else if($keyword && $keyword=='c=horror'){
        $searchUrl = "$SITE_URL/genre/horror-14";
        $items = getSearchItems($searchUrl);

        $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

        echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);
    }else{
        $searchUrl = "$SITE_URL/search?keyword=$keyword";
        $items = getSearchItems($searchUrl);

        $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

        echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);
    }

}


function getSearchItems($url)
{
    $counter = 0;
    $url = ($url) ? str_replace(" ", "%20", $url) : "";
    global $SITE_URL;
    $html = get_web_page($url);



    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $xpath = new DOMXPath($dom);



    $anchors = $xpath->query('//div[contains(@class, "movie-thumbnail")]/a[@href]');
    $images = $xpath->query('//div[contains(@class, "movie-thumbnail")]/a/img[@src]');

    $array = [];
    $array2 = [];
    foreach ($anchors as $index => $anchor) {
        $href = $anchor->getAttribute('href');


        $src = $images[$index]->getAttribute('src');
        $title = $images[$index]->getAttribute('alt');
        if (strpos($href, "series") == false) {
            if($index>29){
                $string = str_replace("/movie", "/watch-movie", $href);
                $finalString = str_replace("/movie", "/watch-movie", $href);
                $exploded = explode("/watch-movie/", $finalString);

                if($exploded && isset($exploded[1])){
                    $array[] = [
                        'id'=>$counter,
                        'video' => $SITE_URL.'/watch-movie/'.$exploded[1],
                        'title' => $title,
                        'poster' => $src,
                        'goku' => 1,
                        'cat' => "all",
                    ];
                    $counter++;
                }
            }else{
                $string = str_replace("/movie", "/watch-movie", $href);
                $finalString = str_replace("/movie", "/watch-movie", $href);
                $exploded = explode("/watch-movie/", $finalString);

                if($exploded && isset($exploded[1])){
                    $array2[] = [
                        'video' => $SITE_URL.'/watch-movie/'.$exploded[1],
                        'title' => $title,
                        'poster' => $src,
                        'goku' => 1,
                        'cat' => "all",
                    ];
                }
            }
        }



        /*if (strpos($href, "series") != false) {

            $finalString = str_replace("/series", "/watch-series", $href);
            $exploded = explode("/watch-series/", $finalString);

            if($exploded && isset($exploded[1])){
                $array[] = [
                    'id'=>$index,
                    'video' => $SITE_URL.'/watch-series/'.$exploded[1],
                    'title' => $title,
                    'poster' => $src,
                    'goku' => 1,
                    'cat' => "all",
                ];
            }

        }*/
    }

    if(count($array2) > 0){
        foreach($array2 as $arr){
            $temp = $arr;
            $temp["id"] = $counter;

            $array[] = $temp;



            $counter++;
        }
    }

    return $array;
}


function get_web_page( $url )
{
    $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

    $options = array(

        CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
        CURLOPT_POST           =>false,        //set to GET
        CURLOPT_USERAGENT      => $user_agent, //set user agent
        CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
        CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 1000,      // timeout on connect
        CURLOPT_TIMEOUT        => 1000,      // timeout on response
        CURLOPT_MAXREDIRS      => 1000,       // stop after 10 redirects
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $content;
}
