<?php

class FitPageTplItemMgrAction extends AuditBaseAction
{

    public function doList () {
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);

        $fitpagetpl = FitPageTpl::getById($fitpagetplid);
        $fitpagetplitems = $fitpagetpl->getFitPageTplItems();

        foreach ($fitpagetplitems as $i => $a) {
            $a->pos = $i + 1;
        }

        XContext::setValue('fitpagetpl', $fitpagetpl);
        XContext::setValue('fitpagetplitems', $fitpagetplitems);

        return self::SUCCESS;
    }

    public function doAdd () {
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);

        $fitpagetpl = FitPageTpl::getById($fitpagetplid);

        XContext::setValue('fitpagetpl', $fitpagetpl);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);
        $code = XRequest::getValue('code', '');
        $name = XRequest::getValue('name', '');
        $content = XRequest::getValue('content', '');
        $pos = XRequest::getValue('pos', 0);
        $remark = XRequest::getValue('remark', '');

        $row = array();
        $row['fitpagetplid'] = $fitpagetplid;
        $row['code'] = trim($code);
        $row['name'] = $name;
        $row['remark'] = $remark;

        $fitpagetplitem = FitPageTplItem::createByBiz($row);

        XContext::setJumpPath("/fitpagetplitemmgr/list?fitpagetplid={$fitpagetplid}");
    }

    public function doModify () {
        $fitpagetplitemid = XRequest::getValue('fitpagetplitemid', 0);

        $fitpagetplitem = FitPageTplItem::getById($fitpagetplitemid);

        XContext::setValue('fitpagetplitem', $fitpagetplitem);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $fitpagetplitemid = XRequest::getValue('fitpagetplitemid', 0);

        $fitpagetplitem = FitPageTplItem::getById($fitpagetplitemid);

        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);
        $name = XRequest::getValue('name', '');
        $content = XRequest::getValue('content', '');
        $pos = XRequest::getValue('pos', $fitpagetplitem->pos);
        $remark = XRequest::getValue('remark', '');

        $code = XRequest::getValue('code', $fitpagetplitem->code);

        $fitpagetplitem->set4lock('code', $code);
        $fitpagetplitem->name = $name;
        $fitpagetplitem->content = $content;
        $fitpagetplitem->pos = $pos;
        $fitpagetplitem->remark = $remark;

        XContext::setJumpPath("/fitpagetplitemmgr/list?fitpagetplid={$fitpagetplid}");
    }

    public function doDeleteJson () {
        $fitpagetplitemid = XRequest::getValue('fitpagetplitemid', 0);

        $fitpagetplitem = FitPageTplItem::getById($fitpagetplitemid);

        if ($fitpagetplitem->getFitPageItemCnt() > 0) {
            echo "失败";
        } else {
            $fitpagetplitem->remove();
            echo "成功";
        }

        return self::BLANK;
    }

    public function doPosModifyPost () {
        $posArr = XRequest::getValue('pos', []);
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);

        foreach ($posArr as $id => $pos) {
            $fitpagetplitem = FitPageTplItem::getById($id);
            $fitpagetplitem->pos = $pos;
        }

        XContext::setJumpPath("/fitpagetplitemmgr/list?fitpagetplid={$fitpagetplid}");
    }
}
