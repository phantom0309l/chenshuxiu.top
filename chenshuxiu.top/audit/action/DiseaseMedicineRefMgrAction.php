<?php

class DiseaseMedicineRefMgrAction extends AuditBaseAction
{

    // 首页
    public function doDefault () {
        return self::SUCCESS;
    }

    // 药品列表
    public function doList(){
        $medicine_name = XRequest::getValue('medicine_name','');
        $groupstr = XRequest::getValue( 'groupstr', '');

        $sql = " select a.*
                 from diseasemedicinerefs a
                 inner join medicines b on a.medicineid = b.id
                 where 1 = 1
                ";

        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $sql .= " and a.diseaseid in ($diseaseidstr) ";

        if($medicine_name != ''){
            $sql .= " and (b.name like :name or b.scientificname like :name) ";
            $bind[':name'] = "%{$medicine_name}%";
            $groupstr = '';
        }

        if ( $groupstr ) {
            $sql .= " and b.groupstr = :groupstr ";
            if( $groupstr === '(未分类)' ){
                $groupstr = '';
            }
            $bind[':groupstr'] = $groupstr;
        }

        $sql .= " order by a.id ";

        $diseasemedicinerefs = Dao::loadEntityList("DiseaseMedicineRef", $sql, $bind);

        if ($this->mydisease instanceof Disease) {
            $groupArr = DiseaseMedicineRef::getGroupstrArr($this->mydisease->id);
        } else {
            $groupArr = DiseaseMedicineRef::getGroupstrArr();
        }

        $grouplist = [];
        foreach ($groupArr as $key) {
            $grouplist["{$key}"] = $key;
        }

        XContext::setValue("groupstr",$groupstr);
        XContext::setValue("grouplist",$grouplist);
        XContext::setValue("medicine_name",$medicine_name);
        XContext::setValue("diseasemedicinerefs", $diseasemedicinerefs);

        return self::SUCCESS;
    }

    // 药品列表
    public function doGroupList(){
        if ($this->mydisease instanceof Disease) {
            $groupArr = DiseaseMedicineRef::getGroupstrArr($this->mydisease->id);
        } else {
            $groupArr = DiseaseMedicineRef::getGroupstrArr();
        }

        XContext::setValue("groupArr", $groupArr);
        return self::SUCCESS;
    }

    // 快速copy关系
    public function doCopyList () {
        $medicine_name = XRequest::getValue('medicine_name','');

        $sql = "select a.*
                from diseasemedicinerefs a
                inner join medicines b on a.medicineid = b.id
                where 1 = 1
                ";

        $bind = [];

        if($medicine_name != ''){
            $sql .= " and (b.name like :name or b.scientificname like :name) ";
            $bind[':name'] = "%{$medicine_name}%";
        }

        $sql .= " order by a.id ";

        $diseasemedicinerefs = Dao::loadEntityList("DiseaseMedicineRef", $sql, $bind);

        XContext::setValue("medicine_name",$medicine_name);
        XContext::setValue("diseasemedicinerefs", $diseasemedicinerefs);
        return self::SUCCESS;
    }

    // 快速copy提交
    public function doCopyPost () {
        DBC::requireNotEmpty($this->mydisease, "必须选疾病");

        $diseasemedicinerefid_source = XRequest::getValue('diseasemedicinerefid',0);

        $diseasemedicineref_source = DiseaseMedicineRef::getById($diseasemedicinerefid_source);
        if( $this->mydisease->id == $diseasemedicineref_source->diseaseid ){
            echo "已存在";
            return self::BLANK;
        }

        if($diseasemedicineref_source instanceof DiseaseMedicineRef){
            $row = array();
            $row["diseaseid"] = $this->mydisease->id;
            $row["medicineid"] = $diseasemedicineref_source->medicineid;
            $row["drug_dose_arr"] = $diseasemedicineref_source->drug_dose_arr;
            $row["drug_frequency_arr"] = $diseasemedicineref_source->drug_frequency_arr;
            $row["drug_change_arr"] = $diseasemedicineref_source->drug_change_arr;
            $row["doctorremark"] = $diseasemedicineref_source->doctorremark;

            $diseasemedicineref = DiseaseMedicineRef::createByBiz($row);

            XContext::setJumpPath("/diseasemedicinerefmgr/modify?diseasemedicinerefid={$diseasemedicineref->id}");
            return self::SUCCESS;
        }

        return self::BLANK;
    }

