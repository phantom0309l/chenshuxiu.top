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

class Init_pipelevel
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;

        $sql = "SELECT a.id
        FROM pipes a
        inner join patients b on b.id=a.patientid
        WHERE a.objtype = 'WxTxtMsg'
        and b.diseaseid=1 and b.is_test=0
        order by a.id desc limit 5000";

        //取出drugsheets中所有remark like '%不服药%'
        $ids = Dao::queryValues($sql);

        $auditorids = [10006, 10031, 10045, 10056, 10062, 10077];
        // 陈萍 寇彬彬 李洁 王小曼 刘武碧晴 王玉妹

        foreach ($ids as $pipeid) {
            // 提交工作单元
            if($i == 1000){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $i = 0;
            }

            $i++;

            echo "========={$pipeid}============{$i}=\n";
            $random_keys = array_rand($auditorids, 2);

            foreach ($random_keys as $k => $auditorids_key) {
                $arr = [];
                $arr['pipeid'] = $pipeid;
                $arr['auditorid'] = $auditorids[$auditorids_key];

                PipeLevel::createByBiz($arr);
            }

            // 创建一条用于机器学习的数据
            $arr = [];
            $arr['pipeid'] = $pipeid;
            $arr['auditorid'] = 9000;

            PipeLevel::createByBiz($arr);

        }

        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_pipelevel.php]=====");

$process = new Init_pipelevel();
$process->dowork();

Debug::trace("=====[cron][end][Init_pipelevel.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
