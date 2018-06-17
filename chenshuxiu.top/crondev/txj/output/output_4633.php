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

class Output_4633
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select
                    a.id
                from patients a
                inner join patientmedicinerefs b on b.patientid = a.id
                where a.doctorid=1 and b.medicineid=2";

        $ids = Dao::queryValues($sql);
        $i = 0;
        $data = array();
        foreach ($ids as $id) {
            echo "[{$id}]\n";
            $patient = Patient::getById($id);
            if( $patient instanceof Patient ){
                $temp = array();
                $temp[] = $patient->id;
                $temp[] = $patient->createtime;

                $patientmedicineref = PatientMedicineRefDao::getByPatientidMedicineid($id, 2);
                $temp[] = $patientmedicineref->first_start_date;

                $paperArr = $this->getPaperArr($patient);

                foreach($paperArr as $a){
                    $temp[] = $a;
                }

                $data[] = $temp;
            }
        }
        $headarr = array(
            "patientid",
            "报到时间",
            "首次服药时间",
            "第1次SNAP-IV评估时间",
            "第1次SNAP-IV评估总分",
            "第2次SNAP-IV评估时间",
            "第2次SNAP-IV评估总分",
            "第3次SNAP-IV评估时间",
            "第3次SNAP-IV评估总分",
            "第4次SNAP-IV评估时间",
            "第4次SNAP-IV评估总分",
            "第5次SNAP-IV评估时间",
            "第5次SNAP-IV评估总分",
            "第6次SNAP-IV评估时间",
            "第6次SNAP-IV评估总分",
            "第7次SNAP-IV评估时间",
            "第7次SNAP-IV评估总分",
            "第8次SNAP-IV评估时间",
            "第8次SNAP-IV评估总分",
            "第9次SNAP-IV评估时间",
            "第9次SNAP-IV评估总分",
            "第10次SNAP-IV评估时间",
            "第10次SNAP-IV评估总分",
            "第11次SNAP-IV评估时间",
            "第11次SNAP-IV评估总分",
            "第12次SNAP-IV评估时间",
            "第12次SNAP-IV评估总分",
            "第13次SNAP-IV评估时间",
            "第13次SNAP-IV评估总分",
            "第14次SNAP-IV评估时间",
            "第14次SNAP-IV评估总分",
            "第15次SNAP-IV评估时间",
            "第15次SNAP-IV评估总分",
            "第16次SNAP-IV评估时间",
            "第16次SNAP-IV评估总分",
            "第17次SNAP-IV评估时间",
            "第17次SNAP-IV评估总分",
            "第18次SNAP-IV评估时间",
            "第18次SNAP-IV评估总分",
            "第19次SNAP-IV评估时间",
            "第19次SNAP-IV评估总分",
            "第20次SNAP-IV评估时间",
            "第20次SNAP-IV评估总分",
            "第21次SNAP-IV评估时间",
            "第21次SNAP-IV评估总分",
            "第22次SNAP-IV评估时间",
            "第22次SNAP-IV评估总分",
            "第23次SNAP-IV评估时间",
            "第23次SNAP-IV评估总分",
            "第24次SNAP-IV评估时间",
            "第24次SNAP-IV评估总分",
            "第25次SNAP-IV评估时间",
            "第25次SNAP-IV评估总分",
            "第26次SNAP-IV评估时间",
            "第26次SNAP-IV评估总分",
            "第27次SNAP-IV评估时间",
            "第27次SNAP-IV评估总分",
            "第28次SNAP-IV评估时间",
            "第28次SNAP-IV评估总分",
            "第29次SNAP-IV评估时间",
            "第29次SNAP-IV评估总分",
            "第30次SNAP-IV评估时间",
            "第30次SNAP-IV评估总分",
        );
        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/output_4633.xlsx");
        $unitofwork->commitAndInit();
    }

    private function getPaperArr($patient){
        $arr = array();
        $papers = PaperDao::getList_adhd_iv($patient->id, 30);
        $papers = array_reverse($papers);
        foreach($papers as $n => $a){
            $arr[] = $a->createtime;
            $arr[] = $a->xanswersheet->score;
        }

        $cnt = count($arr);
        $left_cnt = 60 - $cnt;
        for($i=0; $i<$left_cnt; $i++){
            $arr[] = "";
        }

        return $arr;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_4633.php]=====");

$process = new Output_4633();
$process->dowork();

Debug::trace("=====[cron][end][Output_4633.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
