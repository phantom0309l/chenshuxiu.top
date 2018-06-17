<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/8/18
 * Time: 16:28
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

echo "\n\n-----begin----- " . XDateTime::now() . "\n\n";
$unitofwork = BeanFinder::get("UnitOfWork");

// #4507，360为段明辉，10为MPN诊后管理, 101为段明辉定制分组id
$doctorid = 360;
$wxshopid = 10;
$groupid = 101;

$cond = ' AND doctorid = :doctorid AND wxshopid = :wxshopid ';
$bind = [
    ':doctorid' => $doctorid,
    ':wxshopid' => $wxshopid,
];
$wxusers = Dao::getEntityListByCond('WxUser', $cond, $bind);
$count = count($wxusers);
echo "共 ".count($wxusers)." 个WxUser\n\n";

$wxshop = WxShop::getById($wxshopid);
$maximum = 50;
$group_count = ceil($count / $maximum);
echo "向上取整共 {$group_count} 组\n\n";
for ($i = 0; $i < $group_count; $i++) {
    $arr = array_slice($wxusers, $i * $maximum, $maximum);
    $errmsg = WxApi::batchTagging($wxshop, $arr, $groupid);
    echo $errmsg;
    echo "\n\n";
}

// #4141，1207为李琳，19为Cancer院外管理, 101为李琳定制分组id
//$doctorid = 1207;
//$wxshopid = 19;
//$groupid = 101;
//
//$cond = ' AND doctorid = :doctorid AND wxshopid = :wxshopid ';
//$bind = [
//    ':doctorid' => $doctorid,
//    ':wxshopid' => $wxshopid,
//];
//$wxusers = Dao::getEntityListByCond('WxUser', $cond, $bind);
//echo "共 ".count($wxusers)." 个WxUser\n\n";
//
//$wxshop = WxShop::getById($wxshopid);
//$errmsg = WxApi::batchTagging($wxshop, $wxusers, $groupid);
//echo $errmsg;

// 临时的一个需求，程志强改分组
//$wxshopid = 2;
//$groupid = 100;
//
//$wxuser = WxUser::getById(311079276);
//$wxusers = [$wxuser];
//
//$wxshop = WxShop::getById($wxshopid);
//$errmsg = WxApi::batchTagging($wxshop, $wxusers, $groupid);
//echo $errmsg;

echo "\n\n";
$unitofwork->commitAndInit();