<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// 将 from_doctor 的患者,迁移到 to_doctor 名下
// WxUser, Patient, Pcard
// 其他的表
class Dbfix_mv_patients_doctor2doctor
{

    public function dowork () {
        $from_doctorid = 1295;
        $to_doctorid = 1296;

        echo "\n==== mv {$from_doctorid} to {$to_doctorid} ====\n";

        $this->mv_patients('WxUser', 'doctorid', $from_doctorid, $to_doctorid);
        $this->mv_patients('Patient', 'first_doctorid', $from_doctorid, $to_doctorid);
        $this->mv_patients('Patient', 'doctorid', $from_doctorid, $to_doctorid);
        $this->mv_patients('Pcard', 'doctorid', $from_doctorid, $to_doctorid);
    }

    public function mv_patients ($objType, $field = 'doctorid', $from_doctorid, $to_doctorid) {
        $table = strtolower($objType);
        $table .= "s";

        $sql = "select id from {$table} where {$field}={$from_doctorid} ";

        $ids = Dao::queryValues($sql);

        $cnt = count($ids);

        echo "\n\n==== {$objType} == {$cnt} ==== \n";

        $unitofwork = BeanFinder::get("UnitOfWork");

        foreach ($ids as $i => $id) {

            $unitofwork = BeanFinder::get("UnitOfWork");
            // 修 pcard.doctorid
            $entity = Dao::getEntityById($objType, $id);
            $entity->set4lock($field, $to_doctorid);
            echo "\n {$i} / {$cnt} : {$objType} : {$id} ";
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n===begin===\n";
$process = new Dbfix_mv_patients_doctor2doctor();
$process->dowork();
echo "\n===end===\n";
