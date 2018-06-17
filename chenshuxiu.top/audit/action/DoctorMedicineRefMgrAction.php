<?php
// DoctorMedicineRefMgrAction
class DoctorMedicineRefMgrAction extends AuditBaseAction
{

    public function doOneMedicineList () {
        $pagesize = XRequest::getValue("pagesize", 105);
        $pagenum = XRequest::getValue("pagenum", 1);

        $medicineid = XRequest::getValue('medicineid', 0);
        $medicine = Medicine::getById($medicineid);
        XContext::setValue("medicine", $medicine);

        $url = "/doctormedicinerefmgr/list?1=1";
        $sql = "select dmr.*
                from doctormedicinerefs dmr
                inner join medicines m on m.id = dmr.medicineid
                inner join doctordiseaserefs ddr on dmr.doctorid = ddr.doctorid
                where 1=1 ";
        $sqlcond = "";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $sqlcond .= " and ddr.diseaseid in ($diseaseidstr) ";

        if ($medicineid) {
            $url .= "&medicineid={$medicineid}";

            $sqlcond .= " AND dmr.medicineid = :medicineid ";
            $bind[':medicineid'] = $medicineid;
        }

        $sql .= $sqlcond;
        $sql .= " order by m.groupstr, dmr.id ";

        $doctormedicinerefs = Dao::loadEntityList4Page('DoctorMedicineRef', $sql, $pagesize, $pagenum, $bind);

        $countSql = "select count(*) as cnt
                from doctormedicinerefs dmr
                inner join medicines m on m.id = dmr.medicineid
                inner join doctordiseaserefs ddr on dmr.doctorid = ddr.doctorid
                where 1=1 " . $sqlcond;
        $cnt = Dao::queryValue($countSql, $bind);
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue('doctormedicinerefs', $doctormedicinerefs);

        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 105);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $word = XRequest::getValue('word', '');

        $url = "/doctormedicinerefmgr/list?1=1";
        $sql = "select dmr.*
                from doctormedicinerefs dmr
                inner join medicines m on m.id = dmr.medicineid
                inner join doctordiseaserefs ddr on dmr.doctorid = ddr.doctorid
                where 1=1 ";
        $sqlcond = "";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $sqlcond .= " and ddr.diseaseid in ($diseaseidstr) ";

        if ($doctorid) {
            $url .= "&doctorid={$doctorid}";

            $sqlcond .= " AND dmr.doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($word) {
            $url .= "&word={$word}";
            $sqlcond .= " AND ( dmr.title like :word OR m.name like :word ) ";
            $bind[':word'] = "%{$word}%";
        }

        $sql .= $sqlcond;
        $sql .= " order by m.groupstr, dmr.id ";

        $doctormedicinerefs = Dao::loadEntityList4Page('DoctorMedicineRef', $sql, $pagesize, $pagenum, $bind);

        // 排序
        usort($doctormedicinerefs, function  ($a, $b) {
            return $a->pos - $b->pos > 0 ? 1 : - 1;
        });

        $countSql = "select count(*) as cnt
                from doctormedicinerefs dmr
                inner join medicines m on m.id = dmr.medicineid
                inner join doctordiseaserefs ddr on dmr.doctorid = ddr.doctorid
                where 1=1 " . $sqlcond;
        $cnt = Dao::queryValue($countSql, $bind);
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue('word', $word);
        XContext::setValue('doctormedicinerefs', $doctormedicinerefs);

        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    public function doDoctorList () {
        $word = XRequest::getValue('word', '');

        $sql = "select d.*
                from doctors d
                inner join doctormedicinerefs dmr on d.id = dmr.doctorid
                inner join doctordiseaserefs ddr on d.id = ddr.doctorid
                where 1=1 ";
        $sqlcond = "";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $sqlcond .= " and ddr.diseaseid in ($diseaseidstr) ";

        if ($word) {
            $sqlcond .= " AND dmr.title like :word ";
            $bind[':word'] = "%{$word}%";
        }

        $sql .= $sqlcond;
        $sql .= " group by d.id order by d.id ";

        $doctors = Dao::loadEntityList('Doctor', $sql, $bind);

        XContext::setValue('word', $word);
        XContext::setValue('doctors', $doctors);

        return self::SUCCESS;
    }

    public function doAdd () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $medicineid = XRequest::getValue('medicineid', 0);
        $doctorname = XRequest::getValue('doctorname', '');

        $cond = " and name like :name ";
        $bind = array(':name'=>"%{$doctorname}%");

