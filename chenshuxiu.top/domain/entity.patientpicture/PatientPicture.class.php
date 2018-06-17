<?php

/*
 * PatientPicture
 */
class PatientPicture extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // 医生id
            'parent_patientpictureid',  // 父id
            'objtype',  // objtype
            'objid',  // objid
            'source_type',  // 来源类型
            'type',     // 类型
            'thedate',  // 日期
            'title',  // 图片标题
            'content',  // 大段备注文本
            'ocrjson', // ocrjson
            'ocrjson_back', // ocr返回的原始数据
            'status'); // 状态 0,未归档，1，已归档，2，无意义
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid');
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
        $this->_belongtos["parent_patientpicture"] = array(
            "type" => "PatientPicture",
            "key" => "parent_patientpictureid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["parent_patientpictureid"] = $parent_patientpictureid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["thedate"] = $thedate;
    // $row["title"] = $title;
    // $row["content"] = $content;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientPicture::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["parent_patientpictureid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["source_type"] = '';
        $default["type"] = 'not';
        $default["thedate"] = '';
        $default["title"] = '';
        $default["content"] = '';
        $default["ocrjson"] = '';
        $default['ocrjson_back'] = '';
        $default["status"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getTypeCode () {
        if (2 == $this->status) {
            return "meanless";
        }

        return $this->objtype;
    }

    public function getStatusDesc () {
        $arr = array(
            0 => "未归档",
            1 => "已归档",
            2 => "已归档"); // 无意义也是已归档的一种

        return $arr[$this->status];
    }

    public function getMainPatientPicture () {
        $patientpicture = $this;
        if ($this->parent_patientpictureid) {
            $patientpicture = $this->parent_patientpicture;
        }

        return $patientpicture;
    }

    public function getSubGroup () {
        $parent_patientpictureid = $this->getMainPatientPicture()->id;
        return PatientPictureDao::getListByParentid($parent_patientpictureid);
    }

    public function outGroup () {
        $mainpp = $this->getMainPatientPicture();
        $ppicgroup = $this->getSubGroup();
        $sheets = $this->getPictureDataSheets();

        if (empty($ppicgroup)) {
            foreach ($sheets as $sheet) {
                $sheet->remove();
            }
            return;
        }
        if ($mainpp->id === $this->id) {

            // 选出二当家
            $pp = $ppicgroup[0];

            foreach ($ppicgroup as $a) {
                if ($pp->id === $a->id) {
                    $a->parent_patientpictureid = 0;
                } else {
                    $a->parent_patientpictureid = $pp->parent_patientpictureid;
                }
                $a->title = $mainpp->title;
                $a->content = $mainpp->content;
                $a->thedate = $mainpp->thedate;
            }

            foreach ($sheets as $sheet) {
                $sheet->patientpictureid = $pp->id;
            }
        }

        $this->parent_patientpictureid = 0;
    }

    public function joinGroup ($patientpicture) {
        $mainpp = $this->getMainPatientPicture();
        $this->parent_patientpictureid = $mainpp->id;
        $this->title = $mainpp->title;
        $this->content = $mainpp->content;
        $this->thedate = $mainpp->thedate;
    }

    public function getPictureDataSheets () {
        $patientpicture = $this->getMainPatientPicture();
        return PictureDataSheetDao::getListByPatientpictureid($patientpicture->id);
    }

    public function getContent_brief () {
        $pictureDataSheets = $this->getPictureDataSheets();

        $content = '';

        foreach ($pictureDataSheets as $pictureDataSheet) {
            $temparr = $pictureDataSheet->getQaArr();
            foreach ($temparr as $pair) {
                $content .= $pair['q'] . " : " . $pair['a'] . "\n";
            }
        }

        return $content .= $this->content;
    }

    public function getSourceDesc () {
        $arr = array(
            'WxPicMsg' => "患者微信上传",
            'CheckupPicture' => "医生上传");

        return $arr[$this->source_type];
    }

    public function modifyStatus ($status) {
        Debug::trace("===fff==$status======");
        if ($status == 0) {
            $this->setToDo();
        } elseif ($status == 1) {
            $this->setDone();
        } else {
            $this->setDrop();
        }
    }

    public function setToDo () {
        $this->status = 0;
        $pps = $this->getSubGroup();
        foreach ($pps as $pp) {
            $pp->status = 0;
        }
        $this->getMainPatientPicture()->status = 0;
    }

    public function setDone () {
        $this->status = 1;
        $pps = $this->getSubGroup();
        foreach ($pps as $pp) {
            $pp->status = 1;
        }
        $this->getMainPatientPicture()->status = 1;

        $pps_new = PatientPictureDao::getListByObj($this->obj);
        foreach($pps_new as $pp){
            $pp->status = 1;
        }
    }

    public function setDrop () {
        $this->status = 2;
        $pps = $this->getSubGroup();
        foreach ($pps as $pp) {
            $pp->status = 2;
        }
        $this->getMainPatientPicture()->status = 2;
    }

    public function changeObj ($objtype) {
        $this->setToDo();

        if ($objtype == 'CheckupPicture' && $this->objtype == 'WxPicMsg') {
            $row = array();

            $row["wxuserid"] = $this->wxuserid;
            $row["userid"] = $this->userid;
            $row["patientid"] = $this->patientid;
            $row["doctorid"] = $this->doctor instanceof Doctor ? $this->doctorid : $this->patient->doctorid;
            $row["pictureid"] = $this->obj->pictureid;

            $checkuppicture = CheckupPicture::createByBiz($row);
            $this->objtype = "CheckupPicture";
            $this->objid = $checkuppicture->id;

            return;
        }

        if ($objtype == 'WxPicMsg' && $this->objtype == 'CheckupPicture') {
            $this->obj->remove();
            $this->objtype = "WxPicMsg";

            $cond = " and patientpictureid=:patientpictureid ";
            $bind = [
                ':patientpictureid' => $this->id];

            $wxpicmsg = Dao::getEntityByCond('WxPicMsg', $cond, $bind);
            if ($wxpicmsg instanceof WxPicMsg) {
                $this->objid = $wxpicmsg->id;
            }
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getTypes ($needAll = true) {
        $list = [];

        if ($needAll) {
            $list['all'] = '全部';
        }



        /*
        未标记
        诊断证明
        血常规
        生化检查
        X线
        CT
        超声检查
        核磁
        肿瘤标志物
        尿常规
        便常规
         */
        $list['not'] = '未标记';
        $list['zzzm'] = '诊断证明';
        $list['xcg'] = '血常规';
        $list['shjc'] = '生化检查';
        $list['xx'] = 'X线';
        $list['ct'] = 'CT';
        $list['csjc'] = '超声检查';
        $list['hc'] = '核磁';
        $list['zlbzw'] = '肿瘤标志物';
        $list['ncg'] = '尿常规';
        $list['bcg'] = '便常规';

        return $list;
    }

    public static function getInspectionReportTypes () {
        /*
        全部
        诊断证明
        血常规
        生化检查
        X线
        CT
        超声检查
        核磁
        肿瘤标志物
        尿常规
        便常规
         */
        $list['all'] = 'all';
        $list['xcg'] = '血常规';
        $list['shjc'] = '生化检查';
        $list['xx'] = 'X线';
        $list['ct'] = 'CT';
        $list['csjc'] = '超声检查';
        $list['hc'] = '核磁';
        $list['ncg'] = '尿常规';
        $list['bcg'] = '便常规';

        return $list;
    }
}
