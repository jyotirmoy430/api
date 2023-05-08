<?php
include 'helper.php';
include 'webview.php';





$finalItems = webviewItems();



$year = array();
foreach ($finalItems as $key => $row) {
    $year[$key] = $row->timestamp;
}
array_multisort($year, SORT_DESC, $finalItems);


$finalArrFull = [];
$counterMore = 0;
foreach ($finalItems as $key => $ff) {
    $ff->id = $counterMore;
    $finalArrFull[] = $ff;
    $counterMore++;
}



file_put_contents("listnwebview.json", json_encode($finalArrFull));

echo "completed";
exit;
