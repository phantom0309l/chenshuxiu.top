<?php
// Comment 评论

// owner by xxx
// create by sjp
// review by sjp 20160627
class Comment extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  //
            'userid',  //
            'patientid',  //
            'doctorid',  // doctorid
            'objtype',  //
            'objid',  //
            'typestr',  // 备注的子类型
            'title',  // 标题
            'content',  //
            'replycontent',  //
            'status'); //
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'objid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos['wxuser'] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos['user'] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos['patient'] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");

        $this->_belongtos['obj'] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["typestr"] = $typestr;
    // $row["title"] = $title;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Comment::createByBiz row cannot empty");

        if ($row["wxuserid"] == null) {
            $row["wxuserid"] = 0;
        }

        if ($row["userid"] == null) {
            $row["userid"] = 0;
        }

        if ($row["patientid"] == null) {
            $row["patientid"] = 0;
        }

        if ($row["doctorid"] == null) {
            $row["doctorid"] = 0;
        }

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["typestr"] = '';
        $default["title"] = '';
        $default["content"] = '';
        $default["replycontent"] = '';
        $default["status"] = 1;

        $row += $default;

        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 创建时间
    public function getTimeStr () {
        $activetimediff = XUtility::timeDiffString(XDateTime::valueOf($this->createtime), XDateTime::getNow());
        return $activetimediff;
    }

    // 评论时间
    public function getCreateDate () {
        $t = strtotime($this->createtime);

        return date("m月d日 H:i", $t);
    }

    // 让content只显示两行
    public function getContent2Row () {
        $content = mb_substr($this->content, 0, 37, 'utf-8');
        return $content . '...';
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 许喆测试 用于 crontask/*
    public static function addXuzheComment ($content) {
        $row = array(
            'typestr' => 'xuzhe',
            'content' => $content);

        return Comment::createByBiz($row);
    }
}
