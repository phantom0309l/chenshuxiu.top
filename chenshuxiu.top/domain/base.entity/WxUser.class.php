<?php

/*
 * WxUser
 */
class WxUser extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'woy',  // week of year
            'userid',  // userid
            'patientid',  // patientid
            'wxshopid',  // wxshopid
            'openid',  // 用户的标识，对当前公众号唯一
            'unionid',  // 只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
            'wx_ref_code',  // 扫描二维码
            'nickname',  // 昵称
            'sex',  // 性别, 值为1时是男性，值为2时是女性，值为0时是未知
            'language',  // 语言
            'headimgurl',  // 头像
            'headimgpictureid',  // 存储id
            'subscribe',  // 状态
            'subscribe_time',  // 订阅时间
            'unsubscribe_time',  // 退订时间
            'city',  // 城市
            'province',  // 省
            'country',  // 国家
            'groupid',  // 用户所在的分组ID
            'remark',  // 公众号备注
            'lastpipe_createtime',  // 最后一次用户行为时间
            'ref_pcode',  // ref_pcode
            'ref_objtype',  // ref_objtype
            'ref_objid',  // ref_objid
            'doctorid',  // 医生id
            'is_ops', // 运营监控消息是否推送
            'is_alk',   //是否为ALK项目
        );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'userid',
            'patientid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxshop"] = array(
            "type" => "WxShop",
            "key" => "wxshopid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["headimgpicture"] = array(
            "type" => "Picture",
            "key" => "headimgpictureid");
    }

    // $row = array();
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["wxshopid"] = $wxshopid;
    // $row["openid"] = $openid;
    // $row["unionid"] = $unionid;
    // $row["wx_ref_code"] = $wx_ref_code;
    // $row["nickname"] = $nickname;
    // $row["sex"] = $sex;
    // $row["language"] = $language;
    // $row["headimgurl"] = $headimgurl;
    // $row["subscribe"] = $subscribe;
    // $row["subscribe_time"] = $subscribe_time;
    // $row["unsubscribe_time"] = $unsubscribe_time;
    // $row["city"] = $city;
    // $row["province"] = $province;
    // $row["country"] = $country;
    // $row["groupid"] = $groupid;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "WxUser::createByBiz row cannot empty");

        if ($row["patientid"] == null) {
            $row["patientid"] = 0;
        }

        if ($row["doctorid"] == null) {
            $row["doctorid"] = 0;
        }

        $default = array();
        $default["woy"] = XDateTime::getWFromFirstDate(date('Y-m-d'));
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["wxshopid"] = 1;
        $default["openid"] = '';
        $default["unionid"] = '';
        $default["wx_ref_code"] = '';
        $default["nickname"] = '';
        $default["sex"] = 0;
        $default["language"] = '';
        $default["headimgurl"] = '';
        $default["headimgpictureid"] = 0;
        $default["subscribe"] = 0;
        $default["subscribe_time"] = '0000-00-00 00:00:00';
        $default["unsubscribe_time"] = '0000-00-00 00:00:00';
        $default["city"] = '';
        $default["province"] = '';
        $default["country"] = '';
        $default["groupid"] = '';
        $default["remark"] = '';
        $default["lastpipe_createtime"] = '';
        $default["ref_pcode"] = '';
        $default["ref_objtype"] = '';
        $default["ref_objid"] = 0;
        $default["doctorid"] = 0;
        $default["is_ops"] = 0;
        $default["is_alk"] = 0;

        $row += $default;
        $entity = new self($row);
        return $entity;
    }

    // createByOpenid
    public static function getOrCreateByOpenid ($openid, $wxshopid = 1, $eventKey = "") {
        if (empty($openid)) {
            return null;
        }

        // 避免重复创建
        $wxuser = WxUserDao::getByOpenid($openid);
        if ($wxuser instanceof WxUser) {
            $wxuser->subscribe = 1;

            return $wxuser;
        }

        $row = array();
        $row["userid"] = 0;
        $row["patientid"] = 0;
        $row["wxshopid"] = $wxshopid;
        $row["openid"] = $openid;
        $row["subscribe"] = 1;
        $row["subscribe_time"] = XDateTime::now();
        if ($eventKey && substr($eventKey, 0, 8) == 'qrscene_') {
            $wx_ref_code = substr($eventKey, 8);
            $doctor = DoctorDao::getByCode($wx_ref_code);
            if ($doctor instanceof Doctor) {
                $row['ref_pcode'] = 'DoctorCard';
                $row['ref_objtype'] = 'Doctor';
                $row['ref_objid'] = $doctor->id;
                $row['doctorid'] = $doctor->id;
            }
        }

        $wxuser = self::createByBiz($row);
        Pipe::createByEntity($wxuser, "create", $wxuser->id);

        return $wxuser;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 扫了 钱英 码的wxuser
    public function isScanQianYing () {
        if (100764855 == $this->id) {
            return false;
        }
        if ($this->wx_ref_code == 'QY_TEST' || $this->wx_ref_code == 'ADHD_6_qianying') {
            return true;
        } else {
            return false;
        }
    }

    // 获取5个基本id wxuserid, userid, patientid, doctorid, diseaseid
    public function get5id () {
        $wxuserid = $this->id;
        $userid = 0;
        $patientid = 0;
        $doctorid = 0;
        $diseaseid = 0;

        $user = $this->user;
        $userid = $user->id;

        $patient = $user->patient;
        if ($patient instanceof Patient) {
            $patientid = $patient->id;
            $doctorid = $patient->doctorid;
            $diseaseid = $patient->diseaseid;
        }

        $row = array();
        $row["wxuserid"] = $wxuserid;
        $row["userid"] = $userid;
        $row["patientid"] = $patientid;
        $row["doctorid"] = $doctorid;
        $row["diseaseid"] = $diseaseid;
        return $row;
    }

    // 修改头像
    public function setHeadimgurl ($value) {
        $url = $this->headimgurl;
        if ($url != $value) {
            $this->headimgpictureid = 0;
        }

        $this->set4lock('headimgurl', $value);
    }

    // 强制重新抓取头像
    public function fetchHeadImgPicture () {
        $headimgurl = $this->headimgurl;

        if (! empty($headimgurl)) {
            $picture = Picture::createByFetch($headimgurl);
            if ($picture instanceof Picture) {
                $this->headimgpictureid = $picture->id;
            }
        }
    }

    // 获取头像url,如果没有本地图片,用微信原图
    public function getHeadImgPictureSrc ($width = 0, $height = 0, $iscut = false) {
        if ($this->headimgpicture instanceof Picture) {
            $url = $this->headimgpicture->getSrc($width, $height, $iscut);
        } elseif ($width > 0 && $width < 132) {
            $url = $this->headimgurl;
            $url = ($url == "") ? "" : substr($url, 0, strlen($url) - 1) . "132";
        }
        return $url;
    }

    public function modifySubscribeInfo () {
        $this->subscribe = 1;
        $this->subscribe_time = XDateTime::now();
    }

    // 修正 wxuser->userid
    public function fixUserId ($userid) {
        if (empty($userid)) {
            $userid = 0;
        }
        $this->set4lock('userid', $userid);
    }

    // 修正 wxuser->patientid
    public function fixPatientId ($patientid) {
        if (empty($patientid)) {
            $patientid = 0;
        }
        $this->set4lock('patientid', $patientid);
    }

    public function getMaskNickname () {
        $nameFirst = mb_substr($this->nickname, 0, 1, 'utf-8');
        return $nameFirst . "**";
    }

    // 性别
    public function getSexStr () {
        return XConst::Sex($this->sex);
    }

    // 获取疾病和医生 20170419 by sjp
    public function getDiseaseAndDoctor () {
        $wxuser = $this;

        $diseaseid = 0;
        $doctorid = 0;

        // 尝试获取 pcard
        $pcard = null;
        $patient = $wxuser->user->patient;
        if ($patient instanceof Patient) {
            $pcard = $patient->getOnePcardByWxshopid($wxuser->wxshopid);
            if ($pcard instanceof Pcard) {
                $diseaseid = $pcard->diseaseid;
                $doctorid = $pcard->doctorid;
            }
        }

        // 没有 patient 或没有在本服务号下的 pcard , 看扫码医生
        if ($doctorid < 1) {
            $doctor = $wxuser->doctor;
            if ($doctor instanceof Doctor) {
                $doctorid = $doctor->id;

                // 如果医生只关联一个疾病
                $disease = $doctor->getDiseaseIfOnlyOne();
                if ($disease instanceof Disease) {
                    $diseaseid = $disease->id;
                }
            }
        }

        // 没办法, 取服务号主疾病
        if ($diseaseid < 1) {
            $diseaseid = $wxuser->wxshop->diseaseid;
        }

        $arr = [];
        $arr[] = Disease::getById($diseaseid);
        $arr[] = Doctor::getById($doctorid);

        return $arr;
    }

    // 向微信服务器获取最新的用户资料
    public function updateInfo_safe () {
        FUtil::safeGuardNtimes(function  () {
            WxApi::fetchWxUser($this);
            return (bool) $this->unionid;
        }, 3);
        return true;
    }
    // 向微信服务器获取最新的用户资料
    public function updateInfo () {
        return WxApi::fetchWxUser($this);
    }

    // 保存
    public function setWx_ref_code ($key) {
        if (empty($key)) {
            $this->_cols['wx_ref_code'] = '';
            return;
        }

        preg_match("/[^\d]/", $key, $matches);

        if (! empty($matches)) {
            $doctor = DoctorDao::getByCode($key);
            $patient = $this->user->patient;
            if ($doctor instanceof Doctor) {
                if ($doctor->id == 343) { // wangqianRA 343 转到 wangqian 32
                    $doctor = Doctor::getById(32);
                    $key = $doctor->code;
                }

                if ($doctor->id == 942) { // 942 朱家德 转到 刘盛 941
                    $doctor = Doctor::getById(941);
                    $key = $doctor->code;
                }

                // 新扫的医生的码,修改
                $this->_cols['wx_ref_code'] = $key;

                $ref_pcode = 'DoctorCard';
                $ref_objtype = 'Doctor';
                $ref_objid = $doctor->id;

                // 修正 doctorid
                $doctorid = $doctor->id;
                if ($doctor->hasPdoctor()) {
                    $doctorid = $doctor->pdoctorid;
                }

                $old_doctor_name = $this->doctor->name;
                // 4136 宣武医院胸外科除外，不设为主治医生
                if ($doctorid != 824) {
                    $this->doctorid = $doctorid;
                }
                $diseaseid = $this->getDiseaseidIfOnlyOne();

                if ($patient instanceof Patient) {
                    PushMsgService::sendMsgToAuditorBySystem('WxUser', $this->wxshop->id, "[扫码] {$old_doctor_name} 医生的患者 {$patient->name} 扫了 {$doctor->name} 的二维码");

                    // #4136 宣武医院胸外科除外，不设为主治医生
                    if ($doctorid != 824) {
                        $patient->set4lock("doctorid", $doctorid);

                        // todo txj
                        // 因为患者扫码后，有些没选疾病，导致生成的optask上的diseaseid为0，所以没选疾病的时候，diseaseid不变
                        if ($diseaseid) {
                            $patient->set4lock("diseaseid", $diseaseid);
                        }
                    }

                    // 如果 pcard 不存在,则创建一个
                    $pcard = PcardDao::getByPatientidDoctorid($patient->id, $doctorid);
                    if (false == $pcard instanceof Pcard) {
                        $row = array();
                        // #4136 宣武医院胸外科除外，不设为主治医生
                        if ($doctorid == 824) {
                            $row["last_scan_time"] = "0000-00-00 00:00:00";
                        } else {
                            $row["last_scan_time"] = XDateTime::now();
                        }
                        $row["create_patientid"] = $patient->id;
                        $row["patientid"] = $patient->id;
                        $row["doctorid"] = $doctorid;
                        $row["diseaseid"] = ($diseaseid > 0) ? $diseaseid : $patient->diseaseid;
                        $row["patient_name"] = $patient->name;
                        $pcard = Pcard::createByBiz($row);
                    } else {
                        // todo txj
                        // ，因为患者扫码后，有些没选疾病，导致生成的optask上的diseaseid为0，所以没选疾病的时候，diseaseid不变
                        if ($diseaseid) {
                            $pcard->set4lock("diseaseid", $diseaseid);
                        }elseif($pcard->diseaseid < 1) {
                            // 应该走不到这里才对
                            $pcard->set4lock("diseaseid", $patient->diseaseid);
                        }
                        // 4136 宣武医院胸外科除外，不设为主治医生
                        if ($doctorid == 824) {
                            $pcard->last_scan_time = "0000-00-00 00:00:00";
                        } else {
                            $pcard->last_scan_time = XDateTime::now();
                        }
                    }
                    XContext::setValue("mypcard", $pcard);
                }
            } else {
                Debug::warn("code 没有找到对应医生，code[{$key}]");
                if ($this->doctorid == 0 && $this->ref_pcode != 'DoctorCard' && substr($key, 0, 8) == 'Auditor_') {

                    // 新扫的市场人员的推广码,且没扫过医生的码,修改
                    $this->_cols['wx_ref_code'] = $key;

                    $ref_pcode = 'AuditorCard';
                    $ref_objtype = 'Auditor';
                    $ref_objid = substr($key, 8);

                    $auditor = Auditor::getById($ref_objid);
                    PushMsgService::sendMsgToAuditorBySystem('WxUser', $this->wxshop->id, "[扫码] {$auditor->name} 推广了一个微信用户[{$this->nickname}]");
                } elseif (substr($key, 0, 8) == 'Auditor_') {
                    $auditor = Auditor::getById(substr($key, 8));
                    PushMsgService::sendMsgToAuditorBySystem('WxUser', $this->wxshop->id, "[扫码] {$auditor->name} 推广了一个微信用户[{$this->nickname}] , 但是这个微信已扫码关注了 {$this->doctor->name}");
                } else {
                    PushMsgService::sendMsgToAuditorBySystem('WxUser', $this->wxshop->id, "[扫码] {$this->doctor->name} 医生的患者 {$patient->name} 扫了一个不存在医生（失效）的二维码 错误的key 为 {$key}");
                }
            }
        } else {
            $qrcode = WxQrcode::getByScene_id($key);
            $ref_pcode = $qrcode->pcode;
            $ref_objtype = $qrcode->objtype;
            $ref_objid = $qrcode->objid;
        }

        // TODO by sjp: 原来没扫过医生码 或 新扫的医生码,覆盖
        if ($this->ref_pcode != 'DoctorCard' || $ref_pcode == 'DoctorCard') {
            $this->ref_pcode = $ref_pcode ? $ref_pcode : $this->ref_pcode;
            $this->ref_objtype = $ref_objtype ? $ref_objtype : $this->ref_objtype;
            $this->ref_objid = $ref_objid ? $ref_objid : $this->ref_objid;
        }
    }

    public function getDiseaseidIfOnlyOne () {
        $diseaseid = 0;

        // 医生只绑了一个疾病
        $doctor = $this->doctor;
        if ($doctor instanceof Doctor) {
            $disease = $doctor->getDiseaseIfOnlyOne();
            if ($disease instanceof Disease) {
                return $disease->id;
            }
        }

        // 用户扫了医生专属疾病二维码
        $wx_ref_code = $this->wx_ref_code;
        $arr = explode(":", $wx_ref_code);
        if (count($arr) == 2) {
            $diseaseid = $arr[1];
        }

        return $diseaseid;
    }

    public function getRankOfHelpInOneCourse (Course $course) {
        $mysharecnt = $this->getShareCnt("Fbt");
        $sharecnts = WxUserDao::getArrayOfRef_objidAndShareCnt();

        $i = 0;
        foreach ($sharecnts as $a) {
            if ($a["cnt"] > $mysharecnt) {
                $i ++;
            }
        }

        return $i + 1;
    }

    public function getShareCnt ($subclass) {
        $sql = " select count(*) from wxusers where ref_pcode='Share[{$subclass}]' and ref_objtype='User' and ref_objid=:userid ";
        $bind = array(
            ":userid" => $this->user->id);
        return Dao::queryValue($sql, $bind);
    }

    public function hasDingTopic ($topicid) {
        $list = LikeDao::getListByWxuserDing($this, "Topic", $topicid);
        return count($list) > 0;
    }

    public function getFromBaodaoToUnsubscribeDayCnt () {
        $daycnt = 0;
        $patient = $this->user->patient;
        if ($patient instanceof Patient) {
            $endtime = strtotime($this->unsubscribe_time);
            $starttime = strtotime($patient->createtime);
            $diff = $endtime - $starttime;
            $daycnt = round($diff / 86400);
        } else {
            $daycnt = "";
        }
        return $daycnt;
    }

    // TODO 希望写成一个处理并储存WxQrCode相应数据的接口，可自己分析出是何类型，找出并储存obj的值
    // TODO 需要调用analyzeEventKey4Subscribe analyzeEventKey4Scan getByScene_id 等
    public function disposeQrCodeEventKey ($eventkey) {}

    // =================[start 方寸课堂]===========================
    public function getWxTaskCnt ($ename) {
        $wxtasks = WxTaskDao::getListByWxuseridEname($this->id, $ename);
        return count($wxtasks);
    }

    public function isInWxTask ($ename) {
        $wxtask = WxTaskDao::getLastByEname($this->id, $ename);
        if ($wxtask instanceof WxTask) {
            $endtime = $wxtask->endtime;
            if (time() < strtotime($endtime)) {
                return true;
            }
        }
        return false;
    }

    public function initWxTask ($ename, $starttime, $endtime) {
        $tpl = WxTaskTplDao::getByEname($ename);
        $wxtasktplid = $tpl->id;
        $row = array();
        $row['wxtasktplid'] = $wxtasktplid;
        $row['wxuserid'] = $this->id;
        $row['starttime'] = $starttime;
        $row['endtime'] = $endtime;
        $row['ename'] = $ename;
        $row['status'] = 1;
        $wxtask = WxTask::createByBiz($row);

        $tplitems = WxTaskTplItemDao::getListBy($wxtasktplid);

        foreach ($tplitems as $i => $tplitem) {
            $space = $wxtask->getDetailStartEndTime();
            $time = $space[$i];
            $row = array();
            $row['wxuserid'] = $this->id;
            $row['wxtaskid'] = $wxtask->id;
            $row['wxtasktplitemid'] = $tplitem->id;
            $row['pos'] = $tplitem->pos;
            $row['starttime'] = $time['starttime'];
            $row['endtime'] = $time['endtime'];
            $row['status'] = 0;
            WxTaskItem::createByBiz($row);
        }
    }
    // =================[end 方寸课堂]===========================
    public function setOpsOpen () {
        $user = $this->user;
        if (false == $user instanceof User) {
            return;
        }

        if (false == $user->isAuditor()) {
            return;
        }

        $this->is_ops = 1;
    }

    public function setOpsClose () {
        $this->is_ops = 0;
    }

    public function isOpsOpen () {
        return 1 == $this->is_ops;
    }

    public function joinWxGroup ($ename) {
        $wxshop = $this->wxshop;
        $wxgroup = WxGroupDao::getOneByWxshopidEname($this->wxshopid, $ename);
        if ($wxgroup instanceof WxGroup) {
            WxApi::MvWxuserToGroup($this, $wxgroup->groupid);
        }
    }

    public function joinWxGroupOfADHD () {
        $patient = $this->user->patient;

        if ($patient instanceof Patient) {
            $doctor = $patient->doctor;
            $menzhen_offset_daycnt = $doctor->menzhen_offset_daycnt;

            // 永不开启
            if (0 == $menzhen_offset_daycnt) {
                $groupid_new = 142;
            } else {
                $d = $patient->getDayCntFromBaodao() + 1;
                // 到达开启日期开启，未到达关闭
                if ($d >= $menzhen_offset_daycnt) {
                    $groupid_new = 141;
                } else {
                    $groupid_new = 142;
                }
            }

            $groupid_old = $this->groupid;
            if ($groupid_new != $groupid_old) {
                $this->groupid = $groupid_new;
                WxApi::MvWxuserToGroup($this, $groupid_new);
            }
        } else {
            Debug::warn("入微信分组时未发现patient，wxuserid[{$this->id}]");
        }
    }


    // isInBlackList
    public function isInBlackList(){
        $nickname = $this->nickname;
        $black_list = ["姚sir"];
        foreach($black_list as $v){
            if(strpos($nickname, $v) !== false){
                return true;
            }
        }
        return false;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
