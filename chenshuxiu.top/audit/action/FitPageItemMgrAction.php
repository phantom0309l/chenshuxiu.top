<?php

class FitPageItemMgrAction extends AuditBaseAction
{

    public function doList () {
        // 取模版库元素
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);

        $cond = " and fitpagetplid = :fitpagetplid order by pos";
        $bind = [];
        $bind[':fitpagetplid'] = $fitpagetplid;
        // 元素库
        $fitpagetplitems = Dao::getEntityListByCond('FitPageTplItem', $cond, $bind);
        $ids = [];
        foreach ($fitpagetplitems as $a) {
            $ids[] = $a->id;
        }
        $fitpagetplitemidstr = implode('|', $ids);

        // 取实例元素
        $fitpageid = XRequest::getValue('fitpageid', 0);
        $fitpage = FitPage::getById($fitpageid);

        $cond = " and fitpageid = :fitpageid order by pos";
        $bind = [];
        $bind[':fitpageid'] = $fitpageid;

        $fitpageitems = Dao::getEntityListByCond('FitPageItem', $cond, $bind);

        $ids = [];
        foreach ($fitpageitems as $a) {
            $ids[] = $a->fitpagetplitemid;
        }
        $fitpageitemidstr = implode('|', $ids);

        $ismusts = [];
        foreach ($fitpageitems as $a) {
            $ismusts[] = $a->fitpagetplitemid . '-' . $a->ismust;
        }
        $ismuststr = implode('|', $ismusts);
        // 去除元素库中在实例元素中存在的元素
        // foreach ($fitpagetplitems as $i => $tplitem) {
        // foreach ($fitpageitems as $item) {
        // if ($tplitem->id == $item->fitpagetplitemid) {
        // unset($fitpagetplitems[$i]);
        // }
        // }
        // }

        // 处理序号
        $i = 0;
        foreach ($fitpagetplitems as $a) {
            $i ++;
            $a->pos = $i;
        }

        XContext::setValue('fitpagetplid', $fitpagetplid);
        XContext::setValue('fitpage', $fitpage);
        XContext::setValue('fitpagetplitems', $fitpagetplitems);
        XContext::setValue('fitpageitems', $fitpageitems);
        XContext::setValue('fitpageitemidstr', $fitpageitemidstr);
        XContext::setValue('fitpagetplitemidstr', $fitpagetplitemidstr);
        XContext::setValue('ismuststr', $ismuststr);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $fitpagetplitemid = XRequest::getValue('fitpagetplitemid', 0);
        $fitpageid = XRequest::getValue('fitpageid', 0);

        $fitpagetplitem = FitPageTplItem::getById($fitpagetplitemid);
        $fitpage = FitPage::getById($fitpageid);

        // 判断是否已经存在该元素
        $fitpageitem = FitPageItemDao::getBy2Id($fitpageid, $fitpagetplitemid);
        if (false == $fitpageitem instanceof FitPageItem) {
            $row = array();
            $row['fitpageid'] = $fitpageid;
            $row['fitpagetplitemid'] = $fitpagetplitem->id;
            $row['code'] = $fitpagetplitem->code;
            $row['content'] = $fitpagetplitem->content;
            $row['pos'] = $fitpagetplitem->pos;
            $row['remark'] = $fitpagetplitem - remark;

            $fitpageitem = FitPageItem::createByBiz($row);
        }

        XContext::setValue('fitpage', $fitpage);
        XContext::setJumpPath("/fitpageitemmgr/list?fitpagetplid={$fitpagetplitem->fitpagetplid}&fitpageid={$fitpageid}");
        return self::SUCCESS;
    }

    public function doModify () {
        $fitpageitemid = XRequest::getValue('fitpageitemid', 0);

        $fitpageitem = FitPageItem::getById($fitpageitemid);

        XContext::setValue('fitpageitem', $fitpageitem);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $fitpageitemid = XRequest::getValue('fitpageitemid', 0);
        $content = XRequest::getValue('content', '');

        $fitpageitem = FitPageItem::getById($fitpageitemid);

        $fitpageitem->content = $content;

        XContext::setJumpPath("/fitpageitemmgr/list?fitpagetplid={$fitpageitem->fitpage->fitpagetplid}&fitpageid={$fitpageitem->fitpageid}");
    }

    public function doDeletePost () {
        $fitpageitemid = XRequest::getValue('fitpageitemid', 0);
        $fitpageid = XRequest::getValue('fitpageid', 0);
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);

        $fitpageitem = FitPageItem::getById($fitpageitemid);
        $fitpageitem->remove();

        $myauditor = $this->myauditor;

        $code = $fitpageitem->fitpagetplitem->code;
        $fitpage = FitPage::getById($fitpageid);
        $doctor = $fitpage->doctor;
        $doctor_name = "";
        if ($doctor instanceof Doctor) {
            $doctor_name = $doctor->name;
        }
        Debug::warn("[{$myauditor->name}] 删除了一条 FitPageItem[{$fitpageitem->id}] of [{$code}] [{$doctor_name}]");

        XContext::setJumpPath("/fitpageitemmgr/list?fitpagetplid={$fitpagetplid}&fitpageid={$fitpageid}");
        return self::SUCCESS;
    }

    public function doPosModifyPost () {
        $fitpageid = XRequest::getValue('fitpageid', 0);
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);

        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $fitpageitemid => $pos) {
            $fitpageitem = FitPageItem::getById($fitpageitemid);
            $fitpageitem->pos = $pos;
        }

        XContext::setJumpPath("/fitpageitemmgr/list?fitpagetplid={$fitpagetplid}&fitpageid={$fitpageid}");
        return self::BLANK;
    }

    public function doConfigPost () {
        $fitpageid = XRequest::getValue('fitpageid', 0);
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);

        $poss = XRequest::getValue('pos', []);
        $tplids = XRequest::getValue('tplid', []);
        $ismusts = XRequest::getValue('ismust', []);

        $list = [
            'pos' => $poss,
            'tplids' => $tplids,
            'ismusts' => $ismusts];

        $fitpage = FitPage::getById($fitpageid);
        $fitpageitems = FitPageItemDao::getListByFitPage($fitpage);
        foreach ($fitpageitems as $a) {
            $a->remove();
        }

        foreach ($tplids as $fitpagetplitemid) {
            $row = [
                'fitpageid' => $fitpageid,
                'fitpagetplitemid' => $fitpagetplitemid,
                'ismust' => $ismusts["{$fitpagetplitemid}"] ?? 1,
                'pos' => $poss["{$fitpagetplitemid}"]];

            $fitpageitem = FitPageItem::createByBiz($row);
        }

        // 修改序号
        foreach ($poss as $fitpagetplitemid => $pos) {
            $fitpagetplitem = FitPageTplItem::getById($fitpagetplitemid);
            $fitpagetplitem->pos = $pos;
        }

        XContext::setJumpPath("/fitpageitemmgr/list?fitpagetplid={$fitpagetplid}&fitpageid={$fitpageid}");
        return self::BLANK;
    }
}
