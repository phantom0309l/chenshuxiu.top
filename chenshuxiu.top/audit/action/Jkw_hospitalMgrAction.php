<?php

class Jkw_hospitalMgrAction extends AuditBaseAction
{

    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);
        $jkw_hospital_name = XRequest::getValue("jkw_hospital_name", '');

        $cond = "";
        $bind = [];

        if ($jkw_hospital_name != '') {
            $cond .= ' and name like :jkw_hospital_name ';
            $bind[':jkw_hospital_name'] = "%{$jkw_hospital_name}%";
        }

        $jkw_hospitals = Dao::getEntityListByCond4Page("Jkw_hospital", $pagesize, $pagenum, $cond, $bind);

        $countSql = "select count(*) as cnt from jkw_hospitals where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/jkw_hospitalmgr/list?jkw_hospital_name={$jkw_hospital_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("jkw_hospitals", $jkw_hospitals);
        XContext::setValue("jkw_hospital_name", $jkw_hospital_name);

        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 修改页的显示
    public function doModify () {
        $jkw_hospitalid = XRequest::getValue("jkw_hospitalid", 0);
        $jkw_hospital = Jkw_hospital::getById($jkw_hospitalid);

        XContext::setValue("jkw_hospital", $jkw_hospital);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $jkw_hospitalid = XRequest::getValue("jkw_hospitalid", 0);
        $name = XRequest::getValue("name", '');
        $shortname = XRequest::getValue("shortname", '');
        $logo_pictureid = XRequest::getValue("logo_pictureid", 0);
        $type = XRequest::getValue("type", '');
        $levelstr = XRequest::getValue("levelstr", '');
        $mobile = XRequest::getValue("mobile", '');
        $content = XRequest::getValue("content", '');
        $president_name = XRequest::getValue("president_name", '');
        $found_year = XRequest::getValue("found_year", '');
        $department_cnt = XRequest::getValue("department_cnt", '');
        $employee_cnt = XRequest::getValue("employee_cnt", '');
        $bed_cnt = XRequest::getValue("bed_cnt", '');
        $is_yibao = XRequest::getValue("is_yibao", '');
        $website = XRequest::getValue("website", '');
        $postalcode = XRequest::getValue("postalcode", '');
        $brief = XRequest::getValue("brief", '');
        $bus_route = XRequest::getValue("bus_route", '');
        $from_url = XRequest::getValue("from_url", '');

        $jkw_hospital_place = XRequest::getValue('hospital_place', []);
        $jkw_hospital_place = PatientAddressService::fixNull($jkw_hospital_place);

        $jkw_hospital = Jkw_hospital::getById($jkw_hospitalid);
        $jkw_hospital->name = $name;
        $jkw_hospital->shortname = $shortname;
        $jkw_hospital->logo_pictureid = $logo_pictureid;
        $jkw_hospital->type = $type;
        $jkw_hospital->levelstr = $levelstr;
        $jkw_hospital->mobile = $mobile;
        $jkw_hospital->president_name = $president_name;
        $jkw_hospital->found_year = $found_year;
        $jkw_hospital->department_cnt = $department_cnt;
        $jkw_hospital->employee_cnt = $employee_cnt;
        $jkw_hospital->bed_cnt = $bed_cnt;
        $jkw_hospital->is_yibao = $is_yibao;
        $jkw_hospital->website = $website;
        $jkw_hospital->postalcode = $postalcode;
        $jkw_hospital->brief = $brief;
        $jkw_hospital->bus_route = $bus_route;
        $jkw_hospital->from_url = $from_url;

        $jkw_hospital->xprovinceid = $jkw_hospital_place['xprovinceid'];
        $jkw_hospital->xcityid = $jkw_hospital_place['xcityid'];
        $jkw_hospital->xcountyid = $jkw_hospital_place['xcountyid'];
        $jkw_hospital->content = $content;


        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/jkw_hospitalmgr/modify?jkw_hospitalid=" . $jkw_hospitalid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 新建的显示
    public function doAdd () {
        return self::SUCCESS;
    }

    // 新建提交
    public function doAddPost () {
        $name = XRequest::getValue("name", '');
        $shortname = XRequest::getValue("shortname", '');
        $logo_pictureid = XRequest::getValue("logo_pictureid", 0);
        $type = XRequest::getValue("type", '');
        $levelstr = XRequest::getValue("levelstr", '');
        $mobile = XRequest::getValue("mobile", '');
        $content = XRequest::getValue("content", '');
        $president_name = XRequest::getValue("president_name", '');
        $found_year = XRequest::getValue("found_year", '');
        $department_cnt = XRequest::getValue("department_cnt", '');
        $employee_cnt = XRequest::getValue("employee_cnt", '');
        $bed_cnt = XRequest::getValue("bed_cnt", '');
        $is_yibao = XRequest::getValue("is_yibao", '');
        $website = XRequest::getValue("website", '');
        $postalcode = XRequest::getValue("postalcode", '');
        $brief = XRequest::getValue("brief", '');
        $bus_route = XRequest::getValue("bus_route", '');
        $from_url = XRequest::getValue("from_url", '');

        $jkw_hospital_place = XRequest::getValue('hospital_place', []);
        $jkw_hospital_place = PatientAddressService::fixNull($jkw_hospital_place);

        DBC::requireNotEmpty($name, '全称不能为空');
        DBC::requireNotEmpty($shortname, '简称不能为空');
        DBC::requireNotEmpty($content, '地址不能为空');
        DBC::requireNotEmpty($levelstr, '等级不能为空');

        $cond = ' and name=:name ';
        $bind = array(
            ':name' => $name);
        $jkw_hospital = Dao::getEntityByCond('Jkw_hospital', $cond, $bind);
        if ($jkw_hospital instanceof Jkw_hospital) {
            $preMsg = "名字有重复 {$jkw_hospital->name} ";
            XContext::setJumpPath("/jkw_hospitalmgr/add?preMsg=" . urlencode($preMsg));
            return self::SUCCESS;
        }

        $row = [];
        $row["name"] = $name;
        $row["shortname"] = $shortname;
        $row["logo_pictureid"] = $logo_pictureid;
        $row["type"] = $type;
        $row["levelstr"] = $levelstr;
        $row["mobile"] = $mobile;
        $row["xprovinceid"] = $jkw_hospital_place['xprovinceid'];
        $row["xcityid"] = $jkw_hospital_place['xcityid'];
        $row["xcountyid"] = $jkw_hospital_place['xcountyid'];
        $row["content"] = $content;
        $row["president_name"] = $president_name;
        $row["found_year"] = $found_year;
        $row["department_cnt"] = $department_cnt;
        $row["employee_cnt"] = $employee_cnt;
        $row["bed_cnt"] = $bed_cnt;
        $row["is_yibao"] = $is_yibao;
        $row["website"] = $website;
        $row["postalcode"] = $postalcode;
        $row["brief"] = $brief;
        $row["bus_route"] = $bus_route;
        $row["from_url"] = $from_url;

        $jkw_hospital = Jkw_hospital::createByBiz($row);

        XContext::setJumpPath("/jkw_hospitalmgr/list");
        return self::SUCCESS;
    }

}
