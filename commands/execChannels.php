<?php

include 'helper.php';
include 'channels.php';

$items = init();



file_put_contents("channel.json",json_encode($items));

echo "completed";
exit;
