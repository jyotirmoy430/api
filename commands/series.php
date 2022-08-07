<?php

function series(){
    $ONE = [
        'http://10.16.100.245/ftps2d',
        'http://10.16.100.245/ftps2d4/ftps6d2',
        'http://10.16.100.206/ftps3',
        'http://10.16.100.202/ftps10',
        'http://10.16.100.212/iccftps12',
        'http://10.16.100.213/iccftps13',
        'http://10.16.100.250'
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
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show'
    ];

    $CATEGORY = [
        'Bangla',
        'English'
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
                $urlWithLoop[] = $initialUrl.$i."/".'Tv%20Show';
                $urlWithLoop[] = $initialUrl.$i."/".'Tv%20Shows';
            }
        }else{
            $urlWithLoop[] = $initialUrl.$MOVIE_FOLDER_URL[$key];
            $urlWithLoop[] = $initialUrl.'Tv%20Shows';
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

    $FINAL_URL = addCustomS($FINAL_URL);

    $OTHERS_FOLDERS = [];

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
                        if($takeHref && (end($takeHref) == "mp4" || end($takeHref) == "MP4" || end($takeHref) == "mkv" || end($takeHref) == "MKV"  || end($takeHref) == "avi")){
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
                        }else{
                            $OTHERS_FOLDERS[] =$final. $takeHref[0];
                        }
                    }


                }


            }else{
                continue;
            }

        }
    }





    if($OTHERS_FOLDERS && count($OTHERS_FOLDERS) > 0){
        $FINAL_URL = $OTHERS_FOLDERS;

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
                            if($takeHref && (end($takeHref) == "mp4" || end($takeHref) == "MP4" || end($takeHref) == "mkv" || end($takeHref) == "MKV"  || end($takeHref) == "avi")){
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


function addCustomS( $urls ){
    return $urls;
    $urls[] = 'http://10.16.100.245/ftps2d1/ftps1d3/English%20Movies/2014';
    $urls[] = 'http://10.16.100.245/ftps2d1/ftps1d3/English%20Movies/2015';
    $urls[] = 'http://10.16.100.250/ftps10d1/TV%20Show/English/Lucifer%20Season%2001%20(2016)%20Completed';
    $urls[] = 'http://10.16.100.250/ftps10d2/TV%20Show/English/Lucifer%20Season%2002%20(2016)%20Completed';
    $urls[] = 'http://10.16.100.250/ftps10d2/TV%20Show/English/Lucifer%20Season%2003%20(2017)%20Completed';
    $urls[] = 'http://ftp1.aliflailabd.com/Hindi%20Movies/2018/K.G.F%20Chapter%201%20%282018%29';
    $urls[] = 'http://ftp1.aliflailabd.com/Hindi%20Movies/2018/K.G.F%20Chapter%201%20%282018%29';
    $urls[] = 'http://ftp1.aliflailabd.com/Hindi%20Movies/2022/K.G.F%20Chapter%202%20%282022%29';
    $urls[] = 'http://10.16.100.212/iccftps12/iccftps12sasd8/TV%20Shows/English/Man%20Vs%20Bee%20Season%2001%20(2022)%20Completed';
    $urls[] = 'http://10.16.100.212/iccftps12/iccftps12sasd6/Tv%20Show/English/Lost%20in%20Space%20Season%2001%20(2018)%20Completed';
    $urls[] = 'http://10.16.100.212/iccftps12/iccftps12sasd7/TV%20Shows/English/Lost%20in%20Space%20Season%2002%20(2019)%20Completed';
    $urls[] = 'http://10.16.100.212/iccftps12/iccftps12sasd8/TV%20Shows/English/Lost%20in%20Space%20Season%2003%20(2021)%20Completed';
    return $urls;
}

