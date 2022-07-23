<?php
include 'helper.php';
include 'index.php';
include 'alif.php';

$items = init();
try{
    $alifItems = alif();

}catch (Exception $exception){
    $alifItems = [];
}

$finalItems = array_merge($items, $alifItems);

$finalArrFull = [];
foreach($finalItems as $key=>$ff){
    $ff->id = $key;
    $finalArrFull[] = $ff;
}


file_put_contents("listn.json",json_encode($finalArrFull));

echo "completed";
exit;
