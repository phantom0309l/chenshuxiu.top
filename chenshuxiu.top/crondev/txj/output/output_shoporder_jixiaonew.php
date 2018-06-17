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

class Output_shoporder_jixiao_new
{

    public function dowork () {

        $auditor_arr = [10032, 10042];
        foreach($auditor_arr as $auditorid){
            $unitofwork = BeanFinder::get("UnitOfWork");
            $auditor = Auditor::getById($auditorid);

            echo "======市场[{$auditor->name}]开始导出======\n";

            $ids = $this->getShopOrderFirstArr($auditorid);

            $i = 0;
            $data = array();
            //编号
            $num = 0;

            foreach ($ids as $id) {
                $i ++;
                if ($i > 50) {
                    $i = 0;
                    $unitofwork->commitAndInit();
                    $unitofwork = BeanFinder::get("UnitOfWork");
                }
                echo "[{$id}]\n";
                $temp = array();
                $shopOrder = ShopOrder::getById($id);

                $temp[] = $shopOrder->id;
                $temp[] = $shopOrder->patient->id;
                $temp[] = $shopOrder->patient->name;
                $temp[] = substr($shopOrder->time_pay, 0, 10);
                $temp[] = $shopOrder->thedoctor->name;
                $temp[] = $auditor->name;
                $temp[] = $shopOrder->pos;
                $temp[] = $shopOrder->getTitleAndCntOfShopProducts();

                $left_amount = $shopOrder->getLeft_amount();
                $left_amount = $left_amount - 1000;
                $left_amount = sprintf("%.2f", $left_amount / 100);

                $temp[] = $left_amount;
                $data[] = $temp;
            }

            $headarr = array(
                "shoporderid",
                "patientid",
                "患者",
                "支付时间",
                "医生",
                "市场",
                "第几单",
                "商品详情",
                "总金额",
            );
            ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/shoporder/output_shoporder_jixiao_new_{$auditor->name}.xlsx");
            $unitofwork->commitAndInit();
        }
    }


    private function getShopOrderFirstArr($auditorid){
        $start_date = "2017-12-01";
        $end_date = "2018-01-01";

        $sql = "select tt1.id from (
                    select * from (
                        select * from shoporders where is_pay=1 and type = 'chufang' order by time_pay asc
                    )tt group by patientid
                )tt1 inner join doctors b on b.id = tt1.the_doctorid
                where tt1.time_pay > :start_date and tt1.time_pay < :end_date
                and (tt1.amount - tt1.refund_amount > 1000) and b.auditorid_market = :auditorid";
        $bind = array();
        $bind[":auditorid"] = $auditorid;
        $bind[":start_date"] = $start_date;
        $bind[":end_date"] = $end_date;
        return Dao::queryValues($sql, $bind);
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_shoporder_jixiao_new.php]=====");

$process = new Output_shoporder_jixiao_new();
$process->dowork();

Debug::trace("=====[cron][end][Output_shoporder_jixiao_new.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
