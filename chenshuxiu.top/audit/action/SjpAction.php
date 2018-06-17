<?php

class SjpAction extends AuditBaseAction
{

    public function doTest () {
        $doctorid = XRequest::getValue('doctorid', 461);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $sql = "select distinct a.*
            from patients a
            inner join wxusers b on b.patientid = a.id
            where a.doctorid = :doctorid
            limit 500 ";

        $bind[':doctorid'] = $doctorid;
        $patients = Dao::loadEntityList('Patient', $sql, $bind);

        XContext::setValue('patients', $patients);

        return self::SUCCESS;
    }

    public function doCancerDoctorWxShopFix () {
        $sql = "select distinct c.id
from doctorwxshoprefs a
left join doctorwxshoprefs b on (b.wxshopid in (19) and b.doctorid = a.doctorid )
inner join doctors c on c.id = a.doctorid
inner join wxshops d on d.id = a.wxshopid
where a.wxshopid in (12, 14, 15, 17, 21) and b.id is null;";

        $doctorids = Dao::queryValues($sql);

        foreach ($doctorids as $doctorid) {
            $row = array();
            $row["doctorid"] = $doctorid;
            $row["wxshopid"] = 19;
            $ref = DoctorWxShopRef::createByBiz($row);

            $ref->check_qr_ticket();

            echo "<br/> {$doctorid} => {$ref->doctor->name}";
        }

        return self::blank;
    }

    public function doInitSysAccounts () {
        return self::blank;
    }

    public function doCreateTestShopOrders () {
        $sql = "select distinct a.id
from patients a
inner join users b on b.patientid = a.id
inner join auditors c on c.userid = b.id
where b.id > 1 and auditroleids like '%13%'";

        $patientids = Dao::queryValues($sql);

        $shopProducts = Dao::getEntityListByCond("ShopProduct");

        $shopOrders = [];

        foreach ($patientids as $patientid) {
            $row = array();
            $row["patientid"] = $patientid;
            $shopOrders[] = $shopOrder = ShopOrder::createByBiz($row);

            $amount = 0;

            foreach ($shopProducts as $a) {
                if (rand(0, 100) > 50) {
                    $row = array();
                    $row["shoporderid"] = $shopOrder->id;
                    $row["shopproductid"] = $a->id;
                    $row["price"] = $a->price;
                    $row["cnt"] = rand(2, 9);
                    $shopOrderItem = ShopOrderItem::createByBiz($row);

                    $amount += $shopOrderItem->getAmount();
                }
            }

            $shopOrder->fixAmount($amount);
        }

        return $shopOrders;
    }

    // 测试函数
    public function doDebug () {
        Debug::addNotice("addNotice1");

        Debug::trace("--trace--");
        Debug::trace("--info--");

        Debug::addNotice("addNotice2");

        Debug::sql("--sql--");
        Debug::warn("--warn 第一行
                第二行 --");
        Debug::error("--error--");
        Debug::log("--第一行 \n 第二行 \n 默认参数: LOG , must=false , 合并行--");

        Debug::addNotice("addNotice3");

        DBC::requireTrue(false, '我抛出的异常消息');

        return self::blank;
    }

    // // 测试发红包
    // public function doSendWxRedPack () {
    // $helper = new WxRedPackHelper();
    //
    // $helper->setBaseParams('oX3J9s-4C8-udTD6nPETAJaQ8_rQ', 100);
    //
    // $helper->setValue("nick_name", '方寸泉香(北京)科技有限公司'); // 提供方名称
    // $helper->setValue("send_name", '方寸儿童管理服务平台'); // 红包发送者名称
    // $helper->setValue("wishing", '我是祝福语'); // 红包祝福语
    // $helper->setValue("act_name", '我是活动名称'); // 活动名称
    // $helper->setValue("remark", '快来抢！'); // 备注信息
    //
    // $responseXml = $helper->dowork();
    //
    // $json = $helper->xmlToArray($responseXml);
    //
    // echo json_encode($json, JSON_UNESCAPED_UNICODE);
    // return self::BLANK;
    // }
}
