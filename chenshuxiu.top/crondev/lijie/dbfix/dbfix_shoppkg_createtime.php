<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Dbfix_shoppkg_createtime extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'rightnow';
        $row["title"] = '修复shoppkg的createtime';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog() {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog() {
        return true;
    }

    public function doWorkImp() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "SELECT a.id
            FROM shoppkgs a
            INNER JOIN shoporders b ON b.id = a.shoporderid
            WHERE a.fangcun_platform_no = b.id";

        $ids = Dao::queryValues($sql);

        foreach ($ids as $i => $id) {
            echo "===========[{$i}][shoppkgid:{$id}]=============\n";
            $shopPkg = ShopPkg::getById($id);

            if (0 == $i % 100) {
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $shopPkg->createtime = $shopPkg->shoporder->createtime;
        }

        $unitofwork->commitAndInit();
    }

}

$bfix_shoppkg_createtime = new Dbfix_shoppkg_createtime(__FILE__);
$bfix_shoppkg_createtime->dowork();
