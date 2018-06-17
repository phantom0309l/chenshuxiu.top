<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

echo "\n\n-----begin----- " . XDateTime::now();

$unitofwork = BeanFinder::get("UnitOfWork");

$num = 0;

$openid_list = array();

$sql = " SELECT c.id
from patients a
left join users b on b.patientid=a.id
left join wxusers c on c.userid=b.id
inner join doctors d on d.id=a.doctorid
where a.status=1 and a.subscribe_cnt>0 and a.doubt_type=0 and a.diseaseid=1
and c.subscribe=1 and c.wxshopid=1 and d.hospitalid!=5 ";
//$sql = "select * from wxusers where wxshopid=1 and id in (103343971,834,105202835,108197723,102208279) and subscribe=1";
$openid_list = array();
$ids = Dao::queryValues($sql);

foreach ($ids as $id) {
    $wxuser = WxUser::getById($id);
    if ($wxuser instanceof WxUser) {
        $openid_list[] = $wxuser->openid;
    }
}

$listnum = count($openid_list);

echo "\n-----$listnum----- 111" . XDateTime::now();

function getlittleArr ($list) {
    $result = array();
    while (count($list) > 10) {
        $temp = array();
        while (count($temp) < 10) {
            $temp[] = array_shift($list);
        }
        $result[] = $temp;
    }
    $result[] = $list;
    return $result;
}
$result = getlittleArr($openid_list);

$jj = 0;
foreach ($result as $aa) {
    $num = $num + count($aa);
    $jj ++;
    $fields = array(
        "openid_list" => $aa,
        "to_groupid" => 128);
    $fields = urldecode(json_encode($fields));

    $wxshop = WxShop::getById(1);
    $access_token = $wxshop->getAccessToken();
    $url = "https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate?access_token={$access_token}";
    $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
    echo "\n-----$jj----- " . XDateTime::now();
    echo "\n-----$jsonStr----- " . XDateTime::now();

}
echo "\n-----$num----- " . XDateTime::now();

echo "\n-----$jsonStr----- " . XDateTime::now();

$unitofwork->commitAndInit();

Debug::trace("=====[cron][end][group.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
