<?php

/*
 * Patient 患者
 */
class Patient extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return [
            'createuserid',  // 创建userid
            'doctorid',  // doctorid, pcard
            'first_doctorid',
            'diseaseid',  // 疾病id, pcard
            'woy',  // week of year
            'name',  // 姓名
            'prcrid',  // 身份证
            'sex',  // 性别, 值为1时是男性，值为2时是女性，值为0时是未知
            'birthday',  // 生日
            'blood_type',  // 血型
            'children',  // 子女 X子X女
            'nation',  // 民族
            'marry_status',  // 婚姻状况
            'education',  // 文化程度
            'career',  // 职业
            'income',  // 家庭收入
            'postcode',  // 邮编
            'autoimmune_illness',  // 自身免疫病
            'other_illness',  // 其他疾病
            'past_main_history',  // 自身免疫病
            'past_other_history',  // 其他疾病
            'infect_history',  // 传染病史
            'trauma_history',  // 外伤史
            'drink_history',  // 饮酒史
            'special_contact_history',  // 特殊接触史
            'family_history',  // 家族病史
            'smoke_history',  // 吸烟史,逗号分隔
            'menstruation_history',  // 月经史
            'childbearing_history',  // 生育史
            'allergy_history',  // 过敏史,逗号分隔
            'general_history',  // 普通病史
            'doubt_type',  // 状态0:默认状态 1:怀疑无效 2:不配合患者 3:黑名单患者
            'status',  // 状态 0:无效 1:有效
            'auditstatus',  // 运营审核状态: 0 待审核, 1 审核通过, 2 审核拒绝/下线
            'doctor_audit_status',  // 医生审核状态 0:审核中 1：审核通过 2：审核拒绝/删除
            'drug_status',  // 患者服药状态。0：未知；1：服药；2：不服药；3：停药
            'is_live',  // 患者是否活着
            'is_test',  // 患者是否测试
            'level',  // 患者等级
            'subscribe_cnt',  // 关注数量
            'wxuser_cnt',  // wxuser总数
            'auditorid',  // auditorid
            'auditor_lock_time',  // 运营锁定时间
            'mgtgrouptplid',  // 所属管理组
            'mgtplanid',  // 管理计划id
            'patientstageid',   // 患者阶段
            'patientgroupid',   // 患者分组
            'auditremark',  // 审核备注
            'audittime',  // 审核时间
            'opsremark',  // 运营备注
            'lastpipeid',  // 最后流id, pcard
            'lastpipe_createtime',  // 最后一次用户行为时间, pcard
            'lastactivitydate',  // 上次活跃日期
            'nextactivitydate',  // 下次活跃日期
            'isactivity',  // 活跃状态
            'medicine_break_date',  // 药物中断日期
            'paper_score_trend',  // 得分趋势
            'clone_by_patientid',  // clone源
            'mobile',  // 旧字段, 新含义:电话,数据库患者录入,没有user的情况，迁移到linkman上
            'other_contacts',  // 旧字段, 新含义:备用电话,数据库患者录入,没有user的情况
            'email',  // 邮箱 数据库患者录入,没有user的情况
            'remark',  // 导数据用的备注
            'clicked_agree_cnt',  // 点击过免责声明的次数
            'mother_name',  // 用于验证的妈妈姓名
            'is_fill_cns',  // 是否填写承诺书
            'is_show_pmsheet_tip',  // 是否显示用药核对通知
            'is_medicine_check',  // 用药核对开关 0：关闭 1：开启
            'is_adr_monitor',  // 不良反应监测开关 0：关闭 1：开启
            'is_alk',  // 是否为ALK项目患者
            'old_patientid', // 患者旧id
            'is_lose',  // 是否失访 0：激活 1：失访
            'is_see',   // 患者是否直接可见(运营任务页)
            'adrmonitor_weekday',  // 不良反应监测，星期，1-7
            'chufang_hzbh',  // 海南处方系统患者编号
            'actelion_jifen_balance' // 爱可泰隆患者积分余额
        ];
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'doctorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");

        $this->_belongtos["first_doctor"] = array(
            "type" => "Doctor",
            "key" => "first_doctorid");

        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");

        $this->_belongtos["patientstage"] = array(
            "type" => "PatientStage",
            "key" => "patientstageid");

        $this->_belongtos["patientgroup"] = array(
            "type" => "PatientGroup",
            "key" => "patientgroupid");

        $this->_belongtos["createuser"] = array(
            "type" => "User",
            "key" => "createuserid");

        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");

        $this->_belongtos["lastpipe"] = array(
            "type" => "Pipe",
            "key" => "lastpipeid");

        $this->_belongtos["mgtgrouptpl"] = array(
            "type" => "MgtGroupTpl",
            "key" => "mgtgrouptplid");
    }

    // 返回 MasterWxUser
    public function getMasterWxUser ($wxshopid = 0) {
        return WxUserDao::getMasterWxUserByPatientId($this->id, $wxshopid);
    }

    // 返回 MasterWxUserId
    public function getMasterWxUserId ($wxshopid = 0) {
        $wxuser = WxUserDao::getMasterWxUserByPatientId($this->id, $wxshopid);

        return ($wxuser instanceof WxUser) ? $wxuser->id : 0;
    }

    // 获取唯一的WxUserId
    public function getWxUserIdIfOnlyOne () {
        $users = $this->getUsers();
        if (count($users) == 1) {
            $user = array_shift($users);
            return $user->getWxUserIdIfOnlyOne();
        }
        return 0;
    }

    // 获取WxUser对象
    public function getWxUsers () {
        return WxUserDao::getListByPatient($this);
    }

    // getUsers
    public function getUsers () {
        return UserDao::getListByPatient($this->id);
    }

    // 返回 MasterUser
    // fix by sjp 20170302
    public function getMasterUser () {
        $wxuser = $this->getMasterWxUser();
        return ($wxuser instanceof WxUser) ? $wxuser->user : $this->createuser;
    }

    // 返回 MasterUserId
    // by sjp 20170302
    public function getMasterUserId () {
        $wxuser = $this->getMasterWxUser();
        return ($wxuser instanceof WxUser) ? $wxuser->id : $this->createuserid;
    }

    // 获取唯一的UserId
    public function getUserIdIfOnlyOne () {
        $users = $this->getUsers();
        if (count($users) == 1) {
            $user = array_shift($users);
            return $user->id;
        }
        return 0;
    }

    // 患者状态字符串
    public function getStatusStr () {
        return PatientStatusService::getPatientStatusDesc($this);
    }

    // 是否测试患者
    // 报到姓名含有测试的
    // 报到患者时auditorid
    // 报到到测试医院名下的
    public function isTest () {
        $name = trim($this->name);
        $regex = '/.*测试.*/';
        if (preg_match($regex, $name, $match)) {
            return true;
        }

        $users = $this->getUsers();
        foreach ($users as $user) {
            $auditor = AuditorDao::getByUserid($user->id);
            if ($auditor instanceof Auditor) {
                return true;
            }
        }

        $doctor = $this->doctor;
        if ($doctor instanceof Doctor && $doctor->isTest()) {
            return true;
        }

        return false;
    }

    public function getDayCntFromBaodao ($d = "") {
        if ("" == $d) {
            $d = date("Y-m-d", time());
        }
        $createtime = strtotime($this->createtime);
        $baodaodate = date("Y-m-d", $createtime);

        $diff = XDateTime::getDateDiff($d, $baodaodate);
        return $diff;
    }

    // 目前方寸儿童管理服务平台方向，默认管控患者168天,超过180天将不管控
    // 无效患者不管控
    public function isUnderControl ($d = "") {
        $doubt_type = $this->doubt_type;
        if ($doubt_type == 1) {
            return false;
        }
        $diff = $this->getDayCntFromBaodao($d);
        return $diff <= 168 ? true : false;
    }

    // 开药门诊vip用户，9.5折
    public function isMenZhenVip () {
        $arr = array(
            155031546,
            104760485,
            161790346,
            143212796,
            105990307,
            100806243,
            106669767,
            107944789,
            195359896,
            120242315,
            111706645,
            104169913,
            119217495,
            224596146,
            216398646,
            121118755,
            240461196,
            157397226,
            108010151,
            116808545,
            102818355,
            166370686,
            139357256,
            104427475,
            105343251,
            132231536,
            104753429);
        return in_array($this->id, $arr);
    }

    // 是否被疑似患者
    public function isDoubt () {
        return 1 == $this->doubt_type;
    }

    // 是否被列为黑名单
    public function isOnTheBlackList () {
        return 3 == $this->doubt_type;
    }

    // 是不是加入了第三方合作，当前合作lilly
    public function isInHezuo ($company) {
        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid($company, $this->id, " and status=1");
        return $patient_hezuo instanceof Patient_hezuo;
    }

    public function getGantongDayCnt () {
        return XAnswerSheet::getCntByXQuestionEnameAndPatientid("gantong_hwk_radio", $this->id);
    }

    public function getPgroupid () {
        $pgroupid = 0;
        $patientpgroupref = PatientPgroupRefDao::getOneByPatientid($this->id, " and status=1");
        if ($patientpgroupref instanceof PatientPgroupRef) {
            $pgroupid = $patientpgroupref->pgroupid;
        }
        return $pgroupid;
    }

    public function getMarkName () {
        $nameFirst = mb_substr($this->name, 0, 1, 'utf-8');
        return $nameFirst . "**";
    }

    public function getMaskName () {
        $needMaskName = XContext::getValue("needMaskName");
        $name = $this->name;
        if ($needMaskName) {
            $nameFirst = mb_substr($name, 0, 1, 'utf-8');
            return $nameFirst . "**";
        } else {
            return $name;
        }
    }

    // 近几天有没有支付过的订单 $day=2 前天~今天
    public function hasPayShopOrderNearlyDay ($day = 2) {
        $shoporder = ShopOrderDao::getIsPayShopOrderByPatient($this);
        if ($shoporder instanceof ShopOrder) {
            $diff = XDateTime::getDateDiff(date("Y-m-d", time()), date("Y-m-d", strtotime($shoporder->time_pay)));
            if ($diff <= $day) {
                return true;
            }
        }

        return false;
    }

    // 判断用户当前是不是已经完成了该分组
    public function isDonePgroup ($pgroupid) {
        $patientpgroupref = PatientPgroupRefDao::getOneByPatientidPgroupid($this->id, $pgroupid, "and status in (0,2)");
        return $patientpgroupref instanceof PatientPgroupRef;
    }

    // 判断用户当前是不是已经完成了一类分组
    public function isDonePgroups ($typestr, $subtypestr) {
        $flag = true;

        $cond = " and typestr = :typestr and subtypestr = :subtypestr and showinwx=1 ";

        $bind = [];
        $bind[':typestr'] = $typestr;
        $bind[':subtypestr'] = $subtypestr;

        $pgroups = Dao::getEntityListByCond('Pgroup', $cond, $bind);

        foreach ($pgroups as $pgroup) {
            if (false == $this->isDonePgroup($pgroup->id)) {
                $flag = false;
                break;
            }
        }
        return $flag;
    }

    // a. 当患者当前不在课程中时，未学过的课程显示“加入课程”，未完成和已完成的课程显示“重学”；
    // b. 当患者当前在课程中时，正在学习的课程显示“继续学习”，未完成和已完成的课程显示“查看课程历史”；
    public function getStrForShowPgroup ($pgroupid) {
        $patientpgroupref_current = PatientPgroupRefDao::getOneByPatientid($this->id, " and status = 1 and typestr = 'manage'");
        // 当前不在组中
        if (false == $patientpgroupref_current instanceof PatientPgroupRef) {
            $the_patientpgroupref = PatientPgroupRefDao::getOneByPatientidPgroupid($this->id, $pgroupid);

            if ($the_patientpgroupref instanceof PatientPgroupRef) {
                return "<a class='productItem-b am-cf' href='/pgroup/join?pgroupid={$pgroupid}'>重学</a>";
            } else {
                return "<a class='productItem-b am-cf' href='/pgroup/join?pgroupid={$pgroupid}'>加入课程</a>";
            }
        } else {
            // 当前在组中
            $current_pgroupid = $patientpgroupref_current->pgroupid;
            if ($current_pgroupid == $pgroupid) {
                return "<a class='productItem-b am-cf' href='/patientpgrouptask/todayTask'>继续学习</a>";
            }
            $the_patientpgroupref = PatientPgroupRefDao::getOneByPatientidPgroupid($this->id, $pgroupid);

            if ($the_patientpgroupref instanceof PatientPgroupRef) {
                $status = $the_patientpgroupref->status;
                if ($status == 1) {
                    return "<a class='productItem-b am-cf' href='/patientpgrouptask/todayTask'>继续学习</a>";
                } else {
                    return "<a class='productItem-b am-cf' href='/patientpgroupref/historyList'>查看历史</a>";
                }
            } else {
                return "<span class='productItem-b am-cf productItem-bgray'>暂不可学</span>";
            }
        }
    }

    public function isInPgroup4Day () {
        $condFix = " AND status < 3 AND date_sub(createtime, INTERVAL 4 DAY) < '{$this->createtime}' ";

        $patietpgroupref = PatientPgroupRefDao::getListByPatientid($this->id, $condFix);

        if (count($patietpgroupref) > 0) {
            return true;
        } else {
            return false;
        }
    }

    // 获取入过的某一类组的数量
    public function getPgroupsCntByTypestr ($typestr) {
        return PatientPgroupRefDao::getCntByPatientidAndTypestr($this->id, $typestr);
    }

    // 显示26项的SNAP-IV量表
    public function showAdhd_ivOf26 () {
        if ($this->doctor->useAdhd_ivOf26()) {
            return true;
        }
        return false;
    }

    // 显示18项的SNAP-IV量表
    public function notShowAdhd_ivOf26 () {
        return false == $this->showAdhd_ivOf26();
    }

    public function hasDonePaper ($papertplid, $condFix = "") {
        $a = PaperDao::getListByPatientid($this->id, " and papertplid={$papertplid} " . $condFix);
        return count($a) > 0;
    }

    // 判断患者是否能够进入开药门诊
    public function canIntoMenzhen () {
        if ($this->isInHezuo("Lilly")) {
            return false;
        }

        // 黑名单患者
        if ($this->isOnTheBlackList()) {
            return false;
        }

        $doctor = $this->doctor;
        $menzhen_offset_daycnt = $doctor->menzhen_offset_daycnt;

        if ($menzhen_offset_daycnt == 0) {
            return false;
        }

        $d = $this->getDayCntFromBaodao() + 1;
        if ($d >= $menzhen_offset_daycnt) {
            return true;
        } else {
            return false;
        }
    }

    // 性别
    public function getSexStr () {
        return XConst::Sex($this->sex);
    }

    // 性别
    public function getSexStrFix () {
        $str = XConst::Sex($this->sex);
        if ($str == '空') {
            $str = '';
        }

        return $str;
    }

    // ipad 列表页仅需要一个电话
    public function getOneMobile () {
        $str = $this->getMobiles();
        return substr($str, 0, 11);
    }

    // 获取主要联系人,一定能获取到一个主联系人
    public function getMasterMobile () {
        $linkman_master = LinkmanService::getMasterLinkman($this);

        return $linkman_master->mobile;
    }

    // 获取最老备用联系人：患者报到时，有可能会填写的phone
    public function getBaodaoPhone () {
        $phone_linkman = LinkmanDao::getOtherByPatientid($this->id);

        if (false == $phone_linkman instanceof Linkman) {
            return "";
        }else{
            return $phone_linkman->mobile;
        }

    }

    // 电话号码串
    public function getMobiles ($separator = ' ; ', $removeDuplicate = true, $is_mask = false) {
        $linkmans = $this->getLinkmans();

        $str = '';
        $i = 0;
        if (empty($linkmans)) {
            return $str;
        }
        foreach ($linkmans as $linkman) {
            if ($linkman->mobile) {
                $i ++;
                if ($i > 1) {
                    // 后面的都补间隔符
                    $str .= $separator;
                }

                if ($is_mask) {
                    $str .= $linkman->getMarkMobile();
                } else {
                    $str .= $linkman->mobile;
                }
            }
        }

        if ("" == $str) {
            $str = "未知";
        }

        if ($removeDuplicate) {
            $mobileArr = explode($separator, $str);
            $mobileArrUniq = array_unique($mobileArr);
            $str = implode($separator, $mobileArrUniq);
        }
        return $str;
    }

    public function getMaskMobiles ($separator = ' ; ') {
        return $this->getMobiles($separator, true, true);
    }

    // getAgeStr
    public function getAgeStr () {
        $birth = $this->birthday;
        list ($by, $bm, $bd) = explode('-', $birth);
        $cm = date('n');
        $cd = date('j');
        $age = date('Y') - $by - 1;
        if ($cm > $bm || ($cm == $bm && $cd > $bd)) {
            $age ++;
        }

        if ($age > 100) {
            $age = "";
        }

        return $age;
    }

    // 是否取消关注
    public function isSubscribe () {
        $wxusers = WxUserDao::getListByPatient($this);

        $issubscribe = false;
        foreach ($wxusers as $a) {
            if ($a->subscribe == 1) {
                $issubscribe = true;
                break;
            }
        }

        return $issubscribe;
    }

    public function getSameNamePatientCnt () {
        return PatientDao::getCntByName($this->name);
    }

    // 获取流列表
    public function getPrePipesOfPatientOrderByTime ($cnt = 10, $offsetpipetime = '', $condEx = '') {
        return PipeDao::getPreListByPatientOrderByTime($this->id, $cnt, $offsetpipetime, $condEx);
    }

    // 获取流文档
    public function getPipes ($offsetpipetime = '', $page_size = 10) {

        // 多取2倍数据
        $pipes = $this->getPrePipesOfPatientOrderByTime($page_size * 10, $offsetpipetime);

        $arr = array();
        $i = 0;
        foreach ($pipes as $a) {
            if ($a->isFlow()) {
                $i ++;
                $arr[] = $a;
            }

            if ($i > 9) {
                break;
            }
        }
        return $arr;
    }

    // 获取大于等于某pipeid的所有流
    public function getPipesForTrack ($pipeid) {
        $pipes = PipeDao::getListByPatient($this, " and id >= {$pipeid} order by id desc");

        $arr = array();
        $i = 0;
        foreach ($pipes as $a) {
            if ($a->isFlow()) {
                $i ++;
                $arr[] = $a;
            }
        }

        return $arr;
    }

    // 获取流文档
    public function getHasFilteredPipes ($offsetpipetime = '', $page_size = 10, $filter) {
        $ischeckedall = $filter['ischeckedall'];
        $ispiconly = $filter['ispiconly'];

        $condEx = "";
        if (isset($ispiconly) && 1 == $ispiconly) { // 4131 只显示图片
            $condEx = " AND objtype = 'WxPicMsg' ";
        } else {
            if ($ischeckedall) {
                $condEx = "";
            } else {
                $pipetplids = $filter['pipetplids'];
                if (isset($pipetplids)) {
                    $pipetplids_str = implode(",", $pipetplids);
                    $condEx = " and pipetplid in ({$pipetplids_str})";
                }
            }
        }

        // 多取2倍数据
        $pipes = $this->getPrePipesOfPatientOrderByTime($page_size * 2, $offsetpipetime, $condEx);

        $arr = array();
        $i = 0;
        foreach ($pipes as $a) {
            if ($a->isFlow()) {
                $i ++;
                $arr[] = $a;
            }

            if ($i > 9) {
                break;
            }
        }

        return $arr;
    }

    // 用药核对，患者最近一次提交时间
    public function getLastPatientMedicineCheckSubmitTime () {
        $cond = " and patientid = :patientid and type = 'multiple_diseases' and status = 2 order by submit_time desc limit 1 ";
        $bind = [
            'patientid' => $this->id];
        $patientmedicinecheck = Dao::getEntityByCond('PatientMedicineCheck', $cond, $bind);

        return $patientmedicinecheck->submit_time ? $patientmedicinecheck->submit_time : '';
    }

    // 不良反应监测，患者最近一次提交时间
    public function getLastPADRMonitorSubmitTime () {
        $cond = " and patientid = :patientid order by submit_time DESC limit 1 ";
        $bind = [
            'patientid' => $this->id];
        $padrmonitor = Dao::getEntityByCond('PADRMonitor', $cond, $bind);

        return $padrmonitor->submit_time ? $padrmonitor->submit_time : '';
    }

    // 用户的最后有效流
    public function getLastPipeByUser ($todate = '') {
        return PipeDao::getLastPipeByUser($this->id, $todate);
    }

    // 用户的最后有效流生成的标签
    public function getLastPipeToTagStrByUser () {
        $pipe = $this->lastpipe;

        $str = '';

        if ($pipe instanceof Pipe) {
            $str = $pipe->getCreatemdHi() . " [" . $pipe->pipetpl->title . "]";
        }

        return $str;
    }

    // 修正最后活跃时间
    public function fixLastactivitydate ($todate) {
        $this->lastactivitydate = $this->getCreateDay();

        $pipe = $this->getLastPipeByUser($todate);

        if ($pipe instanceof EntityBase) {
            $this->lastactivitydate = $pipe->getCreateDay();
        }
    }

    // 杨莉名下患者
    public function isOfYangli () {
        if (in_array($this->doctorid, array(
            1,
            2,
            3))) {
            return true;
        }
        return false;
    }

    // 修正下一次活跃时间
    public function fixNextactivitydate ($todate = '') {
        $nowtime = time();
        // 为了能够重跑数据
        if ($todate) {
            $nowtime = strtotime($todate) + 1;
        }

        $lastactivitytime = strtotime($this->lastactivitydate);

        // 末次换药日期, 或首次服药日期, 或报到日期
        $from_date = '0000-00-00'; // 20160223 TODO by sjp : 需要写个函数获取末次调药时间
        $fromtime = strtotime($from_date == '0000-00-00' ? substr($this->createtime, 0, 10) : $from_date);
        $days = ($nowtime - $fromtime) / 86400;
        // 服药患者
        if (false == in_array($this->getMedicinestr(), array(
            '未知',
            '无记录',
            '不服药'))) {

            // 杨莉
            if ($this->isOfYangli()) {
                if ($days < 30) {
                    $nexttime = $lastactivitytime + 86400 * 7;
                } elseif ($days < 60) {
                    $nexttime = $lastactivitytime + 86400 * 14;
                } elseif ($days < 90) {
                    $nexttime = $lastactivitytime + 86400 * 30;
                } else {
                    $nexttime = $lastactivitytime + 86400 * 180;
                }
            } else {
                if ($days < 60) {
                    $nexttime = $lastactivitytime + 86400 * 7;
                } elseif ($days < 90) {
                    $nexttime = $lastactivitytime + 86400 * 14;
                } elseif ($days < 180) {
                    $nexttime = $lastactivitytime + 86400 * 30;
                } else {
                    $nexttime = $lastactivitytime + 86400 * 90;
                }
            }
        } else {
            if ($days < 30) {
                $nexttime = $lastactivitytime + 86400 * 14;
            } elseif ($days < 180) {
                $nexttime = $lastactivitytime + 86400 * 30;
            } else {
                $nexttime = $lastactivitytime + 86400 * 90;
            }
        }

        $this->nextactivitydate = date("Y-m-d", $nexttime + 1);
    }

    // 修正活跃状态
    public function fixIsactivity ($thedate = '') {
        $nowtime = time();
        // 为了能够重跑数据
        if ($thedate) {
            $nowtime = strtotime($thedate);
        }

        $nextactivitytime = strtotime($this->nextactivitydate);
        if ($nextactivitytime > $nowtime) {
            $this->isactivity = 1;
        } else {
            $this->isactivity = 0;
        }
    }

    // 获取用户图片消息列表
    public function getWxPicMsgsForWx () {
        return WxPicMsgDao::getListForWxOfPatientid($this->id);
    }

    // 获取用户图片消息列表
    public function getWxPicMsgs () {
        return WxPicMsgDao::getListByPatientid($this->id);
    }

    // 获取用户文本消息列表
    public function getWxTxtMsgs () {
        return WxTxtMsgDao::getListByPatient($this->id);
    }

    // 获取推送消息列表
    public function getPushMsgs () {
        return PushMsgDao::getListByPatient($this->id);
    }

    // 是否审核通过
    public function has_audited_patient () {
        $patients = PatientDao::getListByName($this->name);
        foreach ($patients as $a) {
            if ($a->id != $this->id && $a->isBaodaoed()) {
                return true;
            }
        }
        return false;
    }

    // 报到成功了
    public function isBaodaoed () {
        if ($this->status == 1 && $this->subscribe_cnt > 0) {
            return true;
        } else {
            return false;
        }
    }

    // ---- MEDICINECODE begin ----

    // 患者当前用药情况划分: 未知 | 无记录 | 不服药 | 其他 | 专注达 | 专注达,择思达
    public function getMedicineStr () {
        $refs = PatientMedicineRefDao::getListByPatient($this);
        if ($this->diseaseid == 3) {
            $refs = PatientMedicineRefDao::getMainListByPatient($this);
        }

        $str = '未知';
        if (false == empty($refs)) {
            $arr = array();
            foreach ($refs as $a) {
                $arr[] = $a->medicine->name;
            }
            if (is_array($arr)) {
                if (count($arr) > 3) {
                    $arr = array_slice($arr, 0, 2);
                    $arr[] = "...";
                }
            }
            $str = implode(',', $arr);
        } elseif (is_null($this->isNoDruging())) {
            $str = '无记录';
        } elseif ($this->isNoDruging()) {
            $str = '不服药';
        }

        return $str;
    }

    // 用药时长,月份
    public function getMedicine_monthcnt_str () {
        $first_start_date = PatientMedicineRefDao::getFirst_start_dateByPatient($this);

        if (substr($first_start_date, 0, 10) == "0000-00-00") {
            return '无';
        }

        $today = date_create();
        $fromDay = date_create($first_start_date);

        $date_diff = date_diff($today, $fromDay);
        $str = '';
        if ($date_diff->y > 0) {
            $months = $date_diff->y * 12 + $date_diff->m;
            $str = "{$months}个月";
        } elseif ($date_diff->m < 1) {
            $str = "1月内";
        } else {
            $str = "{$date_diff->m}个月";
        }
        return $str;
    }

    // level 9 用药时长
    public function getMedicine_monthcnt_strOfMain () {
        $first_start_date = PatientMedicineRefDao::getFirst_start_dateOfLevelByPatient($this);

        if (substr($first_start_date, 0, 10) == "0000-00-00") {
            return '无';
        }

        $today = date_create();
        $fromDay = date_create($first_start_date);

        $date_diff = date_diff($today, $fromDay);
        $str = '';
        if ($date_diff->y > 0) {
            $months = $date_diff->y * 12 + $date_diff->m;
            $str = "{$months}个月";
        } elseif ($date_diff->m < 1) {
            $str = "1月内";
        } else {
            $str = "{$date_diff->m}个月";
        }
        return $str;
    }

    // 获取一个人服过药物的所有ref记录
    public function getAllPatientMedicineRefs () {
        return PatientMedicineRefDao::getAllListByPatient($this);
    }

    // 获取与某一个药物间的ref对象
    public function getRefWithMedicine (Medicine $medicine, $mustGot = false) {
        if ($mustGot) {
            return PatientMedicineRef::getOrCreateByPatientidMedicineid($this->id, $medicine->id);
        } else {
            return PatientMedicineRefDao::getByPatientidMedicineid($this->id, $medicine->id);
        }
    }

    // 获取与某一个药物间的ref对象(没有停药的) by wgy
    public function getNoStopRefWithMedicine (Medicine $medicine) {
        return PatientMedicineRefDao::getNoStopByPatientidMedicineid($this->id, $medicine->id);
    }

    // 获取药物id为0的ref的对象
    public function getNoMedicineRef () {
        return PatientMedicineRef::getOrCreateByPatientidMedicineid($this->id, 0);
    }

    // 获取(一个药)所有的drugitem
    public function getDrugItemList ($medicine_rep = null) {
        if (is_null($medicine_rep)) {
            return DrugItemDao::getListByPatientidMedicineid($this->id);
        }
        if ($medicine_rep instanceof Medicine) {
            return DrugItemDao::getListByPatientidMedicineid($this->id, $medicine_rep->id);
        }
        return DrugItemDao::getListByPatientidMedicineid($this->id, $medicine_rep);
    }

    public function getLastDrugItem ($medicine_rep = null) {
        if (is_null($medicine_rep)) {
            return DrugItemDao::getLastByPatientid($this->id);
        }
        if ($medicine_rep instanceof Medicine) {
            return DrugItemDao::getLastByPatientid($this->id, $medicine_rep->id);
        }
        return DrugItemDao::getLastByPatientid($this->id, $medicine_rep);
    }

    public function getLast_drugchange_date () {
        $refs = $this->getAllPatientMedicineRefs();
        $intdates = array_map(function ($x) {
            return strtotime($x->last_drugchange_date);
        }, $refs);
        rsort($intdates);
        $maxdate = $intdates[0];
        if ($maxdate) {
            return date("Y-m-d", $maxdate);
        }
        return null;
    }

    // 获取最近服药日期
    public function getLastDrugDate ($medicine_rep = null) {
        $lastDrugItem = $this->getLastDrugItem($medicine_rep);
        return $lastDrugItem->record_date;
    }

    // 获取第一条记录，默认所有第一条，若指定药物可传入药物或药物id
    public function getFirstDrugItem ($medicine_rep = null) {
        if (is_null($medicine_rep)) {
            return DrugItemDao::getFirstByPatientid($this->id);
        }
        if ($medicine_rep instanceof Medicine) {
            return DrugItemDao::getFirstByPatientid($this->id, $medicine_rep->id);
        }
        return DrugItemDao::getFirstByPatientid($this->id, $medicine_rep);
    }

    // FIXME 获取第一次停药日期
    public function getFirstStopDrugDate () {
        $drugitem = $this->getFirstDrugItem(0);
        return $drugitem->record_date;
    }

    // 已用药天数, 所有的服药记录
    public function drugingDays () {
        $drugItem = $this->getFirstDrugItem();
        if ($drugItem instanceof DrugItem) {
            $now = date_create();
            $record_date = date_create($drugItem->record_date);
            return date_diff($now, $record_date)->days;
        } else {
            return 0;
        }
    }

    // 患者是否在服用任何药物
    public function isDruging () {
        $refs = PatientMedicineRefDao::getListByPatient($this);
        return ! empty($refs);
    }

    // 患者是否曾经服用过药物
    public function isEverDruging () {
        $refs = PatientMedicineRefDao::getAllListByPatient($this);
        return ! empty($refs);
    }

    // 患者是否为不服药患者
    public function isNoDruging () {
        $refs = PatientMedicineRefDao::getAllListByPatient($this);
        $ref_cnt = count($refs);
        if ($ref_cnt == 0) {
            $drugsheet = DrugSheetDao::getOneByPatientid($this->id, " order by thedate desc");
            if ($drugsheet instanceof DrugSheet && 1 == $drugsheet->is_nodrug) {
                return true;
            }
        }
        return null;
    }

    // 患者已停药
    public function isStopDruging () {
        return $this->isEverDruging() && ! ($this->isDruging());
    }

    // 当前服药状态
    public function drugStatusStr () {
        $str = "无记录";
        if ($this->isDruging()) {
            $str = '服药中';
        } elseif ($this->isNoDruging() === true) {
            $str = '不服药';
        }

        return $str;
    }

    // ---- MEDICINECODE end----

    // 最近一次评估
    public function getLastScalePaper () {
        return PaperDao::getLastScaleOfPatient($this->id);
    }

    // 获取某个时间区间患者所做的量表[)半闭半开区间
    public function getDayRangePapers ($startDate, $endDate) {
        return PaperDao::getByDayRange($this->id, $startDate, $endDate);
    }

    public function getPinyin () {
        return PinyinUtil::Pinyin($this->name);
    }

    public function getPy () {
        $str = PinyinUtil::Word2PY($this->name);
        return strtolower($str);
    }

    public function getAttrStr4Ipad () {
        $str = $this->getSexStrFix();
        $str .= $str ? ' ' : '';
        $agestr = $this->getAgeStr();
        if ('0' != $agestr && "" != $agestr) {
            $str .= $agestr . "岁 ";
        }
        $mobile_place = PatientAddressService::getPatientAddressByTypePatientid('mobile_place', $this->id);
        $xprovincestr = $mobile_place->xprovince->name;
        $xcitystr = $mobile_place->xcity->name;
        if ($xprovincestr != $xcitystr) {
            $str .= (" " . $xcitystr);
        } else {
            $str .= (" " . $xprovincestr);
        }
        return $str;
    }

    public function getAttrStr () {
        $str = $this->getSexStrFix();
        $str .= $str ? ' ' : '';
        $agestr = $this->getAgeStr();
        if ('0' != $agestr && "" != $agestr) {
            $str .= $agestr . "岁 ";
        }
        $str .= $this->getXprovinceXcityStr();
        return $str;
    }

    public function getBaodaoDay () {
        return substr($this->createtime, 0, 10);
    }

    public function getNameOrNameOfUser () {
        $str = $this->name;
        if (empty($str)) {
            $str = $this->getMasterUser()->name;
            $str = mb_substr($str, 0, 5);
            $str = "[" . $str . "]";
        }

        return $str;
    }

    // 获取最后得分
    public function getLastADHDScore () {
        $paper = PaperDao::getLastADHD($this->id);

        if ($paper instanceof Paper) {
            return $paper->xanswersheet->score;
        }

        return '';
    }

    // 获取adhd 分数的趋势 0 稳定， 1上升， 2下降 (没找到写枚举的地方)
    public function getTrendOfADHDScore () {
        $papers = array();
        $papers = PaperDao::getLastADHDList($this->id, 2);
        if (count($papers) < 2) {
            return "";
        }
        if ($papers[0]->xanswersheet->score > $papers[1]->xanswersheet->score) {
            return "1";
        } elseif ($papers[0]->xanswersheet->score == $papers[1]->xanswersheet->score) {
            return "0";
        } else {
            return "2";
        }
    }

    // 修正评分趋势
    public function reset_paper_score_trend () {
        $this->paper_score_trend = $this->getTrendOfADHDScore();
    }

    // 患者答卷统计
    public function getXQuestionSheetSumOfPatient () {
        return XQuestionSheet::getXQuestionSheetSumOfPatient($this->id);
    }

    // 本患者标签ids
    public function getBindTagIds () {
        $tagrefs = $this->getTagRefs();
        $arr = array();
        foreach ($tagrefs as $a) {
            $arr[] = $a->tagid;
        }

        return $arr;
    }

    // 患者标签关系数组
    public function getTagRefs ($typestr = '') {
        return TagRefDao::getListByObj($this, $typestr);
    }

    // 患者与症断结果关系数组(排除其他合并症项)
    public function getTagNamesStr ($typestr = 'Disease') {
        $str = TagRefDao::getTagNamesStr($this, $typestr);
        if (empty($str)) {
            $str = ' - ';
        }

        return $str;
    }

    // ///////////////////////
    // 获取新版联系人linkman
    public function getLinkmans () {
        return LinkmanDao::getListByPatientid($this->id);
    }

    public function getPatientTagNames () {
        $patienttags = PatientTagDao::getListByPatientid($this->id);

        $list = [];
        foreach ($patienttags as $patienttag) {
            $list[] = $patienttag->patienttagtpl->name;
        }

        return $list;
    }

    // information4ipad
    public function information4ipad () {
        $arr = array();
        $arr[] = array(
            'title' => '姓名',
            'k' => 'name',
            'v' => $this->name,
            'type' => 'input',
            'option' => array());
        $arr[] = array(
            'title' => '性别',
            'k' => 'sexstr',
            'v' => $this->getSexStrFix(),
            'type' => 'radio',
            'option' => array(
                "男",
                "女"));
        $arr[] = array(
            'title' => '出生日期',
            'k' => 'birthday',
            'v' => $this->birthday,
            'type' => 'date',
            'option' => array());
        $arr[] = array(
            'title' => '身份证号',
            'k' => 'prcrid',
            'v' => $this->prcrid,
            'type' => 'num',
            'option' => array());
        $arr[] = array(
            'title' => '民族',
            'k' => 'nation',
            'v' => $this->nation,
            'type' => 'input',
            'option' => array());
        $arr[] = array(
            'title' => '婚姻状况',
            'k' => 'marry_status',
            'v' => $this->marry_status,
            'type' => 'radio',
            'option' => array(
                "未婚",
                "已婚",
                "丧偶",
                "离婚",
                "不明"));
        $arr[] = array(
            'title' => '文化程度',
            'k' => 'education',
            'v' => $this->education,
            'type' => 'radio',
            'option' => array(
                "研究生",
                "本科",
                "专科",
                "中职",
                "高中",
                "初中",
                "小学",
                "其他"));
        $arr[] = array(
            'title' => '职业',
            'k' => 'career',
            'v' => $this->career,
            'type' => 'radio',
            'option' => array(
                "国家机关、党群组织、企业、事业单位负责人",
                "专业技术人员",
                "办事人员和有关人员",
                "商业、服务人员",
                "农、林、牧、渔、水利业生产人员",
                "军人",
                "生产、运输设备操作人员及有关人员",
                "其他"));
        $arr[] = array(
            'title' => '收入',
            'k' => 'income',
            'v' => $this->income,
            'type' => 'num',
            'option' => array());
        // $arr[] = array(
        // 'title' => '籍贯',
        // 'k' => 'native_place',
        // 'v' => $this->getNativePlaceStr(),
        // 'type' => 'input',
        // 'option' => array());
        // $arr[] = array(
        // 'title' => '住址',
        // 'k' => 'address',
        // 'v' => $this->addressstr,
        // 'type' => 'input',
        // 'option' => array());
        $arr[] = array(
            'title' => '邮编',
            'k' => 'postcode',
            'v' => $this->postcode,
            'type' => 'num',
            'option' => array());
        $arr[] = array(
            'title' => '手机',
            'k' => 'mobile',
            'v' => $this->getMasterMobile(),
            'type' => 'num',
            'option' => array());
        return $arr;
    }

    // information
    public function information () {
        $arr = $this->information4ipad();
        return $arr;
    }

    // informationmodify4Ipad
    public function informationmodify4Ipad ($data) {
        $this->informationmodify($data);
    }

    // informationmodify
    public function informationmodify ($data) {
        $default = array();

        $default["name"] = $this->name;
        $default["sex"] = $this->sex;
        $default["birthday"] = $this->birthday;
        $default["prcrid"] = $this->prcrid;
        $default["nation"] = $this->nation;
        $default["marry_status"] = $this->marry_status;
        $default["education"] = $this->education;
        $default["career"] = $this->career;
        $default["income"] = $this->income;
        $default["postcode"] = $this->postcode;
        $default["mobile"] = $this->getMasterMobile();

        $data += $default;

        $this->name = $data['name'];
        $sexstr = $data['sexstr'];

        if ($sexstr == "男") {
            $this->sex = 1;
        }

        if ($sexstr == "女") {
            $this->sex = 2;
        }

        $this->birthday = $data['birthday'];
        $this->prcrid = $data['prcrid'];
        $this->nation = $data['nation'];
        $this->marry_status = $data['marry_status'];
        $this->education = $data['education'];
        $this->career = $data['career'];
        $this->income = $data['income'];
        $this->postcode = $data['postcode'];

        // 修改主联系人
        LinkmanService::updateMasterMobile($this, $data['mobile']);
    }

    public function hasClone () {
        $a = PatientDao::getOneByClone_by_patientid($this->id);
        return count($a) > 0;
    }

    public function getXprovinceStr () {
        $patientaddress = PatientAddressDao::getByTypePatientid('mobile_place', $this->id);

        return $patientaddress->xprovince->name ?? '';
    }

    public function getXcityStr () {
        $patientaddress = PatientAddressDao::getByTypePatientid('mobile_place', $this->id);

        $four = [
            110000,
            120000,
            310000,
            500000];

        if (in_array($patientaddress->xprovinceid, $four)) {
            return $patientaddress->xcounty->name;
        }

        return $patientaddress->xcity->name ?? '';
    }

    public function getXprovinceXcityStr () {
        $str = $this->getXprovinceStr();
        if ($str != $this->getXcityStr()) {
            $str .= (" " . $this->getXcityStr());
        }
        return $str;
    }

    public function getXcountyStr () {
        $patientaddress = PatientAddressDao::getByTypePatientid('mobile_place', $this->id);

        return $patientaddress->xcounty->name ?? '';
    }

    public function getNativePlaceStr () {
        $patientaddress = PatientAddressDao::getByTypePatientid('native_place', $this->id);

        return $patientaddress->xprovince->name . " " . $patientaddress->xcity->name;
    }

    // ///////////////////////
    // $row = array();
    // $row["createuserid"] = $createuserid;
    // $row["doctorid"] = $doctorid;
    // $row["name"] = $name;
    // $row["sex"] = $sex;
    // $row["birthday"] = $birthday;
    // $row["is_medicine_check"] = 0;
    // $row["is_adr_monitor"] = 0;
    public static function createByBiz ($row = array()) {
        DBC::requireNotEmpty($row, "Patient::createByBiz row cannot empty");
        $default = array();

        $default["createuserid"] = 0; // 保留一个创建userid,解绑的时候用于找回
        $default["doctorid"] = 0;
        $default["first_doctorid"] = 0;
        $default["diseaseid"] = 0;
        $default["woy"] = XDateTime::getWFromFirstDate(date('Y-m-d'));
        $default["name"] = '';
        $default["prcrid"] = '';
        $default["sex"] = 0;
        $default["birthday"] = '0000-00-00';
        $default["blood_type"] = '';
        $default["children"] = '';
        $default["nation"] = '';
        $default["marry_status"] = '';
        $default["education"] = '';
        $default["career"] = '';
        $default["income"] = '';
        $default["postcode"] = 0;

        $default["autoimmune_illness"] = '';
        $default["other_illness"] = '';
        $default["past_main_history"] = '';
        $default["past_other_history"] = '';
        $default["infect_history"] = '';
        $default["trauma_history"] = '';
        $default["drink_history"] = '';
        $default["special_contact_history"] = '';
        $default["family_history"] = '';
        $default["smoke_history"] = '';
        $default["menstruation_history"] = '';
        $default["childbearing_history"] = '';
        $default["allergy_history"] = '';
        $default["general_history"] = '';

        $default["doubt_type"] = 0; // 患者类型

        $default["status"] = 0; // 先审核后生效
        $default["auditstatus"] = 0; // 运营待审核
        $default["doctor_audit_status"] = 0; // 医生待审核
        $default["drug_status"] = 0; // 用药状态
        $default["is_live"] = 1; // 患者是否活着，默认活着
        $default["is_test"] = 0; // 患者是否测试，默认不是
        $default["level"] = ""; // 患者等级
        $default["subscribe_cnt"] = 0; // 关注数量
        $default["wxuser_cnt"] = 0; // 关注数量

        $default["auditorid"] = 0;
        $default["auditor_lock_time"] = '0000-00-00 00:00:00';
        $default["mgtgrouptplid"] = 0;
        $default["mgtplanid"] = 0;
        $default["patientstageid"] = 1;
        $default["patientgroupid"] = 0;
        $default["auditremark"] = '';
        $default["audittime"] = '0000-00-00 00:00:00';

        $default["opsremark"] = '';
        $default["lastpipeid"] = 0;
        $default["lastpipe_createtime"] = XDateTime::now();
        $default["lastactivitydate"] = date("Y-m-d");
        $default["nextactivitydate"] = date("Y-m-d");
        $default["isactivity"] = 1;
        $default["medicine_break_date"] = '0000-00-00';
        $default["paper_score_trend"] = 0;
        $default["clone_by_patientid"] = 0;

        $default["mobile"] = '';
        $default["other_contacts"] = '';
        $default["email"] = '';
        $default["remark"] = '';
        $default["clicked_agree_cnt"] = 1;
        $default["mother_name"] = "";
        $default["is_fill_cns"] = 0;
        $default["is_show_pmsheet_tip"] = 1;
        $default["is_medicine_check"] = 0;
        $default["is_adr_monitor"] = 0;
        $default["is_alk"] = 0;
        $default["is_lose"] = 0;
        $default["is_see"] = 1;

        // 临时字段
        $default["old_patientid"] = 0;

        $default["adrmonitor_weekday"] = 1;
        $default["chufang_hzbh"] = '';

        $default["actelion_jifen_balance"] = 0;

        $row += $default;

        // #4457 NMO新增患者默认进入管理组
        if (Disease::isNMO($row['diseaseid'])) {
            $row['patientgroupid'] = 2;
        }

        $patient = new self($row);

        return $patient;
    }

    public function getPipecntByDateYm ($themonth) {
        return PipeDao::getPipecntByDateYm($this->id, $themonth);
    }

    public function getLastPatientMedicineSheet () {
        return PatientMedicineSheetDao::getLastByPatientid($this->id);
    }

    // 根据patientid判断当前患者所有关注全部为非扫码
    public function isScan () {
        $wxusers = WxUserDao::getListByPatient($this);
        foreach ($wxusers as $wxuser) {
            if ($wxuser->wx_ref_code != '') {
                return true;
            }
        }
        return false;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getTwoPatientTagStr (Doctor $doctor) {
        $patienttags = PatientTagDao::getListByPatientidDoctorid($this->id, $doctor->id, 2);

        $list = array();
        $str = '';
        foreach ($patienttags as $a) {
            $name = mb_substr($a->patienttagtpl->name, 0, 7);
            if (mb_strlen($a->patienttagtpl->name) > 7) {
                $name .= '...';
            }
            $str .= $name . ',';
        }

        $str = mb_substr($str, 0, mb_strlen($str) - 1);

        $list = PatientTagDao::getListByPatientidDoctorid($this->id, $doctor->id);
        if (count($list) > 2) {
            $str .= " ...";
        }

        return $str;
    }

    public function getPatientStatusStr () {
        return $this->status . "" . $this->auditstatus . "" . $this->doctor_audit_status . "" . $this->is_live;
    }

    // 按
    public function getOneMaxLevelOpTask () {
        $cond = " and patientid = :patientid and status in (0,2) order by level desc limit 1 ";
        $bind = [
            ':patientid' => $this->id
        ];

        return Dao::getEntityByCond('OpTask', $cond, $bind);
    }

    // 获取5个基本id wxuserid, userid, patientid, doctorid, diseaseid
    public function get5id () {
        $wxuserid = 0;
        $userid = 0;
        $patientid = 0;
        $doctorid = 0;
        $diseaseid = 0;

        $patientid = $this->id;
        $doctorid = $this->doctorid;
        $diseaseid = $this->diseaseid;

        $userid = $this->getUserIdIfOnlyOne();
        $wxuserid = $this->getWxUserIdIfOnlyOne();

        $row = array();
        $row["wxuserid"] = $wxuserid;
        $row["userid"] = $userid;
        $row["patientid"] = $patientid;
        $row["doctorid"] = $doctorid;
        $row["diseaseid"] = $diseaseid;
        return $row;
    }

    // 主就诊卡的医生
    public function getMasterDoctor () {
        return $this->doctor;
    }

    // 就诊卡列表
    public function getPcards () {
        return PcardDao::getListByPatient($this);
    }

    // pcardidarr
    public function getPcardDiseaseidArr () {
        $arr = [];

        foreach ($this->getPcards() as $pcard) {
            $arr[] = $pcard->diseaseid;
        }

        return array_unique($arr);
    }

    // 主就诊卡
    public function getMasterPcard () {
        $cachepcard = Pcard::getFromCache($this->id);
        if ($cachepcard instanceof Pcard) {
            return $cachepcard;
        } else {
            return $this->getPcardByDoctorid($this->doctorid);
        }
    }

    // WxUserAuthBaseAction时获取的pcard
    public function getMyPcard (WxUser $wxuser) {
        $doctor = $wxuser->doctor;
        $wxshop = $wxuser->wxshop;
        $mypcard = null;
        if ($doctor instanceof Doctor) {
            $mypcard = $this->getPcardByDoctorid($doctor->id);
        }

        if (false == $mypcard instanceof Pcard) {
            if ($wxshop instanceof WxShop) {
                $mypcard = $this->getOnePcardByWxshopid($wxshop->id);
                if ($wxshop->id != 3) {
                    // Debug::warn("通过wxshopid获取pcard: wxshopid[$wxshop->id],
                    // patientid[{$this->id}][{$this->name}]");
                }
            } else {
                Debug::warn("因wxshopid不存在, 没有找到对应pcard: patientid[{$this->id}][{$this->name}]");
                return $mypcard;
            }
        }

        if (false == $mypcard instanceof Pcard) {
            $disease = $wxshop->disease;
            if ($disease instanceof Disease) {
                $mypcard = $this->getOnePcardByDiseaseid($disease->id);
            } else {
                Debug::warn("因diseaseid不存在, 没有找到对应pcard: patientid[{$this->id}][{$this->name}]");
            }
        }

        if (false == $mypcard instanceof Pcard) {
            $patient = $wxuser->patient;
            $doctorid = $patient->doctorid;
            $mypcard = PcardDao::getByPatientidDoctorid($patient->id, $doctorid);
        }

        return $mypcard;
    }

    // 先查医生关联的 pcard, 如果没有则取 MasterPcard
    public function getPcardByDoctorOrMasterPcard (Doctor $doctor) {
        $pcard = PcardDao::getByPatientidDoctorid($this->id, $doctor->id);
        if (false == $pcard instanceof Pcard) {
            $pcard = $this->getMasterPcard();
        }

        return $pcard;
    }

    // 患者-医生-就诊卡(最多只有一个)
    public function getPcardByDoctorid ($doctorid) {
        return PcardDao::getByPatientidDoctorid($this->id, $doctorid);
    }

    // 患者-疾病-就诊卡(最新扫码的一个) 20170419 TODO by sjp : 所有调用点都考虑替换掉
    public function getOnePcardByDiseaseid ($diseaseid) {
        return PcardDao::getOneByPatientidDiseaseid($this->id, $diseaseid);
    }

    // 患者-疾病-就诊卡列表, 暂无调用点
    public function getPcardListByDiseaseid ($diseaseid) {
        return PcardDao::getListByPatientidDiseaseid($this->id, $diseaseid);
    }

    // 患者-服务号-就诊卡(最新扫码的一个)
    public function getOnePcardByWxshopid ($wxshopid) {
        return PcardDao::getOneByPatientidWxshopid($this->id, $wxshopid);
    }

    // 患者-服务号-就诊卡列表
    public function getPcardListByWxshopid ($wxshopid) {
        return PcardDao::getListByPatientidWxshopid($this->id, $wxshopid);
    }

    public function getOpenCheckupPictureCnt () {
        return CheckupPictureDao::getCntByPatientidNotOpen($this->id);
    }

    // 获取patient上5个状态的json
    public function getJsonPatientStatus () {
        $arr = array(
            'status' => $this->status,
            'auditstatus' => $this->auditstatus,
            'doctor_audit_status' => $this->doctor_audit_status,
            'is_live' => $this->is_live);

        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    // vip失效，降级
    public function expireVIP () {
        // 是否符合LEVEL_300等级
        if ($this->isLevel_300()) {
            $this->level = PatientLevel::LEVEL_300;
        } elseif ($this->isLevel_200()) {
            // 是否符合LEVEL_200等级
            $this->level = PatientLevel::LEVEL_200;
        } else {
            $this->level = PatientLevel::LEVEL_100;
        }
    }

    public function isLevel_400 () {
        return $this->has_valid_quickpass_service();
    }

    public function isLevel_300 () {
        $shoporder_cnt = ShopOrderDao::getIsPayShopOrderCntByPatient($this);

        return $shoporder_cnt > 0;
    }

    public function isLevel_200 () {
        return $this->doctor->menzhen_offset_daycnt > 0;
    }

    // 禁止在状态机之外直接修改状态
    public function setStatus ($value) {
        // $this->set4lock('status', $value);
        // Debug::warn("直接修改status");
        Debug::error('不允许直接修改status');
        DBC::requireTrue(false, '不允许直接修改status,请去看患者状态机');
    }

    public function setAuditstatus ($value) {
        // $this->set4lock('auditstatus', $value);
        // Debug::warn("直接修改auditstatus");
        Debug::error('不允许直接修改auditstatus');
        DBC::requireTrue(false, '不允许直接修改auditstatus,请去看患者状态机');
    }

    public function setDoctor_audit_status ($value) {
        // $this->set4lock('doctor_audit_status', $value);
        // Debug::warn("直接修改doctor_audit_status");
        Debug::error('不允许直接修改doctor_audit_status');
        DBC::requireTrue(false, '不允许直接修改doctor_audit_status,请去看患者状态机');
    }

    public function setIs_live ($value) {
        // $this->set4lock('is_live', $value);
        // Debug::warn("直接修改is_live");
        Debug::error('不允许直接修改is_live');
        DBC::requireTrue(false, '不允许直接修改is_live,请去看患者状态机');
    }

    public function getLock_titleForAudit (Auditor $myauditor) {
        $str = '锁定';

        if ($this->auditorid == $myauditor->id) {
            $str = "解锁";
        } elseif ($this->auditorid > 0) {
            $str = "抢锁";
        }

        return $str;
    }

    public function sendcall ($toMobile, $fromMobile) {
        if (! $fromMobile) {
            $fromMobile = Config::getConfig('meeting_telephone'); // 主叫号码
        }
        $customerSerNum = Config::getConfig('customer_ser_num'); // 被叫侧显示号码
        $fromSerNum = $customerSerNum;
        $hangupCdrUrl = Config::getConfig('hangup_cdr_host') . '/meeting/callbackhandler';

        Debug::trace(__METHOD__ . ' hangupCdrUrl: ' . $hangupCdrUrl);
        return MobileCallUtil::callBack($toMobile, $fromMobile, $customerSerNum, $fromSerNum, '', '', '', '', '', $hangupCdrUrl);
    }

    // 以患者身上的主疾病为准
    public function getDiseaseGroup () {
        return $this->disease->diseasegroup;
    }

    // 判断患者是否在某个管理计划里
    public function isInMgtPlan ($ename) {
        $mgtplanid = $this->mgtplanid;
        if ($mgtplanid) {
            $mgtPlan = MgtPlan::getById($mgtplanid);
            if ($mgtPlan instanceof MgtPlan) {
                return $ename == $mgtPlan->ename;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // 获取气道狭隘患者发送【呼吸困难量表】日期
    public function getDatesQDXZ () {
        $days = Disease::getDaysQDXZ();

        $createdate = date('Y-m-d', strtotime($this->createtime));

        $dates = [];
        foreach ($days as $day) {
            $dates[] = date('Y-m-d', strtotime($createdate) + $day * 3600 * 24);
        }

        return $dates;
    }

    // 生成气道狭隘患者发送量表计划
    public function createplan_qdxz (WxUser $wxuser) {
        $plan_dates = $this->getDatesQDXZ();

        if (! empty($plan_dates)) {
            foreach ($plan_dates as $plan_date) {
                $row = [];
                $row["wxuserid"] = $wxuser->id;
                $row["userid"] = $wxuser->userid;
                $row["patientid"] = $this->id;
                $row["plan_date"] = $plan_date;
                $row["status"] = 0;
                Plan_qdxz::createByBiz($row);
            }
        }
    }

    // 获取一个有效userid
    public function getOneUserId () {
        $sql = "select id from users where id = (
                    select createuserid
                    from patients
                    where id = {$this->id}
                ) ";
        $userid = Dao::queryValue($sql);

        if ($userid) {
            return $userid;
        } else {
            $sql = "select id from users where patientid = {$this->id} order by id asc limit 1";
            $userid = Dao::queryValue($sql);
            if ($userid) {
                return $userid;
            } else {
                return 0;
            }
        }
    }

    // 是否买过药
    public function hadBuy() {
        $sql = "SELECT count(*) AS cnt FROM shoporders WHERE patientid = :patientid AND is_pay = 1 ";
        $bind = [
            ':patientid' => $this->id
        ];

        $cnt = Dao::queryValue($sql, $bind);
        if ($cnt > 0) {
            return true;
        }

        return false;
    }

    // 是否买过药针对肿瘤的
    public function hadBuyForCancer() {
        // 不论是否支付成功，只要有过下单行为，都算作有效
        $sql = "SELECT count(*) AS cnt
                FROM shoporderitems a
                INNER JOIN shoporders b ON a.shoporderid = b.id
                WHERE b.patientid = :patientid ";
        $bind = [
            ':patientid' => $this->id
        ];

        $cnt = Dao::queryValue($sql, $bind);
        if ($cnt > 0) {
            return true;
        }

        return false;
    }

    /**
     * 取消失访标记
     * 5805 失访激活后系统自动生成+7天的【定期随访】任务
     */
    public function cancelLose() {
        if ($this->is_lose == 1) {
            $this->is_lose = 0;

            // 失访激活后系统自动生成+7天的【定期随访】任务
            if (Disease::isCancer($this->diseaseid)) {
                $plantime = date('Y-m-d', time() + 3600 * 24 * 7);
                OpTaskService::createPatientOpTask($this, 'follow:regular_follow', null, $plantime);
            }
        }
    }

    /**
     * 标记失访
     */
    public function lose() {
        $this->is_lose = 1;
    }

    public function getDeadDate(){
        if($this->is_live == 1){
            return '';
        }

        $patientrecords = PatientRecordDao::getParentListByPatientidCodeType($this->id, 'common', 'dead');
        $patientrecord = $patientrecords[0];
        if($patientrecord instanceof PatientRecord){
            return $patientrecord->thedate;
        }

        return '未知';
    }

    /**
     * 是否爱可泰隆患者
     */
    public function isActelion() {
        return Patient_hezuoDao::getOneByCompanyPatientid('Actelion', $this->id);
    }

    /**
     * 是否拥有未过期且有效地快速通行证服务
     */
    public function has_valid_quickpass_service() {
        $quickpass_serviceitem = QuickPass_ServiceItemDao::getValidOneByPatientAndTime($this->id, date('Y-m-d H:i:s'));
        if ($quickpass_serviceitem instanceof QuickPass_ServiceItem) {
            return true;
        }
        return false;
    }
    /**
     * 是否今日重点患者
     */
    public function isTodayMark () {
        $todaymarks = PatientTodayMarkDao::getListByPatientIdThedate($this->id, date('Y-m-d'));
        
        return !empty($todaymarks);
    }
    
    /**
     * 今日重点患者标签
     */
    public function getTodayMarksStr() {
        $todaymarks = PatientTodayMarkDao::getListByPatientIdThedate($this->id, date('Y-m-d'));
        if (empty($todaymarks)) {
            return '非重点患者';
        } else {
            $arr = [];
            foreach ($todaymarks as $todaymark) {
                $arr[] = $todaymark->title;
            }
    
            return implode(' ', $arr);
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getLevels () {
        // '患者等级 100：普通患者 200：开通开药门诊医生的患者 300：有过购药行为的患者 400：VIP患者'
        $levels = [0, 100, 200, 300, 400];

        return $levels;
    }
}