    // 新增疾病药品关系
    public function doAdd () {
        $medicineid = XRequest::getValue("medicineid", 0);
        $medicine = '';
        if( $medicineid ){
            $medicine = Medicine::getById($medicineid);
        }

        XContext::setValue('medicine', $medicine);
        return self::SUCCESS;
    }

    // 新增疾病药品关系提交
    public function doAddPost () {
        $medicineid = XRequest::getValue("medicineid", 0);
        $name = XRequest::getValue("name", '');
        $scientificname = XRequest::getValue("scientificname", '');
        $groupstr = XRequest::getValue("groupstr", '');
        $drug_dose_arr = XRequest::getValue("drug_dose_arr", '');
        $drug_frequency_arr = XRequest::getValue("drug_frequency_arr", '');
        $drug_change_arr = XRequest::getValue("drug_change_arr", '');
        $ischinese = XRequest::getValue("ischinese", 0);
        $herbjson = XRequest::getValue("herbjson", '');
        $doctorremark = XRequest::getValue("doctorremark", '');
        $content = XRequest::getValue("content", '');
        $pictureid = XRequest::getValue('pictureid', 0);

        DBC::requireNotEmpty($this->mydisease, "必须选疾病");

        $mydiseaseid = $this->mydisease->id;
        $medicine = Medicine::getById($medicineid);
        if( false == $medicine instanceof Medicine) {
            $medicine = MedicineDao::getByName($name);
            if( false == $medicine instanceof Medicine){
                $row = array();
                $row["name"] = $name;
                $row["scientificname"] = $scientificname;
                $row["groupstr"] = $groupstr;
                $row["ischinese"] = $ischinese;
                $row["content"] = $content;
                $row["pictureid"] = $pictureid;
                $medicine = Medicine::createByBiz($row);
            }
        }

        $diseasemedicineref = DiseaseMedicineRefDao::getByDiseaseAndMedicine($this->mydisease,$medicine);
        if( $diseasemedicineref instanceof DiseaseMedicineRef ){
            XContext::setJumpPath("/diseasemedicinerefmgr/add?preMsg=" . urlencode("{$name}在{$this->mydisease->name}已经有过了，不可以重复添加哦：）"));
            return self::SUCCESS;
        }else{
            $row1 = [];
            $row1["diseaseid"] = $mydiseaseid;
            $row1["medicineid"] = $medicine->id;
            $row1["drug_dose_arr"] = $drug_dose_arr;
            $row1["drug_frequency_arr"] = $drug_frequency_arr;
            $row1["drug_change_arr"] = $drug_change_arr;
            $row1["herbjson"] = $herbjson;
            $row1["doctorremark"] = $doctorremark;

            $diseasemedicineref = DiseaseMedicineRef::createByBiz($row1);
        }

        XContext::setJumpPath("/diseasemedicinerefmgr/list");

        return self::SUCCESS;
    }

    // 修改药品
    public function doModify () {
        $diseasemedicinerefid = XRequest::getValue('diseasemedicinerefid', 0);

        $diseasemedicineref = DiseaseMedicineRef::getById($diseasemedicinerefid);

        XContext::setValue('diseasemedicineref', $diseasemedicineref);

        return self::SUCCESS;
    }

