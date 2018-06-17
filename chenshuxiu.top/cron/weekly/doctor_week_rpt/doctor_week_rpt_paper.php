<?php
/**
 * Created by PhpStorm.
 * User: nigestream
 * Date: 2018/5/14
 * Time: 17:42
 */


class Doctor_Week_Rpt_Paper extends Doctor_Week_Rpt_Base
{
    public function __construct() {
        parent::__construct();
    }

    public function stData($doctorId, $diseaseId) {
        return $this->stPaper($doctorId, $diseaseId);
    }

    private function stPaper($doctorId, $diseaseId) {
        $ret = [];
        $sql = "SELECT COUNT(DISTINCT a.patientid) AS cnt FROM papers a 
                INNER JOIN patients b ON a.patientid=b.id
                WHERE 1
                AND a.createtime>=:start_date AND a.createtime<:end_date
                AND a.doctorid=:doctorid AND b.diseaseid=:diseaseid
                AND b.is_test=0
               ";
        $bind = [
            ':start_date' => $this->last_monday,
            ':end_date' => $this->this_monday,
            ':doctorid' => $doctorId,
            ':diseaseid' => $diseaseId,
        ];
        $cnt = Dao::queryValue($sql, $bind);
        $ret['paper_user'] = $this->formatCnt($cnt);

        $sql = "SELECT COUNT(DISTINCT a.id) AS cnt FROM papers a 
                INNER JOIN patients b ON a.patientid=b.id
                WHERE 1
                AND a.createtime>=:start_date AND a.createtime<:end_date
                AND a.doctorid=:doctorid AND b.diseaseid=:diseaseid
                AND b.is_test=0
               ";
        $bind = [
            ':start_date' => $this->last_monday,
            ':end_date' => $this->this_monday,
            ':doctorid' => $doctorId,
            ':diseaseid' => $diseaseId,
        ];
        $cnt = Dao::queryValue($sql, $bind);
        $ret['paper'] = $this->formatCnt($cnt);
        return $ret;
    }
}
