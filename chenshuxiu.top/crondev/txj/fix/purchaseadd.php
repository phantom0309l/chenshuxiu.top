<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class PurchaseAdd
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $data = $this->getData();
        GuanYiService::purchaseAddByData($data);
        /*foreach ($data as $str) {
            if(empty($str)){
                continue;
            }
            $arr = explode(",", $str);
            $sku_code = $arr[0];
            $qty = $arr[1];
            $aa = GuanYiService::purchaseAdd($sku_code, $qty);
            if($aa == false){
                echo "\n[fail--1][{$sku_code}]\n";
            }else{
                if($aa["success"] == true){
                    echo "\n[ok][{$sku_code}]\n";
                }else{
                    echo "\n[fail--2][{$sku_code}]\n";
                }
            }
        }*/
        $unitofwork->commitAndInit();
    }
    private function getData(){
        $str = file_get_contents("purchase_order1.csv");
        $d = explode("\n", $str);
        return $d;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][PurchaseAdd.php]=====");

$process = new PurchaseAdd();
$process->dowork();

Debug::trace("=====[cron][end][PurchaseAdd.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
