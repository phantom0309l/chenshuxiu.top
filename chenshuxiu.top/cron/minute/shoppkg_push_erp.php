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
class ShopPkg_push_erp extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'minute';
        $row["title"] = '每20分钟，向erp进行订单推送';
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

        // 一个订单对应一个配送单的，自动推送；拆分过的配送单，手动推送；
        $sql = "SELECT a.id FROM shoppkgs a
                    INNER JOIN shoporders b ON b.id=a.shoporderid
                    WHERE a.need_push_erp = 1 AND a.is_push_erp = 0
                    AND a.is_goodsout = 0 AND a.is_sendout = 0
                    AND a.status = 1 AND b.refund_amount = 0
                    AND b.time_pay > '2018-04-08 00:00:00'
                    AND a.id IN (
                        SELECT id
                        FROM shoppkgs
                        GROUP BY shoporderid
                        HAVING count(id) = 1
                    )";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            $shopPkg = ShopPkg::getById($id);
            $isBalance = ShopOrderService::isBalance($shopPkg->shoporder);
            if(false == $isBalance){
                Debug::warn("自动推送配送单到erp时，订单【shoporderid={$shopPkg->shoporderid}】的商品没有完全分配到配送单！！！");
                continue;
            }

            if ($shopPkg instanceof ShopPkg && $shopPkg->canPushErp()) {
                $result = GuanYiService::tradeAddByShopPkg($shopPkg);
                $success = $result["success"];
                if($success){
                    $shopPkg->is_push_erp = 1;
                    $shopPkg->time_push_erp = date("Y-m-d H:i:s");
                    $shopPkg->remark_push_erp = "";
                }else{
                    $errorDesc = $result["errorDesc"];
                    $shopPkg->remark_push_erp = $errorDesc;
                    Debug::warn("shopPkg[{$shopPkg->id}]订单推送ERP失败[{$errorDesc}]");
                }
                $this->cronlog_content .= "{$shopPkg->id}\n";
            }
        }

        $this->cronlog_brief = count($ids);
        $this->cronlog_content = trim($this->cronlog_content);

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new ShopPkg_push_erp(__FILE__);
$process->dowork();
