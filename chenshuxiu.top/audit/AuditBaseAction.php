<?php

class AuditBaseAction extends BaseAction
{

    protected $myauditor = null;

    protected $mydisease = null;

    public function __construct() {
        parent::__construct();

        $myuser = $this->myuser;
        if (false == $myuser instanceof User || false == $myuser->isAuditor()) {
            UrlFor::jump302(UrlFor::wwwLogin());
        }

        $myauditor = $myuser->getAuditor();

        $action = XRequest::getValue('action', '');
        $method = XRequest::getValue('method', '');

        if ('errormgr' != $action) {
            $auditresource = AuditResourceDao::getByActionMethod($action, $method);
            if ($auditresource instanceof AuditResource) {
                if ('' == $auditresource->auditroleids) {
                    $auditresource->auditroleids = "12,13,17";
                }
                if (false == $myauditor->isHasAuth($auditresource)) {
                    $error = "{$myauditor->name}当前没有权限访问{$action}/{$method}\n<br/>如果需要请联系技术配置权限，：）";
                    Debug::warn($error);
                    UrlFor::jump302("/errormgr/error?errmsg=" . urlencode("$error"));
                }
            } else {
                UrlFor::jump302("/errormgr/addresource?action_add={$action}&method_add={$method}");
            }
        }

        $this->myauditor = $myauditor;

        if ($myuser->isLoginTimeout()) {
            $this->clearMyUserIdCookie();
            UrlFor::jump302(UrlFor::wwwLogin());
        }

        if ($myauditor->isHasRole(array(
            'yunying'))) {
            XContext::setValue("needMaskName", false);
        } else {
            XContext::setValue("needMaskName", true);
        }

        XContext::setValue("myauditor", $myauditor);

        $mydisease = null;
        $mydiseaseid = XRequest::getValue('diseaseid', '') ? XRequest::getValue('diseaseid', '') : XCookie::get0("_diseaseid_");
        $mydiseaseid = intval($mydiseaseid);
        if ($mydiseaseid > 0) {
            $mydisease = Disease::getById($mydiseaseid);
            $this->mydisease = $mydisease;
        }

        if ($myuser->username == 'suifang301') {
            XContext::setJumpPath(UrlFor::suifangIndex());
        }

        XContext::setValue("mydisease", $mydisease);
        XContext::setValue("tpl", ROOT_TOP_PATH . "/audit/tpl");

    }

    public function getContextDiseaseidStr() {
        $diseaseids = [];
        if ($this->mydisease instanceof Disease) {
            $diseaseids[] = $this->mydisease->id;
        } else {
            $diseaseids = $this->myauditor->getDiseaseIdArr();
        }

        // 如果没有选择疾病，且运营未绑定疾病的话，就返回所有的疾病ids
        if (empty($diseaseids)) {
            $diseases = DiseaseDao::getListAll();
            foreach ($diseases as $disease) {
                $diseaseids[] = $disease->id;
            }
        }
        return implode(',', $diseaseids);
    }
}
