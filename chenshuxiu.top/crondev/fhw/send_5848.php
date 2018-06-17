<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Send_5848
{
    public function run()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select DISTINCT patientid
                from pcards
                where complication in ('复发-缓解型多发性硬化(RRMS)', ' 继发进展型多发性硬化(SPMS)') and doctorid = 33 ";
        $ids = Dao::queryValues($sql);
//        $ids = [595002516];

        $not_wxusers = [];

        $i = 0;
        $k = 0;
        $cnt = count($ids);
        foreach ($ids as $id) {
            $patient = Patient::getById($id);

            if (false == $patient instanceof Patient) {
                continue;
            }

            //忽略掉无效患者和测试患者
//            if ($patient->status != 1 || $patient->is_test == 1) {
//                continue;
//            }

            $wxusers = WxUserDao::getListByPatient($patient);
            if (count($wxusers) > 0) {
                foreach ($wxusers as $wxuser) {
                    $this->send($wxuser);
                }
            } else {
                $not_wxusers[] = $patient->id . " " . $patient->name;
            }

            $i++;
            if ($i % 100 == 0) {
                $k += 100;
                echo $k . "/" . $cnt . "\n";
                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }

        echo "{$cnt}/{$cnt}\n";

        print_r($not_wxusers);

        $unitofwork->commitAndInit();
    }

    private function send ($wxuser) {
        $content = "各位患友大家好，我们是MSNMO院外管理平台的随访助理，现已接到关于“注射用重组人工干扰素β-1b”的药品通知。具体关于“注射用重组人工干扰素β-1b”药品申请及医保报销等流程，请您在微信平台右下角『课程—患教课程—多发硬化课程』的『基本医疗保险门诊费手工报销流程』、『多发硬化备案医院』、『北京就医备案流程』里查看，请大家知晓。";

        PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);
    }
}

$send = new Send_5848();
$send->run();
