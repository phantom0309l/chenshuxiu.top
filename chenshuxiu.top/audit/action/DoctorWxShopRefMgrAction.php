<?php
// DoctorWxShopRefMgrAction
class DoctorWxShopRefMgrAction extends AuditBaseAction
{

    // 列表 医生-服务号-关联
    public function doList () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $wxshopid = XRequest::getValue('wxshopid', 0);

        $doctor = Doctor::getById($doctorid);
        $wxshop = WxShop::getById($wxshopid);

        $cond = '';
        $bind = [];

        // 医生
        $notBind_wxshops = array();
        if ($doctor instanceof Doctor) {
            $cond .= ' and doctorid=:doctorid ';
            $bind[':doctorid'] = $doctorid;

            $notBind_wxshops = WxShopDao::getList_NotBindDoctorByDoctor($doctor);
        }

        if ($wxshop instanceof WxShop) {
            $cond .= ' and wxshopid=:wxshopid';
            $bind[':wxshopid'] = $wxshopid;
        }

        $cond .= ' order by wxshopid asc, diseaseid asc';

        $doctorWxShopRefs = Dao::getEntityListByCond('DoctorWxShopRef', $cond, $bind);

        $wxshopids = [];
        // 也许需要修补 qr_ticket
        foreach ($doctorWxShopRefs as $doctorWxShopRef) {
            $doctorWxShopRef->getQrUrl();
            $wxshopids[] = $doctorWxShopRef->wxshopid;
        }

        // 方寸诊后服务平台
        if (false == in_array(13, $wxshopids)) {
            $notBind_wxshops[] = WxShop::getById(13);
        }

        XContext::setValue('doctor', $doctor);
        XContext::setValue('wxshop', $wxshop);
        XContext::setValue('doctorWxShopRefs', $doctorWxShopRefs);
        XContext::setValue('notBind_wxshops', $notBind_wxshops);

