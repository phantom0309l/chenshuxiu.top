<?php

class MedicineMgrAction extends AuditBaseAction
{

    // 药品列表
    public function doList () {
        $medicine_name = XRequest::getValue('medicine_name', '');
        $groupstr = XRequest::getValue('groupstr', '');
        $isshow = XRequest::getValue('isshow', 0);

        $sql = "select a.*
                from medicines a
                where 1 = 1 ";

        $bind = [];

        if ($medicine_name != '') {
            $sql .= " and (a.name like :name or a.scientificname like :name) ";
            $bind[':name'] = "%{$medicine_name}%";
            $groupstr = '';
            $isshow = 0;
        }

        if ($groupstr) {
            $sql .= " and a.groupstr = :groupstr ";
            if ($groupstr === '(未分类)') {
                $groupstr = '';
            }
            $bind[':groupstr'] = $groupstr;
        }

        if ($isshow == 1) {
            $sql .= " and (a.isshow = :isshow) ";
            $bind[':isshow'] = 1;
        }

        $sql .= " order by a.id ";

        $medicines = Dao::loadEntityList("Medicine", $sql, $bind);

        XContext::setValue("medicine_name", $medicine_name);
        XContext::setValue("groupstr", $groupstr);
        XContext::setValue("isshow", $isshow);
        XContext::setValue("medicines", $medicines);

        return self::SUCCESS;
    }

    // 药品分组列表
    public function doGroupList () {
        $sql = "select groupstr
                from medicines
                group by groupstr";
        $groupArr = Dao::queryValues($sql, []);

        XContext::setValue("groupArr", $groupArr);
        return self::SUCCESS;
    }

    public function doListOfSearchHtml () {
        $medicine_name = XRequest::getValue('medicine_name', '');
        $cond = " and (name like :name or scientificname like :name) ";
        $bind = [];
        $bind[':name'] = "%{$medicine_name}%";
        $medicines = Dao::getEntityListByCond("Medicine", $cond, $bind);
        XContext::setValue("medicines", $medicines);
        return self::SUCCESS;
    }

