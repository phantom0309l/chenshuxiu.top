<?php

class WxTemplateMgrAction extends AuditBaseAction
{

    // 列表
    public function doList () {
        $wxshops = Dao::getEntityListByCond("WxShop");
        $wxtemplates = Dao::getEntityListByCond("WxTemplate");

        XContext::setValue("wxshops", $wxshops);
        XContext::setValue("wxtemplates", $wxtemplates);
        return self::SUCCESS;
    }

    // 新增
    public function doAdd () {
        return self::SUCCESS;
    }

    // 新建 提交
    public function doAddPost () {

        $wxshopid = XRequest::getValue("wxshopid", 1);
        $code = XRequest::getValue("code", "");
        $title = XRequest::getValue("title", "");
        $ename = XRequest::getValue("ename", "");
        $showkey = XRequest::getValue("showkey", "");
        $content = XRequest::getValue("content", "");

        if ($wxshopid && $code && $title && $ename) {
            $row = array();
            $row["wxshopid"] = $wxshopid;
            $row["code"] = $code;
            $row["title"] = $title;
            $row["ename"] = $ename;
            $row["showkey"] = $showkey;
            $row["content"] = $content;
            $wxtemplate = WxTemplate::createByBiz($row);
        }
        XContext::setJumpPath("/wxtemplatemgr/list");
        return self::SUCCESS;
    }

    // 修改
    public function doModify () {
        $wxtemplateid = XRequest::getValue('id', 0);
        $wxtemplate = WxTemplate::getById($wxtemplateid);

        XContext::setValue('wxtemplate', $wxtemplate);
        return self::SUCCESS;
    }

    // 修改 提交
    public function doModifyPost () {
        $wxtemplateid = XRequest::getValue("wxtemplateid", 0);
        $code = XRequest::getValue("code", "");
        $title = XRequest::getValue("title", "");
        $ename = XRequest::getValue("ename", "");
        $showkey = XRequest::getValue("showkey", "");
        $content = XRequest::getValue("content", "");

        $wxTemplate = WxTemplate::getById($wxtemplateid);
        $wxTemplate->code = $code;
        $wxTemplate->title = $title;
        $wxTemplate->ename = $ename;
        $wxTemplate->showkey = $showkey;
        $wxTemplate->content = $content;

        XContext::setJumpPath("/wxtemplatemgr/list");
        return self::SUCCESS;
    }

    // 发送模板消息页面
    public function doSend () {
        $mydisease = $this->mydisease;
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor = Doctor::getById($doctorid);
        $current_wxshopid = XRequest::getValue('current_wxshopid', 0);
        $current_wxtemplateid = XRequest::getValue('current_wxtemplateid', 0);

        $wxshops = WxShopDao::getAllList();
        $current_wxshop = WxShop::getById($current_wxshopid);
        if (false == $current_wxshop instanceof WxShop) {
            if ($mydisease instanceof Disease) {
                $current_wxshop = WxShopDao::getByDiseaseid($mydisease->id);
            } else {
                $current_wxshop = $wxshops[0];
            }
        }

        $wxtemplates = WxTemplateDao::getListByWxShopId($current_wxshop->id);
        $current_wxtemplate = WxTemplate::getById($current_wxtemplateid);
        if (false == $current_wxtemplate instanceof WxTemplate) {
            $current_wxtemplate = $wxtemplates[0];
        }

        $content_title_arr = WxApi::getWxTemplateContentTitleArr($current_wxtemplate->code, $current_wxshop->id);

        XContext::setValue('doctor', $doctor);
        XContext::setValue('current_wxshop', $current_wxshop);
        XContext::setValue('wxshops', $wxshops);
        XContext::setValue('current_wxtemplate', $current_wxtemplate);
        XContext::setValue('wxtemplates', $wxtemplates);
        XContext::setValue('content_title_arr', $content_title_arr);

        return self::SUCCESS;
    }

    // 发送模板消息
    public function doSendJson () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $current_wxshopid = XRequest::getValue('current_wxshopid', 0);
        $current_wxtemplateid = XRequest::getValue('current_wxtemplateid', 0);
        $first = XRequest::getValue('first', '');
        $keywords = XRequest::getValue('keywords', '');
        $remark = XRequest::getValue('remark', '');
        $url = XRequest::getValue('url', '');
        if ($url) {
            $url = urldecode($url);
        }

        $doctor = Doctor::getById($doctorid);
        $wxshop = WxShop::getById($current_wxshopid);
        $wxtemplate = WxTemplate::getById($current_wxtemplateid);
        $send_content = $this->getSendContent($first, $keywords, $remark);

        $this->sendTemplateMsg($doctor, $wxshop, $wxtemplate, $send_content, $url);
        echo "ok";
        return self::BLANK;
    }

    private function sendTemplateMsg ($doctor, $wxshop, $wxtemplate, $send_content, $url = "") {
        $myauditor = $this->myauditor;

        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select id
            from patients
            where doctorid = :doctorid and status = 1 and subscribe_cnt > 0 ";
        $bind = [];
        $bind[':doctorid'] = $doctor->id;

        $ids = Dao::queryValues($sql, $bind);
        $i = 0;
        foreach ($ids as $id) {
            $patient = Patient::getById($id);

            //将随访提醒模版消息中的xx替换为患者姓名
            $send_fix_content = $this->getSendFixContent($wxtemplate, $send_content, $patient);

            $wxusers = WxUserDao::getListByPatient($patient);
            foreach ($wxusers as $wxuser) {
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == $wxshop->id) {
                    PushMsgService::sendTplMsgToWxUserByAuditor($wxuser, $myauditor, $wxtemplate->ename, $send_fix_content, $url);
                }
            }
            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }
        $unitofwork->commitAndInit();
    }

    private function getSendFixContent($wxtemplate, $send_content, $patient){
        if($wxtemplate->ename == "followupNotice"){
            $content = str_replace("xx", $patient->name, $send_content);
        }else {
            $content = $send_content;
        }
        return $content;
    }

    private function getSendContent ($first, $keywords, $remark) {
        $first = array(
            "value" => $first,
            "color" => "#ff6600");

        $arr = array();
        foreach ($keywords as $a) {
            $temp = array();
            $temp["value"] = $a;
            $temp["color"] = "#666";
            $arr[] = $temp;
        }
        return WxTemplateService::createTemplateContent($first, $arr, $remark);
    }
}
