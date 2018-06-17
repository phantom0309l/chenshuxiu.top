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

// 临时修正 wxuser->doctorid => 1294
class Dbfix_wxuser_doctorid
{

    public function dowork () {
        $sql = "select a.id
            from wxusers a
            inner join users b on b.id = a.userid
            inner join patients c on c.id = b.patientid
            where c.doctorid=1294 and a.wxshopid = 22;";

        $wxuserids = Dao::queryValues($sql);

        $unitofwork = BeanFinder::get("UnitOfWork");

        foreach ($wxuserids as $wxuserid) {
            $wxuser = WxUser::getById($wxuserid);

            $old_doctorid = $wxuser->doctorid;
            $wxuser->set4lock('doctorid', 1294);

            echo "\n wxuser[{$wxuserid}]->doctorid : {$old_doctorid} => 1294";
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n===begin===\n";
$process = new Dbfix_wxuser_doctorid();
$process->dowork();
echo "\n===end===\n";
