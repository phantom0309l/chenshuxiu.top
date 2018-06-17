<?php
$arr = [];
// 随机产生一些数字
for ($i = 0; $i < 5; $i ++) {
    $arr[] = rand(2, 10);
}

// 排序
sort($arr);
// 去重
$arr = array_unique($arr);

echo "\n============\n";
echo implode(",", $arr);
echo "\n============\n";

$min = $arr[0];
$max = array_sum($arr);

// 全求和
$sums = sums($arr);
// 排序
asort($sums);

echo "\n============\n";
print_r($sums);
echo "\n============\n";
$m = rand($min, $max);
echo " rand({$min},{$max}) => {$m} ";
echo "\n============\n";

$mK = 0;
$mV = 0;

foreach($sums as $k => $v){
    if($v > $m){
        break;
    }
    $mK = $k;
    $mV = $v;
}

echo "[{$mK}] => {$mV}";
echo "\n============\n";

// 全组合
function sums ($arr) {
    // 重置数组的key
    sort($arr);

    $arr2 = [];

    if(count($arr) == 2){
        $arr2[$arr[0].",".$arr[1]] = $arr[0]+$arr[1];
    }

    // 每个单值
    for($i=0; $i < count($arr); $i++){
        $arrClone = $arr;
        $x = $arrClone[$i];
        $arr2[$x] = $x;
        unset($arrClone[$i]); // 去掉一个值
        $arrSub = sums($arrClone);

        foreach($arrSub as $k => $v){
            $arr2[$k] = $v;
            $kArr = explode(',', $k);
            $kArr[] = $x;
            sort($kArr);
            $arr2[implode(',', $kArr)] = $x + $v;
        }
    }

    return $arr2;
}

