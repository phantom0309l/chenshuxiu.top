<?php

class TagMgrAction extends AuditBaseAction
{

    // 列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $typestr = XRequest::getValue('typestr', "All");

        $cond = " ";
        $bind = [];

        if (strtolower($typestr) != "all") {
            $cond .= ' and typestr=:typestr ';
            $bind[':typestr'] = $typestr;
        }

        $tags = Dao::getEntityListByCond4Page("Tag", $pagesize, $pagenum, $cond, $bind);

        // 分页
        $countSql = "select count(*) as cnt from tags where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/tagmgr/list?typestr={$typestr}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("tags", $tags);
        XContext::setValue("typestr", $typestr);
        XContext::setValue("pagelink", $pagelink);
        return self::SUCCESS;
    }

    // 新增标签
    public function doAdd () {
        $typestr = XRequest::getValue('typestr', '');
        XContext::setValue("typestr", $typestr);
        return self::SUCCESS;
    }

    // 新增标签
    public function doAddPost () {

        $name = XRequest::getValue("name", '');
        $typestr = XRequest::getValue("typestr", 'WxPicMsg');

        $maxId = Dao::queryValue("select max(id) as maxid from tags");

        $row = array();
        $row["id"] = $maxId + 1;
        $row["typestr"] = $typestr;
        $row["name"] = $name;
        $tag = Tag::createByBiz($row);

        XContext::setJumpPath("/tagmgr/list");

        return self::SUCCESS;
    }

    // admin.com/tagmgr/addJson
    // 新增标签
    // post 'admin/case/add_tag', to: 'admin/case#add_tag'
    public function doAddJson () {

        $name = XRequest::getValue("name", '');
        $typestr = XRequest::getValue("typestr", 'WxPicMsg');

        $maxId = Dao::queryValue("select max(id) as maxid from tags");

        $row = array();
        $row["id"] = $maxId + 1;
        $row["typestr"] = $typestr;
        $row["name"] = $name;
        $tag = Tag::createByBiz($row);

        echo $tag->id;

        return self::BLANK;
    }

    // 修改标签
    public function doModify () {
        $tagid = XRequest::getValue('tagid', 0);

        $tag = Tag::getById($tagid);

        XContext::setValue('tag', $tag);

        return self::SUCCESS;
    }

    // 新增标签
    public function doModifyPost () {

        $tagid = XRequest::getValue('tagid', 0);
        $name = XRequest::getValue("name", '');
        $typestr = XRequest::getValue("typestr", 'WxPicMsg');

        $tag = Tag::getById($tagid);
        $tag->name = $name;
        $tag->typestr = $typestr;

        XContext::setJumpPath("/tagmgr/modify?tagid={$tagid}");

        return self::SUCCESS;
    }

    // 生成checkbox的html
    public function doCreateCheckboxHtml () {
        $patientid = XRequest::getValue('patientid',0);
        $typestr = XRequest::getValue('typestr','patientDiagnosis');
        $name = XRequest::getValue('name','patientDiagnosis');

        $patient = Patient::getById($patientid);
        DBC::requireTrue($patient instanceof Patient, '患者不存在');

        $patientDiagnosisArr = TagService::getTagArrByTypestr($typestr);
        $checked = TagService::getTagidsByObj($patient,$typestr);
        $checkboxStr = HtmlCtr::getCheckboxCtrImp4OneUi($patientDiagnosisArr,$name,$checked);
        XContext::setValue('checkboxStr',$checkboxStr);

        return self::SUCCESS;
    }
}
