<?php

/*
 * AuditMenu
 */
class AuditMenu extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'parentmenuid',  // 父菜单id
            'title',  // title
            'url',  // 链接
            'auditresourceid',  // auditresourceid
            'pos'); // 序号
    }

    protected function init_keys_lock () {}

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["parentmenu"] = array(
            "type" => "AuditMenu",
            "key" => "parentmenuid");

        $this->_belongtos["auditresource"] = array(
            "type" => "AuditResource",
            "key" => "auditresourceid");
    }

    // $row = array();
    // $row["parentmenuid"] = $parentmenuid;
    // $row["title"] = $title;
    // $row["url"] = $url;
    // $row["auditresourceid"] = $auditresourceid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "AuditMenu::createByBiz row cannot empty");

        $default = array();
        $default["parentmenuid"] = 0;
        $default["title"] = '';
        $default["url"] = '';
        $default["auditresourceid"] = 0;
        $default["pos"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getSubMenuListByAuditor (Auditor $auditor) {
        $auditroleidarr = $auditor->getAuditRoleIdArr();
        $subMenus = AuditMenuDao::getListByParentmenuid($this->id);
        $ret = [];
        foreach ($subMenus as $a) {
            if($auditor->canVisitAuditResource($a->auditresource)){
                $ret[] = $a;
            }
        }
        return $ret;
    }

    public function getParentArr () {
        $parentarr = array();
        $parentarr[0] = '无父级';

        $menus = AuditMenuDao::getParentList();
        foreach ($menus as $a) {
            $parentarr["{$a->id}"] = $a->title;
        }

        return $parentarr;
    }

    public function getAuditResourceList () {
        return AuditResourceDao::getListByAuditmenuid($this->id);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
