<?php

/*
 * Doctor 医生
 */
class Doctor extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'userid',  // userid
            'hospitalid',  // hospitalid
            'name',  // 姓名
            'sex',  // 性别, 值为1时是男性，值为2时是女性，值为0时是未知
            'title',  // 职称
            'department',  // 部门科室
            'headimg_pictureid',  // 头像图片id
            'code',  // 编码
            'pdoctorid',  // 编码
            'patients_referencing',  // 帮其他医生维护患者,旧字段
            'first_patient_date',  // 首个患者加入时间
            'is_sign', //医生是否签约，0：未签约，1：已签约
            'menzhen_offset_daycnt',  // 医生允许患者报到n天后开启门诊，值为0时，表示永不开启
            'menzhen_pass_date',  // 门诊开通时间
            'is_audit_chufang', //是否需要审核处方
            'audit_chufang_pass_time', //延伸处方(续方)审核通过时间
            'brief',  // 医生简介
            'be_good_at',  // 擅长
            'tip',  // 医生挂的通知
            'scheduletip',  // 医生门诊变更通知
            'bulletin',  // 医生门诊公告
            'is_bulletin_show',  // 是否在患者端展示
            'auditorid_yunying',  // 运营负责人
            'auditorid_market',  // 市场责任人
            'auditorid_createby',  // 创建人
            'status',  // 状态
            'auditstatus',  // 审核状态
            'auditorid',  // auditorid
            'doctorgroupid',  // 医生组id
            'auditremark',  // 审核备注
            'service_remark',  // 服务备注
            'hospital_name',  // 医生自提交医院名称
            'mobile',  // 医生手机号
            'email',    // email
            'old_doctorid', // 旧doctorid
            'remark',   // 备注
            'sourcestr',  // 来源渠道
            'is_new_pipe',  // 是否有新的流
            'module_pushmsg',  // 模块开关,是否显示医生和患者交流
            'module_audit',  // 模块开关,是否医生审核患者报到
            'bedtkt_pass_content',  // 约床位,医生默认通过理由
            'bedtkt_refuse_content',  // 约床位,医生默认拒绝理由
            'is_allow_bedtkt',  // 约床位,医生默认拒绝理由
            'is_treatment_notice',  // 是否开启就诊须知。0：没有；1：有
            'lastcachetime',  // 最后缓存时间
            'lastreadtasktime',  // 最后看任务的时间r
            'lastschedule_updatetime', // 医生门诊最近更新时间
            'is_alk'    // 是否为ALK项目
        );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'userid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["headimg_picture"] = array(
            "type" => "Picture",
            "key" => "headimg_pictureid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["pdoctor"] = array(
            "type" => "Doctor",
            "key" => "pdoctorid");
        $this->_belongtos["hospital"] = array(
            "type" => "Hospital",
            "key" => "hospitalid");
        $this->_belongtos["yunyingauditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid_yunying");
        $this->_belongtos["marketauditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid_market");
        $this->_belongtos["createbyauditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid_createby");
        $this->_belongtos["doctorgroup"] = array(
            "type" => "DoctorGroup",
            "key" => "doctorgroupid");
    }

    // 获取账户对象
    public function getAccount ($code = 'doctor_rmb', $unit = Unit::rmb) {
        return $this->user->getAccount($code, $unit);
    }

    public function getAccountRmbBalance_yuan () {
        $account = Account::getByUserAndCodeImp($this->user, 'doctor_rmb');
        if(false == $account instanceof Account){
            return "0.00";
        }
        return sprintf("%.2f", $account->balance / 100);
    }

    public function getTreatmentLesson(){
        return LessonDao::getTreatmentLessonByDoctor($this);
    }

    // 判断医生是否有开药门诊
    public function hasMenzhen () {
        return $this->menzhen_offset_daycnt > 0;
    }

    // 是否以科室为维度看病
    public function hasPdoctor () {
        return $this->pdoctorid > 0;
    }

    // 使用26项的SNAP-IV量表
    public function useAdhd_ivOf26 () {
        if (($this->hospitalid == 89) || ($this->id == 6)) {
            return true;
        } else {
            return false;
        }
    }

    public function isYangLi () {
        return 1 == $this->id;
    }

    // 判断是否为戚元丽医生
    public function isQiYuanLi () {
        $flag = false;
        if ($this->id == 48) {
            $flag = true;
        }
        return $flag;
    }

    // 使用18项的SNAP-IV量表
    public function notUseAdhd_ivOf26 () {
        return false == $this->useAdhd_ivOf26();
    }

    // === DoctorDiseaseRef === begin ===

    // getDiseases
    public function getDiseases () {
        $arr = array();
        foreach (DoctorDiseaseRefDao::getListByDoctor($this) as $ref) {
            $arr[] = $ref->disease;
        }

        return $arr;
    }

    // getDiseaseIdArray
    public function getDiseaseIdArray () {
        $arr = array();
        foreach (DoctorDiseaseRefDao::getListByDoctor($this) as $ref) {
            $arr[] = $ref->diseaseid;
        }

        return $arr;
    }

    // 疾病名字符串
    public function getDiseaseNamesStr () {
        $str = '';
        foreach (DoctorDiseaseRefDao::getListByDoctor($this) as $ref) {
            $str .= $ref->disease->name;
            $str .= " ";
        }

        return trim($str);
    }

    // 合作医生
    public function isHezuo ($company) {
        $doctor_hezuo = Doctor_hezuoDao::getOneByCompanyDoctorid($company, $this->id, " AND status = 1 ");
        return $doctor_hezuo instanceof Doctor_hezuo;
    }

    // 方寸儿童管理服务平台医生
    public function isAdhdDoctor () {
        return $this->isBindDisease(1);
    }

    // 是否为多疾病医生
    public function isMultiDiseaseDoctor () {
        $not_in = array(
            '1',
            '8',
            '15',
            '19',
            '21'); // todo 除这几个id以外的为多疾病
        $doctorDiseaseRefs = DoctorDiseaseRefDao::getListByDoctor($this);
        foreach ($doctorDiseaseRefs as $ref) {
            if (true == in_array($ref->diseaseid, $not_in)) {
                return false;
            }
        }
        return true;
    }

    // 脱髓鞘病医生
    public function isIDDCNSDoctor () {
        return $this->isBindDisease(3);
    }

    // 绑定某种疾病
    public function isBindDisease ($diseaseid) {
        $doctorDiseaseRefs = DoctorDiseaseRefDao::getListByDoctor($this);
        foreach ($doctorDiseaseRefs as $ref) {
            if ($ref->diseaseid == $diseaseid) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取医生开通的药品
     */
    public function getDoctorShopProductRefs() {
        return DoctorShopProductRefDao::getListByDoctor($this);
    }

    public function hasBindShopProduct (ShopProduct $shopProduct) {
        $doctorShopProductRef = DoctorShopProductRefDao::getOneByDoctorShopProduct($this, $shopProduct);
        return $doctorShopProductRef instanceof DoctorShopProductRef;
    }

    //判断医生是否能够绑定正丁
    public function canBindZhengding(){
        $isAdhdDoctor = $this->isAdhdDoctor();
        if($isAdhdDoctor){
            $hospital = $this->hospital;
            if($hospital->canPublicZhengding()){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    // 获取唯一的疾病
    public function getDiseaseIfOnlyOne () {
        $refs = DoctorDiseaseRefDao::getListByDoctor($this);
        if (count($refs) != 1) {
            return null;
        }

        $ref = array_shift($refs);
        return $ref->disease;
    }

    // 获取主疾病
    public function getMasterDisease () {
        $ref = $this->getMasterDoctorDiseaseRef();
        return $ref->disease;
    }

    // getMasterDoctorDiseaseRef
    public function getMasterDoctorDiseaseRef () {
        $arr = $this->getDoctorDiseaseRefs();

        return array_shift($arr);
    }

    // getDoctorDiseaseRefs
    public function getDoctorDiseaseRefs () {
        return DoctorDiseaseRefDao::getListByDoctor($this);
    }

    // === DoctorDiseaseRef === end ===

    // === DoctorWxShopRef === begin ===

    // 获取唯一的Wxshop
    public function getWxShopIfOnlyOne () {
        $refs = DoctorWxShopRefDao::getDefaultListByDoctor($this);
        if (count($refs) != 1) {
            return null;
        }

        $ref = array_shift($refs);
        return $ref->wxshop;
    }

    // getMasterDoctorWxShopRef
    public function getMasterDoctorWxShopRef () {
        $arr = DoctorWxShopRefDao::getDefaultListByDoctor($this);
        return array_shift($arr);
    }

    // getDoctorWxShopRefs
    public function getDoctorWxShopRefs () {
        return DoctorWxShopRefDao::getDefaultListByDoctor($this);
    }

    // 服务号,字符串
    public function getWxShopNamesStr () {
        $str = '';
        foreach (DoctorWxShopRefDao::getDefaultListByDoctor($this) as $ref) {
            $str .= $ref->wxshop->name;
            $str .= " ";
        }

        return trim($str);
    }

    // === DoctorWxShopRef === end ===

    // === 医生收益相关 === begin ===

    //获取一个月的医生收益 传入 2017-08-01
    public function getShouyiOfTheMonth($themonth){
        $shopOrderShouyi = $this->getShopOrderShouyiByThemonth($themonth);
        return $shopOrderShouyi;
    }

    //获取活跃患者收益，按月
    public function getActivePatientShouyiByThemonth($themonth){
        $num = 0;
        $a = DoctorSettleOrderDao::getByDoctoridAndDateYm($this->id, $themonth);
        if($a instanceof DoctorSettleOrder){
            $num = $a->amount;
        }
        return $num;
    }

    public function getShopOrderShouyiByThemonth($themonth){
        $l_date = $themonth;
        $r_date = date( "Y-m-d", strtotime( "{$themonth} +1 month" ) );
        $r_date = date("Y-m-d", strtotime($r_date)-86400);
        return $this->getShopOrderShouyi($l_date, $r_date);
    }

    //shoporder收益 首单+服务
    public function getShopOrderShouyi($l_date, $r_date){
        $cond = " and the_doctorid = :doctorid and time_pay >= :l_date and time_pay < :r_date";
        $bind = [];
        $bind[':doctorid'] = $this->id;
        $bind[':l_date'] = $l_date;
        $r_date = date("Y-m-d", strtotime($r_date)+86400);
        $bind[':r_date'] = $r_date;
        $shopOrders = Dao::getEntityListByCond('ShopOrder', $cond, $bind);
        $first = 0;
        $service_percent = 0;
        foreach ($shopOrders as $shopOrder) {
            if ($shopOrder instanceof ShopOrder) {

                if(false == $shopOrder->isValid()){
                    continue;
                }
                //首单且是药品
                if(1 == $shopOrder->pos && ShopOrder::type_chufang == $shopOrder->type){
                    $first += 50;
                }

                $service_percent += $shopOrder->getService_yuan();
            }
        }
        return ($first + $service_percent);
    }

    public function getShopOrderShouyi_firstByThemonth($themonth){
        $l_date = $themonth;
        $r_date = date( "Y-m-d", strtotime( "{$themonth} +1 month" ) );
        $r_date = date("Y-m-d", strtotime($r_date)-86400);
        return $this->getShopOrderShouyi_first($l_date, $r_date);
    }

    //shoporder首单收益
    public function getShopOrderShouyi_first($l_date, $r_date){
        $cond = " and the_doctorid = :doctorid and time_pay >= :l_date and time_pay < :r_date";
        $bind = [];
        $bind[':doctorid'] = $this->id;
        $bind[':l_date'] = $l_date;
        $r_date = date("Y-m-d", strtotime($r_date)+86400);
        $bind[':r_date'] = $r_date;
        $shopOrders = Dao::getEntityListByCond('ShopOrder', $cond, $bind);
        $first = 0;
        foreach ($shopOrders as $shopOrder) {
            if ($shopOrder instanceof ShopOrder) {

                if(false == $shopOrder->isValid()){
                    continue;
                }
                //首单且是药品
                if(1 == $shopOrder->pos && ShopOrder::type_chufang == $shopOrder->type){
                    $first += 50;
                }
            }
        }
        return $first;
    }

    public function getShopOrderShouyi_serviceByThemonth($themonth){
        $l_date = $themonth;
        $r_date = date( "Y-m-d", strtotime( "{$themonth} +1 month" ) );
        $r_date = date("Y-m-d", strtotime($r_date)-86400);
        return $this->getShopOrderShouyi_service($l_date, $r_date);
    }

    //shoporder服务收益
    public function getShopOrderShouyi_service($l_date, $r_date){
        $cond = " and the_doctorid = :doctorid and time_pay >= :l_date and time_pay < :r_date";
        $bind = [];
        $bind[':doctorid'] = $this->id;
        $bind[':l_date'] = $l_date;
        $r_date = date("Y-m-d", strtotime($r_date)+86400);
        $bind[':r_date'] = $r_date;
        $shopOrders = Dao::getEntityListByCond('ShopOrder', $cond, $bind);
        $service_percent = 0;
        foreach ($shopOrders as $shopOrder) {
            if ($shopOrder instanceof ShopOrder) {

                if(false == $shopOrder->isValid()){
                    continue;
                }

                $service_percent += $shopOrder->getService_yuan();
            }
        }
        return $service_percent;
    }

    public function getDoctorServiceOrdersAmount_yuan($the_month){
        $amountSum = DoctorServiceOrderDao::getAmountSumByDoctorThe_month($this, $the_month);
        return sprintf("%.2f", $amountSum / 100);
    }

    // === 医生收益相关 === end ===

    // 是测试医生
    public function isTest () {
        return $this->hospitalid == 5;
    }

    // 获取负责的医生的idsstr
    public function getDoctorIdsStr () {
        return $this->id . $this->patients_referencing;
    }

    // 医生账号切换到科室
    public function change2Department () {
        if ($this->id == '19' || $this->id == '20') {
            $departmentDoctor = Doctor::getById('35');
            return $departmentDoctor;
        }
        return $this;
    }

    // 获取出诊表
    public function getScheduleTpls () {
        return ScheduleTplDao::getListByDoctor($this);
    }

    // 医生是否为肿瘤
    public function isCancer () {
        $cancer_diseaseidstr = Disease::getCancerDiseaseidsStr();

        $sql = "select count(*) as cnt
                from doctordiseaserefs
                where doctorid = {$this->id} and diseaseid in ($cancer_diseaseidstr) ";
        $cnt = Dao::queryValue($sql);

        return $cnt > 0;
    }

    // 获取pcard列表
    public function getPcards ($diseaseid = 0) {
        if ($diseaseid > 0) {
            return PcardDao::getListByDoctoridDiseaseid($this->id, $diseaseid);
        } else {
            return PcardDao::getListByDoctor($this);
        }
    }

    // 获取患者列表
    public function getPatients ($diseaseid = 0) {
        $pcards = $this->getPcards($diseaseid);
        $patients = array();
        foreach ($pcards as $a) {
            $patients[] = $a->patient;
        }

        return $patients;
    }

    // 获取登录 token
    public function getToken () {
        return $this->user->token;
    }

    // 更新cache时间
    public function modifyCacheTime () {
        $this->lastcachetime = XDateTime::now();
    }

    // 更新最后阅读任务时间
    public function modifyLastReadTaskTime () {
        $this->lastreadtasktime = XDateTime::now();
    }

    public function getWxUsers () {
        $wxusers = WxUserDao::getListByUserId($this->userid);
        return $wxusers;
    }

    // 有用的的统计数据 不用图表 version 1.07
    public function getUsefulCnt () {
        $arr = array();

        // all patients
        $entitys = $this->getPatients();
        $item1 = array();
        $item1['title'] = "全部\n报到患者";
        $item1['cnt'] = count($entitys);
        $item1['patientids'] = FUtil::entitysToIdsStr($entitys);

        // 近期天扫码
        $entitys = PatientDao::getPatients_lastdays($this->id);
        $item2 = array();
        $item2['title'] = "一个月\n扫码";
        $item2['cnt'] = count($entitys);
        $item2['patientids'] = FUtil::entitysToIdsStr($entitys);

        // 近期天扫码报到
        $entitys = PatientDao::getPatients_lastdays_scan_baodao($this->id);
        $item3 = array();
        $item3['title'] = "一个月\n扫码报到";
        $item3['cnt'] = count($entitys);
        $item3['patientids'] = FUtil::entitysToIdsStr($entitys);

        // 近期天非扫码报到
        $entitys = PatientDao::getPatients_lastdays_notscan($this->id);
        $item4 = array();
        $item4['title'] = "一个月\n非扫码报到";
        $item4['cnt'] = count($entitys);
        $item4['patientids'] = FUtil::entitysToIdsStr($entitys);

        $arr[] = $item1;
        $arr[] = $item2;
        $arr[] = $item3;
        $arr[] = $item4;

        return $arr;
    }

    // 增长数据 version 1.08
    public function getIncreaseStatistics ($days = 14) {
        $diseaseid = $this->getMasterDisease()->id;

        $arr = array();

        // 新增报到患者
        $entitysAndTotalCnt = PatientDao::getPatients_lastdays_baodao($this->id, $days);
        $entitys = $entitysAndTotalCnt[0];
        $item = array();
        $item['title'] = "新增报到患者";
        $item['cnt'] = count($entitys);
        $item['totalcnt'] = '(共' . $entitysAndTotalCnt[1] . '人)';
        $item['last_pipe'] = "报到";
        $item['patientids'] = FUtil::entitysToIdsStr($entitys);

        $img_uri = Config::getConfig("img_uri");

        $array1 = array(
            'grouptitle' => '新增报到患者',
            'color' => '#64b7f0',
            'icon' => $img_uri . '/dapi/baodo.png',
            'items' => array(
                $item));

        // 新增扫码患者
        $entitysAndTotalCnt = PatientDao::getPatients_lastdays_scan($this->id, $days);
        $entitys = $entitysAndTotalCnt[0];
        $item = array();
        $item['title'] = "新增扫码患者";
        $item['cnt'] = count($entitys);
        $item['last_pipe'] = "扫码";
        $item['totalcnt'] = '(共' . $entitysAndTotalCnt[1] . '人)';
        $item['patientids'] = FUtil::entitysToIdsStr($entitys);
        $array2 = array(
            'grouptitle' => '新增扫码患者',
            'color' => '#fc8a39',
            'icon' => $img_uri . '/dapi/scan.png',
            'items' => array(
                $item));

        // 参加培训课患者
        $lessonTitle = "新参加培训课患者";
        $lastPipeStr = "培训课";

        if ($diseaseid == 3) {
            $lessonTitle = "疾病自我管理课程";
            $lastPipeStr = "疾病自我管理";
        }

        $entitysAndTotalCnt = PatientDao::getFbtPatientsOfDoctor($this->id, $days);
        $entitys = $entitysAndTotalCnt[0];
        $item = array();
        $item['title'] = $lessonTitle;
        $item['cnt'] = count($entitys);
        $item['totalcnt'] = '(共' . $entitysAndTotalCnt[1] . '人)';
        $item['last_pipe'] = $lastPipeStr;
        $item['patientids'] = FUtil::entitysToIdsStr($entitys);
        $array3 = array(
            'grouptitle' => $lessonTitle,
            'color' => '#58d505',
            'icon' => $img_uri . '/dapi/fbt.png',
            'items' => array(
                $item));

        // 服药患者
        $entitysAndTotalCnt = PatientDao::getListIsDrugOfDoctor($this->id, $days);
        $entitys = $entitysAndTotalCnt[0];
        $item = array();
        $item['title'] = "新服药患者";
        $item['cnt'] = count($entitys);
        $item['last_pipe'] = "服药";
        $item['totalcnt'] = '(共' . $entitysAndTotalCnt[1] . '人)';
        $item['patientids'] = FUtil::entitysToIdsStr($entitys);
        $array4 = array(
            'grouptitle' => '新服药患者',
            'color' => '#ffaf2c',
            'icon' => $img_uri . '/dapi/drug_statistics.png',
            'items' => array(
                $item));

        // 活跃患者
        // 填量表
        $entitysAndTotalCnt = PatientDao::getPatients_lastdays_answersheet($this->id, $days);
        $entitys = $entitysAndTotalCnt[0];
        $item1 = array();
        $item1['title'] = "填评估量表患者";
        $item1['cnt'] = count($entitys);
        $item1['totalcnt'] = '(共' . $entitysAndTotalCnt[1] . '人)';
        $item1['last_pipe'] = "量表";
        $item1['patientids'] = FUtil::entitysToIdsStr($entitys);
        // 写日记
        $entitysAndTotalCnt = PatientDao::getPatients_lastdays_note($this->id, $days);
        $entitys = $entitysAndTotalCnt[0];
        $item2 = array();
        $item2['title'] = "写日记患者";
        $item2['last_pipe'] = "日记";
        $item2['totalcnt'] = '(共' . $entitysAndTotalCnt[1] . '人)';
        $item2['cnt'] = count($entitys);
        $item2['patientids'] = FUtil::entitysToIdsStr($entitys);
        // 提问
        $entitysAndTotalCnt = PatientDao::getPatients_lastdays_ask($this->id, $days);
        $entitys = $entitysAndTotalCnt[0];
        $item3 = array();
        $item3['title'] = "提问患者";
        $item3['cnt'] = count($entitys);
        $item3['last_pipe'] = "提问";
        $item3['totalcnt'] = '(共' . $entitysAndTotalCnt[1] . '人)';
        $item3['patientids'] = FUtil::entitysToIdsStr($entitys);
        $array5 = array(
            'grouptitle' => '活跃患者',
            'color' => '#12d6bd',
            'icon' => $img_uri . '/dapi/active.png',
            'items' => array(
                $item1,
                $item2,
                $item3));

        if ($this->isAdhdDoctor()) {
            $arr[] = $array5;
            $arr[] = $array1;
            $arr[] = $array2;
            $arr[] = $array3;
            $arr[] = $array4;
        }

        if ($this->isIDDCNSDoctor()) {
            $arr[] = $array5;
            $arr[] = $array1;
            $arr[] = $array3;
        }

        return $arr;
    }

    // 医生配置
    public function getAllConfigs () {
        $black_doctorconfigtpl_codes = [];
        $is_adhd = $this->isAdhdDoctor();
        if($is_adhd){
            $black_doctorconfigtpl_codes = ["bedtkt_audit_pass", "bedtkt_patient_pass", "bedtkt_patient_refuse", "revisittkt_audit_pass", "revisittkt_list_push"];
        }else {
            $black_doctorconfigtpl_codes = ["letter_send"];
        }

        $doctorconfigs = DoctorConfigDao::getListByDoctorid($this->id);
        $arr = [];
        foreach ($doctorconfigs as $doctorconfig) {
            if(false == in_array($doctorconfig->doctorconfigtpl->code, $black_doctorconfigtpl_codes)){
                $arr[] = $doctorconfig;
            }
        }

        return $arr;
    }

    // 医生某一个配置
    public function getConfigByCode ($code) {
        $doctorconfigtpl = DoctorConfigTplDao::getByCode($code);
        return DoctorConfigDao::getByDoctoridDoctorConfigTplid($this->id, $doctorconfigtpl->id);
    }

    // 医生有某一个业务的配置
    public function hasConfigByCode ($code) {
        $doctorconfig = $this->getConfigByCode($code);
        return $doctorconfig instanceof DoctorConfig;
    }

    // 全部报到患者数
    public function getPatientCnt ($notest = false) {
        return PatientDao::getPaitentCntOfDoctor($this->id, $notest);
    }

    // 近7天全部患者数
    public function getPaitentCnt_lastdays () {
        return PatientDao::getPaitentCnt_lastdays($this->id);
    }

    // 近一个月全部有效患者数
    public function getPaitentCnt_lastmonths ($notest = false) {
        return PatientDao::getPaitentCnt_lastmonths($this->id, $notest);
    }

    // 近7天扫码数
    public function getPaitentCnt_lastdays_scan () {
        return PatientDao::getPaitentCnt_lastdays_scan($this->id);
    }

    // 近7天扫码报到数
    public function getPaitentCnt_lastdays_scan_baodao () {
        return PatientDao::getPaitentCnt_lastdays_scan_baodao($this->id);
    }

    // 近7天非扫码报到数
    public function getPaitentCnt_lastdays_notscan () {
        return PatientDao::getPaitentCnt_lastdays_notscan($this->id);
    }

    // 近四周扫码报到统计
    public function getLastFourWeeksRpt () {
        $arr = array();
        $arr['type'] = 'mulbar'; // mulbar | pie
        $arr['title'] = '近四周扫码报到人数统计';
        $arr['name'] = array(
            '报到患者',
            '扫码患者');
        $arr['x'] = array();
        $arr['y1'] = array();
        $arr['y2'] = array();

        $begintimeOfTheWeek = XDateTime::getTheMondayBeginTime();
        $week1Begintime = $begintimeOfTheWeek - 21 * 86400;

        for ($i = 0; $i < 4; $i ++) {
            $weekBegintime = $week1Begintime + $i * 7 * 86400;
            $weekEndtime = $weekBegintime + 6 * 86400;

            $begindate = date("Y-m-d", $weekBegintime);
            $enddate = date("Y-m-d", $weekEndtime);
            $rpt = Rpt_week_doctor_patientDao::getOneByDoctor($this->id, $begindate, $enddate);

            $arr['x'][] = date('m.d', $weekBegintime) . '-' . date('m.d', $weekEndtime);
            $arr['y1'][] = 0 + $rpt->scancnt;
            $arr['y2'][] = 0 + $rpt->baodaocnt;
        }

        return $arr;
    }

    // 近四周扫码报到统计 新接口 客户端不缓存
    public function getLastFourWeeksRptNew () {
        $arr = array();
        $arr['type'] = 'mulbar'; // mulbar | pie
        $arr['title'] = '近四周扫码报到人数统计';
        $arr['name'] = array(
            '报到患者',
            '扫码患者');
        $arr['x'] = array();
        $arr['y1'] = array();
        $arr['y2'] = array();
        $arr['patientids'] = array();

        $begintimeOfTheWeek = XDateTime::getTheMondayBeginTime();
        $week1Begintime = $begintimeOfTheWeek - 21 * 86400;

        for ($i = 0; $i < 4; $i ++) {
            $weekBegintime = $week1Begintime + $i * 7 * 86400;
            $weekEndtime = $weekBegintime + 6 * 86400;

            $begindate = date("Y-m-d", $weekBegintime);
            $enddate = date("Y-m-d", $weekEndtime);

            $scanpatients = PatientDao::getScanPatientsByTime($this->id, $begindate, $enddate);
            $baodaopatients = PatientDao::getBaodaoPatientsByTime($this->id, $begindate, $enddate);

            $arr['x'][] = date('m.d', $weekBegintime) . '-' . date('m.d', $weekEndtime);
            $arr['y1'][] = 0 + count($scanpatients);
            $arr['y2'][] = 0 + count($baodaopatients);
            $arr['patientids'][0][] = FUtil::entitysToIdsStr($scanpatients);
            $arr['patientids'][1][] = FUtil::entitysToIdsStr($baodaopatients);
        }

        return $arr;
    }

    // 消息总数
    public function getDwxMsgCnt () {
        $sql = "select count(*)
            from dwx_pipes
            where objtype in ('Dwx_voicemsg', 'Dwx_picmsg', 'Dwx_txtmsg') and doctorid = {$this->id} ";
        $cnt1 = Dao::queryValue($sql);

        $sql = "select count(distinct a.id)
            from dwx_pipes a
            inner join dwx_kefumsgs b on b.id = a.objid
            where a.objtype = 'Dwx_kefumsg' and b.send_by_way = 'txtmsg' and a.doctorid = {$this->id} ";
        $cnt2 = Dao::queryValue($sql);

        return $cnt1 + $cnt2;
    }

    // 医生发的新消息数
    public function getNewDoctorMsgCnt () {
        $sql = "select max(a.createtime)
        	from dwx_pipes a
        	inner join dwx_kefumsgs b on b.id = a.objid
        	where a.objtype = 'Dwx_kefumsg' and b.send_by_way in ('custom', 'template') and a.doctorid = {$this->id}";
        $max_yunying_createtime = Dao::queryValue($sql);
        $max_yunying_createtime = $max_yunying_createtime ? $max_yunying_createtime : '0000-00-00';

        $sql = "select count(*)
            from dwx_pipes
            where objtype in ('Dwx_voicemsg', 'Dwx_picmsg', 'Dwx_txtmsg') and doctorid = {$this->id}
            and createtime > '{$max_yunying_createtime}' ";

        $cnt = Dao::queryValue($sql);

        return $cnt;
    }

    public function getLastTimeDwxPipe () {
        $sql = "select max(createtime)
            from dwx_pipes
            where objtype in ('Dwx_voicemsg', 'Dwx_picmsg', 'Dwx_txtmsg') and doctorid = {$this->id} ";
        return Dao::queryValue($sql);
    }

    // version 1.10
    public function getBaicellAndGancellByDisease ($titlestr, $tagid) {
        $arr = array();
        $arr['type'] = 'mulbar'; // mulbar | pie
        $arr['title'] = $titlestr;
        $arr['name'] = array(
            $titlestr);
        $arr['x'] = array();
        $arr['y1'] = array();
        $arr['patientids'] = array();

        $sql = "select a.patientid from xanswersheets a join xquestionsheets b on a.xquestionsheetid = b.id
              JOIN tagrefs c ON a.patientid = c.objid
              WHERE  c.tagid = :tagid
              AND b.objid = 101679275 GROUP BY a.patientid";

        $bind = [];
        $bind[':tagid'] = $tagid;

        $rows = Dao::queryValues($sql, $bind);
        if (false == empty($rows)) {
            $arr['x'][] = '肝功能异常';
            $arr['y1'][] = 0 + count($rows);
            $arr['patientids'][0][] = implode(',', $rows);
        }

        $sql = "select a.patientid from xanswersheets a join xquestionsheets b on a.xquestionsheetid = b.id
              JOIN tagrefs c ON a.patientid = c.objid
              WHERE  c.tagid = :tagid
              AND b.objid = 101682449 GROUP BY a.patientid";
        $bind = [];
        $bind[':tagid'] = $tagid;

        $rows = Dao::queryValues($sql, $bind);
        if (false == empty($rows)) {
            $arr['x'][] = '白细胞异常';
            $arr['y1'][] = 0 + count($rows);
            $arr['patientids'][0][] = implode(',', $rows);
        }

        if (true == empty($arr['y1'])) {
            return array();
        }
        return $arr;
    }

    // version 1.09
    // sql注入漏洞 TODO by sjp 20170503
    public function getMedicineTimeRptByIdAndMoths ($moths, $row) {
        $medicineid = $row['medicineid'];
        $time0 = time();

        $sql = "SELECT tt.patientid FROM
            (SELECT pmr.* from patients p
            inner join pcards x on x.patientid = p.id
            inner join patientmedicinerefs pmr ON p.id=pmr.patientid
            inner join diseasemedicinerefs c ON pmr.medicineid = c.medicineid
            WHERE pmr.medicineid > 0 AND pmr.first_start_date<>'0000-00-00' AND pmr.status = 1 AND p.status=1 and p.subscribe_cnt>0
                AND c.level = 9
                AND x.doctorid={$this->id} AND pmr.medicineid = {$medicineid} GROUP BY pmr.patientid) tt
            WHERE 1=1 ";

        if ($this->id == '45') {
            $sql = "SELECT tt.patientid FROM
                (SELECT pmr.* from patients p
                inner join pcards x on x.patientid = p.id
                inner join patientmedicinerefs pmr ON p.id=pmr.patientid
                inner join diseasemedicinerefs c ON pmr.medicineid = c.medicineid
                WHERE pmr.medicineid > 0 AND pmr.first_start_date<>'0000-00-00' AND pmr.status = 1 AND p.status=1 and p.subscribe_cnt>0
                    AND x.doctorid={$this->id} AND pmr.medicineid = {$medicineid} GROUP BY pmr.patientid) tt
                WHERE 1=1 ";
        }

        $arr = array();
        $arr['type'] = 'pie'; // mulbar | pie
        $arr['title'] = $row['name'] . '用药时长分布';
        $arr['x'] = array();
        $arr['y1'] = array();
        $arr['patientids'] = array();

        $cnt = count($moths);

        for ($i = 0; $i <= $cnt; $i ++) {
            $cond = '';
            $x = '';
            if ($i == 0) {
                $date = date('Y-m-d', $time0 - $moths[$i] * 30 * 86400);
                $cond = " and tt.first_start_date >= '{$date}' ";
                $x = $moths[$i] . "个月以下";
            } else
                if ($i == $cnt) {
                    $date = date('Y-m-d', $time0 - $moths[$i - 1] * 30 * 86400);
                    $cond = " and tt.first_start_date < '{$date}' ";
                    $x = $moths[$i - 1] . "个月以上";
                } else {
                    $date = date('Y-m-d', $time0 - $moths[$i] * 30 * 86400);
                    $preDate = date('Y-m-d', $time0 - $moths[$i - 1] * 30 * 86400);
                    $cond = " and tt.first_start_date > '{$date}' and tt.first_start_date < '{$preDate}' ";
                    $x = $moths[$i - 1] . "~" . $moths[$i] . "个月";
                }

            $y = Dao::queryValues($sql . $cond);

            if (count($y) != 0) {
                $arr['y1'][] = 0 + count($y);
                $arr['x'][] = $x;
                $arr['patientids'][0][] = implode(',', $y);
            }
        }

        if (true == empty($arr['y1'])) {
            return array();
        }
        return $arr;
    }

    // 用药时长分布 新 version 1.08
    public function getMedicineTimeRptNew () {
        $time0 = time();

        $date1 = date('Y-m-d', $time0 - 1 * 30 * 86400);
        $date2 = date('Y-m-d', $time0 - 3 * 30 * 86400);
        $date3 = date('Y-m-d', $time0 - 14 * 30 * 86400);

        $sql = "select count( distinct(p.id) ) from patients p
                inner join pcards x on x.patientid = p.id
                join patientmedicinerefs pmr on p.id=pmr.patientid
                where x.doctorid={$this->id}";

        $cond1 = " and first_start_date >= '{$date1}' ";
        $cond2 = " and first_start_date >= '{$date2}' and first_start_date < '{$date1}' ";
        $cond3 = " and first_start_date >= '{$date3}' and first_start_date < '{$date2}' ";
        $cond4 = " and first_start_date < '{$date3}' ";

        $y1 = Dao::queryValues($sql . $cond1);
        $y2 = Dao::queryValues($sql . $cond2);
        $y3 = Dao::queryValues($sql . $cond3);
        $y4 = Dao::queryValues($sql . $cond4);

        $arr = array();
        $arr['type'] = 'pie'; // mulbar | pie
        $arr['title'] = '用药时长分布';
        $arr['x'] = array();
        $arr['y1'] = array();
        $arr['patientids'] = array();

        if (count($y1) != 0) {
            $arr['y1'][] = 0 + count($y1);
            $arr['x'][] = '一个月之内';
            $arr['patientids'][0][] = implode(',', $y1);
        }
        if (count($y2) != 0) {
            $arr['y1'][] = 0 + count($y2);
            $arr['x'][] = '一到三个月';
            $arr['patientids'][0][] = implode(',', $y2);
        }
        if (count($y3) != 0) {
            $arr['y1'][] = 0 + count($y3);
            $arr['x'][] = '三到十四个月';
            $arr['patientids'][0][] = implode(',', $y3);
        }
        if (count($y4) != 0) {
            $arr['y1'][] = 0 + count($y4);
            $arr['x'][] = '十四个月以上';
            $arr['patientids'][0][] = implode(',', $y4);
        }

        if (true == empty($arr['y1'])) {
            return array();
        }
        return $arr;
    }

    // 用药种类分布
    // 已经改成新api（getMedicineTimeRptByIdAndMoths） by wgy
    // 为了版本兼容暂时保留
    public function getMedicineStrRpt () {
        $sql1 = "select count(*) from patientmedicinerefs
                 where medicineid=2"; // 择思达

        $sql2 = "select count(*) from patientmedicinerefs
                 where medicineid=3"; // 专注达

        // 其他 (因为有可能同时服两种药,可能略有误差)
        $sql3 = "select count(distinct patientid) from pcards where diseaseid=1";

        $y1 = Dao::queryValue($sql1, []);
        $y2 = Dao::queryValue($sql2, []);
        $y3 = Dao::queryValue($sql3, []) - $y1 - $y2;

        $arr = array();
        $arr['type'] = 'pie'; // mulbar | pie
        $arr['title'] = '用药种类分布';
        $arr['x'] = array();
        $arr['y1'] = array();
        if ($y1 != 0) {
            $arr['x'][] = '专注达';
            $arr['y1'][] = $y1;
        }
        if ($y2 != 0) {
            $arr['x'][] = '择思达';
            $arr['y1'][] = $y2;
        }
        if ($y3 != 0) {
            $arr['x'][] = '其他';
            $arr['y1'][] = $y3;
        }
        if (true == empty($arr['y1'])) {
            return array();
        }

        return $arr;
    }

    public function getDoDrugPatientids () {
        return PatientDao::getDoDrugPatientids($this->id);
    }

    public function getExceptDotDrugPatientids () {
        return PatientDao::getExceptDotDrugPatientids($this->id);
    }

    // #5658 menzhen_offset_daycnt 触发器
    public function setMenzhen_offset_daycnt ($value) {
        if ($this->menzhen_offset_daycnt == $value) {
            return;
        }

        $arr = [];
        $arr["doctorid"] = $this->id;
        $arr["old_value"] = $this->menzhen_offset_daycnt;
        $arr["new_value"] = $value;
        $json = json_encode($arr, JSON_UNESCAPED_UNICODE);

        // nsq 异步处理
        $job = Job::getInstance();
        $job->doBackground('menzhen_offset_daycnt', $json);

        $this->set4lock('menzhen_offset_daycnt', $value);
    }

    // 得到当前医生患者服药率的pie图数据结构
    public function getDrugStatisticRptBySql ($titleStr) {
        $drugstatistic = array();

        $ids_dodrug = $this->getDoDrugPatientids();
        $ids_exceptdodrug = $this->getExceptDotDrugPatientids();

        $arr['title'] = $titleStr;
        $arr['type'] = 'pie';
        $arr['x'] = array();
        $arr['x'][] = "服药人数";
        $arr['x'][] = "不服药人数";
        $arr['y1'] = array();
        $arr['patientids'] = array();
        if ($ids_dodrug) {
            $arr['y1'][] = count($ids_dodrug);
            $arr['patientids'][0][] = implode(',', $ids_dodrug);
        }
        if ($ids_exceptdodrug) {
            $arr['y1'][] = count($ids_exceptdodrug);
            $arr['patientids'][0][] = implode(',', $ids_exceptdodrug);
        }

        return $arr;
    }

    // version 1.09
    public function getMedicineStrRptBySql ($rows, $sql, $otherSql, $titleStr) {
        $medicineAndName = array();
        $ids_dodrug = $this->getDoDrugPatientids();
        foreach ($rows as $row) {
            $names = explode('（', $row['name']);

            $medicineAndName[] = array(
                $row['medicineid'],
                $names[0]);
        }

        $arr = array();
        $arr['type'] = 'horizontalcolumn'; // mulbar | pie
        if ($this->isIDDCNSDoctor() || 487 == $this->id || 360 == $this->id) {
            $arr['type'] = 'mulbar';
            $arr['name'] = array(
                $titleStr);
        }

        $arr['title'] = $titleStr;
        $arr['x'] = array();
        $arr['y1'] = array();
        $arr['totalcnt'] = count($ids_dodrug);
        $arr['patientids'] = array();

        foreach ($medicineAndName as $a) {

            $bind = [];
            $bind[':medicineid'] = $a[0];
            $bind[':doctorid'] = $this->id;

            $y = Dao::queryValues($sql, $bind);

            if (false == empty($y)) {
                $arr['x'][] = mb_substr($a[1], 0, 6);
                $arr['y1'][] = 0 + count($y);
                $arr['patientids'][0][] = implode(',', $y);
            }
        }

        $bind = [];
        $bind[':diseaseid'] = $this->getMasterDisease()->id;
        $bind[':doctorid'] = $this->id;

        if (true == empty($arr['y1'])) {
            Debug::trace("-----a------");
            return array();
        }

        if (true == empty($arr['x'])) {
            Debug::trace("-----b------");

            return array();
        }

        return $arr;
    }

    // version 1.09
    public function getMedicineStrRptNew ($rows) {
        $medicineAndName = array();
        foreach ($rows as $row) {
            $medicineAndName[] = array(
                $row['medicineid'],
                $row['name']);
        }

        $arr = array();
        $arr['type'] = 'pie'; // mulbar | pie
        if ($this->isIDDCNSDoctor()) {
            $arr['type'] = 'mulbar';
            $arr['name'] = array(
                '用药种类分布');
        }

        $arr['title'] = '用药种类分布';
        $arr['x'] = array();
        $arr['y1'] = array();
        $arr['patientids'] = array();

        foreach ($medicineAndName as $a) {
            $sql = "SELECT distinct a.id
                from patients a
                inner join pcards x on x.patientid = a.id
                inner join patientmedicinerefs b ON a.id = b.patientid
                WHERE  b.medicineid = :medicineid  AND b.medicineid > 0  AND b.first_start_date<>'0000-00-00'
                    AND x.doctorid= :doctorid AND b.status = 1";

            $bind = [];
            $bind[':medicineid'] = $a[0];
            $bind[':doctorid'] = $this->id;

            $y = Dao::queryValues($sql, $bind);

            if (count($y) != 0) {
                $arr['x'][] = $a[1];
                $arr['y1'][] = 0 + count($y);
                $arr['patientids'][0][] = implode(',', $y);
            }
        }

        $otherSql = "select distinct p.id
            from patients p
            inner join pcards x on x.patientid = p.id
            where p.id not in
            ( select  a.patientid from  patientmedicinerefs a
                join diseasemedicinerefs b
                on a.medicineid = b.medicineid
                where b.level = 9 AND b.diseaseid = :diseaseid
            )
            AND x.doctorid = :doctorid
            AND p.status=1 and p.subscribe_cnt>0 ";

        $otherBind = [];
        $otherBind[':diseaseid'] = $this->getMasterDisease()->id;
        $otherBind[':doctorid'] = $this->id;

        $otherY = Dao::queryValues($otherSql, $otherBind);
        if (count($otherY) != 0) {
            $arr['x'][] = '其他';
            $arr['y1'][] = 0 + count($otherY);
            $arr['patientids'][0][] = implode(',', $otherY);
        }

        if (true == empty($arr['y1'])) {
            return array();
        }
        return $arr;
    }

    // 总缓解率：算法：snap-IV 量表总分十八分以下和十八分以上比例（饼图）
    public function getAllBetterRateRpt () {
        $arr = array();
        $arr['type'] = 'pie'; // mulbar | pie
        $arr['title'] = '总缓解率';
        $arr['x'] = array();
        $arr['patientslist'] = array();

        // TODO need pcard fix
        $pcards = PcardDao::getListByDoctoridDiseaseid($this->id, 1);

        $lowCnt = 0;
        $highCnt = 0;
        foreach ($pcards as $pcard) {
            $patient = $pcard->patient;
            if (false == $patient instanceof Patient) {
                continue;
            }

            $score = $patient->getLastADHDScore();
            if ($score == '') {
                continue;
            }
            if ($score < 18) {
                $lowCnt ++;
            } else {
                $highCnt ++;
            }
        }
        $arr['y1'] = array();

        if ($lowCnt != 0) {
            $arr['y1'][] = $lowCnt;
            $arr['x'][] = '18分以下';
        }

        if ($highCnt != 0) {
            $arr['y1'][] = $highCnt;
            $arr['x'][] = '18分以上';
        }

        if (true == empty($arr['y1'])) {
            return array();
        }

        return $arr;
    }

    // 总缓解率：算法：snap-IV 量表总分十八分以下和十八分以上比例（饼图）新
    public function getAllBetterRateRptNew () {
        $arr = array();
        $arr['type'] = 'pie'; // mulbar | pie
        $arr['title'] = '总缓解率';
        $arr['x'] = array();
        $arr['y1'] = array();
        $arr['patientids'] = array();

        $lowpatients = array();
        $highpatients = array();

        // TODO need pcard fix
        $pcards = PcardDao::getListByDoctoridDiseaseid($this->id, 1);

        $lowCnt = 0;
        $highCnt = 0;
        foreach ($pcards as $pcard) {

            $patient = $pcard->patient;
            if (false == $patient instanceof Patient) {
                continue;
            }

            $score = $patient->getLastADHDScore();
            if ($score == '') {
                continue;
            }
            if ($score < 18) {
                $lowCnt ++;
                $lowpatients[] = $patient;
            } else {
                $highCnt ++;
                $highpatients[] = $patient;
            }
        }

        if ($lowCnt != 0) {
            $arr['y1'][] = $lowCnt;
            $arr['x'][] = '18分以下';
            $arr['patientids'][0][] = FUtil::entitysToIdsStr($lowpatients);
        }

        if ($highCnt != 0) {
            $arr['y1'][] = $highCnt;
            $arr['x'][] = '18分以上';
            $arr['patientids'][0][] = FUtil::entitysToIdsStr($highpatients);
        }

        if (true == empty($arr['y1'])) {
            return array();
        }

        return $arr;
    }

    // 八周后专注达患者缓解率：snap-IV 量表总分十八分以下和十八分以上比例（饼图）by wgy
    public function getZzdBetterRateRpt () {
        $arr = array();
        $arr['type'] = 'pie'; // mulbar | pie
        $arr['title'] = '八周后专注达患者缓解率';
        $arr['x'] = array();
        $arr['y1'] = array();

        // TODO need pcard fix
        $pcards = PcardDao::getListByDoctoridDiseaseid($this->id, 1);

        $lowCnt = 0;
        $highCnt = 0;
        foreach ($pcards as $pcard) {

            $patient = $pcard->patient;

            // if ($patient->getMedicineStr() != '专注达') {
            // continue;
            // }
            if (false == $patient instanceof Patient) {
                continue;
            }

            $patientmedicineref = $patient->getRefWithMedicine(MedicineDao::getByName('专注达'));
            if (! $patientmedicineref instanceof PatientMedicineRef) {
                continue;
            }

            // 服药天数
            $medicineDayCnt = $patient->drugingDays();

            if ($medicineDayCnt < 8 * 7) {
                continue;
            }

            $score = $patient->getLastADHDScore();
            if ($score == '') {
                continue;
            }
            if ($score < 18) {
                $lowCnt ++;
            } else {
                $highCnt ++;
            }
        }

        if ($lowCnt > 0 && $highCnt > 0) {
            $arr['x'] = array(
                '18分以下',
                '18分以上');
            $arr['y1'] = array(
                $lowCnt,
                $highCnt);
        } elseif ($lowCnt > 0) {
            $arr['x'] = array(
                '18分以下');
            $arr['y1'] = array(
                $lowCnt);
        } elseif ($highCnt > 0) {
            $arr['x'] = array(
                '18分以上');
            $arr['y1'] = array(
                $highCnt);
        } else {
            $arr = array();
        }

        return $arr;
    }

    // 八周后药品的缓解率 新 by wgy only for diseaseid == 1
    public function getBetterRateRptNew ($title, $medicineid) {
        $arr = array();
        $arr['type'] = 'pie'; // mulbar | pie
        $arr['title'] = $title;
        $arr['x'] = array();
        $arr['y1'] = array();
        $arr['patientids'] = array();

        // TODO need pcard fix , 暂时写死了疾病
        $pcards = PcardDao::getListByDoctoridDiseaseid($this->id, 1);

        $lowCnt = 0;
        $highCnt = 0;
        $lowpatinets = array();
        $highpatients = array();
        foreach ($pcards as $pcard) {

            $patient = $pcard->patient;

            // if ($patient->getMedicineStr() != $medicineststr) {
            // continue;
            // }

            if (false == $patient instanceof Patient) {
                continue;
            }
            $patientmedicineref = $patient->getNoStopRefWithMedicine(Medicine::getById($medicineid));
            if (! $patientmedicineref instanceof PatientMedicineRef) {
                continue;
            }

            $medicineDayCnt = $patient->drugingDays();

            if ($medicineDayCnt < 8 * 7) {
                continue;
            }

            $score = $patient->getLastADHDScore();
            if ($score == '') {
                continue;
            }
            if ($score < 18) {
                $lowCnt ++;
                $lowpatinets[] = $patient;
            } else {
                $highCnt ++;
                $highpatients[] = $patient;
            }
        }

        if ($lowCnt > 0 && $highCnt > 0) {
            $arr['x'] = array(
                '18分以下',
                '18分以上');
            $arr['y1'] = array(
                $lowCnt,
                $highCnt);
            $arr['patientids'][0][] = FUtil::entitysToIdsStr($lowpatinets);
            $arr['patientids'][0][] = FUtil::entitysToIdsStr($highpatients);
        } elseif ($lowCnt > 0) {
            $arr['x'] = array(
                '18分以下');
            $arr['y1'] = array(
                $lowCnt);
            $arr['patientids'][0][] = FUtil::entitysToIdsStr($lowpatinets);
        } elseif ($highCnt > 0) {
            $arr['x'] = array(
                '18分以上');
            $arr['y1'] = array(
                $highCnt);
            $arr['patientids'][0][] = FUtil::entitysToIdsStr($highpatients);
        } else {
            $arr = array();
        }

        return $arr;
    }

    // 获取统计数据 version 1.07
    public function getStatistics () {
        $arr = array();
        $arr['usefulcnt'] = $this->getUsefulCnt(); // 有用的统计数据的数组
        $arr['rpts'][] = $this->getLastFourWeeksRptNew();

        $medicineTimeRpt = $this->getMedicineTimeRptNew();
        if (false == empty($medicineTimeRpt)) {
            $arr['rpts'][] = $medicineTimeRpt;
        }

        $allBetterRateRpt = $this->getAllBetterRateRptNew();
        if (false == empty($allBetterRateRpt)) {
            $arr['rpts'][] = $allBetterRateRpt;
        }

        $zzdBetterRateRpt = $this->getBetterRateRptNew('八周后专注达缓解率', 3);
        if (false == empty($zzdBetterRateRpt)) {
            $arr['rpts'][] = $zzdBetterRateRpt;
        }

        $zsdBetterRateRpt = $this->getBetterRateRptNew('八周后择思达缓解率', 2);
        if (false == empty($zsdBetterRateRpt)) {
            $arr['rpts'][] = $zsdBetterRateRpt;
        }

        return $arr;
    }

    // 获取统计数据 android version 1.10
    public function getGroupedChartStatistics ($isWeb) {
        $diseaseid = $this->getMasterDisease()->id;

        $rows = MedicineDao::getRowsBydoctoridAndDiseaseid($this->id, $diseaseid, " and c.level = 9 ");
        $rowalls = MedicineDao::getRowsBydoctoridAndDiseaseid($this->id, $diseaseid);

        $arr = array();
        $drugstatisticArray = array();
        $medicineTypeArray = array();

        // 全部
        $sql = "SELECT distinct a.id
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join patientmedicinerefs b ON a.id = b.patientid
            WHERE  b.medicineid = :medicineid  AND b.medicineid > 0  AND b.first_start_date<>'0000-00-00'
            AND x.doctorid= :doctorid AND b.status = 1 AND a.status=1 and a.subscribe_cnt>0 ";

        $otherSql = "select distinct p.id
                from patients p
                inner join pcards x on x.patientid = p.id
                where p.id not in
                (   select a.patientid
                    from patientmedicinerefs a
                    inner join diseasemedicinerefs b on b.medicineid = a.medicineid
                    where b.diseaseid = :diseaseid
                )
                AND x.doctorid = :doctorid AND p.status=1 and p.subscribe_cnt>0 ";

        $drugstatisticRpt = $this->getDrugStatisticRptBySql('患者服药率');
        if (false == empty($drugstatisticRpt)) {
            Debug::trace("--false == empty(\$drugstatisticRpt)--");

            $drugstatisticArray['title'] = "患者服药率";
            $drugstatisticArray['rpts'][] = $drugstatisticRpt;
        }

        $medicineStrRpt = $this->getMedicineStrRptBySql($rowalls, $sql, $otherSql, '服药患者数及服药种类');
        if (false == empty($medicineStrRpt)) {
            Debug::trace("--false == empty(\$medicineStrRpt)--");

            $medicineTypeArray['rpts'][] = $medicineStrRpt;
        }

        if ($diseaseid == '3') {

            // 视神经脊髓炎
            $sql = "SELECT distinct a.id
                from patients a
                inner join pcards x on x.patientid = a.id
                inner join patientmedicinerefs b ON a.id = b.patientid
                inner join tagrefs c ON a.id = c.objid
                WHERE c.tagid = 56 AND b.medicineid = :medicineid  AND b.medicineid > 0
                    AND b.first_start_date<>'0000-00-00'  AND x.doctorid= :doctorid
                    AND b.status = 1 AND a.status= 1 and a.subscribe_cnt>0 ";

            $otherSql = "select distinct p.id
                from patients p
                inner join pcards x on x.patientid = p.id
                inner join tagrefs c ON p.id = c.objid
                where c.tagid = 56 AND p.id not in
                    (
                        select  a.patientid
                        from patientmedicinerefs a
                        inner join diseasemedicinerefs b on a.medicineid = b.medicineid
                        WHERE b.level = 9 AND b.diseaseid = :diseaseid
                    )
                    AND x.doctorid = :doctorid AND p.status=1 and p.subscribe_cnt>0 ";

            $medicineStrRpt = $this->getMedicineStrRptBySql($rows, $sql, $otherSql, '视神经脊髓炎用药种类分布');
            if (false == empty($medicineStrRpt)) {
                Debug::trace("--'视神经脊髓炎用药种类分布--");

                $medicineTypeArray['rpts'][] = $medicineStrRpt;
            }

            // 多发性硬化
            $sql = "SELECT distinct a.id
                from patients a
                inner join pcards x on x.patientid = a.id
                INNER JOIN patientmedicinerefs b ON a.id = b.patientid
                INNER JOIN tagrefs c ON a.id = c.objid
                WHERE  c.tagid = 55 AND b.medicineid = :medicineid  AND b.medicineid > 0
                    AND b.first_start_date<>'0000-00-00' AND x.doctorid= :doctorid
                    AND b.status = 1 AND a.status = 1 and a.subscribe_cnt>0 ";

            $otherSql = "select distinct p.id
                from patients p
                inner join pcards x on x.patientid = p.id
                inner JOIN tagrefs c ON p.id = c.objid
                where c.tagid = 55 AND p.id not in
                    ( select  a.patientid
                    from patientmedicinerefs a
                    inner join diseasemedicinerefs b on a.medicineid = b.medicineid
                    WHERE b.level = 9 AND b.diseaseid = :diseaseid
                    )
                    AND x.doctorid = :doctorid AND p.status=1 and p.subscribe_cnt>0 ";

            $medicineStrRpt = $this->getMedicineStrRptBySql($rows, $sql, $otherSql, '多发性硬化用药种类分布');
            if (false == empty($medicineStrRpt)) {
                Debug::trace("--'多发性硬化用药种类分布')--");

                $medicineTypeArray['rpts'][] = $medicineStrRpt;
            }
        }

        if ($diseaseid == '18' || $this->id == 360) {
            $bind = array(
                ":doctorid" => $this->id);
            $sql = 'select distinct t.id
                from tagrefs tr
                inner join patients p on p.id = tr.objid
                inner join pcards x on x.patientid = p.id
                inner join tags t on t.id = tr.tagid
                where x.doctorid = :doctorid and p.status = 1 and p.subscribe_cnt>0 ';

            $tagids = Dao::queryValues($sql, $bind);
            foreach ($tagids as $tagid) {
                $tag = Tag::getById($tagid);

                $sql = "SELECT distinct a.id
                from patients a
                inner join pcards x on x.patientid = a.id
                INNER JOIN patientmedicinerefs b ON a.id = b.patientid
                INNER JOIN tagrefs c ON a.id = c.objid
                WHERE  c.tagid = {$tag->id} AND b.medicineid = :medicineid  AND b.medicineid > 0
                    AND b.first_start_date<>'0000-00-00' AND x.doctorid= :doctorid
                    AND b.status = 1 AND a.status = 1 and a.subscribe_cnt>0 ";

                $otherSql = "select distinct p.id
                from patients p
                inner join pcards x on x.patientid = p.id
                inner JOIN tagrefs c ON p.id = c.objid
                where c.tagid = {$tag->id} AND p.id not in
                    ( select  a.patientid
                    from patientmedicinerefs a
                    inner join diseasemedicinerefs b on a.medicineid = b.medicineid
                    WHERE b.level = 9 AND b.diseaseid = :diseaseid
                    )
                    AND x.doctorid = :doctorid AND p.status=1 and p.subscribe_cnt>0 ";

                $medicineStrRpt = $this->getMedicineStrRptBySql($rows, $sql, $otherSql, "{$tag->name}用药种类分布");
                if (false == empty($medicineStrRpt)) {
                    Debug::trace("--{$tag->name}用药种类分布)--");

                    $medicineTypeArray['rpts'][] = $medicineStrRpt;
                }
            }
        }

        $version = PhoneUtil::getVersion();

        if (false == empty($medicineTypeArray)) {
            Debug::trace("--false != empty(\$medicineTypeArray)--");
            $medicineTypeArray['title'] = "服药患者数及服药种类";
            $arr['rpts'][] = $drugstatisticArray;
            if ($version > 1.26 || $isWeb) {
                $arr['rpts'][] = $medicineTypeArray;
            }
        }

        if ($diseaseid == '18' || $this->id == 360) {

            // 患者诊断分布
            $complication['title'] = '患者诊断分布';
            $complication['type'] = 'pie';
            $complication['x'] = array();
            $complication['y1'] = array();
            $complication['patientids'] = array();

            $bind = array(
                ":doctorid" => $this->id);
            $sql = 'select distinct t.id
                from tagrefs tr
                inner join patients p on p.id = tr.objid
                inner join pcards x on x.patientid = p.id
                inner join tags t on t.id = tr.tagid
                where x.doctorid = :doctorid and p.status = 1 and p.subscribe_cnt>0 ';

            $tagids = Dao::queryValues($sql, $bind);

            foreach ($tagids as $tagid) {
                $tag = Tag::getById($tagid);
                $complication['x'][] = $tag->name;

                $bind = array(
                    ":doctorid" => $this->id,
                    ':tagid' => $tagid);
                $sql = 'select distinct p.id
                from tagrefs tr
                inner join patients p on p.id = tr.objid
                inner join pcards x on x.patientid = p.id
                inner join tags t on t.id = tr.tagid
                where x.doctorid = :doctorid and t.id=:tagid and p.status = 1 and p.subscribe_cnt>0 ';

                $patientids = Dao::queryValues($sql, $bind);
                $complication['y1'][] = count($patientids);
                $complication['patientids'][0][] = implode(',', $patientids);
            }
            $complicationArr = array();
            $complicationArr['rpts'][] = $complication;
            $arr['rpts'][] = $complicationArr;
        }

        if ($diseaseid == '3') {

            // 肝功能异常 白细胞异常
            $baicellGancellArray = array();
            $baicellGancellArray['title'] = "白细胞肝功能异常";

            $baicellGancellRpt = $this->getBaicellAndGancellByDisease('视神经脊髓炎', 56);
            if (false == empty($baicellGancellRpt)) {
                $baicellGancellArray['rpts'][] = $baicellGancellRpt;
            }
            $baicellGancellRpt = $this->getBaicellAndGancellByDisease('多发性硬化', 55);
            if (false == empty($baicellGancellRpt)) {
                $baicellGancellArray['rpts'][] = $baicellGancellRpt;
            }

            $arr['rpts'][] = $baicellGancellArray;
        }

        $months = array(
            1,
            3,
            14);
        if ($diseaseid == 3) {
            $months = array(
                1,
                6);
        }

        foreach ($rows as $row) {
            $medicineTimeRptArray = array();
            $medicineTimeRptArray['title'] = "用药时长分布";

            $medicineTimeRpt = $this->getMedicineTimeRptByIdAndMoths($months, $row);
            if (false == empty($medicineTimeRpt)) {
                $medicineTimeRptArray['rpts'][] = $medicineTimeRpt;
                $arr['rpts'][] = $medicineTimeRptArray;
            }
        }

        if ($diseaseid == '1') {

            $allBetterRateRptArray = array();
            $allBetterRateRptArray['title'] = '总缓解率';
            $allBetterRateRpt = $this->getAllBetterRateRptNew();
            if (false == empty($allBetterRateRpt)) {
                $allBetterRateRptArray['rpts'][] = $allBetterRateRpt;
                $arr['rpts'][] = $allBetterRateRptArray;
            }

            if ($this->id == '45') {
                $zzdBetterRateRptArray = array();
                $zzdBetterRateRptArray['title'] = '八周后益智宁神液缓解率';
                $zzdBetterRateRpt = $this->getBetterRateRptNew('八周后益智宁神液缓解率', 42);
                if (false == empty($zzdBetterRateRpt)) {
                    $zzdBetterRateRptArray['rpts'][] = $zzdBetterRateRpt;
                    $arr['rpts'][] = $zzdBetterRateRptArray;
                }
            }

            $zzdBetterRateRptArray = array();
            $zzdBetterRateRptArray['title'] = '八周后专注达缓解率';
            $zzdBetterRateRpt = $this->getBetterRateRptNew('八周后专注达缓解率', 3);
            if (false == empty($zzdBetterRateRpt)) {
                $zzdBetterRateRptArray['rpts'][] = $zzdBetterRateRpt;
                $arr['rpts'][] = $zzdBetterRateRptArray;
            }

            $zsdBetterRateRptArray = array();
            $zsdBetterRateRptArray['title'] = '八周后择思达缓解率';
            $zsdBetterRateRpt = $this->getBetterRateRptNew('八周后择思达缓解率', 2);
            if (false == empty($zsdBetterRateRpt)) {
                $zsdBetterRateRptArray['rpts'][] = $zsdBetterRateRpt;
                $arr['rpts'][] = $zsdBetterRateRptArray;
            }
        }
        return $arr;
    }

    // 获取统计数据 version 1.09
    public function getChartStatistics () {
        $diseaseid = $this->getMasterDisease()->id;

        $medicineTypeSql = "select a.id, b.medicineid, c.level, d.name, c.diseaseid
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join patientmedicinerefs b  on a.id = b.patientid
            inner join diseasemedicinerefs c on b.medicineid = c.medicineid
            inner join medicines d on b.medicineid = d.id
            WHERE  x.doctorid = :doctorid  and c.diseaseid = :diseaseid
                and c.level = 9  AND b.status = 1
            GROUP BY b.medicineid";

        $bind = [];
        $bind[':doctorid'] = $this->id;
        $bind[':diseaseid'] = $diseaseid;

        $rows = Dao::queryRows($medicineTypeSql, $bind);

        $arr = array();

        $medicineStrRpt = $this->getMedicineStrRptNew($rows);
        if (false == empty($medicineStrRpt)) {
            $arr['rpts'][] = $medicineStrRpt;
        }

        $months = array(
            1,
            3,
            14);
        if ($diseaseid == 3) {
            $months = array(
                1,
                6);
        }

        foreach ($rows as $row) {
            $medicineTimeRpt = $this->getMedicineTimeRptByIdAndMoths($months, $row);
            if (false == empty($medicineTimeRpt)) {
                $arr['rpts'][] = $medicineTimeRpt;
            }
        }

        if ($diseaseid == '1') {

            $allBetterRateRpt = $this->getAllBetterRateRptNew();
            if (false == empty($allBetterRateRpt)) {
                $arr['rpts'][] = $allBetterRateRpt;
            }

            $zzdBetterRateRpt = $this->getBetterRateRptNew('八周后专注达缓解率', 3);
            if (false == empty($zzdBetterRateRpt)) {
                $arr['rpts'][] = $zzdBetterRateRpt;
            }

            $zsdBetterRateRpt = $this->getBetterRateRptNew('八周后择思达缓解率', 2);
            if (false == empty($zsdBetterRateRpt)) {
                $arr['rpts'][] = $zsdBetterRateRpt;
            }
        }
        return $arr;
    }

    public function hasPatient ($patientid) {
        return ! ! PcardDao::getByPatientidDoctorid($patientid, $this->id);
    }

    public function getDoctorMedicineRefCnt () {
        return count(DoctorMedicineRefDao::getListByDoctorid($this->id));
    }

    public function getAssistants () {
        return AssistantDao::getListByDoctorid($this->id);
    }

    public function getPcardByPatientid ($patientid) {
        return PcardDao::getByPatientidDoctorid($patientid, $this->id);
    }

    //医生是否需要审核处方
    public function needAuditChufang(){
        return 1 == $this->is_audit_chufang;
    }

    //判断是否是目标医生的主管
    public function isSuperiorOfDoctor(Doctor $doctor) {
        $doctorSuperior = Doctor_SuperiorDao::getOneBy2Doctorid($doctor->id, $this->id);
        return !!$doctorSuperior;
    }

    //获取主管医生们的id
    public function getSuperiorDoctorIds() {
        $doctor_superiors = Doctor_SuperiorDao::getListByDoctorid($this->id);
        $superiorDoctorids = [];
        foreach ($doctor_superiors as $one) {
           $superiorDoctorids[] = $one->superior_doctorid;
        }

        return $superiorDoctorids;
    }

    // $row = array();
    // $row["userid"] = $userid;
    // $row["hospitalid"] = $hospitalid;
    // $row["name"] = $name;
    // $row["sex"] = $sex;
    // $row["title"] = $title;
    // $row["department"] = $department;
    // $row["code"] = $code;
    // $row["patients_referencing"] = $patients_referencing;
    // $row["brief"] = $brief;
    // $row["tip"] = $tip;
    // $row["scheduletip"] = $scheduletip;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Doctor::createByBiz row cannot empty");

        $default = array();
        $default["userid"] = 0;
        $default["hospitalid"] = 0;
        $default["name"] = '';
        $default["sex"] = 0;
        $default["title"] = '';
        $default["department"] = '';
        $default["headimg_pictureid"] = 0;
        $default["code"] = '';
        $default["pdoctorid"] = 0;
        $default["patients_referencing"] = '';
        $default["first_patient_date"] = '0000-00-00';
        $default["is_sign"] = 0;
        $default["menzhen_offset_daycnt"] = 0;
        $default["menzhen_pass_date"] = '0000-00-00';
        $default["is_audit_chufang"] = 0;
        $default["audit_chufang_pass_time"] = '0000-00-00 00:00:00';
        $default["brief"] = '';
        $default["be_good_at"] = '';
        $default["tip"] = '';
        $default["scheduletip"] = '';
        $default["bulletin"] = '';
        $default["is_bulletin_show"] = 1;
        $default["auditorid_yunying"] = 0;
        $default["auditorid_market"] = 0;
        $default["auditorid_createby"] = 0;
        $default["status"] = 0;
        $default["auditstatus"] = 0;
        $default["auditorid"] = 0;
        $default["doctorgroupid"] = 0;
        $default["auditremark"] = '';
        $default["service_remark"] = '';
        $default["hospital_name"] = '';
        $default["mobile"] = '';
        $default["email"] = '';
        $default["old_doctorid"] = '';
        $default["remark"] = '';
        $default["sourcestr"] = '';
        $default["is_new_pipe"] = 0;
        $default["module_pushmsg"] = 0;
        $default["module_audit"] = 0;
        $default["bedtkt_pass_content"] = '';
        $default["bedtkt_refuse_content"] = '';
        $default["is_allow_bedtkt"] = 0;
        $default["is_treatment_notice"] = 0;
        $default["lastcachetime"] = XDateTime::now();
        $default["lastreadtasktime"] = XDateTime::now();
        $default["lastschedule_updatetime"] = "0000-00-00 00:00:00";
        $default["is_alk"] = 0;

        $row += $default;
        $doctor = new self($row);

        // 创建医生的时候， 默认配置医生（doctorconfig）
        $doctorconfigtpls = Dao::getEntityListByCond("DoctorConfigTpl");
        foreach ($doctorconfigtpls as $doctorconfigtpl) {
            $doctorconfig = DoctorConfigDao::getByDoctoridDoctorConfigTplid($doctor->id, $doctorconfigtpl->id);

            if (false == $doctorconfig instanceof DoctorConfig) {
                $row = array();
                $row['doctorid'] = $doctor->id;
                $row['doctorconfigtplid'] = $doctorconfigtpl->id;

                $doctorconfig = DoctorConfig::createByBiz($row);
            }
        }

        return $doctor;
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////
    public static function getIdsOfCompany () {
        $doctors = DoctorDao::getListByHospital(5);
        $ids = array();

        foreach ($doctors as $a) {
            $ids[] = $a->id;
        }

        return $ids;
    }

    public static function patientinfoCallback ($xquestionsheet) {
        // 此方法用来装木做样，不能删 20170531 不明白为啥不能删 by 许喆
    }

    // 测试医生 + 韩颖 + 钱英
    public static function getTestDoctorIdStr ($fixIds = array(9,151)) {
        $ids = self::getTestDoctorIdArray($fixIds);
        return implode(",", $ids);
    }

    // 测试医生 + 韩颖 + 钱英
    public static function getTestDoctorIdArray ($fixIds = array(9,151)) {
        $ids = array();
        $doctors = DoctorDao::getListByHospital(5);
        foreach ($doctors as $a) {
            $ids[] = $a->id;
        }

        foreach ($fixIds as $a) {
            $ids[] = $a;
        }

        return $ids;
    }

    public static function getIdNameArrByDiseaseid ($diseaseid) {
        $doctors = DoctorDao::getListByDiseaseid($diseaseid);
        $doctorid_names = [];
        foreach ($doctors as $doctor) {
            $doctorid_names["{$doctor->id}"] = $doctor->name;
        }

        return $doctorid_names;
    }
}
