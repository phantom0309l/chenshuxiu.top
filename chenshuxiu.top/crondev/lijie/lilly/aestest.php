<?php
/**
 *
 */
 ini_set("arg_seperator.output", "&amp;");
 ini_set("magic_quotes_gpc", 0);
 ini_set("magic_quotes_sybase", 0);
 ini_set("magic_quotes_runtime", 0);
 ini_set('display_errors', 1);
 ini_set("memory_limit", "2048M");
 include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
 include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
 mb_internal_encoding("UTF-8");

 TheSystem::init(__FILE__);

include_once (ROOT_TOP_PATH . "/../core/tools/AESCrypt.php");

$aes = new AESCrypt('ZzvRn9PLlWBqE2Z0lJ8ylv3GtYFkhLHb');

// $data = "{first: '您的患者报到情况如下：',keywords: ['2017-06-07', '1位'],remark: '您可以进入患者管理查看详情，感谢您的辛勤工作！'}";
// $data = $aes->encrypt($data);
// echo "加密：{$data}\n";

// $data = $aes->decrypt($data);
// $data = $aes->decrypt("DQVdddU83N+TabrcdZp1M6uIcKVm6mJJg8sEa0pPDscvoJ2qtWvXF1zVeuid2BdKDWPtdfoSFrDof0WmF7sfe5DMIJQ5yzk1eTvxf2eOUlbrEz07EvFhXNkYe5XYLosLxcyzjRetvonfZ5A9DwAwPFUHfksPIaFXQFwJZEckID8=");
// echo "加密：{$data}\n";
// $data = $aes->encrypt("_kuZrpBjkmxNULiYWye0YctTpBLmd-rNF6Q55Pw7U2q6Wi2uLEjnXa-wi3frY3B59Nk_f574hB1metjpciKedQ45QMPHE5UdC-cpUgM5ogUEDGfAIAMLO");
// echo "加密：{$data}\n";
//
// $data = $aes->decrypt($data);
// echo "解密：{$data}\n";


$result = "463mvCPGiXrwtuWXEYIhy9U0C5GYPscQ6fi8XLAw6R8pQelkSBGn4Kp3ACpdY2QFW3prnHJJBQd0+IWEwPYl39o8YFHeiEBDxgRq21bgyTkB9nAXOCr4B134UOGT+nzjgQl8id4829ao2XjtTLRzcg+a8/1AxK4YPGqZTiE4dIwM8+8Wy7vQVAIxYgiNzDk2+XPfErqvrMipKQnyV+j7diGYElKc5PZiehg7TqFaZ68g9fRWv/uRy04L7Wqg0ZcuS6DdFuDtz3L/xCirKy4tQSXJIAOuxSgnzwHS4QzKI7g=";

$result = $aes->decrypt($result);
echo "解密：{$result}\n\n";
