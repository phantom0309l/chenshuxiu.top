<?php
class DoctordbOplogService {
    //记录操作日志
    public static function log($user, $doctor, $objtype, $objid, $content, $patientid='') {
        if (false === $doctor instanceof Doctor) {
            Debug::warn(__METHOD__ . ' doctor is null');
            return false;
        }
        $username = $user->name;
        if ($doctor->userid != $user->id) {
            $assistant = AssistantDao::getByUserid($user->id);
            $username = $assistant->name;
        }
        $row = array();
        $row['doctorid'] = $doctor->id;
        $row['doctorname'] = $doctor->name;
        $row['userid'] = $user->id;//真正修改人的id
        $row['username'] = $username;//真正修改人的名称
        $row['patientid'] = $patientid;
        $row['content'] = $content;
        $row['objtype'] = $objtype;
        $row['objid'] = $objid;

        $doctordbOplog = DoctordbOplog::createByBiz($row);
        return $doctordbOplog;
    }
}
