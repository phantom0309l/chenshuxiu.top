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
class ShopPkg_pull_erp extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'minute';
        $row["title"] = '每30分钟,从erp拉取数据,做快递单号提醒等操作';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.id from shoppkgs a
                inner join shoporders b on b.id=a.shoporderid
                where a.need_push_erp = 1 and a.is_push_erp = 1
                      and a.is_goodsout = 0 and a.is_sendout = 0
                      and a.status = 1
                      and a.express_no = '' and b.is_pay = 1 and b.refund_amount = 0";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            $shopPkg = ShopPkg::getById($id);
            if ($shopPkg instanceof ShopPkg) {
                $result = GuanYiService::tradeDeliverysGetOfDoneByShopPkg($shopPkg);
                $success = $result["success"];
                if($success){

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
        }

        $this->cronlog_brief = count($ids);
        $this->cronlog_content = trim($this->cronlog_content);

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

// //////////////////////////////////////////////////////

$process = new ShopPkg_pull_erp(__FILE__);
$process->dowork();
