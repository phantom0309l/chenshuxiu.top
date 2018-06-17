<?php
/*
 * CourseUserRef
 */
class CourseUserRef extends Entity
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
            'courseid',  // courseid
            'pos',  // pos
            'begintime',  // begintime
            'endtime',  // endtime
            'status',  // status
            'entercnt'); // entercnt
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'doctorid',
            'courseid');
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

        $this->_belongtos["course"] = array(
            "type" => "Course",
            "key" => "courseid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["courseid"] = $courseid;
    // $row["entercnt"] = $entercnt;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CourseUserRef::createByBiz row cannot empty");

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
        $default["courseid"] = 0;
        $default["pos"] = 1;
        $default["begintime"] = '0000-00-00 00:00:00';
        $default["endtime"] = '0000-00-00 00:00:00';
        $default["status"] = 0;
        $default["entercnt"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function calcWriteHwkDays () {
        $sql = " select count(*) from (
        select count(*) from xanswersheets xas
        JOIN lessons ls on ls.hwkxquestionsheetid=xas.xquestionsheetid
        join lessonuserrefs lus on lus.lessonid=ls.id
        where xas.userid=:userid and lus.courseid=:courseid
        group by date_format(xas.createtime,'%Y-%m-%d')
        )tt ";
        $bind = array(
            ":userid" => $this->userid,
            ":courseid" => $this->courseid);

        return Dao::queryValue($sql, $bind);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 转换成 CourseArray
    public static function toCourseArray (array $refs) {
        $arr = array();
        foreach ($refs as $a) {
            $arr[] = $a->course;
        }

        return $arr;
    }

    // 转换成 UserArray
    public static function toUserArray (array $refs) {
        $arr = array();
        foreach ($refs as $a) {
            $arr[] = $a->user;
        }

        return $arr;
    }
}
