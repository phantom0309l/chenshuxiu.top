<?php
/*
 * @desc MPN特定用药患者发送通知
 *
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class SendWxMsg {
    public function __construct() {

    }

    public function run() {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $doctorid = 1507;
        $auditorid = 10048;//赖雪梅
        $auditor = Auditor::getById($auditorid);
        $cond = " AND doctorid=:doctorid AND is_test=0 AND status=1";
        $bind = [
            ':doctorid' => $doctorid,
        ];
        $patients = Dao::getEntityListByCond('Patient', $cond, $bind);
        foreach ($patients as $patient) {
            //忽略掉无效患者和测试患者
            if ($patient->status != 1 || $patient->is_test == 1) {
                echo "忽略测试患者和无效患者\n";
                continue;
            }
            $wxusers = WxUserDao::getListByPatient($patient);
            foreach ($wxusers as $wxuser) {
                echo $patient->id, " ", $patient->name, " ", $wxuser->nickname, "\n";
                $this->sendTxtMsg($wxuser, $auditor);
            }
        }

        //$wxuser = WxUser::getById('193857196');//李琨亭
        //$wxuser = WxUser::getById('120476785');//冯伟
        $wxuser = WxUser::getById('121691355');//赖雪梅
        $this->sendTxtMsg($wxuser, $auditor);
        $unitofwork->commitAndInit();
    }

    private function sendTxtMsg($wxuser, $auditor) {
        $content = '紧急温馨提示，罗教授14号周四上午到下周三下午在国外，周三晚上五点回到北京，下周四正常上班。25号周一下午追加一次内科专家门诊！！';
        PushMsgService::sendTxtMsgToWxUserByAuditor ($wxuser, $auditor, $content);
    }
}

$obj = new SendWxMsg();
$obj->run();

