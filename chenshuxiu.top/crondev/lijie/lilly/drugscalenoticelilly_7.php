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

// Debug::$debug = 'Dev';
$patient_id = $argv[1];

// 催评估后7天没做，没做用药给运营生成用药任务，没做评估给运营生成评估任务
class DrugScaleNoticeLilly_7
{

    public function dowork ($patient_id) {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patient_id, " and status=1 ");
        if (false == $patient_hezuo instanceof Patient_hezuo) {
            echo "\n=========没有找到合作患者！";
            return;
        }

        $fromdate = date('Y-m-d', strtotime($patient_hezuo->createtime));
        $adhd_papertpl = PaperTplDao::getByEname("adhd_iv");
        $QCD_papertpl = PaperTplDao::getByEname("QCD");
        $patient = $patient_hezuo->patient;

        // 如果最近一次催评估，催用药后，患者填写了（SNAP-IV评估，QCD评估，用药）跳出
        if ($this->haveFinishAll($patient, $fromdate, $adhd_papertpl->id, $QCD_papertpl->id)) {
            // return;
        }

        $drugitem = DrugItemDao::getByPatientid($patient->id, " and createtime>'{$fromdate}' ");
        if (false == $drugitem instanceof DrugItem) {
            // 创建任务: 基础用药任务, 已暂停
            OpTaskService::createPatientOpTask($patient, 'baseDrug:');
        }

        $paper_adhd = PaperDao::getLastByPatientidPapertplid($patient->id, $adhd_papertpl->id, " and createtime>'{$fromdate}' ");
        if (false == $paper_adhd instanceof Paper) {
            // 创建任务: 基础评估任务, 已暂停
            OpTaskService::createPatientOpTask($patient, 'baseScale:');
        }

        $unitofwork->commitAndInit();
        echo "\n=========成功！";
    }
}

// //////////////////////////////////////////////////////

$process = new DrugScaleNoticeLilly_7(__FILE__);
$process->dowork($patient_id);