    public function doListOfSearchJson () {
        $medicine_name = XRequest::getValue('q', '');
        $cond = " and (name like :name or scientificname like :name) LIMIT 20";
        $bind = [];
        $bind[':name'] = "%{$medicine_name}%";
        $medicines = Dao::getEntityListByCond("Medicine", $cond, $bind);
        $data = [];
        foreach ($medicines as $medicine) {
            $tmp = [];
            $tmp['id'] = $medicine->id;
            $tmp['name'] = $medicine->name;
            $tmp['scientificname'] = $medicine->scientificname;
            $tmp['unit'] = $medicine->unit;
            $data[] = $tmp;
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 新增药品
    public function doAdd () {
        return self::SUCCESS;
    }

    // 新增药品提交
    public function doAddPost () {
        $name = XRequest::getValue("name", '');
        $scientificname = XRequest::getValue("scientificname", '');
        $groupstr = XRequest::getValue("groupstr", '');
        $unit = XRequest::getValue("unit", '');
        $content = XRequest::getValue("content", '');
        $isshow = XRequest::getValue('isshow', 0);
        $drug_way_arr = XRequest::getValue("drug_way_arr", '');
        $drug_std_dosage_arr = XRequest::getValue("drug_std_dosage_arr", '');
        $drug_timespan_arr = XRequest::getValue("drug_timespan_arr", '');
        $drug_dose_arr = XRequest::getValue("drug_dose_arr", '');
        $drug_frequency_arr = XRequest::getValue("drug_frequency_arr", '');
        $drug_change_arr = XRequest::getValue("drug_change_arr", '');
        $ischinese = XRequest::getValue("ischinese", 0);
        $herbjson = XRequest::getValue("herbjson", '');
        $doctorremark = XRequest::getValue("doctorremark", '');
        $pictureid = XRequest::getValue('pictureid', 0);

        $row = array();
        $row["name"] = $name;
        $row["scientificname"] = $scientificname;
        $row["groupstr"] = $groupstr;
        $row["unit"] = $unit;
        $row["content"] = $content;
        $row["isshow"] = $isshow;
        $row["drug_way_arr"] = $drug_way_arr;
        $row["drug_std_dosage_arr"] = $drug_std_dosage_arr;
        $row["drug_timespan_arr"] = $drug_timespan_arr;
        $row["drug_dose_arr"] = $drug_dose_arr;
        $row["drug_frequency_arr"] = $drug_frequency_arr;
        $row["drug_change_arr"] = $drug_change_arr;
        $row["ischinese"] = $ischinese;
        $row["herbjson"] = $herbjson;
        $row["doctorremark"] = $doctorremark;
        $row["pictureid"] = $pictureid;

        $medicine = Medicine::createByBiz($row);

        XContext::setJumpPath("/medicinemgr/dmrefadd?medicineid={$medicine->id}");

        return self::SUCCESS;
    }

    // 修改药品
    public function doModify () {
        $medicineid = XRequest::getValue('medicineid', 0);

        $medicine = Medicine::getById($medicineid);

        XContext::setValue('medicine', $medicine);

        return self::SUCCESS;
    }

    // 修改药品提交
    public function doModifyPost () {
        $medicineid = XRequest::getValue('medicineid', 0);
        $name = XRequest::getValue("name", '');
        $scientificname = XRequest::getValue("scientificname", '');
        $groupstr = XRequest::getValue("groupstr", '');
        $unit = XRequest::getValue("unit", '');
        $content = XRequest::getValue("content", '');
        $isshow = XRequest::getValue('isshow', 0);
        $drug_way_arr = XRequest::getValue("drug_way_arr", '');
        $drug_std_dosage_arr = XRequest::getValue("drug_std_dosage_arr", '');
        $drug_timespan_arr = XRequest::getValue("drug_timespan_arr", '');
        $drug_dose_arr = XRequest::getValue("drug_dose_arr", '');
        $drug_frequency_arr = XRequest::getValue("drug_frequency_arr", '');
        $drug_change_arr = XRequest::getValue("drug_change_arr", '');
        $ischinese = XRequest::getValue("ischinese", 0);
        $herbjson = XRequest::getValue("herbjson", '');
        $doctorremark = XRequest::getValue("doctorremark", '');
        $pictureid = XRequest::getValue('pictureid', 0);

        $medicine = Medicine::getById($medicineid);
        $medicine->name = $name;
        $medicine->scientificname = $scientificname;
        $medicine->groupstr = $groupstr;
        $medicine->unit = $unit;
        $medicine->content = $content;
        $medicine->isshow = $isshow;
        $medicine->drug_way_arr = $drug_way_arr;
        $medicine->drug_std_dosage_arr = $drug_std_dosage_arr;
        $medicine->drug_timespan_arr = $drug_timespan_arr;
        $medicine->drug_dose_arr = $drug_dose_arr;
        $medicine->drug_frequency_arr = $drug_frequency_arr;
        $medicine->drug_change_arr = $drug_change_arr;
        $medicine->ischinese = $ischinese;
        $medicine->herbjson = $herbjson;
        $medicine->doctorremark = $doctorremark;
        $medicine->pictureid = $pictureid;

        XContext::setJumpPath("/medicinemgr/modify?medicineid={$medicineid}&preMsg=" . urlencode('修改已保存'));

        return self::SUCCESS;
    }

    // 疾病药品关系新建
    public function doDMRefAdd () {
        $medicineid = XRequest::getValue('medicineid', 0);
        $medicine = Medicine::getById($medicineid);

        $diseases = DiseaseDao::getListAll();
        $diseases_notselect = array();
        foreach ($diseases as $disease) {
            $diseases_notselect[$disease->id] = $disease;
        }
        $diseasemedicinerefs = DiseaseMedicineRefDao::getListByMedicine($medicine);

        foreach ($diseasemedicinerefs as $diseasemedicineref) {
            unset($diseases_notselect[$diseasemedicineref->diseaseid]);
        }

        XContext::setValue('medicine', $medicine);
        XContext::setValue('diseasemedicinerefs', $diseasemedicinerefs);
        XContext::setValue('diseases_notselect', $diseases_notselect);
        return self::SUCCESS;
    }

    public function doDMRefAddPost () {
        $medicineid = XRequest::getValue('medicineid', 0);
        $diseaseid = XRequest::getValue('diseaseid', 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");

        $medicine = Medicine::getById($medicineid);

        $row = array();
        $row["diseaseid"] = $diseaseid;
        $row["medicineid"] = $medicineid;
        $row["drug_std_dosage_arr"] = $medicine->drug_std_dosage_arr;
        $row["drug_timespan_arr"] = $medicine->drug_timespan_arr;
        $row["drug_dose_arr"] = $medicine->drug_dose_arr;
        $row["drug_frequency_arr"] = $medicine->drug_frequency_arr;
        $row["drug_change_arr"] = $medicine->drug_change_arr;
        $row["herbjson"] = $medicine->herbjson;
        $row["doctorremark"] = $medicine->doctorremark;

        $diseasemedicineref = DiseaseMedicineRef::createByBiz($row);

        XContext::setJumpPath("/medicinemgr/dmrefadd?medicineid={$medicineid}");
        return self::SUCCESS;
    }

    // 重复药品列表
    public function doMergeAllList () {
        $sql = "
            select name,count(*) as cnt,group_concat(id) as ids
            from
            (
                select id,name
                from medicines
                where name<>''
                union
                select id,scientificname as name
                from medicines
                where scientificname<>''
            ) tt
            group by name
            having cnt > 1";

        $repetitionmedicines = Dao::queryRows($sql, []);
        $count = count($repetitionmedicines);

        XContext::setValue('repetitionmedicines', $repetitionmedicines);
        XContext::setValue('count', $count);

        return self::SUCCESS;
    }

    // 单个重复药品列表
    // sql注入漏洞 TODO by sjp 20170503
    public function doMergeOneList () {
        $ids = XRequest::getValue('ids', '');

        $idarr = array();
        $idarr = explode(',', $ids);

        $medicines = array();
        foreach ($idarr as $id) {
            if ($id) {
                $medicine = Medicine::getById($id);
                if ($medicine instanceof Medicine) {
                    $medicines[] = Medicine::getById($id);
                }
            }
        }

        // --------------------------------------medicine相关的记录-----------------------------------------
        $cnts = array();
        if ($ids) {
            $sql = "show tables";

            $tables = Dao::queryValues($sql, []);

            $table_medicineids = array();
            foreach ($tables as $table) {
                $sql = "show full fields from {$table}";

                $fields = Dao::queryRows($sql, []);

                foreach ($fields as $field) {
                    if ($field['field'] == 'medicineid') {
                        $table_medicineids[] = $table;
                        break;
                    }
                }
            }

            foreach ($table_medicineids as $table) {
                $sql = "
                    select medicineid,count(*) as cnt
                    from {$table}
                    where medicineid in ({$ids})
                    group by medicineid
                    ";
                $medicinecnts = Dao::queryRows($sql);

                foreach ($medicinecnts as $a) {
                    $cnts["{$a['medicineid']}"] += $a['cnt'];
                }
            }
        }

        // --------------------------------------medicine相关的记录-----------------------------------------

        XContext::setValue('ids', $ids);
        XContext::setValue('medicines', $medicines);
        XContext::setValue('cnts', $cnts);

        return self::SUCCESS;
    }

    public function doMergePost () {
        $keepmedicineid = XRequest::getValue('keepmedicineid', 0);
        $deletemedicineid = XRequest::getValue('deletemedicineid', 0);

        if ($keepmedicineid >= 403 && $keepmedicineid <= 426) {
            echo "medicineid在403～426（包含）之间的药品不能合并";

            return self::BLANK;
        }

        if ($deletemedicineid >= 403 && $deletemedicineid <= 426) {
            echo "medicineid在403～426（包含）之间的药品不能合并";

            return self::BLANK;
        }

        $deletemedicine = Medicine::getById($deletemedicineid);
        if ($deletemedicine instanceof Medicine) {
            $deletemedicine->remove();
        }

        // --------------------------------------medicine相关的记录的合并-----------------------------------------
        $sql = "show tables";

        $tables = Dao::queryValues($sql, []);

        $table_medicineids = array();
        foreach ($tables as $table) {
            $sql = "show full fields from {$table}";

            $fields = Dao::queryRows($sql, []);

            foreach ($fields as $field) {
                if ($field['field'] == 'medicineid') {
                    $table_medicineids[] = $table;
                    break;
                }
            }
        }

        foreach ($table_medicineids as $table) {
            $sql = "update {$table}
                    set medicineid = {$keepmedicineid}
                    where medicineid = {$deletemedicineid} ";
            $modifycnts = Dao::executeNoQuery($sql);
        }
        // --------------------------------------medicine相关的记录的合并-----------------------------------------

        echo "success";

        return self::BLANK;
    }
}
