<?php

class HospitalMgrAction extends AuditBaseAction
{

    public function doList () {
        $hospital_name = XRequest::getValue("hospital_name", '');

        $cond = "";
        $bind = [];

        if ($hospital_name != '') {
            $cond .= ' and ( name like :hospital_name or shortname like :hospital_name ) ';
            $bind[':hospital_name'] = "%{$hospital_name}%";
        }

        $cond .= "  and id < 10000";

        $hospitals = Dao::getEntityListByCond("Hospital", $cond, $bind);

        XContext::setValue("hospitals", $hospitals);
        XContext::setValue("hospital_name", $hospital_name);

        return self::SUCCESS;
    }

    // 修改页的显示
    public function doModify () {
        $hospitalid = XRequest::getValue("hospitalid", 0);
        $hospital = Hospital::getById($hospitalid);

        XContext::setValue("hospital", $hospital);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $hospitalid = XRequest::getValue("hospitalid", 0);
        $name = XRequest::getValue("name", '');
        $shortname = XRequest::getValue("shortname", '');
        $logo_pictureid = XRequest::getValue("logo_pictureid", 0);
        $qr_logo_pictureid = XRequest::getValue("qr_logo_pictureid", 0);
        $addressstr = XRequest::getValue("address", '');
        $levelstr = XRequest::getValue("levelstr", '');
        $can_public_zhengding = XRequest::getValue("can_public_zhengding", '');

        $hospital_address = XRequest::getValue('hospital', []);
        $hospital_address = PatientAddressService::fixNull($hospital_address);

        DBC::requireNotEmpty($hospital_address['xprovinceid'], '省份不能为空');

        $hospital = Hospital::getById($hospitalid);
        $hospital->name = $name;
        $hospital->shortname = $shortname;
        $hospital->logo_pictureid = $logo_pictureid;
        $hospital->qr_logo_pictureid = $qr_logo_pictureid;
        $hospital->levelstr = $levelstr;
        $hospital->xprovinceid = $hospital_address['xprovinceid'];
        $hospital->xcityid = $hospital_address['xcityid'];
        $hospital->xcountyid = $hospital_address['xcountyid'];
        $hospital->content = $addressstr;
        $hospital->can_public_zhengding = $can_public_zhengding;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/hospitalmgr/modify?hospitalid=" . $hospitalid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 新建的显示
    public function doAdd () {
        return self::SUCCESS;
    }

    // 新建的显示
    public function doAddFromJkw_hospital () {
        $jkw_hospitalid = XRequest::getValue("jkw_hospitalid", '');
        $jkw_hospital = Jkw_hospital::getById($jkw_hospitalid);

        XContext::setValue("jkw_hospital", $jkw_hospital);
        return self::SUCCESS;
    }

    // 新建提交
    public function doAddPost () {
        $name = XRequest::getValue("name", '');
        $shortname = XRequest::getValue("shortname", '');
        $logo_pictureid = XRequest::getValue("logo_pictureid", 0);
        $qr_logo_pictureid = XRequest::getValue("qr_logo_pictureid", 0);
        $content = XRequest::getValue("content", '');
        $levelstr = XRequest::getValue("levelstr", '');
        $can_public_zhengding = XRequest::getValue("can_public_zhengding", '');

        $hospital_place = XRequest::getValue('hospital_place', []);
        $hospital_place = PatientAddressService::fixNull($hospital_place);

        DBC::requireNotEmpty($name, '全称不能为空');
        DBC::requireNotEmpty($shortname, '简称不能为空');
        DBC::requireNotEmpty($content, '地址不能为空');
        DBC::requireNotEmpty($levelstr, '等级不能为空');
        DBC::requireNotEmpty($hospital_place['xprovinceid'], '省份不能为空');

        $cond = ' and name=:name ';
        $bind = array(
            ':name' => $name);

        $hospital = Dao::getEntityByCond('Hospital', $cond, $bind);
        if ($hospital instanceof Hospital) {
            $preMsg = "名字有重复 {$hospital->name} ";
            XContext::setJumpPath("/hospitalmgr/modify?hospitalid={$hospital->id}&preMsg=" . urlencode($preMsg));
            return self::SUCCESS;
        }

        $sql = " select max(id) as maxid from hospitals where id < 10000 ";
        $maxid = Dao::queryValue($sql, []);

        $row = [];
        $row["id"] = $maxid + 1;
        $row["name"] = $name;
        $row["shortname"] = $shortname;
        $row["logo_pictureid"] = $logo_pictureid;
        $row["qr_logo_pictureid"] = $qr_logo_pictureid;
        $row["levelstr"] = $levelstr;
        $row["xprovinceid"] = $hospital_place['xprovinceid'];
        $row["xcityid"] = $hospital_place['xcityid'];
        $row["xcountyid"] = $hospital_place['xcountyid'];
        $row["content"] = $content;
        $row["can_public_zhengding"] = $can_public_zhengding;
        $hospital = Hospital::createByBiz($row);

        XContext::setJumpPath("/hospitalmgr/list");
        return self::SUCCESS;
    }

    // 医院变更市场负责人页面
    public function doOneForChangeAuditorMarket () {
        $hospitalid = XRequest::getValue("hospitalid", 0);

        $hospital = Hospital::getById($hospitalid);

        XContext::setValue("hospital", $hospital);

        return self::SUCCESS;
    }

    // 医院变更市场负责人接口
    public function doChangeAuditorMarketJson () {
        $hospitalid = XRequest::getValue("hospitalid", 0);
        $to_auditorid_market = XRequest::getValue("to_auditorid_market", 0);

        if ($to_auditorid_market) {

            if (! $hospitalid) {
                echo "default";
                return self::BLANK;
            }

            $sql = " select a.*
                    from doctors a
                    inner join doctordiseaserefs b on b.doctorid=a.id
                    where 1=1 ";
            $bind = [];

            $sql .= " and a.hospitalid=:hospitalid ";
            $bind[':hospitalid'] = $hospitalid;

            $diseaseidstr = $this->getContextDiseaseidStr();
            $sql .= " and b.diseaseid in ($diseaseidstr) ";

            $doctors = Dao::loadEntityList("Doctor", $sql, $bind);

            if (count($doctors) > 0) {
                foreach ($doctors as $doctor) {
                    $doctor->auditorid_market = $to_auditorid_market;
                }

                // 变更成功
                echo "ok";
                return self::BLANK;
            } else {

                // 没有查询到要变更的医生
                echo "notChange";
                return self::BLANK;
            }
        } else {

            // 没有收到要变更成的auditorid_market
            echo "notToMarketId";
            return self::BLANK;
        }
    }
}
