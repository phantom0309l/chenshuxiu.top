<?php

class DiseaseMgrAction extends AuditBaseAction
{

    public function doList () {
        $diseasegroupid = XRequest::getValue("diseasegroupid", "");

        $cond = "";
        $bind = [];

        if ($diseasegroupid > 0) {
            $cond = " and diseasegroupid=:diseasegroupid ";
            $bind[':diseasegroupid'] = $diseasegroupid;
        }

        $cond .= " order by diseasegroupid asc , id asc ";

        $diseases = Dao::getEntityListByCond('Disease', $cond, $bind);

        XContext::setValue("diseases", $diseases);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $diseasegroupid = XRequest::getValue("diseasegroupid", "");
        $name = XRequest::getValue("name", "");
        $code = XRequest::getValue("code", "");

        $row = array();
        $row["id"] = 1 + Dao::queryValue('select max(id) as maxid from diseases where id < 10000');
        $row["name"] = $name;
        $row["code"] = $code;
        $row["diseasegroupid"] = $diseasegroupid;

        Disease::createByBiz($row);

        XContext::setJumpPath("/diseasemgr/list");
        return self::SUCCESS;
    }

    public function doModify () {
        $diseaseid = XRequest::getValue("diseaseid", 0);

        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");

        XContext::setValue("disease", $disease);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");

        $name = XRequest::getValue("name", "");
        $code = XRequest::getValue("code", "");
        $diseasegroupid = XRequest::getValue("diseasegroupid", "");

        $disease->name = $name;
        $disease->code = $code;
        $disease->diseasegroupid = $diseasegroupid;

        XContext::setJumpPath("/diseasemgr/list");
        return self::SUCCESS;
    }

    // 获取wxshop相关的信息
    public function doConfig () {
        $diseaseid = XRequest::getValue('diseaseid', 1);

        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");
        XContext::setValue("disease", $disease);

        $diseasepapertplrefs = DiseasePaperTplRefDao::getListByDisease($disease, null, 1);
        XContext::setValue("diseasepapertplrefs", $diseasepapertplrefs);

        $diseasecourserefs = DiseaseCourseRefDao::getListByDiseaseid($disease->id);
        XContext::setValue("diseasecourserefs", $diseasecourserefs);

        return self::SUCCESS;
    }
}
