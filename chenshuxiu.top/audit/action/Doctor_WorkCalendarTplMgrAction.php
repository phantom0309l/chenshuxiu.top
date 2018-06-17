<?php

// Doctor_WorkCalendarTplMgrAction
class Doctor_WorkCalendarTplMgrAction extends AuditBaseAction
{
    public function doList() {
        $doctorid = XRequest::getValue('doctorid', 0);
        if (empty($doctorid)) {
            $workcalendartpls = Dao::getEntityListByCond('Doctor_WorkCalendarTpl');
        } else {
            $workcalendartpls = Doctor_WorkCalendarTplDao::getListByDoctorid($doctorid);
        }

        XContext::setValue('workcalendartpls', $workcalendartpls);
        XContext::setValue('doctorid', $doctorid);
        return self::SUCCESS;
    }

    public function doEdit() {
        $doctorid = XRequest::getValue('doctorid');
        DBC::requireNotEmpty($doctorid, '医生不存在');
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $diseaseid = XRequest::getValue('diseaseid');
        DBC::requireNotEmpty($diseaseid, '疾病不存在');
        $disease = Disease::getById($diseaseid);
        DBC::requireTrue($disease instanceof Disease, '疾病不存在');

        $tpl = Doctor_WorkCalendarTplDao::getByDoctoridAndDiseaseid($doctorid, $diseaseid);
        if ($tpl instanceof Doctor_WorkCalendarTpl) {
            $configs = $tpl->content;
        }
        XContext::setValue('doctor', $doctor);
        XContext::setValue('disease', $disease);
        XContext::setValue('configs', $configs);
        return self::SUCCESS;
    }

    public function doAdd() {
        $doctorid = XRequest::getValue('doctorid', 0);
        if (!empty($doctorid)) {
            $doctor = Doctor::getById($doctorid);
            DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

            $diseases = $doctor->getDiseases();
        } else {
            $diseases = DiseaseDao::getListAll();
        }

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('diseases', $diseases);

        return self::SUCCESS;
    }

    public function doAddPost() {
        $doctorid = XRequest::getValue('doctorid', 0);
        if (!empty($doctorid)) {
            $doctor = Doctor::getById($doctorid);
            DBC::requireTrue($doctor instanceof Doctor, '医生不存在');
        }

        $diseaseid = XRequest::getValue('diseaseid', 0);
        if (!empty($doctorid) && !empty($diseaseid)) {
            $ref = DoctorDiseaseRefDao::getByDoctoridDiseaseid($doctorid, $diseaseid);
            DBC::requireTrue($ref instanceof DoctorDiseaseRef, '医生疾病关联不存在');
        }

        $code = XRequest::getValue('code');
        DBC::requireNotEmpty($code, 'code不能为空');

        $title = XRequest::getValue('title');
        DBC::requireNotEmpty($title, '标题不能为空');

        $content = XRequest::getUnSafeValue('content');
        DBC::requireNotEmpty($content, '模板配置不能为空');

        $content = trim($content);
        $content = str_replace('\r\n', '', $content);
        $arr = json_decode($content, true);
        DBC::requireTrue($arr, '模板配置解析失败');
        $content = json_encode($arr, JSON_UNESCAPED_UNICODE);

        $row = array();
        $row["doctorid"] = $doctorid;
        $row["diseaseid"] = $diseaseid;
        $row["code"] = $code;
        $row["title"] = $title;
        $row["content"] = $content;
        $workcalendartpl = Doctor_WorkCalendarTpl::createByBiz($row);

        $preMsg = "创建成功";
        XContext::setJumpPath("/doctor_workcalendartplmgr/list?doctorid=" . $doctorid . "&preMsg=" . $preMsg);
        return self::SUCCESS;
    }

    public function doModify() {
        $workcalendartplid = XRequest::getValue('workcalendartplid');
        DBC::requireNotEmpty($workcalendartplid, '工作日历模板不存在');
        $workcalendartpl = Doctor_WorkCalendarTpl::getById($workcalendartplid);
        DBC::requireTrue($workcalendartpl instanceof Doctor_WorkCalendarTpl, '工作日历模板不存在');

        XContext::setValue('workcalendartpl', $workcalendartpl);
        return self::SUCCESS;
    }

    public function doModifyPost() {
        $workcalendartplid = XRequest::getValue('workcalendartplid');
        DBC::requireNotEmpty($workcalendartplid, '工作日历模板不存在');
        $workcalendartpl = Doctor_WorkCalendarTpl::getById($workcalendartplid);
        DBC::requireTrue($workcalendartpl instanceof Doctor_WorkCalendarTpl, '工作日历模板不存在');

        $code = XRequest::getValue('code');
        DBC::requireNotEmpty($code, 'code不能为空');

        $title = XRequest::getValue('title');
        DBC::requireNotEmpty($title, '标题不能为空');

        $content = XRequest::getUnSafeValue('content');
        DBC::requireNotEmpty($content, '模板配置不能为空');

        $content = trim($content);
        $content = str_replace('\r\n', '', $content);
        $arr = json_decode($content, true);
        DBC::requireTrue($arr, '模板配置解析失败');
        $content = json_encode($arr, JSON_UNESCAPED_UNICODE);

        $workcalendartpl->code = $code;
        $workcalendartpl->title = $title;
        $workcalendartpl->content = $content;

        $preMsg = "修改成功";
        XContext::setJumpPath("/doctor_workcalendartplmgr/modify?workcalendartplid=" . $workcalendartplid . "&preMsg=" . $preMsg);
        return self::SUCCESS;
    }

    public function doDeletePost() {
        $workcalendartplid = XRequest::getValue('workcalendartplid');
        DBC::requireNotEmpty($workcalendartplid, '工作日历模板不存在');
        $workcalendartpl = Doctor_WorkCalendarTpl::getById($workcalendartplid);
        DBC::requireTrue($workcalendartpl instanceof Doctor_WorkCalendarTpl, '工作日历模板不存在');

        $doctorid = $workcalendartpl->doctorid;

        $workcalendartpl->remove();

        $preMsg = "删除成功";
        XContext::setJumpPath("/doctor_workcalendartplmgr/list?doctorid=" . $doctorid . "&preMsg=" . $preMsg);
        return self::SUCCESS;
    }
}
