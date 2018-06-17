<?php

class DiseasePaperTplRefMgrAction extends AuditBaseAction
{

    public function doList() {
        $papertplid = XRequest::getValue('papertplid', 0);
        $title = XRequest::getValue('title', '');
        $diseaseid = XRequest::getValue('diseaseid', 0);
        $show_in_audit = XRequest::getValue('show_in_audit', '-1');
        $show_in_wx = XRequest::getValue('show_in_wx', '-1');

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $papertpl = PaperTpl::getById($papertplid);
        $disease = Disease::getById($diseaseid);
        $doctor = Doctor::getById($doctorid);

        $sql = "select distinct a.*
                from diseasepapertplrefs a
                inner join diseases b on b.id = a.diseaseid
                inner join papertpls c on c.id = a.papertplid
                where 1 = 1 ";
        $cond = "";
        $bind = [];

        if ($title) {
            $cond .= " and c.title like :title ";
            $bind[':title'] = "%{$title}%";
        }

        // 筛模板
        if ($papertpl instanceof PaperTpl) {
            $cond .= " and a.papertplid = :papertplid ";
            $bind[':papertplid'] = $papertpl->id;
        }

        // 筛疾病
        if ($disease instanceof Disease) {
            $cond .= " and a.diseaseid = :diseaseid ";
            $bind[':diseaseid'] = $disease->id;
        }

        // 筛医生
        if ($doctorid >= 0) {
            $cond .= " and a.doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        // 运营端可见
        if ($show_in_audit >= 0) {
            $cond .= " and a.show_in_audit = :show_in_audit ";
            $bind[':show_in_audit'] = $show_in_audit;
        }

        // 患者端可见
        if ($show_in_wx >= 0) {
            $cond .= " and a.show_in_wx = :show_in_wx ";
            $bind[':show_in_wx'] = $show_in_wx;
        }

        $sql .= $cond;

        $sql .= " order by a.diseaseid, a.doctorid, a.pos, c.title, a.id ";
        $sql .= " limit 400";

        $diseasepapertplrefs = Dao::LoadEntityList('DiseasePaperTplRef', $sql, $bind);

        XContext::setValue('title', $title);
        XContext::setValue('diseaseid', $diseaseid);
        XContext::setValue('show_in_audit', $show_in_audit);
        XContext::setValue('show_in_wx', $show_in_wx);

        XContext::setValue('disease', $disease);
        XContext::setValue('doctor', $doctor);

        XContext::setValue('diseasepapertplrefs', $diseasepapertplrefs);

        $sql = "select distinct a.*
            from doctors a
            inner join diseasepapertplrefs b on b.doctorid = a.id
            order by a.id";
        $doctors = Dao::loadEntityList('Doctor', $sql);
        XContext::setValue('doctors', $doctors);

        return self::SUCCESS;
    }

    // 修正数据, 将疾病绑定的papertpl同步到医生
    public function doDiseaseToDoctorsJson() {
        $diseaseid = XRequest::getValue('diseaseid', 0);

        $disease = Disease::getById($diseaseid);

        $diseasepapertplrefs = DiseasePaperTplRefDao::getListByDiseaseidDoctorid($diseaseid, 0);

        foreach ($diseasepapertplrefs as $a) {
            $papertpl = $a->papertpl;
            $papertplid = $papertpl->id;

            echo "<br/><br/>{$papertplid} {$papertpl->title} <br/>";

            $doctordiseaseRefs = DoctorDiseaseRefDao::getListByDisease($disease);
            foreach ($doctordiseaseRefs as $b) {
                $doctorid = $b->doctorid;

                echo "<br/>  $doctorid {$b->doctor->name} ";

                $cond = ' and diseaseid=:diseaseid and doctorid = :doctorid and papertplid = :papertplid ';
                $bind = array(
                    ":diseaseid" => $diseaseid,
                    ":doctorid" => $doctorid,
                    ":papertplid" => $papertplid);

                $ref = Dao::getEntityByCond('DiseasePaperTplRef', $cond, $bind);

                if (false == $ref instanceof DiseasePaperTplRef) {
                    $row = array();
                    $row["papertplid"] = $papertplid;
                    $row["diseaseid"] = $diseaseid;
                    $row["doctorid"] = $doctorid;
                    $row["show_in_audit"] = $a->show_in_audit;
                    $row["show_in_wx"] = $a->show_in_wx;
                    DiseasePaperTplRef::createByBiz($row);

                    echo " ++";
                } else {
                    echo " ==";
                }
            }
        }

        return self::blank;
    }

    public function doModify() {
        $diseasepapertplrefid = XRequest::getValue("diseasepapertplrefid", 0);
        $diseasepapertplref = DiseasePaperTplRef::getById($diseasepapertplrefid);
        XContext::setValue('diseasepapertplref', $diseasepapertplref);

        return self::SUCCESS;
    }

    public function doAddPost() {
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");

        $papertplid = XRequest::getValue("papertplid", 0);
        $show_in_audit = XRequest::getValue("show_in_audit", 0);
        $show_in_wx = XRequest::getValue("show_in_wx", 0);

        $row = array();
        $row["diseaseid"] = $diseaseid;
        $row["papertplid"] = $papertplid;
        $row["show_in_audit"] = $show_in_audit;
        $row["show_in_wx"] = $show_in_wx;

        DiseasePaperTplRef::createByBiz($row);

        XContext::setJumpPath("/diseasepapertplrefmgr/list?diseaseid={$diseaseid}");
        return self::BLANK;
    }

    public function doModifyPost() {
        $diseasepapertplrefid = XRequest::getValue("diseasepapertplrefid", 0);
        $doctorid = XRequest::getValue("doctorid", 0);
        $show_in_audit = XRequest::getValue("show_in_audit", 0);
        $show_in_wx = XRequest::getValue("show_in_wx", 0);
        $diseasepapertplref = DiseasePaperTplRef::getById($diseasepapertplrefid);

        $diseasepapertplref->doctorid = $doctorid;
        $diseasepapertplref->show_in_audit = $show_in_audit;
        $diseasepapertplref->show_in_wx = $show_in_wx;

        $preMsg = "修改已保存 " . date('H:i:s');
        XContext::setJumpPath("/diseasepapertplrefmgr/modify?diseasepapertplrefid={$diseasepapertplrefid}&preMsg=" . urlencode($preMsg));

        return self::BLANK;
    }

    // 修改序号
    public function doPosModifyPost() {
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $refid => $pos) {
            $a = DiseasePaperTplRef::getById($refid);
            $a->pos = $pos;
        }

        $preMsg = "已保存序号调整" . XDateTime::now();
        XContext::setJumpPath("/diseasepapertplrefmgr/list?preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doDeletePost() {
        $diseasepapertplrefid = XRequest::getValue("diseasepapertplrefid", 0);
        $diseasepapertplref = DiseasePaperTplRef::getById($diseasepapertplrefid);
        $diseaseid = $diseasepapertplref->diseaseid;

        $diseasepapertplref->remove();

        $preMsg = "已删除 ref [{$diseasepapertplrefid}] [{$diseasepapertplref->diseaseid} {$diseasepapertplref->disease->name},{$diseasepapertplref->papertplid} {$diseasepapertplref->papertpl->title}] ";
        XContext::setJumpPath("/diseasepapertplrefmgr/list?diseaseid={$diseaseid}&preMsg=" . urlencode($preMsg));

        return self::BLANK;
    }

    // 医生量表列表
    public function doListOfDoctor() {
        $doctorid = XRequest::getValue("doctorid", 0);

        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $keyword = XRequest::getValue("keyword");
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $pagenum = XRequest::getValue("pagenum", 1);
        $pagesize = 20;

        $diseases = $doctor->getDiseases();

        $sql = "SELECT a.*
                FROM diseasepapertplrefs a ";

        $sqlFix = "LEFT JOIN papertpls b on b.id = a.papertplid
                   WHERE a.doctorid = :doctorid";

        $bind[":doctorid"] = $doctor->id;

        if (!empty($keyword)) {
            $sqlFix .= ' AND b.title LIKE :title ';
            $bind[":title"] = "%{$keyword}%";
        }

        if (!empty($diseaseid)) {
            $sqlFix .= ' AND a.diseaseid = :diseaseid ';
            $bind[":diseaseid"] = $diseaseid;
        }

        $sqlFix .= " ORDER BY a.pos, a.id ";

        $sql = $sql . $sqlFix;
        $diseaDiseasePaperTplRefs = Dao::loadEntityList4Page('DiseasePaperTplRef', $sql, $pagesize, $pagenum, $bind);

        // 分页
        $countSql = "SELECT COUNT(*)
                FROM diseasepapertplrefs a " . $sqlFix;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/diseasepapertplrefmgr/listofdoctor?doctorid={$doctorid}&keyword={$keyword}&diseaseid={$diseaseid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("diseases", $diseases);
        XContext::setValue("diseaDiseasePaperTplRefs", $diseaDiseasePaperTplRefs);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("keyword", $keyword);
        XContext::setValue("diseaseid", $diseaseid);
        XContext::setValue("doctor", $doctor);
        return self::SUCCESS;
    }

    // 删除医生量表
    public function doDeleteOfDoctorPost() {
        $diseasepapertplrefid = XRequest::getValue("diseasepapertplrefid", 0);
        $diseasepapertplref = DiseasePaperTplRef::getById($diseasepapertplrefid);
        DBC::requireTrue($diseasepapertplref instanceof DiseasePaperTplRef, '量表不存在');

        $diseasepapertplref->remove();

        $preMsg = '删除成功';

        $refererUrl = urldecode(XContext::getValue('refererUrl'));
        XContext::setJumpPath("{$refererUrl}&preMsg=" . urlencode($preMsg));
        return self::BLANK;
    }

    public function doModifyOfDoctor() {
        $diseasepapertplrefid = XRequest::getValue("diseasepapertplrefid", 0);
        $diseasepapertplref = DiseasePaperTplRef::getById($diseasepapertplrefid);
        DBC::requireTrue($diseasepapertplref instanceof DiseasePaperTplRef, '量表不存在');

        $xquestionsheet = XQuestionSheet::getById($diseasepapertplref->papertpl->xquestionsheetid);

        XContext::setValue('diseasepapertplref', $diseasepapertplref);
        XContext::setValue('xquestionsheet', $xquestionsheet);
        XContext::setValue('doctor', $diseasepapertplref->doctor);
        return self::SUCCESS;
    }

    // 修改医生量表
    public function doModifyOfDoctorPost() {
        $diseasepapertplrefid = XRequest::getValue("diseasepapertplrefid", 0);
        $diseasepapertplref = DiseasePaperTplRef::getById($diseasepapertplrefid);
        DBC::requireTrue($diseasepapertplref instanceof DiseasePaperTplRef, '量表不存在');

        $show_in_audit = XRequest::getValue("show_in_audit");
        $show_in_wx = XRequest::getValue("show_in_wx");

        $diseasepapertplref->show_in_audit = $show_in_audit == 'on' ? 1 : 0;
        $diseasepapertplref->show_in_wx = $show_in_wx == 'on' ? 1 : 0;

        $preMsg = "修改成功";
        XContext::setJumpPath("/diseasepapertplrefmgr/modifyofdoctor?diseasepapertplrefid={$diseasepapertplrefid}&preMsg=" . urlencode($preMsg));
        return self::BLANK;
    }

    public function doAddOfDoctor() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $diseases = $doctor->getDiseases();
        DBC::requireTrue(count($diseases), '医生没有疾病');

        $diseaseid = XRequest::getValue("diseaseid", 0);
        if ($diseaseid == 0) {
            $diseaseid = $diseases[0]->id;
        }

        $papertpls = PaperTplDao::getListByDiseaseid($diseaseid, " and b.doctorid=0 ");

        $sql = "SELECT papertplid FROM diseasepapertplrefs WHERE doctorid = :doctorid AND diseaseid = :diseaseid";
        $bind = [
            ":doctorid" => $doctorid,
            ":diseaseid" => $diseaseid
        ];
        $diseasepapertplrefids = Dao::queryValues($sql, $bind);

        XContext::setValue('diseaseid', $diseaseid);
        XContext::setValue('diseases', $diseases);
        XContext::setValue('papertpls', $papertpls);
        XContext::setValue('diseasepapertplrefids', $diseasepapertplrefids);
        XContext::setValue('doctor', $doctor);
        return self::SUCCESS;
    }

