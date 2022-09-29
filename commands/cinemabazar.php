<?php


function loopAndTake($parent, $payload, $data){
    try{
        $HOST_ONLY = 'http://cinemabazar.net';

        if(
            strpos($parent, "/DATA/NAS1/Tutorial") !== false ||
            strpos($parent, "/DATA/NAS1/E-Books") !== false ||
            strpos($parent, "/DATA/NAS1/Games") !== false ||
            strpos($parent, "/DATA/NAS1/MP3") !== false ||
            strpos($parent, "/DATA/NAS1/Software") !== false ||
            strpos($parent, "/DATA/NAS1/WALLPAPER") !== false ||
            strpos($parent, "/DATA/NAS1/WWE%20%26%20AEW%20Wrestling/") !== false ||
            strpos($parent, "/DATA/NAS1/WWE %26 AEW Wrestling") !== false ||
            strpos($parent, "/DATA/NAS1/Documentary") !== false
        ){
            return $data;
        }


        $result = call($parent, $payload);


        if($result && $result["content"]) {
            $content = $result["content"];

            $content = json_decode($content);

            if($content) {
                if ($content && isset($content->items)) {
                    $items = $content->items;

                    $parsedPayload = json_decode($payload);
                    foreach($items as $item){
                        if(
                            $item->href !== $parsedPayload->items->href &&
                            strpos($item->href, $parsedPayload->items->href) !== false){

                            if(strpos($item->href, ".jpg") === false &&
                                (strpos($item->href, ".mp4") !== false ||
                                    strpos($item->href, ".MP4") !== false ||
                                    strpos($item->href, ".avi") !== false ||
                                    strpos($item->href, ".AVI") !== false ||
                                    strpos($item->href, ".MKV") !== false ||
                                    strpos($item->href, ".mkv") !== false)){
                                array_push($data, $HOST_ONLY.$item->href.":::".$item->size);

                                echo "<pre>";
                                print_r($HOST_ONLY.$item->href);
                                echo "</pre>\n\n\n";


                                $data = array_unique($data);


                            }else{
                                $newParent = $HOST_ONLY.$item->href;


                                $newPayload = '{"action":"get","items":{"href":"'.$item->href.'","what":1}}';

                                $newItems = loopAndTake($newParent, $newPayload, $data);

                                $newData = array_merge($data, $newItems);


                                $data = array_unique($newData);

                            }

                            echo "Word Found!";
                        } else{
                            //echo "Word Not Found!";
                        }

                    }

                }
            }


        }
        return array_values($data);
    }catch (\Exception $e){
        return $data;
    }

}

function cinemabazar(){
    $HOST = 'http://cinemabazar.net/DATA/NAS1/Movies/';

    $parent = $HOST.'/';
    $payloadHref = '/DATA/NAS1/Movies/';
    $payload = '{"action":"get","items":{"href":"'.$payloadHref.'","what":1}}';
    $parent = str_replace(' ', '%20', $parent);
    $payload = str_replace(' ', '%20', $payload);


    $bigArr = loopAndTake($parent, $payload, []);

    $FULL_FINAL = [];

    if($bigArr){
        foreach($bigArr as $key=>$final){

            try{
                $object = new stdClass();
                $object->id = $key;

                $finalExplode = explode(":::", $final);
                $object->video = $finalExplode[0];
                $object->size = $finalExplode[1];



                if(strpos($final, "2022") !== false){
                    $object->year = 2022;
                } elseif(strpos($final, "2021") !== false){
                    $object->year = 2021;
                } elseif(strpos($final, "2020") !== false){
                    $object->year = 2020;
                } elseif(strpos($final, "2019") !== false){
                    $object->year = 2019;
                } elseif(strpos($final, "2018") !== false){
                    $object->year = 2018;
                } elseif(strpos($final, "2017") !== false){
                    $object->year = 2017;
                } elseif(strpos($final, "2016") !== false){
                    $object->year = 2016;
                } else{
                    $object->year = 0;
                }


                $FULL_FINAL[] = $object;
            }catch (\Exception $e){

            }


        }
    }

    return $FULL_FINAL;

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

