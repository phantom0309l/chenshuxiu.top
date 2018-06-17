<?php
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

// $file = ROOT_TOP_PATH . "/domain/tpl/config/msg_template.yml";

 $unitofwork = BeanFinder::get ( "UnitOfWork" );
function getDetailStartEndTime ($starttime, $endtime) {
    $result = array();
    $s = strtotime($starttime);
    $e = strtotime($endtime);

    $tail1 = date("H:i:s", $s);
    $tail2 = date("H:i:s", $e);

    $head1 = date("Y-m-d", $s);
    $head2 = date("Y-m-d", $e);
    $onedaytime = 24 * 60 * 60;
    $day = 1 + (strtotime($head2) - strtotime($head1)) / $onedaytime;

    for ($i = 0; $i < $day; $i ++) {
        $arr = array();
        $time = strtotime($head1) + $i * $onedaytime;
        $arr['starttime'] = date("Y-m-d", $time) . " {$tail1}";
        $arr['endtime'] = date("Y-m-d", $time) . " {$tail2}";
        $result[] = $arr;
    }
    print_r($result);
}
$starttime = "2016-03-09 20:00:00";
$endtime = "2016-03-29 23:59:59";
// getDetailStartEndTime($starttime, $endtime);

/*$content = "{{first.DATA}}\n姓名：{{keyword1.DATA}}\n就诊医院：{{keyword2.DATA}}\n就诊医生：{{keyword3.DATA}}\n复诊时间：{{keyword4.DATA}}\n{{remark.DATA}}";

$regex = '/\n(.+)：/';
$match = array();
preg_match_all($regex,$content,$match);
echo $match[0][0];
echo $match[0][1];
echo $match[1][0];*/

/*$str = "杨莉密码";
$regex = '/^(.+)密码$/';
preg_match($regex,$str,$match);
echo $match[1];*/

function getRandStr($len){
  $chars_array = array(
    "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
    "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
    "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
    "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
    "S", "T", "U", "V", "W", "X", "Y", "Z",
  );
  $charsLen = count($chars_array) - 1;

  $outputstr = "";
  for ($i=0; $i<$len; $i++){
    $outputstr .= $chars_array[mt_rand(0, $charsLen)];
  }
  return $outputstr;
}
//echo getRandStr(4);

//$no1 = "130503670433001";
$no1 = "37028219871120513x";

//$sex = IDCard::getSex($no1);
//$birthday = IDCard::getBirthday($no1);

//$isTrue = IDCard::isCreditNo($no1);

//echo "\n[性别]{$sex}\n";
//echo "\n[生日]{$birthday}\n";
//echo "\n[真假]{$isTrue}\n";

//$wxuser = WxUser::getById(97);
//$wxuser->wx_ref_code = "adhd_bjtest_shilaoshi:3";

//$doctor = DoctorDao::getByCode("adhd_bjtest_shilaoshi:3");

//echo "\ndoctorid[{$doctor->id}]\n";

/*$txt = "";
$regex = '/[a-zA-Z0-9]+/';
if(false == preg_match($regex,$txt,$match)){
    echo "没匹配上了";
}else{
    echo "匹配上了";
}*/

//$patient = Patient::getById(308627186);
//$pos = ShopOrderDao::getShopOrderCntByPatient($patient) + 1;
//echo "\npos[{$pos}]\n";

//测试支付入库
//$shopOrderItem = ShopOrderItem::getById(315601316);
//ShopProductService::goodsOut($shopOrderItem);
//$planArr = DoctorServiceOrderService::planArr();
//$a = $planArr["80"];
//echo "123\n";
//print_r($a);

//$wxuser = WxUser::getById(97);
//WxApi::MvWxuserToGroup($wxuser, 141);

//下订单
/*$a = new BSPOrderService();
$params = array(
    "j_company" => "方寸医生",
    "j_contact" => "占小姐",
    "j_tel" => "010-60643332",
    "j_mobile" => "18510542099",
    "j_address" => "北京市西城区华远北街通港大厦708",
    "parcel_quantity" => 2
);

$addedServices = array(
    array(
        "name" => "COD", //代收货款
        "value" => 10.21, //货款，保留 3 位小数
        "value1" => "0100026792" //代收货款卡号
    )
);
$d = $a->Order(294403593,"张三","18701503285","山东省青岛市即墨市七级镇中岔河村133号", $params, $addedServices);
print_r($d);*/

