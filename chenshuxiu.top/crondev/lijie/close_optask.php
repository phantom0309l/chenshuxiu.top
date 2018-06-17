<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-8-14
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

class Close_optask
{
    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from optasks
            where optasktplid in
            (
                select id from optasktpls
                where code in ('Paper', 'baseDrug', 'baseScale', 'baseScale_QCD', 'makesure_ADHD', 'sunflower_tel_12', 'sunflower_tel_30')
            ) and status=0 and diseaseid=1";

        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $k => $id) {
            $i ++;
            if ($i >= 1000) {
                $i = 0;
                echo "\n\n-----commit----- " . XDateTime::now();
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            echo "\n[序号：{$k}]====id[{$id}]===" . XDateTime::now();

            $optask = OpTask::getById($id);

            $optask->close();
        }

        $unitofwork->commitAndInit();
    }

}

// //////////////////////////////////////////////////////

$process = new Close_optask(__FILE__);
$process->dowork();
