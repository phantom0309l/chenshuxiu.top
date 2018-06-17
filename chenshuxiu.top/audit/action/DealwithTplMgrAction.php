<?php

class DealwithTplMgrAction extends AuditBaseAction
{

    // 快捷回复模板列表
    public function dolist() {
        $diseasegroupid = XRequest::getValue("diseasegroupid", null);
        $diseaseid = XRequest::getValue("diseaseid", null);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $groupstr = XRequest::getValue("groupstr", null);

        $title = XRequest::getValue("title", "");

        $orderby = XRequest::getValue("orderby", 'diseasegroupid');

        $cond = " and diseasegroupid=0 or ( 1=1 ";
        $bind = [];

        $url = "/dealwithtplmgr/list?";

        if (false == is_null($diseasegroupid)) {
            $cond .= " and diseasegroupid = :diseasegroupid ";
            $bind[':diseasegroupid'] = $diseasegroupid;

            $url .= "&diseasegroupid={$diseasegroupid}";
        }

        if (false == is_null($diseaseid)) {
            $cond .= " and diseaseid = :diseaseid ";
            $bind[':diseaseid'] = $diseaseid;

            $url .= "&diseaseid={$diseaseid}";
        }

        if (false == is_null($doctorid)) {
            $cond .= " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;

            $url .= "&doctorid={$doctorid}";
        }

        if (false == is_null($groupstr)) {
            $cond .= " and groupstr = :groupstr ";
            $bind[':groupstr'] = $groupstr;

            $url .= "&groupstr={$groupstr}";
        }

        $cond .= " ) ";

        // 搜索覆盖其他条件
        if ($title) {
            $cond = " and title like :title ";
            $bind = [];
            $bind[':title'] = "%{$title}%";
        }

        // 排序
        if ($orderby == 'diseasegroupid') {
            $cond .= " order by diseasegroupid, diseaseid, groupstr, objtype, objid, title ";
        } elseif ($orderby == 'title') {
            $cond .= " order by title, diseasegroupid, diseaseid, groupstr, objtype, objid ";
        } elseif ($orderby == 'groupstr') {
            $cond .= " order by groupstr, diseasegroupid, diseaseid, objtype, objid, title ";
        } else {
            $cond .= " order by diseasegroupid, diseaseid, groupstr, objtype, objid, title ";
        }

        $dealwithtpls = Dao::getEntityListByCond('DealwithTpl', $cond, $bind);

        XContext::setValue('diseasegroupid', $diseasegroupid);
        XContext::setValue('diseaseid', $diseaseid);
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('groupstr', $groupstr);

        XContext::setValue('title', $title);

        XContext::setValue('orderby', $orderby);

        XContext::setValue('url', $url);

        XContext::setValue('dealwithtpls', $dealwithtpls);

        return self::SUCCESS;
    }

    // 快捷回复模板新建
    public function doAdd() {
        return self::SUCCESS;
    }

    public function doAddPost() {
        $diseasegroupid = XRequest::getValue("diseasegroupid", "");
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $doctorid = XRequest::getValue("doctorid", 0);
        $groupstr = XRequest::getValue("groupstr", '');

        $title = XRequest::getValue("title", "");
        $msgcontent = XRequest::getValue("msgcontent", "");
        $keywords = XRequest::getValue("keywords", "");

        // 疾病
        $disease = Disease::getById($diseaseid);
        if ($disease instanceof Disease) {
            $diseasegroupid = $disease->diseasegroupid;
        }

        $row = array();
        $row["diseasegroupid"] = $diseasegroupid;
        $row["diseaseid"] = $diseaseid;
        $row["doctorid"] = $doctorid;
        $row["groupstr"] = trim($groupstr);
        $row["title"] = $title;
        $row["msgcontent"] = $msgcontent;
        $row["keywords"] = $keywords;

        DealwithTpl::createByBiz($row);

        XContext::setJumpPath("/dealwithTplMgr/list");

        return self::SUCCESS;
    }

