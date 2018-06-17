<?php

// CheckupTplMgrAction
class CheckupTplMgrAction extends AuditBaseAction
{

    // 检查报告模板列表
    public function doList () {
        $title = XRequest::getValue('title', '');

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $cond = "";
        $bind = [];

        if ($title != '') {
            $cond .= " and title like :title ";
            $bind[':title'] = "%{$title}%";
        }

        if ($doctorid > 0) {
            $cond .= " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        } else {
            $cond .= " AND doctorid > 0 "; // 忽略掉疾病默认模板
        }

        $cond .= " order by pos asc ";

        $checkuptpls = Dao::getEntityListByCond('CheckupTpl', $cond, $bind);

        $i = 0;
        foreach ($checkuptpls as $a) {
            $i++;
            $a->pos = $i;
        }

        XContext::setValue('title', $title);
        XContext::setValue('checkuptpls', $checkuptpls);

        return self::SUCCESS;
    }

    public function doAdd() {
        return self::SUCCESS;
    }

    public function doAddPost() {
        $doctorid = XRequest::getValue('doctorid', '');
        $diseaseid = XRequest::getValue('diseaseid', '0');

        $checkuptplids = XRequest::getValue('checkuptplids', '0');

        foreach ($checkuptplids as $checkuptplid) {

            $checkuptpl = Dao::getEntityById('CheckupTpl', $checkuptplid);

            $cond = ' AND title = :title AND diseaseid = :diseaseid AND doctorid = :doctorid ';
            $bind = array(
                ':title' => $checkuptpl->title,
                ':diseaseid' => $diseaseid,
                ':doctorid' => $doctorid);
            $checkuptplNew = Dao::getEntityByCond('CheckupTpl', $cond, $bind);

            if ($checkuptplNew instanceof CheckupTpl) {
                if (0 == $checkuptplNew->xquestionsheetid) {
                    // 复制问卷
                    $checkuptpl->copyXQuestionSheetTo($checkuptplNew);
                } else {
                    continue;
                }
            } else {
                $checkuptpl->copyOne($diseaseid, $doctorid);
            }

        }

        XContext::setJumpPath("/checkuptplmgr/list");
        return self::SUCCESS;
    }

    public function doModify() {
        $checkuptplid = XRequest::getValue('checkuptplid', 0);

        $checkuptpl = CheckupTpl::getById($checkuptplid);

        XContext::setValue('checkuptpl', $checkuptpl);
        return self::SUCCESS;
    }

    public function doModifyPost() {
        $checkuptplid = XRequest::getValue('checkuptplid', 0);
        $doctorid = XRequest::getValue('doctorid', 0);
        $groupstr = XRequest::getValue('groupstr', '');
        $ename = XRequest::getValue('ename', '');
        $title = XRequest::getValue('title', '');
        $brief = XRequest::getValue('brief', '');
        $content = XRequest::getValue('content', '');
        $is_in_tkt = XRequest::getValue('is_in_tkt', 0);
        $is_in_admin = XRequest::getValue('is_in_admin', 0);
        $is_selected = XRequest::getValue('is_selected', 0);

        $checkuptpl = CheckupTpl::getById($checkuptplid);
        $checkuptpl->doctorid = $doctorid;
        $checkuptpl->groupstr = $groupstr;
        $checkuptpl->ename = $ename;
        $checkuptpl->title = $title;
        $checkuptpl->brief = $brief;
        $checkuptpl->content = $content;
        $checkuptpl->is_in_tkt = $is_in_tkt;
        $checkuptpl->is_in_admin = $is_in_admin;
        $checkuptpl->is_selected = $is_selected;

        $preMsg = "修改已提交 " . XDateTime::now();
        XContext::setJumpPath("/checkuptplmgr/modify?checkuptplid={$checkuptplid}&preMsg=" . urlencode($preMsg));

        return self::SUCCESS;
    }

    // 修改排序
    public function doPosModifyPost() {
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $id => $pos) {
            $checkuptpl = CheckupTpl::getById($id);
            $checkuptpl->pos = $pos;
        }

        $preMsg = "已保存顺序调整,并修正序号 " . XDateTime::now();
        XContext::setJumpPath("/checkuptplmgr/list?preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doDeleteJson() {
        $checkuptplid = XRequest::getValue('checkuptplid', 0);

        $checkuptpl = CheckupTpl::getById($checkuptplid);
        $checkuptpl->remove();

        echo "success";

        return self::BLANK;
    }

    public function doDefaultCheckupTplAndDoctorOfDiseaseJson() {
        $diseaseid = XRequest::getValue('diseaseid', 0);

        $cond = ' AND diseaseid=:diseaseid AND doctorid=0';
        $bind = array(
            ':diseaseid' => $diseaseid);
        $checkuptpls = Dao::getEntityListByCond('CheckupTpl', $cond, $bind);
        $doctors = DoctorDao::getListByDiseaseid($diseaseid);
        $data = array();
        $data['checkuptpls'] = FUtil::entitysToJsonArray($checkuptpls);
        $data['doctors'] = FUtil::entitysToJsonArray($doctors);
        XContext::setValue("json", $data);
        return self::TEXTJSON;

    }

