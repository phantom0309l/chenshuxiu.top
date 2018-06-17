<?php

// CommonWordMgrAction
class CommonWordMgrAction extends AuditBaseAction
{

    // 医生常用词列表
    public function doList()
    {
        $doctorid = XRequest::getValue("doctorid", 0);

        $cond = '';
        $bind = [];
        if ($doctorid) {
            $cond .= " and (ownerid in (
            select id
            from patientremarktpls
            where doctorid=:doctorid
            ) or ownerid in (
            select id
            from doctors
            where id=:doctorid
            ))";
            $bind[":doctorid"] = $doctorid;
        }

        $commonwords = Dao::getEntityListByCond('CommonWord', $cond, $bind);
        XContext::setValue('commonwords', $commonwords);
        XContext::setValue('doctorid', $doctorid);
        return self::SUCCESS;
    }

    // 医生常用词新建
    public function doAdd()
    {
        $doctorid = XRequest::getValue("doctorid", 0);
        $prts = PatientRemarkTplDao::getListByDoctorid($doctorid);
        $typestrs = CommonWordDao::getAllTypestr();
        $groupstrs = CommonWordDao::getAllGroupstr();

        $prtarr = array();
        foreach ($prts as $prt) {
            $prtarr[$prt->id] = $prt->name;
        }

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('prtarr', $prtarr);
        XContext::setValue('typestrs', $typestrs);
        XContext::setValue('groupstrs', $groupstrs);
        return self::SUCCESS;
    }

    // 医生常用词批量新建
    public function doMultiAdd()
    {
        $doctorid = XRequest::getValue("doctorid", 0);
        $prts = PatientRemarkTplDao::getListByDoctorid($doctorid);
        $typestrs = CommonWordDao::getAllTypestr();
        $groupstrs = CommonWordDao::getAllGroupstr();

        $prtarr = array();
        foreach ($prts as $prt) {
            $prtarr[$prt->id] = $prt->name;
        }

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('prtarr', $prtarr);
        XContext::setValue('typestrs', $typestrs);
        XContext::setValue('groupstrs', $groupstrs);
        return self::SUCCESS;
    }

    public function doMultiAddPost()
    {
        $doctorid = XRequest::getValue("doctorid", 0);
        $prtid = XRequest::getValue("prtid", 0);
        $typestr = XRequest::getValue("typestr", '');
        $groupstr = XRequest::getValue("groupstr", '');
        $contents = XRequest::getValue("contents", []);
        $weights = XRequest::getValue("weights", []);

        $row = array();

        if ($typestr == 'diagnosis') {
            $row['ownertype'] = 'Doctor';
            $row['ownerid'] = $doctorid;
        } else {
            $patientremarktpl = PatientRemarkTpl::getById($prtid);

            if (false == $patientremarktpl instanceof PatientRemarkTpl) {
                XContext::setJumpPath("/commonwordmgr/add?doctorid={$doctorid}&preMsg=" . urlencode("添加失败，检查是否存在相应医生的内容分类"));
                return self::BLANK;
            }

            $row['ownertype'] = 'PatientRemarkTpl';
            $row['ownerid'] = $patientremarktpl->id;
        }

        $row['typestr'] = $typestr;
        $row['groupstr'] = $groupstr;
        for ($i = 0; $i < count($contents); $i++) {
            $content = $contents[$i];
            if ($content == "") {
                continue;
            }
            $row['content'] = $content;
            $row['weight'] = $weights[$i];
            $commonword = CommonWord::createByBiz($row);
        }

        XContext::setJumpPath("/commonwordmgr/multiadd?doctorid={$doctorid}&preMsg=" . urlencode("批量添加已保存"));
        return self::BLANK;
    }

    public function doAddPost()
    {
        $doctorid = XRequest::getValue("doctorid", 0);
        $prtid = XRequest::getValue("prtid", 0);
        $typestr = XRequest::getValue("typestr", '');
        $groupstr = XRequest::getValue("groupstr", '');
        $content = XRequest::getValue("content", '');
        $weight = XRequest::getValue("weight", 10);

        $row = array();

        if ($typestr == 'diagnosis') {
            $row['ownertype'] = 'Doctor';
            $row['ownerid'] = $doctorid;
        } else {
            $patientremarktpl = PatientRemarkTpl::getById($prtid);

            if (false == $patientremarktpl instanceof PatientRemarkTpl) {
                XContext::setJumpPath("/commonwordmgr/add?doctorid={$doctorid}&preMsg=" . urlencode("添加失败，检查是否存在相应医生的内容分类"));
                return self::BLANK;
            }

            $row['ownertype'] = 'PatientRemarkTpl';
            $row['ownerid'] = $patientremarktpl->id;
        }

        $row['typestr'] = $typestr;
        $row['groupstr'] = $groupstr;
        $row['content'] = $content;
        $row['weight'] = $weight;

        $commonword = CommonWord::createByBiz($row);

        XContext::setJumpPath("/commonwordmgr/add?doctorid={$doctorid}&preMsg=" . urlencode("{$commonword->content}添加已保存"));
        return self::BLANK;
    }

    // 医生常用词修改
    public function doModify()
    {
        $commonwordid = XRequest::getValue("commonwordid", 0);
        $commonword = CommonWord::getById($commonwordid);
        $typestr = XRequest::getValue('typestr', $commonword->typestr);

        $prts = array();
        if ($typestr !== 'diagnosis') {
            $prts = PatientRemarkTplDao::getListByDoctorid($commonword->owner->doctorid);
        }
        $typestrs = CommonWordDao::getAllTypestr();
        $groupstrs = CommonWordDao::getAllGroupstr();

        $prtarr = array();
        foreach ($prts as $prt) {
            $prtarr[$prt->id] = $prt->name;
        }

        XContext::setValue('prtarr', $prtarr);
        XContext::setValue('typestrs', $typestrs);
        XContext::setValue('groupstrs', $groupstrs);

        XContext::setValue('commonword', $commonword);
        return self::SUCCESS;
    }

    public function doModifyPost()
    {
        $commonwordid = XRequest::getValue("commonwordid", 0);
        $prtid = XRequest::getValue("prtid", 0);
        $typestr = XRequest::getValue("typestr", '');
        $doctorid = XRequest::getValue("doctorid", 0);
        $groupstr = XRequest::getValue("groupstr", '');
        $content = XRequest::getValue("content", '');
        $weight = XRequest::getValue("weight", 10);

        $commonword = CommonWord::getById($commonwordid);

        if ($typestr == 'diagnosis') {
            $commonword->ownertype = 'Doctor';
            $commonword->set4lock('ownerid', $doctorid);
        } else {
            $patientremarktpl = PatientRemarkTpl::getById($prtid);

            if (false == $patientremarktpl instanceof PatientRemarkTpl) {
                XContext::setJumpPath("/commonwordmgr/modify?commonwordid={$commonword->id}&preMsg=" . urlencode("修改失败，检查是否存在相应医生的内容分类"));
                return self::BLANK;
            }

            $commonword->ownertype = 'PatientRemarkTpl';
            $commonword->set4lock('ownerid', $patientremarktpl->id);
        }

        $commonword->typestr = $typestr;
        $commonword->groupstr = $groupstr;
        $commonword->content = $content;
        $commonword->weight = $weight;

        XContext::setJumpPath("/commonwordmgr/modify?commonwordid={$commonword->id}&preMsg=" . urlencode("{$commonword->content}修改已保存"));
        return self::BLANK;
    }

    public function doDeletePost()
    {
        $commonwordid = XRequest::getValue("commonwordid", 0);
        $commonword = CommonWord::getById($commonwordid);

        $premsg = "{$commonword->typestr},{$commonword->groupstr},{$commonword->content}已删除";

        $commonword->remove();

        XContext::setJumpPath("/commonwordmgr/list?preMsg=" . urlencode($premsg));
        return self::BLANK;
    }
}
