<?php

error_reporting(0);

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



function getDataFromTableUsingUrl($url, $category)
{
    echo "Generating from ".$url."\n\n";

    try{
        $html = file_get_contents($url);

        if(!$html)
            return [];

        // Create a new DOMDocument object and load the HTML
        $dom = new DOMDocument();
        $dom->loadHTML($html);

        // Find the table element
        $table = $dom->getElementsByTagName('table')->item(0);

        // Initialize an empty array to hold the table data
        $data = array();

        if(!$table)
            return [];

        // Loop through the rows in the table
        foreach ($table->getElementsByTagName('tr') as $row) {

            // Initialize an empty array to hold the row data
            $rowData = array();

            // Loop through the cells in the row
            foreach ($row->getElementsByTagName('td') as $key => $cell) {
                // Add the cell data to the row data array
                $hrefItems = $row->getElementsByTagName('a');
                $href = '';
                foreach ($hrefItems as $item) {
                    //$output[] = $item->getAttribute('href');
                    $href = $item->getAttribute('href');


                }
                $rowData[] = $cell->textContent;
                if ($key === 0)
                    $rowData[] = $url . $href;
            }

            // Add the row data to the table data array
            $data[] = $rowData;
        }

        if(!$data){
            return [];
        }
        $takeArr = [];
        $counter = 0;

        foreach($data as $thisData){
            try{
                if(!$thisData[1] || $thisData[1] == '' || ($thisData[2] && $thisData[2] == 'Parent Directory')){
                    continue;
                }

                $takeArr[$counter]['url'] = $thisData[1];
                $takeArr[$counter]['size'] = $thisData[4];
                $takeArr[$counter]['date'] = $thisData[3];
                $takeArr[$counter]['timestamp'] = strtotime($thisData[3]);
                $takeArr[$counter]['name'] = $thisData[2];
                $takeArr[$counter]['cat'] = $category;
                $takeArr[$counter]['year'] = date("Y", strtotime($thisData[3]));
                $takeArr[$counter]['fromTable'] = 1;
                $counter++;
            }catch (\Exception $e){
            }
        }
        return $takeArr;
    }catch (\Exception $e){
        return [];
    }
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
