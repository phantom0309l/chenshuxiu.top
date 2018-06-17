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

class Lilly_patient_check extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 07:50 Check_Patient_hezuo 检查sunflower项目患者的扫码情况。';
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
        $today = date('Y-m-d');

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = " select id from patient_hezuos where company='Lilly' and patientid!=0 and status = 1 ";
        // $sql = " select id from patient_hezuos where id=278144116 ";

        $ids = Dao::queryValues($sql);

        $i = 0;

        foreach ($ids as $id) {
            if ($i == 100) {
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $i = 0;
            }

            $patient_hezuo = Patient_hezuo::getById($id);

            $patient = $patient_hezuo->patient;
            if (false == $patient instanceof Patient) {
                continue;
            }

            if (0 == $patient->subscribe_cnt) {
                // 生成任务: 取关退出项目跟进 (患者唯一)
                OpTaskService::tryCreateOpTaskByPatient($patient, 'follow:outSunflower', null, '', 1);

                $patient_hezuo->goOut(7);
            }

            $doctor = $patient->doctor;
            if (false == $doctor instanceof Doctor) {
                continue;
            }
            echo "\ndoctorid=[{$doctor->id}]";

            $ishezuo = $doctor->isHezuo("Lilly");

            // if($patient_hezuo->status == 1){
            // 如果当前扫码医生不是合作医生，移出sunflower组
            if (false == $ishezuo) {
                $patient_hezuo->goOut(6);
                echo "\npatient_hezuo status=[6]";
                continue;
            }

            // 考虑合并患者，被合并过来的weuser的菜单也需要变动
            if ($ishezuo) {
                $patient_hezuo->addGroup();
                echo "\n合并患者所有的关注更新个性化菜单。";
                continue;
            }
            // }

            // if($patient_hezuo->status == 6){
            // //如果当前扫码医生是合作医生，重新入sunflower组
            // if($ishezuo){
            // //是否到期自动出sunflower组
            // if($patient_hezuo->needAutoOut()){
            // $patient_hezuo->goOut(2);
            // continue;
            // }
            //
            // //是否不活跃出sunflower组
            // // if($patient_hezuo->needNotActiveOut()){
            // // $patient_hezuo->goOut(3);
            // // continue;
            // // }
            //
            // $patient_hezuo->goInto();
            // $patient_hezuo->addGroup();
            // echo "\npatient_hezuo status=[1]";
            // continue;
            // }
            // }

            $i ++;
        }

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new Lilly_patient_check(__FILE__);
$cnt = $process->dowork();
