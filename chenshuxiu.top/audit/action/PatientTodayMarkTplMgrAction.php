<?php

// PatientTodayMarkTplMgrAction
class PatientTodayMarkTplMgrAction extends AuditBaseAction
{
    /***
     * 展示模板列表
     * @return string
     */
    public function doList() {
        $todaymarktpls = Dao::getEntityListByCond('PatientTodayMarkTpl');
        $diseasegroups = DiseaseGroupDao::getAll();

        XContext::setValue("diseasegroups", $diseasegroups);
        XContext::setValue("todaymarktpls", $todaymarktpls);

        return self::SUCCESS;
    }

    /***
     * 更改模板
     * @return string
     */
    public function doModifyPostJson() {
        $marktplid = XRequest::getValue('marktplid', 0);
        $marktpltitle = XRequest::getValue('marktpltitle', 0);
        $diseasegroupid = XRequest::getValue('diseasegroupid', 0);

        $marktpl = PatientTodayMarkTplDao::getEntityById('PatientTodayMarkTpl', $marktplid);
        if (false == $marktpl instanceof PatientTodayMarkTpl) {
            $this->returnError("模板不存在");
        }

        $patienttodaymark = PatientTodayMarkTplDao::getOneByDiseasegroupidTitle($diseasegroupid, $marktpltitle);
        if ($patienttodaymark instanceof PatientTodayMarkTpl && $patienttodaymark->id != $marktplid) {
            $this->returnError('当前疾病组下已存在 [' . $marktpltitle . ']');
        }

        $marktpl->diseasegroupid = $diseasegroupid;
        $marktpl->title = $marktpltitle;
        return self::TEXTJSON;
    }

    /***
     * 添加模板
     * @return string
     */
    public function doAddPostJson() {
        $marktpltitle = XRequest::getValue('marktpltitle', 0);
        $diseasegroupid = XRequest::getValue('diseasegroupid', 0);
        $patienttodaymark = PatientTodayMarkTplDao::getOneByDiseasegroupidTitle($diseasegroupid, $marktpltitle);
        if ($patienttodaymark instanceof PatientTodayMarkTpl) {
            $this->returnError('当前疾病组下已存在 [' . $marktpltitle . ']');
        }
        $row = array();
        $row["diseasegroupid"] = $diseasegroupid;
        $row["title"] = $marktpltitle;

        PatientTodayMarkTpl::createByBiz($row);
        return self::TEXTJSON;
    }

    /***
     * 删除模板
     * @return string
     */
    public function doDeletePost() {
        $marktplid = XRequest::getValue('marktplid', 0);
        $marktpl = PatientTodayMarkTplDao::getEntityById('PatientTodayMarkTpl', $marktplid);

        if (!$marktpl instanceof PatientTodayMarkTpl) {
            $this->returnError("模板不存在");
        }
        $marktpl->remove();
        XContext::setJumpPath("/patienttodaymarktplmgr/list");
        return self::SUCCESS;
    }
}
