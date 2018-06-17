<?php
/*
 * AuditResource
 */
class AuditResource extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'type',  // page,json,...
            'title',  // 标题/名称
            'action',  // 小写 eg: patientmgr
            'method',  // 小写 eg: list
            'auditmenuid',  // 关联菜单id
            'auditroleids',  // 逗号分隔
            'diseasegroupid', //diseasegroupid
            'owner_auditorid',  // 负责人
            'content',  // 说明,手册
            'remark'); // 备注

    }

    protected function init_keys_lock () {}

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["auditmenu"] = array(
            "type" => "AuditMenu",
            "key" => "auditmenuid");

        $this->_belongtos["diseasegroup"] = array(
            "type" => "DiseaseGroup",
            "key" => "diseasegroupid");

        $this->_belongtos["owner_auditor"] = array(
            "type" => "Auditor",
            "key" => "owner_auditorid");
    }

    // $row = array();
    // $row["type"] = $type;
    // $row["title"] = $title;
    // $row["action"] = $action;
    // $row["method"] = $method;
    // $row["auditmenuid"] = $auditmenuid;
    // $row["auditroleids"] = $auditroleids;
    // $row["diseasegroupid"] = $diseasegroupid;
    // $row["owner_auditorid"] = $owner_auditorid;
    // $row["content"] = $content;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "AuditResource::createByBiz row cannot empty");

        $default = array();
        $default["type"] = '';
        $default["title"] = '';
        $default["action"] = '';
        $default["method"] = '';
        $default["auditmenuid"] = 0;
        $default["auditroleids"] = '';
        $default["diseasegroupid"] = 0;
        $default["owner_auditorid"] = 0;
        $default["content"] = '';
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getAuditRoleIdArr () {
        return explode(',', $this->auditroleids);
    }

    // 角色列表
    public function getAuditRoles () {
        $ids = $this->getAuditRoleIdArr();

        foreach ($ids as $k => $v) {
            if (empty($v)) {
                unset($ids[$k]);
            }
        }

        return Dao::getEntityListByIds('AuditRole', $ids);
    }

    // 角色字符串
    public function getAuditRolesStr ($separator = ',') {
        $arr = array();
        foreach ($this->getAuditRoles() as $a) {
            $arr[] = $a->name;
        }

        return implode($separator, $arr);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    public static function getTypeArr () {
        return array(
            'page' => 'page',
            'post' => 'post',
            'json' => 'json',
            'jsonHtml' => 'jsonHtml');
    }
}
