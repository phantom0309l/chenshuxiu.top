<?php

/*
 * OpTask
 */
class OpTask extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',
            'diseaseid',
            'optasktplid',
            'patientstageid',   // 关闭任务时候，患者所在阶段
            'opnodeid',  // 所在节点id
            'pgroupid',
            'pipeid',
            'objtype',
            'objid',
            'content',  // 跟进内容
            'audit_remark',  // 运营主管审核备注
            'plantime',  // 计划时间
            'donetime',  // 任务关闭时间
            'level',  // 1 低, 2 普通, 3 高, 4 紧急, 5 立刻
            'level_remark',  // 加急原因
            'status',  // 状态 0:进行中 1:关闭 2:挂起
            'auditorid',  // 责任人
            'createauditorid', // 任务创建人
            'send_status',  // 自动消息发送状态（0：未发送 1：已发送）
            'first_plantime' // 任务首次计划时间
        );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
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
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["optasktpl"] = array(
            "type" => "OpTaskTpl",
            "key" => "optasktplid");
        $this->_belongtos["opnode"] = array(
            "type" => "OpNode",
            "key" => "opnodeid");
        $this->_belongtos["pgroup"] = array(
            "type" => "Pgroup",
            "key" => "pgroupid");
        $this->_belongtos["pipe"] = array(
            "type" => "Pipe",
            "key" => "pipeid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
        $this->_belongtos["createauditor"] = array(
            "type" => "Auditor",
            "key" => "createauditorid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["content"] = $content;
    // $row["audit_remark"] = $audit_remark;
    // $row["plantime"] = $plantime;
    // $row["status"] = $status;
    // $row["auditorid"] = $auditorid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "OpTask::createByBiz row cannot empty");

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
        $default["diseaseid"] = 0;
        $default["optasktplid"] = 0;
        $default["patientstageid"] = 0;
        $default["opnodeid"] = 0;
        $default["pgroupid"] = 0;
        $default["pipeid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["content"] = '';
        $default["audit_remark"] = '';
        $default["plantime"] = date('Y-m-d');
        $default["donetime"] = '0000-00-00 00:00:00';
        $default["level"] = 2;
        $default["level_remark"] = '';
        $default["status"] = 0;
        $default["auditorid"] = 0;
        $default["createauditorid"] = 0;
        $default["send_status"] = 0;

        $row += $default;
        $row["first_plantime"] = $row["plantime"];
        $optask = new self($row);
        // 当有新任务生成时，该患者所有挂起(0:进行中，1:关闭，2:挂起)的任务全部唤醒（NMO方向）
        if ($optask->diseaseid == 3) {
            $optasks_hangup = OpTaskDao::getListByPaitentStatus($optask->patient, 2);
            foreach ($optasks_hangup as $a) {
                $a->status = 0;
            }
        }

        return $optask;
    }

    public static function getAllStatus() {
        return [
            '0' => '进行中',
            '1' => '关闭',
            '2' => '挂起',
        ];
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getLevelStr () {
        $arr = OpTask::getLevelDescArr();
        $level = $this->level;
        return $arr[$level]['name'];
    }

    public function getLevelColor () {
        $arr = OpTask::getLevelDescArr();
        $level = $this->level;
        return $arr[$level]['color'];
    }

    public function getStatusStr () {
        $arr = self::getAllStatus();

        return $arr[$this->status];
    }

    // 已经关闭
    public function isClose () {
        return $this->status == 1;
    }

    // 未关闭
    public function isOpen () {
        return (false == $this->isClose());
    }

    public function getStatusAndOpNodeTitle ($separator = ' - ') {
        return $this->getStatusStr() . $separator . $this->opnode->title;
    }

    public function getOptLogs () {
        return OptLogDao::getListByOptaskid($this->id);
    }

    // 计划日期
    public function getPlanDate () {
        return substr($this->plantime, 0, 10);
    }

    // 计划日期, 精简版
    public function getFixPlantime () {
        $plantime = $this->plantime;
        if ("0000-00-00 00:00:00" == $plantime) {
            $plantime = $this->createtime;
        }

        $str = substr($plantime, 5, 11);

        if (substr($plantime, 11, 8) == '00:00:00') {
            $str = substr($plantime, 5, 5);
        }

        return $str;
    }

    public function getFixDonetime () {
        $donetime = $this->donetime;

        $str = substr($donetime, 5, 11);

        if (substr($donetime, 11, 8) == '00:00:00') {
            $str = substr($donetime, 5, 5);
        }

        return $str;
    }

    public function getOwnerNames () {
        $str = "";
        $diseaseid = $this->diseaseid;
        if ($diseaseid != 1) {
            return $str;
        }
        $pgroup = $this->pgroup;
        $pgroupid = 0;
        if ($pgroup instanceof Pgroup) {
            $pgroupid = $pgroup->id;
        }

        $optasktplid = $this->optasktplid;

        $cond = " and pgroupid = :pgroupid and status=1 and auditorid not in (10003,10004,10006,10041) ";
        $bind = [];
        $bind[':pgroupid'] = $pgroupid;
        $auditorpgrouprefs = Dao::getEntityListByCond('AuditorPgroupRef', $cond, $bind);

        $ids = array();
        foreach ($auditorpgrouprefs as $a) {
            $ids[] = $a->auditor->id;
        }

        $idsStr = implode(",", $ids);
        if ($idsStr) {

            $cond = " and optasktplid = :optasktplid and auditorid in ({$idsStr}) ";
            $bind = [];
            $bind[':optasktplid'] = $optasktplid;
            $optasktplauditorrefs = Dao::getEntityListByCond("OpTaskTplAuditorRef", $cond, $bind);

            $temp = array();
            foreach ($optasktplauditorrefs as $a) {
                $temp[] = $a->auditor->name;
            }
            $str = implode(",", $temp);
        }

        return $str;
    }

    public function close () {
        $this->status = 1;
        $this->donetime = date('Y-m-d H:i:s');
    }

    public function isClosed () {
        return 1 == $this->status;
    }

    /**
     * 获取任务颜色
     */
    public function getColor () {
        $plantime = substr($this->plantime, 0, 10);
        if ($plantime <= date('Y-m-d')) {
            $color = 'red';
        } elseif ($plantime > date('Y-m-d') && $this->plantime <= date('Y-m-d', time() + 3600 * 24 * 7)) {
            $color = 'orange';
        } else {
            $color = 'green';
        }

        return $color;
    }

    public function getEvaluateList () {
        $plantime = $this->plantime;
        $fromtime = date('Y-m-d 19:00:00', strtotime($plantime) - 3600 * 24 * 1);
        $totime = date('Y-m-d 19:00:00', strtotime($plantime));

        $cond = " and patientid=:patientid and createtime>:fromtime and createtime<=:totime order by id asc";
        $bind = array(
            ':patientid' => $this->patientid,
            ':fromtime' => $fromtime,
            ':totime' => $totime);

        return Dao::getEntityListByCond('Paper', $cond, $bind);
    }

    public function getCreateAuditorName () {
        $name = '';
        if ($this->createauditor) {
            $name = $this->createauditor->name;
        } else {
            $name = '未知';
        }
        return $name;
    }

    // 下面的流
    public function getNextOpNodeFlows () {
        return OpNodeFlowDao::getListByFrom_opnode($this->opnode);
    }

    // 下面的节点
    public function getTheOpnodeAllOpNodeFlow () {
        $opnodeflows = OpNodeFlowDao::getListByFrom_opnode($this->opnode);

        $arr = [];
        $arr["0|not"] = "未选择";
        foreach ($opnodeflows as $a) {
            if ($a->type == 'manual') {
                $arr["{$a->id}|{$a->to_opnode->code}|{$this->objtype}|{$a->to_opnode->is_show_next_plantime}"] = $a->to_opnode->title;
            }
        }

        return $arr;
    }

    public function getPlanTxtMsgs () {
        $plantxtmsgs = Plan_txtMsgDao::getListByObj($this);
        return $plantxtmsgs;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getLevelStrByLevel ($level) {
        $arr = OpTask::getLevelDescArr();
        return $arr[$level]['name'];
    }

    public static function getDealMinutesColorByLevel ($level) {
        $arr = OpTask::getLevelDescArr();
        return $arr[$level]['dealminutes'];
    }

    public static function getLevelWordColorByLevel ($level) {
        $arr = OpTask::getLevelDescArr();
        return $arr[$level]['wordcolor'];
    }
    public static function getLevelStrArr () {
        return [
            "1"=>"低",
            "2"=>"普通",
            "3"=>"高",
            "4"=>"紧急",
            "5"=>"立刻",
            "9"=>"最高",
        ];
    }
    public static function getLevelDescArr () {
        $arr = array(
            '1' => array(
                'name' => '低',
                'class' => '',
                'dealminutes' => '120',
                'wordcolor' => '#ccc',
                'color' => '#FFCCCC'),
            '2' => array(
                'name' => '普通',
                'class' => '',
                'dealminutes' => '60',
                'wordcolor' => '#999',
                'color' => '#fff'),
            '3' => array(
                'name' => '高',
                'class' => '',
                'dealminutes' => '45',
                'wordcolor' => '#f3b760',
                'color' => '#ffe7be'),
            '4' => array(
                'name' => '紧急',
                'class' => '',
                'dealminutes' => '10',
                'wordcolor' => '#d26a5c',
                'color' => '#ffcfa7'),
            '5' => array(
                'name' => '立刻',
                'class' => '',
                'dealminutes' => '30',
                'wordcolor' => '#ff0000',
                'color' => '#fab89b'),
            '9' => array(
                'name' => '最高',
                'class' => '',
                'dealminutes' => '10',
                'wordcolor' => '#ff0000',
                'color' => '#fab89b'));
        return $arr;
    }

    public static function getLevelRemark ($str) {
        $arr = OpTask::getLevelRemarkArr();
        return $arr[$str];
    }

    public static function getLevelRemarkArr () {
        $arr = array();
        $arr['afterpay'] = '下单后两天内的消息任务直接判定为紧急';
        $arr['because_ai'] = 'AI处理后判定为紧急';
        $arr['urgent_word'] = '含有紧急词汇的判定为紧急';
        $arr['trans_question'] = '快递 + 疑问词场景判定为紧急';
        $arr['trans_worry'] = '快递 + 负面情绪场景判定为紧急';
        $arr['menzhen_queation'] = '门诊 + 疑问词场景判定为紧急';
        return $arr;
    }
}