    // 快捷回复模板修改
    public function doModify() {
        $dealwithtplid = XRequest::getValue("dealwithtplid", 0);

        $dealwithtpl = DealwithTpl::getById($dealwithtplid);
        DBC::requireTrue($dealwithtpl instanceof DealwithTpl, "不存在:{$dealwithtplid}");

        XContext::setValue("dealwithtpl", $dealwithtpl);

        $doctors = [];
        if ($dealwithtpl->diseasegroupid > 0) {
            $doctors = DoctorDao::getListByDiseasegroupid($dealwithtpl->diseasegroupid);
        } elseif ($dealwithtpl->diseaseid > 0) {
            $doctors = DoctorDao::getListByDiseaseid($dealwithtpl->diseaseid);
        }

        XContext::setValue("doctors", $doctors);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost() {
        $dealwithtplid = XRequest::getValue("dealwithtplid", 0);

        $diseasegroupid = XRequest::getValue("diseasegroupid", "");
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $doctorid = XRequest::getValue("doctorid", 0);

        $groupstr = XRequest::getValue("groupstr", "");
        $title = XRequest::getValue("title", "");
        $msgcontent = XRequest::getValue("msgcontent", "");
        $keywords = XRequest::getValue("keywords", "");

        $dealwithtpl = DealwithTpl::getById($dealwithtplid);
        DBC::requireTrue($dealwithtpl instanceof DealwithTpl, "不存在:{$dealwithtplid}");

        // 疾病
        $disease = Disease::getById($diseaseid);
        if ($disease instanceof Disease) {
            $diseasegroupid = $disease->diseasegroupid;
        }

        $dealwithtpl->diseasegroupid = $diseasegroupid;
        $dealwithtpl->diseaseid = $diseaseid;
        $dealwithtpl->doctorid = $doctorid;

        $dealwithtpl->groupstr = trim($groupstr);
        $dealwithtpl->title = $title;
        $dealwithtpl->msgcontent = $msgcontent;
        $dealwithtpl->keywords = $keywords;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/dealwithtplmgr/modify?dealwithtplid=" . $dealwithtplid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doDeleteJson() {
        $dealwithtplid = XRequest::getValue("dealwithtplid", 0);

        if ($dealwithtplid && is_numeric($dealwithtplid)) {
            $dealwithtpl = DealwithTpl::getById($dealwithtplid);
            $dealwithtpl->remove();
            echo "ok";
        }
        return self::BLANK;
    }

    // SendcntJson
    public function doSendcntJson() {
        $dealwithtplid = XRequest::getValue("dealwithtplid", 0);

        if ($dealwithtplid) {
            $dealwithtpl = DealwithTpl::getById($dealwithtplid);
            if ($dealwithtpl instanceof DealwithTpl) {
                $dealwithtpl->sendcnt += 1;
            }
            echo "ok";
        }

        return self::BLANK;
    }

    // 通用
    public function doGetDealwithTplListJson() {
        $dealwith_group = XRequest::getValue("dealwith_group", '');

        list ($first, $second) = explode(':', $dealwith_group);

        $dealwithTpls = [];

        if ($dealwith_group == 'noselect') {
            $dealwithTpls = [];
        } elseif ($first == 'all') {
            list ($diseasegroupid, $orderby) = explode('|', $second);
            if ($orderby == 'title') {
                $dealwithTpls = DealwithTplDao::getDealwithTplListByDiseasegroupidAndCommon($diseasegroupid);
            } else {
                $dealwithTpls = DealwithTplService::getDealwithTpls_all_ByDiseasegroupid($diseasegroupid);
            }
        } elseif ($first == 'common') {
            $dealwithTpls = DealwithTplDao::getDealwithTplListByCommon();
        } elseif ($first == 'diseasegroup') {
            $dealwithTpls = DealwithTplDao::getDealwithTplListByDiseasegroupid($second);
        } elseif ($first == 'disease') {
            $dealwithTpls = DealwithTplDao::getDealwithTplListByDiseaseid($second);
        } elseif ($first == 'doctor') {
            $dealwithTpls = DealwithTplDao::getDealwithTplListByDoctorid($second);
        } elseif ($first == 'groupstr') {
            list ($groupstr, $diseasegroupid) = explode('|', $second);
            $dealwithTpls = DealwithTplDao::getDealwithTplListByGroupstr($groupstr, $diseasegroupid);
        }

        $i = 0;
        $arr = [];
        foreach ($dealwithTpls as $dealwithTpl) {

            if ($dealwithTpl instanceof DealwithTpl) {
                $i += 1;
                $arr[] = array(
                    "dealwithtplid" => $dealwithTpl->id,
                    "title" => sprintf("%02d", $i) . " " . $dealwithTpl->getTitleFix(),
                    "msgcontent" => $dealwithTpl->msgcontent);
            } else {
                $arr[] = array(
                    "dealwithtplid" => 0,
                    "title" => $dealwithTpl,
                    "msgcontent" => '');
            }
        }
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
        return self::blank;
    }

    public function doGetGroupstrs() {
        $diseasegroupid = XRequest::getValue("diseasegroupid", 0);
        DBC::requireTrue($diseasegroupid, "diseasegroupid为0");

        $groupstrarr = DealwithTplDao::getGroupstrArray($diseasegroupid);

        $groupstrs = HtmlCtr::getRadioCtrImp($groupstrarr, 'groupstrradio', '-1' , '', 'groupstrradio');

        $groupstrs .= "</br>";
        $groupstrs .= "分组：<input id='groupstr' type='text' name='groupstr'/>";

        $this->result['groupstrs'] = $groupstrs;
        return self::TEXTJSON;
    }
}
