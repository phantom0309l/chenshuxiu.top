<?php

/*
 * Rpt_week_patient
 */
class Rpt_week_patient extends Entity
{

    protected function init_database () {
        $this->database = 'statdb';
    }

    // 不需要记录xobjlog
    public function notXObjLog () {
        return true;
    }

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'mondaydate',  // 统计日期－周一
            'sundaydate',  // 统计日期－周末
            'baodaocnt',  // 报到总人数
            'baodao_scan_cnt',  // 扫码报到总人数
            'had_drug_cnt',  // 报到服药总人数
            'had_drug_cnt_limit4day',  // 报到4天内服药总人数
            'had_drug_ratio',  // 报到4天内服药总人数与扫码报到总人数的比值
            'had_drug_hadremind_cnt_limit4day',  // 报到4天内服药有催用药总人数
            'had_drug_noremind_cnt_limit4day',  // 报到4天内服药无催用药总人数
            'inpgroup_cnt',  // 报到入组总人数
            'inpgroup_cnt_limit4day',  // 报到4天内入组总人数
            'ingroup_ratio'); // 报到4天内入组总人数与报到入组总人数的比值
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["mondaydate"] = $mondaydate;
    // $row["sundaydate"] = $sundaydate;
    // $row["baodaocnt"] = $baodaocnt;
    // $row["baodao_scan_cnt"] = $baodao_scan_cnt;
    // $row["had_drug_cnt"] = $had_drug_cnt;
    // $row["had_drug_cnt_limit4day"] = $had_drug_cnt_limit4day;
    // $row["had_drug_ratio"] = $had_drug_ratio;
    // $row["had_drug_hadremind_cnt_limit4day"] =
    // $had_drug_hadremind_cnt_limit4day;
    // $row["had_drug_noremind_cnt_limit4day"] =
    // $had_drug_noremind_cnt_limit4day;
    // $row["remind_ratio"] = $remind_ratio;
    // $row["inpgroup_cnt"] = $inpgroup_cnt;
    // $row["inpgroup_cnt_limit4day"] = $inpgroup_cnt_limit4day;
    // $row["ingroup_ratio"] = $ingroup_ratio;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_week_patient::createByBiz row cannot empty");

        $default = array();
        $default["mondaydate"] = '';
        $default["sundaydate"] = '';
        $default["baodaocnt"] = 0;
        $default["baodao_scan_cnt"] = 0;
        $default["had_drug_cnt"] = 0;
        $default["had_drug_cnt_limit4day"] = 0;
        $default["had_drug_ratio"] = 0;
        $default["had_drug_hadremind_cnt_limit4day"] = 0;
        $default["had_drug_noremind_cnt_limit4day"] = 0;
        $default["inpgroup_cnt"] = 0;
        $default["inpgroup_cnt_limit4day"] = 0;
        $default["ingroup_ratio"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 获得实时的当周报到的入组人数
    public function getRealInpgroupcnt () {
        return PatientPgroupRefDao::getCntByDate($this->mondaydate, $this->sundaydate,
                " AND a.status = 1 and a.subscribe_cnt > 0 AND b.status < 3 group by a.id ");
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 根据传入的上周一，周日的时间得到一行要统计的数据
    public static function getRptRowByDate ($last_monday, $last_sunday) {
        $this_monday = date('Y-m-d', strtotime("$last_sunday + 1 days"));

        // echo ($last_monday);
        // echo ($last_sunday);

        $baodaocnt = PatientDao::getBaodaoCntByDate($last_monday, $this_monday);

        $had_drug_cnt = 0;
        $had_drug_cnt_limit4day = 0;

        $had_drug_hadremind_cnt_limit4day = 0;
        $had_drug_noremind_cnt_limit4day = 0;

        $inpgroup_cnt = 0;
        $inpgroup_cnt_limit4day = 0;

        $ids = PatientDao::getScanBaodaoCntByDate($last_monday, $this_monday);

        $baodao_scan_cnt = count($ids);

        foreach ($ids as $id) {
            // echo
            // ("\n----------------------------------------------------------------".$id);
            $patient = Patient::getById($id);
            if ($patient instanceof Patient) {
                // 该患者用药返回ture
                if (PatientMedicineRefDao::getCntByPatientid($id) > 0) {
                    $had_drug_cnt ++;
                }
                // 该患者报到４天内用药返回ture
                if (PatientMedicineRefDao::getCntByPatientid($id, " and date_sub(createtime, INTERVAL 4 DAY) < '{$patient->createtime}' ") > 0) {
                    $had_drug_cnt_limit4day ++;
                    // 该患者报到４天内用药有催用药返回ture
                    if (CommentDao::getCntByPatientid($id, " AND typestr = 'reminddrug' ") > 0) {
                        // 该患者报到４天内用药有催用药
                        $had_drug_hadremind_cnt_limit4day ++;
                    } else {
                        // 该患者报到４天内用药无催用药
                        $had_drug_noremind_cnt_limit4day ++;
                    }
                }
                // 该患者申请入组返回ture
                // if($this->getInpgroupCnt($patient) > 0){
                if (count(PatientPgroupRefDao::getListByPatientid($id, " AND status < 3 AND typestr = 'manage' ")) > 0) {
                    $inpgroup_cnt ++;
                }
                // 该患者报到４天内申请入组返回ture
                if ($patient->isInPgroup4Day()) {
                    $inpgroup_cnt_limit4day ++;
                }
            }
        }

        $row = array();
        $row['mondaydate'] = $last_monday;
        $row['sundaydate'] = $last_sunday;
        $row['baodaocnt'] = $baodaocnt;
        $row['baodao_scan_cnt'] = $baodao_scan_cnt;
        $row['had_drug_cnt'] = $had_drug_cnt;
        $row['had_drug_cnt_limit4day'] = $had_drug_cnt_limit4day;
        if ($baodao_scan_cnt) {
            $row['had_drug_ratio'] = round($had_drug_cnt_limit4day / $baodao_scan_cnt, 2) * 100;
        } else {
            $row['had_drug_ratio'] = 0;
        }
        $row['had_drug_hadremind_cnt_limit4day'] = $had_drug_hadremind_cnt_limit4day;
        $row['had_drug_noremind_cnt_limit4day'] = $had_drug_noremind_cnt_limit4day;
        $row['inpgroup_cnt'] = $inpgroup_cnt;
        $row['inpgroup_cnt_limit4day'] = $inpgroup_cnt_limit4day;
        if ($baodao_scan_cnt) {
            $row['ingroup_ratio'] = round($inpgroup_cnt_limit4day / $baodao_scan_cnt, 2) * 100;
        } else {
            $row['ingroup_ratio'] = 0;
        }

        return $row;
    }
}
