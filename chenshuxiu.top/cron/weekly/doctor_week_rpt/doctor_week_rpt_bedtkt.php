<?php
/**
 * Created by PhpStorm.
 * User: nigestream
 * Date: 2018/5/14
 * Time: 17:42
 */


class Doctor_Week_Rpt_Bedtkt extends Doctor_Week_Rpt_Base
{
    const DRAFT = 0;
    const WAIT_AUDITOR = 1;
    const PATIENT_CLOSE = 2;
    const WAIT_DOCTOR = 3;
    const AUDITOR_CLOSED = 4;
    const CONFIRMED = 5;
    const DOCTOR_CLOSED = 6;

    protected static $statusDesc = [
        self::DRAFT => '草稿',
        self::WAIT_AUDITOR => '待运营审核',
        self::PATIENT_CLOSE => '患者关闭',
        self::WAIT_DOCTOR => '待医生审核',
        self::AUDITOR_CLOSED => '运营关闭',
        self::CONFIRMED => '已确认入住',
        self::DOCTOR_CLOSED => '医生关闭',
    ];

    public function __construct() {
        parent::__construct();
    }

    public function stData($doctorId, $diseaseId) {
        return $this->stBedTkt($doctorId, $diseaseId);
    }

    private function stBedTkt($doctorId, $diseaseId) {
        $ret = [];
        $sql = "SELECT a.status, COUNT(*) AS cnt FROM bedtkts a 
                INNER JOIN patients b ON a.patientid=b.id
                WHERE 1
                 AND a.createtime>=:start_date AND a.createtime<:end_date
                AND a.doctorid=:doctorid AND b.diseaseid=:diseaseid
                AND b.is_test=0
                GROUP BY a.status
               ";
        $bind = [
            ':start_date' => $this->last_monday,
            ':end_date' => $this->this_monday,
            ':doctorid' => $doctorId,
            ':diseaseid' => $diseaseId,
        ];
        $rows = Dao::queryRows($sql, $bind);
        $tmp = [];
        $total = 0;
        foreach ($rows as $row) {
            if (!isset(self::$statusDesc[$row['status']])) {
                $tmp['未知'] = $row['cnt'];
            } else {
                $tmp[self::$statusDesc[$row['status']]] = $row['cnt'];
            }
            $total += $row['cnt'];
        }
        $tmp['全部'] = $total;
        $ret['bedtkt'] = $tmp;
        return $ret;
    }
}