//订单结果查询
/*$b = new BSPOrderSearchService();
$d = $b->OrderSearch(294403593);
print_r($d);*/

//订单确定
/*$b = new BSPOrderConfirmService();
$d = $b->OrderConfirm(294403593,444007034417);
print_r($d);*/

//订单取消
/*$b = new BSPOrderConfirmService();
$d = $b->OrderCancel(294403593,444007034417);
print_r($d);*/

//路由查询接口
//$b = new BSPRouteService();
//$d = $b->RouteRequest(2,294403592);
//$d = $b->RouteRequest(1,824467001929);
//print_r($d);

/*$user = User::getById(10004);
$patient = $user->patient;
$pcard = $patient->getMasterPcard();
echo "\npcard[$pcard->id]\n";

$wxusers = $patient->getWxUsers();
foreach($wxusers as $wxuser){
    echo "\nwxuser[$wxuser->nickname][$wxuser->id]\n";
}

$shopOrder = ShopOrder::getById(478977786);
$is_payCntOfToday = $shopOrder->getIs_payCntOfToday() + 1;
$shopProductTitleStr = $shopOrder->getShopProductTitleStr();

echo $shopProductTitleStr;

$content = "测试消息忽略，测试消息忽略 \nPrice[{$shopOrder->amount}]Patient[{$shopOrder->patient->name}]Doctor[{$shopOrder->patient->doctor->name}]成功支付订单,ShopOrder[{$shopOrder->id}]\nTodayCnt[{$is_payCntOfToday}]{$shopProductTitleStr}";
//PushMsgService::sendMsgToAuditorBySystem('ShopOrder', 1, $content);
echo $content;*/

/*function getHourDiff($lhourstr, $rhourstr){
    $time1 = strtotime($lhourstr);
    $time1_str = date("Y-m-d H:i:s", $time1);
    echo "\ntime1[{$time1_str}]\n";
    $time2 = strtotime($rhourstr);
    $time2_str = date("Y-m-d H:i:s", $time2);
    echo "\ntime2[{$time2_str}]\n";
    $time = 86400 - ($time1 - $time2);
    $h0 = floor($time/3600);
    $h1 = round((floor($time/60) % 60)/60, 2);
    $h = $h0 + $h1;
    return $h;
}

//$str = getHourDiff("20:50", "6:30");
//echo "\n{$str}\n";

function getHourStr($hourstr, $minute){
    $time = strtotime($hourstr);
    $time = $time + $minute*60;
    $time_str = date("H:i", $time);
    echo "\ntime_str[{$time_str}]\n";
}

getHourStr("19:30", 31);*/

/*$a = ['aaa', 'aaa', 'aaa', 'ddd', 'aaa'];

for ($i = 0; $i < count($a); $i ++) {
    if ($a[$i] == 'aaa') {
        //array_splice($a, $i, 1);
        //print_r($a);
        unset($a[$i]);
    }
}

print_r($a);*/

/*$kdniaoservice = new KdniaoService();
$arr = [];
$arr["OrderNos"] = "601566930188,450718776";
$data = $kdniaoservice->getRegData($arr);

print_r($data);*/

//$patient = Patient::getById(311849386);
//$ispay_chufang_cnt = ShopOrderDao::getIsPayShopOrderCntByPatientType($patient, ShopOrder::type_chufang);


//$shopOrder = ShopOrder::getById(556850606);
//$result = ExpressService::tracesSub($shopOrder);

//$result = json_encode($result, JSON_UNESCAPED_UNICODE);
//echo "\n==result[{$result}]===\n";

function getPaperCntOfThedateByPatientid ($patientid, $thedate) {
    $sql = "select count(*) as cnt
        from papers
        where patientid = :patientid and createtime > :startdate and createtime < :enddate";

    $bind = [];
    $bind[':patientid'] = $patientid;
    $bind[':startdate'] = $thedate;
    $bind[':enddate'] = date("Y-m-d", strtotime($thedate) + 86400);

    $cnt = Dao::queryValue($sql, $bind);
    echo "\n==cnt[{$cnt}]===\n";
}

//$patient = Patient::getById(538065006);

