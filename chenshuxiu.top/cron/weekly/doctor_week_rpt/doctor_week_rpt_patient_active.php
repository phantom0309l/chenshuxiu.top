<?php
/**
 * Created by PhpStorm.
 * User: nigestream
 * Date: 2018/5/16
 * Time: 11:48
 */

class Doctor_Week_Rpt_Patient_Active extends Doctor_Week_Rpt_Base
{
    public function __construct() {
        parent::__construct();
    }

    public function stData($doctorId, $diseaseId) {
        return $this->stActive($doctorId, $diseaseId);
    }

    private function stActive($doctorId, $diseaseId) {
        //$diseaseId 不使用
        $sql = "SELECT id FROM patients WHERE doctorid = {$doctorId} ";
        $patientIds = Dao::queryValues($sql);
        $cnt = 0;
        foreach ($patientIds as $patientId) {
            if ($this->isActive($doctorId, $patientId)) {
                $cnt ++;
            }
        }
        $ret = [];
        $ret['active'] = $this->formatCnt($cnt);
        return $ret;
    }

    /*
     * ***************
     * ***************
     */

    private function isActive($doctorId, $patientId) {
        // 出现频率最高的是次序：$isWxTxtMsg，$isCdrMeeting，$isWxPicMsg，$isPaper，$isBedTkt，$isRevisitTkt，$isWxVoiceMsg，$isPatientNotes
        if ($this->isWxTxtMsg($doctorId, $patientId) ||
            $this->isCdrMeeting($patientId) ||
            $this->isWxPicMsg($doctorId, $patientId) ||
            $this->isPaper($doctorId, $patientId) ||
            $this->isBedTkt($doctorId, $patientId) ||
            $this->isRevisitTkt($doctorId, $patientId) ||
            $this->isWxVoiceMsg($doctorId, $patientId) ||
            $this->isPatientNotes($doctorId, $patientId) )
        {
            return true;
        }
        return false;
    }

    private function getSql($table) {
        if (empty($table)) {
            return "";
        }
        $sql = "SELECT id
                FROM {$table}
                WHERE doctorid=:doctorid AND patientid=:patientid
                AND createtime>=:start_date AND createtime<:end_date ";
        return $sql;
    }

    private function getBind($doctorId, $patientId) {
        $bind = [
            ':doctorid' => $doctorId,
            ':patientid' => $patientId,
            ':start_date' => $this->last_monday,
            ':end_date' => $this->this_monday,
        ];
        return $bind;
    }

    private function isPaper($doctorId, $patientId) {
        $sql = $this->getSql('papers');
        $bind = $this->getBind($doctorId, $patientId);
        $cnt = Dao::queryValue($sql, $bind);

        return !!$cnt;
    }

    private function isWxTxtMsg($doctorId, $patientId) {
        $sql = $this->getSql('wxtxtmsgs');
        $bind = $this->getBind($doctorId, $patientId);
        $cnt = Dao::queryValue($sql, $bind);

        return !!$cnt;
    }

    private function isWxPicMsg($doctorId, $patientId) {
        $sql = $this->getSql('wxpicmsgs');
        $bind = $this->getBind($doctorId, $patientId);
        $cnt = Dao::queryValue($sql, $bind);

        return !!$cnt;
    }

    private function isWxVoiceMsg($doctorId, $patientId) {
        $sql = $this->getSql('wxvoicemsgs');
        $bind = $this->getBind($doctorId, $patientId);
        $cnt = Dao::queryValue($sql, $bind);

        return !!$cnt;
    }

    private function isCdrMeeting($patientId) {
        $sql = "SELECT id
                FROM cdrmeetings
                WHERE cdr_call_type in (1,2) 
                AND patientid=:patientid
                AND createtime>=:start_date AND createtime<:end_date  
                ";
        $bind = [
            ':patientid' => $patientId,
            ':start_date' => $this->last_monday,
            ':end_date' => $this->this_monday,
        ];
        $cnt = Dao::queryValue($sql, $bind);

        return !!$cnt;
    }

    private function isRevisitTkt($doctorId, $patientId) {
        $sql = $this->getSql('revisittkts');
        $bind = $this->getBind($doctorId, $patientId);

        return !!$cnt;
    }

    private function isBedTkt($doctorId, $patientId) {
        $sql = $this->getSql('bedtkts');
        $bind = $this->getBind($doctorId, $patientId);
        $cnt = Dao::queryValue($sql, $bind);

        return !!$cnt;
    }

    private function isPatientNotes($doctorId, $patientId) {
        $sql = $this->getSql('patientnotes');
        $bind = $this->getBind($doctorId, $patientId);
        $cnt = Dao::queryValue($sql, $bind);

        return !!$cnt;
    }
}
