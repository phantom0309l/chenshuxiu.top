<?php

/*
 * Hospital 医院
 */
class Hospital extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'name',  // 医院名称
            'shortname',  // 医院名称
            'logo_pictureid',  // 医院logo图片
            'qr_logo_pictureid',  // 医院logo图片
            'levelstr',  // 等级
            'xprovinceid',  // 省id
            'xcityid',  // 市id
            'xcountyid',  // 区id
            'content',  // 详细地址
            'status', // 状态
            'can_public_zhengding', //能否配置正丁药品
            'old_hospitalid',
            'remark'
        );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["logo_picture"] = array(
            "type" => 'Picture',
            "key" => "logo_pictureid");

        $this->_belongtos["qr_logo_picture"] = array(
            "type" => 'Picture',
            "key" => "qr_logo_pictureid");

        $this->_belongtos["xprovince"] = array(
            "type" => "Xprovince",
            "key" => "xprovinceid");
        $this->_belongtos["xcity"] = array(
            "type" => "Xcity",
            "key" => "xcityid");
        $this->_belongtos["xcounty"] = array(
            "type" => "Xcounty",
            "key" => "xcountyid");
    }

    // $row = array();
    // $row["name"] = $name;
    // $row["shortname"] = $shortname;
    // $row["logo_pictureid"] = $logo_pictureid;
    // $row["qr_logo_pictureid"] = $qr_logo_pictureid;
    // $row["levelstr"] = $levelstr;
    // $default["xprovinceid"] = 0;
    // $default["xcityid"] = 0;
    // $default["xcountyid"] = 0;
    // $default["content"] = '';
    // $default["can_public_zhengding"] = 1;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Hospital::createByBiz row cannot empty");

        $default = array();
        $default["name"] = '';
        $default["shortname"] = '';
        $default["logo_pictureid"] = 0;
        $default["qr_logo_pictureid"] = 0;
        $default["levelstr"] = '';
        $default["xprovinceid"] = 0;
        $default["xcityid"] = 0;
        $default["xcountyid"] = 0;
        $default["content"] = '';
        $default["status"] = 1;
        $default["can_public_zhengding"] = 1;
        $default["old_hospitalid"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 是测试医院
    public function isTest () {
        return $this->id == 5;
    }

    public function getFixName ($doctorid = 0) {
        $name = $this->name;
        // 以科室为维度的医生，医院名字也应该为空
        $doctor = Doctor::getById($doctorid);
        if ($doctor instanceof Doctor && $doctor->hasPdoctor()) {
            return "";
        }
        // 广州市妇女儿童医疗中心 不希望出现医院名称
        if ($this->id == 22 || $this->id == 95) {
            return "";
        }
        return $name;
    }

    // 获取医生列表
    public function getDoctors () {
        return DoctorDao::getListByHospital($this->id);
    }

    public function getDoctorCnt () {
        return DoctorDao::getCntOfHospital($this->id);
    }

    public function getHospitalAddressStr () {
        $four = [110000, 120000, 310000, 500000];
        if (in_array($this->xprovinceid, $four)) {
            $xprovince_name = $this->xprovince->name;
            $xcity_name = "";
        } else {
            $xprovince_name = $this->xprovince->name;
            $xcity_name = $this->xcity->name;
        }
        $xcounty_name = $this->xcounty->name;
        $content = $this->content;

        return "{$xprovince_name}{$xcity_name}{$xcounty_name}{$content}";
    }

    public function canPublicZhengding(){
        return 1 == $this->can_public_zhengding;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
