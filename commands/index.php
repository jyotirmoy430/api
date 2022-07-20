<?php


/*$str = 'My long <a href="http://example.com/abc" rel="link">string</a> has any
    <a href="/local/path" title="with attributes">number</a> of
    <a href="#anchor" data-attr="lots">links</a>.';

$dom = new DomDocument();
$dom->loadHTML($str);
$output = array();
foreach ($dom->getElementsByTagName('a') as $item) {
    $output[] = array (
        'str' => $dom->saveHTML($item),
        'href' => $item->getAttribute('href'),
        'anchorText' => $item->nodeValue
    );
}
echo "<pre>";
print_r($output);
echo "</pre>\n\n";
exit;
exit;*/


$ONE = [
    'http://10.16.100.245/ftps2d',
    'http://10.16.100.245/ftps2d4/ftps6d2',
    'http://10.16.100.206/ftps3',
    'http://10.16.100.202/ftps10',
    'http://10.16.100.212/iccftps12',
    'http://10.16.100.213/iccftps13',
];

$TWO = [
    '',
    '',
    'ftps3d',
    'iccftps10sasd',
    'iccftps12sasd',
    'iccftps13sasd',
];

$THREE=[
    4,
    0,
    8,
    10,
    10,
    10,
];

$MOVIE_FOLDER_URL = [
    'Movies',
    '',
    'Movies',
    'Movies',
    'Movies',
    'Movies',
];

$FOUR = [
    '3D/English',
    '4K/English',
    'Animated',
    'Anime',
    'Bangla (BD)',
    'Bangla (Kolkata)',
    'Chinese',
    'Dual Audio',
    'English',
    'Full HD',
    'Hindi',
    'Japanese',
    'Korean',
    'Other Foreign',
    'Punjabi',
    'Tamil Movie',
    'South Indian (Hindi Dubbed)',
    'South Indian',
];



$FINAL_URL = [];
foreach($ONE as $key=>$one){
    $initialUrl = $one;
    if($TWO[$key] !== ''){
        $initialUrl = $initialUrl.'/'.$TWO[$key];
    }
    $loop = $THREE[$key];
    if($loop !== 0){
        for($i=1; $i<=(int)$loop; $i++){
            $urlWithLoop[] = $initialUrl.$i."/".$MOVIE_FOLDER_URL[$key];
        }
    }else{
        $urlWithLoop[] = $initialUrl.$MOVIE_FOLDER_URL[$key];
    }

}
if($urlWithLoop){
    foreach($urlWithLoop as $key=>$top){
        foreach($FOUR as $four){
            $takeUrl = $top.'/'.str_replace(' ', '%20', $four);;
            $FINAL_URL[] = $takeUrl;
        }
    }
}
$FULL_FINAL_LIST = [];

$FINAL_URL = addCustom($FINAL_URL);


if($FINAL_URL){
    foreach($FINAL_URL as $key=>$final){
        $data = get_web_page($final);

        if($data["http_code"] == 200){
            $dom = new DomDocument();
            $dom->loadHTML($data["content"]);

            foreach ($dom->getElementsByTagName('a') as $item) {
                //$output[] = $item->getAttribute('href');
                $href = $item->getAttribute('href');

                if($href){
                    $takeHref = explode(".",$href);
                    if($takeHref && (end($takeHref) == "mp4" || end($takeHref) == "mkv"  || end($takeHref) == "avi")){
                        $implode = implode(".", $takeHref);



                        $explodeFinal = explode("/", $final);
                        $explodeImplode = explode("/", $implode);


                        $makeFinal = [];
                        foreach($explodeFinal as $key=>$ex){

                                if(!in_array($ex, $explodeImplode) && !in_array($ex, $makeFinal)){
                                    $makeFinal[] = $ex;
                                }

                        }





                        $final = implode("/", $makeFinal);



                        if(endsWith($final,"/") || startsWith($implode,"/")){
                            $fullFinalUrl = $final.$implode;
                        }else{
                            $fullFinalUrl = $final."/".$implode;
                        }
                        $fullFinalUrl = str_replace("http:/","http://",$fullFinalUrl);


                        $FULL_FINAL_LIST[] = $fullFinalUrl;
                    }
                }


            }


        }else{
            continue;
        }

    }
}

$FULL_FINAL = [];

foreach($FULL_FINAL_LIST as $key=>$final){
    $object = new stdClass();
    $object->id = $key;
    $object->video = $final;

    $FULL_FINAL[] = $object;

}


file_put_contents("../listn.json",json_encode($FULL_FINAL));

echo "Completed";
exit;


/*$data = get_web_page("http://10.16.100.212/iccftps12/");

echo "<pre>";
print_r($data["content"]);
echo "</pre>\n\n";
exit;*/

function startsWith ($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

function endsWith($string, $endString)
{
    $len = strlen($endString);
    if ($len == 0) {
        return true;
    }
    return (substr($string, -$len) === $endString);
}

function addCustom( $urls ){
    $urls[] = 'http://10.16.100.245/ftps2d1/ftps1d3/English%20Movies/2014';
    $urls[] = 'http://10.16.100.245/ftps2d1/ftps1d3/English%20Movies/2015';
    return $urls;
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
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
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
