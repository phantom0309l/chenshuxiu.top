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

class Output_shoporder_jixiao_new_fix
{

    public function dowork () {

        $auditor_arr = [10032, 10042];

        $arr_10032 = array(
                            314351256,448860136,468970846,488974666,489738746,490661236,
                            502457776,502500516,506790086,507823306,508070696,508403026,
                            508685656,508715356,508729566,509191476,509233986,509663616,
                            510226736,511194556,511436246,512306696,513121886,513429386,
                            513578576,513974156,515117806,516370326,516462796,516842246,
                            518613206,519532976,520912066,522159186,522233286,523528586,
                            523563906,530501576,534876146);
        $arr_10042 = array(493098206,509316636,509337806,514937466,515520776,525949606);

        $auditorid = 10032;
        $auditor = Auditor::getById($auditorid);
        $i = 0;
        $data = array();
        //编号
        $num = 0;

        foreach($arr_10032 as $patientid){
            $unitofwork = BeanFinder::get("UnitOfWork");

            echo "======市场[{$auditor->name}]开始导出======\n";

            $ids = $this->getShopOrderIdArr($patientid);
            $ids = array_slice($ids,1);


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
        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/shoporder/output_shoporder_jixiao_new_fix_{$auditor->name}.xlsx");
        $unitofwork->commitAndInit();
    }


    private function getShopOrderIdArr($patientid){
        $start_date = "2017-12-01";
        $end_date = "2018-01-01";

        $sql = "select id from shoporders
                where is_pay=1 and type = 'chufang'
                and time_pay >= '2017-12-01' and time_pay < '2018-01-01' and patientid = :patientid";
        $bind = array();
        $bind[":patientid"] = $patientid;
        return Dao::queryValues($sql, $bind);
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_shoporder_jixiao_new_fix.php]=====");

$process = new Output_shoporder_jixiao_new_fix();
$process->dowork();

Debug::trace("=====[cron][end][Output_shoporder_jixiao_new_fix.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
