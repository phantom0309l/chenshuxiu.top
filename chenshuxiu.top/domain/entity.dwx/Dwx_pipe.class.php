<?php
/*
 * Dwx_pipe
 */
class Dwx_pipe extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'doctorid',  // doctorid
            'assistantid',  // assistantid
            'relate_patientid',  // 内容相关的patientid, 备用
            'objtype',  // objtype, 备用
            'objid',  // objid, 备用
            'objcode',  // objcode, 备用
            'content'); // 备注

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'doctorid',
            'assistantid',
            'relate_patientid',
            'objtype',
            'objid',
            'objcode');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["assistant"] = array(
            "type" => "Assistant",
            "key" => "assistantid");
        $this->_belongtos["relate_patient"] = array(
            "type" => "Patient",
            "key" => "relate_patientid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["doctorid"] = $doctorid;
    // $row["assistantid"] = $assistantid;
    // $row["relate_patientid"] = $relate_patientid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["objcode"] = $objcode;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Dwx_pipe::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["doctorid"] = 0;
        $default["assistantid"] = 0;
        $default["relate_patientid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["objcode"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    public static function createByEntity (Entity $entity, $objcode = '') {

        $row = array();
        $row["wxuserid"] = $entity->wxuserid;
        $row["userid"] = $entity->userid;
        $row["doctorid"] = $entity->doctorid;
        $row["assistantid"] = $entity->assistantid;
        $row["relate_patientid"] = $entity->relate_patientid;
        $row["objtype"] = get_class($entity);
        $row["objid"] = $entity->id;
        $row["objcode"] = $objcode;

        return self::createByBiz($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================

}
