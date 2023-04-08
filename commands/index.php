<?php

function init(){
    $ONE = [
        'http://10.16.100.245/ftps2d',
        'http://10.16.100.245/ftps2d4',
        'http://10.16.100.206/ftps3',
        'http://10.16.100.202/ftps10',
        'http://10.16.100.212/iccftps12',
        'http://10.16.100.213/iccftps13',
        'http://10.16.100.250',
        'http://10.16.100.241',
        'http://10.16.100.241',
        'http://10.16.100.241',
        'http://10.16.100.214/iccftps14',
    ];

    $TWO = [
        '',
        '',
        'ftps3d',
        'iccftps10sasd',
        'iccftps12sasd',
        'iccftps13sasd',
        'ftps10d',
        'ftps2d',
        'ftps3d',
        'ftps4d',
        'iccftps14sasd',
    ];

    $THREE=[
        4,
        0,
        8,
        10,
        10,
        10,
        2,
        4,
        10,
        4,
        8,
    ];

    $MOVIE_FOLDER_URL = [
        'Movies',
        '',
        'Movies',
        'Movies',
        'Movies',
        'Movies',
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
        'Bangla%20(BD)',
        'Bangla%20(Kolkata)',
        'Chinese',
        'Dual%20Audio',
        'English',
        'Full%20HD',
        'Hindi',
        'Japanese',
        'Korean',
        'Other%20Foreign',
        'Punjabi',
        'Tamil%20Movie',
        'South%20Indian%20(Hindi%20Dubbed)',
        'South%20Indian',
        'Bangla/Kolkata',
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
    $catCounter = 0;
    if($urlWithLoop){
        foreach($urlWithLoop as $key=>$top){
            foreach($CATEGORY as $four){
                $takeUrl = $top.'/'.str_replace(' ', '%20', $four);
                $FINAL_URL[$catCounter]['url'] = $takeUrl;
                $FINAL_URL[$catCounter]['cat'] = $four;

                $catCounter++;
            }
        }
    }
    $FULL_FINAL_LIST = [];

    //$FINAL_URL = addCustom($FINAL_URL);


    $itemCounter = 0;

    if($FINAL_URL){
        foreach($FINAL_URL as $key=>$itemGet){
            $final = $itemGet["url"];

            $dataFromTable = getDataFromTableUsingUrl($final.'/', $itemGet['cat']);


            if(!empty($dataFromTable)){
                $FULL_FINAL_LIST = array_merge($FULL_FINAL_LIST, $dataFromTable);
                continue;
            }







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


                            $FULL_FINAL_LIST[$itemCounter]['url'] = $fullFinalUrl;
                            $FULL_FINAL_LIST[$itemCounter]['cat'] = $itemGet['cat'];
                            $FULL_FINAL_LIST[$itemCounter]['timestamp'] = 1396966731;
                            $itemCounter++;
                        }
                    }


                }


            }else{
                continue;
            }

        }
    }

    $FULL_FINAL = [];


    foreach($FULL_FINAL_LIST as $key=>$itemGet){

        $final = $itemGet['url'];
        $object = new stdClass();
        $object->id = $key;
        $object->video = $final;
        $object->timestamp = $itemGet['timestamp'];


        if($itemGet["size"]){
            $size = gbToByte($itemGet["size"]);
            if($size){
                $object->size = gbToByte($itemGet["size"]);
            }
        }
        if($itemGet["cat"]){
            $object->cat = $itemGet["cat"];
        }
        if($itemGet["name"]){
            $object->name = $itemGet["name"];
        }
        if($itemGet["date"]){
            $object->date = $itemGet["date"];
        }


        if(strpos($final, "2023") !== false){
            $object->year = 2023;
        } elseif(strpos($final, "2022") !== false){
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
        $object->cat = ($itemGet['cat'] == "Bangla/Kolkata") ? "Bangla%20(Kolkata)" : $itemGet['cat'];


        $FULL_FINAL[] = $object;

    }


    //file_put_contents("../listn.json",json_encode($FULL_FINAL));

    return $FULL_FINAL;
}

function gbToByte($gb){
    try{
        if (strpos(strtolower($gb), "g") !== false) {
            return $gb * 1073741824;
        } elseif(strpos(strtolower($gb), "m") !== false) {
            return $gb * 1048576;
        } else{
            return null;
        }

    }catch (\Exception $e){
        return null;
    }
}


function addCustom( $urls ){
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

