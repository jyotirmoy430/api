<?php
error_reporting(0);

try{
    $keyword = ($_GET["keyword"]) ? $_GET["keyword"] : "";
    $category = ($_GET["category"]) ? $_GET["category"] : "";
    $offset = ($_GET["offset"]) ? $_GET["offset"] : 0;
    $limit = ($_GET["limit"]) ? $_GET["limit"] : 2;
}catch (\Exception $e){
}


$data = get_web_page("https://jyotirmoy430.github.io/api/listn.json");
$decoded_json = json_decode($data["content"], false);

$takeArr = [];

foreach($decoded_json as $decoded){
    if($category){
        if (strpos($decoded->cat, $category) !== false) {
            $checkMatch = checkWords($decoded->video, $keyword);
            if($checkMatch){
                $takeArr[] = $decoded;
            }
        }
    }else{
        $checkMatch = checkWords($decoded->video, $keyword);
        if($checkMatch){
            $takeArr[] = $decoded;
        }
    }
}

function checkWords($video, $keyword){
    try{
        $keywords = explode("%20", $keyword);

        if($keywords && count($keywords) > 0){
            foreach($keywords as $word){
                if (strpos($video, $word) !== false) {
                    return true;
                }
            }
        }
    }catch (\Exception $e){
        return true;
    }
    return false;
}



echo json_encode(array_slice($takeArr, $offset, $limit), JSON_PRETTY_PRINT);



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
        CURLOPT_CONNECTTIMEOUT => 10,      // timeout on connect
        CURLOPT_TIMEOUT        => 10,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
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
    return $header;
}
