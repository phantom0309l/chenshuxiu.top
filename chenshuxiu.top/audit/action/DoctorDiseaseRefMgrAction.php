<?php
// DoctorDiseaseRefMgrAction
class DoctorDiseaseRefMgrAction extends AuditBaseAction
{

    // doList
    public function doList() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $diseaseid = XRequest::getValue("diseaseid", 0);

        $doctor = Doctor::getById($doctorid);
        $disease = Disease::getById($diseaseid);

        $cond = '';
        $bind = [];

        if ($doctor instanceof Doctor) {
            $cond .= " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctor->id;
        }

        if ($disease instanceof Disease) {
            $cond .= " and diseaseid = :diseaseid ";
            $bind[':diseaseid'] = $disease->id;
        } else {
            $diseaseidsstr = $this->getContextDiseaseidStr();
            $cond .= " and diseaseid in ($diseaseidsstr) ";
        }

        $doctorDiseaseRefs = Dao::getEntityListByCond('DoctorDiseaseRef', $cond, $bind);

        XContext::setValue("doctor", $doctor);
        XContext::setValue("disease", $disease);
        XContext::setValue("doctorDiseaseRefs", $doctorDiseaseRefs);

        $diseases = DiseaseDao::getListAll();
        XContext::setValue("diseases", $diseases);

        return self::SUCCESS;
    }

    // 修改绑定疾病提交
    public function doBindDiseasePost() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $diseaseids = XRequest::getValue("diseaseids", array());

        $doctor = Doctor::getById($doctorid);
        XContext::setValue("doctor", $doctor);

        foreach ($doctor->getDoctorDiseaseRefs() as $ref) {
            if (false == in_array($ref->diseaseid, $diseaseids)) {
                $ref->remove();
            }
        }

        foreach ($diseaseids as $diseaseid) {
            if (false == $doctor->isBindDisease($diseaseid)) {
                $row = array();
                $row["doctorid"] = $doctor->id;
                $row["diseaseid"] = $diseaseid;
                DoctorDiseaseRef::createByBiz($row);
            }
        }

        XContext::setJumpPath("/doctorDiseaseRefMgr/list?doctorid={$doctorid}");

        return self::SUCCESS;
    }

    // 通过疾病获取医生
    public function doGetDoctoridsByDiseaseJson () {
        $diseaseid = XRequest::getValue('diseaseid', 0);

        $list = [];
        $list['0'] = ["全部"];
        if ($diseaseid) {
            $doctors = DoctorDao::getListByDiseaseid($diseaseid);

            foreach ($doctors as $doctor) {
                $list["{$doctor->id}"] = $doctor->name;
            }
        }

        $this->result['data'] = $list;

        return self::TEXTJSON;
    }
}
