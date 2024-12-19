<?php

function series()
{
    $ONE = [
        'http://10.16.100.245/ftps2d',
        'http://10.16.100.245/ftps2d4',
        'http://10.16.100.206/ftps3',
        'http://10.16.100.202/ftps10',
        'http://10.16.100.212/iccftps12',
        'http://10.16.100.213/iccftps13',
        'http://10.16.100.250',
        'http://10.16.100.214/iccftps14',
        'http://10.16.100.246',
    ];

    $TWO = [
        '',
        '',
        'ftps3d',
        'iccftps10sasd',
        'iccftps12sasd',
        'iccftps13sasd',
        'ftps10d',
        'iccftps14sasd',
        'ftps4d',
    ];

    $THREE = [
        4,
        0,
        8,
        10,
        10,
        10,
        2,
        8,
        4,
    ];

    $MOVIE_FOLDER_URL = [
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show',
        'Tv%20Show',
    ];

    $CATEGORY = [
        'Bangla',
        'English',
        'Hindi',
        'Bangla%20(BD)',
        'Bangla%20(Kolkata)',
        'Dual%20Audio',
        'Animation',
        'Serials%20(English)',
        'Serials%20(Dual%20Audio)',
        'Serials%20(Anime)',
        'Serials%20(Others)/Spain',
    ];


    $FINAL_URL = [];
    foreach ($ONE as $key => $one) {
        $initialUrl = $one;
        if ($TWO[$key] !== '') {
            $initialUrl = $initialUrl . '/' . $TWO[$key];
        }
        $loop = $THREE[$key];
        if ($loop !== 0) {
            for ($i = 1; $i <= (int)$loop; $i++) {
                $urlWithLoop[] = $initialUrl . $i . "/" . 'Tv%20Show';
                $urlWithLoop[] = $initialUrl . $i . "/" . 'Tv%20Shows';
                $urlWithLoop[] = $initialUrl . $i . "/" . 'TV%20Shows';
                $urlWithLoop[] = $initialUrl . $i . "/" . 'TV%20Show';
                $urlWithLoop[] = $initialUrl . $i . "/" . 'Tv%20Show';
            }
        } else {
            $urlWithLoop[] = $initialUrl . $MOVIE_FOLDER_URL[$key];
            $urlWithLoop[] = $initialUrl . 'Tv%20Shows';
            $urlWithLoop[] = $initialUrl . 'TV%20Shows';
            $urlWithLoop[] = $initialUrl . 'TV%20Show';
            $urlWithLoop[] = $initialUrl . 'Tv%20Show';
        }

    }

    $catCounter = 0;
    if ($urlWithLoop) {
        foreach ($urlWithLoop as $key => $top) {
            foreach ($CATEGORY as $four) {
                $takeUrl = $top . '/' . str_replace(' ', '%20', $four);
                $FINAL_URL[$catCounter]['url'] = $takeUrl;
                $FINAL_URL[$catCounter]['cat'] = $four;
                $catCounter++;
            }
        }
    }
    $FULL_FINAL_LIST = [];

    //$FINAL_URL = addCustomS($FINAL_URL);


    $OTHERS_FOLDERS = [];

    $itemCounter = 0;
    $otherCounter = 0;

    //http://10.16.100.213/iccftps13/iccftps13sasd6/TV%20Show/Serials%20(Others)/Spain/Money%20Heist%20(La%20Casa%20De%20Papel)%20Season%2001%20(2017)%20Completed/Money%20Heist%20(La%20Casa%20De%20Papel)%20S01E01.mp4


    if ($FINAL_URL) {
        foreach ($FINAL_URL as $key => $itemGet) {
            try {

                $final = $itemGet["url"];


                echo "final::<pre>";
                print_r($final);
                echo "</pre>\n\n";


                $data = get_web_page($final);


                if ($data["http_code"] == 200) {
                    $dom = new DomDocument();
                    $dom->loadHTML($data["content"]);


                    foreach ($dom->getElementsByTagName('a') as $item) {
                        $href = $item->getAttribute('href');

                        if (strpos($href, '?C=') !== false) {
                            continue;
                        }


                        if ($href) {
                            $takeHref = explode(".", $href);
                            if ($takeHref && (end($takeHref) == "mp4" || end($takeHref) == "MP4" || end($takeHref) == "mkv" || end($takeHref) == "MKV" || end($takeHref) == "avi")) {
                                $implode = implode(".", $takeHref);


                                $explodeFinal = explode("/", $final);
                                $explodeImplode = explode("/", $implode);

                                $makeFinal = [];
                                foreach ($explodeFinal as $key => $ex) {

                                    if (!in_array($ex, $explodeImplode) && !in_array($ex, $makeFinal)) {
                                        $makeFinal[] = $ex;
                                    }

                                }

                                $final = implode("/", $makeFinal);


                                if (endsWith($final, "/") || startsWith($implode, "/")) {
                                    $fullFinalUrl = $final . $implode;
                                } else {
                                    $fullFinalUrl = $final . "/" . $implode;
                                }


                                $fullFinalUrl = str_replace("http:/", "http://", $fullFinalUrl);


                                echo "final::<pre>";
                                print_r($fullFinalUrl);
                                echo "</pre>\n\n";


                                if (!in_array($fullFinalUrl, $FULL_FINAL_LIST)) {
                                    $FULL_FINAL_LIST[$itemCounter]['url'] = $fullFinalUrl;
                                    $FULL_FINAL_LIST[$itemCounter]['cat'] = $itemGet['cat'];
                                    $FULL_FINAL_LIST[$itemCounter]['timestamp'] = 1396966731;


                                    $itemCounter++;
                                }

                            } else {
                                $fullUrlHere = $final . ((strpos($takeHref[0], '/') === 0) ? '' : "/") . $takeHref[0];

                                if (!in_array($fullUrlHere, $OTHERS_FOLDERS)) {
                                    $OTHERS_FOLDERS[$otherCounter]["url"] = $fullUrlHere;
                                    $OTHERS_FOLDERS[$otherCounter]["cat"] = $itemGet['cat'];
                                    $otherCounter++;
                                }
                            }
                        }


                    }


                } else {
                    continue;
                }
            } catch (Exception $e) {
                echo "<pre>";
                print_r($e->getMessage());
                echo "</pre>";
                continue;
            }

        }
    }


    if ($OTHERS_FOLDERS && count($OTHERS_FOLDERS) > 0) {
        $FINAL_URL = $OTHERS_FOLDERS;

        if ($FINAL_URL) {
            foreach ($FINAL_URL as $key => $itemGet) {
                try {

                    $final = $itemGet["url"];

                    $dataFromTable = getDataFromTableUsingUrl($final, $itemGet['cat']);


                    if (!empty($dataFromTable)) {

                        foreach ($dataFromTable as $key => $hrefTable) {

                            $href = $hrefTable["url"];
                            $takeHref = explode(".", $href);

                            if ($takeHref && (end($takeHref) == "mp4" || end($takeHref) == "MP4" || end($takeHref) == "mkv" || end($takeHref) == "MKV" || end($takeHref) == "avi")) {
                                $FULL_FINAL_LIST[$itemCounter] = $hrefTable;
                                $itemCounter++;
                            }
                        }
                        continue;
                    }


                    $data = get_web_page($final);


                    if ($data["http_code"] == 200) {
                        $dom = new DomDocument();
                        $dom->loadHTML($data["content"]);


                        foreach ($dom->getElementsByTagName('a') as $item) {


                            try {
//$output[] = $item->getAttribute('href');
                                $href = $item->getAttribute('href');


                                if ($href) {
                                    $takeHref = explode(".", $href);
                                    if ($takeHref && (end($takeHref) == "mp4" || end($takeHref) == "MP4" || end($takeHref) == "mkv" || end($takeHref) == "MKV" || end($takeHref) == "avi")) {
                                        $implode = implode(".", $takeHref);

                                        $explodeFinal = explode("/", $final);
                                        $explodeImplode = explode("/", $implode);

                                        $makeFinal = [];
                                        foreach ($explodeFinal as $key => $ex) {

                                            if (!in_array($ex, $explodeImplode) && !in_array($ex, $makeFinal)) {
                                                $makeFinal[] = $ex;
                                            }

                                        }

                                        $final = implode("/", $makeFinal);

                                        if (endsWith($final, "/") || startsWith($implode, "/")) {
                                            $fullFinalUrl = $final . $implode;
                                        } else {
                                            $fullFinalUrl = $final . "/" . $implode;
                                        }
                                        $fullFinalUrl = str_replace("http:/", "http://", $fullFinalUrl);

                                        $FULL_FINAL_LIST[$itemCounter]['url'] = $fullFinalUrl;
                                        $FULL_FINAL_LIST[$itemCounter]['cat'] = $itemGet['cat'];
                                        $FULL_FINAL_LIST[$itemCounter]['timestamp'] = 1396966731;
                                        $itemCounter++;

                                    }
                                }
                            } catch (Exception $e) {
                                echo "<pre>";
                                print_r($e->getMessage());
                                echo "</pre>";
                                continue;

                            }


                        }


                    } else {
                        continue;
                    }
                } catch (Exception $e) {
                    echo "<pre>";
                    print_r($e->getMessage());
                    echo "</pre>";
                    continue;

                }

            }
        }
    }
    $FULL_FINAL = [];


    foreach ($FULL_FINAL_LIST as $key => $itemGet) {
        try{
            if (strpos(strtolower($itemGet['url']), "wwe") !== false) {
                echo "Substring found!";
                continue;
            }

            $final = $itemGet['url'];
            $object = new stdClass();
            $object->id = $key;
            $object->video = $final;
            $object->timestamp = $itemGet['timestamp'];


            /*if (!$itemGet['cat']) {

                if (strpos($final, "Tv%20Show") !== false) {
                    $object->cat = "Tv%20Show";
                } elseif (strpos($final, "TV%20Show") !== false) {
                    $object->cat = "Tv%20Show";
                } elseif (strpos($final, "Bangla") !== false) {
                    $object->cat = "Bangla";
                } elseif (strpos($final, "English") !== false) {
                    $object->cat = "English";
                } elseif (strpos($final, "Dual") !== false) {
                    $object->cat = "Dual";
                } elseif (strpos($final, "Animation") !== false) {
                    $object->cat = "Animation";
                } else {
                    $object->cat = "";
                }
            }*/


            if ($itemGet["size"]) {
                $size = gbToByte($itemGet["size"]);
                if ($size) {
                    $object->size = gbToByte($itemGet["size"]);
                }
            }
            if ($itemGet["cat"]) {
                $object->cat = $itemGet["cat"];
            }
            if ($itemGet["name"]) {
                $object->name = $itemGet["name"];
            }
            if ($itemGet["date"]) {
                $object->date = trim($itemGet["date"]);
            }


            if (strpos($final, "2023") !== false) {
                $object->year = 2023;
                $object->timestamp = strtotime("2023-01-01 12:00:00");;
            } elseif (strpos($final, "2022") !== false) {
                $object->year = 2022;
                $object->timestamp = strtotime("2022-01-01 12:00:00");;
            } elseif (strpos($final, "2021") !== false) {
                $object->year = 2021;
                $object->timestamp = strtotime("2021-01-01 12:00:00");;
            } elseif (strpos($final, "2020") !== false) {
                $object->year = 2020;
                $object->timestamp = strtotime("2020-01-01 12:00:00");;
            } elseif (strpos($final, "2019") !== false) {
                $object->year = 2019;
                $object->timestamp = strtotime("2019-01-01 12:00:00");;
            } elseif (strpos($final, "2018") !== false) {
                $object->year = 2018;
                $object->timestamp = strtotime("2018-01-01 12:00:00");;
            } elseif (strpos($final, "2017") !== false) {
                $object->year = 2017;
                $object->timestamp = strtotime("2017-01-01 12:00:00");;
            } elseif (strpos($final, "2016") !== false) {
                $object->year = 2016;
                $object->timestamp = strtotime("2016-01-01 12:00:00");;
            } else {
                $object->year = 0;
                $object->timestamp = 1396966731;
            }

            //$object->cat = ($itemGet['cat'] && $itemGet['cat'] == "Bangla/Kolkata") ? "Bangla%20(Kolkata)" : $itemGet['cat'];

            $object->cat = "Tv%20Show";
            echo "<pre>";
            print_r($object);
            echo "</pre>";

            $FULL_FINAL[] = $object;
        }catch (Exception $e){
            echo "<pre>";
            print_r($e->getMessage());
            echo "</pre>";

            continue;
        }
    }

    //file_put_contents("../listn.json",json_encode($FULL_FINAL));

    return $FULL_FINAL;
}


function addCustomS($urls)
{
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

