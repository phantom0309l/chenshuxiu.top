<?php

class FitPageMgrAction extends AuditBaseAction
{

    public function doList () {
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);

        $cond = " and fitpagetplid = :fitpagetplid order by id";
        $bind = [];
        $bind[':fitpagetplid'] = $fitpagetplid;

        $fitpagetpl = FitPageTpl::getById($fitpagetplid);
        $fitpages = Dao::getEntityListByCond('FitPage', $cond, $bind);

        XContext::setValue('fitpagetpl', $fitpagetpl);
        XContext::setValue('fitpages', $fitpages);

        return self::SUCCESS;
    }

    public function doAdd () {
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);
        $diseaseid = XRequest::getValue('diseaseid', 0);

        if ($diseaseid != 0) {
            $doctorCtrArr = CtrHelper::getDoctorCtrArray($diseaseid);
            XContext::setValue('doctorCtrArr', $doctorCtrArr);
        }
        $fitpagetpl = FitPageTpl::getById($fitpagetplid);

        XContext::setValue('fitpagetpl', $fitpagetpl);
        XContext::setValue('diseaseid', $diseaseid);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);
        $diseaseid = XRequest::getValue('diseaseid', 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");
        $doctorid = XRequest::getValue('doctorid', 0);
        $remark = XRequest::getValue('remark', '');

        $fitpagetpl = FitPageTpl::getById($fitpagetplid);

        $row = array();
        $row['fitpagetplid'] = $fitpagetplid;
        $row['code'] = $fitpagetpl->code;
        $row['diseaseid'] = $diseaseid;
        $row['doctorid'] = $doctorid;
        $row['remark'] = $remark;

        $fitpage = FitPage::createByBiz($row);

        XContext::setJumpPath("/fitpagemgr/list?fitpagetplid={$fitpagetplid}");
    }

    public function doDeleteJson () {
        $fitpageid = XRequest::getValue('fitpageid', 0);

        $fitpage = FitPage::getById($fitpageid);

        if ($fitpage->getFitPageItemCnt() > 0) {
            echo "失败";
        } else {
            $fitpage->remove();
            echo "成功";
        }

        return self::BLANK;
    }

}
