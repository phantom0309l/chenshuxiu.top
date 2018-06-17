<?php

class CronProcessMgrAction extends AuditBaseAction
{

    public function doList () {
        $cronprocesstplid = XRequest::getValue("cronprocesstplid", 0);
        $cronprocesstasktype = XRequest::getValue("cronprocesstasktype", 0);

        if ($cronprocesstplid != 0) {
            $cronprocesstpl = CronProcessTpl::getById($cronprocesstplid);
        } else {
            $cronprocesstpl = CronProcessTplDao::getByTasktype($cronprocesstasktype);
        }

        $cronprocesses = CronProcessDao::getListByTplidorTasktype($cronprocesstplid, $cronprocesstasktype);

        XContext::setValue("cronprocesstpl", $cronprocesstpl);
        XContext::setValue("cronprocesses", $cronprocesses);

        return self::SUCCESS;
    }

    public function doAdd () {
        $cronprocesstplid = XRequest::getValue("cronprocesstplid", 0);

        if ($cronprocesstplid == 0) {
            return self::BLANK;
        }
        $cronprocesstpl = CronProcessTpl::getById($cronprocesstplid);
        $cronprocesstplvars = CronProcessTplVarDao::getListByCronprocesstplid($cronprocesstplid);

        XContext::setValue("cronprocesstpl", $cronprocesstpl);
        XContext::setValue("cronprocesstplvars", $cronprocesstplvars);
        return self::SUCCESS;
    }

    public function doAddPost () {
        $cronprocesstplid = XRequest::getValue("cronprocesstplid", 0);
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");

        $title = XRequest::getValue("title", '');
        $pos = XRequest::getValue("pos", 0);
        $remark = XRequest::getValue("remark", '');
        $s = XRequest::getValue("s", '*');
        $m = XRequest::getValue("m", '*');
        $h = XRequest::getValue("h", '*');
        $dom = XRequest::getValue("dom", '*');
        $mon = XRequest::getValue("mon", '*');
        $dow = XRequest::getValue("dow", '*');

        $cronprocesstpl = CronProcessTpl::getById($cronprocesstplid);

        $row = array();
        $row['cronprocesstplid'] = $cronprocesstplid;
        $row['tasktype'] = $cronprocesstpl->tasktype;
        $row['diseaseid'] = $diseaseid;
        $row['title'] = $title;
        $row['pos'] = $pos;
        $row['s'] = $s;
        $row['m'] = $m;
        $row['h'] = $h;
        $row['dom'] = $dom;
        $row['mon'] = $mon;
        $row['dow'] = $dow;
        $row['status'] = 0;
        $row['remark'] = $remark;

        $cronprocess = CronProcess::createByBiz($row);

        $cronprocesstplvars = CronProcessTplVarDao::getListByCronprocesstplid($cronprocesstplid);

        foreach ($cronprocesstplvars as $cronprocesstplvar) {
            $tempvar = 'var_' . "{$cronprocesstplvar->code}";
            $var = XRequest::getValue("$tempvar", '');

            $row_var = array();
            $row_var["cronprocessid"] = $cronprocess->id;
            $row_var["cronprocesstplvarid"] = $cronprocesstplvar->id;
            $row_var["code"] = $cronprocesstplvar->code;
            $row_var["value"] = $var;
            $row_var["unit"] = $cronprocesstplvar->unit;
            $row_var["remark"] = $cronprocesstplvar->remark;

            $cronprocessvar = CronProcessVar::createByBiz($row_var);
        }

        XContext::setJumpPath("/cronprocessmgr/list?cronprocesstplid={$cronprocesstplid}");
        return self::BLANK;
    }

    public function doModify () {
        $cronprocessid = XRequest::getValue("cronprocessid", 0);

        $cronprocess = CronProcess::getById($cronprocessid);

        $cronprocesstpl = CronProcessTpl::getById($cronprocess->cronprocesstplid);

        $cronprocessvars = CronProcessVarDao::getListByCronprocessid($cronprocessid);

        XContext::setValue("cronprocess", $cronprocess);
        XContext::setValue("cronprocesstpl", $cronprocesstpl);
        XContext::setValue("cronprocessvars", $cronprocessvars);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $cronprocessid = XRequest::getValue("cronprocessid", 0);
        $title = XRequest::getValue("title", '');
        $pos = XRequest::getValue("pos", 0);
        $remark = XRequest::getValue("remark", '');
        $s = XRequest::getValue("s", '*');
        $m = XRequest::getValue("m", '*');
        $h = XRequest::getValue("h", '*');
        $dom = XRequest::getValue("dom", '*');
        $mon = XRequest::getValue("mon", '*');
        $dow = XRequest::getValue("dow", '*');

        $cronprocess = CronProcess::getById($cronprocessid);

        $cronprocess->title = $title;
        $cronprocess->pos = $pos;
        $cronprocess->remark = $remark;
        $cronprocess->s = $s;
        $cronprocess->m = $m;
        $cronprocess->h = $h;
        $cronprocess->dom = $dom;
        $cronprocess->mon = $mon;
        $cronprocess->dow = $dow;

        $cronprocessvars = CronProcessVarDao::getListByCronprocessid($cronprocessid);

        foreach ($cronprocessvars as $cronprocessvar) {
            $tempvar = 'var_' . "{$cronprocessvar->code}";
            $var = XRequest::getValue("$tempvar", '');

            $cronprocessvar->value = $var;

        }

        XContext::setJumpPath("/cronprocessmgr/list");
        return self::BLANK;
    }
}
