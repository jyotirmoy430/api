<?php

error_reporting(0);

$allowed_origin = "http://jbmovies.rf.gd";

// Check the Origin header
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = $_SERVER['HTTP_ORIGIN'];
    
    // Allow only requests from http://jbmovies.rf.gd
    if ($origin === $allowed_origin) {
        header("Access-Control-Allow-Origin: $allowed_origin");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
    } else {
        // If the request comes from an unauthorized origin, block it
        header("HTTP/1.1 403 Forbidden");
        echo json_encode(['error' => 'Unauthorized origin']);
        exit;
    }
} else {
    // Block requests with no Origin header
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(['error' => '403 Forbidden']);
    exit;
}


$SITE_URL = "http://15.1.1.50:5000/api/posts";


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




        $searchUrl = "$SITE_URL?searchTerm=$keyword&order=desc";
        $items = getSearchItems($searchUrl);

        $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

        echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);



function getSearchItems($url)
{
    $result = [];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false); // Exclude headers from response
    $response = curl_exec($curl);

    if(curl_errno($curl)) {
        return [];
    } else {
        // Decode the JSON response
        $data = json_decode($response, true);

        // Check if JSON decoding was successful
        if ($data === null) {
            return [];
        } else {
            $result = $data;
        }
    }

    curl_close($curl);

    if(!$result || !isset($result["posts"])){
        return [];
    }

    $takeArr = [];
    $counter = 0;
    foreach($result["posts"] as $post){
        if($post["type"] == "singleVideo"){
            $takeArr[$counter]["id"] = $counter;
            $takeArr[$counter]["video"] = $post["id"];
            $takeArr[$counter]["title"] = $post["title"];
            $takeArr[$counter]["poster"] = "http://15.1.1.50:5000/uploads/".$post["image"];
            $takeArr[$counter]["cat"] = "all";
            $takeArr[$counter]["circle"] = 1;
            $counter++;
        }

    }
    return $takeArr;
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
