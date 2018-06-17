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

function needDrugCheck($mypatient){
    $need = false;
    $lastdrugitem = $mypatient->getLastDrugItem();
    if( false == $lastdrugitem instanceof DrugItem ){
        $need = true;
    }else{
        $createtime = $lastdrugitem->createtime;
        $createtime = strtotime( $createtime );
        $now = time();
        if( $now - $createtime > 7*86400 ){
            $need = true;
        }
    }
    return $need;
}

function sendmsg ($patient) {
    // 得到模板内容
    if ($patient instanceof Patient) {
        $wxuser = $patient->createuser->createwxuser;
        if( $wxuser instanceof WxUser ){
            $str = "医生助理";
            $content = "{$patient->doctor->name}医生助理来核对孩子用药信息了，请点击此消息更新当前用药";
            $openid = $wxuser->openid;

            $first = array(
                "value" => "",
                "color" => "");
            $keywords = array(
                array(
                    "value" => $str,
                    "color" => "#aaa"),
                array(
                    "value" => $content,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);
            $url = Config::getConfig("wx_uri")."/patientmedicineref/record?openid={$openid}";

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
        }
    }
}

$unitofwork = BeanFinder::get("UnitOfWork");
$sql = "select id from patients where status=1 and subscribe_cnt>0 and diseaseid=1";
$ids = Dao::queryValues($sql);
$i = 0;
foreach ($ids as $id) {
    $patient = Patient::getById( $id );
    if( $patient instanceof Patient ){
        $need = needDrugCheck($patient);
        if( $need ){
            sendmsg($patient);
            echo "\n====[{$patient->id}][{$i}]===\n";
            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }
    }
}
$unitofwork->commitAndInit();
