<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 18-05-30
 * Time: 上午11:44
 */
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

$shopPkgId = $argv[1];

class Test_erp_pull
{

    public function dowork($shopPkgId) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $shopPkg = ShopPkg::getById($shopPkgId);

        if ($shopPkg instanceof ShopPkg) {
            $result = GuanYiService::tradeDeliverysGetOfDoneByShopPkg($shopPkg);
            Debug::trace($result);
            $success = $result["success"];
            if($success){
                echo "拉取成功！\n";
                $deliverys = $result["deliverys"];
                $cnt = count($deliverys);
                foreach($deliverys as $a){
                    $express_no = $a["express_no"];
                    $shopPkg->express_no = $express_no;
                }

                if($cnt > 0 && $shopPkg->express_no){
                    //尝试出库和发货
                    $this->tryGoodsOutSendOut($shopPkg);
                    //发送快递单号
                    ExpressService::sendExpress_no($shopPkg);
                }
                $this->cronlog_content .= "{$shopPkg->id}\n";
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
Debug::trace("=====[cron][beg][Test_erp_pull.php]=====");

$process = new Test_erp_pull();
$process->dowork($shopPkgId);

Debug::trace("=====[cron][end][Test_erp_pull.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
