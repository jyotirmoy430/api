<?php


function loopAndTake($parent, $payload, $data){
    $HOST_ONLY = 'http://cinemabazar.net';

/*    echo "<pre>";
    print_r($parent);
    echo "</pre>";
    echo "<pre>";
    print_r($payload);
    echo "</pre>\n\n\n";*/



    $result = call($parent, $payload);

    /*if($parent == 'http://cinemabazar.net/DATA/NAS1/TV%20Series/Bangla%20TV%20Series/Tiktiki/s1/'){
        echo "aa<pre>";
        print_r($payload);
        echo "</pre>\n\n\n\n";
        exit;
    }*/

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
                            array_push($data, $HOST_ONLY.$item->href);


                            $data = array_unique($data);


                        }else{
                            $newParent = $HOST_ONLY.$item->href;


                            $newPayload = '{"action":"get","items":{"href":"'.$item->href.'","what":1}}';

                            $newItems = loopAndTake($newParent, $newPayload, $data);

                            $newData = array_merge($data, $newItems);


                            $data = array_unique($newData);

                            echo "<pre>";
                            print_r($data);
                            echo "</pre>";

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
}

function alif2(){
    $HOST = 'http://cinemabazar.net/DATA/NAS1/TV%20Series/Bangla%20TV%20Series/';


    $CATEGORY = [
        'Bangla TV Series'
    ];
    $counter = 0;
    $finalAlif = [];


    $parent = $HOST.'/';
    $payloadHref = '/DATA/NAS1/TV%20Series/Bangla%20TV%20Series/';
    $payload = '{"action":"get","items":{"href":"'.$payloadHref.'","what":1}}';
    $parent = str_replace(' ', '%20', $parent);
    $payload = str_replace(' ', '%20', $payload);


    $bigArr = loopAndTake($parent, $payload, []);

    echo "<pre>";
    print_r($bigArr);
    echo "</pre>";
    exit;


    exit;



    foreach($CATEGORY as $key=>$cat){
            $parent = $HOST.'/'.$cat.'/';
            $payloadHref = '/DATA/NAS1/TV%20Series/'.$cat.'/';
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

                            $parentSubCat = $HOST_ONLY.$item;
                            $payloadHrefSubCat = $item;
                            $payloadSubCat = '{"action":"get","items":{"href":"'.$payloadHrefSubCat.'","what":1}}';

                            /*echo "<pre>";
                            print_r($payloadSubCat);
                            echo "</pre>";
                            exit;*/

                            /*echo "<pre>";
                            print_r($payloadSubCat);
                            echo "</pre>";*/




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
                                                strpos($HOST.$subCatHref, ".MP4") !== false ||
                                                strpos($HOST.$subCatHref, ".avi") !== false ||
                                                strpos($HOST.$subCatHref, ".AVI") !== false ||
                                                strpos($HOST.$subCatHref, ".MKV") !== false ||
                                                strpos($HOST.$subCatHref, ".mkv") !== false){
                                                $object = new stdClass();
                                                $object->id = $counter;
                                                $object->flag = 1;
                                                $object->video = $HOST_ONLY.$subCatHref;
                                                $object->size = formatBytes($subCat->size);

                                                /*echo "<pre>";
                                                print_r($object);
                                                echo "</pre>";*/

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

    $FULL_FINAL = [];

    if($finalAlif){
        foreach($finalAlif as $key=>$final){

            $object = new stdClass();
            $object->id = $key;
            $object->video = $final->video;



            if(strpos($final->video, "2022") !== false){
                $object->year = 2022;
            } elseif(strpos($final->video, "2001") !== false){
                $object->year = 2001;
            } elseif(strpos($final->video, "2020") !== false){
                $object->year = 2020;
            } elseif(strpos($final->video, "2019") !== false){
                $object->year = 2019;
            } elseif(strpos($final->video, "2018") !== false){
                $object->year = 2018;
            } elseif(strpos($final->video, "2017") !== false){
                $object->year = 2017;
            } elseif(strpos($final->video, "2016") !== false){
                $object->year = 2016;
            } else{
                $object->year = 0;
            }

            echo "<pre>";
            print_r($object);
            echo "</pre>";

            $FULL_FINAL[] = $object;

        }
    }


    echo "<pre>";
    print_r($FULL_FINAL);
    echo "</pre>";
    exit;


    return $FULL_FINAL;

}