        $doctors = Dao::getEntityListByCond( 'Doctor', $cond, $bind );
        XContext::setValue('doctors', $doctors);

        $medicine = Medicine::getById($medicineid);

        if( $medicine && $this->mydisease ){
            $diseasemedicineref = DiseaseMedicineRefDao::getByDiseaseAndMedicine( $this->mydisease, $medicine );
            XContext::setValue('diseasemedicineref', $diseasemedicineref);
        }

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('medicine', $medicine);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $medicineid = XRequest::getValue('medicineid', 0);
        $title = XRequest::getValue('title', '');
        $drug_dose_arr = XRequest::getValue('drug_dose_arr', '');
        $drug_frequency_arr = XRequest::getValue('drug_frequency_arr', '');
        $drug_change_arr = XRequest::getValue('drug_change_arr', '');
        $herbjson = XRequest::getValue("herbjson", '');
        $doctorremark = XRequest::getValue('doctorremark', '');

        $ref = DoctorMedicineRefDao::getByDoctoridMedicineid($doctorid,$medicineid);
        if( $ref instanceof DoctorMedicineRef){
            XContext::setJumpPath("/doctormedicinerefmgr/list?doctorid={$doctorid}".urlencode("药物重复"));

            return self::SUCCESS;
        }

        $row = array();
        $row['doctorid'] = $doctorid;
        $row['title'] = $title;
        $row['medicineid'] = $medicineid;
        $row['drug_dose_arr'] = $drug_dose_arr;
        $row['drug_frequency_arr'] = $drug_frequency_arr;
        $row['drug_change_arr'] = $drug_change_arr;
        $row["herbjson"] = $herbjson;
        $row['doctorremark'] = $doctorremark;

        $doctormedicineref = DoctorMedicineRef::createByBiz($row);
        XContext::setJumpPath("/doctormedicinerefmgr/list?doctorid={$doctorid}");

        return self::SUCCESS;
    }

    public function doModify () {
        $medicines = Dao::getEntityListByCond('Medicine');

        $doctormedicinerefid = XRequest::getValue('doctormedicinerefid', 0);

        $doctormedicineref = DoctorMedicineRef::getById($doctormedicinerefid);

        XContext::setValue('medicines', $medicines);
        XContext::setValue('doctormedicineref', $doctormedicineref);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $doctormedicinerefid = XRequest::getValue('doctormedicinerefid', 0);

        $title = XRequest::getValue('title', '');
        $drug_std_dosage_arr = XRequest::getValue("drug_std_dosage_arr", '');
        $drug_timespan_arr = XRequest::getValue("drug_timespan_arr", '');
        $drug_dose_arr = XRequest::getValue('drug_dose_arr', '');
        $drug_frequency_arr = XRequest::getValue('drug_frequency_arr', '');
        $drug_change_arr = XRequest::getValue('drug_change_arr', '');
        $herbjson = XRequest::getValue("herbjson", '');
        $doctorremark = XRequest::getValue('doctorremark', '');

        $doctormedicineref = DoctorMedicineRef::getById($doctormedicinerefid);
        $doctormedicineref->title = $title;
        $doctormedicineref->drug_std_dosage_arr = $drug_std_dosage_arr;
        $doctormedicineref->drug_timespan_arr = $drug_timespan_arr;
        $doctormedicineref->drug_dose_arr = $drug_dose_arr;
        $doctormedicineref->drug_frequency_arr = $drug_frequency_arr;
        $doctormedicineref->drug_change_arr = $drug_change_arr;
        $doctormedicineref->herbjson = $herbjson;
        $doctormedicineref->doctorremark = $doctorremark;

        XContext::setJumpPath("/doctormedicinerefmgr/modify?doctormedicinerefid={$doctormedicinerefid}&preMsg=" . urlencode('已修改'));

        return self::SUCCESS;
    }

    public function doPosModifyPost () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $doctormedicinerefid => $pos) {
            $doctormedicineref = DoctorMedicineRef::getById($doctormedicinerefid);
            $doctormedicineref->pos = $pos;
        }

        XContext::setJumpPath("/doctormedicinerefmgr/list?doctorid={$doctorid}");

        return self::SUCCESS;
    }

    public function doDeleteJson () {
        $doctormedicinerefid = XRequest::getValue('doctormedicinerefid', 0);

        $doctormedicineref = DoctorMedicineRef::getById($doctormedicinerefid);
        $doctormedicineref->remove();
        echo "success";

        return self::BLANK;
    }
}
