<?php
include 'helper.php';
include 'index.php';
include 'alif.php';
include 'series.php';



$items = init();
try{
    $allSeries = series();
}catch (Exception $exception){
    $items = [];
}


$finalItems = array_merge($items, $allSeries);

$year = array();
foreach ($finalItems as $key => $row)
{
    $year[$key] = $row->timestamp;
}
array_multisort($year, SORT_DESC, $finalItems);


$finalArrFull = [];
$counterMore = 0;
foreach($finalItems as $key=>$ff){
    $ff->id = $counterMore;
    $finalArrFull[] = $ff;
    $counterMore++;
}


file_put_contents("listn.json",json_encode($finalArrFull , JSON_UNESCAPED_SLASHES));

echo "completed";
exit;
