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

class InitShopPkgAndShopPkgItem extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'rightnow';
        $row["title"] = '初始化shoppkgs与shoppkgitems表的数据！';
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

        $sql = "select a.id from shoporders a
            left join shoppkgs b on b.shoporderid = a.id
            where b.id is null and a.type !='weituo' and a.is_pay=1";

        $ids = Dao::queryValues($sql);

        foreach ($ids as $i => $id) {
            echo "===========[{$i}][shoporderid:{$id}]=============\n";
            $shopOrder = ShopOrder::getById($id);

            if (0 == $i % 1000) {
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $shopPkg = ShopPkgDao::getByShopOrder($shopOrder);
            if ($shopPkg instanceof ShopPkg) {
                echo "===========[{$i}][shoporderid:{$id}]===不能重复生成==========\n";
                continue;
            }

            $shopPkg = $this->createShopPkgByShopOrder($shopOrder);

            $this->createShopPkgItemsByShopOrderAndShopPkg($shopOrder, $shopPkg);
        }

        $unitofwork->commitAndInit();
    }

    private function createShopPkgByShopOrder($shopOrder) {
        $arr = [];
        $arr["wxuserid"] = $shopOrder->wxuserid;
        $arr["userid"] = $shopOrder->userid;
        $arr["patientid"] = $shopOrder->patientid;
        $arr["shoporderid"] = $shopOrder->id;
        $arr["fangcun_platform_no"] = $shopOrder->id;
        $arr["express_price"] = $shopOrder->express_price;
        $arr["express_price_real"] = $shopOrder->express_price_real;
        $arr["is_goodsout"] = $shopOrder->is_goodsout;
        $arr["is_sendout"] = $shopOrder->is_sendout;
        $arr["express_company"] = $shopOrder->express_company;
        $arr["express_no"] = $shopOrder->express_no;
        $arr["time_goodsout"] = $shopOrder->time_goodsout;
        $arr["time_sendout"] = $shopOrder->time_sendout;
        $arr["eorder_content"] = $shopOrder->eorder_content;
        $arr["need_push_erp"] = $shopOrder->need_push_erp;
        $arr["is_push_erp"] = $shopOrder->is_push_erp;
        $arr["time_push_erp"] = $shopOrder->time_push_erp;
        $arr["remark_push_erp"] = $shopOrder->remark_push_erp;
        $arr["status"] = $shopOrder->status;

        return ShopPkg::createByBiz($arr);
    }

    private function createShopPkgItemsByShopOrderAndShopPkg($shopOrder, $shopPkg) {
        $shopOrderItems = $shopOrder->getShopOrderItems();
        foreach ($shopOrderItems as $shopOrderItem) {
            $arr = [];
            $arr["shoppkgid"] = $shopPkg->id;
            $arr["shopproductid"] = $shopOrderItem->shopproductid;
            $arr["price"] = $shopOrderItem->price;
            $arr["cnt"] = $shopOrderItem->cnt;

            ShopPkgItem::createByBiz($arr);
        }
    }
}

$initShopPkgAndShopPkgItem = new InitShopPkgAndShopPkgItem(__FILE__);
$initShopPkgAndShopPkgItem->dowork();
