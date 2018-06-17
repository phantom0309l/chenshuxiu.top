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

class Output_3344_02
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from patients where createtime > '2017-01-01' and createtime < '2017-01-29' and status=1 and diseaseid=1 and doctorid not in (10,11)";
        $ids = Dao::queryValues($sql);
        $i = 0;
        $cnt = count($ids);
        $cnt1 = 0;
        $cnt2 = 0;
        $cnt3 = 0;

        foreach ($ids as $id) {
            echo "[{$id}]\n";
            $patient = Patient::getById($id);
            if( $patient instanceof Patient ){
                $is_fill1 = $this->isFillInFirstMonth($patient);
                $is_fill2 = $this->isFillInSecondMonth($patient);
                $is_fill3 = $this->isFillInThirdMonth($patient);
                if($is_fill1){
                    $cnt1++;
                }
                if($is_fill1&&$is_fill2){
                    $cnt2++;
                }
                if($is_fill1&&$is_fill2&&$is_fill3){
                    $cnt3++;
                }
            }
        }
        echo "\n\n";
        echo "======cnt[{$cnt}]========";
        echo "======cnt1[{$cnt1}]========";
        echo "======cnt2[{$cnt2}]========";
        echo "======cnt3[{$cnt3}]========";
        $unitofwork->commitAndInit();
    }

    private function isFillInFirstMonth($patient){
        $createtime = $patient->createtime;
        $start_time = strtotime($createtime);
        $end_time = $start_time + 86400*28;

        $start_date = date("Y-m-d", $start_time);
        $end_date = date("Y-m-d", $end_time);
        $is_fill = $this->isFillDrugAndScale($patient, $start_date, $end_date);
        return $is_fill;
    }

    private function isFillInSecondMonth($patient){
        $createtime = $patient->createtime;
        $start_time = strtotime($createtime) + 86400*28;
        $end_time = $start_time + 86400*56;

        $start_date = date("Y-m-d", $start_time);
        $end_date = date("Y-m-d", $end_time);
        $is_fill = $this->isFillDrugAndScale($patient, $start_date, $end_date);
        return $is_fill;
    }

    private function isFillInThirdMonth($patient){
        $createtime = $patient->createtime;
        $start_time = strtotime($createtime) + 86400*56;
        $end_time = $start_time + 86400*84;

        $start_date = date("Y-m-d", $start_time);
        $end_date = date("Y-m-d", $end_time);
        $is_fill = $this->isFillDrugAndScale($patient, $start_date, $end_date);
        return $is_fill;
    }

    private function isFillDrugAndScale($patient, $startdate, $enddate){
        $drugsheets = DrugSheetDao::getListByPatientid($patient->id, " and thedate >= '{$startdate}' and thedate < '{$enddate}'");
        $papers = PaperDao::getListByPatientid($patient->id, " and createtime >= '{$startdate}' and createtime < '{$enddate}' and ename = 'adhd_iv'");
        $drugsheet_cnt = count($drugsheets);
        $paper_cnt = count($papers);

        if ($drugsheet_cnt > 0 && $paper_cnt > 0) {
            return true;
        } else {
            return false;
        }
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_3344_02.php]=====");

$process = new Output_3344_02();
$process->dowork();

Debug::trace("=====[cron][end][Output_3344_02.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
