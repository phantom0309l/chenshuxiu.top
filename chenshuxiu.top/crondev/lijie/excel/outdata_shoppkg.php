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
$thedate = $argv[1];

class Outdata_shoppkg
{

    public function dowork ($thedate) {
        if(is_null($thedate)){
            $thedate = '2018-05';
        }
        //生成销售数据
        $this->create_shoppkg_data($thedate);
    }

    //生成物流数据
    private function create_shoppkg_data($thedate){

        echo "\n[数据导出开始]\n";
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "SELECT a.id
            FROM shoppkgs a
            INNER JOIN shoporders b ON b.id=a.shoporderid
            WHERE b.is_pay = 1 AND a.is_sendout = 1
            AND a.express_company = '中通'
            AND left(b.time_pay, 7) = :thedate";

        $bind = [];
        $bind[":thedate"] = $thedate;
        $ids = Dao::queryValues($sql, $bind);

        $data = array();
        foreach ($ids as $i => $id) {
            if ($i / 100 == 0) {
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $shopPkg = ShopPkg::getById($id);
            if( $shopPkg instanceof ShopPkg ){
                $temp = array();
                $temp[] = $shopPkg->shoporderid;
                $temp[] = $shopPkg->id;
                $temp[] = $shopPkg->fangcun_platform_no;
                $temp[] = $shopPkg->express_no;
                $temp[] = $shopPkg->shoporder->shopaddress->getDetailAddress();
                $temp[] = $shopPkg->shoporder->shopaddress->xprovince->name;
                $temp[] = $shopPkg->shoporder->shopaddress->xcity->name;
                $temp[] = $shopPkg->time_sendout;

                $result = ExpressService::getTraces($shopPkg);
                Debug::trace($result);

                $temp[] = $result["Traces"][0]["AcceptTime"];

                $data[] = $temp;
            }
        }
        $headarr = array(
            "中通订单号",
            "中通配送单号",
            "方寸平台单号",
            "快递单号",
            "到货地址",
            "到货地址(省)",
            "到货地址(市)",
            "发货时间",
            "到货时间",
        );

        $this->createExcelAndFTPUpload($data, $headarr, $thedate, "shoppkg");
        echo "\n[数据导出完毕]\n";
        $unitofwork->commitAndInit();
    }

    //生成excel
    private function createExcelAndFTPUpload($data, $headarr, $thedate, $data_type){
        $thedate_fix = date("Y_m", strtotime($thedate));
        $file_name = "{$thedate_fix}_{$data_type}.xls";
        $file_url = "/home/taoxiaojin/{$file_name}";
        ExcelUtil::createForCron($data, $headarr, $file_url);
        //$this->FTPUpload($file_url, $file_name_part);
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Outdata_shoppkg.php]=====");

$process = new Outdata_shoppkg();
$process->dowork($thedate);

Debug::trace("=====[cron][end][Outdata_shoppkg.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
