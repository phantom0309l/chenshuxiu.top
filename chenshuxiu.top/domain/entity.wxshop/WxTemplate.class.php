<?php
// 名称: 微信模板消息模板类, 微信称之为: 模板消息（业务通知）
// 备注: 微信模板消息用于在wxuser未取消订阅,但已失活的情况下(用户和公众号产生特定动作的交互48小时后),仍能送达通知消息;
// 目前主要用几个模板消息:
// 诊后医嘱提醒(doctornotice),随访提醒(followupNotice),管理员通知(adminNotice,方寸儿童管理服务平台,方寸课堂,脱髓鞘病)
// 其他的模板消息很少使用
class WxTemplate extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxshopid',  // wxshopid
            'code',  // 微信模板id
            'title',  // 模板名称
            'ename',  // 模板名称英文名称
            'showkey',  // 用于展示的key
            'content'); // 模板显示格式

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxshopid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxshop"] = array(
            "type" => "WxShop",
            "key" => "wxshopid");
    }

    // $row = array();
    // $row["wxshopid"] = $wxshopid;
    // $row["code"] = $code;
    // $row["title"] = $title;
    // $row["ename"] = $ename;
    // $row["showkey"] = $showkey;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "WxTemplate::createByBiz row cannot empty");

        $default = array();
        $default["wxshopid"] = 0;
        $default["code"] = '';
        $default["title"] = '';
        $default["ename"] = '';
        $default["showkey"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getContentOfAdminNoticeOrFollowupNotice ($patient, $title, $content) {
        $ename = $this->ename;
        if ('adminNotice' == $ename) {
            return WxTemplateService::getSendContent($ename, "", [$title, $content]);
        }

        if ('followupNotice' == $ename) {
            if($patient instanceof Patient){
                $patient_name = $patient->name;
            }else {
                $patient_name = '';
            }
            return WxTemplateService::getSendContent($ename, $title, [$patient_name, date("Y-m-d H:i:s"), $content]);
        }

        return "";
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    // 获取该微信user使用的某模板消息id
    public static function getTemplateid ($wxuser, $ename) {
        $tplid = "";
        $wxtemplate = WxTemplateDao::getByEname($wxuser->wxshopid, $ename);
        if (false == $wxtemplate instanceof WxTemplate) {
            Debug::warn(__METHOD__ . "{$wxuser->id} => {$wxuser->wxshopid}没有找到{$ename}的模板");
            return "";
        }
        $tplid = $wxtemplate->code;
        return $tplid;
    }
}
