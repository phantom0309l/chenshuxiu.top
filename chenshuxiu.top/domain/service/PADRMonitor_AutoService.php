<?php

/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/11/1
 * Time: 11:24
 *
 * 不良反应监测（自动化）用于 #4725 新版的业务自动化
 */
class PADRMonitor_AutoService
{

    /**
     * 开启不良反应监测业务byPatient
     *
     * @param Patient $patient
     * @param $diseaseid
     * @param array $prev_dates
     */
    public static function openMonitorByPatient(Patient $patient, $diseaseid, $prev_dates = []) {
        $patient->is_adr_monitor = 1;

        // 患者正在吃的所有药物
        $medicines = self::getMedicines($patient);

        $arr = [];
        foreach ($medicines as $medicineid => $medicine) {
            // 获取药品所有的规则（按监测项目分组）
            $adrmritems = ADRMonitorRuleItemDao::getGroupListByMedicineidAndDiseaseid($medicineid, $diseaseid);
            foreach ($adrmritems as $adrmritem) {
                $ename = $adrmritem->ename;
                $prev_date = $prev_dates[$ename];

                // 下一个监测日期
                $next_plan_date = self::getNextPlanDate($patient, $diseaseid, $medicineid, $ename, $prev_date);

                $item = $arr[$ename];
                if (!empty($next_plan_date) && (empty($item) || $next_plan_date < $item['plan_date'])) {  // 取最小日期
                    $arr[$ename] = [
                        "medicineid" => $medicineid,
                        "prev_date" => $prev_date,
                        "plan_date" => $next_plan_date,
                    ];
                }
            }
        }

        foreach ($arr as $ename => $item) {
            $row = array();
            $row["patientid"] = $patient->id;
            $row["diseaseid"] = $diseaseid;
            $row["medicineid"] = $item['medicineid'];
            $row["type"] = "monitor";
            $row["prev_date"] = $item['prev_date'] ?? '0000-00-00';
            $row["plan_date"] = $item['plan_date'];
            $row["adrmonitorruleitem_ename"] = $ename;
            PADRMonitor::createByBiz($row);
        }
    }

    /**
     * 开启不良反应监测业务byAuditor
     *
     * @param Patient $patient
     * @param $diseaseid
     * @param array $next_dates
     * @return array
     */
    public static function openMonitorByAuditor(Patient $patient, $diseaseid, $next_dates = []) {
        $patient->is_adr_monitor = 1;

        $arr = [];
        foreach ($next_dates as $ename => $value) {
            $row = array();
            $row["patientid"] = $patient->id;
            $row["diseaseid"] = $diseaseid;
//            $row["medicineid"] = $item['medicineid'];
            $row["type"] = "monitor";
//            $row["prev_date"] = $item['prev_date'];
            $row["plan_date"] = $value;
            $row["adrmonitorruleitem_ename"] = $ename;
            $padrmonitor = PADRMonitor::createByBiz($row);
            $arr[$ename] = $padrmonitor;
        }
        return $arr;
    }

    /**
     * 尝试关闭不良反应监测业务(#5225)
     *
     * @param Patient $patient
     * @return bool
     */
    public static function tryCloseMonitor(Patient $patient) {
        // 患者正在吃的所有药物
        $medicines = self::getMedicines($patient);

        $diseaseid = $patient->diseaseid;

        foreach ($medicines as $medicineid => $medicine) {
            // 获取药品所有的规则（按监测项目分组）
            $adrmritems = ADRMonitorRuleItemDao::getGroupListByMedicineidAndDiseaseid($medicineid, $diseaseid);
            // 如果药品有对应的监控规则，则return false
            if (!empty($adrmritems)) {
                return false;
            }
        }

        self::closeMonitor($patient);
        return true;
    }

    /**
     * 关闭不良反应监测业务
     *
     * @param Patient $patient
     */
    public static function closeMonitor(Patient $patient) {
        $patient->is_adr_monitor = 0;

        $padrmonitors = PADRMonitorDao::getListByPatientid($patient->id, " and status in (0, 1) ");
        foreach ($padrmonitors as $padrmonitor) {
            $padrmonitor->status = 3;
        }
    }