    public function doAjaxPaperTplHtml() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $papertplid = XRequest::getValue("papertplid", 0);
        $papertpl = PaperTpl::getById($papertplid);
        DBC::requireTrue($papertpl instanceof PaperTpl, '量表不存在');

        $diseaseid = XRequest::getValue("diseaseid", 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireTrue($disease instanceof Disease, '疾病不存在');

        // 这个是用来回显 运营可见，患者可见 的
        $diseasepapertplref = DiseasePaperTplRefDao::getByDoctorAndDiseaseAndPaperTpl($doctor, $disease, $papertpl);

        $xquestionsheet = XQuestionSheet::getById($papertpl->xquestionsheetid);

        XContext::setValue('papertpl', $papertpl);
        XContext::setValue('xquestionsheet', $xquestionsheet);
        XContext::setValue('diseasepapertplref', $diseasepapertplref);
        XContext::setValue('disease', $disease);
        XContext::setValue('doctor', $doctor);
        return self::SUCCESS;
    }

    public function doAddOfDoctorPost() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $papertplid = XRequest::getValue("papertplid", 0);
        $papertpl = PaperTpl::getById($papertplid);
        DBC::requireTrue($papertpl instanceof PaperTpl, '量表不存在');

        $diseaseid = XRequest::getValue("diseaseid", 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireTrue($disease instanceof Disease, '疾病不存在');

        $show_in_audit = XRequest::getValue("show_in_audit");
        $show_in_wx = XRequest::getValue("show_in_wx");

        $row = array();
        $row["doctorid"] = $doctorid;
        $row["diseaseid"] = $diseaseid;
        $row["papertplid"] = $papertplid;
        $row["show_in_audit"] = $show_in_audit == 'on' ? 1 : 0;
        $row["show_in_wx"] = $show_in_wx == 'on' ? 1 : 0;

        $diseasePaperTplRef = DiseasePaperTplRefDao::getByDoctorAndDiseaseAndPaperTpl($doctor, $disease, $papertpl);
        if (false == $diseasePaperTplRef instanceof DiseasePaperTplRef) {
            $diseasePaperTplRef = DiseasePaperTplRef::createByBiz($row);
        }

        $refererUrl = urldecode(XContext::getValue('refererUrl'));
        XContext::setJumpPath("{$refererUrl}&preMsg=量表添加成功");
        return self::BLANK;
    }
}
