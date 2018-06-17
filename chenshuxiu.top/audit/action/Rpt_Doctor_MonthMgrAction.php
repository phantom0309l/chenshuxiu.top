<?php

class Rpt_Doctor_MonthMgrAction extends AuditBaseAction
{

    // 医生月统计列表
    public function doList () {
        $year = XRequest::getValue("year", "2016");
        $months = XDateTime::getMonthArrByYear($year);

        $doctorsArr = $this->getDoctorsData($months);
        $patientsArr = $this->getPatientsData($months);
        $doctorCntArr = $this->getDoctorCntData($months);

        XContext::setValue("doctorsArr", $doctorsArr);
        XContext::setValue("patientsArr", $patientsArr);
        XContext::setValue("doctorCntArr", $doctorCntArr);
        XContext::setValue("year", $year);
        return self::SUCCESS;
    }

    private function getDoctorsData ($months) {
        $arr = [];
        $sql = " SELECT
            left(themonth, 7) as themonth,
            count(*) as cnt
        FROM rpt_doctor_months
        WHERE 1=1 ";

        // 进患者医生数：当月入1个患者以上（含）
        $cond = " and patient_cnt_baodao_scan >= 1 GROUP BY left(themonth, 7)";
        $doctorcnt_hascomeArr = Dao::queryRows($sql . $cond, [], 'statdb');

        // 活跃医生：当月入5个患者以上（含）
        $cond = " and patient_cnt_baodao_scan >= 5 GROUP BY left(themonth, 7)";
        $doctorcnt_activeArr = Dao::queryRows($sql . $cond, [], 'statdb');

        // 开通医生数(本月)：本月开通的医生数
        $cond = " and month_offsetcnt = 1 GROUP BY left(themonth, 7)";
        $doctorcnt_monthArr = Dao::queryRows($sql . $cond, [], 'statdb');

        foreach ($months as $month) {
            // 开通医生数(总计)：截止本月为止 ，进过患者的医生总数
            if(strtotime($month. ' +1 month ')<time()){
                $sql = "select count(*) from
                    ( select *
                        from rpt_doctor_months
                        where left(themonth, 7) <= :month
                        and month_offsetcnt = 1
                        group by doctorid ) t";

                $bind = [];
                $bind[':month'] = $month;

                $doctorcnt = Dao::queryValue($sql, $bind, 'statdb');
            }else {
                $doctorcnt = 0;
            }

            $doctorcnt_hascome = $this->dealwithArrByMonth($doctorcnt_hascomeArr, $month);
            $doctorcnt_active = $this->dealwithArrByMonth($doctorcnt_activeArr, $month);
            $doctorcnt_month = $this->dealwithArrByMonth($doctorcnt_monthArr, $month);

            $arr["doctors_hascome"][] = $doctorcnt_hascome;
            $arr["doctors_active"][] = $doctorcnt_active;
            $arr["doctors_month"][] = $doctorcnt_month;
            $arr["doctors_all"][] = $doctorcnt;
            $arr["doctors_hascome_rate"][] = $doctorcnt > 0 ? number_format($doctorcnt_hascome * 100 / $doctorcnt, 2, '.', '') : 0;
            $arr["doctors_active_rate"][] = $doctorcnt > 0 ? number_format($doctorcnt_active * 100 / $doctorcnt, 2, '.', '') : 0;
        }
        return $arr;
    }

    private function getPatientsData ($months) {
        $arr = [];
        $sql = " SELECT left(baodaodate, 7) as themonth, count(*) as cnt
            FROM rpt_patient_months
            WHERE isscan = 1 AND month_offsetcnt = 1 AND patient_status_last > 0 ";

        $cond = " GROUP BY left(themonth, 7) ";
        $patientcntArr = Dao::queryRows($sql . $cond, [], 'statdb');

        $cond = " and drugitem_cnt > 0 GROUP BY left(themonth, 7) ";
        $patientcnt_drugArr = Dao::queryRows($sql . $cond, [], 'statdb');

        foreach ($months as $month) {
            $patientcnt = $this->dealwithArrByMonth($patientcntArr, $month);
            $patientcnt_drug = $this->dealwithArrByMonth($patientcnt_drugArr, $month);

            $arr["patientcnt"][] = $patientcnt;
            $arr["patientcnt_drug"][] = $patientcnt_drug;
            $arr["patientcnt_rate"][] = $patientcnt > 0 ? number_format($patientcnt_drug * 100 / $patientcnt, 2, '.', '') : 0;
        }
        return $arr;
    }

    private function getDoctorCntData ($months, $pagenum = 1, $pagesize = 50) {
        $startenum = ($pagenum - 1) * $pagesize;
        $sql = " SELECT t.doctorid as doctorid,
            max(t.column_0) as column_0,
            max(t.column_1) as column_1,
            max(t.column_2) as column_2,
            max(t.column_3) as column_3,
            max(t.column_4) as column_4,
            max(t.column_5) as column_5,
            max(t.column_6) as column_6,
            max(t.column_7) as column_7,
            max(t.column_8) as column_8,
            max(t.column_9) as column_9,
            max(t.column_10) as column_10,
            max(t.column_11) as column_11
            FROM (
            SELECT
                doctorid,
                if(left(themonth, 7)='{$months[0]}', patient_cnt_baodao_scan, 0) as column_0,
                if(left(themonth, 7)='{$months[1]}', patient_cnt_baodao_scan, 0) as column_1,
                if(left(themonth, 7)='{$months[2]}', patient_cnt_baodao_scan, 0) as column_2,
                if(left(themonth, 7)='{$months[3]}', patient_cnt_baodao_scan, 0) as column_3,
                if(left(themonth, 7)='{$months[4]}', patient_cnt_baodao_scan, 0) as column_4,
                if(left(themonth, 7)='{$months[5]}', patient_cnt_baodao_scan, 0) as column_5,
                if(left(themonth, 7)='{$months[6]}', patient_cnt_baodao_scan, 0) as column_6,
                if(left(themonth, 7)='{$months[7]}', patient_cnt_baodao_scan, 0) as column_7,
                if(left(themonth, 7)='{$months[8]}', patient_cnt_baodao_scan, 0) as column_8,
                if(left(themonth, 7)='{$months[9]}', patient_cnt_baodao_scan, 0) as column_9,
                if(left(themonth, 7)='{$months[10]}', patient_cnt_baodao_scan, 0) as column_10,
                if(left(themonth, 7)='{$months[11]}', patient_cnt_baodao_scan, 0) as column_11
            FROM rpt_doctor_months) t GROUP BY t.doctorid";

        return Dao::queryRows($sql, [], 'statdb');
    }

    private function dealwithArrByMonth ($arr1, $month) {
        foreach ($arr1 as $k => $v) {
            if ($v["themonth"] == $month) {
                $i = $v["cnt"];
                break;
            } else {
                $i = 0;
            }
        }

        return $i;
    }
}
