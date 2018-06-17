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

class Optask_test
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $patient = Patient::getById(550967796);
        $optasktpl = OpTaskTplDao::getOneByUnicode("PatientMsg:message");
        $this->checkIsNeedCreateOpTask($patient, $optasktpl);

        $unitofwork->commitAndInit();
    }

    // 是否需要任务
    private function checkIsNeedCreateOpTask ($patient, $optasktpl) {

        // patient 不存在
        if (false == $patient instanceof Patient) {
            Debug::warn("===== checkIsNeedCreate : patient不存在");
            return false;
        }

        // optasktpl 不存在
        if (false == $optasktpl instanceof OpTaskTpl) {
            Debug::warn("===== optasktpl不存在，请排查获取optasktpl时方法是否正确");
            return false;
        }

        // 默认模板必须生成
        if ($optasktpl->isDefault_optasktpl()) {
            return true;
        }

        // optasktpl 无效
        if ($optasktpl->isClosed()) {
            Debug::warn("===== optasktpl[{$optasktpl->getUnicode()}] 已为无效");
            return false;
        }

        // #4457 失活组、拒绝组不会生成新的任务 NMO
        if (in_array($patient->patientgroupid, [
            3,
            4])) {
            Debug::trace("===== #4457 失活组、拒绝组不会生成新的任务 NMO");
            return false;
        }

        // 无效患者, 不生成任务
        if ($patient->doubt_type == 1) {
            return false;
        }

        //黑名单患者, 不生成任务
        if($patient->isOnTheBlackList()){
            echo "=====[因加入黑名单而不能生成任务]=====";
            return false;
        }

        // 患者死亡，不创建任何新的任务，消息任务除外
        if ($patient->is_live == 0 && ($optasktpl->code != 'PatientMsg' || $optasktpl->subcode != 'message')) {
            Debug::trace("===== 患者死亡，不创建任何新的任务，消息任务除外");
            return false;
        }

        // 多动症, 检查
        if (1 == $patient->diseaseid) {
            $is_in_hezuo = $patient->isInHezuo("Lilly");

            // 礼来项目用户不生成分组作业任务
            $code = $optasktpl->code;
            if ($code == 'hwk' && $is_in_hezuo) {
                return false;
            }

            // 礼来项目用户加入项目时的服药时长是2个月（含）及以上，只生成消息任务
            if ($is_in_hezuo) {
                $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid('Lilly', $patient->id);

                if ($patient_hezuo->drug_monthcnt_when_create >= 2 && $code != 'PatientMsg' && $code != 'follow' && $code != 'wenzhen') {
                    return false;
                }
            }
        }

        echo "=====[可以生成任务]=====";
        return true;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Optask_test.php]=====");

$process = new Optask_test();
$process->dowork();

Debug::trace("=====[cron][end][Optask_test.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
