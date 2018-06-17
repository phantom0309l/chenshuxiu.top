<?php

class LinkmanMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    
    // 列表
    public function doListofpatient() {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, 'patient is null');

        $cond = "";
        $bind = [];

        $cond .= " and patientid = :patientid order by id desc ";
        $bind[":patientid"] = $patientid;

        //获得实体
        $linkmans = Dao::getEntityListByCond('Linkman', $cond, $bind);
        XContext::setValue("linkmans", $linkmans);
        XContext::setValue("patient", $patient);

        return self::SUCCESS;
    }
    
    // 详情页
    public function doOne () {
        $linkmanid = XRequest::getValue("linkmanid", 0);

        $linkman = Linkman::getById($linkmanid);

        XContext::setValue("linkman", $linkman);
        return self::SUCCESS;
    }

    public function doAddOrModifyJson () {
        $linkmanid = XRequest::getValue("linkmanid", 0);
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        $name = XRequest::getValue("name", '');
        $shipstr = XRequest::getValue("shipstr", '');
        $mobile = XRequest::getValue("mobile", '');
        $is_master = XRequest::getValue("is_master", 0);

        if (!$mobile) {
            echo "号码不能为空";

            return self::BLANK;
        }

        if ($is_master == 1) {
            $linkmans = LinkmanDao::getListByPatientid($patientid);
            foreach ($linkmans as $a) {
                $a->is_master = 0;
            }
        }

        if ($linkmanid) {
            $linkman = Linkman::getById($linkmanid);
            DBC::requireTrue($linkman instanceof Linkman, "linkman不存在:{$linkmanid}");

            if ($linkman->mobile != $mobile) {
                $linkman_x = LinkmanDao::getByPatientidMobile($patientid, $mobile);
                if ($linkman_x instanceof Linkman) {
                    echo 'mobile_already';

                    return self::BLANK;
                }

                XPatientIndex::deleteXpatientIndexMobile($patient, $linkman->mobile);
                XPatientIndex::addXPatientIndexMobile($patient, $mobile);
            }

            $linkman->name = $name;
            $linkman->shipstr = $shipstr;
            $linkman->mobile = $mobile;
            $linkman->is_master = $is_master;

            echo "success-modify";
        } else {
            $linkman_x = LinkmanDao::getByPatientidMobile($patientid, $mobile);
            if ($linkman_x instanceof Linkman) {
                echo 'mobile_already';

                return self::BLANK;
            }

            $row = [];
            $row["patientid"] = $patientid;
            $row["name"] = $name;
            $row["shipstr"] = $shipstr;
            $row["mobile"] = $mobile;
            $row["is_master"] = $is_master;
            Linkman::createByBiz($row);

            XPatientIndex::addXPatientIndexMobile($patient, $mobile);

            echo "success-add";
        }

        return self::BLANK;
    }

    // 获取联系人关系
    public function dogetshipstrsjson () {
        $shipstrs = Linkman::getShipstrs();

        $this->result['data'] = $shipstrs;

        XContext::setValue('json', $this->result);

        return self::TEXTJSON;
    }

    public function doDeleteJson () {
        $linkmanid = XRequest::getValue('linkmanid', 0);
        $linkman = Linkman::getById($linkmanid);

        if ($linkman instanceof Linkman) {
            XPatientIndex::deleteXpatientIndexMobile($linkman->patient, $linkman->mobile);

            $linkman->remove();

            echo "delete-success";
        } else {
            echo "delete-fail";
        }

        return self::BLANK;
    }
}
        