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

class Output_5836
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from patients where createtime > '2018-01-13' and createtime < '2018-01-20' and status = 1 and diseaseid = 1 order by createtime asc";

        $ids = Dao::queryValues($sql);
        $i = 0;
        $data = array();
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 40) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $patient = Patient::getById($id);
            if( $patient instanceof Patient ){
                $temp = array();

                //患者id
                $temp[] = $patient->id;

                //报到时间
                $baodaotime = $patient->createtime;
                $temp[] = substr($baodaotime, 0, 10);

                //报到天数
                $baodao_cnt = $patient->getDayCntFromBaodao();
                $temp[] = $baodao_cnt;

                //是否礼来项目
                $temp[] = $patient->isInHezuo("Lilly") ? '是' : '否';

                //0-27天是否在服主要药物
                $startdate1 = date("Y-m-d", strtotime($baodaotime));
                $enddate1 = date("Y-m-d", strtotime($baodaotime) + 28*86400);
                $temp[] = $this->isFillMasterMedicines($patient, $startdate1, $enddate1) == true ? '是' : '否';

                //28-55天是否在服主要药物
                $startdate2 = date("Y-m-d", strtotime($baodaotime) + 28*86400);
                $enddate2 = date("Y-m-d", strtotime($baodaotime) + 56*86400);
                $temp[] = $this->isFillMasterMedicines($patient, $startdate2, $enddate2) == true ? '是' : '否';

                //28-55天是否遵医嘱停药
                $temp[] = $this->isStopMasterMedicinesByDoctor($patient, $startdate2, $enddate2) == true ? '是' : '否';

                $data[] = $temp;
            }
        }
        $headarr = array(
            "患者ID",
            "报到时间",
            "报到天数",
            "是否礼来项目",
            "0-27天是否在服主要药物",
            "28-55天是否在服主要药物",
            "28-55天是否遵医嘱停药",
        );
        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/output_5836.xlsx");
        $unitofwork->commitAndInit();
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
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_5836.php]=====");

$process = new Output_5836();
$process->dowork();

Debug::trace("=====[cron][end][Output_5836.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
