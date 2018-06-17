<?php

class CtrHelper
{
    // ---------- base beg ----------
    public static function getStatus_onlineCtrArray ($needAll = false): array {
        $arr = array();
        if ($needAll) {
            $arr[2] = '全部';
        }

        $arr[1] = '上线';
        $arr[0] = '下线';

        return $arr;
    }

    public static function getIsauditCtrArray ($needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[2] = '全部';
        }

        $arr[1] = '已审核';
        $arr[0] = '未审核';

        return $arr;
    }

    public static function getIsshowCtrArray ($needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[2] = '全部';
        }

        $arr[1] = '显示';
        $arr[0] = '关闭';

        return $arr;
    }

    public static function getIsBindingCtrArray ($needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[2] = '全部';
        }

        $arr[1] = '已绑定';
        $arr[0] = '未绑定';

        return $arr;
    }

    public static function getYesNoCtrArray ($needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[2] = '全部';
        }

        $arr[1] = '是';
        $arr[0] = '否';

        return $arr;
    }
    // ---------- base end ----------

    // ---------- Subsys beg ----------
    // 用于筛选
    public static function getSubsysCtrArray (): array {
        $arr = array();
        $arr['all'] = 'all';
        $arr['audit'] = 'audit';
        $arr['dapi'] = 'dapi';
        $arr['dm'] = 'dm';
        $arr['doctor'] = 'doctor';

        return $arr;
    }
    // ---------- Subsys end ----------

    // ---------- PatientMedicineRef beg ----------
    // getStop_drug_typeCtrArray
    public static function getStop_drug_typeCtrArray ($needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '请选择';
        }

        $arr[1] = '遵医嘱停药';
        $arr[2] = '自主停药';
        return $arr;
    }
    // ---------- PatientMedicineRef end ----------

    // ---------- xprovince xcity xcounty beg ----------
    public static function getAllXprovinceCtrArray ($needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '未选择';
        }

        $xprovinces = Dao::getEntityListByCond('Xprovince');
        foreach ($xprovinces as $a) {
            $arr[$a->id] = $a->name;
        }

        return $arr;
    }

    public static function getAllXcityByXprovinceidCtrArray ($xprovinceid = 0, $needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '未选择';
        }

        if(0 == $xprovinceid){
            $arr[0] = '请选择一个省份';
            return $arr;
        }

        $cond = " and xprovinceid = :xprovinceid ";
        $bind = [];
        $bind[":xprovinceid"] = $xprovinceid;
        $xcitys = Dao::getEntityListByCond('Xcity', $cond, $bind);
        foreach ($xcitys as $a) {
            $arr[$a->id] = $a->name;
        }

        return $arr;
    }
    // ---------- xprovince xcity xcounty end ----------

    // ---------- Patient beg ----------
    // getDoubt_typeCtrArray
    public static function getDoubt_typeCtrArray (): array {
        $arr = array();
        $arr[0] = '有效患者';
        $arr[1] = '怀疑无效';
        $arr[2] = '不配合患者';
        $arr[3] = '黑名单患者';
        return $arr;
    }

    // ---------- Patient end ----------

    // ---------- Auditor beg ----------

    // auditor type 数组
    public static function getAuditorTypeCtrArray ($needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '全部';
        }

        $arr[1] = '正式';
        $arr[2] = '兼职';

        return $arr;
    }

    // 全部 auditors
    public static function getAllAuditorCtrArray ($needAll = true): array {
        $cond = ' order by id ';
        $auditors = Dao::getEntityListByCond("Auditor", $cond, []);

        return self::toAuditorCtrArray($auditors, $needAll);
    }

    // 全部 在职 auditors
    public static function getAuditorCtrArray ($needAll = true): array {
        $cond = ' and status = 1 order by id ';
        $auditors = Dao::getEntityListByCond("Auditor", $cond, []);

        return self::toAuditorCtrArray($auditors, $needAll);
    }

    // 市场 auditors
    public static function getMarketAuditorCtrArray ($needAll = true): array {
        $cond = ' and status = 1 order by id ';
        $auditorsBak = Dao::getEntityListByCond("Auditor", $cond, []);

        $auditors = array();
        foreach ($auditorsBak as $a) {
            if ($a->isHasRole(array(
                'market'))) {
                $auditors[] = $a;
            }
        }

        return self::toAuditorCtrArray($auditors, $needAll);
    }

    // 技术 auditors
    public static function getTechAuditorCtrArray ($needAll = true): array {
        $cond = ' and status = 1 order by id ';
        $auditorsBak = Dao::getEntityListByCond("Auditor", $cond, []);

        $auditors = array();
        foreach ($auditorsBak as $a) {
            if ($a->isHasRole(array(
                'tech'))) {
                $auditors[] = $a;
            }
        }

        return self::toAuditorCtrArray($auditors, $needAll);
    }

    // 运营 auditors for 任务
    public static function getYunyingAuditorCtrArrayForOpTask() {
        $arr = [];
        $arr['-3'] = '[全部]';
        $arr['-4'] = '[锁定]';
        $arr['-2'] = '[--我--]';
        $arr['-1'] = '[我+未分配]';
        $arr['0'] = '[未分配]';
        $arr += self::getYunyingAuditorCtrArray(false);
        return $arr;
    }

    // 运营 auditors
    public static function getYunyingAuditorCtrArray ($needAll = true): array {
        $cond = ' and status = 1 order by id ';
        $auditorsBak = Dao::getEntityListByCond("Auditor", $cond, []);

        $auditors = array();
        foreach ($auditorsBak as $a) {
            if ($a->isHasRole(array(
                'yunying'))) {
                $auditors[] = $a;
            }
        }

        return self::toAuditorCtrArray($auditors, $needAll);
    }

    // 量表
    public static function getPaperTplCtrArray ($needAll = true) {
        $papertpls = Dao::getEntityListByCond('PaperTpl');

        $arr = array();
        if ($needAll) {
            $arr[0] = '0 未选择';
        }

        foreach ($papertpls as $a) {
            $arr[$a->id] = $a->title;
        }

        return $arr;
    }

    // 员工组 auditorgroups
    public static function getAuditorGroupCtrArray ($noOne = true, $type = ''): array {
        $cond = '';
        $bind = [];

        if($type){
            $cond .= ' AND type=:type ';
            $bind[':type'] = $type;
        }

        $auditorgroups = Dao::getEntityListByCond("AuditorGroup", $cond, $bind);

        $arr = array();
        if ($noOne) {
            $arr[0] = '未选择';
        }

        foreach ($auditorgroups as $a) {
            $arr[$a->id] = $a->name;
        }

        return $arr;
    }

    // 医生分组 doctorgroups
    public static function getDoctorGroupsCtrArray ($needAll = true) {
        $doctorgroups = Dao::getEntityListByCond('DoctorGroup');

        $arr = [];
        if ($needAll) {
            $arr[0] = '0 未选择';
        }

        foreach ($doctorgroups as $a) {
            $arr[$a->id] = $a->title;
        }

        return $arr;
    }

    public static function getDoctorGroupsCtrArrayForFilter () {
        $doctorgroups = Dao::getEntityListByCond('DoctorGroup');

        $arr = [];
        $arr['-2'] = '全部';
        $arr['-1'] = '无医生组';

        foreach ($doctorgroups as $a) {
            $arr[$a->id] = $a->title;
        }

        return $arr;
    }

    // 患者阶段 patientgroups
    public static function getPatientGroupsCtrArray ($needAll = true) {
        $patientgroups = Dao::getEntityListByCond('PatientGroup', 'order by pos asc, id desc ');

        $arr = [];
        if ($needAll) {
            $arr[0] = '0 未选择';
        }

        foreach ($patientgroups as $a) {
            $arr[$a->id] = $a->title;
        }

        return $arr;
    }

    // 患者阶段 patientgroups
    public static function getPatientGroupsCtrArrayForFilter () {
        $patientgroups = Dao::getEntityListByCond('PatientGroup', 'order by pos asc, id desc ');

        $arr = [];
        $arr['-2'] = '全部';
        $arr['-1'] = '无患者组';

        foreach ($patientgroups as $a) {
            $arr[$a->id] = $a->title;
        }

        return $arr;
    }

    // 患者阶段 patientstages
    public static function getPatientStagesCtrArray ($needAll = true) {
        $patientstages = Dao::getEntityListByCond('PatientStage', 'order by pos asc, id desc ');

        $arr = [];
        if ($needAll) {
            $arr[0] = '0 未选择';
        }

        foreach ($patientstages as $a) {
            $arr[$a->id] = $a->title;
        }

        return $arr;
    }

    // 患者阶段 patientstages
    public static function getPatientStagesCtrArrayForFilter () {
        $patientstages = Dao::getEntityListByCond('PatientStage', 'order by pos asc, id desc ');

        $arr = [];
        $arr['-2'] = '全部';

        foreach ($patientstages as $a) {
            $arr[$a->id] = $a->title;
        }

        return $arr;
    }

    // toAuditorCtrArray
    private static function toAuditorCtrArray (array $auditors, $needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '0 未选择';
        }

        foreach ($auditors as $a) {

            $str = $a->id . " " . $a->name;

            $arr[$a->id] = $str;
        }

        return $arr;
    }
    // ---------- Auditor end ----------

    // toDc_projectCtrArray
    public static function toDc_projectCtrArray ($dc_projects, $needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '0 未选择';
        }

        foreach ($dc_projects as $a) {

            $str = $a->title;

            $arr[$a->id] = $str;
        }

        return $arr;
    }

    public function toDc_patientplanCtrArray ($dc_doctorprojects , $needAll = true) {
        $arr = array();
        if ($needAll) {
            $arr[0] = '0 未选择';
        }

        foreach ($dc_doctorprojects as $a) {

            $str = $a->dc_project->title . " ({$a->begin_date}~{$a->end_date})";

            $arr[$a->id] = $str;
        }

        return $arr;
    }

    // ---------- Disease beg ----------
    // getDiseaseCtrArray
    public static function getDiseaseCtrArray ($needAll = true): array {
        $diseases = Dao::getEntityListByCond('Disease');
        return self::toDiseaseCtrArray($diseases, $needAll);
    }

    // toDiseaseCtrArray
    public static function toDiseaseCtrArray (array $diseases, $needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '全部疾病';
        }

        foreach ($diseases as $a) {
            if ($a->id == 1) {
                $arr[$a->id] = 'ADHD';
                continue;
            }
            $arr[$a->id] = $a->name;
        }
        return $arr;
    }

    //toOptaskStatuCtrArray
    public static function toOptaskStatuCtrArray ($needAll = true) {
        $list = [];
        if ($needAll) {
            $list['-1'] = '所有';
        }

        $list['0'] = '进行中';
        $list['1'] = '已完成';
        $list['2'] = '已挂起';

        return $list;
    }

    //toOptaskStatuCtrArrayForFilter
    public static function toOptaskStatuCtrArrayForFilter () {
        $list = [];
        $list['-2'] = '所有';
        $list['-1'] = '进行中';
        $list['1'] = '已完成';
        $list['2'] = '已挂起';

        return $list;
    }

    public static function toOptaskPlantimeCtrArray ($needAll = true) {
        $list = [];
        if ($needAll) {
            $list['0'] = '所有';
        }

        $list['1'] = '今日任务';
        $list['2'] = '未来任务';

        return $list;
    }

    public static function toOptaskPlantimeCtrArrayForFilter () {
        $list = [];

        $list['-2'] = '所有';
        $list['1'] = '今日任务';
        $list['2'] = '未来任务';

        return $list;
    }

    // getOptaskTplCtrArray
    public static function getOptaskTplCtrArray ($needAll = true): array {
        $optasktpls = Dao::getEntityListByCond("OpTaskTpl");
        return self::toOptaskTplCtrArray($optasktpls, $needAll);
    }

    // toOptaskTplCtrArray
    public static function toOptaskTplCtrArray (array $optasktpls, $needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '全部';
        }

        foreach ($optasktpls as $a) {
            $arr[$a->id] = $a->title;
        }
        return $arr;
    }

    // toOptaskTplForDiseaseGroupCtrArray
    public static function toOptaskTplForDiseaseGroupCtrArray ($diseasegroup, array $optasktpls, $needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '全部';
        }

        //疾病组的疾病
        $diseaseids = [];
        if($diseasegroup instanceof DiseaseGroup){
            $diseaseids = DiseaseDao::getIdsByDiseasegroup($diseasegroup);
        }

        foreach ($optasktpls as $optasktpl) {
            //任务模版的疾病
            $optasktpl_diseaseids = explode(',', $optasktpl->diseaseids);

            //没有疾病组 或者 任务模版的疾病为全部 或者 疾病组的疾病与任务模版上的疾病相交不为空
            if(empty($diseaseids) || 0==$optasktpl->diseaseids || false == empty(array_intersect($diseaseids, $optasktpl_diseaseids))){
                $arr[$optasktpl->id] = $optasktpl->title;
            }
        }
        return $arr;
    }

    // toOpNodeCtrArray
    public static function toOpNodeCtrArray (array $opnodels, $needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '全部';
        }

        foreach ($opnodels as $a) {
            $arr[$a->id] = $a->title;
        }
        return $arr;
    }

    // getOptaskTplStatusCtrArray
    public static function getOptaskTplStatusCtrArray () {
        $arr = [];
        $arr['1'] = '有效';
        $arr['0'] = '无效';
        return $arr;
    }

    // getOptaskLevelCtrArray
    public static function getOptaskLevelCtrArray ($needAll = false) {
        $arr = [];
        if($needAll){
            $arr[0] = '全部';
        }
        $arr_levels = OpTask::getLevelDescArr();
        foreach ($arr_levels as $k => $v) {
            $arr[$k] = "L" . $k . " " . $v["name"];
        }
        return $arr;
    }

    // getOptaskLevelCtrArrayForFilter
    public static function getOptaskLevelCtrArrayForFilter () {
        $arr = [];
        $arr['-2'] = '全部';
        $arr_levels = OpTask::getLevelDescArr();
        foreach ($arr_levels as $k => $v) {
            $arr[$k] = "L" . $k . " " . $v["name"];
        }
        return $arr;
    }
    // ---------- Disease end ----------

    // ---------- DiseaseGroup beg ----------

    // getDiseaseGroupCtrArray
    public static function getDiseaseGroupCtrArray ($needNull = true): array {
        $diseasegroups = Dao::getEntityListByCond('DiseaseGroup');
        return self::toDiseaseGroupCtrArray($diseasegroups, $needNull);
    }

    // toDiseaseGroupCtrArray
    public static function toDiseaseGroupCtrArray (array $diseasegroups, $needNull = true): array {
        $arr = array();
        if ($needNull) {
            $arr[0] = '全部';
        }

        foreach ($diseasegroups as $a) {
            $arr[$a->id] = $a->name;
        }
        return $arr;
    }

    // getDiseaseGroupCtrArray
    public static function getDiseaseGroupCtrArrayForFilter (Auditor $auditor): array {
        $arr =[];
        $diseasegroups = [];
        if ($auditor->diseasegroupid > 0) {
            $diseasegroups[] = $auditor->diseasegroup;
        } else {
            $diseasegroups = Dao::getEntityListByCond('DiseaseGroup');
            $arr['-1'] = '全部';
        }

        foreach ($diseasegroups as $a) {
            $arr[$a->id] = $a->name;
        }
        return $arr;
    }

    // getDiseaseGroupCtrArray
    public static function getDiseaseCtrArrayForFilter (): array {
        $sql = "select id,name from diseases ";
        $rows = Dao::queryRows($sql);

        $list = [];
        $list["-2"] = "全部";
        foreach ($rows as $row) {
            $list["{$row['id']}"] = $row['name'];
        }

        return $list;
    }

    // ---------- DiseaseGroup end ----------

    // ---------- Express beg ----------

    // getExpress_companyCtrArray
    public static function getExpress_companyCtrArray () {
        $arr = [];
        $arr['顺丰'] = '顺丰';
        $arr['申通'] = '申通';
        $arr['圆通'] = '圆通';
        $arr['中通'] = '中通';

        return $arr;
    }

    // getExpress_companyCtrArray
    public static function getExpress_companyCtrArrayForAudit () {
        $arr = [];
        $arr['顺丰'] = '顺丰';
        $arr['申通'] = '申通';
        $arr['圆通'] = '圆通';
        $arr['中通'] = '中通';
        $arr['韵达'] = '韵达';
        $arr['EMS'] = 'EMS';

        return $arr;
    }

    // 奇门接口快递ename数组
    public static function getExpress_companyOfEnameCtrArrayForQiMen () {
        $arr = [];
        $arr['顺丰'] = 'SFGR';
        $arr['申通'] = 'STO';
        $arr['圆通'] = 'YTO';
        $arr['中通'] = 'ZTO';
        $arr['韵达'] = 'YUNDA';
        $arr['EMS'] = 'EMS';

        return $arr;
    }

    // getExpress_companyOfEnameCtrArray
    //用于kd100,kdniao快递配送接口
    public static function getExpress_companyOfEnameCtrArray ($service_name) {
        $arr = array(
            ExpressService::KD100 => array(
                "顺丰" => "shunfeng",
                "申通" => "shentong",
                "圆通" => "yuantong",
                "中通" => "zhongtong",
                "韵达" => "yunda",
                "百世快递" => "huitongkuaidi",
                "EMS" => "ems",
                "天天快递" => "tiantian",
            ),
            ExpressService::KDNIAO => array(
                "顺丰" => "SF",
                "申通" => "STO",
                "圆通" => "YTO",
                "中通" => "ZTO",
                "韵达" => "YD",
                "百世快递" => "HTKY",
                "EMS" => "EMS",
                "天天快递" => "HHTT",
            ),
        );
        return $arr[$service_name] ?? [];
    }

    // ---------- Express end ----------

    // ---------- ShopProduct beg ----------

    // getShopProductTypeCtrArray
    public static function getShopProductTypeCtrArray (): array {
        $entitys = Dao::getEntityListByCond("ShopProductType", 'order by pos');

        $arr = array();
        foreach ($entitys as $a) {
            $arr[$a->id] = $a->name;
        }

        return $arr;
    }

    // toShopProductTypeCtrArray
    public static function toShopProductTypeCtrArray (array $shopProductTypes, $needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '全部';
        }

        foreach ($shopProductTypes as $a) {
            $arr[$a->id] = $a->name;
        }
        return $arr;
    }

    // getShopProductIs_waterCtrArray
    public static function getShopProductIs_waterCtrArray (): array {
        $arr = array();
        $arr[0] = '否';
        $arr[1] = '是';
        return $arr;
    }

    // toShopProductCtrArray
    public static function toShopProductCtrArray (array $shopProducts, $needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '全部';
        }

        foreach ($shopProducts as $a) {
            $arr[$a->id] = $a->title;
        }
        return $arr;
    }
    // ---------- ShopProduct end ----------

    // ---------- ShopProductNotice beg ----------

    // getShopProductNoticeCtrArray
    public static function getShopProductNoticeCtrArray ( $needAll = true): array {
        $arr = [];
        if ($needAll) {
            $arr[-1] = '全部';
        }

        $arr += ShopProductNotice::getStatusArr();

        return $arr;
    }
    // ---------- ShopProductNotice end ----------

    // ---------- ShopOrderType beg ----------

    // getShopOrderTypeCtrArray
    public static function getShopOrderTypeCtrArray () {
        $arr = [];
        $arr['all'] = '全部';
        $arr[ShopOrder::type_chufang] = '处方申请单';
        $arr[ShopOrder::type_weituo] = '担保申请单';
        $arr[ShopOrder::type_shopping] = '购物订单';

        return $arr;
    }

    // getShopOrderHaveItemArray
    public static function getShopOrderHaveItemArray () {
        $arr = [];
        $arr['all'] = '全部';
        $arr['noitem'] = '未选商品';
        $arr['haveitem'] = '已选商品';

        return $arr;
    }

    // getShopOrderPayCtrArray
    public static function getShopOrderPayCtrArray () {
        $arr = [];
        $arr['all'] = '全部';
        $arr['unpay'] = '未支付';
        $arr['pay'] = '已支付';
        return $arr;
    }

    // getShopOrderSendoutCtrArray
    public static function getShopOrderSendoutCtrArray () {
        $arr = [];
        $arr['all'] = '全部';
        $arr['unsendout'] = '待发货';
        $arr['sendout'] = '已发货';
        return $arr;
    }

    // getShopOrderStatusCtrArray
    public static function getShopOrderStatusCtrArray () {
        $arr = [];
        $arr['all'] = '全部';
        $arr['unaudit'] = '未审核';
        $arr['pass'] = '审核通过';
        $arr['refuse'] = '审核拒绝';
        return $arr;
    }

    // getShopOrderRefundCtrArray
    public static function getShopOrderRefundCtrArray () {
        $arr = [];
        $arr['all'] = '全部';
        $arr['refund_all'] = '全额退款';
        $arr['refund_part'] = '部分退款';
        $arr['refund_not'] = '未退款';
        $arr['refund_not_all'] = '未退款+部分退款';

        return $arr;
    }

    // getShopOrderRecipeCtrArray
    public static function getShopOrderRecipeCtrArray () {
        $arr = [];
        $arr['all'] = '全部';
        $arr['yes'] = '已绑定';
        $arr['no'] = '未绑定';

        return $arr;
    }

    // getShopOrderAuditstatusCtrArray
    public static function getShopOrderAuditstatusCtrArray () {
        $arr = [];
        $arr['all'] = '全部';
        $arr['yes'] = '已关闭';
        $arr['no'] = '未关闭';

        return $arr;
    }

    // getShopOrderFirstCtrArray
    public static function getShopOrderFirstCtrArray () {
        $arr = [];
        $arr['all'] = '全部';
        $arr['first'] = '首单';
        $arr['other'] = '非首单';
        return $arr;
    }

    // getShopOrderPosCtrArray
    public static function getShopOrderPosCtrArray () {
        $arr = [];
        $arr[0] = '全部';
        for($i = 1; $i <= 30; $i++){
            $arr[$i] = "第{$i}单";
        }
        return $arr;
    }

    // ---------- ShopOrderType end ----------

    // ---------- ShopProductType beg ----------
    public static function getShopProductMedicineTypeCtrArray ($needall = true) {
        $arr = [];
        if ($needall) {
            $arr['all'] = '全部';
        }
        $arr['yes'] = '药品';
        $arr['no'] = '非药品';
        return $arr;
    }
    // ---------- ShopProductType end ----------

    // ---------- ShopProductNoticeLineType beg ----------
    public static function getShopProductNoticeLineTypeCtrArray ($needall = true) {
        $arr = [];
        if ($needall) {
            $arr['all'] = '全部';
        }
        $arr['lt'] = '低于';
        $arr['gt'] = '高于';
        return $arr;
    }
    // ---------- ShopProductNoticeLineType end ----------

    // ---------- Tag beg ----------
    // getTagCtrArrayWithAll
    public static function getTagCtrArrayWithAll ($typestr = ''): array {
        if ($typestr == 'all') {
            $typestr = '';
        }
        $tags = TagDao::getListByTypestr($typestr);

        return self::toTagCtrArray($tags);
    }

    // toTagCtrArray
    public static function toTagCtrArray (array $tags): array {
        $arr = array();
        foreach ($tags as $a) {
            $arr[$a->id] = $a->name;
        }

        return $arr;
    }
    // ---------- Tag end ----------

    // ---------- Doctor beg ----------
    // getDoctorCtrArray
    public static function getDoctorCtrArray ($diseaseid = 0): array {
        if ($diseaseid > 0) {
            $doctors = DoctorDao::getListByDiseaseid($diseaseid);
        } else {
            $doctors = [];
        }
        return self::toDoctorCtrArray($doctors);
    }

    public static function toDc_doctorProjectCtrArray ($dc_doctorprojects, $needall = true) {
        $arr = array();
        if ($needall) {
            $arr[0] = '全部';
        }

        foreach ($dc_doctorprojects as $a) {
            $arr[$a->id] = $a->doctor->name . " " .$a->dc_project->title;
        }

        return $arr;
    }

    // toDoctorCtrArray
    public static function toDoctorCtrArray (array $doctors = [], $needall = true): array {
        $arr = [];
        if ($needall) {
            $arr[0] = '0 全部';
        }

        if (empty($doctors)) {
            $rows = Dao::queryRows("select id,name from doctors ");

            foreach ($rows as $row) {
                $arr[$row['id']] = $row['id'] . " " . $row['name'];
            }
        } else {
            foreach ($doctors as $doctor) {
                $arr[$doctor->id] = $doctor->id . " " . $doctor->name;
            }
        }

        return $arr;
    }

    // toDoctorCtrArray
    public static function getDoctorCtrArrayForFilter ($needall = true): array {
        $rows = Dao::queryRows("select id,name from doctors ");

        $arr = [];
        if ($needall) {
            $arr['-1'] = '0 全部';
        }

        foreach ($rows as $row) {
            $arr["{$row['id']}"] = $row['id'] . " " . $row['name'];
        }

        return $arr;
    }

    // toDoctorCtrArray2
    public static function toDoctorCtrArray2 (array $doctors, $needNoSelect = true): array {
        $arr = array();
        if ($needNoSelect) {
            $arr[0] = '0 未选择';
        }

        foreach ($doctors as $a) {
            $arr[$a->id] = $a->id . " " . $a->name;
        }

        return $arr;
    }

    // toDoctorCtrArray3
    public static function toDoctorCtrArray3 (array $doctors): array {
        $arr = array();
        $arr[- 1] = '-1 全部医生';
        $arr[0] = '0 空医生';

        foreach ($doctors as $a) {
            $arr[$a->id] = $a->id . " " . $a->name;
        }

        return $arr;
    }

    // getDoctorStatusCtrArray
    public static function getDoctorStatusCtrArray ($needall = false): array {
        $arr = array();
        if ($needall) {
            $arr['-1'] = '全部';
        }
        $arr['0'] = '未开通';
        $arr['1'] = '开通';
        return $arr;
    }

    // getDoctorHaveLessonCtrArray
    public static function getDoctorHaveLessonCtrArray (): array {
        $arr = array();
        $arr['19'] = '项俊华';
        $arr['53'] = '肖侠明';
        return $arr;
    }

    // getSimpleDoctorMenzhenStatusCtrArray
    public static function getSimpleDoctorMenzhenStatusCtrArray ($needall = true): array {
        $arr = array();
        if ($needall) {
            $arr['-1'] = '全部';
        }
        $arr['0'] = '未开通';
        $arr['1'] = '开通';
        return $arr;
    }

    // getDoctorStatusCtrArray
    public static function getDoctorMenzhenStatusCtrArray ($needall = true): array {
        $arr = array();
        if ($needall) {
            $arr['-1'] = '全部';
        }
        $arr['0'] = '未开通';
        $arr['1'] = '立刻';
        $arr['28'] = '28 同意';
        $arr['168'] = '168 同意';
        return $arr;
    }
    // ---------- Doctor end ----------

    // ---------- ScheduleTpl beg ----------
    // toScheduleTplCtrArray
    public static function toScheduleTplCtrArray (array $scheduletpls, $needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '0 未选择';
        }
        foreach ($scheduletpls as $a) {
            $arr[$a->id] = $a->toStr();
        }
        return $arr;
    }
    // ---------- ScheduleTpl end ----------

    // ---------- Hospital beg ----------
    // getHospitalCtrArray
    public static function getHospitalCtrArray (): array {
        $cond = ' order by id limit 1500 ';
        $hospitals = Dao::getEntityListByCond("Hospital", $cond, []);
        return self::toHospitalCtrArray($hospitals);
    }

    // toHospitalCtrArray
    public static function toHospitalCtrArray (array $hospitals, $needAll = false, $forpatient = false): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '0 全部';
        }
        if ($forpatient) {
            $arr[''] = '请选择...';
        }
        foreach ($hospitals as $a) {
            $arr[$a->id] = $a->id . " " . $a->name;
        }
        return $arr;
    }

    // getCan_public_zhengdingCtrArray
    public static function getCan_public_zhengdingCtrArray (): array {
        $arr = array();
        $arr[0] = '否';
        $arr[1] = '是';
        return $arr;
    }
    // ---------- Hospital end ----------

    // ---------- Medicine begin----------
    // getMedicineCtrArray
    public static function getMedicineCtrArray (): array {
        $medicines = Dao::getEntityListByCond('Medicine');
        return self::toMedicineCtrArray($medicines);
    }

    // toMedicineCtrArray
    public static function toMedicineCtrArray ($medicines): array {
        $arr = array();
        foreach ($medicines as $a) {
            $arr[$a->id] = $a->name;
        }
        return $arr;
    }

    // toMedicineCtrArrayForAudit
    public static function toMedicineCtrArrayForAudit (array $medicines): array {
        $arr = array();
        foreach ($medicines as $a) {
            $arr[$a->id] = "{$a->id} {$a->name} ({$a->scientificname})";
        }
        return $arr;
    }

    public static function toMedicineCtrArrayForPmSideEffect (array $medicines): array {
        $arr = array(
            0 => '未确定');

        foreach ($medicines as $medicine) {
            $refs = $medicine->getDiseaseMedicineRefs();
            $tmp = $medicine->name;
            $tmp .= ' | ';
            foreach ($refs as $ref) {
                $tmp .= $ref->disease->name;
                $tmp .= ' ';
            }
            $arr[$medicine->id] = $tmp;
        }

        return $arr;
    }
    // ---------- Medicine end ----------

    // ---------- DealwithTpl beg ----------
    // getDealwithTplGroupstrCtrArray
    public static function getDealwithTplGroupstrCtrArray ($needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr['all'] = '全部';
        }

        $arr['reply'] = "回复";
        $arr['feedback'] = "反馈";
        $arr['ask'] = "问诊";
        $arr['remind'] = "提醒";
        $arr['notice'] = "通知";
        $arr['cui_revisit'] = "催复诊";
        $arr['cui_checkup'] = "催报告";
        $arr['cui_paper'] = "催量表";
        $arr['cui_profile'] = "催资料";
        $arr['other'] = "其他";

        return $arr;
    }

    // ---------- DealwithTpl end ----------

    // ---------- Pgroup beg ----------
    // getPgroupCtrArray
    public static function getPgroupCtrArray ($needAll = true, $diseaseid = 0): array {
        $bind = [];

        $cond = "";
        if ($diseaseid) {
            $cond .= " AND diseaseid = :diseaseid ";
            $bind[':diseaseid'] = $diseaseid;
        }

        $pgroups = Dao::getEntityListByCond('Pgroup', $cond, $bind);

        return self::toPgroupCtrArray($pgroups, $needAll);
    }

    // toPgroupCtrArray
    public static function toPgroupCtrArray (array $pgroups, $needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = '全部';
        }
        foreach ($pgroups as $a) {
            $arr[$a->id] = $a->name;
        }
        return $arr;
    }
    // ---------- Pgroup end ----------

    // ---------- Course beg ----------
    // toCourseCtrArray
    public static function toCourseCtrArray (array $courses): array {
        $arr = array();
        $arr[0] = "请选择";
        foreach ($courses as $a) {
            $arr[$a->id] = "{$a->title}" . "{$a->subtitle}";
        }

        return $arr;
    }
    // ---------- Course end ----------

    // ---------- PaperTpl beg ----------
    // toPaperTplCtrArray
    public static function toPaperTplCtrArray (array $papertpls, $all = false): array {
        $arr = array();

        if ($all) {
            $arr[0] = '全部';
        }

        foreach ($papertpls as $a) {
            $arr[$a->id] = $a->title;
        }

        return $arr;
    }

    public static function toNotEmptyPaperTplCtrArray (array $papertpls, $all = false): array {
        $arr = array();

        if ($all) {
            $arr[0] = '全部';
        }

        foreach ($papertpls as $a) {
            $arr[$a->id] = $a->title . " ({$a->id}) " . "<a target=\"_blank\" href=\"/xquestionsheetmgr/one?xquestionsheetid={$a->xquestionsheetid}\">" .
                "[{$a->xquestionsheet->getQuestionCnt()}个问题]" . "</a>";
        }

        return $arr;
    }
    // ---------- PaperTpl end ----------

    // ---------- WxShop beg ----------
    // getWxShopCtrArray
    public static function getWxShopCtrArray ($needAll = false): array {
        $cond = ' and id < 1000 order by id ';
        $wxshops = Dao::getEntityListByCond("WxShop", $cond, []);
        return self::toWxShopCtrArray($wxshops, $needAll);
    }

    // toWxShopCtrArray
    public static function toWxShopCtrArray (array $wxshops, $needAll = false, $showDiseaseName = false): array {
        $arr = array();
        if ($needAll) {
            $arr[0] = "0 全部";
        }
        foreach ($wxshops as $a) {
            $str = $a->id . " " . $a->name;

            // 20170419 TODO by sjp : 服务号主疾病
            if ($showDiseaseName) {
                $str .= " ({$a->disease->name})";
            }

            $arr[$a->id] = $str;
        }
        return $arr;
    }
    // ---------- WxShop end ----------

    // ---------- XUnitOfWork tableno begin ----------

    // getXUnitOfWorkTablenoCtrArray
    public static function getXUnitOfWorkTablenoCtrArray (): array {
        $the_month = date('Ym');
        $the_month_time = strtotime($the_month . "01");

        $month_arr = [];
        $month_arr['201611'] = '201611';
        $month_arr['201612'] = '201612';
        for ($i = 1; $i < 13; $i ++) {
            $str = "2017" . sprintf("%02d", $i);

            if (strtotime($str . "01") > $the_month_time) {
                continue;
            }

            $month_arr[$str] = $str;
        }

        for ($i = 1; $i < 13; $i ++) {
            $str = "2018" . sprintf("%02d", $i);

            if (strtotime($str . "01") > $the_month_time) {
                continue;
            }

            $month_arr[$str] = $str;
        }

        return $month_arr;
    }

    // ---------- XUnitOfWork tableno end ----------

    // ---------- StockItem beg ----------
    public static function getStockItemSourceArray ($needAll = true): array {
        $arr = array();
        if ($needAll) {
            $arr['all'] = '全部';
        }

        $arr['1药网'] = "1药网";
        $arr['健客网'] = "健客网";
        $arr['九州通'] = "九州通";
        $arr['安康嘉和'] = "安康嘉和";
        $arr['华润普仁鸿'] = "华润普仁鸿";
        $arr['天星普信'] = "天星普信";
        $arr['亚捷康立达'] = "亚捷康立达";
        $arr['华润医药集团'] = "华润医药集团";
        $arr['国控北京'] = "国控北京";
        $arr['国控康辰'] = "国控康辰";
        $arr['华润国康'] = "华润国康";
        $arr['兴盛源'] = "兴盛源";
        $arr['江苏济源'] = "江苏济源";
        $arr['珠海安生'] = "珠海安生";
        $arr['湖北万康源'] = "湖北万康源";
        $arr['河北冀北'] = "河北冀北";
        $arr['康德乐'] = "康德乐";
        $arr['科园信海'] = "科园信海";
        $arr['1号药城'] = "1号药城";
        $arr['嘉事堂'] = "嘉事堂";
        $arr['安徽华源'] = "安徽华源";
        $arr['药企'] = "药企";
        $arr['药店'] = "药店";
        $arr['医院'] = "医院";
        $arr['药代'] = "药代";
        $arr['淘宝'] = "淘宝";
        $arr['天猫'] = "天猫";
        $arr['京东'] = "京东";
        return $arr;
    }

    public static function getStockItemPayTypeArray ($needAll = true): array {
        $arr = [];
        if ($needAll) {
            $arr[0] = '全部';
        }

        $arr += StockItem::getPayTypeArr();
        return $arr;
    }

    public static function getStockItemHasInvoiceArray ($needAll = true): array {
        $arr = [];
        if ($needAll) {
            $arr[-1] = '全部';
        }

        $arr += StockItem::getHasInvoiceArr();
        return $arr;
    }
    // ---------- StockItem end ----------

    // ---------- MgtPlan beg ----------
    // getMgtPlanCtrArray
    public static function getMgtPlanCtrArray ($needAll = true): array {
        $arr = [];
        if($needAll){
            $arr["-1"] = "全部";
        }
        $arr["0"] = "基础管理计划";
        $mgtPlans = MgtPlanDao::getList();
        foreach($mgtPlans as $a){
            $arr[$a->id] = $a->title;
        }
        return $arr;
    }

    // filter
    public static function getMgtPlanCtrArrayForFilter ($needAll = true): array {
        $arr = [];
        if($needAll){
            $arr["-2"] = "全部";
        }
        $arr["-1"] = "基础管理计划";
        $mgtPlans = MgtPlanDao::getList();
        foreach($mgtPlans as $a){
            $arr[$a->id] = $a->title;
        }
        return $arr;
    }
    // ---------- MgtPlan end ----------

    // ---------- MgtGroupTpl beg ----------
    public static function getMgtGroupTplCtrArrayForFilter ($needAll = true): array {
        $arr = [];
        if($needAll){
            $arr["-2"] = "全部";
        }
        $arr["-1"] = "基础管理";
        $mgtGroupTpls = MgtGroupTplDao::getList();
        foreach($mgtGroupTpls as $a){
            $arr[$a->id] = $a->title;
        }
        return $arr;
    }
    // ---------- MgtGroupTpl end ----------




    // ---------- CdrMeeting beg ----------
    public static function getCdrMeetingStatusCtrArray () {
        $arr = [];
        $arr['all'] = '全部';
        $arr['connect_ok'] = '接通';
        $arr['other'] = '其他';

        return $arr;
    }
    // ---------- CdrMeeting end ----------

}
