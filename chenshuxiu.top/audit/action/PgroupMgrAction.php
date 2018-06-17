<?php

class PgroupMgrAction extends AuditBaseAction
{

    public function dolist () {
        $typestr = XRequest::getValue("typestr", "");
        $hide_all_closed = XRequest::getValue("hide_all_closed", 1);
        $cond = "";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();

        $cond .= " and diseaseid in ($diseaseidstr) ";

        if( $typestr ){
            $cond .= " and typestr = :typestr";
            $bind[':typestr'] = $typestr;
        }

        if( $hide_all_closed ){
            $cond .= " and ( showinwx = 1 or showinaudit = 1 )";
        }

        $cond .= " order by showinaudit desc, showinwx desc";
        $pgroups = Dao::getEntityListByCond('Pgroup', $cond, $bind);
        XContext::setValue('typestr', $typestr);
        XContext::setValue('hide_all_closed', $hide_all_closed);
        XContext::setValue('pgroups', $pgroups);
        return self::SUCCESS;
    }

    public function doDetailHtml () {
        $pgroupid = XRequest::getValue("pgroupid", 0);
        $pgroup = Pgroup::getById( $pgroupid );

        XContext::setValue('pgroup', $pgroup);
        return self::SUCCESS;
    }

    public function doAdd () {
        $diseases = DiseaseDao::getListAll();
        XContext::setValue("diseases", $diseases);
        return self::SUCCESS;
    }

    public function doAddPost () {
        $name = XRequest::getValue("name", "");
        $ename = XRequest::getValue("ename", "");
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");
        $doctorid = XRequest::getValue("doctorid", 0);
        $typestr = XRequest::getValue("typestr", "manage");

        $row = array();
        $row["name"] = $name;
        $row["ename"] = $ename;
        $row["diseaseid"] = $diseaseid;
        $row["doctorid"] = $doctorid;
        $row["typestr"] = $typestr;

        $pgroup = Pgroup::createByBiz($row);
        $pgroupid = $pgroup->id;

        XContext::setJumpPath("/pgroupmgr/modify?pgroupid={$pgroupid}");
        return self::SUCCESS;
    }

    public function doModify () {
        $pgroupid = XRequest::getValue("pgroupid", 0);
        $pgroup = Pgroup::getById($pgroupid);
        XContext::setValue("pgroup", $pgroup);
        return self::SUCCESS;
    }

    public function doModifyInfo () {
        $pgroupid = XRequest::getValue("pgroupid", 0);
        $pgroup = Pgroup::getById($pgroupid);
        $subtypestr_arr = Pgroup::getSubTypestrDescArr();

        XContext::setValue("pgroup", $pgroup);
        XContext::setValue("subtypestr_arr", $subtypestr_arr);
        return self::SUCCESS;
    }

    public function doModifyInfoPost () {
        $pgroupid = XRequest::getValue("pgroupid", 0);
        $name = XRequest::getValue("name", "");
        $ename = XRequest::getValue("ename", "");
        $subtypestr = XRequest::getValue("subtypestr", "");
        $refer_pgroupids = XRequest::getValue("refer_pgroupids", "");
        $pgroup = Pgroup::getById($pgroupid);

        $pgroup->name = $name;
        $pgroup->ename = $ename;
        $pgroup->subtypestr = $subtypestr;
        $pgroup->refer_pgroupids = $refer_pgroupids;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/pgroupmgr/modifyinfo?pgroupid={$pgroupid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doCheckAddJson () {
        $ename = XRequest::getValue("ename", "");

        $pgroup = PgroupDao::getOneByEname($ename);

        if ($pgroup instanceof Pgroup) {
            echo "false";
            return self::BLANK;
        }

        echo "ok";
        return self::BLANK;
    }

    public function doAddHtml () {
        $addtype = XRequest::getValue("addtype", "");
        $pgroupid = XRequest::getValue("pgroupid", 0);
        $pgroup = Pgroup::getById($pgroupid);

        if ($addtype == "addcourse") {
            $courses = CourseDao::getEntityListByCond("Course");
            XContext::setValue("courses", $courses);
        }

        if ($addtype == "addoutpapertpl") {
            $papertpls = PaperTplDao::getListByDiseaseid($pgroup->diseaseid);
            XContext::setValue("papertpls", $papertpls);
        }

        XContext::setValue("pgroup", $pgroup);
        XContext::setValue("addtype", $addtype);
        return self::SUCCESS;
    }

    public function doAddCoursePost () {
        $pgroupid = XRequest::getValue("pgroupid", 0);
        $courseid = XRequest::getValue("courseid", 0);

        $pgroup = Pgroup::getById( $pgroupid );
        $pgroup->set4lock("courseid", $courseid);

        XContext::setJumpPath("/pgroupmgr/modify?pgroupid={$pgroupid}");
        return self::SUCCESS;
    }

    public function doAddOutPaperTplPost () {
        $pgroupid = XRequest::getValue("pgroupid", 0);
        $papertplid = XRequest::getValue("papertplid", 0);

        if ($pgroupid) {
            $pgroup = Pgroup::getById($pgroupid);
            $pgroup->outpapertplid = $papertplid;
        }

        XContext::setJumpPath("/pgroupmgr/modify?pgroupid={$pgroupid}");
        return self::SUCCESS;
    }

    public function doModifyLevelJson () {
        $pgroupid = XRequest::getValue("pgroupid", 0);
        $value = XRequest::getValue("value", 0);

        $pgroup = Pgroup::getById($pgroupid);
        $pgroup->level = $value;

        echo "ok";
        return self::BLANK;
    }

    public function doModifyShowInWxJson () {
        $pgroupid = XRequest::getValue("pgroupid", 0);
        $value = XRequest::getValue("value", 1);

        $pgroup = Pgroup::getById($pgroupid);
        $pgroup->showinwx = $value;

        echo "ok";
        return self::BLANK;
    }

    public function doModifyShowInAuditJson () {
        $pgroupid = XRequest::getValue("pgroupid", 0);
        $value = XRequest::getValue("value", 1);

        $pgroup = Pgroup::getById($pgroupid);
        $pgroup->showinaudit = $value;

        echo "ok";
        return self::BLANK;
    }

}
