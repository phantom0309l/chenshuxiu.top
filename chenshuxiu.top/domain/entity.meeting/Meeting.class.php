<?php
/*
 * Meeting 电话会议
 */
class Meeting extends Entity
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
            'auditorid',
            'appid',
            'callsid',
            'datecreated',
            'customersernum',
            'orderid',
            'userdata',
            'subid',
            'caller',
            'called',
            'starttime',
            'endtime',
            'duration',
            'begincalltime',
            'ringingbegintime',
            'ringingendtime',
            'byetype',
            'recordurl',
            'downloadstatus',
            'filename');
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'auditorid');
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
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row['wxuserid'] = $wxuserid;
    // $row['userid'] = $userid;
    // $row['patientid'] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row['auditorid'] = $auditorid;
    // $row['appid'] = $appid;
    // $row['callsid'] = $callsid;
    // $row['datecreated'] = 0;
    // $row['customersernum'] = 0;
    // $row['orderid'] = 0;
    // $row['userdata'] = $userdata;
    // $row['subid'] = $subid;
    // $row['caller'] = $caller;
    // $row['called'] = $called;
    // $row['starttime'] = $starttime;
    // $row['endtime'] = $endtime;
    // $row['duration'] = $duration;
    // $row['begincalltime'] = $begincalltime;
    // $row['ringingbegintime'] = $ringingbegintime;
    // $row['ringingendtime'] = $ringingendtime;
    // $row['byetype'] = $byetype;
    // $row['recordurl'] = $recordurl;
    // $row['downloadstatus'] = '';
    // $row['filename'] = '';
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, __METHOD__ . ' row cannot empty');

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
        $default['auditorid'] = 0;
        $default['appid'] = '';
        $default['callsid'] = '';
        $default['datecreated'] = 0;
        $default['customersernum'] = 0;
        $default['orderid'] = 0;
        $default['userdata'] = '';
        $default['subid'] = '';
        $default['caller'] = '';
        $default['called'] = '';
        $default['starttime'] = '';
        $default['endtime'] = '';
        $default['duration'] = '';
        $default['begincalltime'] = '';
        $default['ringingbegintime'] = '';
        $default['ringingendtime'] = '';
        $default['byetype'] = '';
        $default['recordurl'] = '';
        $default['downloadstatus'] = '';
        $default['filename'] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getContent () {
        return '电话会议';
    }

    public function formatStartTime () {
        return date('Y-m-d H:i:s', strtotime($this->starttime));
    }

    public function formatEndTime () {
        return date('Y-m-d H:i:s', strtotime($this->endtime));
    }

    public function formatDuration () {
        return $this->duration . '秒';
    }

    public function getVoiceUrl () {
        $voiceUri = Config::getConfig('voice_uri');
        return $voiceUri . '/' . $this->filename;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