    /**
     * 通过上次检查日期新建监测
     *
     * @param Patient $patient
     * @param $type
     * @param $diseaseid
     * @param $ename
     * @param $prev_date
     * @return null|PADRMonitor
     */
    public static function createByPrevDate(Patient $patient, $diseaseid, $type, $ename, $prev_date = null) {
        $medicines = self::getMedicines($patient);

        $plan_date = null;
        $medicineid = null;
        foreach ($medicines as $mid => $medicine) {
            // 根据药物的规则获取下次监测日期
            $next_date = self::getNextPlanDate($patient, $diseaseid, $mid, $ename, $prev_date);
            if (!empty($next_date) && (empty($plan_date) || $next_date < $plan_date)) {
                $plan_date = $next_date;
                $medicineid = $mid;
            }
            Debug::trace("plan_date：" . $plan_date);
        }

        if (empty($medicineid)) {
            Debug::trace("---------------------不存在需要监测的药品---------------------");
            return null;
        }
        $row = array();
        $row["patientid"] = $patient->id;
        $row["diseaseid"] = $diseaseid;
        $row["medicineid"] = $medicineid;
        $row["type"] = $type;
        $row["adrmonitorruleitem_ename"] = $ename;
        $row["prev_date"] = $prev_date ?? '0000-00-00';
        $row["plan_date"] = $plan_date;
        $padrmonitor = PADRMonitor::createByBiz($row);

        Debug::trace($padrmonitor);

        return $padrmonitor;
    }

    /**
     * 通过下次检查日期新建监测
     *
     * @param Patient $patient
     * @param $diseaseid
     * @param $medicineid
     * @param $type
     * @param $ename
     * @param $plan_date
     * @param null $prev_date
     * @return PADRMonitor
     */
    public static function createByPlanDate(Patient $patient, $diseaseid, $medicineid, $type, $ename, $plan_date, $prev_date = null) {
        $row = array();
        $row["patientid"] = $patient->id;
        $row["diseaseid"] = $diseaseid;
        $row["medicineid"] = $medicineid;
        $row["type"] = $type;
        $row["prev_date"] = $prev_date;
        $row["plan_date"] = $plan_date;
        $row["adrmonitorruleitem_ename"] = $ename;
        $padrmonitor = PADRMonitor::createByBiz($row);

        Debug::trace($padrmonitor);

        return $padrmonitor;
    }

    /**
     * 获取患者正在吃的所有药物
     * 包括医嘱用药和实际用药
     *
     * @param Patient $patient
     * @return array
     */
    public static function getMedicines(Patient $patient) {
        $medicines = [];
        // 医嘱用药
        $pmtargets = PatientMedicineTargetDao::getListByPatient($patient);
        foreach ($pmtargets as $pmtarget) { // 过滤掉停药的应用药
            $status = $pmtarget->getNewestDrugStatus();
            if ($status != 3) {
                $medicines[$pmtarget->medicineid] = $pmtarget->medicine;
            }
        }

        // 实际用药
        $pmsitems = PatientMedicineSheetItemDao::getTakingListByPatientid($patient->id);
        foreach ($pmsitems as $pmsitem) {   // 这步是为了合并药品
            $medicines[$pmsitem->medicineid] = $pmsitem->medicine;
        }

        return $medicines;
    }

    /**
     * 获取首次用药时间
     *
     * @param $diseaseid
     * @param $patientid
     * @param $medicineid
     * @return bool|mixed|string
     */
    public static function getFirstDrugDate($patientid, $diseaseid, $medicineid) {
        if (empty($medicineid)) {
            return false;
        }
        $lastStop_pmsitem = PatientMedicineSheetItemDao::getLastStopByPatientidAndMedicineid($patientid, $medicineid);
        //MARK: - 如果该药停药超过3个月，则以该停药后首次的服药记录时间为 有效首次用药时间
        if ($lastStop_pmsitem instanceof PatientMedicineSheetItem) { // 停过药
            $sql = "SELECT a.*
                FROM patientmedicinesheetitems a
                LEFT JOIN patientmedicinesheets b ON b.id = a.patientmedicinesheetid
                WHERE b.patientid = :patientid
                AND a.medicineid = :medicineid
                AND a.drug_date >= :drug_date
                ORDER BY a.drug_date ASC
                LIMIT 1";
            $bind = [];
            $bind[':patientid'] = $patientid;
            $bind[':medicineid'] = $medicineid;
            $bind[':drug_date'] = $lastStop_pmsitem->drug_date;
            $pmsitem = Dao::loadEntityList("PatientMedicineSheetItem", $sql, $bind);

            $diff = XDateTime::getDateDiff($lastStop_pmsitem->drug_date, $pmsitem->drug_date) / 30;
            if ($diff > 3) {    // 停药超过3个月
                return $pmsitem->getDrugDate();
            }
        }

        $pmsitem = self::getFirstPMSI($patientid, $diseaseid, $medicineid);
//        if (self::isNMOMedicine($diseaseid, $medicineid)) {
//            $pmsitem = self::getFirstPMSIForNMO($patientid);
//        } else {
//            $pmsitem = PatientMedicineSheetItemDao::getFirstDrugByPatientidAndMedicineid($patientid, $medicineid);
//        }

        if ($pmsitem instanceof PatientMedicineSheetItem) {
            return $pmsitem->getDrugDate();
        } else {
            // 如果没有实际用药记录，则使用核对用药，当天开当天吃
            $patient = Patient::getById($patientid);
            $medicine = Medicine::getById($medicineid);
            $pmTarget = PatientMedicineTargetDao::getByPatientMedicine($patient, $medicine);
            if ($pmTarget instanceof PatientMedicineTarget && $pmTarget->getNewestDrugStatus() != 3) {
                return $pmTarget->record_date == '0000-00-00' ? substr($pmTarget->createtime, 0, 10) : $pmTarget->record_date;
            } else {
                return false;
            }
        }
    }

