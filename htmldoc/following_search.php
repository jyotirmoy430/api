<?php
error_reporting(0);

$allowed_origins = ["http://jbmovies.rf.gd", "https://jbmovies.rf.gd"];

// Check the Origin header
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = $_SERVER['HTTP_ORIGIN'];
    
    // Allow only requests from http://jbmovies.rf.gd
    if(in_array($origin, $allowed_origins)){
        header("Access-Control-Allow-Origin: $origin");
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

//$SITE_URL = "https://wa.atoztipsntricks.com/";
$SITE_URL = "https://fibwatch.info/"; //https://fibwatch.online/


try {
    $keyword = ($_REQUEST["keyword"]) ? str_replace(" ", "%20", $_REQUEST["keyword"]) : "2024";
    $category = ($_REQUEST["category"] && $_REQUEST["category"] !== ' ') ? $_REQUEST["category"] : "";
    $offset = ($_REQUEST["offset"]) ? $_REQUEST["offset"] : 0;
    $limit = ($_REQUEST["limit"]) ? $_REQUEST["limit"] : 100;
    $sort = ($_REQUEST["sort"] === 'asc') ? SORT_ASC : SORT_DESC;
} catch (\Exception $e) {
}
if ($limit & $limit > 100) {
    $limit = 100;
}

$searchUrl = url_picker($category);


if (!$keyword || $keyword == '') {
    $items = getSearchItems($searchUrl, $category=='All' ? "" : 'video-thumb');

    $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

    echo json_encode(array_slice($finalItems, $offset, $limit), JSON_PRETTY_PRINT);
} else {

        if ($offset < 20) {
            // No page_id if offset is 0
            $page_id = "";
        } else {
            // Calculate page_id based on offset
            $page_id = "&page_id=".($offset / 20) + 1;
        }

        $searchUrl = "$SITE_URL"."search?keyword=$keyword".$page_id;

        $items = getSearchItems($searchUrl, 'video-thumb');

        $finalItems = ($items && count($items) > 0) ? array_values($items) : [];

        echo json_encode($finalItems, JSON_PRETTY_PRINT);

}


function getSearchItems($url, $className='video-list-image')
{
    $url = ($url) ? str_replace(" ", "%20", $url) : "";
    $html = get_web_page($url);

    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $anchors = $xpath->query('//div[contains(@class, "'.$className.'")]/a[@href]');
    $images = $xpath->query('//div[contains(@class, "'.$className.'")]/a/img[@src]');


    $array = [];

    foreach ($anchors as $index => $anchor) {
        $href = $anchor->getAttribute('href');
        $src = $images[$index]->getAttribute('src');
        $title = $images[$index]->getAttribute('alt');


        if ($href) {

            $array[] = [
                'id' => $index,
                'video' => $href,
                'title' => $title,
                'poster' => $src,
                'following' => 1,
                'cat' => "all",
            ];
        }
    }
    return $array;
}

function url_picker($key){
    global $SITE_URL;

    if($key === 'Animated'){
        return $SITE_URL.'videos/category/7';
    }
    if($key === 'Tv show'){
        return $SITE_URL.'videos/category/854';
    }
    if($key === 'Bangla'){
        return $SITE_URL.'videos/category/1';
    }
    if($key === 'Hindi'){
        return $SITE_URL.'videos/category/4';
    }
    if($key === 'Dual'){
        return $SITE_URL.'videos/category/852';
    }
    if($key === 'English'){
        return $SITE_URL.'videos/category/8';
    }
    return $SITE_URL;
}


function get_web_page($url)
{
    $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

    $options = array(

        CURLOPT_CUSTOMREQUEST => "GET",        //set request type post or get
        CURLOPT_POST => false,        //set to GET
        CURLOPT_USERAGENT => $user_agent, //set user agent
        CURLOPT_COOKIEFILE => "cookie.txt", //set cookie file
        CURLOPT_COOKIEJAR => "cookie.txt", //set cookie jar
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING => "",       // handle all encodings
        CURLOPT_AUTOREFERER => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 1000,      // timeout on connect
        CURLOPT_TIMEOUT => 1000,      // timeout on response
        CURLOPT_MAXREDIRS => 1000,       // stop after 10 redirects
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    $header = curl_getinfo($ch);
    curl_close($ch);

    $header['errno'] = $err;
    $header['errmsg'] = $errmsg;
    $header['content'] = $content;
    return $content;
}
