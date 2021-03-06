<?php

function init(){
    $ONE = [
        'http://10.16.100.245/ftps2d',
        'http://10.16.100.245/ftps2d4/ftps6d2',
        'http://10.16.100.206/ftps3',
        'http://10.16.100.202/ftps10',
        'http://10.16.100.212/iccftps12',
        'http://10.16.100.213/iccftps13',
        'http://10.16.100.250',
    ];

    $TWO = [
        '',
        '',
        'ftps3d',
        'iccftps10sasd',
        'iccftps12sasd',
        'iccftps13sasd',
        'ftps10d',
    ];

    $THREE=[
        4,
        0,
        8,
        10,
        10,
        10,
        2
    ];

    $MOVIE_FOLDER_URL = [
        'Movies',
        '',
        'Movies',
        'Movies',
        'Movies',
        'Movies',
        'Movies',
    ];

    $CATEGORY = [
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
            foreach($CATEGORY as $four){
                $takeUrl = $top.'/'.str_replace(' ', '%20', $four);
                $FINAL_URL[] = $takeUrl;
            }
        }
    }
    $FULL_FINAL_LIST = [];

    $FINAL_URL = addCustom($FINAL_URL);


    if($FINAL_URL){
        foreach($FINAL_URL as $key=>$final){
            echo "<pre>";
            print_r($final);
            echo "</pre>";

            $data = get_web_page($final);

            if($data["http_code"] == 200){
                $dom = new DomDocument();
                $dom->loadHTML($data["content"]);

                foreach ($dom->getElementsByTagName('a') as $item) {
                    //$output[] = $item->getAttribute('href');
                    $href = $item->getAttribute('href');

                    echo "<pre>";
                    print_r($href);
                    echo "</pre>";

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

        echo "<pre>";
        print_r($object);
        echo "</pre>";

        $FULL_FINAL[] = $object;

    }


    //file_put_contents("../listn.json",json_encode($FULL_FINAL));

    return $FULL_FINAL;
}


function addCustom( $urls ){
    $urls[] = 'http://10.16.100.245/ftps2d1/ftps1d3/English%20Movies/2014';
    $urls[] = 'http://10.16.100.245/ftps2d1/ftps1d3/English%20Movies/2015';
    $urls[] = 'http://10.16.100.250/ftps10d1/TV%20Show/English/Lucifer%20Season%2001%20(2016)%20Completed';
    $urls[] = 'http://10.16.100.250/ftps10d2/TV%20Show/English/Lucifer%20Season%2002%20(2016)%20Completed';
    $urls[] = 'http://10.16.100.250/ftps10d2/TV%20Show/English/Lucifer%20Season%2003%20(2017)%20Completed';
    return $urls;
}

