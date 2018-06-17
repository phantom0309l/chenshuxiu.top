<?php

// PatientTodayMarkMgrAction
class PatientTodayMarkMgrAction extends AuditBaseAction
{
    /**
     * 添加重点患者备注
     */
    public function doAddPostJson() {
        $patientid = XRequest::getValue("patientid", 0);
        $marktplids = XRequest::getValue("marktplids", []);
    
        $patient = Patient::getById($patientid);
        if (false == $patient instanceof Patient) {
            $this->returnError("患者信息异常");
        }
        
        $thedate = date('Y-m-d');
        $todaymarks = PatientTodayMarkDao::getListByPatientIdThedate($patientid, $thedate);
        
        if (!empty($todaymarks)) {
            foreach ($todaymarks as $todaymark) {
                $todaymark->remove();
            }
        }
        
        $marksStr = '';
        foreach ($marktplids as $marktplid) {
            $marktpl = PatientTodayMarkTpl::getById($marktplid);
            
            if (false == $marktpl instanceof PatientTodayMarkTpl) {
                continue;
            }
            
            $row = array();
            $row["patientid"] = $patientid;
            $row["patienttodaymarktplid"] = $marktpl->id;
            $row["thedate"] = $thedate;
            $row["title"] = $marktpl->title;
            
            PatientTodayMark::createByBiz($row);
            
            $marksStr .= $marktpl->title." ";
        }
        $this->result['data'] = [
            'marksStr' => $marksStr,
        ];
        
        return self::TEXTJSON;
    }
    
    /**
     * 获取重点患者备注
     */
    public function doGetTodayMarkJson() {
        $patientid = XRequest::getValue("patientid", 0);
    
        $patient = Patient::getById($patientid);
        if (false == $patient instanceof Patient) {
            $this->returnError("患者信息异常");
        }
        
        $todaymarks = PatientTodayMarkDao::getListByPatientIdThedate($patientid, date('Y-m-d'));
        $arr = [];
        foreach ($todaymarks as $todaymark) {
            $arr[] = $todaymark->patienttodaymarktplid;
        }
        $this->result['data'] = [
            'selected_tplids' => $arr,
        ];
        
        return self::TEXTJSON;
    }
    
    /**
     * 验证患者信息
     */
    private function checkPatient($patientid) {
        $patient = Patient::getById($patientid);
        
        return $patient instanceof Patient;
    }
}
