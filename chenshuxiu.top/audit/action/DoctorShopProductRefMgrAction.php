<?php

class DoctorShopProductRefMgrAction extends AuditBaseAction
{

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct();
    }

    public function doBindDoctor() {
        $shopproducttypeid = XRequest::getValue('shopproducttypeid', 0);
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor = Doctor::getById($doctorid);

        $mydisease = $this->mydisease;

        //初始化商品类型
        $shopProductTypes = [];

        //初始化商品
        $shopProducts = [];
        if ($mydisease instanceof Disease && $mydisease->diseasegroup instanceof DiseaseGroup) {
            $shopProductTypes = ShopProductTypeDao::getListByDiseaseGroupid($mydisease->diseasegroupid);

            $cond = "";
            $bind = [];

            $cond .= " and b.diseasegroupid=:diseasegroupid ";
            $bind[":diseasegroupid"] = $mydisease->diseasegroupid;

            if ($shopproducttypeid > 0) {
                $cond .= " and a.shopproducttypeid=:shopproducttypeid ";
                $bind[":shopproducttypeid"] = $shopproducttypeid;
            }

            $can_bind_zhengding = $doctor->canBindZhengding();
            if (false == $can_bind_zhengding) {
                $zhengding_id = ShopProduct::ZHENGDING_ID;
                $cond .= " and a.id not in ({$zhengding_id})";
            }


            $sql = "select a.*
            from shopproducts a
            inner join shopproducttypes b on b.id = a.shopproducttypeid
            where 1=1 " . $cond . " order by shopproducttypeid , a.pos, title_pinyin ";
            $shopProducts = Dao::loadEntityList("ShopProduct", $sql, $bind);
        }

        XContext::setValue('shopproducttypeid', $shopproducttypeid);

        XContext::setValue('shopProductTypes', $shopProductTypes);

        XContext::setValue('doctor', $doctor);
        XContext::setValue('shopProducts', $shopProducts);

        return self::SUCCESS;
    }

    public function doBindOrUnbindShopProductJson() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $shopproductid = XRequest::getValue("shopproductid", 0);

        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, "医生不能为空");

        $shopproduct = ShopProduct::getById($shopproductid);
        DBC::requireTrue($shopproduct instanceof ShopProduct, "产品不能为空");

        $status = XRequest::getValue("status", 1);

        $doctorShopProductRef = DoctorShopProductRefDao::getOneByDoctorShopProduct($doctor, $shopproduct);

        if ($doctorShopProductRef instanceof DoctorShopProductRef) {
            if ($status == 0) {
                $doctorShopProductRef->remove();
            }
        } else {
            if ($status == 1) {
                $row = array();
                $row["doctorid"] = $doctorid;
                $row["shopproductid"] = $shopproductid;
                DoctorShopProductRef::createByBiz($row);
            }
        }
        echo "ok";
        return self::BLANK;
    }

    public function doBindOrUnbindShopProductsJson() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $shopproductids = XRequest::getValue("shopproductids", []);

        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, "医生不能为空");

        $status = XRequest::getValue("status", 1);

        foreach ($shopproductids as $shopproductid) {
            $shopproduct = ShopProduct::getById($shopproductid);
            if (false == $shopproduct instanceof ShopProduct) {
                continue;
            }

            $doctorShopProductRef = DoctorShopProductRefDao::getOneByDoctorShopProduct($doctor, $shopproduct);

            if ($doctorShopProductRef instanceof DoctorShopProductRef) {
                if ($status == 0) {
                    $doctorShopProductRef->remove();
                }
            } else {
                if ($status == 1) {
                    $row = array();
                    $row["doctorid"] = $doctorid;
                    $row["shopproductid"] = $shopproductid;
                    DoctorShopProductRef::createByBiz($row);
                }
            }
        }
        return self::TEXTJSON;
    }

    public function doBindOnlineShopProducts() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        if (false == $doctor instanceof Doctor) {
            $this->returnError('医生不存在');
        }

        $mydisease = $this->mydisease;
        if ($mydisease instanceof Disease && $mydisease->diseasegroup instanceof DiseaseGroup) {
            $cond = "";
            $bind = [];

            $cond .= " and b.diseasegroupid=:diseasegroupid ";
            $bind[":diseasegroupid"] = $mydisease->diseasegroupid;

            $can_bind_zhengding = $doctor->canBindZhengding();
            if (false == $can_bind_zhengding) {
                $zhengding_id = ShopProduct::ZHENGDING_ID;
                $cond .= " and a.id not in ({$zhengding_id})";
            }

            $cond .= " AND a.status = 1 ";

            $sql = "SELECT a.id
                    FROM shopproducts a
                    INNER JOIN shopproducttypes b ON b.id = a.shopproducttypeid
                    WHERE 1=1 " . $cond . " ORDER BY shopproducttypeid , a.pos, title_pinyin ";
            $shopproductids = Dao::queryValues($sql, $bind);

            foreach ($shopproductids as $shopproductid) {
                $shopproduct = ShopProduct::getById($shopproductid);
                if (false == $shopproduct instanceof ShopProduct) {
                    continue;
                }

                $doctorShopProductRef = DoctorShopProductRefDao::getOneByDoctorShopProduct($doctor, $shopproduct);

                if (false == $doctorShopProductRef instanceof DoctorShopProductRef) {
                    $row = array();
                    $row["doctorid"] = $doctorid;
                    $row["shopproductid"] = $shopproductid;
                    DoctorShopProductRef::createByBiz($row);
                }
            }
        }
        XContext::setJumpPath('/doctorshopproductrefmgr/binddoctor?preMsg=操作成功&doctorid='.$doctorid);
        return self::SUCCESS;
    }
}