    /**
     * 获取首次用药记录
     *
     * @param $patientid
     * @param $diseaseid
     * @param $medicineid
     * @return null
     */
    public static function getFirstPMSI($patientid, $diseaseid, $medicineid) {
        $sql = "SELECT medicineid
                FROM adrmonitorrules
                WHERE medicine_common_name = (
                    SELECT medicine_common_name
                    FROM adrmonitorrules
                    WHERE medicineid = :medicineid
                    AND diseaseid = :diseaseid
                    LIMIT 1
                )";
        $bind = [];
        $bind[':medicineid'] = $medicineid;
        $bind[':diseaseid'] = $diseaseid;
        $medicineids = Dao::queryValues($sql, $bind);
        if (empty($medicineids)) {
            return null;
        }
        $medicineids = implode(',', $medicineids);

        $sql = "SELECT a.*
                FROM patientmedicinesheetitems a
                LEFT JOIN patientmedicinesheets b ON b.id = a.patientmedicinesheetid
                WHERE b.patientid = :patientid
                AND a.medicineid in ({$medicineids}) 
                AND a.status != 3
                ORDER BY a.drug_date ASC
                LIMIT 1";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $first_pmsi = Dao::loadEntity("PatientMedicineSheetItem", $sql, $bind);

        return $first_pmsi;
    }

    /**
     * 当前处于第几周
     *
     * @param $date1
     * @param null $date2
     * @return float|int
     */
    public static function getWeek($date1, $date2 = null) {
        if (empty($date2)) {
            // 当前日期
            $date2 = date('Y-m-d');
            Debug::trace("当前日期：{$date2}");
        }

        // 当前处于第几周
        $time0 = strtotime($date1);
        $time1 = strtotime($date2);

        $week = ($time1 - $time0) / (86400 * 7);
        return $week;
    }

    /**
     * 获取下一个监测日期
     *
     * @param Patient $patient
     * @param $diseaseid
     * @param $medicineid
     * @param $ename
     * @param null $prev_date
     * @return null|string
     */
    public static function getNextPlanDate(Patient $patient, $diseaseid, $medicineid, $ename, $prev_date = null) {
        // 获取首次用药时间
        $firstDrugDate = self::getFirstDrugDate($patient->id, $diseaseid, $medicineid);
        if ($firstDrugDate == false) { // 用药记录不存在
            return null;
        }

        // 首次用药时间
        $firstDrugDate = substr($firstDrugDate, 0, 10);
        Debug::trace("首次用药日期：{$firstDrugDate}");

        $week = self::getWeek($firstDrugDate);
        Debug::trace("当前处于第 {$week} 周");

        // 符合当前时间的监测规则
        $msmritem = ADRMonitorRuleItemDao::getByMedicineidAndDiseaseidAndWeekAndEname($medicineid, $diseaseid, $week, $ename);
        if (false == $msmritem instanceof ADRMonitorRuleItem) {    // 不存在符合当前时间的监测规则
            Debug::trace("不存在符合当前时间的监测规则");
            if ($week < 1) {
                Debug::trace("因为周数小于1，尝试改为1再次获取");
                $msmritem = ADRMonitorRuleItemDao::getByMedicineidAndDiseaseidAndWeekAndEname($medicineid, $diseaseid, 1, $ename);
                if (false == $msmritem instanceof ADRMonitorRuleItem) {    // 不存在符合当前时间的监测规则
                    return null;
                }
            } else {
                return null;
            }
        }

        Debug::trace("当前处于[{$msmritem->week_from},{$msmritem->week_to})区间,间隔周期：{$msmritem->week_interval}");

        if (empty($prev_date)) {    // 适用于创建首次任务的时候
            // 计划监测日期
            $offset_week = ceil($week / $msmritem->week_interval) * $msmritem->week_interval;
            $plan_time = strtotime("+{$offset_week} week", strtotime($firstDrugDate));
            $next_date = date("Y-m-d", $plan_time);

            Debug::trace("下次监测日期：{$next_date}");
        } else {
            // MARK: - 根据上次监测日期生成下次监测日期。
            // 就算生成的是过去时间，也没关系，运营把新生成的监测任务，手动设置为 约定跟进 节点，日期设置为跟患者约定好的日期就可以了。
            $prev_date_weekday = date("w", $prev_date);
            $prev_date_weekday = $prev_date_weekday == 0 ? 7 : $prev_date_weekday;
            Debug::trace("上次监测日期： {$prev_date} [周 {$prev_date_weekday}]");

            // 避免出现首次用药日期晚于上次检测日期的情况
            $the_date = $firstDrugDate > $prev_date ? $firstDrugDate : $prev_date;

            // 计划监测日期
            $offset_week = $msmritem->week_interval;
            $plan_time = strtotime("+{$offset_week} week", strtotime($the_date));
            $next_date = date("Y-m-d", $plan_time);

            Debug::trace("下次监测日期：{$next_date}");
        }

        return $next_date;
    }