//getPaperCntOfThedateByPatientid($patient->id, "2018-01-23");

$str = "{&#34;PushTime&#34;:&#34;2018-01-23 17:07:09&#34;,&#34;EBusinessID&#34;:&#34;1308158&#34;,&#34;Data&#34;:[{&#34;LogisticCode&#34;:&#34;617936657622&#34;,&#34;ShipperCode&#34;:&#34;SF&#34;,&#34;Traces&#34;:[{&#34;AcceptStation&#34;:&#34;顺丰速运 已收取快件&#34;,&#34;AcceptTime&#34;:&#34;2018-01-23 15:26:04&#34;},{&#34;AcceptStation&#34;:&#34;快件在【北京西城西四营业点】装车，已发往 【北京紫竹院集散中心】&#34;,&#34;AcceptTime&#34;:&#34;2018-01-23 17:05:57&#34;}],&#34;State&#34;:&#34;2&#34;,&#34;EBusinessID&#34;:&#34;1308158&#34;,&#34;Success&#34;:true}],&#34;Count&#34;:&#34;1&#34;}";


$str = str_replace("&#34;", '"', $str);
//echo "\n==str[{$str}]===\n";

//$local_file = ROOT_TOP_PATH ."/crondev/txj/test/aa.txt";
//$ftp = new FTPService(); // 打开FTP连接
//$ftp->up_file($local_file,"upload/aa.txt"); // 上传文件
//$ftp->move_file('a/b/c/cc.txt','a/cc.txt'); // 移动文件
//$ftp->copy_file('a/cc.txt','a/b/dd.txt'); // 复制文件
//$ftp->del_file('a/b/dd.txt'); // 删除文件

//$local_file = ROOT_TOP_PATH ."/crondev/txj/test/aa.txt";
//$remote_file = 'upload/readme112.txt';

//$ftp->uploadImp($local_file, $remote_file);
//$ftp->close(); // 关闭FTP连接

//$shopOrder = ShopOrder::getById(637859256);
//$aa = GuanYiService::tradeAddByShopPkg($shopOrder);
//$shopOrder = ShopOrder::getById(63785956);
//$aa = GuanYiService::tradeDeliverysGetOfDoneByShopPkg($shopOrder);

//$aa = GuanYiService::provincesGet();
//$result = json_encode($aa, JSON_UNESCAPED_UNICODE);
//echo "\n==result[{$result}]===\n";

//$shopProduct = ShopProduct::getById(315873746);
//$aa = GuanYiService::itemAddByShopProduct($shopProduct);
//echo "\n{$aa}\n";
//$result = json_encode($aa, JSON_UNESCAPED_UNICODE);
//echo "\n==result[{$result}]===\n";

//$aa = GuanYiService::getFixProvince("北京");
//echo "\n{$aa}\n";

//查询发货成功的订单
//$data = array();
//平台单号
//$data["outer_code"] = 63785956;
//发货状态
//$data["delivery"] = 1;
//$aa = GuanYiService::tradeDeliverysGetImp($data);
//$result = json_encode($aa, JSON_UNESCAPED_UNICODE);
//echo "\n==result[{$result}]===\n";

//$shopOrder = ShopOrder::getById(649151476);
//$shopOrder->need_push_erpSet();


//采购入库单新增
//$aa = GuanYiService::purchaseArriveAdd("ZST1070000001", 182, "2019-8");
//echo "\n{$aa}\n";
//$result = json_encode($aa, JSON_UNESCAPED_UNICODE);
//echo "\n==result[{$result}]===\n";


/*$aa = TianRunService::getCdrObList("2018-04-27 00:00:00", "2018-04-28 00:00:00", "15953250469");
$result = json_encode($aa, JSON_UNESCAPED_UNICODE);
echo "\n==result[{$result}]===\n";

$aa = TianRunService::asrDownload("3004870-20180514103020-13601190986-18101272021-record-10.10.57.17-1526265020.36342");
$result = json_encode($aa, JSON_UNESCAPED_UNICODE);
echo "\n==result[{$result}]===\n";*/

$shopOrder = ShopOrder::getById(746788576);
$str = ShopPkgService::getInitExpress_company($shopOrder);
echo "\n==result[{$str}]===\n";



$unitofwork->commitAndInit ();
