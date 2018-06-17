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

class Patientdrugstate_add extends CronBase
{

    private static $drugitems = array();
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 23:25 更新患者最新一个阶段的用药状态';
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

        $sql = " select id from patients where diseaseid=1 and status=1 ";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            echo "\n\nid[{$id}]";
            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $patient = Patient::getById($id);

            if($patient instanceof Patient){
                $d = $patient->getDayCntFromBaodao();
                $can_add = $this->canAdd($d);
                if($can_add){
                    $this->createPatientMedicineStage($patient, $d);
                }
            }
        }

        $unitofwork->commitAndInit();
    }

    private function createPatientMedicineStage($patient, $d){
        $dateArr = $this->getStart_dateEnd_date($patient, $d);

        $drugsheet = $this->getDrugSheet($patient, $dateArr[0], $dateArr[1]);
        $state = $this->getDrugState($drugsheet);
        $row = array();
        $row["patientid"] = $patient->id;
        $row["baodao_date"] = date("Y-m-d", strtotime($patient->createtime));
        $row["pos"] = $this->getNextPos($patient);
        $row["state"] = $state;
        $row["content"] = $this->getDrugNameStrs($state);
        $row["offset_daycnt"] = $d;
        $row["auditorid"] = 1;

        PatientDrugState::createByBiz($row);
    }

    private function canAdd($d){
        $n = $d - 1;
        return ($n%28 == 0);
    }

    private function getStart_dateEnd_date($patient, $d){
        $createtime = strtotime($patient->createtime);

        if($d == 1){
            $start_date = date("Y-m-d", $createtime);
            $end_date = date("Y-m-d", $createtime + 2*86400);
        }else{
            $createtime = $createtime + $d*86400;
            $start_date = date("Y-m-d", $createtime - 7*86400);
            $end_date = date("Y-m-d", $createtime);
        }

        return [$start_date, $end_date];
    }

    private function getNextPos($patient){
        $pos = 1;
        $last_patientdrugstate = PatientDrugStateDao::getLastByPatient($patient);
        if($last_patientdrugstate instanceof PatientDrugState){
            $pos = $last_patientdrugstate->pos + 1;
        }
        return $pos;
    }

    //根据drugsheet获取患者服药状态
    //unknown : 未知; ondrug : 服药; stopdrug : 停药; nodrug : 不服
    private function getDrugState($drugsheet){
        self::$drugitems = array();
        if(false == $drugsheet instanceof DrugSheet){
            return PatientDrugState::state_unknown;
        }

        $drugitems = $drugsheet->getDrugItems();
        if(count($drugitems) == 0){
            return PatientDrugState::state_nodrug;
        }

        self::$drugitems = $drugitems;

        foreach ($drugitems as $a) {
            if($a->value > 0){
                return PatientDrugState::state_ondrug;
            }
        }
        //停药的上一条一定是服药，否则置成不服
        $last_patientdrugstate = PatientDrugStateDao::getLastByPatient($drugsheet->patient);
        if($last_patientdrugstate instanceof PatientDrugState && PatientDrugState::state_ondrug == $last_patientdrugstate->state){
            return PatientDrugState::state_stopdrug;
        }else{
            return PatientDrugState::state_nodrug;
        }
    }

    private function getDrugNameStrs($state){
        $drugitems = self::$drugitems;
        if(count($drugitems) == 0){
            return "";
        }

        $temp = array();
        $temp1 = array();
        foreach($drugitems as $a){
            $medicine = $a->medicine;
            if(false == $medicine instanceof Medicine){
                continue;
            }
            $name = $medicine->name;
            if($a->value > 0){
                $temp[] = $name;
            }
            $temp1[] = $name;
        }
        if($state == PatientDrugState::state_ondrug){
            $str = "在服：";
            $str .= implode("|", $temp);
        }else{
            $str = "停药/不服：";
            $str .= implode("|", $temp1);
        }
        return $str;
    }

    private function getDrugSheet($patient, $start_date, $end_date){
        $condEx = " and thedate >= '{$start_date}' and thedate < '{$end_date}' order by thedate desc";
        return DrugSheetDao::getOneByPatientid($patient->id, $condEx);
    }

}

// //////////////////////////////////////////////////////

$process = new Patientdrugstate_add(__FILE__);
$process->dowork();
