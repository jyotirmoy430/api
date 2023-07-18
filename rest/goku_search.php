<?php

error_reporting(0);
$SITE_URL = "https://goku.sx";


try{
    $keyword = ($_GET["keyword"]) ? str_replace(" ", "%20", $_GET["keyword"]) : "a";
    $category = ($_GET["category"] && $_GET["category"] !== ' ') ? $_GET["category"] : "";
    $offset = ($_GET["offset"]) ? $_GET["offset"] : 0;
    $limit = ($_GET["limit"]) ? $_GET["limit"] : 20;
    $sort = ($_GET["sort"] === 'asc') ? SORT_ASC : SORT_DESC;
}catch (\Exception $e){
}

$searchUrl = "$SITE_URL/search?keyword=$keyword";
$items = getSearchItems($searchUrl);

$finalItems = ($items && count($items) > 0) ? array_values($items) : [];

echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);

function getSearchItems($url)
{
    global $SITE_URL;
    $html = get_web_page($url);



    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $xpath = new DOMXPath($dom);



    $anchors = $xpath->query('//div[contains(@class, "movie-thumbnail")]/a[@href]');
    $images = $xpath->query('//div[contains(@class, "movie-thumbnail")]/a/img[@src]');

    $array = [];
    foreach ($anchors as $index => $anchor) {
        $href = $anchor->getAttribute('href');
        $src = $images[$index]->getAttribute('src');
        $array[] = [
            'id'=>$index,
            'video' => $SITE_URL.str_replace("/movie", "/watch-movie", $href),
            'poster' => $src,
            'goku' => 1
        ];
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
