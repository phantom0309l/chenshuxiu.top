<?php
// CheckupTplMgrAction
class DoctorDbMgrAction extends AuditBaseAction
{

    public function doDefault () {
        return self::SUCCESS;
    }

    // 疾病数据库列表
    public function doIndex () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $cond = "";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond .= " and diseaseid in ($diseaseidstr) ";

        if ($doctorid > 0) {
            $cond .= " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        $doctorDiseaseRefs = Dao::getEntityListByCond('DoctorDiseaseRef', $cond, $bind);

        XContext::setValue('doctorDiseaseRefs', $doctorDiseaseRefs);

        return self::SUCCESS;
    }
}
