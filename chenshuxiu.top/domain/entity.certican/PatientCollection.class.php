<?php

/*
 * PatientCollection
 */
class PatientCollection extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
             'wxuserid'    //wxuserid
            ,'userid'    //userid
            ,'patientid'    //patientid
            ,'type'    //类型
            ,'json_content'    //json
            ,'is_fill'  // 是否填写
        );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid' ,'userid' ,'patientid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

    $this->_belongtos["wxuser"] = array ("type" => "WxUser", "key" => "wxuserid" );
    $this->_belongtos["user"] = array ("type" => "User", "key" => "userid" );
    $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["type"] = $type;
    // $row["json_content"] = $json_content;
    // $row["is_fill"] = $is_fill;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientCollection::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] =  0;
        $default["userid"] =  0;
        $default["patientid"] =  0;
        $default["type"] = '';
        $default["json_content"] = '';
        $default["is_fill"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    // 填写状态
    public function getFillStatus () {
        $data = json_decode($this->json_content, true);

        if ($this->type == 'treet_diagnose') {
            if (array_key_exists('step3', $data)) {
                return 'finish';
            } elseif (array_key_exists('step2', $data)) {
                return 'step3';
            } elseif (array_key_exists('step1', $data)) {
                return 'step2';
            } else {
                return 'step1';
            }
        } elseif ($this->type == 'medicine_check') {
            if (array_key_exists('step2', $data)) {
                return 'finish';
            } elseif (array_key_exists('step1', $data)) {
                return 'step2';
            } else {
                return 'step1';
            }
        }
    }

    public function getBasicPictures () {
        return BasicPictureDao::getListByObj($this);
    }
}