        return self::SUCCESS;
    }

    public function doModify () {
        $doctorid = XRequest::getValue('doctorid', 0);

        $doctor = Doctor::getById($doctorid);

        $notBind_wxshops = [];

        $cond = '';
        $bind = [];

        // 医生
        if ($doctor instanceof Doctor) {
            $cond .= ' and doctorid=:doctorid order by wxshopid asc,diseaseid asc ';
            $bind[':doctorid'] = $doctorid;

            $notBind_wxshops = WxShopDao::getList_NotBindDoctorByDoctor($doctor);
        }

        $doctorWxShopRefs = Dao::getEntityListByCond('DoctorWxShopRef', $cond, $bind);

        $wxshopids = [];

        // 也许需要修补 qr_ticket
        foreach ($doctorWxShopRefs as $doctorWxShopRef) {
            $doctorWxShopRef->getQrUrl();
            $wxshopids[] = $doctorWxShopRef->wxshopid;
        }

        // 方寸诊后服务平台
        if (false == in_array(13, $wxshopids)) {
            $notBind_wxshops[] = WxShop::getById(13);
        }
        $doctordiseaserefs = DoctorDiseaseRefDao::getListByDoctor($doctor);

        XContext::setValue('doctordiseaserefs', $doctordiseaserefs);
        XContext::setValue('doctor', $doctor);
        XContext::setValue('doctorWxShopRefs', $doctorWxShopRefs);
        XContext::setValue('notBind_wxshops', $notBind_wxshops);

        return self::SUCCESS;
    }

    // 批量添加 医生-服务号-关联
    public function doAddPost () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $wxshopids = XRequest::getValue('wxshopids', []);

        foreach ($wxshopids as $wxshopid) {
            $the_doctorwxshopref = DoctorWxShopRefDao::getByDoctoridWxShopidDiseaseid($doctorid, $wxshopid, 0);
            if (false == $the_doctorwxshopref instanceof DoctorWxShopRef) {
                $row = array();
                $row["doctorid"] = $doctorid;
                $row["wxshopid"] = $wxshopid;
                $doctorWxShopRef = DoctorWxShopRef::createByBiz($row);
                $doctorWxShopRef->getQrUrl();
            }
        }

        XContext::setJumpPath("/doctorwxshoprefmgr/modify?doctorid={$doctorid}");

        return self::SUCCESS;
    }

    // 删除 医生-服务号-关联
    public function doDeletePost () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);
        $doctorwxshopref->remove();

        XContext::setJumpPath("/doctorwxshoprefmgr/modify?doctorid={$doctorwxshopref->doctorid}&preMsg=" . urlencode("删除成功"));

        return self::SUCCESS;
    }

    // 添加 医生-服务号-关联 下的专属疾病二维码
    public function doAddOnlyOnedisease () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);
        $doctordiseaserefs = DoctorDiseaseRefDao::getListByDoctor($doctorwxshopref->doctor);

        XContext::setValue('doctorwxshopref', $doctorwxshopref);
        XContext::setValue('doctordiseaserefs', $doctordiseaserefs);

        return self::SUCCESS;
    }

    // 添加 医生-服务号-关联 下的专属疾病二维码
    public function doAddOnlyOnediseasePost () {
        $diseaseid = XRequest::getValue('diseaseid', 0);
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);
        $frompage = XRequest::getValue('frompage', '');

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);
        $doctorid = $doctorwxshopref->doctorid;
        $wxshopid = $doctorwxshopref->wxshopid;

        $the_doctorwxshopref = DoctorWxShopRefDao::getByDoctoridWxShopidDiseaseid($doctorid, $wxshopid, $diseaseid);
        if (false == $the_doctorwxshopref instanceof DoctorWxShopRef) {
            $row = array();
            $row["doctorid"] = $doctorid;
            $row["wxshopid"] = $wxshopid;
            $row["diseaseid"] = $diseaseid;
            $a = DoctorWxShopRef::createByBiz($row);
            $a->getQrUrl();
        }

        $jumpurl = "/doctorwxshoprefmgr/list?doctorid={$doctorid}&preMsg=" . urlencode("添加成功");
        if ($frompage) {
            $jumpurl = "/doctorwxshoprefmgr/modify?doctorid={$doctorid}&preMsg=" . urlencode("添加成功");
        }

        XContext::setJumpPath($jumpurl);

        return self::SUCCESS;
    }

    // 非多动症-名片-正面
    public function doNamecard_front () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::namecard_front($doctorwxshopref);

        return self::blank;
    }

    // 非多动症-名片-背面
    public function doNamecard_back () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::namecard_back($doctorwxshopref);

        return self::blank;
    }

    // 非多动症-名片2-正面
    public function doNamecard2_front () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::namecard2_front($doctorwxshopref);

        return self::blank;
    }

    // 非多动症-名片2-背面
    public function doNamecard2_back () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::namecard2_back($doctorwxshopref);

        return self::blank;
    }

    // 非多动症-名片2-背面-肿瘤
    public function doNamecard2_back_cancer () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::namecard2_back_cancer($doctorwxshopref);

        return self::blank;
    }

    // 非多动症-桌牌-竖
    public function dozhuopai_shu () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::zhuopai_shu($doctorwxshopref);

        return self::blank;
    }

    // adhd-桌牌-横版
    public function doAdhd_zhuopai_heng () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::adhd_zhuopai_heng($doctorwxshopref);

        return self::blank;
    }

    // adhd-桌牌-竖
    public function doAdhd_zhuopai_shu () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::adhd_zhuopai_shu($doctorwxshopref);

        return self::blank;
    }

    // Adhd-名片-正面-绿色
    public function doAdhd_namecard_front_green () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::adhd_namecard_front($doctorwxshopref, 'green');

        return self::blank;
    }

    // Adhd-名片-背面-绿色
    public function doAdhd_namecard_back_green () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::adhd_namecard_back($doctorwxshopref, 'green');

        return self::blank;
    }

    // Adhd-名片-背面-绿色-复杂
    public function doAdhd_namecard_back_green_fix () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::adhd_namecard_back_fix($doctorwxshopref, 'green');

        return self::blank;
    }

    // Adhd-名片-正面-蓝色
    public function doAdhd_namecard_front_blue () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::adhd_namecard_front($doctorwxshopref, 'blue');

        return self::blank;
    }

    // Adhd-名片-背面-蓝色
    public function doAdhd_namecard_back_blue () {
        $doctorwxshoprefid = XRequest::getValue('doctorwxshoprefid', 0);

        $doctorwxshopref = DoctorWxShopRef::getById($doctorwxshoprefid);

        NameCardHelper::adhd_namecard_back($doctorwxshopref, 'blue');

        return self::blank;
    }
}
