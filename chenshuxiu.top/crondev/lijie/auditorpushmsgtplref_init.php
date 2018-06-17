<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 17-9-20
 * Time: 上午11:44
 */
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

class Auditorpushmsgtplref_init
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        //处理一般情况--运营的is_ops=1
        $cond = " and ename not in ('Sunflower', 'ShopOrder') ";
        $auditorpushmsgtpls = Dao::getEntityListByCond('AuditorPushMsgTpl', $cond);

        $sql = " select a.id
        from auditors a
        inner join wxusers b on b.userid=a.userid
        where b.is_ops=1 and a.status=1 and b.wxshopid!=8
        group by a.id ";
        $auditorids = Dao::queryValues($sql);

        foreach ($auditorpushmsgtpls as $auditorpushmsgtpl) {
            foreach ($auditorids as $auditorid) {
                $this->createAuditorPushMsgTplRef($auditorid, $auditorpushmsgtpl->id);
            }
        }

        //处理 ShopOrder
        $cond = " and ename in ('ShopOrder') ";
        $auditorpushmsgtpls = Dao::getEntityListByCond('AuditorPushMsgTpl', $cond);

        $auditorids = [10002, 10003, 10004, 10006, 10007, 10021, 10073];

        foreach ($auditorpushmsgtpls as $auditorpushmsgtpl) {
            foreach ($auditorids as $auditorid) {
                $this->createAuditorPushMsgTplRef($auditorid, $auditorpushmsgtpl->id);
            }
        }

        //处理 Sunflower
        $cond = " and ename in ('Sunflower') ";
        $auditorpushmsgtpls = Dao::getEntityListByCond('AuditorPushMsgTpl', $cond);

        $auditorids = [10003, 10004, 10006, 10056, 10077];

        foreach ($auditorpushmsgtpls as $auditorpushmsgtpl) {
            foreach ($auditorids as $auditorid) {
                $this->createAuditorPushMsgTplRef($auditorid, $auditorpushmsgtpl->id);
            }
        }

        $unitofwork->commitAndInit();
    }

    private function createAuditorPushMsgTplRef ($auditorid, $auditorpushmsgtplid) {
        $row = array();
        $row["auditorid"] = $auditorid;
        $row["auditorpushmsgtplid"] = $auditorpushmsgtplid;
        $row["can_ops"] = 1;
        AuditorPushMsgTplRef::createByBiz($row);
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Auditorpushmsgtplref_init.php]=====");

$process = new Auditorpushmsgtplref_init();
$process->dowork();

Debug::trace("=====[cron][end][Auditorpushmsgtplref_init.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
