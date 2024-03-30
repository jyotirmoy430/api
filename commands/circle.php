<?php
//error_reporting( E_ALL );
error_reporting(0);

function init(){

    $ONE = [
        'http://ftp5.circleftp.net/FILE/Animation%20Movies',
        'http://ftp5.circleftp.net/FILE/Animation%20Dubbed%20Movies',
        'http://index.circleftp.net/FILE/English%20Movies',
        'http://index2.circleftp.net/FILE/English%20Movies',
        'http://index2.circleftp.net/FILE/English%20Movies',
        'http://index.circleftp.net/FILE/English%20%26%20Foreign%20Dubbed%20Movies',
        'http://index2.circleftp.net/FILE/English%20%26%20Foreign%20Dubbed%20Movies',
        'http://index1.circleftp.net/FILE/Hindi%20Movies',
    ];


    $FINAL_URLS = [];

    foreach($ONE as $one){
        for($i = 1995; $i<=2024; $i++){
            $url = $one.'/'.$i;
            $FINAL_URLS[] = $url;
        }
    }
    $FINAL_URLS[] = 'http://index.circleftp.net/FILE/English%20Movies/%281995%29%20%26%20Before';

    $ALL_URLS = [];

    $counter = 0;
    $counterInner = 0;

    foreach($FINAL_URLS as $url){
        $html = get_web_page_local($url);
        $hrefs = extractHrefs($html["content"], $url);
        if($hrefs){
            foreach($hrefs as $href){
                $explode = explode('/', $href);

                $ALL_URLS[$counter]['cat'] = $explode[2];
                $ALL_URLS[$counter]['year'] = $explode[3];
                $ALL_URLS[$counter]['url'] = $url.'/'.$explode[4];
                $counter++;
            }
        }
    }

    $VIDEO_URLS = [];
    if($ALL_URLS){
        foreach($ALL_URLS as $allUrl){
            $html = get_web_page_local($allUrl["url"]);
            $hrefs = extractHrefs($html["content"], $allUrl["url"]);



            if($hrefs){
                foreach($hrefs as $href){
                    $explode = explode('/', $href);



                    if (
                        strpos($href, ".mp4") !== false ||
                        strpos($href, ".MP4") !== false ||
                        strpos($href, ".mkv") !== false ||
                        strpos($href, ".MKV") !== false ||
                        strpos($href, ".avi") !== false
                    ) {
                        $VIDEO_URLS[$counterInner]['name'] = $explode[5];
                        $VIDEO_URLS[$counterInner]['url'] = $url . '/' . $explode[4] . '/' . $explode[5];
                        $VIDEO_URLS[$counterInner]['cat'] = $allUrl['cat'];
                        $VIDEO_URLS[$counterInner]['year'] = $allUrl['year'];
                        echo "Found movie::".$explode[5]."\n\n";
                        $counterInner++;
                    }
                }
            }
        }
    }

    $FULL_FINAL = [];
    if($VIDEO_URLS){
        foreach($VIDEO_URLS as $key=>$itemGet){
            $object = new stdClass();
            $object->id = $key;
            $object->video = $itemGet["url"];
            $object->name = $itemGet["name"];
            $object->year = $itemGet["year"];

            $timestamp = strtotime($object->year . "-01-01");

            $object->timestamp = $timestamp;
            $object->cat = $itemGet["cat"];
            $object->circle = '1';
            $FULL_FINAL[] = $object;

        }
    }


    return $FULL_FINAL;

}

function extractHrefs($htmlContent, $baseUrl) {
    if(!$htmlContent){
        return [];
    }
    $hrefs = [];

    // Create DOMDocument
    $dom = new DOMDocument();
    // Load HTML content
    $dom->loadHTML($htmlContent);

    // Get all anchor (a) tags
    $anchors = $dom->getElementsByTagName('a');
    foreach ($anchors as $anchor) {
        $href = $anchor->getAttribute('href');


        // Check if href starts with the specified base URL
        if (strpos($href, '/FILE/') === 0) {
            $hrefs[] = $href;
        }
    }

    return $hrefs;
}


function get_web_page_local( $url )
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

