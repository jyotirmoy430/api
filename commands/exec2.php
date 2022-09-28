<?php
include 'helper.php';
include 'index.php';
include 'alif.php';
include 'alif2.php';
include 'series.php';




try{
    $alif2 = alif2();

}catch (Exception $exception){
    $alif2 = [];
}


$finalItems = $alif2;


$year = array();
foreach ($finalItems as $key => $row)
{
    $year[$key] = $row->year;
}
array_multisort($year, SORT_ASC, $finalItems);


$finalArrFull = [];
$counterMore = 0;
foreach($finalItems as $key=>$ff){
    $ff->id = $counterMore;
    $finalArrFull[] = $ff;
    $counterMore++;
}




file_put_contents("listncinebazar.json",json_encode($finalArrFull));

echo "completed";
exit;
