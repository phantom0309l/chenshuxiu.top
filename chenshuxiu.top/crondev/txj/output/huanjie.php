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
class Huanjie
{

    private static $paper = null;
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $now = date("Y-m-d H:i:s", time());
        $sql = "select id from patients where datediff(now(), createtime) >=30 and datediff(now(), createtime) <=180 and diseaseid=1 and doubt_type=0";
        $ids = Dao::queryValues($sql);
        $i = 0;
        $data = array();

        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            $doctor = $patient->doctor;
            if( false == $doctor instanceof Doctor ){
                continue;
            }
            if( $patient instanceof Patient ){
                $i ++;
                if ($i >= 100) {
                    $i = 0;
                    $unitofwork->commitAndInit();
                    $unitofwork = BeanFinder::get("UnitOfWork");
                }
                echo "\n====patientid[{$id}]===\n";
                $status = $this->getHuanjieStatus($patient);
                if($status==0){
                    continue;
                }

                $medicinestr = $this->getMedicineStr($patient);

                $patientpgrouprefs = PatientPgroupRefDao::getListByPatientid($id, " and pos < 8 order by id asc");
                $cnt = count($patientpgrouprefs);

                $temp = array();
                $temp[] = $id;
                $temp[] = ($medicinestr=="" ? "否" : "是");
                $temp[] = $medicinestr;
                $temp[] = $status;
                $temp[] = $cnt>0 ? "有" : "没有";
                $temp[] = $cnt;
                $temp[] = (self::$paper instanceof Paper ) ? self::$paper->createtime : "";

                $zuArr = $this->getZuArr($patientpgrouprefs);
                foreach ($zuArr as $a) {
                    $temp[] = $a;
                }
                $data[] = $temp;
            }
        }
        $unitofwork->commitAndInit();
        $headarr = array(
            "patientid",
            "该患者是否用过药",
            "用药类型",
            "缓解类型",
            "是否有过入组行为",
            "截止到当前有过几次入组行为",
            "第一次SNAP-iv评估平均值≤1的时间",
            "第1组入组时间",
            "第1组出组状态",
            "第2组入组时间",
            "第2组出组状态",
            "第3组入组时间",
            "第3组出组状态",
            "第4组入组时间",
            "第4组出组状态",
            "第5组入组时间",
            "第5组出组状态",
            "第6组入组时间",
            "第6组出组状态",
            "第7组入组时间",
            "第7组出组状态",
        );

        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/huanjie.xlsx");

    }

    //status 0 ：没有填写或者未缓解
    //1:评估平均分小于等于一分
    //2:总分最高分比最低分高20%（含）以上的患者
    private function getHuanjieStatus($patient){
        self::$paper = null;
        $status = 0;
        $doctor = $patient->doctor;
        $num = $this->getQuestionNum($doctor);

        $scores = array();
        $sql = " select a.*
            from papers a
            inner join xanswersheets b on b.id=a.xanswersheetid
            where a.ename='adhd_iv' and b.score > 0 ";
        $papers = Dao::loadEntityList("Paper", $sql);

        foreach($papers as $a){
            $r = $a->xanswersheet->score/$num;
            if($r<=1){
                self::$paper = $a;
                $status = 1;
                break;
            }else{
                $scores[] = $a->xanswersheet->score;
            }
        }

        if($status==0){
            $max = max($scores);
            $min = min($scores);
            $max_pos = 0;
            $min_pos = 0;
            foreach ($scores as $i => $value) {
                if($value == $max){
                    $max_pos = $i;
                }
                if($value == $min){
                    $min_pos = $i;
                }
            }

            if($max_pos<$min_pos){
                if( $min <= ($max*0.8) ){
                    $status = 2;
                }
            }
        }
        return $status;
    }

    private function getQuestionNum(Doctor $doctor){
        $flag = $doctor->useAdhd_ivOf26();
        return  $flag == true ? 26 : 18;
    }

    private function getMedicineStr($patient){
        $arr = array();
        $patientmedicinerefs = PatientMedicineRefDao::getAllListByPatient($patient);
        foreach($patientmedicinerefs as $a){
            $arr[] = $a->medicine->name;
        }
        return implode(",", $arr);
    }

    private function getZuArr($patientpgrouprefs){
        $arr = array();

        foreach ($patientpgrouprefs as $j => $a) {
            $arr[] = $a->startdate;
            $arr[] = $a->status;
        }
        $cnt = 14 - count($arr);
        for($i=0; $i<cnt; $i++){
            $arr[] = "";
        }
        return $arr;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Huanjie.php]=====");

$process = new Huanjie();
$process->dowork();

Debug::trace("=====[cron][end][Huanjie.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
