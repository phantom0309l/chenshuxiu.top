<?php
// PatientMedicinePkgMgrAction
class PatientMedicinePkgMgrAction extends AuditBaseAction
{

    public function doList () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        $patientid = XRequest::getValue('patientid', 0);
        $patient_name = XRequest::getValue('patient_name', '');


        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);
        XContext::setValue('patientid', $patientid);
        XContext::setValue('patient_name', $patient_name);

        $sql = "SELECT distinct a.id,a.createtime,a.patientid, p.doctorid, IFNULL(t.cnt,0) AS cnt
                FROM patientmedicinepkgs a
                INNER JOIN pcards p ON p.patientid = a.patientid
                LEFT JOIN (
	               SELECT max(createtime) as lastcreatetime, patientmedicinepkgid , COUNT(*) AS cnt
	               FROM patientmedicinepkgitems
	               GROUP BY patientmedicinepkgid
                ) t ON t.patientmedicinepkgid = a.id
                where 1 = 1 ";
        $bind = [];

        if ($doctorid) {
            $sql .= " and p.doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($patient_name) {
            $sql .= " and p.patient_name like :patient_name ";
            $bind[':patient_name'] = "%{$patient_name}%";
        }

        $sql .= " order by t.lastcreatetime ";

        $patientmedicinepkgarr = Dao::queryRows($sql, $bind);

        XContext::setValue('patientmedicinepkgarr', $patientmedicinepkgarr);

        return self::SUCCESS;
    }

    public function doOne () {
        return self::SUCCESS;
    }
}