    // ====================================
    // -------------- For NMO -------------
    // ====================================
    /**
     * 获取患者最新的用药记录
     *
     * @param $patientid
     * @return null
     */
    public static function getLastPMSIForNMO($patientid) {
        $matimaikaofenzhiStr = PADRMonitor::getMtmkfzMidsStr();    // 吗替麦考酚酯
        $liucuopiaolingStr = PADRMonitor::getLcplMidsStr();  // 硫锉嘌呤
        $qianglvkuiStr = PADRMonitor::getQlkMidsStr();  // 羟氯喹

        // 药品是互斥的
        // 先获取最新的用药
        $sql = "SELECT a.*
                FROM patientmedicinesheetitems a
                LEFT JOIN patientmedicinesheets b ON b.id = a.patientmedicinesheetid
                WHERE b.patientid = :patientid 
                AND a.medicineid in ({$matimaikaofenzhiStr}, {$liucuopiaolingStr}, {$qianglvkuiStr}) 
                ORDER BY a.drug_date DESC 
                LIMIT 1";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $pmsitem = Dao::loadEntity("PatientMedicineSheetItem", $sql, $bind);
//        DBC::requireTrue($pmsitem instanceof PatientMedicineSheetItem, '不存在需要监测的药品');
//        DBC::requireTrue($pmsitem->status != 3, '该患者已停药');
        if (false == $pmsitem instanceof PatientMedicineSheetItem || $pmsitem->status == 3) {   // 实际用药不存在或患者已停药
            return null;
        }

        return $pmsitem;
    }

    /**
     * 获取首次用药时间
     *
     * @param $patientid
     * @return null
     */
    public static function getFirstPMSIForNMO($patientid) {
        $matimaikaofenzhiArr = PADRMonitor::getMtmkfzMids();    // 吗替麦考酚酯
        $matimaikaofenzhiStr = PADRMonitor::getMtmkfzMidsStr();    // 吗替麦考酚酯
        $liucuopiaolingArr = PADRMonitor::getLcplMids();  // 硫锉嘌呤
        $liucuopiaolingStr = PADRMonitor::getLcplMidsStr();  // 硫锉嘌呤
        $qianglvkuiArr = PADRMonitor::getQlkMids();  // 羟氯喹
        $qianglvkuiStr = PADRMonitor::getQlkMidsStr();  // 羟氯喹

        $last_pmsi = self::getLastPMSIForNMO($patientid);
        if (false == $last_pmsi instanceof PatientMedicineSheetItem) {
            return null;
        }

        // 然后开始获取最早用药记录
        // startdate 用最早的同类药品的drug_date
        $medicineids = '';
        if (in_array($last_pmsi->medicineid, $matimaikaofenzhiArr)) {
            $medicineids = $matimaikaofenzhiStr;
        } elseif (in_array($last_pmsi->medicineid, $liucuopiaolingArr)) {
            $medicineids = $liucuopiaolingStr;
        } elseif (in_array($last_pmsi->medicineid, $qianglvkuiArr)) {
            $medicineids = $qianglvkuiStr;
        }
        $sql = "SELECT a.*
                FROM patientmedicinesheetitems a
                LEFT JOIN patientmedicinesheets b ON b.id = a.patientmedicinesheetid
                WHERE b.patientid = :patientid
                AND a.medicineid in ({$medicineids}) 
                ORDER BY a.drug_date ASC
                LIMIT 1";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $first_pmsi = Dao::loadEntity("PatientMedicineSheetItem", $sql, $bind);

        return $first_pmsi;
    }

    /**
     * 是否属于NMO药品
     *
     * @param $diseaseid
     * @param $medicineid
     * @return bool
     */
    public static function isNMOMedicine($diseaseid, $medicineid) {
        if ($diseaseid != 3) {
            return false;
        }
        $lcplmids = PADRMonitor::getLcplMids();
        $mtmkfzmids = PADRMonitor::getMtmkfzMids();
        $qlkmids = PADRMonitor::getQlkMids();
        $arr = array_merge($lcplmids, $mtmkfzmids, $qlkmids);
        if (in_array($medicineid, $arr)) {
            return true;
        }
    }
}