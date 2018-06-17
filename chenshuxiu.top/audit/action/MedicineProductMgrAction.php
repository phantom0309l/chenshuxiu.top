<?php

// MedicineProductMgrAction
class MedicineProductMgrAction extends AuditBaseAction
{

    public function doList () {
        $medicineProducts = Dao::getEntityListByCond('MedicineProduct');
        XContext::setValue("medicineProducts", $medicineProducts);
        return self::SUCCESS;
    }

    public function doAdd () {
        $sfda_medicineid = XRequest::getValue("sfda_medicineid");
        $can_repeat = XRequest::getValue("can_repeat", false);
        DBC::requireNotEmpty($sfda_medicineid, 'sfda_medicineid is null');

        $cond = " and sfda_medicineid =:sfda_medicineid ";
        $bind = array();
        $bind[":sfda_medicineid"] = $sfda_medicineid;
        $medicineproduct = Dao::getEntityByCond("MedicineProduct", $cond, $bind);

        if ($can_repeat == false && $medicineproduct != null) {
            XContext::setJumpPath("/medicineproductmgr/modify?medicineproductid=" . $medicineproduct->id);
        }

        $sfda_medicine = Sfda_medicine::getById($sfda_medicineid);
        DBC::requireTrue($sfda_medicine instanceof Sfda_medicine, "sfda药物不存在{$sfda_medicineid}");

        $medicines = $this->getMedicines($sfda_medicine->name_common, $sfda_medicine->name_brand);

        XContext::setValue('medicines', $medicines);
        XContext::setValue('sfda_medicine', $sfda_medicine);
        return self::SUCCESS;
    }

    private function getMedicines ($name_common, $name_brand) {

        $condbase = " and name = :name_common";
        $bindbase = array();
        $bindbase[":name_common"] = $name_common;
        $medicines = Dao::getEntityListByCond("Medicine", $condbase, $bindbase);
        if(count($medicines)){
            return $medicines;
        }

        $name_common_arr = preg_split('/(?<!^)(?!$)/u', $name_common);
        $name_brand_arr = preg_split('/(?<!^)(?!$)/u', $name_brand);
        $arr = [];

        // 通用名模糊查询
        foreach ($name_common_arr as $k => $word) {
            if ($word != '') {
                $arr[] = " scientificname like '%{$word}%' ";
            }
        }

        // 商品名模糊查询
        foreach ($name_brand_arr as $k => $word) {
            if ($word != '') {
                $arr[] = " name like '%{$word}%' ";
            }
        }

        $cond = '';

        if (count($arr) > 0) {
            $cond .= ' and ( ' . implode('or', $arr) . ' ) ';
        }

        return Dao::getEntityListByCond("Medicine", $cond);
    }

    public function doAddPost () {
        $sfda_medicineid = XRequest::getValue("sfda_medicineid");
        $medicineid = XRequest::getValue("medicineid");
        $pictureid = XRequest::getValue("pictureid", 0);
        $name_common = XRequest::getValue("name_common");
        $name_common_en = XRequest::getValue("name_common_en");
        $name_brand = XRequest::getValue("name_brand");
        $name_brand_en = XRequest::getValue("name_brand_en");
        $name_chem = XRequest::getValue("name_chem");
        $name_chem_en = XRequest::getValue("name_chem_en");
        $drug_way = XRequest::getValue("drug_way");
        $drug_dose = XRequest::getValue("drug_dose");
        $drug_frequency = XRequest::getValue("drug_frequency");
        $pack_unit = XRequest::getValue("pack_unit");
        $yuanliao = XRequest::getValue("yuanliao");
        $zuoyong = XRequest::getValue("zuoyong");
        $yongfa = XRequest::getValue("yongfa");
        $content = XRequest::getValue("content");
        $type_jixing = XRequest::getValue("type_jixing");
        $type_chanpin = XRequest::getValue("type_chanpin");
        $size_chengfen = XRequest::getValue("size_chengfen");
        $size_pack = XRequest::getValue("size_pack");
        $pizhun_date = XRequest::getValue("pizhun_date");
        $piwenhao = XRequest::getValue("piwenhao");
        $benweima = XRequest::getValue("benweima");
        $company_name = XRequest::getValue("company_name");
        $company_name_en = XRequest::getValue("company_name_en");
        $status = XRequest::getValue("status");
        $remark = XRequest::getValue("remark");

        DBC::requireNotEmpty($sfda_medicineid, 'sfda_medicineid is null');
        DBC::requireNotEmpty($medicineid, 'medicineid is null');

        $row = array();
        $row["sfda_medicineid"] = $sfda_medicineid;
        $row["medicineid"] = $medicineid;
        $row["pictureid"] = $pictureid;
        $row["name_common"] = $name_common;
        $row["name_common_en"] = $name_common_en;
        $row["name_brand"] = $name_brand;
        $row["name_brand_en"] = $name_brand_en;
        $row["name_chem"] = $name_chem;
        $row["name_chem_en"] = $name_chem_en;
        $row["drug_way"] = $drug_way;
        $row["drug_dose"] = $drug_dose;
        $row["drug_frequency"] = $drug_frequency;
        $row["pack_unit"] = $pack_unit;
        $row["yuanliao"] = $yuanliao;
        $row["zuoyong"] = $zuoyong;
        $row["yongfa"] = $yongfa;
        $row["content"] = $content;
        $row["type_jixing"] = $type_jixing;
        $row["type_chanpin"] = $type_chanpin;
        $row["size_chengfen"] = $size_chengfen;
        $row["size_pack"] = $size_pack;
        $row["pizhun_date"] = $pizhun_date;
        $row["piwenhao"] = $piwenhao;
        $row["benweima"] = $benweima;
        $row["company_name"] = $company_name;
        $row["company_name_en"] = $company_name_en;
        $row["status"] = $status;
        $row["remark"] = $remark;

        $medicineproduct = MedicineProduct::createByBiz($row);

        XContext::setJumpPath("/medicineproductmgr/list");

        return self::SUCCESS;
    }

