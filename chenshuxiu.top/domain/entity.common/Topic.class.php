<?php
/*
 * Topic
 */
class Topic extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'content',  //
            'likecnt',  //
            'objtype',  //
            'objid',  //
            'objcode',  //
            'lastactivetime',  //
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
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");

        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["content"] = $content;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Topic::createByBiz row cannot empty");

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
        $default["content"] = '';
        $default["likecnt"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["objcode"] = '';
        $default["lastactivetime"] = '0000-00-00 00:00:00';
        $default["status"] = 1;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getCommentList () {
        return CommentDao::getListByObjtypeObjid("Topic", $this->id);
    }

    public function getLikeCnt () {
        return LikeDao::getDingCnt("Topic", $this->id);
    }

    public function getLikeList () {
        return LikeDao::getDingList("Topic", $this->id);
    }

    public function getLikeNames () {
        $list = $this->getLikeList();
        $arr = array();
        foreach ($list as $a) {
            $wxuser = $a->wxuser;
            if ($wxuser instanceof WxUser) {
                $arr[] = $wxuser->getMaskNickname();
            }
        }
        return implode(",", $arr);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
