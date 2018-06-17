<?php
$dirname = dirname(__FILE__) . "/yp/";

$files = scandir($dirname);
foreach ($files as $file) {
    if ($file == "." || $file == "..") {
        continue;
    }

    echo "\n";
    echo $file;
    echo "\n";
    $content = file_get_contents($dirname . $file);
    echo $content = trim($content);

    echo "\n";
    $arr = json_decode($content, true);

    echo "\n";
    echo $str = json_encode($arr,JSON_UNESCAPED_UNICODE);
    echo "\n";
    echo "\n";
    exit();
}

function getYpArray () {
    return array(
        'YPBH' => '2015000098',  // 药品编号
        'CanUseYB' => '0',
        'PC' => 'CGD201707180001',
        'YPMC' => '酒精',
        'FPXMBH' => '1',
        'PrintName' => '酒精',
        'YPLBBH' => '1',
        'GG' => '100毫升/瓶*1/瓶',
        'MRSL' => '100',
        'YYTS' => '1',
        'PD' => '1',
        'MCYL' => '100',
        'JXDWBH' => '毫升',
        'YYZL' => '2',
        'YYZCS' => '1',
        'DJ' => '5',
        'YFZT' => '',
        'YFBH' => '42',
        'YFMC' => '口服',
        'BZ' => '1',
        'SFBH' => '',
        'DDW' => '瓶',
        'XDW' => '瓶',
        'YPZLDW' => '1',
        'HSL2' => '1',
        'ZH' => '1',
        'LRRBH' => '1029',
        'LRRMC' => '王福升',
        'XH' => '1',
        'HSBL' => '1.0000',
        'ZLDWMC' => '瓶',
        'ZLDWBH' => '4',
        'YPZL' => '2',
        'YPDJ' => '5.0000',
        'ZYYFBH' => '');
}
