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

// 将一批王迁的患者迁移到保险测试组
class dbfix_patients_mv_wxgroup_5123
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.*
from wxusers a
inner join patients b on b.id=a.patientid
where a.doctorid=824 and b.createtime < '2017-10-01'; ";

        $wxusers = Dao::loadEntityList('WxUser', $sql);
        $cnt = count($wxusers);

        foreach ($wxusers as $w) {
            echo "\n wxuser[{$w->id}][{$w->wxshopid}][{$w->patient->name}] => xuanwuxiongwai";
            $w->joinWxGroup('xuanwuxiongwai');
        }

        $unitofwork->commitAndInit();
    }
}

echo " \n===begin===\n ";
$process = new dbfix_patients_mv_wxgroup_5123();
$process->dowork();
echo " \n\n===end===\n ";
