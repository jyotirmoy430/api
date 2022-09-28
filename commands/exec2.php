<?php
include 'helper.php';
include 'cinemabazar.php';

$cinemabazar = cinemabazar();

$year = array();
foreach ($cinemabazar as $key => $row)
{
    $year[$key] = $row->year;
}
array_multisort($year, SORT_ASC, $cinemabazar);


$finalArrFull = [];
$counterMore = 0;
foreach($cinemabazar as $key=>$ff){
    $ff->id = $counterMore;
    $finalArrFull[] = $ff;
    $counterMore++;
}




file_put_contents("listncinemabazar.json",json_encode($finalArrFull));

echo "completed";
exit;
