<?php

// ClinicalTestMgrAction
class ClinicalTestMgrAction extends AuditBaseAction
{

    public function doList() {
        $clinicaltests = ClinicalTestDao::getAll();

        XContext::setValue('clinicaltests', $clinicaltests);
        return self::SUCCESS;
    }

    public function doOne() {
        $clinicaltestid = XRequest::getValue('clinicaltestid', 0);
        if (!$clinicaltestid) {
            $this->returnError('缺少参数');
        }

        $clinicaltest = ClinicalTest::getById($clinicaltestid);
        if (false == $clinicaltest instanceof ClinicalTest) {
            $this->returnError('项目不存在');
        }

        XContext::setValue('clinicaltest', $clinicaltest);
        return self::SUCCESS;
    }

    public function doAdd() {
        return self::SUCCESS;
    }

    public function doAddPost() {
        $title = XRequest::getValue('title');
        $list_title = XRequest::getValue('list_title', $title);
        $brief = XRequest::getValue('brief');
        $content = XRequest::getUnSafeValue('content');
        $status = XRequest::getValue('status', 0);
        $remark = XRequest::getValue('remark');

        if (empty($title)) {
            $this->returnError('标题不能为空');
        } elseif (empty($brief)) {
            $this->returnError('简介不能为空');
        } elseif (empty($content)) {
            $this->returnError('内容不能为空');
        }

        $row = [];
        $row["auditorid"] = $this->myauditor->id;
        $row["title"] = $title;
        $row["list_title"] = $list_title;
        $row["brief"] = $brief;
        $row["content"] = $content;
        $row["status"] = $status;
        $row["remark"] = $remark;
        $clinicaltest = ClinicalTest::createByBiz($row);

        XContext::setJumpPath('/clinicaltestmgr/list?preMsg=添加成功');
        return self::SUCCESS;
    }

    public function doModify() {
        $clinicaltestid = XRequest::getValue('clinicaltestid', 0);
        $clinicaltest = ClinicalTest::getById($clinicaltestid);
        if (false == $clinicaltest instanceof ClinicalTest) {
            $this->returnError('活动不存在');
        }

        XContext::setValue('clinicaltest', $clinicaltest);
        return self::SUCCESS;
    }

    public function doModifyPost() {
        $clinicaltestid = XRequest::getValue('clinicaltestid', 0);
        $clinicaltest = ClinicalTest::getById($clinicaltestid);
        if (false == $clinicaltest instanceof ClinicalTest) {
            $this->returnError('活动不存在');
        }

        $title = XRequest::getValue('title');
        $list_title = XRequest::getValue('list_title', $title);
        $brief = XRequest::getValue('brief');
        $content = XRequest::getUnSafeValue('content');
        $status = XRequest::getValue('status', 0);
        $remark = XRequest::getValue('remark');

        if (empty($title)) {
            $this->returnError('标题不能为空');
        } elseif (empty($brief)) {
            $this->returnError('简介不能为空');
        } elseif (empty($content)) {
            $this->returnError('内容不能为空');
        }

        $clinicaltest->title = $title;
        $clinicaltest->list_title = $list_title;
        $clinicaltest->brief = $brief;
        $clinicaltest->content = $content;
        $clinicaltest->status = $status;
        $clinicaltest->remark = $remark;

        XContext::setJumpPath('/clinicaltestmgr/modify?clinicaltestid=' . $clinicaltestid . '&preMsg=修改成功');
        return self::BLANK;
    }

    public function doAjaxDeletePost() {
        $clinicaltestid = XRequest::getValue('clinicaltestid', 0);
        $clinicaltest = ClinicalTest::getById($clinicaltestid);
        if (false == $clinicaltest instanceof ClinicalTest) {
            $this->returnError('活动不存在');
        }

        $clinicaltest->remove();

        return self::TEXTJSON;
    }
}
