<?php
ini_set('display_errors', 0);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Export_data
{
    public function run ($id) {
        $j = 0;
        while ($j < 2) {
            try {
                $unitofwork = BeanFinder::get("UnitOfWork");

                $i = 0;
                $exportJob = null;
                while ($i < 50) {
                    $exportJob = Dao::getEntityById('Export_Job', $id);
                    if (! $exportJob) {
                        $i ++;
                        usleep(200000);
                        continue;
                    }
                    break;
                }

                // 没找到任务
                if (false == $exportJob instanceof Export_Job) {
                    Debug::warn(__METHOD__ . ' export job is null id [' . $id . ']');
                    Debug::flushXworklog();
                    return false;
                }

                // 不是初始状态
                if (! $exportJob->isNew()) {
                    Debug::trace(__METHOD__ . 'export job [' . $id . '] status is not new');
                    Debug::flushXworklog();
                    return true;
                }

                // 开始运行导出任务
                // 给任务置一个状态
                $exportJob->status = Export_Job::STATUS_RUNNING;
                $export_job_type = $exportJob->type;

                if($export_job_type == "sunflowerforpatient"){
                    $this->createExcelSunflowerForPatient($exportJob);
                }

                if($export_job_type == "ADHD_KPI"){
                    $this->createExcelADHD_KPI($exportJob);
                }

                $unitofwork->commitAndInit();
                break; // 跳出外层循环
            } catch (Exception $e) {
                print_r($e->getMessage());
                $j ++;
                BeanFinder::clearBean("UnitOfWork");
                BeanFinder::clearBean("DbExecuter");
                $dbExecuter = BeanFinder::get('DbExecuter');
                $dbExecuter->reConnection();
                Debug::warn('export job fail ' . $j . ' jobid:' . $id);
            }
        }
        Debug::trace('export job has done success jobid:' . $id);
        Debug::flushXworklog();

        return true;
    }

    //导出sunflower数据（患者纬度）
    private function createExcelSunflowerForPatient($exportJob){
        $unitofwork = BeanFinder::get("UnitOfWork");
        $exportJobid = $exportJob->id;
        $arr = json_decode($exportJob->data, true);
        $startdate = isset($arr["startdate"]) ? $arr["startdate"] : '0000-00-00';
        $enddate = isset($arr["enddate"]) ? $arr["enddate"] : '0000-00-00';

        $sql = "select a.id
            from patients a
            inner join doctor_hezuos b on b.doctorid=a.doctorid
            inner join doctors c on c.id=a.doctorid
            where b.status=1 and a.createtime>b.starttime
            and a.status=1 and a.is_test=0 and c.hospitalid!=5
            and a.createtime >= :startdate and a.createtime < :enddate";

        $bind = [];
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        $ids = Dao::queryValues($sql, $bind);
        $len = count($ids);
        $m = 0;
        $data = array();
        foreach ($ids as $i => $id) {
            echo "===$id===$i---\n";
            $m++;
            if ($m > 0 && $m % 100 == 0) {
                $exportJob->progress = round(($m / $len) * 100, 1);

                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $exportJob = Dao::getEntityById('Export_Job', $exportJobid);
            }

            $patient = Patient::getById($id);
            $patientid = $patient->id;
            $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid('Lilly', $patientid);
            $doctor_hezuo = Doctor_hezuoDao::getOneByCompanyDoctorid("Lilly", $patient->doctorid);

            $drugdiffday = $this->getDrugDiffDayForBase($patient);
            $paperdiffday = $this->getPaperDiffDayForBase($patient);

            $temp = array();
            $temp[] = $patientid;
            $temp[] = $patient->doctor->name;
            $temp[] = $doctor_hezuo instanceof Doctor_hezuo ? $doctor_hezuo->marketer_name : '';
            $temp[] = $patient->getCreateDay();
            $optask_firstTel = OpTaskDao::getOneByPatientUnicode($patient, 'firstTel:audit', false);
            $temp[] = $optask_firstTel instanceof OpTask ? $optask_firstTel->getCreateDay() : '';
            $drugitem = Dao::getEntityByCond("DrugItem", " and patientid={$patientid} and createtime<'{$optask_firstTel->donetime}' ");
            $temp[] = $drugitem instanceof DrugItem ? '是' : '否';

            if($patient_hezuo instanceof Patient_hezuo){
                $temp[] = '是';
                $temp[] = $patient_hezuo->getCreateDay();
                $temp[] = $patient_hezuo->drug_monthcnt_when_create . "个月";
                $sql = " select count(id) as cnt
                    from cdrmeetings
                    where patientid = {$patientid} and cdr_bridge_time>0 and createtime<'{$patient_hezuo->createtime}' ";
                $temp[] = Dao::queryValue($sql);
                $temp[] = $patient_hezuo->getStatusStr();
                $temp[] = $patient_hezuo->enddate;
            } else {
                $temp[] = '否';
                $temp[] = '';
                $temp[] = '';
                $temp[] = 0;
                $temp[] = '';
                $temp[] = '';
            }

            $patientpgroupref = Dao::getEntityByCond("PatientPgroupRef", " and patientid={$patientid} ");
            $studyplan_done = Dao::getEntityByCond("StudyPlan", " and patientid={$patientid} and objcode='hwk' ");
            $temp[] = $patientpgroupref instanceof PatientPgroupRef  ? "是" : "否";
            $temp[] = $studyplan_done instanceof StudyPlan  ? "是" : "否";
            $temp[] = $this->getAECntByPatientid($patientid);
            $temp[] = $this->getPCCntByPatientid($patientid);
            $sql = " select group_concat(b.name) as names
                from tagrefs a
                inner join tags b on b.id=a.tagid
                where a.objtype='Patient' and a.objid = {$patientid} and a.tagid in (176, 177, 178, 179) group by a.objid";
            $temp[] = Dao::queryValue($sql);
            $data[] = $temp;
        }

        $headarr = array(
            "patientID",
            "所属医生",
            "所属礼来代表",
            "报到日期",
            "首次电话任务生成日期",
            "首次电话任务关闭前，是否有用药记录",
            "是否加入项目（是、否）",
            "入项目日期",
            "入组时的服药时长",
            "加入项目前有几次双方接通的电话",
            "当前状态",
            "出组日期",
            "是否有过入课程行为",
            "是否提交过作业",
            "AE 的数量",
            "PC 的数量",
            "未加入项目原因");
        $this->writeFile($exportJob, $data, $headarr);
        $unitofwork->commitAndInit();
    }

    private function getDrugDiffDayForBase($patient){
        $baodaoDate = $patient->getCreateDay();
        $drugitem = Dao::getEntityByCond("DrugItem", " and patientid={$patient->id} ORDER BY id ");
        if($drugitem instanceof DrugItem){
            return XDateTime::getDateDiff($drugitem->getCreateDay(), $baodaoDate);
        }
        return "无";
    }

    private function getPaperDiffDayForBase($patient){
        $baodaoDate = $patient->getCreateDay();
        $paper = Dao::getEntityByCond("Paper", " and patientid={$patient->id} and ename='adhd_iv' ORDER BY id ");
        if($paper instanceof Paper){
            return XDateTime::getDateDiff($paper->getCreateDay(), $baodaoDate);
        }
        return "无";
    }

    private function getAECntByPatientid ($patientid) {
        $sql = " select count(a.id) as cnt
            from patients a
            inner join papers b on b.patientid=a.id
            where a.id = {$patientid} and (b.papertplid=275143816 or b.papertplid=312586776) ";
        return Dao::queryValue($sql);
    }

    private function getPCCntByPatientid ($patientid) {
        $sql = " select count(a.id) as cnt
            from patients a
            inner join papers b on b.patientid=a.id
            where a.id = {$patientid} and (b.papertplid=275209326 or b.papertplid=312586776) ";
        return Dao::queryValue($sql);
    }

    private function createExcelADHD_KPI($exportJob){
        $unitofwork = BeanFinder::get("UnitOfWork");

        $exportJobid = $exportJob->id;
        $arr = json_decode($exportJob->data, true);
        $startdate = isset($arr["startdate"]) ? $arr["startdate"] : '0000-00-00';
        $enddate = isset($arr["enddate"]) ? $arr["enddate"] : '0000-00-00';

        $sql = "select
                    distinct a.id
                from patients a
                inner join patientmedicinerefs b on b.patientid = a.id
                where a.diseaseid = 1 and b.medicineid in (2,3,396,45,185,21,10,41,9,182,122)
                and a.status=1 and a.is_test=0
                and a.createtime >= :startdate and a.createtime < :enddate";

        $bind = [];
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = $enddate;
        $ids = Dao::queryValues($sql, $bind);

        $len = count($ids);
        $data = array();
        $temp = $this->genTeamKPIData($ids, $exportJob);
        $data[] = $temp;
        $temp = $this->genAuditorKPIData($ids, $exportJob);
        $data[] = $temp;

        $headarr = array(
            "类型",
            "8周服药率",
            "12周服药率",
            "16周服药率",
            "20周服药率",
            "24周服药率",
        );
        $this->writeFile($exportJob, $data, $headarr);
        $exportJob->progress = 100;
        $unitofwork->commitAndInit();
    }

    private function genTeamKPIData($ids, $exportJob){
        $unitofwork = BeanFinder::get("UnitOfWork");
        $exportJobid = $exportJob->id;
        $len = count($ids);
        $m = 0;
        $up8 = 0;
        $down8 = 0;

        $up12 = 0;
        $down12 = 0;

        $up16 = 0;
        $down16 = 0;

        $up20 = 0;
        $down20 = 0;

        $up24 = 0;
        $down24 = 0;

        foreach ($ids as $id) {
            if ($m > 0 && $m % 100 == 0) {
                $exportJob->progress = round(($m / $len / 2) * 100, 1);

                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $exportJob = Dao::getEntityById('Export_Job', $exportJobid);
            }
            $patient = Patient::getById($id);
            if( $patient instanceof Patient ){

                $baodao_cnt = $patient->getDayCntFromBaodao();
                $baodaodate = $patient->createtime;

                //在报到日期+7天内（左闭右闭）是否填写主要药物
                $startdate = $baodaodate;
                $enddate = date("Y-m-d", strtotime($startdate) + 8*86400);
                $isFillMasterMedicines = $this->isFillMasterMedicines($patient, $startdate, $enddate);
                if(false == $isFillMasterMedicines){
                    continue;
                }

                $baseday_arr = $this->getBaseDayArr();
                foreach($baseday_arr as $baseday){
                    if($baodao_cnt < $baseday){
                        continue;
                    }

                    $startdate = $baodaodate;
                    $enddate = date("Y-m-d", strtotime($startdate) + ($baseday-28)*86400);

                    $isStopMasterMedicinesByDoctor = $this->isStopMasterMedicinesByDoctor($patient, $startdate, $enddate);
                    if($isStopMasterMedicinesByDoctor){
                        continue;
                    }

                    $week = $baseday/7;
                    $down = "down{$week}";
                    $up = "up{$week}";

                    //分母++
                    $$down++;

                    //分子++
                    $startdate = date("Y-m-d", strtotime($baodaodate) + ($baseday-28)*86400);
                    $enddate = date("Y-m-d", strtotime($baodaodate) + $baseday*86400);
                    $isFillMasterMedicines = $this->isFillMasterMedicines($patient, $startdate, $enddate);
                    if($isFillMasterMedicines){
                        $$up++;
                    }

                }
            }
        }
        $temp = array();
        $temp[] = "团队KPI";
        $baseWeekArr = $this->getBaseWeekArr();
        foreach($baseWeekArr as $baseWeek){
            $down = "down{$baseWeek}";
            $up = "up{$baseWeek}";
            if($$down == 0){
                $temp[] = '0%';
            }else{
                $temp[] = sprintf("%.2f", ($$up/$$down)*100) . '%';
            }
        }
        $unitofwork->commitAndInit();

        return $temp;
    }

    private function genAuditorKPIData($ids, $exportJob){
        $unitofwork = BeanFinder::get("UnitOfWork");
        $exportJobid = $exportJob->id;
        $len = count($ids);
        $m = 0;
        $up8 = 0;
        $down8 = 0;

        $up12 = 0;
        $down12 = 0;

        $up16 = 0;
        $down16 = 0;

        $up20 = 0;
        $down20 = 0;

        $up24 = 0;
        $down24 = 0;

        foreach ($ids as $id) {
            if ($m > 0 && $m % 100 == 0) {
                $exportJob->progress = round(50 + ($m / $len/2) * 100, 1);

                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $exportJob = Dao::getEntityById('Export_Job', $exportJobid);
            }
            $patient = Patient::getById($id);
            if( $patient instanceof Patient ){

                $baodao_cnt = $patient->getDayCntFromBaodao();
                $baodaodate = $patient->createtime;


                $baseday_arr = $this->getBaseDayArr();
                foreach($baseday_arr as $baseday){
                    if($baodao_cnt < $baseday){
                        continue;
                    }

                    $startdate = date("Y-m-d", strtotime($baodaodate) + ($baseday-56)*86400);
                    $enddate = date("Y-m-d", strtotime($baodaodate) + ($baseday-28)*86400);
                    $isFillMasterMedicines = $this->isFillMasterMedicines($patient, $startdate, $enddate);
                    if(false == $isFillMasterMedicines){
                        continue;
                    }

                    $week = $baseday/7;
                    $down = "down{$week}";
                    $up = "up{$week}";

                    //分母++
                    $$down++;


                    //分子++
                    $startdate = date("Y-m-d", strtotime($baodaodate) + ($baseday-28)*86400);
                    $enddate = date("Y-m-d", strtotime($baodaodate) + $baseday*86400);
                    $isFillMasterMedicines = $this->isFillMasterMedicines($patient, $startdate, $enddate);

                    //遵医嘱停药
                    $startdate = date("Y-m-d", strtotime($baodaodate) + ($baseday-28)*86400);
                    $enddate = date("Y-m-d", strtotime($baodaodate) + $baseday*86400);
                    $isStopMasterMedicinesByDoctor = $this->isStopMasterMedicinesByDoctor($patient, $startdate, $enddate);

                    if($isFillMasterMedicines || $isStopMasterMedicinesByDoctor){
                        $$up++;
                    }

                }
            }
        }
        $temp = array();
        $temp[] = "运营KPI";
        $baseWeekArr = $this->getBaseWeekArr();
        foreach($baseWeekArr as $baseWeek){
            $down = "down{$baseWeek}";
            $up = "up{$baseWeek}";
            if($$down == 0){
                $temp[] = '0%';
            }else{
                $temp[] = sprintf("%.2f", ($$up/$$down)*100) . '%';
            }
        }
        $unitofwork->commitAndInit();

        return $temp;
    }

    private function isFillMasterMedicines($patient, $startdate, $enddate){
        $medicineid_arr = Medicine::$masterMedicines;
        $medicineidstr = implode(',', $medicineid_arr);
        $sql = "select
                    count(*) as cnt
                from drugitems where medicineid in ($medicineidstr) and type = 1
                and patientid = :patientid and createtime >= :startdate and createtime < :enddate";

        $bind = [];
        $bind[":patientid"] = $patient->id;
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = $enddate;
        return Dao::queryValue($sql, $bind) > 0;
    }

    private function isStopMasterMedicinesByDoctor($patient, $startdate, $enddate){
        $medicineid_arr = Medicine::$masterMedicines;
        $medicineidstr = implode(',', $medicineid_arr);
        $patientid = $patient->id;
        $sql = "select
                    stopdate
                from patientmedicinerefs where medicineid in ($medicineidstr) and status = 0 and stop_drug_type = 1
                and patientid = :patientid";

        $bind = [];
        $bind[":patientid"] = $patientid;
        $arr_stop = Dao::queryValues($sql, $bind);

        //没有遵医嘱停药的情况直接返回
        $cnt_stop = count($arr_stop);
        if($cnt_stop == 0){
            return false;
        }

        $sql = "select
                    count(*) as cnt
                from patientmedicinerefs where medicineid in (2,3,396,45,185,21,10,41,9,182,122)
                and patientid = :patientid";

        $bind = [];
        $bind[":patientid"] = $patientid;
        $cnt_all = Dao::queryValue($sql, $bind);

        //没有全部遵医嘱停药返回
        if($cnt_stop < $cnt_all){
            return false;
        }

        //获取一个日期数组里的最大值
        $max_date = max($arr_stop);
        if($max_date == "0000-00-00"){
            return false;
        }

        if($max_date > $startdate && $max_date < $enddate){
            return true;
        }else{
            return false;
        }
    }

    private function getBaseDayArr(){
        $arr = [8*7, 12*7, 16*7, 20*7, 24*7];
        return $arr;
    }

    private function getBaseWeekArr(){
        $arr = [8, 12, 16, 20, 24];
        return $arr;
    }


    private function writeFile($exportJob, $data, $headarr, $needMergeRowIndexArr = null, $needMergeColIndexArr = null){
        $username = $exportJob->auditor->user->username;
        if(empty($username)){
            Debug::trace(__METHOD__ . "======exportjob[{$exportJob->id}]==========username is empty==============");
            return;
        }
        $distDir = "/home/xdata/download/audit/{$username}";
        if (! is_dir($distDir)) {
            mkdir($distDir, 0755, true);
        }

        $fileName = md5($exportJob->id);
        $fileurl = $distDir . '/' . $fileName . '.xlsx';
        if( empty($needMergeRowIndexArr) ){
            ExcelUtil::createForCron($data, $headarr, $fileurl);
        }else{
            ExcelUtil::createHasMergeCellsForCron($data, $headarr, $fileurl, $needMergeRowIndexArr, $needMergeColIndexArr);
        }

        $exportJob->progress = 100;
        $exportJob->status = Export_Job::STATUS_COMPLETE;
    }

}

if ($argc < 2) {
    echo "usage: php " . basename(__FILE__) . " jobid\n";
    exit(1);
}

$id = $argv[1];
if (!$id) {
    echo "jobid ($id) is empty\n";
    exit(2);
}

$obj = new Export_data();
$obj->run($id);

Debug::flushXworklog();
