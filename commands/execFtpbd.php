<?php
ini_set("memory_limit", "-1");
//set_time_limit(0);

include 'helper.php';
include 'ftpbd.php';

$ftpbd = ftpbd();

$currentStr = file_get_contents(__DIR__ . '/../listnftpbd.json');
$currentJson = $currentStr ? json_decode($currentStr) : [];




$ftpbdMerged = array_merge($currentJson, $ftpbd);



$year = array();
foreach ($ftpbdMerged as $key => $row)
{
    $year[$key] = $row->year;
}
array_multisort($year, SORT_ASC, $ftpbdMerged);



$finalArrFull = [];
$counterMore = 0;

$sourceGet = [];
foreach($ftpbdMerged as $key=>$ff){


    if(!in_array($ff->video, $sourceGet)){
        $ff->id = $counterMore;
        $finalArrFull[] = $ff;
        $counterMore++;
        $sourceGet[] = $ff->video;
    }

}





file_put_contents("listnftpbd.json",json_encode($finalArrFull));

echo "completed";
exit;