    // 修改药品提交
    public function doModifyPost () {
        $diseasemedicinerefid = XRequest::getValue('diseasemedicinerefid', 0);
        $drug_std_dosage_arr = XRequest::getValue("drug_std_dosage_arr", '');
        $drug_timespan_arr = XRequest::getValue("drug_timespan_arr", '');
        $drug_dose_arr = XRequest::getValue("drug_dose_arr", '');
        $drug_frequency_arr = XRequest::getValue("drug_frequency_arr", '');
        $drug_change_arr = XRequest::getValue("drug_change_arr", '');
        $herbjson = XRequest::getValue("herbjson", '');
        $doctorremark = XRequest::getValue("doctorremark", '');
        $level = XRequest::getValue("level", 0);

        $diseasemedicineref = DiseaseMedicineRef::getById($diseasemedicinerefid);
        $diseasemedicineref->drug_std_dosage_arr = $drug_std_dosage_arr;
        $diseasemedicineref->drug_timespan_arr = $drug_timespan_arr;
        $diseasemedicineref->drug_dose_arr = $drug_dose_arr;
        $diseasemedicineref->drug_frequency_arr = $drug_frequency_arr;
        $diseasemedicineref->drug_change_arr = $drug_change_arr;
        $diseasemedicineref->herbjson = $herbjson;
        $diseasemedicineref->doctorremark = $doctorremark;
        $diseasemedicineref->level = $level;

        XContext::setJumpPath("/diseasemedicinerefmgr/modify?diseasemedicinerefid={$diseasemedicinerefid}&preMsg=" . urlencode('修改已保存'));

        return self::SUCCESS;
    }

    public function doDoctorMRefAdd(){
        $diseasemedicinerefid = XRequest::getValue('diseasemedicinerefid', 0);
        $diseasemedicineref = DiseaseMedicineRef::getById($diseasemedicinerefid);

        $diseaseid = $diseasemedicineref->diseaseid;
        $doctors = DoctorDao::getListByDiseaseid($diseaseid);
        $doctors_notselect = array();

        foreach( $doctors as $doctor ){
            $doctors_notselect[$doctor->id] = $doctor;
        }

        $doctormedicinerefs = DoctorMedicineRefDao::getListByMedicineid($diseasemedicineref->medicineid);

        foreach( $doctormedicinerefs as $doctormedicineref ){
            unset($doctors_notselect[$doctormedicineref->doctorid]);
        }

        XContext::setValue('diseasemedicineref', $diseasemedicineref);
        XContext::setValue('doctormedicinerefs', $doctormedicinerefs);
        XContext::setValue('doctors_notselect', $doctors_notselect);

        return self::SUCCESS;
    }

    public function doDoctorMRefAddPost(){
        $diseasemedicinerefid = XRequest::getValue('diseasemedicinerefid', 0);
        $doctorid = XRequest::getValue('doctorid', 0);

        $diseasemedicineref = DiseaseMedicineRef::getById($diseasemedicinerefid);
        $doctor = Doctor::getById($doctorid);

        $ref = DoctorMedicineRefDao::getByDoctoridMedicineid($doctorid,$diseasemedicineref->medicineid);
        if( $ref instanceof DoctorMedicineRef){
            XContext::setJumpPath("/diseasemedicinerefmgr/doctormrefadd?diseasemedicinerefid={$diseasemedicinerefid}".urlencode("药物重复"));

            return self::SUCCESS;
        }
        $row = array();
        $row['doctorid'] = $doctor->id;
        $row['title'] = $diseasemedicineref->medicine->name;
        $row['medicineid'] = $diseasemedicineref->medicineid;
        $row["drug_std_dosage_arr"] = $diseasemedicineref->drug_std_dosage_arr;
        $row["drug_timespan_arr"] = $diseasemedicineref->drug_timespan_arr;
        $row['drug_dose_arr'] = $diseasemedicineref->drug_dose_arr;
        $row['drug_frequency_arr'] = $diseasemedicineref->drug_frequency_arr;
        $row['drug_change_arr'] = $diseasemedicineref->drug_change_arr;
        $row["herbjson"] = $diseasemedicineref->herbjson;
        $row['doctorremark'] = $diseasemedicineref->doctorremark;

        $doctormedicineref = DoctorMedicineRef::createByBiz($row);

        XContext::setJumpPath("/diseasemedicinerefmgr/doctormrefadd?diseasemedicinerefid={$diseasemedicinerefid}");
        return self::SUCCESS;
    }
}
