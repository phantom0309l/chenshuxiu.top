<?php
// 问卷管理
class XQuestionSheetMgrAction extends AuditBaseAction
{

    // 问卷列表
    public function doList () {

        $xQuestionSheets = Dao::getEntityListByCond('XQuestionSheet', " order by id desc ");

        XContext::setValue('list', $xQuestionSheets);

        return self::SUCCESS;
    }

    // 预览
    public function doOne () {

        $xquestionsheetid = XRequest::getValue('xquestionsheetid', 0);
        $issimple = XRequest::getValue('issimple', 0);

        $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);

        XContext::setValue('xquestionsheet', $xquestionsheet);
        XContext::setValue('issimple', $issimple);

        return self::SUCCESS;
    }

    // 作第一题
    public function doFirstQuestion () {
        $xquestionsheetid = XRequest::getValue('xquestionsheetid', 0);
        $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);

        $xquestion = $xquestionsheet->getFirstQuestion();

        XContext::setValue('xquestionsheet', $xquestionsheet);
        XContext::setValue('xquestion', $xquestion);

        return self::SUCCESS;
    }

    // 问卷新增
    public function doAdd () {
        $sn = XRequest::getValue('sn', '');
        $title = XRequest::getValue('title', '');
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);
        $objcode = XRequest::getValue('objcode', '');

        XContext::setValue('sn', $sn);
        XContext::setValue('title', $title);
        XContext::setValue('objtype', $objtype);
        XContext::setValue('objid', $objid);
        XContext::setValue('objcode', $objcode);

        return self::SUCCESS;
    }

    // 问卷新增提交
    public function doAddPost () {

        $sn = XRequest::getValue('sn', '');
        $title = XRequest::getValue('title', '');
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);
        $objcode = XRequest::getValue('objcode', '');

        if ($sn) {
            $entity = XQuestionSheet::getBySn($sn);
            if ($entity instanceof XQuestionSheet) {
                $preMsg = "sn重复,请修改";
                XContext::setJumpPath("/xquestionsheetmgr/add?sn={$sn}&objtype={$objtype}&objid={$objid}&objcode={$objcode}&title={$title}&preMsg={$preMsg}");
                return self::SUCCESS;
            }
        }

        $row = array();
        $row["sn"] = $sn;
        $row["title"] = $title;
        $row["objtype"] = $objtype;
        $row["objid"] = $objid;
        $row["objcode"] = $objcode;
        $xquestionsheet = XQuestionSheet::createByBiz($row);

        if ($objtype && $objid) {
            $entity = Dao::getEntityById($objtype, $objid);

            if (empty($objcode)) {
                $callback = "{$objtype}Callback";
            } else {
                $callback = "{$objcode}Callback";
            }

            $entity->$callback($xquestionsheet);
        }

        XContext::setJumpPath("/xquestionsheetmgr/list");

        return self::SUCCESS;
    }

    // 问卷修改
    public function doModify () {
        $xquestionsheetid = XRequest::getValue('xquestionsheetid', 0);
        $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);

        XContext::setValue('xquestionsheet', $xquestionsheet);
        return self::SUCCESS;
    }

    // 问卷修改提交
    public function doModifyPost () {
        $xquestionsheetid = XRequest::getValue('xquestionsheetid', 0);
        $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);

        $xquestionsheet->sn = XRequest::getValue('sn', '');
        $xquestionsheet->title = XRequest::getValue('title', '');
        $xquestionsheet->ishidepos = XRequest::getValue('ishidepos', 0);

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/xquestionsheetmgr/modify?xquestionsheetid={$xquestionsheetid}&preMsg={$preMsg}");

        return self::SUCCESS;
    }

    // 问卷删除
    public function doDeleteJson () {
        $xquestionsheetid = XRequest::getValue('xquestionsheetid', 0);
        $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);

        $xanswersheetcnt = $xquestionsheet->getXAnswerSheetCnt();
        if ($xanswersheetcnt > 0) {
            echo 'fail';
        } else {
            $questions = $xquestionsheet->getQuestions();
            foreach ($questions as $q) {
                $q->remove();
            }
            $xquestionsheet->remove();

            echo 'success';
        }

        return self::BLANK;
    }
}
