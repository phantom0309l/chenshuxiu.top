<?php
/**
 * Created by PhpStorm.
 * User: nigestream
 * Date: 2018/5/14
 * Time: 17:42
 */


class Doctor_Week_Rpt_Msg extends Doctor_Week_Rpt_Base
{
    protected static $objtypes = ['WxPicMsg', 'WxTxtMsg', 'WxVoiceMsg'];

    public function __construct() {
        parent::__construct();
    }

    public function stData($doctorId, $diseaseId) {
        return $this->stMsg($doctorId, $diseaseId);
    }

    private function stMsg($doctorId, $diseaseId) {
        $ret = [];
        $objtypeStr = '"' . implode('","', self::$objtypes) . '"';
        $sql = "SELECT COUNT(DISTINCT a.patientid) AS cnt FROM pipes a
                INNER JOIN patients b ON a.patientid=b.id
                WHERE a.objtype IN ($objtypeStr)
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
        $ret['msg_user'] = $this->formatCnt($cnt);

        $sql = "SELECT COUNT(a.id) AS cnt FROM pipes a
                INNER JOIN patients b ON a.patientid=b.id
                WHERE a.objtype IN ($objtypeStr)
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
        $ret['msg'] = $this->formatCnt($cnt);
        return $ret;
    }
}
