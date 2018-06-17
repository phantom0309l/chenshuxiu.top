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

class Output_5627
{

    public function dowork () {

        $sql = "select
                    distinct a.id
                from patients a
                inner join patientmedicinerefs b on b.patientid = a.id
                where a.diseaseid = 1 and b.medicineid in (2,3,396,45,185,21,10,41,9,182,122)
                and a.status=1 and a.is_test=0
                and a.createtime >= :startdate and a.createtime < :enddate";

        $startdate = date("Y-m-d", time() - 24*7*86400 - 3*86400);
        $enddate = date("Y-m-d", time() - 8*7*86400 - 0*86400);
        $bind = [];
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = $enddate;
        $ids = Dao::queryValues($sql, $bind);
        $data = array();

        $temp = $this->genTeamKPIData($ids);
        $data[] = $temp;
        $temp = $this->genAuditorKPIData($ids);
        $data[] = $temp;

        $headarr = array(
            "类型",
            "8周服药率",
            "12周服药率",
            "16周服药率",
            "20周服药率",
            "24周服药率",
        );
        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/output_5627_kpi.xlsx");
    }

    private function genTeamKPIData($ids){
        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;
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
            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
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

    private function genAuditorKPIData($ids){
        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;
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
            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
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
        $sql = "select
                    count(*) as cnt
                from drugitems where medicineid in (2,3,396,45,185,21,10,41,9,182,122) and type = 1
                and patientid = :patientid and createtime >= :startdate and createtime < :enddate";

        $bind = [];
        $bind[":patientid"] = $patient->id;
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = $enddate;
        return Dao::queryValue($sql, $bind) > 0;
    }

    private function isStopMasterMedicinesByDoctor($patient, $startdate, $enddate){
        $patientid = $patient->id;
        $sql = "select
                    stopdate
                from patientmedicinerefs where medicineid in (2,3,396,45,185,21,10,41,9,182,122) and status = 0 and stop_drug_type = 1
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
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_5627.php]=====");

$process = new Output_5627();
$process->dowork();

Debug::trace("=====[cron][end][Output_5627.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
