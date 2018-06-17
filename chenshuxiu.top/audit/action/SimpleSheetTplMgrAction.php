<?php
// SimpleSheetTplMgrAction
class SimpleSheetTplMgrAction extends AuditBaseAction
{

    public function doList () {
        $simplesheettpls = Dao::getEntityListByCond('SimpleSheetTpl');

        XContext::setValue('simplesheettpls', $simplesheettpls);

        return self::SUCCESS;
    }

    public function doOneShow () {
        $simplesheettplid = XRequest::getValue('simplesheettplid', 0);
        $simplesheettpl = SimpleSheetTpl::getById($simplesheettplid);
        DBC::requireNotEmpty($simplesheettpl, 'simplesheettpl is null');

        XContext::setValue('simplesheettpl', $simplesheettpl);

        return self::SUCCESS;
    }

    public function doAddOrModifyjson () {
        $simplesheettplid = XRequest::getValue('simplesheettplid', 0);
        $title = XRequest::getValue('title', '');
        $ename = XRequest::getValue('ename', '');
        $content = XRequest::getValue('content', '');
        $remark = XRequest::getValue('remark', '');

        $simplesheettpl_ename = SimpleSheetTplDao::getByEname($ename);

        if ($simplesheettplid) {
            $simplesheettpl = SimpleSheetTpl::getById($simplesheettplid);

            if ($simplesheettpl->ename != $ename && $simplesheettpl_ename instanceof SimpleSheetTpl) {
                echo "ename-already";

                return self::BLANK;
            }

            $simplesheettpl->title = $title;
            $simplesheettpl->ename = $ename;
            $simplesheettpl->content = $content;
            $simplesheettpl->remark = $remark;

            echo "modify-success";
        } else {
            if ($simplesheettpl_ename instanceof SimpleSheetTpl) {
                echo "ename-already";

                return self::BLANK;
            }

            $row = [];
            $row['title'] = $title;
            $row['ename'] = $ename;
            $row['content'] = $content;
            $row['createauditorid'] = $this->myauditor->id;
            $row['remark'] = $remark;

            SimpleSheetTpl::createByBiz($row);

            echo "add-success";
        }

        return self::BLANK;
    }

    public function doModify () {
        return self::SUCCESS;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }
}
