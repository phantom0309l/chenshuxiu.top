<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/1/10
 * Time: 13:42
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

include_once(dirname(__FILE__) . "/../../../../core/tools/PHPExcel/Classes/PHPExcel/Calculation/Functions.php");

//MARK: - 用于根据医生姓名给患者群发 副反应管理开通通知

// 示例： 史老师，老史，老师
fwrite(STDOUT, "请输入医生names：");
$doctor_names = trim(fgets(STDOUT));
$doctor_names = mb_str_replace('，', ',', $doctor_names);
$name_arr = explode(',', $doctor_names);
doPreview($name_arr);

fwrite(STDOUT, "请输入医生ids（退出请输入n）：");
$doctor_ids = trim(fgets(STDOUT));
if ($doctor_ids == 'n' || $doctor_ids == 'N' || $doctor_ids == 'no' || $doctor_ids == 'NO' || $doctor_ids == 'false') {
    exit();
}
$doctor_ids = mb_str_replace('，', ',', $doctor_ids);
$id_arr = explode(',', $doctor_ids);
doSend($id_arr);

// 根据医生name预览
function doPreview($name_arr) {
    $id_arr = [];
    foreach ($name_arr as $name) {
        echo "\n";

        $list = DoctorDao::getListByName($name);
        if (empty($list)) {
            echo "未找到姓名为：{$name} 的医生\n\n";
            echo "-------------------------------------------\n";
            continue;
        }

        echo count($list) . " " . $name . "\n\n";
        foreach ($list as $doctor) {
            $id_arr[] = $doctor->id;
            echo "id：" . $doctor->id . "\n";
            echo "疾病：" . $doctor->getDiseaseNamesStr() . "\n";
            echo "公众号：" . $doctor->getWxShopNamesStr() . "\n";
            $patients = $doctor->getPatients();
            echo "患者数量：" . count($patients) . "\n\n";
        }
        echo "-------------------------------------------\n";
    }
    echo "\n医生ids：\n\n";
    echo implode(',', $id_arr);
    echo "\n\n";
}

// 根据id数组发送
function doSend($id_arr) {
    $unitofwork = BeanFinder::get("UnitOfWork");

    $brief = 0;
    foreach ($id_arr as $id) {
        $doctor = Doctor::getById($id);
        if ($doctor instanceof Doctor) {
            echo "\n" . $doctor->name . "：" . $doctor->id . "\n";
            echo "疾病：" . $doctor->getDiseaseNamesStr() . "\n";
            echo "公众号：" . $doctor->getWxShopNamesStr() . "\n";
            $patients = $doctor->getPatients();
            echo "患者数量：" . count($patients) . "\n\n";
            foreach ($patients as $patient) {
                sendMsg($doctor, $patient);
                $brief++;

                if ($brief % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
        } else {
            echo "\n";
        }
        echo "-------------------------------------------\n";
    }
    $unitofwork->commitAndInit();
    echo "\n\n共发送" . $brief . "条\n\n";
}

// 发送消息
function sendMsg($doctor, $patient) {
    $url = "http://wx.fangcunyisheng.com/shopproduct/listforcancer";

    $first = array(
        "value" => $doctor->name . '医生',
        "color" => "");
    $keywords = array(
        array(
            "value" => $patient->name,
            "color" => "#aaa"),
        array(
            "value" => date("Y-m-d"),
            "color" => "#aaa"),
        array(
            "value" => "为了方便您在家中及时处理口腔溃疡、恶心呕吐、血细胞低、肝功能异常及免疫力低等不良反应，避免为此去医院就诊开药的奔波之苦。新推出的“副反应管理”功能将为您按症状提供具体的解决方案（包括医生咨询及送药上门服务）。",
            "color" => "#ff6600"));
    $content = WxTemplateService::createTemplateContent($first, $keywords);
    PushMsgService::sendTplMsgToPatientBySystem($patient, 'followupNotice', $content, $url);
}