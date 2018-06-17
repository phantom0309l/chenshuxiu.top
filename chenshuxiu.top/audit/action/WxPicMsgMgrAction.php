<?php

class WxPicMsgMgrAction extends AuditBaseAction
{

    // admin.com/wxpicmsgmgr/list?patientid=112
    // 病历列表 of 患者
    // get 'admin/showcase', to: 'admin/showcase#index’
    public function doList () {
        $patientid = XRequest::getValue("patientid", 0);
        $tagid = XRequest::getValue("tagid", 0);

        $tags = TagDao::getListByTypestr("WxPicMsg");
        $patient = Patient::getById($patientid);

        $wxpicmsgs = array();
        $cond = "";
        $bind = [];

        if ($tagid > 0) {

            if ($patientid) {
                $cond = " and a.patientid=:patientid ";
                $bind[':patientid'] = $patientid;
            }

            $sql = "select a.* from wxpicmsgs a
                inner join tagrefs b on a.id=b.objid and b.objtype='WxPicMsg'
                where b.tagid=:tagid and a.status=1 {$cond}
                order by a.id desc limit 100 ";

            $bind[':tagid'] = $tagid;
            $bind[':patientid'] = $patientid;

            $wxpicmsgs = Dao::loadEntityList("WxPicMsg", $sql, $bind);
        } else {
            if ($patientid) {
                $cond = " and patientid=:patientid ";
                $bind[':patientid'] = $patientid;
            }
            $cond .= " and status=1 ";
            $cond .= " order by id desc limit 100 ";

            $wxpicmsgs = Dao::getEntityListByCond("WxPicMsg", $cond, $bind);
        }

        XContext::setValue("tagid", $tagid);
        XContext::setValue("tags", $tags);
        XContext::setValue("patient", $patient);
        XContext::setValue("wxpicmsgs", $wxpicmsgs);
        return self::SUCCESS;
    }

    public function doListOfAll(){
        $pagesize = XRequest::getValue("pagesize", 100);
        $pagenum = XRequest::getValue("pagenum", 1);

        $diseaseid = XRequest::getValue("diseaseid", 0);

        $cond = '';
        $bind = [];

        if($diseaseid > 0){
            $cond .= " and b.diseaseid = :diseaseid ";
            $bind[":diseaseid"] = $diseaseid;
        }

        //获得实体
        $sql = "select distinct a.*
                    from wxpicmsgs a
                    left join patients b on b.id = a.patientid
                    where 1 = 1 {$cond} order by a.id desc";
        $wxpicmsgs = Dao::loadEntityList4Page("WxPicMsg", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("wxpicmsgs", $wxpicmsgs);

        //获得分页
        $countSql = "select count(distinct a.id)
                    from wxpicmsgs a
                    left join patients b on b.id = a.patientid
                    where 1 = 1 {$cond} order by a.id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/wxpicmsgmgr/listofall?diseaseid={$diseaseid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("diseaseid", $diseaseid);
        return self::SUCCESS;

    }

    // admin.com/wxpicmsgmgr/batuploadcase
    // 上传病历
    // get 'admin/uploadcase', to: 'admin/uploadcase#index’
    public function doBatUploadcase () {
        $patientid = XRequest::getValue("patientid", 0);

        $patient = Patient::getById($patientid);
        XContext::setValue("patient", $patient);

        return self::SUCCESS;
    }

    // admin.com/wxpicmsg/uploadcasePost
    // 上传病历 提交(由新图片上传代替)
    // post 'admin/uploadcase/create', to: 'admin/uploadcase#create’
    public function douploadcasePost () {
        $patientid = XRequest::getValue("patientid", 0);
        $pictureid = XRequest::getValue("pictureid", 0);

        $patient = Patient::getById($patientid);

        $pcard = $patient->getMasterPcard(); // done pcard fix

        $row = array();
        $row["patientid"] = $patientid;
        $row["doctorid"] = $pcard->doctorid; // done pcard fix
        $row["pictureid"] = $pictureid;
        $row["send_by_objtype"] = 'Auditor';
        $row["send_by_objid"] = $this->myauditor->id;
        $row["send_explain"] = 'medical_record';
        $wxPicMsg = WxPicMsg::createByBiz($row);

        XContext::setJumpPath("/wxpicmsgmgr/list?patientid=" . $patientid);

        return self::BLANK;
    }

    // admin.com/wxpicmsg/batuploadcasePost
    // 批量上传病历
    public function doBatuploadcasePost () {
        $patientid = XRequest::getValue("patientid", 0);
        $pictureids = XRequest::getValue("pictureids", []);

        $patient = Patient::getById($patientid);

        $pcard = $patient->getMasterPcard(); // done pcard fix

        foreach ($pictureids as $pictureid) {
            $row = array();
            $row["patientid"] = $patientid;
            $row["doctorid"] = $pcard->doctorid; // done pcard fix
            $row["pictureid"] = $pictureid;
            $row["send_by_objtype"] = 'Auditor';
            $row["send_by_objid"] = $this->myauditor->id;
            $row["send_explain"] = 'medical_record';
            $wxPicMsg = WxPicMsg::createByBiz($row);
        }

        XContext::setJumpPath("/wxpicmsgmgr/list?patientid=" . $patientid);

        return self::BLANK;
    }
}
