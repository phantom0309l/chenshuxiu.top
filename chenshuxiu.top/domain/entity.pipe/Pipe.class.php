<?php

/*
 * Pipe 流管道
 */
class Pipe extends Entity
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
            'pipetplid',  // 流类型模板id
            'objtype',  // objtype
            'objid',  // objid
            'objcode',  // objcode
            'subdomain',  // subdomain
            'content');
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
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
        $this->_belongtos["pipetpl"] = array(
            "type" => "PipeTpl",
            "key" => "pipetplid");
        $this->_belongtos["obj"] = array(
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
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Pipe::createByBiz row cannot empty");

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
        $default["pipetplid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["objcode"] = '';
        $default["subdomain"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    public static function createByEntity (Entity $entity, $objcode = 'create', $wxuserid = 0) {
        XContext::setValue('EntityBase::__get-debug-close', true);
        // $wxuserid = 参数
        $userid = 0;
        $patientid = 0;
        $doctorid = 0;

        // 患者实体, 有可能为空
        $patient = null;

        // 先以参数为准, 否则取实体上的
        if ($wxuserid < 1) {
            try {
                $wxuserid = $entity->wxuserid;
            } catch (Exception $ex) {}
        }

        $user = null;
        try {
            $user = $entity->user;
        } catch (Exception $ex) {}

        // $entity->user 存在的情况
        if ($wxuserid < 1 && $user instanceof User) {
            $wxuserid = $user->getWxUserIdIfOnlyOne(); // 可能存在
            $userid = $user->id; // 肯定存在
            $patientid = $user->patientid; // 可能存在
        }

        // 正修
        if ($wxuserid > 0) {
            $wxuser = WxUser::getById($wxuserid);
            if ($wxuser instanceof WxUser) {
                $userid = $wxuser->userid;
                $patientid = $wxuser->user->patientid;
                $patient = $wxuser->user->patient;
            }
        } else {
            // 特殊修正
            if ($entity instanceof WxUser) {
                $wxuserid = $entity->id;
                $userid = $entity->userid;
                $patientid = $entity->user->patientid;
                $patient = $entity->user->patient;
            }
        }

        // 特殊修正, obj 是 Patient
        if ($entity instanceof Patient) {
            $patientid = $entity->id;
            $patient = $entity;
        }

        // 尝试 userid
        if ($userid < 1) {
            try {
                $userid = $entity->userid;
            } catch (Exception $ex) {}
        }

        // 尝试 patientid
        if ($patientid < 1) {
            try {
                $patientid = $entity->patientid;
                $patient = $entity->patient;
            } catch (Exception $ex) {}
        }

        // 尝试 doctorid
        try {
            $doctorid = $entity->doctorid;
        } catch (Exception $ex) {}

        if (false == $patient instanceof Patient && $patientid > 0) {
            $patient = Patient::getById($patientid);
        }

        // 反修正 wxuserid , userid, doctorid
        if ($patient instanceof Patient) {
            if ($userid < 1) {
                $userid = $patient->getUserIdIfOnlyOne();
            }

            if ($wxuserid < 1) {
                $wxuserid = $patient->getWxUserIdIfOnlyOne();
            }

            if ($doctorid < 1) {
                $doctorid = $patient->doctorid;
            }
        }

        $objtype = get_class($entity);
        $pipetpl = PipeTplDao::getOneByObjtypeAndObjcode($objtype, $objcode);

        $pipetplid = 0;
        if ($pipetpl instanceof PipeTpl) {
            $pipetplid = $pipetpl->id;
        }
        $subdomain = XContext::getValueEx("xdomain", "");

        $row = array();
        if ($objcode == "create") {
            $row["createtime"] = $entity->createtime;
        }
        $row["wxuserid"] = $wxuserid;
        $row["userid"] = $userid;
        $row["patientid"] = $patientid;
        $row["doctorid"] = $doctorid;
        $row["pipetplid"] = $pipetplid;
        $row["objtype"] = $objtype;
        $row["objid"] = $entity->id;
        $row["objcode"] = $objcode;
        $row["subdomain"] = $subdomain;
        $pipe = Pipe::createByBiz($row);

        if ($pipe->patient instanceof Patient && $pipe->isUserPipe()) {
            $pipe->patient->lastpipe_createtime = $pipe->createtime;
            $pipe->patient->lastpipeid = $pipe->id;
        }

        if ($pipe->wxuser instanceof WxUser && $pipe->isUserPipe()) {
            $pipe->wxuser->lastpipe_createtime = $pipe->createtime;
        }

        XContext::setValue('EntityBase::__get-debug-close', false);

        return $pipe;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 复写魔法方法
    public function __toString () {
        return "{$this->id},{$this->createtime},{$this->wxuserid},{$this->userid},{$this->patientid},{$this->doctorid},{$this->objtype},{$this->objid}";
    }

    // 流中显示
    public function isFlow () {
        // 随访暂时不需要实体
        if ($this->objtype == 'PatientFollow') {
            return true;
        }

        if (false == $this->obj instanceof Entity) {
            return false;
        }

        if ($this->objtype == 'WxPicMsg' && 0 == $this->obj->status) {
            return false;
        }

        if ($this->objtype == 'WxPicMsg' && 0 == $this->obj->pictureid) {
            // 修正为默认图/暂未图片
            // $this->obj->set4lock('pictureid',254847039);
            $this->obj->set4lock('pictureid', 445230749);
        }

        return true;
    }

    // 是不是发系统消息生成
    public function isFromSystem () {
        $obj = $this->obj;
        if ($obj instanceof PushMsg && $this->objcode == 'bySystem') {
            return true;
        } else {
            return null;
        }
    }

    // 是不是运营发消息生成
    public function isFromOps () {
        $obj = $this->obj;
        if ($obj instanceof PushMsg && $this->objcode == 'byAuditor') {
            return true;
        } else {
            return null;
        }
    }

    // 是否用户产生的
    public function isUserPipe () {
        return $this->subdomain == 'wx';
    }

    // 获取 pipetpl 的 title
    public function getPipeTplTitle () {
        $title = $this->pipetpl->title;
        if ($this->objtype == "LessonUserRef") {
            $title .= "：" . $this->obj->lesson->title;
        }
        if ($this->objtype == "Study") {
            $title .= "：" . $this->obj->studyplan->obj->title;
        }
        if ($this->objtype == "Paper") {
            $title .= "：" . $this->obj->papertpl->title;
        }
        if ($this->obj instanceof WxPicMsg) {
            $title = $this->obj->getTitleStr();
        }
        return $title;
    }

    //获取流的创建时间
    public function getPipeCreatetime () {
        $obj = $this->obj;
        $pipetpl = $this->pipetpl;
        $objcode = $pipetpl->objcode;
        if($obj instanceof ShopOrder && $objcode == "pay"){
            return $obj->time_pay;
        }else{
            return $this->createtime;
        }
    }

    // 获取用于回复的wxuser
    public function getWxUserForPushMsg () {
        $wxuser = $this->wxuser;
        if (false == $wxuser instanceof WxUser && $this->user instanceof User) {
            $wxuser = $this->user->getMasterWxUser();
        }

        if (false == $wxuser instanceof WxUser && $this->patient instanceof Patient) {
            $wxuser = $this->patient->getMasterWxUser();
        }

        return $wxuser;
    }

    public function getWriter () {
        $str = "";
        $obj = $this->obj;
        if ($obj) {
            if (method_exists($obj, 'getWriter')) {
                $str = $obj->getWriter();
            } else {
                $auditorid = 0;
                try {
                    $auditorid = $obj->auditorid;
                } catch (Exception $ex) {}

                $auditor = Auditor::getById($auditorid);
                if ($auditor instanceof Auditor) {
                    $str = $auditor->name;
                } else {
                    if ($obj instanceof WxPicMsg && "运营回复患者图片" == $obj->title) {
                        $str = "运营";
                    }
                }
            }
        }

        if ($str == "") {
            $str = $this->user->shipstr;
        }

        return $str;
    }

    // add by chenshigang@fangcunyisheng.com
    public function getIconClass () {
        $icon = "si si-info";
        switch ($this->objtype) {
            case "PushMsg":
            case "WxTxtMsg":
            case "WxUser":
                $icon = "si si-bubble";
                break;
            case "CourseUserRef":
                break;
            case "DrugSheet":
            case "DrugItem":
                $icon = "fa fa-medkit";
                break;
            case "LessonUserRef":
                break;
            case "Meeting":
            case "CdrMeeting":
                $icon = "si si-call-end";
                break;
            case "Paper":
            case "Checkup":
            case "XAnswerSheet":
                $icon = "fa fa-newspaper-o";
                break;
            case "Patient":
                break;
            case "PatientMedicineSheet":
            case "PatientMedicinePkg":
                $icon = "fa fa-medkit";
                break;
            case "PatientNote":
                break;
            case "PatientPgroupRef":
                break;
            case "PatientRemark":
                break;
            case "PmSideEffect":
                break;
            case "RevisitRecord":
                break;
            case "RevisitTkt":
                break;
            case "Study":
                break;
            case "User":
                break;
            case "WxOpMsg":
            case "WxPicMsg":
                $icon = "si si-picture";
                break;
            case "WxVoiceMsg":
                $icon = "si si-volume-2";
                break;
            case "BedTkt":
                $icon = "fa fa-bed";
                break;
            case "Chemo":
                $icon = "si si-chemistry";
                break;
            default:
                break;
        }
        return $icon;
    }

    public function canCreatePipeLevel () {
        $patient = $this->patient;
        $obj = $this->obj;
        if (false == $patient instanceof Patient) {
            return false;
        }

        // 无效患者、黑名单患者 返回false
        if( $patient->isDoubt() || $patient->isOnTheBlackList() ){
            return false;
        }

        // 礼来患者 返回false
        if( $patient->isInHezuo("Lilly")){
            return false;
        }

        // '开药门诊' 返回false
        if( $obj instanceof WxTxtMsg && '开药门诊' == $obj->content ){
            return false;
        }

        // 近两天有支付过的订单 返回false
        if( $patient->hasPayShopOrderNearlyDay(2)){
            return false;
        }
        return true;
    }

    // 判断本条流实体 是否可以被 添加感谢信
    public function canJoinLetter () {
        $objtypes = array(
            'WxTxtMsg',
            'PatientNote'
        );

        if(in_array(get_class($this->obj),$objtypes)){
            return true;
        }else {
            return false;
        }
    }

    public function canSendOcr () {
        $objTypes = array(
            'WxPicMsg',
        );

        if(in_array(get_class($this->obj),$objTypes)){
            return true;
        }else {
            return false;
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
