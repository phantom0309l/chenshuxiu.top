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

class Init_drugsheet
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;

        $sql = " SELECT id FROM drugsheets WHERE remark like '%不服药 |%' ";

        //取出drugsheets中所有remark like '%不服药%'
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            // 提交工作单元
            if($i == 3000){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $i = 0;
            }

            $i++;

            echo "========={$id}============{$i}=\n";
            $drugsheet = DrugSheet::getById($id);

            $arr = explode("|",$drugsheet->remark);
            $auditor_name = $arr[1];

            // 初始化运营id
            if($auditor_name){
                $auditor_name = str_replace(' ', '', $auditor_name);
                $auditor = Dao::getEntityByCond("Auditor", " and name like '%{$auditor_name}%' ");
                if($auditor instanceof Auditor){
                    $drugsheet->auditorid = $auditor->id;
                }else {
                    echo "\n未找到这个drugsheet的运营 drugsheetid：{$drugsheet->id}";
                }
            }

        }

        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_drugsheet.php]=====");

$process = new Init_drugsheet();
$process->dowork();

Debug::trace("=====[cron][end][Init_drugsheet.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
