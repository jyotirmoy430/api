<?php
include 'helper.php';
include 'circle.php';


$items = init();



$finalItems = $items;

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


file_put_contents("listncircle.json",json_encode($finalArrFull));

echo "completed";
exit;
