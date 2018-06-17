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

// #4372 wxusers 增加 字段 patientid
class Dbfix_init_wxusers_patientid
{

    public function dowork () {
        $sql = "select a.id as wxuserid, a.patientid as wxuser_patientid, b.patientid as user_patientid
                from wxusers a
                inner join users b on b.id=a.userid
                where a.patientid <> b.patientid;";

        $rows = Dao::queryRows($sql);

        $unitofwork = BeanFinder::get("UnitOfWork");

        $cnt = count($rows);

        foreach ($rows as $i => $row) {
            $wxuserid = $row['wxuserid'];
            $wxuser_patientid = $row['wxuser_patientid'];
            $user_patientid = $row['user_patientid'];

            $wxuser = WxUser::getById($wxuserid);

            $wxuser->set4lock('patientid', $user_patientid);

            if ($i % 100 == 0) {
                echo "\n {$i} / {$cnt} : wxuser[{$wxuserid}]->patientid : {$wxuser_patientid} => {$user_patientid}";

                $unitofwork->commitAndInit();

            } else {
                echo ".";
            }
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n===begin===\n";
$process = new Dbfix_init_wxusers_patientid();
$process->dowork();
echo "\n===end===\n";
