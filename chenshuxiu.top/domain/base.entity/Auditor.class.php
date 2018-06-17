<?php

/*
 * Auditor 运营人员表
 */

class Auditor extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'userid',  // userid
            'pictureid',  // pictureid头像
            'name',  //
            'type', // 员工类型，1：正式，2：兼职
            'auditroleids',  // 角色
            'diseasegroupid', //疾病组
            'auditorid_prev', // 推荐人
            'qr_ticket',
            'status',  // 0 表示离职 1 表示在职
            'can_send_msg',  // 是否能够发消息:1、可发送；2、不可发送
            'xprovinceid_control', // 市场管辖省
            'standard_date', // 达标日期
            'cdr_no1', // IP电话座席号
            'cdr_no2', // 个人电话座席号
            'is_auto_lock_patient',  // 自动锁定患者开关
            'remark'); // 备注
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'userid');
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
        $this->_belongtos["diseasegroup"] = array(
            "type" => "DiseaseGroup",
            "key" => "diseasegroupid");
        $this->_belongtos["prevauditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid_prev");
        $this->_belongtos["controlxprovince"] = array(
            "type" => "Xprovince",
            "key" => "xprovinceid_control");
    }

    // $row = array();
    // $row["userid"] = $userid;
    // $row["name"] = $name;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "Auditor::createByBiz row cannot empty");

        $default = array();
        $default["userid"] = 0;
        $default["pictureid"] = 0;
        $default["name"] = '';
        $default["type"] = 1;
        $default["auditroleids"] = '';
        $default["diseasegroupid"] = 0;
        $default["auditorid_prev"] = 0;
        $default["qr_ticket"] = '';
        $default["status"] = 1;
        $default["can_send_msg"] = 1;
        $default["xprovinceid_control"] = 0;
        $default["standard_date"] = '0000-00-00';
        $default["cdr_no1"] = '';
        $default["cdr_no2"] = '';
        $default["is_auto_lock_patient"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function isSuperman() {
        return $this->isHasRole(array(
            'super'));
    }

    // 管理员
    public function isAdmin() {
        return $this->isHasRole(array(
            'admin'));
    }

    // 运营
    public function isYunying() {
        return $this->isHasRole(array(
            'yunying',
            'yunyingmgr'));
    }

    // 随访中心301
    public function isSuifang301 () {
        return $this->isHasRole(['follow_301']);
    }

    // 是否离职
    public function isLeave() {
        return $this->status == 0;
    }

    // 兼职或专职
    public function getTypeDesc() {
        $arr = [];
        $arr[0] = '其他';
        $arr[1] = '正式';
        $arr[2] = '兼职';

        return $arr[$this->type];
    }

    public function canAuditorMgr() {

        // 非线上环境都可以
        if (Config::getConfig('env') != 'production' || $this->name == '史建平') {
            return true;
        }

        if ($this->isHasRole(array(
            'admin'))) {
            return true;
        }

        return false;
    }

    public function getQrTicket() {
        $qr_ticket = $this->qr_ticket;
        if (empty($qr_ticket)) {
            $wxshop = WxShop::getById(1);
            $access_token = $wxshop->getAccessToken();
            $scene_str = "Auditor_{$this->id}";
            $qr_ticket = WxApi::getQrTicket($access_token, $scene_str);
            $this->qr_ticket = $qr_ticket;
        }
        return $this->qr_ticket;
    }

    public function hasBindPgroup($pgroupid) {
        $auditorpgroupref = AuditorPgroupRefDao::getOneByAuditoridPgroupid($this->id, $pgroupid, " and status = 1");
        return $auditorpgroupref instanceof AuditorPgroupRef;
    }

    public function hasBindOptasktpl($optasktplid) {
        $optasktplauditorref = OpTaskTplAuditorRefDao::getOneByOptasktplidAuditorid($optasktplid, $this->id);
        return $optasktplauditorref instanceof OpTaskTplAuditorRef;
    }

    public function hasBindDisease($diseaseid) {
        $auditordiseaseref = AuditorDiseaseRefDao::getOneByAuditoridDiseaseid($this->id, $diseaseid);
        return $auditordiseaseref instanceof AuditorDiseaseRef;
    }

    public function hasBindPushMsgTpl($auditorpushmsgtplid) {
        $auditorPushMsgTplRef = AuditorPushMsgTplRefDao::getByAuditoridAndPushMsgTplid($this->id, $auditorpushmsgtplid, " and can_ops = 1 ");
        return $auditorPushMsgTplRef instanceof AuditorPushMsgTplRef;
    }

    public function canHandleOptask($optask) {
        $flag = true;
        $disease = $optask->disease;
        $pgroupid = $optask->pgroupid;
        $optasktplid = $optask->optasktplid;
        if (false == $disease instanceof Disease) {
            return $flag;
        }
        if (1 == $disease->id) {
            // 绑定任务类型
            $flag = $this->hasBindOptasktpl($optasktplid);
        } else {
            // 其他疾病暂时没有配置的需求，有需求后再加逻辑
        }
        return $flag;
    }

    public function getPgroupidsStr() {
        $auditorpgrouprefs = AuditorPgroupRefDao::getListByAuditorid($this->id);

        $arr = array();
        foreach ($auditorpgrouprefs as $a) {
            $arr[] = $a->pgroupid;
        }

        $str = "-1";
        if (count($arr)) {
            $str = implode(",", $arr);
        }
        return $str;
    }

    public function getOptasktplidsStr() {
        $cond = " and auditorid = :auditorid ";

        $bind = [];
        $bind[':auditorid'] = $this->id;

        $optasktplauditorrefs = Dao::getEntityListByCond("OpTaskTplAuditorRef", $cond, $bind);

        $arr = array();
        foreach ($optasktplauditorrefs as $a) {
            $arr[] = $a->optasktplid;
        }

        $str = "";
        if (count($arr)) {
            $str = implode(",", $arr);
        }
        return $str;
    }

    public function getAuditRoleIdArr() {
        $auditroleids = trim($this->auditroleids);
        if ($auditroleids == "") {
            return [];
        }

        return explode(',', $auditroleids);
    }

    // 仅仅这个角色
    public function isOnlyOneRole($rolecode) {
        $arr = $this->getAuditRoleIdArr();

        if (1 != count($arr)) {
            return false;
        }

        $auditrole = AuditRole::getById($arr[0]);
        if (false == $auditrole instanceof AuditRole) {
            return false;
        }

        if ($rolecode == $auditrole->code) {
            return true;
        }

        return false;
    }

    // 具有这个角色
    public function isHasRole($rolecodearr = array()) {
        $auditroleidarr = $this->getAuditRoleIdArr();

        foreach ($auditroleidarr as $auditroleid) {
            $auditrole = AuditRole::getById($auditroleid);

            if (false == $auditrole instanceof AuditRole) {
                continue;
            }

            if (in_array($auditrole->code, $rolecodearr)) {
                return true;
            }
        }

        return false;
    }

    // 具有这个权限, 求交集
    public function isHasAuth(AuditResource $auditresource) {
        if (empty(array_intersect($this->getAuditRoleIdArr(), $auditresource->getAuditRoleIdArr()))) {
            return false;
        } else {
            return true;
        }
    }

    // 获取运营绑定的疾病，id数组
    public function getDiseaseIdArr() {
        $auditordiseaserefs = AuditorDiseaseRefDao::getListByAuditor($this);

        $ids = [];
        foreach ($auditordiseaserefs as $a) {
            $ids[] = $a->diseaseid;
        }

        return $ids;
    }

    // 该运营负责的医生是否有新消息
    public function isDoctorHasNewPipe() {
        $sql = "select a.id
                from doctors a
                inner join doctordiseaserefs b on b.doctorid = a.id
                inner join auditordiseaserefs c on c.diseaseid = b.diseaseid
                where c.auditorid = :auditorid and a.is_new_pipe = 1 limit 1  ";
        $bind = [];
        $bind[':auditorid'] = $this->id;

        $doctorid = Dao::queryValue($sql, $bind);
        if ($doctorid) {
            return 1;
        }

        return 0;
    }

    // 该运营负责的疾病是否有需要审核的患者
    public function needAuditPatientCnt() {
        $diseaseids = $this->getDiseaseIdArr();
        if (empty($diseaseids)) {
            return false;
        }

        $diseaseidstr = implode(',', $diseaseids);

        // 报到optasktpl
        $optasktpl_baodao = OpTaskTplDao::getOneByUnicode('baodao:Patient');

        $sql = "select count(*)
            from optasks
            where optasktplid = {$optasktpl_baodao->id} and status = 0 and diseaseid in ($diseaseidstr) and doctorid != 33 ";
        $cnt = Dao::queryValue($sql);

        return $cnt;
    }

    // 判断运营能否访问资源
    public function canVisitAuditResource($auditresource) {
        $auditroleidarr = $this->getAuditRoleIdArr();
        if (false == $auditresource instanceof AuditResource) {
            return false;
        }

        // 角色判断
        if (empty(array_intersect($auditroleidarr, $auditresource->getAuditRoleIdArr()))) {
            return false;
        }

        // 疾病判断
        if (false == $this->canVisitAuditResourceOnlyCheckDisease($auditresource)) {
            return false;
        }

        return true;
    }

    private function canVisitAuditResourceOnlyCheckDisease($auditresource) {
        $the_diseasegroupid = $auditresource->diseasegroupid;
        if ($the_diseasegroupid == 0) {
            return true;
        }
        $auditordiseaserefs = AuditorDiseaseRefDao::getListByAuditor($this);
        foreach ($auditordiseaserefs as $auditordiseaseref) {
            $diseasegroupid = $auditordiseaseref->disease->diseasegroupid;
            if ($diseasegroupid == $the_diseasegroupid) {
                return true;
            }
        }
        return false;
    }

    /**
     * 待处理的快速咨询数量
     *
     * @return null
     */
    public function getPendingQuickConsultOrderCount() {
        $diseaseids = $this->getDiseaseIdArr();
        if (empty($diseaseids)) {
            return 0;
        }
        $ids = implode(',', $diseaseids);
        return QuickConsultOrderDao::getPendingCountByDiseaseids($ids);
    }

    /**
     * 待处理的快速通行证消息任务的患者数
     *
     * @return null
     */
    public function getQuickPassWaitAuditorReplyPatientCount() {
        $diseaseids = $this->getDiseaseIdArr();
        if (empty($diseaseids)) {
            return 0;
        }
        $ids = implode(',', $diseaseids);

        $sql = "SELECT COUNT(a.patientid)
                FROM optasks a
                INNER JOIN opnodes b ON a.opnodeid = b.id
                INNER JOIN optasktpls c ON a.optasktplid = c.id
                WHERE a.diseaseid IN ({$ids})
                AND c.code = 'PatientMsg' AND c.subcode = 'quickpass_msg'
                AND (b.code = 'wait_auditor_reply' OR b.code = 'root')
                AND a.status IN (0, 2)
                GROUP BY a.patientid ";
        return Dao::queryValue($sql);
    }

    // 锁定的患者数
    public function getLock_patient_cnt() {
        $sql = " select count(*) from patients where auditorid=:auditorid ";
        $bind = [];
        $bind[':auditorid'] = $this->id;
        return Dao::queryValue($sql, $bind);
    }

    //多动症疾病组
    public function isADHDDiseaseGroup() {
        return 2 == $this->diseasegroupid;
    }

    public function isInOneTypeAuditorGroup($type, $ename) {
        $auditorGroup = AuditorGroupDao::getByTypeAndEname($type, $ename);
        if ($auditorGroup instanceof AuditorGroup) {
            $auditorIds = AuditorGroupRefDao::getAuditorIdsByAuditorGroupId($auditorGroup->id);
            return in_array($this->id, $auditorIds);
        } else {
            return false;
        }
    }

    // 管理报到时长0~55天的患者的运营
    public function isManageADHDTwoMonth() {
        return $this->isInOneTypeAuditorGroup('auditor', 'ManageADHDTwoMonth');
    }

    // 管理报到时长56天及以上的患者的运营
    public function isManageADHDTwoMonthLater() {
        return $this->isInOneTypeAuditorGroup('auditor', 'ManageADHDTwoMonthLater');
    }

    // 管理礼来项目中的患者的运营
    public function isManageADHDSunflower() {
        return $this->isInOneTypeAuditorGroup('auditor', 'ManageADHDSunflower');
    }

    // 管理六院项目中的患者的运营
    public function isManageADHDLiuyuan() {
        return $this->isInOneTypeAuditorGroup('auditor', 'ManageADHDLiuyuan');
    }

    // ADHD主管
    public function isManageADHDMaster() {
        return $this->isInOneTypeAuditorGroup('auditor', 'ManageADHDMaster');
    }

    //肿瘤组
    public function isCancerDiseaseGroup() {
        return 3 == $this->diseasegroupid;
    }

    //多疾病组
    public function isMulDiseaseGroup() {
        $arr = [10040, 10069];
        return in_array($this->id, $arr);
    }

    //多疾病组一小队
    public function isMulDiseaseGroupOne() {
        return $this->isInOneTypeAuditorGroup('auditor', 'MulDiseaseGroupOne');
    }

    //多疾病组二小队
    public function isMulDiseaseGroupTwo() {
        return $this->isInOneTypeAuditorGroup('auditor', 'MulDiseaseGroupTwo');
    }

    public function getHeadImgUrl ($width=100,$height=100) {
        if ($this->pictureid == 0) {
            return Config::getConfig("img_uri").'/static/img/audit/default_auditor_header.jpg';
        }else {
            return $this->picture->getSrc($width,$height);
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 系统角色的Auditor
    public static function getSystemAuditor() {
        return Auditor::getById(1);
    }
}
