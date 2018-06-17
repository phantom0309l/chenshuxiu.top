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

class Init_medicineproduct_medicineid
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from medicineproducts";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $k => $id) {
            $medicineproduct = MedicineProduct::getById($id);
            $medicines = $this->getMedicines($medicineproduct);
            if(1 == count($medicines)){
                $medicineproduct->medicineid = $medicines[0]->id;
            }else if(1 < count($medicines)){
                echo "{$medicineproduct->id}   通用名:{$medicineproduct->name_common}   商品名:{$medicineproduct->name_brand}   找到多条\n";
            }else {
                echo "{$medicineproduct->id}   通用名:{$medicineproduct->name_common}   商品名:{$medicineproduct->name_brand}   未找到\n";
            }
        }
        $unitofwork->commitAndInit();
    }

    private function getMedicines($medicineproduct){
        $cond = ' and ( name=:name_brand or scientificname=:name_common) ';

        $bind = array(
            ":name_brand" => $medicineproduct->name_brand,
            ":name_common" => $medicineproduct->name_common,
        );

        return Dao::getEntityListByCond("Medicine", $cond, $bind);
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_medicineproduct_medicineid.php]=====");

$process = new Init_medicineproduct_medicineid();
$process->dowork();

Debug::trace("=====[cron][end][Init_medicineproduct_medicineid.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
