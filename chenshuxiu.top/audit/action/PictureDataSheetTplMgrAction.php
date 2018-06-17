<?php
// PictureDataSheetTplMgrAction
class PictureDataSheetTplMgrAction extends AuditBaseAction
{

    public function doList () {
        $pictureDataSheetTpls = Dao::getEntityListByCond('PictureDataSheetTpl');
        XContext::setValue("pictureDataSheetTpls", $pictureDataSheetTpls);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $title = XRequest::getValue("title", "");
        $ename = XRequest::getValue("ename", '');
        $questiontitles = XRequest::getValue("questiontitles", "");

        DBC::requireNotEmpty($this->mydisease, "必须选疾病");

        $row = array();
        $row["diseaseid"] = $this->mydisease->id;
        $row["title"] = $title;
        $row["ename"] = $ename;
        $row["questiontitles"] = $questiontitles;

        PictureDataSheetTpl::createByBiz($row);

        XContext::setJumpPath("/picturedatasheettplmgr/list");
        return self::BLANK;
    }

    public function doModify () {
        $picturedatasheettplid = XRequest::getValue("picturedatasheettplid", 0);

        $picturedatasheettpl = PictureDataSheetTpl::getById($picturedatasheettplid);

        XContext::setValue("picturedatasheettpl", $picturedatasheettpl);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $picturedatasheettplid = XRequest::getValue("picturedatasheettplid", 0);
        $title = XRequest::getValue("title", "");
        $ename = XRequest::getValue("ename", '');
        $questiontitles = XRequest::getValue("questiontitles", "");

        $picturedatasheettpl = PictureDataSheetTpl::getById($picturedatasheettplid);
        $picturedatasheettpl->title = $title;
        $picturedatasheettpl->ename = $ename;
        $picturedatasheettpl->questiontitles = $questiontitles;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/picturedatasheettplmgr/modify?picturedatasheettplid=" . $picturedatasheettplid . "&preMsg=" . urlencode($preMsg));
        return self::BLANK;
    }
}
