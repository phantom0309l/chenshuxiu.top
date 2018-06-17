<?php

class FitPageTplMgrAction extends AuditBaseAction
{

    public function doList () {
        $fitpagetpls = Dao::getEntityListByCond('FitPageTpl');

        XContext::setValue('fitpagetpls', $fitpagetpls);

        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $code = XRequest::getValue('code', '');
        $name = XRequest::getValue('name', '');
        $remark = XRequest::getValue('remark', '');

        $row = array();
        $row['code'] = trim($code);
        $row['name'] = $name;
        $row['remark'] = $remark;

        $fitpagetpl = FitPageTpl::createByBiz($row);

        XContext::setJumpPath('/fitpagetplmgr/list');

        return self::SUCCESS;
    }

    public function doModify () {
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);

        $fitpagetpl = FitPageTpl::getById($fitpagetplid);

        XContext::setValue('fitpagetpl', $fitpagetpl);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);
        $name = XRequest::getValue('name', '');
        $remark = XRequest::getValue('remark', '');

        $fitpagetpl = FitPageTpl::getById($fitpagetplid);
        $fitpagetpl->name = $name;
        $fitpagetpl->remark = $remark;

        XContext::setJumpPath('/fitpagetplmgr/list');

        return self::SUCCESS;
    }

    public function doDeleteJson () {
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);

        $fitpagetpl = FitPageTpl::getById($fitpagetplid);

        // 如果fitpage下有实例而且元素库中有元素，则不能删除
        if ($fitpagetpl->getFitPageTplItemCnt() > 0 || $fitpagetpl->getFitPageCnt() > 0) {
            echo "fail";
        } else {
            $fitpagetpl->remove();
            echo "success";
        }

        return self::BLANK;
    }
}
