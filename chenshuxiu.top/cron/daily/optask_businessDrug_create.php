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

// Debug::$debug = 'Dev';
class Optask_businessDrug_create extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 商业化正在用药任务';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $yesterday = date("Y-m-d", time() - 1 * 86400);

        $medicineid_arr = Medicine::$masterMedicines;
        $medicineidstr = implode(',', $medicineid_arr);
        $sql = "select patientid from drugitems where medicineid in ({$medicineidstr}) and value > 0 and left(createtime, 10) = :createdate group by patientid";

        $bind = [];
        $bind[":createdate"] = $yesterday;
        $ids = Dao::queryValues($sql, $bind);

        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
            }

            $patient = Patient::getById($id);
            if ($patient instanceof Patient) {
                if($patient->diseaseid > 1){
                    continue;
                }
                if($patient->isInHezuo("Lilly")){
                    echo "\n====礼来合作患者patientid[{$id}]===";
                    continue;
                }
                // 过滤掉 有未结束的 sunflower首次电话任务 且 该患者的 doctor 是合作医生 的患者
                if($this->isInSunflowerFirstTelOptask($patient)){
                    echo "\n====未结束sunflower首次电话任务patientid[{$id}]===";
                    continue;
                }

                //没有服用 或 停用运营关心药物
                if ($this->isNotOrStopDrugAllMasterMedicineBeforeThedate($patient, $yesterday)) {
                    // 生成任务: 商业化正在用药任务
                    OpTaskService::createPatientOpTask($patient, 'businessDrug:create', null, '', 1);
                }
                echo "\n====id[{$id}]===" . XDateTime::now();
            }
        }
        $unitofwork->commitAndInit();
    }

    private function isNotOrStopDrugAllMasterMedicineBeforeThedate($patient, $thedate){
        $medicineid_arr = Medicine::$masterMedicines;
        $medicineidstr = implode(',', $medicineid_arr);
        $sql = "select *
            from drugitems
            where id in (
            select max(id)
            from drugitems
            where patientid = :patientid
            and createtime < :thedate
            and medicineid in ($medicineidstr)
            group by medicineid)";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $bind[":thedate"] = $thedate;
        $drugitems = Dao::loadEntityList("DrugItem", $sql, $bind);

        foreach ($drugitems as $drugitem) {
            if($drugitem->value > 0){
                return false;
            }
        }
        return true;
    }

    private function isInSunflowerFirstTelOptask($patient) {
        $optaskTpl = OpTaskTplDao::getOneByUnicode('firstTel:audit');
        $optask = OpTaskDao::getOneByPatientOptasktpl($patient,$optaskTpl);
        if($optask instanceof OpTask){
            return true;
        }else {
            return false;
        }
    }
}

// //////////////////////////////////////////////////////

$process = new Optask_businessDrug_create(__FILE__);
$process->dowork();
