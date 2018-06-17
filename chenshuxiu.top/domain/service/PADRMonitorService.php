<?php

/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/8/31
 * Time: 11:46
 *
 * 患者不良反应监测
 */
class PADRMonitorService
{

    /**
     * 新建监测
     *
     * @param Patient $patient
     * @param
     *            $diseaseid
     * @param
     *            $ename
     * @param
     *            $prev_date
     * @return null|PADRMonitor
     */
    public static function createMonitorByEname (Patient $patient, $diseaseid, $ename, $prev_date) {
        $weekday = $patient->adrmonitor_weekday;

        $medicines = self::getMedicines($patient);

        $plan_date = null;
        $medicineid = null;
        foreach ($medicines as $mid => $medicine) {
            // 根据药物的规则获取下次监测日期
            $next_date = self::getNextPlanDate($patient, $diseaseid, $mid, $ename, $weekday, $prev_date);
            if (! empty($next_date) && (empty($plan_date) || $next_date < $plan_date)) {
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
        $row["type"] = 'monitor';
        $row["adrmonitorruleitem_ename"] = $ename;
        $row["weekday"] = $weekday;
        $row["prev_date"] = $prev_date;
        $row["plan_date"] = $plan_date;
        $padrmonitor = PADRMonitor::createByBiz($row);

        Debug::trace($padrmonitor);

        return $padrmonitor;
    }

    /**
     *
     * @param PADRMonitor $padrmonitor
     * @return PADRMonitor
     */
    public static function createVisitByMonitor (PADRMonitor $padrmonitor) {
        $weekday = $padrmonitor->patient->adrmonitor_weekday;

        $plan_date = date('Y-m-d', strtotime('+7 day', time()));

        $row = array();
        $row["patientid"] = $padrmonitor->patientid;
        $row["diseaseid"] = $padrmonitor->diseaseid;
        $row["medicineid"] = $padrmonitor->medicineid;
        $row["type"] = 'visit';
        $row["adrmonitorruleitem_ename"] = $padrmonitor->adrmonitorruleitem_ename;
        $row["weekday"] = $weekday;
        $row["prev_date"] = date('Y-m-d');
        $row["plan_date"] = $plan_date;
        $padrmonitor = PADRMonitor::createByBiz($row);

        return $padrmonitor;
    }

    /**
     *
     * @param PADRMonitor $padrmonitor
     * @return PADRMonitor
     */
    public static function createObserveByMonitor (PADRMonitor $padrmonitor) {
        $weekday = $padrmonitor->patient->adrmonitor_weekday;

        $plan_date = date('Y-m-d', strtotime('+7 day', time()));

        $row = array();
        $row["patientid"] = $padrmonitor->patientid;
        $row["diseaseid"] = $padrmonitor->diseaseid;
        $row["medicineid"] = $padrmonitor->medicineid;
        $row["type"] = 'observe';
        $row["adrmonitorruleitem_ename"] = $padrmonitor->adrmonitorruleitem_ename;
        $row["weekday"] = $weekday;
        $row["prev_date"] = date('Y-m-d');
        $row["plan_date"] = $plan_date;
        $padrmonitor = PADRMonitor::createByBiz($row);

        return $padrmonitor;
    }

    /**
     *
     * @param PADRMonitor $padrmonitor
     * @return PADRMonitor
     */
    public static function createSecondObserveByMonitor (PADRMonitor $padrmonitor) {
        $weekday = $padrmonitor->patient->adrmonitor_weekday;

        $plan_date = date('Y-m-d', strtotime('+7 day', time()));

        $row = array();
        $row["patientid"] = $padrmonitor->patientid;
        $row["diseaseid"] = $padrmonitor->diseaseid;
        $row["medicineid"] = $padrmonitor->medicineid;
        $row["type"] = 'second_observe';
        $row["adrmonitorruleitem_ename"] = $padrmonitor->adrmonitorruleitem_ename;
        $row["weekday"] = $weekday;
        $row["prev_date"] = date('Y-m-d');
        $row["plan_date"] = $plan_date;
        $padrmonitor = PADRMonitor::createByBiz($row);

        return $padrmonitor;
    }

    /**
     * 更新监测任务，用于生成就诊记录的时候
     * 关闭所有监测任务，重新根据用药生成监测任务
     *
     * @param Patient $patient
     * @param
     *            $diseaseid
     * @param RevisitRecord $revisitrecord
     */
    public static function updateMonitorByDoctorForRevisitRecord (Patient $patient, $diseaseid, RevisitRecord $revisitrecord) {
        Debug::trace("updateMonitorByDoctorForRevisitRecord");
        $record_date = $revisitrecord->thedate;

        $weekday = $patient->adrmonitor_weekday;

        // 患者正在吃的所有药物
        $medicines = self::getMedicines($patient);

        $plans = [];
        foreach ($medicines as $medicineid => $medicine) {
            // 获取药品所有的规则（按监测项目分组）
            $adrmritems = ADRMonitorRuleItemDao::getGroupListByMedicineidAndDiseaseid($medicineid, $diseaseid);
            foreach ($adrmritems as $adrmritem) {
                $ename = $adrmritem->ename;

                // 下一个监测日期
                $plan_date = self::getNextPlanDate($patient, $diseaseid, $medicineid, $ename, $weekday, $record_date);

                $plan = $plans[$ename];
                if (! empty($plan_date) && (empty($plan) || $plan_date < $plan["plan_date"])) { // 取最小日期
                    $plans[$ename] = [
                        "medicineid" => $medicineid,
                        "weekday" => $weekday,
                        "prev_date" => $record_date,
                        "plan_date" => $plan_date];
                }
            }
        }

        // 关闭所有任务
        // 需要结算
        $optasks = self::getAllOpenOpTask($patient->id);
        foreach ($optasks as $optask) {
            $padrmonitor = $optask->obj;

            $plan = $plans[$padrmonitor->adrmonitorruleitem_ename];

            $plan_date = $plan["plan_date"];
            if ($plan_date == $padrmonitor->plan_date) { // 新的监测日期和正在跑的监测日期是同一天，可能是医生数据库开药导致重复创建
                $plans[$padrmonitor->adrmonitorruleitem_ename] = null;
            } else {
                $optask->close();
                $padrmonitor->the_date = $record_date;
            }
        }

        self::createMonitorAndOpTaskByPlans($patient, $diseaseid, $plans);
    }

    /**
     * 更新监测任务，用于医生开医嘱用药
     * 关闭所有监测任务，重新根据用药生成监测任务
     *
     * @param Patient $patient
     * @param
     *            $diseaseid
     * @param PatientMedicinePkg $pmpkg
     */
    public static function updateMonitorByDoctorForPkg (Patient $patient, $diseaseid, PatientMedicinePkg $pmpkg) {
        Debug::trace("updateMonitorByDoctorForPkg");
        $record_date = $pmpkg->revisitrecord->thedate;

        $weekday = $patient->adrmonitor_weekday;

        // 患者正在吃的所有药物
        $medicines = self::getMedicines($patient);

        $plans = [];
        foreach ($medicines as $medicineid => $medicine) {
            // 获取药品所有的规则（按监测项目分组）
            $adrmritems = ADRMonitorRuleItemDao::getGroupListByMedicineidAndDiseaseid($medicineid, $diseaseid);
            foreach ($adrmritems as $adrmritem) {
                $ename = $adrmritem->ename;

                // 下一个监测日期
                $plan_date = self::getNextPlanDate($patient, $diseaseid, $medicineid, $ename, $weekday, $record_date);

                $plan = $plans[$ename];
                if (! empty($plan_date) && (empty($plan) || $plan_date < $plan["plan_date"])) { // 取最小日期
                    $plans[$ename] = [
                        "medicineid" => $medicineid,
                        "weekday" => $weekday,
                        "prev_date" => $record_date,
                        "plan_date" => $plan_date];
                }
            }
        }

        // 关闭所有任务
        // 需要结算
        $optasks = self::getAllOpenOpTask($patient->id);
        foreach ($optasks as $optask) {
            $padrmonitor = $optask->obj;

            $plan = $plans[$padrmonitor->adrmonitorruleitem_ename];

            $plan_date = $plan["plan_date"];
            if ($plan_date == $padrmonitor->plan_date) { // 新的监测日期和正在跑的监测日期是同一天，可能是医生数据库开药导致重复创建
                $plans[$padrmonitor->adrmonitorruleitem_ename] = null;
            } else {
                $optask->close();
                $padrmonitor->the_date = $record_date;
            }
        }

        self::createMonitorAndOpTaskByPlans($patient, $diseaseid, $plans);
    }

    /**
     * 运营给患者补实际用药
     *
     * 之前的任务，不需要监测的关闭，如果跟之前任务为监测任务，且监测任务时间不一致的，修改任务
     *
     * @param Patient $patient
     * @param
     *            $diseaseid
     */
    public static function updateMonitorByAuditorForDrug (Patient $patient, $diseaseid) {
        Debug::trace("updateMonitorByAuditorForDrug");
        self::updateMonitorForDrug($patient, $diseaseid);
    }

    /**
     * 患者填写实际用药
     *
     * 之前的任务，不需要监测的关闭，如果跟之前任务为监测任务，且监测任务时间不一致的，修改任务
     *
     * @param Patient $patient
     * @param
     *            $diseaseid
     */
    public static function updateMonitorByPatientForDrug (Patient $patient, $diseaseid) {
        Debug::trace("updateMonitorByPatientForDrug");
        self::updateMonitorForDrug($patient, $diseaseid);
    }

    /**
     * 实际用药
     *
     * 之前的任务，不需要监测的关闭，如果跟之前任务为监测任务，且监测任务时间不一致的，修改任务
     *
     * @param Patient $patient
     * @param
     *            $diseaseid
     */
    private static function updateMonitorForDrug (Patient $patient, $diseaseid) {
        $weekday = $patient->adrmonitor_weekday;

        // 患者正在吃的所有药物
        $medicines = self::getMedicines($patient);
        Debug::trace(json_encode($medicines));

        $prev_dates = [];
        $plans = [];
        foreach ($medicines as $medicineid => $medicine) {
            // 获取药品的规则（按监测项目分组）
            $adrmritems = ADRMonitorRuleItemDao::getGroupListByMedicineidAndDiseaseid($medicineid, $diseaseid);
            foreach ($adrmritems as $adrmritem) {
                $ename = $adrmritem->ename;

                $prev_date = $prev_dates[$ename] ?? null;
                if (empty($prev_date)) {
                    // 当前检测项目上次的检测记录
                    $padrmonitor = PADRMonitorDao::getLastMonitorByPatientidAndEname($patient->id, $ename);
                    if ($padrmonitor instanceof PADRMonitor) {
                        $prev_date = $padrmonitor->the_date;
                    }
                    // 是不是null都没关系
                    $prev_dates[$ename] = $prev_date;
                }
                // 下一个监测日期
                $plan_date = self::getNextPlanDate($patient, $diseaseid, $medicineid, $ename, $weekday, $prev_date);

                $plan = $plans[$ename];
                if (! empty($plan_date) && (empty($plan) || $plan_date < $plan["plan_date"])) { // 取最小日期
                    $plans[$ename] = [
                        "medicineid" => $medicineid,
                        "weekday" => $weekday,
                        "prev_date" => $prev_date ?? "0000-00-00",
                        "plan_date" => $plan_date];
                }
            }
        }

        Debug::trace(json_encode($plans));

        // 之前的任务，不需要监测的关闭，如果任务为监测任务，且任务时间与计算时间不一致的，修改任务
        $optasks = self::getAllOpenOpTask($patient->id);
        foreach ($optasks as $optask) {
            $padrmonitor = $optask->obj;
            if ($optask->optasktpl->subcode == 'monitor') { // 监测任务
                $plan = $plans[$padrmonitor->adrmonitorruleitem_ename];
                if (empty($plan)) { // 不存在则代表不需要监测了
                    $optask->close();
                } else {
                    $medicineid = $plan["medicineid"];
                    $weekday = $plan["weekday"];
                    $prev_date = $plan["prev_date"];
                    $plan_date = $plan["plan_date"];
                    if ($plan_date != $padrmonitor->plan_date) { // 与任务的时间不一样，修改任务和监测
                        $optask->plantime = $plan_date;

                        $padrmonitor->medicineid = $medicineid;
                        $padrmonitor->weekday = $weekday;
                        $padrmonitor->prev_date = $prev_date ?? "0000-00-00";
                        $padrmonitor->plan_date = $plan_date;
                    }
                    // 清空，不然会生成任务
                    $plans[$padrmonitor->adrmonitorruleitem_ename] = null;
                }
            } else { // 不是监测任务，则不对之前任务做任何操作
                     // 清空，不然会生成任务
                $plans[$padrmonitor->adrmonitorruleitem_ename] = null;
            }
        }

        self::createMonitorAndOpTaskByPlans($patient, $diseaseid, $plans);
    }

    /**
     * 运营给患者补医嘱用药
     *
     * 【运营补医嘱用药等同于医生门诊开药】
     * 运营补的有可能是之前的
     *
     * @param Patient $patient
     * @param
     *            $diseaseid
     * @param PatientMedicineTarget $pmTarget
     */
    public static function updateMonitorByAuditorForTarget (Patient $patient, $diseaseid, PatientMedicineTarget $pmTarget) {
        Debug::trace("updateMonitorByAuditorForTarget");
        $record_date = $pmTarget->getRecordDate();

        $weekday = $patient->adrmonitor_weekday;

        // 患者正在吃的所有药物
        $medicines = self::getMedicines($patient);

        $prev_dates = [];
        $plans = [];
        foreach ($medicines as $medicineid => $medicine) {
            // 获取药品所有的规则（按监测项目）
            $adrmritems = ADRMonitorRuleItemDao::getGroupListByMedicineidAndDiseaseid($medicineid, $diseaseid);
            foreach ($adrmritems as $adrmritem) {
                $ename = $adrmritem->ename;

                $prev_date = $prev_dates[$ename] ?? null;
                if (empty($prev_date)) {
                    // 当前检查项目最后一条监测记录
                    $padrmonitor = PADRMonitorDao::getLastCreateByPatientidAndEname($patient->id, $ename);
                    if ($padrmonitor instanceof PADRMonitor) {
                        $prev_date = $padrmonitor->plan_date;
                    }

                    if ($record_date > $prev_date) { // 医嘱用药日期大于监测日期，则用record_date当做上次检查日期
                        $prev_date = $record_date;
                    } else { // 医嘱日期小于上次计划日期，属于补数据
                             // 补数据的话，直接跳过
                        $plans[$ename] = [
                            "medicineid" => $medicineid,
                            "weekday" => $weekday,
                            "prev_date" => $padrmonitor->prev_date,
                            "plan_date" => $padrmonitor->plan_date];
                        $prev_dates[$ename] = $padrmonitor->prev_date;
                        continue;
                    }
                    $prev_dates[$ename] = $prev_date;
                }

                // 监测日期
                $plan_date = self::getNextPlanDate($patient, $diseaseid, $medicineid, $ename, $weekday, $prev_date);

                $plan = $plans[$ename];
                if (! empty($plan_date) && (empty($plan) || $plan_date < $plan["plan_date"])) { // 取最小日期
                    $plans[$ename] = [
                        "medicineid" => $medicineid,
                        "weekday" => $weekday,
                        "prev_date" => $prev_date ?? "0000-00-00",
                        "plan_date" => $plan_date];
                }
            }
        }

        // 之前的任务，不需要监测的关闭，如果任务为监测任务，且任务时间与计算时间不一致的，更新监测日期，关闭任务
        $optasks = self::getAllOpenOpTask($patient->id);
        foreach ($optasks as $optask) {
            $padrmonitor = $optask->obj;
            if ($optask->optasktpl->subcode == 'monitor') { // 监测任务
                $plan = $plans[$padrmonitor->adrmonitorruleitem_ename];
                if (empty($plan)) { // 不存在则代表不需要监测了
                    $optask->close();
                } else {
                    $plan_date = $plan["plan_date"];
                    if ($record_date > date("Y-m-d", strtotime($optask->plantime))) { // 医嘱用药时间大于任务时间，更新监测记录的the_date并关闭任务
                        $padrmonitor->the_date = $record_date;
                        $optask->close();
                    } elseif ($plan_date != $padrmonitor->plan_date) { // 计划监测日期与任务的时间不一致，修改任务和监测
                        $medicineid = $plan["medicineid"];
                        $weekday = $plan["weekday"];
                        $prev_date = $plan["prev_date"];

                        $optask->plantime = $plan_date;

                        $padrmonitor->medicineid = $medicineid;
                        $padrmonitor->weekday = $weekday;
                        $padrmonitor->prev_date = $prev_date ?? "0000-00-00";
                        $padrmonitor->plan_date = $plan_date;
                    }
                    // 清空，不然会生成任务
                    $plans[$padrmonitor->adrmonitorruleitem_ename] = null;
                }
            } else { // 不是监测任务，则不对之前任务做任何操作
                     // 清空，不然会生成任务
                $plans[$padrmonitor->adrmonitorruleitem_ename] = null;
            }
        }

        self::createMonitorAndOpTaskByPlans($patient, $diseaseid, $plans);
    }

    /**
     * 根据监测项目生成监测和任务
     *
     * @param Patient $patient
     * @param
     *            $diseaseid
     * @param
     *            $plans
     */
    public static function createMonitorAndOpTaskByPlans (Patient $patient, $diseaseid, $plans) {
        if (empty($plans)) {
            return;
        }

        // 如果有新的监测项目，生成新的不良反应监测及任务
        foreach ($plans as $ename => $value) {
            if (empty($value)) {
                continue;
            }
            $medicineid = $value["medicineid"];
            $prev_date = $value["prev_date"];
            $plan_date = $value["plan_date"];
            $weekday = $value["weekday"];

            $row = array();
            $row["patientid"] = $patient->id;
            $row["diseaseid"] = $diseaseid;
            $row["medicineid"] = $medicineid;
            $row["type"] = 'monitor';
            $row["adrmonitorruleitem_ename"] = $ename;
            $row["weekday"] = $weekday;
            $row["prev_date"] = $prev_date ?? "0000-00-00";
            $row["plan_date"] = $plan_date;
            $padrmonitor = PADRMonitor::createByBiz($row);

            // 生成任务: 不良反应监测任务
            $optask = OpTaskService::createPatientOpTask($patient, 'padrmonitor:monitor', $padrmonitor, $plan_date);
        }
    }

    public static function getAllOpenOpTask ($patientid) {
        $cond = " AND patientid = :patientid AND objtype = :objtype AND code = :code AND status <> 1 ";
        $bind = [
            ":patientid" => $patientid,
            ":objtype" => "PADRMonitor",
            ":code" => "padrmonitor"];
        return Dao::getEntityListByCond("OpTask", $cond, $bind);
    }

    /**
     * 获取患者正在吃的所有药物
     * 包括医嘱用药和实际用药
     *
     * @param Patient $patient
     * @return array
     */
    public static function getMedicines (Patient $patient) {
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
        foreach ($pmsitems as $pmsitem) { // 这步是为了合并药品
            $medicines[$pmsitem->medicineid] = $pmsitem->medicine;
        }

        return $medicines;
    }

    /**
     * 获取首次用药时间
     *
     * @param
     *            $diseaseid
     * @param
     *            $patientid
     * @param
     *            $medicineid
     * @return bool|mixed|string
     */
    public static function getFirstDrugDate ($patientid, $diseaseid, $medicineid) {
        $lastStop_pmsitem = PatientMedicineSheetItemDao::getLastStopByPatientidAndMedicineid($patientid, $medicineid);
        // MARK: - 如果该药停药超过3个月，则以该停药后首次的服药记录时间为 有效首次用药时间
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
            if ($diff > 3) { // 停药超过3个月
                return $pmsitem->getDrugDate();
            }
        }

        if (self::isNMO($diseaseid, $medicineid)) {
            $pmsitem = self::getFirstPMSIForNMO($patientid);
        } else {
            $pmsitem = PatientMedicineSheetItemDao::getFirstDrugByPatientidAndMedicineid($patientid, $medicineid);
        }

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
     * 当前处于第几周
     *
     * @param
     *            $date1
     * @param null $date2
     * @return float|int
     */
    public static function getWeek ($date1, $date2 = null) {
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
     * @param
     *            $diseaseid
     * @param
     *            $medicineid
     * @param
     *            $ename
     * @param null $weekday
     * @param null $prev_date
     * @return false|string
     */
    public static function getNextPlanDate (Patient $patient, $diseaseid, $medicineid, $ename, $weekday = null, $prev_date = null) {
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
        if (false == $msmritem instanceof ADRMonitorRuleItem) { // 不存在符合当前时间的监测规则
            Debug::trace("不存在符合当前时间的监测规则");
            if ($week < 1) {
                Debug::trace("因为周数小于1，尝试改为1再次获取");
                $msmritem = ADRMonitorRuleItemDao::getByMedicineidAndDiseaseidAndWeekAndEname($medicineid, $diseaseid, 1, $ename);
                if (false == $msmritem instanceof ADRMonitorRuleItem) { // 不存在符合当前时间的监测规则
                    return null;
                }
            } else {
                return null;
            }
        }

        // 周几监测
        // 传进来的weekday是为了应对 还没有 监测任务
        if (empty($weekday)) {
            $weekday = $patient->adrmonitor_weekday;
        }
        Debug::trace("计划每 {$msmritem->week_interval} 周 的 周 {$weekday} 监测");

        if (empty($prev_date)) { // 适用于创建首次任务的时候
                                 // 计划监测日期
            $offset_week = ceil($week / $msmritem->week_interval) * $msmritem->week_interval;
            $plan_time = strtotime("+{$offset_week} week", strtotime($firstDrugDate));
            $plan_date = date("Y-m-d", $plan_time);

            // 计划监测日期属于周几 0-6，0为周末，转成7了
            $plan_weekday = date("w", $plan_time);
            $plan_weekday = $plan_weekday == 0 ? 7 : $plan_weekday;
            Debug::trace("计划监测日期：{$plan_date} [周 {$plan_weekday}]");

            // 偏移天数，根据监测计划上的weekday，算出偏移的天数，前后找，挨着哪个周的近，就用哪个
            $offset_day = $weekday - $plan_weekday;
            if ($offset_day > 3 || $offset_day < - 3) {
                $offset_day = 7 + $offset_day;
            }
            Debug::trace("偏移天数：{$offset_day}");

            // 下次监测日期
            $next_date = date("Y-m-d", strtotime("+{$offset_day} day", strtotime($plan_date)));
            Debug::trace("下次监测日期：{$next_date}");
        } else {
            // MARK: - 根据上次监测日期生成下次监测日期。
            // 就算生成的是过去时间，也没关系，运营把新生成的监测任务，手动设置为 约定跟进 节点，日期设置为跟患者约定好的日期就可以了。
            $prev_date_weekday = date("w", $prev_date);
            $prev_date_weekday = $prev_date_weekday == 0 ? 7 : $prev_date_weekday;
            Debug::trace("上次监测日期： {$prev_date} [周 {$prev_date_weekday}]");
            // 计划监测日期
            $offset_week = $msmritem->week_interval;
            $plan_time = strtotime("+{$offset_week} week", strtotime($prev_date));
            $plan_date = date("Y-m-d", $plan_time);

            // 计划监测日期属于周几 0-6，0为周末，转成7了
            $plan_weekday = date("w", $plan_time);
            $plan_weekday = $plan_weekday == 0 ? 7 : $plan_weekday;
            Debug::trace("计划监测日期：{$plan_date} [周 {$plan_weekday}]");

            // 偏移天数，根据监测计划上的weekday，算出偏移的天数，前后找，挨着哪个周的近，就用哪个
            $offset_day = $weekday - $plan_weekday;
            if ($offset_day > 3 || $offset_day < - 3) {
                $offset_day = 7 + $offset_day;
            }
            Debug::trace("偏移天数：{$offset_day}");

            // 下次监测日期
            $next_date = date("Y-m-d", strtotime("+{$offset_day} day", strtotime($plan_date)));
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
     * @param
     *            $patientid
     * @return null
     */
    public static function getLastPMSIForNMO ($patientid) {
        $matimaikaofenzhiStr = PADRMonitor::getMtmkfzMidsStr(); // 吗替麦考酚酯
        $liucuopiaolingStr = PADRMonitor::getLcplMidsStr(); // 硫锉嘌呤
        $qianglvkuiStr = PADRMonitor::getQlkMidsStr(); // 羟氯喹

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
        // DBC::requireTrue($pmsitem instanceof PatientMedicineSheetItem,
        // '不存在需要监测的药品');
        // DBC::requireTrue($pmsitem->status != 3, '该患者已停药');
        if (false == $pmsitem instanceof PatientMedicineSheetItem || $pmsitem->status == 3) { // 实际用药不存在或患者已停药
            return null;
        }

        return $pmsitem;
    }

    /**
     * 获取首次用药时间
     *
     * @param
     *            $patientid
     * @return null
     */
    public static function getFirstPMSIForNMO ($patientid) {
        $matimaikaofenzhiArr = PADRMonitor::getMtmkfzMids(); // 吗替麦考酚酯
        $matimaikaofenzhiStr = PADRMonitor::getMtmkfzMidsStr(); // 吗替麦考酚酯
        $liucuopiaolingArr = PADRMonitor::getLcplMids(); // 硫锉嘌呤
        $liucuopiaolingStr = PADRMonitor::getLcplMidsStr(); // 硫锉嘌呤
        $qianglvkuiArr = PADRMonitor::getQlkMids(); // 羟氯喹
        $qianglvkuiStr = PADRMonitor::getQlkMidsStr(); // 羟氯喹

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
     * @param
     *            $diseaseid
     * @param
     *            $medicineid
     * @return bool
     */
    public static function isNMO ($diseaseid, $medicineid) {
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