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
class Dbfix_mv_patient_5365_20171219
{

    public function dowork ($patientid, $from_doctorids, $to_doctorid, $fix_pushmsg = 1) {
        echo "\n==== mv patientid[{$patientid}] to doctorid[{$to_doctorid}] ====\n";

        $this->mv_wxuser($patientid, $to_doctorid);

        $this->mv_patient('Patient', 'first_doctorid', 'id', $patientid, $to_doctorid);
        $this->mv_patient('Patient', 'doctorid', 'id', $patientid, $to_doctorid);

        $pcard = PcardDao::getByPatientidDoctorid($patientid, $to_doctorid);
        if (false == $pcard instanceof Pcard) {
            foreach ($from_doctorids as $from_doctorid) {
                $pcard = PcardDao::getByPatientidDoctorid($patientid, $from_doctorid);

                if ($pcard instanceof Pcard) {
                    $pcard->set4lock('doctorid', $to_doctorid);
                    echo "\n\t{$from_doctorid} => {$to_doctorid} ";
                    break;
                }
            }
        }

        $this->fix_patient_tables($patientid, $to_doctorid, $fix_pushmsg);
    }

    public function mv_wxuser ($patientid, $to_doctorid) {
        $sql = "select a.id from wxusers a
            inner join users b on b.id=a.userid
            where b.patientid={$patientid}";

        $ids = Dao::queryValues($sql);

        $cnt = count($ids);

        echo "\n\n==== WxUser->doctorid  : cnt = {$cnt} ==== \n";

        $unitofwork = BeanFinder::get("UnitOfWork");

        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            $unitofwork = BeanFinder::get("UnitOfWork");
            $entity = Dao::getEntityById('WxUser', $id);
            $entity->set4lock('doctorid', $to_doctorid);
            echo "\n {$i} / {$cnt} : WxUser : {$id} ";
        }

        $unitofwork->commitAndInit();
    }

    public function mv_patient ($objType, $field = 'doctorid', $key, $patientid, $to_doctorid) {
        $table = strtolower($objType);
        $table .= "s";

        $sql = "select id from {$table} where {$key}={$patientid} ";

        $ids = Dao::queryValues($sql);

        $cnt = count($ids);

        echo "\n\n==== {$objType}->{$field} : cnt = {$cnt} ==== \n";

        $unitofwork = BeanFinder::get("UnitOfWork");

        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            $unitofwork = BeanFinder::get("UnitOfWork");
            $entity = Dao::getEntityById($objType, $id);
            $entity->set4lock($field, $to_doctorid);
            echo "\n {$i} / {$cnt} : {$objType} : {$id} ";
        }

        $unitofwork->commitAndInit();
    }

    public function fix_patient_tables ($patientid, $to_doctorid, $fix_pushmsg = 1) {
        echo "\n==== doctorid=[{$to_doctorid}], fix_pushmsg=[{$fix_pushmsg}] ====\n";

        $jumpTables_str = "'pcardhistorys', 'pcards'";

        // 是否跳过 pushmsgs
        if (false == $fix_pushmsg) {
            $jumpTables_str .= ",'pushmsgs'";
        }

        $sql = "select a.TABLE_SCHEMA as db_name, a.TABLE_NAME as table_name
        from information_schema.COLUMNS a
        inner join information_schema.COLUMNS b on b.TABLE_NAME = a.TABLE_NAME
        where a.COLUMN_NAME='doctorid' and b.COLUMN_NAME = 'patientid' and a. TABLE_NAME not in ({$jumpTables_str})";

        $rows = Dao::queryRows($sql);

        foreach ($rows as $row) {
            $unitofwork = BeanFinder::get("UnitOfWork");

            $db_name = $row['db_name'];
            $table_name = $row['table_name'];

            $sql = "select a.*
            from {$db_name}.{$table_name} a
            where a.patientid = {$patientid} and a.doctorid <> {$to_doctorid} ";

            $entityType = XObjLog::table2entityType($table_name);

            if (empty($entityType)) {
                echo "\n==== {$db_name}.{$table_name} == jump ==== \n";
                continue;
            }

            $entitys = Dao::loadEntityList($entityType, $sql, [], $db_name);

            $cnt = count($entitys);

            echo "\n==== {$db_name}.{$table_name} == {$cnt} ==== \n";

            if ('revisittkts' == $table_name) {
                echo "continue";
                continue;
            }

            foreach ($entitys as $a) {

                echo "\n{$entityType} [{$a->id}] : ";

                // PushMsg 跳过不修
                if ($a instanceof Pipe) {
                    echo " {$a->objtype} [{$a->objid}] : ";

                    if ($a->objtype == 'RevisitTkt') {
                        echo " jump ";
                        continue;
                    }

                    if (false == $fix_pushmsg && $a->objtype == 'PushMsg') {
                        echo " jump ";
                        continue;
                    }
                }

                $old_doctorid = $a->doctorid;
                $a->set4lock('doctorid', $to_doctorid);

                echo " {$old_doctorid} => {$to_doctorid}";
            }

            $unitofwork->commitAndInit();
        }
    }
}

echo "\n";
// $patientids = $argv[1];
// $from_doctorids = $argv[2];
// $to_doctorid = $argv[3];
// $fix_pushmsg = isset($argv[4]) ? $argv[4] : 1;
// if (empty($patientids) || empty($to_doctorid)) {
//     echo "\nplease input patientids from_doctorids to_doctorid fix_pushmsg , 格式: 12,13,14 21,22 1605 0|1 \n\n";
//     exit();
// }

// $patientids = explode(',', $patientids);
// $from_doctorids = explode(',', $from_doctorids);

$sql = "select distinct a.id
from patients a
inner join pcards b on b.patientid = a.id
where b.diseaseid=8 and b.doctorid in (364, 1221) and a.name in ('毕洪春',
'贾煜',
'薄成武',
'冯小冲',
'刘勇',
'王来福',
'刘盛银',
'袁亚辉',
'黄瑞起',
'李召志',
'李付恋',
'陈茂生',
'郑渝',
'李福河',
'李德福',
'王军',
'田振民',
'赵常荣',
'龙勤',
'刘昌华',
'姚立彬',
'冯海胜',
'王春仿',
'刘勇',
'陈秋山',
'薄成武',
'景玉霞',
'杜荣杰',
'贺春林',
'吴建辛',
'马海',
'杨金福',
'裴固富',
'程卫东',
'王会欣',
'陈伟民',
'卢丙校',
'张忠禄',
'杜兴杰',
'吴广',
'王时平',
'刘瑞云',
'赵常荣',
'戚有来',
'李祥',
'曹玉杰',
'姜树苹',
'张墨和',
'李达生')
order by a.name;";

$patientids = Dao::queryValues($sql);
$from_doctorids = [364, 1221, 1218];
$to_doctorid = 1605;
$fix_pushmsg = 1;

echo "\n===begin===\n";
foreach ($patientids as $patientid) {
    $process = new Dbfix_mv_patient_5365_20171219();
    $process->dowork($patientid, $from_doctorids, $to_doctorid, $fix_pushmsg);
}
echo "\n===end===\n";