    public function doModify () {
        $medicineproductid = XRequest::getValue("medicineproductid");
        DBC::requireNotEmpty($medicineproductid, 'medicineproductid is null');

        $medicineproduct = MedicineProduct::getById($medicineproductid);
        DBC::requireTrue($medicineproduct instanceof MedicineProduct, "药物商品不存在{$medicineproductid}");
        $sfda_medicine = $medicineproduct->sfda_medicine;
        $medicines = $this->getMedicines($medicineproduct->name_common, $medicineproduct->name_brand);

        XContext::setValue('medicines', $medicines);
        XContext::setValue('medicineproduct', $medicineproduct);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $medicineproductid = XRequest::getValue("medicineproductid");

        $medicineid = XRequest::getValue("medicineid", 0);
        $pictureid = XRequest::getValue("pictureid", 0);
        $name_common = XRequest::getValue("name_common");
        $name_common_en = XRequest::getValue("name_common_en");
        $name_brand = XRequest::getValue("name_brand");
        $name_brand_en = XRequest::getValue("name_brand_en");
        $name_chem = XRequest::getValue("name_chem");
        $name_chem_en = XRequest::getValue("name_chem_en");
        $drug_way = XRequest::getValue("drug_way");
        $drug_dose = XRequest::getValue("drug_dose");
        $drug_frequency = XRequest::getValue("drug_frequency");
        $pack_unit = XRequest::getValue("pack_unit");
        $yuanliao = XRequest::getValue("yuanliao");
        $zuoyong = XRequest::getValue("zuoyong");
        $yongfa = XRequest::getValue("yongfa");
        $content = XRequest::getValue("content");
        $type_jixing = XRequest::getValue("type_jixing");
        $type_chanpin = XRequest::getValue("type_chanpin");
        $size_chengfen = XRequest::getValue("size_chengfen");
        $size_pack = XRequest::getValue("size_pack");
        $pizhun_date = XRequest::getValue("pizhun_date");
        $piwenhao = XRequest::getValue("piwenhao");
        $benweima = XRequest::getValue("benweima");
        $company_name = XRequest::getValue("company_name");
        $company_name_en = XRequest::getValue("company_name_en");
        $status = XRequest::getValue("status");
        $remark = XRequest::getValue("remark");

        // todo 断言

        $medicineproduct = MedicineProduct::getById($medicineproductid);
        DBC::requireNotEmpty($medicineproduct, "药物商品不存在{$medicineproductid}");

        $row = array();
        $medicineproduct->medicineid = $medicineid;
        $medicineproduct->pictureid = $pictureid;
        $medicineproduct->name_common = $name_common;
        $medicineproduct->name_common_en = $name_common_en;
        $medicineproduct->name_brand = $name_brand;
        $medicineproduct->name_brand_en = $name_brand_en;
        $medicineproduct->name_chem = $name_chem;
        $medicineproduct->name_chem_en = $name_chem_en;
        $medicineproduct->drug_way = $drug_way;
        $medicineproduct->drug_dose = $drug_dose;
        $medicineproduct->drug_frequency = $drug_frequency;
        $medicineproduct->pack_unit = $pack_unit;
        $medicineproduct->yuanliao = $yuanliao;
        $medicineproduct->zuoyong = $zuoyong;
        $medicineproduct->yongfa = $yongfa;
        $medicineproduct->content = $content;
        $medicineproduct->type_jixing = $type_jixing;
        $medicineproduct->type_chanpin = $type_chanpin;
        $medicineproduct->size_chengfen = $size_chengfen;
        $medicineproduct->size_pack = $size_pack;
        $medicineproduct->pizhun_date = $pizhun_date;
        $medicineproduct->piwenhao = $piwenhao;
        $medicineproduct->benweima = $benweima;
        $medicineproduct->company_name = $company_name;
        $medicineproduct->company_name_en = $company_name_en;
        $medicineproduct->status = $status;
        $medicineproduct->remark = $remark;

        XContext::setJumpPath("/medicineproductmgr/list");

        return self::SUCCESS;
    }

    public function doMark_tongbuJson () {
        $medicineproductid = XRequest::getValue("medicineproductid");
        $medicineproduct = MedicineProduct::getById($medicineproductid);

        $medicineproduct->is_tongbu_chufang_system = 1;

        XContext::setJumpPath("/medicineproductmgr/list");

        return self::SUCCESS;
    }

    public function doMark_untongbuJson () {
        $medicineproductid = XRequest::getValue("medicineproductid");
        $medicineproduct = MedicineProduct::getById($medicineproductid);

        $medicineproduct->is_tongbu_chufang_system = 0;

        XContext::setJumpPath("/medicineproductmgr/list");

        return self::SUCCESS;
    }
}
