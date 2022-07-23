<?php
function alif(){
    $HOST = 'http://ftp1.aliflailabd.com';

    $CATEGORY = [
        'Animation Movies',
        'Bangla Movies',
        'English Movies',
        'Foreign Language Movies',
        'Hindi Movies',
        'South Indian Movies'
    ];
    $counter = 0;
    $finalAlif = [];

    foreach($CATEGORY as $key=>$cat){
        for($i=1980; $i<=2022; $i++){
            $parent = $HOST.'/'.$cat.'/'.$i.'/';
            $payloadHref = '/'.$cat.'/'.$i.'/';
            $payload = '{"action":"get","items":{"href":"'.$payloadHref.'","what":1}}';
            $parent = str_replace(' ', '%20', $parent);
            $payload = str_replace(' ', '%20', $payload);



            $result = call($parent, $payload);

            if($result && $result["content"]){
                $content = $result["content"];

                $content = json_decode($content);
                if($content && $content->items){
                    $content = $content->items;

                    $subCatArr = [];

                    foreach($content as $subCat){
                        $subCatHref = $subCat->href;
                        $payloadHref = str_replace(' ', '%20', $payloadHref);

                        if($subCatHref !== $payloadHref && strpos($subCatHref, $payloadHref) !== false){
                            $subCatArr[] = $subCatHref;
                        }
                    }

                    if($subCatArr){
                        foreach($subCatArr as $item){

                            $parentSubCat = $HOST.$item;
                            $payloadHrefSubCat = $item;
                            $payloadSubCat = '{"action":"get","items":{"href":"'.$payloadHrefSubCat.'","what":1}}';



                            $resultSubCat = call($parentSubCat, $payloadSubCat);


                            if($resultSubCat && $resultSubCat["content"]) {
                                $contentSubCat = $resultSubCat["content"];

                                $contentSubCat = json_decode($contentSubCat);

                                if($contentSubCat && $contentSubCat->items){
                                    $contentSubCat = $contentSubCat->items;


                                    foreach($contentSubCat as $subCat){
                                        $subCatHref = $subCat->href;

                                        if($subCatHref !== $payloadHrefSubCat && strpos($subCatHref, $payloadHrefSubCat) !== false){

                                            if(strpos($HOST.$subCatHref, ".mp4") !== false ||
                                                strpos($HOST.$subCatHref, ".avi") !== false ||
                                                strpos($HOST.$subCatHref, ".mkv") !== false){
                                                $object = new stdClass();
                                                $object->id = $counter;
                                                $object->flag = 1;
                                                $object->video = $HOST.$subCatHref;
                                                $object->size = formatBytes($subCat->size);

                                                echo "<pre>";
                                                print_r($object);
                                                echo "</pre>";

                                                $finalAlif[] = $object;
                                                $counter++;

                                            }

                                        }
                                    }

                                }

                            }






















                        }
                    }

                }


            }

        }
    }



    return $finalAlif;

}

function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('', 'KB', 'MB', 'GB', 'TB');

    return round(pow(1024, $base - floor($base)), $precision) .''. $suffixes[floor($base)];

}

function call($url, $payload){
   // sleep(1);
    $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

    $options = array(

        CURLOPT_CUSTOMREQUEST  =>"POST",        //set request type post or get
        CURLOPT_POSTFIELDS  =>$payload,        //set request type post or get
        CURLOPT_POST           =>true,
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
        CURLOPT_MAXREDIRS      => 2,       // stop after 10 redirects
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