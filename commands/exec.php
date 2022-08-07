<?php
include 'helper.php';
include 'index.php';
include 'alif.php';
include 'series.php';

$items = init();
//$items = [];
try{
    $alifItems = series();

}catch (Exception $exception){
    $alifItems = [];
}


$finalItems = array_merge($items, $alifItems);

$finalArrFull = [];
$counterMore = 0;
foreach($finalItems as $key=>$ff){
    $ff->id = $counterMore;
    $finalArrFull[] = $ff;
    $counterMore++;
}


file_put_contents("listn.json",json_encode($finalArrFull));

echo "completed";
exit;
