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

class Create_lilly_shoporder_data
{

    private $is_online = true;

    private $thedate = "";

    const fangcun_name = "方寸泉香（北京）科技有限公司";

    const fangcun_code = 1264391;

    const chufang_hospital_name = "华晋综合门诊部";

    const chufang_hospital_code = "华晋综合门诊部";

    public function dowork () {
        $thedate = date("Y-m-d", time()-86400);
        $this->thedate = $thedate;
        //生成销售数据
        $this->create_sales_data();

        //生成采购数据
        $this->create_purchase_data();

        //生成库存数据
        $this->create_inventory_data();

    }

    //生成销售数据
    private function create_sales_data(){

        echo "\n[销售数据导出开始]\n";
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select a.id
                    from shoporderitemstockitemrefs a
                    inner join shoporderitems b on b.id = a.shoporderitemid
                    where b.shopproductid in (282796166, 282702206, 282796036)
                    and a.createtime > :startdate and a.createtime < :enddate order by a.createtime asc";
        $thedate = $this->thedate;
        $thetime = strtotime($thedate);
        $bind = [];
        $bind[":startdate"] = date("Y-m-d", $thetime - 39*86400);
        $bind[":enddate"] = date("Y-m-d", $thetime + 1*86400);
        $ids = Dao::queryValues($sql, $bind);

        $i = 0;
        $data = array();
        foreach ($ids as $id) {
            $i ++;
            if ($i > 0 && $i % 50 == 0) {
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $shopOrderItemStockItemRef = ShopOrderItemStockItemRef::getById($id);
            if( $shopOrderItemStockItemRef instanceof ShopOrderItemStockItemRef ){
                $temp = array();
                $shopOrderItem = $shopOrderItemStockItemRef->shoporderitem;
                $shopOrder = $shopOrderItem->shoporder;

                //销售日期
                $temp[] = substr($shopOrderItemStockItemRef->createtime, 0, 10);

                //交易号
                $depositeOrder = $shopOrder->getRechargeDepositeOrder();
                $fangcun_trade_no = $depositeOrder->fangcun_trade_no;
                $temp[] = $fangcun_trade_no;

                //销售方名称
                $temp[] = self::fangcun_name;

                //销售方代码
                $temp[] = self::fangcun_code;

                //采购方名称
                $temp[] = self::chufang_hospital_name;

                //采购方代码
                $temp[] = self::chufang_hospital_code;

                //产品名称（商品名 + 通用名）
                $shopProduct = $shopOrderItem->shopproduct;
                $medicineProduct = $shopProduct->obj;
                $medicine_name = $this->getMedicine_name($medicineProduct);
                $temp[] = $medicine_name;

                //产品规格（包含完整的产品包装规格 + 单位）
                $size = $this->getMedicine_size_pack($medicineProduct);
                $temp[] = $size;

                //产品代码
                $temp[] = $shopProduct->id;

                //销售量（销售：正数；销退：负数）
                $temp[] = $shopOrderItemStockItemRef->cnt;

                //批号
                $stockItem = $shopOrderItemStockItemRef->stockitem;
                $temp[] = $stockItem->batch_number;

                //效期
                $temp[] = $stockItem->expire_date;

                $data[] = $temp;
            }
        }
        $headarr = array(
            "销售日期",
            "交易号",
            "销售方名称",
            "销售方代码",
            "采购方名称",
            "采购方代码",
            "产品名称",
            "产品规格",
            "产品代码",
            "销售量",
            "批号",
            "效期",
        );

        $this->createExcelAndFTPUpload($data, $headarr, $thetime, "Sales");
        $unitofwork->commitAndInit();
    }

    //生成采购数据
    private function create_purchase_data(){
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select id from stockitems
                    where shopproductid in (282796166, 282702206, 282796036)
                    and createtime > :startdate  and createtime < :enddate";
        $thedate = $this->thedate;
        $thetime = strtotime($thedate);
        $bind = [];
        $bind[":startdate"] = date("Y-m-d", $thetime - 39*86400);
        $bind[":enddate"] = date("Y-m-d", $thetime + 1*86400);
        $ids = Dao::queryValues($sql, $bind);

        $i = 0;
        $data = array();
        foreach ($ids as $id) {
            echo "[{$id}]\n";
            $i ++;
            if ($i > 0 && $i % 50 == 0) {
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $stockItem = StockItem::getById($id);
            if( $stockItem instanceof StockItem ){
                $temp = array();

                //采购日期
                $temp[] = substr($stockItem->in_time, 0, 10);

                //交易号
                $temp[] = $stockItem->id;

                //发货方名称
                $temp[] = $stockItem->sourse;

                //发货方代码
                $temp[] = $stockItem->sourse;

                //采购方名称
                $temp[] = self::fangcun_name;

                //采购方代码
                $temp[] = self::fangcun_code;

                //产品名称（商品名 + 通用名）
                $shopProduct = $stockItem->shopproduct;
                $medicineProduct = $shopProduct->obj;
                $medicine_name = $this->getMedicine_name($medicineProduct);
                $temp[] = $medicine_name;

                //产品规格（包含完整的产品包装规格 + 单位）
                $size = $this->getMedicine_size_pack($medicineProduct);
                $temp[] = $size;

                //产品代码
                $temp[] = $shopProduct->id;

                //采购量（采购：正数；采退：负数）
                $temp[] = $stockItem->cnt;

                //批号
                $temp[] = $stockItem->batch_number;

                //效期
                $temp[] = $stockItem->expire_date;

                $data[] = $temp;
            }
        }
        $headarr = array(
            "采购日期",
            "交易号",
            "发货方名称",
            "发货方代码",
            "采购方名称",
            "采购方代码",
            "产品名称",
            "产品规格",
            "产品代码",
            "采购量",
            "批号",
            "效期",
        );

        $this->createExcelAndFTPUpload($data, $headarr, $thetime, "Purchase");

        $unitofwork->commitAndInit();
    }

    //生成库存数据
    private function create_inventory_data(){
        $unitofwork = BeanFinder::get("UnitOfWork");
        $thedate = $this->thedate;
        $thetime = strtotime($thedate);

        $sql = "select id from stockitems
                    where shopproductid in (282796166, 282702206, 282796036) and left_cnt > 0";
        $ids = Dao::queryValues($sql);

        $i = 0;
        $data = array();
        foreach ($ids as $id) {
            $i ++;
            if ($i > 0 && $i % 50 == 0) {
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $stockItem = StockItem::getById($id);
            if( $stockItem instanceof StockItem ){
                $temp = array();

                //库存统计日期
                $temp[] = $thedate;

                //经销商名称
                $temp[] = self::fangcun_name;

                //经销商代码
                $temp[] = self::fangcun_code;

                //产品名称（商品名 + 通用名）
                $shopProduct = $stockItem->shopproduct;
                $medicineProduct = $shopProduct->obj;
                $medicine_name = $this->getMedicine_name($medicineProduct);
                $temp[] = $medicine_name;

                //产品规格（包含完整的产品包装规格 + 单位）
                $size = $this->getMedicine_size_pack($medicineProduct);
                $temp[] = $size;

                //产品代码
                $temp[] = $shopProduct->id;

                //库存量
                $temp[] = $stockItem->left_cnt;

                //批号
                $temp[] = $stockItem->batch_number;

                //效期
                $temp[] = $stockItem->expire_date;

                $data[] = $temp;
            }
        }
        $headarr = array(
            "库存统计日期",
            "经销商名称",
            "经销商代码",
            "产品名称",
            "产品规格",
            "产品代码",
            "库存量",
            "批号",
            "效期",
        );

        $this->createExcelAndFTPUpload($data, $headarr, $thetime, "Inventory");

        $unitofwork->commitAndInit();
    }

    //生成excel并FTP上传
    private function createExcelAndFTPUpload($data, $headarr, $thetime, $data_type){
        $thedate_fix = date("Y_m_d", $thetime);
        $file_name_part = "{$thedate_fix}_1264391_{$data_type}.xls";
        $path = $this->getSaveFilePath();
        $file_url = "{$path}{$file_name_part}";
        ExcelUtil::createForCron($data, $headarr, $file_url);
        $this->FTPUpload($file_url, $file_name_part);
    }

    //获取存放生成文件的路径
    private function getSaveFilePath(){
        $is_online = $this->is_online;
        if($is_online){
            $path = "/home/xdata/lilly/shoporder_data/";
        }else{
            $path = "/home/taoxiaojin/shoporder_lilly/";
        }
        return $path;
    }

    //FTP上传
    private function FTPUpload($local_file, $remote_file_name){
        $ftp = new FTPService();
        $remote_file = "Inbound/{$remote_file_name}";
        $is_upload = $ftp->uploadImp($local_file, $remote_file);
        $ftp->close();
        if(false == $is_upload){
            Debug::warn("礼来经销商数据上传失败，请重传");
        }
    }

    //获取产品名称
    private function getMedicine_name($medicineProduct){
        $name_brand = $medicineProduct->name_brand;
        $name_common = $medicineProduct->name_common;
        $medicine_name = "{$name_brand}({$name_common})";
        return $medicine_name;
    }

    //获取产品规格
    private function getMedicine_size_pack($medicineProduct){
        $size_pack = $medicineProduct->size_pack;
        $size_chengfen = $medicineProduct->size_chengfen;
        $size = "{$size_pack} {$size_chengfen}";
        return $size;
    }


}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Create_lilly_shoporder_data.php]=====");

$process = new Create_lilly_shoporder_data();
$process->dowork();

Debug::trace("=====[cron][end][Create_lilly_shoporder_data.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
