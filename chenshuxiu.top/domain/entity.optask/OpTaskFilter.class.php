<?php

/*
 * OpTaskFilter
 */
class OpTaskFilter extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'title'    //过滤器标题, 未命名 是临时过滤器
        ,'filter_json'    //过滤器配置，json格式
        ,'is_public'    //0 私有, 1表示公开
        ,'create_auditorid'    //创建者
        ,'remark'    //备注
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'create_auditorid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        
    $this->_belongtos["create_auditor"] = array ("type" => "Auditor", "key" => "create_auditorid" );
    }

    // $row = array(); 
    // $row["title"] = $title;
    // $row["filter_json"] = $filter_json;
    // $row["is_public"] = $is_public;
    // $row["create_auditorid"] = $create_auditorid;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "OpTaskFilter::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["filter_json"] = '';
        $default["is_public"] =  0;
        $default["create_auditorid"] =  0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ------------ static method ---------
    // ====================================
    public static function getAllConfig () {
        $list = [
            'mgtplan',
            'baodaotime',
            'auditor',
            'doctorgroup',
            'doctor',
            'diseasegroup',
            'disease',
            'patientgroup',
            'patientstage',
            'optasktpl',
            'status',
            'plantime',
            'level',
        ];

        return $list;
    }

}