    public function doListOfDoctor() {
        $doctorid = XRequest::getValue("doctorid", 0);

        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $keyword = XRequest::getValue("keyword");
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $pagenum = XRequest::getValue("pagenum", 1);

        $pagesize = 20;

        $diseases = $doctor->getDiseases();

        $cond = "";
        $bind = [];

        if (!empty($keyword)) {
            $cond .= " AND title LIKE :title ";
            $bind[':title'] = "%{$keyword}%";
        }

        if (!empty($diseaseid)) {
            $cond .= ' AND diseaseid = :diseaseid ';
            $bind[":diseaseid"] = $diseaseid;
        }

        $cond .= " AND doctorid = :doctorid ";
        $bind[':doctorid'] = $doctorid;

//        $cond .= " ORDER BY pos ASC, createtime DESC ";
        $cond .= " ORDER BY createtime DESC ";

        $checkuptpls = Dao::getEntityListByCond4Page('CheckupTpl', $pagesize, $pagenum, $cond, $bind);

        // 分页
        $countSql = "SELECT COUNT(*) FROM checkuptpls WHERE 1 = 1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/checkuptplmgr/listofdoctor?doctorid={$doctorid}&keyword={$keyword}&diseaseid={$diseaseid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("diseases", $diseases);
        XContext::setValue('checkuptpls', $checkuptpls);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("keyword", $keyword);
        XContext::setValue("diseaseid", $diseaseid);
        XContext::setValue("doctor", $doctor);
    }

    public function doDeleteOfDoctorPost() {
        $checkuptplid = XRequest::getValue("checkuptplid", 0);
        $checkuptpl = CheckupTpl::getById($checkuptplid);
        DBC::requireTrue($checkuptpl instanceof CheckupTpl, '检查报告不存在');

        $checkuptpl->remove();

        $preMsg = '删除成功';

        $refererUrl = urldecode(XContext::getValue('refererUrl'));
        XContext::setJumpPath("{$refererUrl}&preMsg=" . urlencode($preMsg));
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

        $cond = ' AND diseaseid=:diseaseid AND doctorid=0';
        $bind = array(
            ':diseaseid' => $diseaseid);
        $checkuptpls = Dao::getEntityListByCond('CheckupTpl', $cond, $bind);

        XContext::setValue('diseaseid', $diseaseid);
        XContext::setValue('diseases', $diseases);
        XContext::setValue('checkuptpls', $checkuptpls);
        XContext::setValue('doctor', $doctor);
        return self::SUCCESS;
    }

    public function doAjaxCheckupTpl() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $checkuptplid = XRequest::getValue("checkuptplid", 0);
        $checkuptpl = CheckupTpl::getById($checkuptplid);
        DBC::requireTrue($checkuptpl instanceof CheckupTpl, '检查报告不存在');

        $diseaseid = XRequest::getValue("diseaseid", 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireTrue($disease instanceof Disease, '疾病不存在');

        $xquestionsheet = XQuestionSheet::getById($checkuptpl->xquestionsheetid);

        XContext::setValue('checkuptpl', $checkuptpl);
        XContext::setValue('xquestionsheet', $xquestionsheet);
        XContext::setValue('disease', $disease);
        XContext::setValue('doctor', $doctor);
        return self::SUCCESS;
    }

    public function doAddOfDoctorPost() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $diseaseid = XRequest::getValue("diseaseid", 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireTrue($disease instanceof Disease, '疾病不存在');

        $checkuptplid = XRequest::getValue("checkuptplid", 0);
        $checkuptpl = CheckupTpl::getById($checkuptplid);
        DBC::requireTrue($checkuptpl instanceof CheckupTpl, '检查报告不存在');

        $cond = ' AND title = :title AND diseaseid = :diseaseid AND doctorid = :doctorid ';
        $bind = array(
            ':title' => $checkuptpl->title,
            ':diseaseid' => $diseaseid,
            ':doctorid' => $doctorid);
        $checkuptplNew = Dao::getEntityByCond('CheckupTpl', $cond, $bind);

        $preMsg = '添加成功';
        if ($checkuptplNew instanceof CheckupTpl) {
            if (0 == $checkuptplNew->xquestionsheetid) {
                // 复制问卷
                $checkuptpl->copyXQuestionSheetTo($checkuptplNew);
            }
        } else {
            $checkuptpl->copyOne($diseaseid, $doctorid);
        }

        $refererUrl = urldecode(XContext::getValue('refererUrl'));
        XContext::setJumpPath("{$refererUrl}&preMsg={$preMsg}");
        return self::SUCCESS;
    }

    public function doModifyOfDoctor() {
        $checkuptplid = XRequest::getValue("checkuptplid", 0);
        $checkuptpl = CheckupTpl::getById($checkuptplid);
        DBC::requireTrue($checkuptpl instanceof CheckupTpl, '检查报告不存在');

        XContext::setValue('checkuptpl', $checkuptpl);
        XContext::setValue('doctor', $checkuptpl->doctor);
        return self::SUCCESS;
    }

    // 修改医生量表
    public function doModifyOfDoctorPost() {
        $checkuptplid = XRequest::getValue('checkuptplid', 0);
        $groupstr = XRequest::getValue('groupstr', '');
        $ename = XRequest::getValue('ename', '');
        $title = XRequest::getValue('title', '');
        $brief = XRequest::getValue('brief', '');
        $content = XRequest::getValue('content', '');
        $is_in_tkt = XRequest::getValue('is_in_tkt', 0);
        $is_in_admin = XRequest::getValue('is_in_admin', 0);
        $is_selected = XRequest::getValue('is_selected', 0);

        $checkuptpl = CheckupTpl::getById($checkuptplid);
        $checkuptpl->groupstr = $groupstr;
        $checkuptpl->ename = $ename;
        $checkuptpl->title = $title;
        $checkuptpl->brief = $brief;
        $checkuptpl->content = $content;
        $checkuptpl->is_in_tkt = $is_in_tkt;
        $checkuptpl->is_in_admin = $is_in_admin;
        $checkuptpl->is_selected = $is_selected;

        $preMsg = "修改成功";
        XContext::setJumpPath("/checkuptplmgr/modifyofdoctor?checkuptplid={$checkuptplid}&preMsg=" . $preMsg);
        return self::BLANK;
    }

}
