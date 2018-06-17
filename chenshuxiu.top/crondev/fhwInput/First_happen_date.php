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

class Complication
{
    public function getPatientByOut_case_no($out_case_no)
    {
        $cond = " and out_case_no = '{$out_case_no}' and doctorid = 33 ";

        return Dao::getEntityByCond("Patient", $cond);
    }

    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "SELECT t.patientid,t.name,t.check_date
            FROM
            (SELECT a.patientid,b.name,a.`check_date`
            FROM checkups a
            LEFT JOIN patients b ON b.id = a.patientid
            WHERE b.name NOT LIKE '%测试%' AND a.checkuptplid = 106312063 AND b.doctorid = 33
            ORDER BY a.`check_date` ASC)t
            GROUP BY t.patientid ";

        $arr = Dao::queryRows($sql);

        $i = 0;
        foreach ($arr as $a){
            $pcard = PcardDao::getByPatientidDoctorid($a['patientid'], 33);
            if($pcard->first_happen_date != $a['check_date']){
                $i++;
                $pcard->first_happen_date = $a['check_date'];
                echo "[----------------------{$pcard->patient_name} {$pcard->first_happen_date}----------------------]\n";
            }
        }

        echo "sum = {$i}";

        $unitofwork->commitAndInit();
    }
}

$complication = new Complication();
$complication->dowork();
