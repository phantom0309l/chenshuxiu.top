<?php

/*
 * OpTaskTpl
 */
class OpTaskTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return [
            'code',  // 类型
            'subcode',  // 子类型
            'objtype',  // 对应实体类型
            'diseaseids',  // diseaseids,逗号分隔
            'title',  // 标题
            'content',  // 内容
            'status',   // 状态
            'is_can_handcreate', //是否能手动创建任务 0:不能 1:能
            'is_auto_send', //是否 自动发送消息 0：不发送 1：发送
            'is_auto_to_opnode', // 是否自动进入节点
            'auto_to_opnode_daycnt',    // 自动进入节点的天数
            'auto_to_opnode_code'  // 自动进入的节点
        ];
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["code"] = $code;
    // $row["subcode"] = $subcode;
    // $row["objtype"] = $objtype;
    // $row["diseaseids"] = $diseaseids;
    // $row["title"] = $title;
    // $row["content"] = $content;
    // $row["status"] = $status;
    // $row["is_can_handcreate"] = $is_can_handcreate;
    // $row["is_auto_send"] = $is_auto_send;
    // $row["is_auto_to_opnode"] = $is_auto_to_opnode;
    // $row["auto_to_opnode_daycnt"] = $auto_to_opnode_daycnt;
    // $row["auto_to_opnode_code"] = $auto_to_opnode_code;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "OpTaskTpl::createByBiz row cannot empty");

        $default = array();
        $default["objtype"] = '';
        $default["code"] = '';
        $default["subcode"] = '';
        $default["diseaseids"] = '';
        $default["title"] = '';
        $default["content"] = '';
        $default["status"] = 0;
        $default["is_can_handcreate"] = 0;
        $default["is_auto_send"] = 0;
        $default["is_auto_to_opnode"] = 0;
        $default["auto_to_opnode_daycnt"] = 0;
        $default["auto_to_opnode_code"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 默认任务模板
    public function isDefault_optasktpl () {
        return 'default_optasktpl' == $this->subcode;
    }

    // 获取统一码 unicode
    public function getUnicode () {
        return "{$this->code}:{$this->subcode}";
    }

    public function isClosed () {
        return 0 == $this->status;
    }

    public function getDiseaseIdArr () {
        return explode(',', $this->diseaseids);
    }

    public function getTypestr () {
        $code = $this->code;
        $subcode = $this->subcode;
        $objtype = $this->objtype;

        // 向前兼容
        if (empty($subcode)) {
            $subcode = $objtype;
        }

        if ($code == "follow" && $subcode != 'Report') {
            return $code;
        }

        $arr = [];

        if ($code) {
            $arr[] = $code;
        }

        if ($subcode) {
            $arr[] = $subcode;
        }

        return implode("_", $arr);
    }

    public function getOpNodeList () {
        return OpNodeDao::getListByOpTaskTpl($this);
    }

    public function getOpNodeCnt () {
        $opnodes = OpNodeDao::getListByOpTaskTpl($this);

        return count($opnodes);
    }

    public function getRptData () {
        $sql = "select a.optasktplid
            , count(*) as cnt
            , sum(if(a.status=0, 1, 0)) as cnt_0
            , sum(if(a.status=1, 1, 0)) as cnt_1
            , sum(if(a.status=2, 1, 0)) as cnt_2
            , left(min(a.createtime), 10) as min_date
            , left(max(a.createtime), 10) as max_date
            from optasks a
            where optasktplid = :optasktplid";
        $bind = [];
        $bind[':optasktplid'] = $this->id;

        return Dao::queryRow($sql, $bind);
    }

    // diseaseid是否在optasktpl的diseaseids中
    public function isInOpTaskTplDiseaseids ($diseaseid) {
        // optasktpl的diseaseids为0表示全疾病通用
        if (!$this->diseaseids) {
            return true;
        }
        
        $diseaseids = explode(',', $this->diseaseids);

        return in_array($diseaseid, $diseaseids);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================


    public static function getDayCnts () {
        $list = [];
        for ($i = 1; $i <= 30; $i++) {
            $list["{$i}"] = $i;
        }

        return $list;
    }
}
