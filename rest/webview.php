<?php
error_reporting(0);

try{
    $keyword = ($_GET["keyword"]) ? str_replace(" ", "%20", $_GET["keyword"]) : "";
    $category = ($_GET["category"] && $_GET["category"] !== ' ') ? str_replace(" ", "%20", $_GET["category"]) : "";
    $offset = ($_GET["offset"]) ? $_GET["offset"] : 0;
    $limit = ($_GET["limit"]) ? $_GET["limit"] : 20;
    $sort = ($_GET["sort"] === 'asc') ? SORT_ASC : SORT_DESC;
}catch (\Exception $e){
}

if($limit & $limit > 100){
    $limit = 100;
}

//$data = file_get_contents("https://raw.githubusercontent.com/jyotirmoy430/api/main/listnwebview.json");
$jsonFilePath = 'listnwebview.json';

$jsonData = file_get_contents($jsonFilePath);

$decoded_json = json_decode($jsonData);

//$decoded_json = json_decode($data["content"], false);

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
