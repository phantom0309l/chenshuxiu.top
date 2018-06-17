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

class Erp_shoppkg_do_finish
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select id from shoppkgs
                    where need_push_erp = 1 and is_push_erp = 1
                    and is_goodsout = 0 and is_sendout = 0
                    and status = 1
                    and express_no = ''";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $shopPkg = ShopPkg::getById($id);
            if ($shopPkg instanceof ShopPkg) {
                $time_pay = $shopPkg->time_pay;
                $i++;
                $result = GuanYiService::tradeDeliverysGetOfDoneByShopPkg($shopPkg);
                $success = $result["success"];
                if($success){

                    //$descstr = json_encode($result, JSON_UNESCAPED_UNICODE);
                    //echo "\n[ok][{$id}][{$descstr}]\n";
                    $deliverys = $result["deliverys"];
                    $cnt = count($deliverys);
                    foreach($deliverys as $a){
                        $express_no = $a["express_no"];
                        $shopPkg->express_no = $express_no;
                        echo "\n[{$i}][ok1]shoppkgid[{$id}][{$time_pay}]\n";
                    }

                    if($cnt > 0 && $shopPkg->express_no){
                        //尝试出库和发货
                        $this->tryGoodsOutSendOut($shopPkg);
                        //发送快递单号
                        ExpressService::sendExpress_no($shopPkg);
                        echo "\n[{$i}][ok2]shoppkgid[{$id}][{$time_pay}]\n";
                    }

                }else{
                    $descstr = json_encode($result, JSON_UNESCAPED_UNICODE);
                    echo "\n[{$i}][fail]shoppkgid[{$id}][{$time_pay}]\n";
                    echo "\n[{$descstr}]\n";
                }
            }
        }
        $unitofwork->commitAndInit();
    }

    //尝试置自身系统的出库和发货
    //有可能失败，因为WMS仓储系统推送到ERP，我们再通过发货单查询接口查询，这一系列流程有时间差。
    //在这个时间内，自身库存可能会有变化，所以做尝试出库和发货，如果失败，手动进行处理。
    private function tryGoodsOutSendOut($shopPkg){
        if($shopPkg->is_goodsout){
            return;
        }

        if($shopPkg->is_sendout){
            return;
        }

        if(true == $shopPkg->checkStock()){
            //出库
            $shopPkg->goodsOut();
            //发货
            $shopPkg->is_sendout = 1;
            $shopPkg->time_sendout = XDateTime::now();
        }
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Erp_shoppkg_do_finish.php]=====");

$process = new Erp_shoppkg_do_finish();
$process->dowork();

Debug::trace("=====[cron][end][Erp_shoppkg_do_finish.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
