<?php

file_put_contents("log.txt", $$_SERVER['REMOTE_ADDR'] . "\n", FILE_APPEND);

echo "logged";