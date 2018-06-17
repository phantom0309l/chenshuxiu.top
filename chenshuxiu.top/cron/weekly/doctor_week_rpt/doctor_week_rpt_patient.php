<?php
/**
 * Created by PhpStorm.
 * User: nigestream
 * Date: 2018/5/14
 * Time: 17:42
 */

class Doctor_Week_Rpt_Patient extends Doctor_Week_Rpt_Base
{
    const MALE = 1;
    const FEMALE = 2;
    const UNKNOWN = 0;
    protected static $sexDesc = [
        self::MALE => '男',
        self::FEMALE => '女',
        self::UNKNOWN => '未知',
    ];
    public function __construct() {
        parent::__construct();
    }

    public function stData($doctorId, $diseaseId) {
        $ret = [];
        $ret = array_merge($ret, $this->stPatient($doctorId, $diseaseId));
        $ret = array_merge($ret, $this->stWxUser($doctorId, $diseaseId));
        return $ret;
    }

    //统计患者
    private function stPatient($doctorId, $diseaseId) {
        if (empty($doctorId) || empty($diseaseId)) {
            return [];
        }
        $ret = [];
        $sql = "SELECT name FROM diseases WHERE id=:diseaseid";
        $diseaseName = Dao::queryValue($sql, [':diseaseid' => $diseaseId]);
        //////患者总数
        $sql = "SELECT sex, COUNT(*) AS cnt FROM patients
			WHERE diseaseid=:diseaseid AND doctorid=:doctorid AND status=1 AND is_test=0
			GROUP BY sex
			";
        $bind = [
            ':diseaseid' => $diseaseId,
            ':doctorid' => $doctorId,
        ];
        $rows = Dao::queryRows($sql, $bind);
        $total = 0;
        $patientTotal = [];
        foreach ($rows as $row) {
            $patientTotal[self::$sexDesc[$row['sex']]] = $row['cnt']; 
            $total += $row['cnt'];
        }
        $patientTotal['全部'] = $total;
        $ret['total'] = $patientTotal;
        //////新增患者数
        $newPatientSt = $this->stNewPatient($doctorId, $diseaseId, $diseaseName, $this->last_monday, $this->this_monday);
        //取上周数据
        $lastWeekPatientSt = $this->stNewPatient($doctorId, $diseaseId, $diseaseName, $this->last_last_monday, $this->last_monday);
        $ret['new'] = $newPatientSt;
        $ret['new_lastweek'] = $lastWeekPatientSt;
        return $ret;
    }

    private function stNewPatient($doctorId, $diseaseId, $diseaseName, $start_date, $end_date) {
        $sql = "SELECT COUNT(*) FROM patients 
                    WHERE status=1 AND is_test=0 
                    AND doctorid=:doctorid AND diseaseid=:diseaseid
                    AND createtime>=:start_date AND createtime<:end_date
                    ";
        $bind = [
            ':diseaseid' => $diseaseId,
            ':doctorid' => $doctorId,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
        ];
        $cnt = Dao::queryValue($sql, $bind);
        $newPatient = [];
        if ($cnt > 0) {
            $newPatient = [
                "全部" => $cnt,
                "$diseaseName" => $cnt,
            ];
        } else {
            $newPatient = [
                "全部" => $cnt,
            ];
        }
        return $newPatient;
    }

    //统计扫码
    private function stWxUser($doctorId, $diseaseId) {
        if (empty($doctorId) || empty($diseaseId)) {
            return [];
        }
        $ret = [];
        $newWxUserSt = $this->stNewWxUser($doctorId, $diseaseId, $diseaseName, $this->last_monday, $this->this_monday);
        //取上周数据
        $lastWeekWxUserSt = $this->stNewWxUser($doctorId, $diseaseId, $diseaseName, $this->last_last_monday, $this->last_monday);
        $ret['new_wxuser'] = $newWxUserSt;
        $ret['new_wxuser_lastweek'] = $lastWeekWxUserSt;
        return $ret;
    }

    private function stNewWxUser($doctorId, $diseaseId, $diseaseName, $start_date, $end_date) {

        $sql = "SELECT COUNT(DISTINCT c.id) AS cnt
            FROM wxusers a LEFT JOIN users AS b ON a.userid=b.id 
            LEFT JOIN patients c ON b.`patientid`=c.`id`
            WHERE a.`wx_ref_code`<>'' AND c.status=1 AND c.is_test=0
            AND c.doctorid=:doctorid AND c.diseaseid=:diseaseid
            AND a.createtime>=:start_date AND a.createtime<:end_date
            ";
        $bind = [
            ':diseaseid' => $diseaseId,
            ':doctorid' => $doctorId,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
        ];
        $cnt = Dao::queryValue($sql, $bind);
        return $this->formatCnt($cnt);
    }
}
