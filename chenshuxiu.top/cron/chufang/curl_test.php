<?php
foreach ($argv as $i => $file) {

    if ($i < 1) {
        continue;
    }

    sleep(1);
    echo "\n=================\n";
    echo $file;
    echo "\n---------0--------\n";

    echo $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
    $cmd = trim($cmd);

    echo "\n---------1--------\n";

    $jsonStr = system($cmd);

    $json = json_decode($jsonStr, true);

    echo "\n---------2--------\n";

    print_r($jsonStr);

    echo "\n---------3--------\n";
}
