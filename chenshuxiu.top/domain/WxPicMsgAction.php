<?php

class WxPicMsgAction extends BaseAction
{

    // 构造函数，初始化了很多数据
    public function __construct () {
        parent::__construct();
    }

    // admin.com/wxpicmsg/addTagRefJson
    // 图片打标签
    // post 'admin/picture/add_tag', to: 'admin/showcase#add_tag’
    public function doaddTagRefJson () {

        $tagid = XRequest::getValue("tagid", 0);
        $wxpicmsgid = XRequest::getValue("wxpicmsgid", 0);

        $wxpicmsg = WxPicMsg::getById($wxpicmsgid);

        $tagRef = $wxpicmsg->getTagRefByTagid($tagid);
        if (false == $tagRef instanceof TagRef && $tagid > 0) {
            TagRef::createByEntity($wxpicmsg, $tagid);
        }

        echo "ok";

        return self::BLANK;
    }

    // admin.com/wxpicmsg/removeTagRefJson
    // 图片标签删除
    // post 'admin/picture/del_tag', to: 'admin/showcase#del_tag’
    public function doremoveTagRefJson () {

        $tagid = XRequest::getValue("tagid", 0);
        $wxpicmsgid = XRequest::getValue("wxpicmsgid", 0);

        $wxpicmsg = WxPicMsg::getById($wxpicmsgid);

        $tagRef = $wxpicmsg->getTagRefByTagid($tagid);
        if ($tagRef instanceof TagRef) {
            $tagRef->remove();
        }

        echo "ok";

        return self::BLANK;
    }

    // admin.com/wxpicmsg/destroyJson
    // 删除病历
    // delete 'admin/uploadcase/destroy', to: 'admin/uploadcase#destroy’
    public function dodestroyJson () {

        $this->doremoveJson();

        return self::BLANK;
    }

    // admin.com/wxpicmsg/removeJson
    // 图片删除
    // post 'admin/picture/del', to: 'admin/uploadcase#destroy’
    public function doremoveJson () {
        $wxpicmsgid = XRequest::getValue("wxpicmsgid", 0);
        $wxpicmsg = WxPicMsg::getById($wxpicmsgid);
        $wxpicmsg->status = 0;

        echo 'ok';

        return self::BLANK;
    }

    // admin.com/wxpicmsg/uploadcasePost
    // 上传病历 提交(由新图片上传代替)
    // post 'admin/uploadcase/create', to: 'admin/uploadcase#create’
    public function douploadcasePost () {
        $patientid = XRequest::getValue("patientid", 0);
        $pictureid = XRequest::getValue("pictureid", 0);

        $patient = Patient::getById($patientid);

        $pcard = $patient->getMasterPcard(); // done pcard fix

        Debug::warn("domain/WxPicMsgAction::douploadcasePost");

        $row = array();
        $row["patientid"] = $patientid;
        $row["doctorid"] = $pcard->doctorid; // done pcard fix
        $row["pictureid"] = $pictureid;
        $row["send_by_objtype"] = 'Auditor';
        $row["send_by_objid"] = 0;
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

        Debug::warn("domain/WxPicMsgAction::dobatuploadcasePost");

        foreach ($pictureids as $pictureid) {
            $row = array();
            $row["patientid"] = $patientid;
            $row["doctorid"] = $pcard->doctorid; // done pcard fix
            $row["pictureid"] = $pictureid;
            $row["send_by_objtype"] = 'Auditor';
            $row["send_by_objid"] = 0;
            $row["send_explain"] = 'medical_record';
            $wxPicMsg = WxPicMsg::createByBiz($row);
        }

        XContext::setJumpPath("/wxpicmsgmgr/list?patientid=" . $patientid);

        return self::BLANK;
    }
}
