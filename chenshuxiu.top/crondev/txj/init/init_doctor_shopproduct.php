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

class Init_doctor_shopproduct
{
    public function dowork () {
        //$unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select a.id from doctors a
                    inner join doctordiseaserefs b on b.doctorid = a.id
                    where a.menzhen_offset_daycnt > 0 and b.diseaseid=1";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $doctor = Doctor::getById($id);
            if ($doctor instanceof Doctor) {
                $i ++;
                echo "\n====[$i][{$id}]===\n";
                $this->createDoctorShopProducts($doctor);
                //$this->createDoctorShopProductsByShopOrders($doctor);
            }
            if($i>50){
                $i = 0;
                //$unitofwork->commitAndInit();
                //$unitofwork = BeanFinder::get("UnitOfWork");
            }
        }
        //$unitofwork->commitAndInit();
    }

    private function createDoctorShopProducts($doctor){
        //$shopProductArr = array(282796166,282702206,282796036,305494016,287697596,282709756,287703016,287696276,293577176,297483166);
        $shopProductArr = array(638615366);
        foreach ($shopProductArr as $shopproductid) {
            $shopProduct = ShopProduct::getById($shopproductid);
            $isBind = $doctor->hasBindShopProduct($shopProduct);
            if(false == $isBind){
                $unitofwork = BeanFinder::get("UnitOfWork");
                $row = array();
                $row["doctorid"] = $doctor->id;
                $row["shopproductid"] = $shopproductid;
                DoctorShopProductRef::createByBiz($row);
                $unitofwork->commitAndInit();
            }
        }
    }

    private function createDoctorShopProductsByShopOrders($doctor){
        $shopOrders = ShopOrderDao::getIsPayShopOrdersByDoctorType($doctor, "chufang");

        foreach ($shopOrders as $shopOrder) {
            $shopOrderItems = $shopOrder->getShopOrderItems();
            foreach ($shopOrderItems as $shopOrderItem) {
                $shopProduct = $shopOrderItem->shopproduct;
                $isBind = $doctor->hasBindShopProduct($shopProduct);
                if(false == $isBind){
                    $unitofwork = BeanFinder::get("UnitOfWork");
                    $row = array();
                    $row["doctorid"] = $doctor->id;
                    $row["shopproductid"] = $shopProduct->id;
                    DoctorShopProductRef::createByBiz($row);
                    $unitofwork->commitAndInit();
                }
            }
        }
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_doctor_shopproduct.php]=====");

$process = new Init_doctor_shopproduct();
$process->dowork();

Debug::trace("=====[cron][end][Init_doctor_shopproduct.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
