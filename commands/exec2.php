<?php
ini_set("memory_limit", "-1");
//set_time_limit(0);

include 'helper.php';
include 'cinemabazar.php';

$cinemabazar = cinemabazar();

$currentStr = file_get_contents(__DIR__ . '/../listncinemabazar.json');
$currentJson = json_decode($currentStr);

$cinemabazarMerged = array_merge($currentJson, $cinemabazar);



$year = array();
foreach ($cinemabazarMerged as $key => $row)
{
    $year[$key] = $row->year;
}
array_multisort($year, SORT_ASC, $cinemabazarMerged);


$finalArrFull = [];
$counterMore = 0;

$sourceGet = [];
foreach($cinemabazarMerged as $key=>$ff){


    if(!in_array($ff->video, $sourceGet)){
        $ff->id = $counterMore;
        $finalArrFull[] = $ff;
        $counterMore++;
        $sourceGet[] = $ff->video;
    }

}





file_put_contents("listncinemabazar.json",json_encode($finalArrFull));

echo "completed";
exit;
