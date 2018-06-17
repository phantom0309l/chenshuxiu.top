<?php

// ADRMonitorRuleMgrAction
class ADRMonitorRuleMgrAction extends AuditBaseAction
{

    public function doList() {
        $adrmrs = ADRMonitorRuleDao::getEntityListByCond("ADRMonitorRule");

        XContext::setValue('adrmonitorrules', $adrmrs);
        return self::SUCCESS;
    }

    public function doOne() {
        return self::SUCCESS;
    }

    public function doAdd() {
        $diseases = DiseaseDao::getListAll();
        $medicines = MedicineDao::getListAll();

        XContext::setValue('itemtpls', ADRMonitorRuleItem::getItemTpls());
        XContext::setValue('diseases', $diseases);
        XContext::setValue('medicines', $medicines);
        return self::SUCCESS;
    }

    public function doAjaxAddPost() {
        $medicine_common_name = XRequest::getValue('medicine_common_name', '');
        DBC::requireNotEmptyString($medicine_common_name, '药品通用名称不能为空');
        $medicineids = XRequest::getValue('medicineids', []);
        DBC::requireNotEmpty($medicineids, '药品不能为空');
        $diseaseids = XRequest::getValue('diseaseids', []);
        DBC::requireNotEmpty($diseaseids, '疾病不能为空');
        $rules = XRequest::getValue('rules', []);
        DBC::requireNotEmpty($rules, '规则不能为空');

        foreach ($rules as $rule) {
            $week_from = $rule['week_from'];
            Debug::trace($week_from);
            DBC::requireTrue($week_from > 0, '起始区间范围最小为1');
            $week_to = $rule['week_to'];
            Debug::trace($week_to);
            $week_to = $week_to == "" || $week_to == null ? 99999 : $week_to;
            Debug::trace($week_to);
            DBC::requireTrue(($week_to > $week_from), '右端点必须大于左端点');
            $week_interval = $rule['week_interval'];
            DBC::requireTrue($week_interval > 0, '间隔周期最小为1');
            $items = $rule['items'];
            DBC::requireNotEmpty($items, '监测项目不能为空');
        }

        foreach ($medicineids as $medicineid) {
            foreach ($diseaseids as $diseaseid) {

                $msmrule = ADRMonitorRuleDao::getByMedicineidAndDiseaseidAndMedicineCommonName($medicineid, $diseaseid, $medicine_common_name);
                if (false == $msmrule instanceof ADRMonitorRule) {
                    $row = array();
                    $row["medicineid"] = $medicineid;
                    $row["diseaseid"] = $diseaseid;
                    $row["medicine_common_name"] = $medicine_common_name;
                    $msmrule = ADRMonitorRule::createByBiz($row);
                }
                foreach ($rules as $rule) {
                    $week_from = $rule['week_from'];
                    $week_to = $rule['week_to'];
                    $week_to = $week_to == "" ? 99999 : $week_to;
                    $week_interval = $rule['week_interval'];

                    $row = array();
                    $row["adrmonitorruleid"] = $msmrule->id;
                    $row["week_from"] = $week_from;
                    $row["week_to"] = $week_to;
                    $row["week_interval"] = $week_interval;

                    $items = $rule['items'];
                    foreach ($items as $item) {
                        $msmritem = ADRMonitorRuleItemDao::getByADRMonitorRuleidAndSectionAndIntervalAndEname($msmrule->id, $week_from, $week_to, $week_interval, $item);

                        if (false == $msmritem instanceof ADRMonitorRuleItem) {
                            $row["ename"] = $item;
                            $msmritem = ADRMonitorRuleItem::createByBiz($row);
                        }
                    }
                }
            }
        }

        return self::TEXTJSON;
    }

    public function doModify() {
        $adrmonitorruleid = XRequest::getValue('adrmonitorruleid', 0);
        $adrmonitorrule = ADRMonitorRule::getById($adrmonitorruleid);
        DBC::requireTrue($adrmonitorrule instanceof ADRMonitorRule, '药品不良反应监测规则不存在');

        $diseases = DiseaseDao::getListAll();
        $medicines = MedicineDao::getListAll();

        XContext::setValue('adrmonitorrule', $adrmonitorrule);
        XContext::setValue('itemtpls', ADRMonitorRuleItem::getItemTpls());
        XContext::setValue('diseases', $diseases);
        XContext::setValue('medicines', $medicines);
        return self::SUCCESS;
    }

    public function doAjaxModifyPost() {
        $adrmonitorruleid = XRequest::getValue('adrmonitorruleid', 0);
        $adrmonitorrule = ADRMonitorRule::getById($adrmonitorruleid);
        DBC::requireTrue($adrmonitorrule instanceof ADRMonitorRule, '药品不良反应监测规则不存在');

        $medicine_common_name = XRequest::getValue('medicine_common_name', '');
        DBC::requireNotEmptyString($medicine_common_name, '药品通用名称不能为空');
        $medicineid = XRequest::getValue('medicineid', 0);
        DBC::requireTrue($medicineid > 0, '药品不能为空');
        $diseaseid = XRequest::getValue('diseaseid', 0);
        DBC::requireTrue($diseaseid > 0, '疾病不能为空');
        $rules = XRequest::getValue('rules', []);
        DBC::requireNotEmpty($rules, '规则不能为空');

        foreach ($rules as $rule) {
            $week_from = $rule['week_from'];
            Debug::trace($week_from);
            DBC::requireTrue($week_from > 0, '起始区间范围最小为1');
            $week_to = $rule['week_to'];
            Debug::trace($week_to);
            $week_to = $week_to == "" || $week_to == null ? 99999 : $week_to;
            Debug::trace($week_to);
            DBC::requireTrue(($week_to > $week_from), '右端点必须大于左端点');
            $week_interval = $rule['week_interval'];
            DBC::requireTrue($week_interval > 0, '间隔周期最小为1');
            $items = $rule['items'];
            DBC::requireNotEmpty($items, '监测项目不能为空');
        }

        $items = $adrmonitorrule->getItems();
        foreach ($items as $item) {
            $item->remove();
        }

        $adrmonitorrule->medicine_common_name = $medicine_common_name;

        foreach ($rules as $rule) {
            $week_from = $rule['week_from'];
            $week_to = $rule['week_to'];
            $week_to = $week_to == "" ? 99999 : $week_to;
            $week_interval = $rule['week_interval'];

            $row = array();
            $row["adrmonitorruleid"] = $adrmonitorrule->id;
            $row["week_from"] = $week_from;
            $row["week_to"] = $week_to;
            $row["week_interval"] = $week_interval;

            $items = $rule['items'];
            foreach ($items as $item) {
                $row["ename"] = $item;
                $msmritem = ADRMonitorRuleItem::createByBiz($row);
            }
        }

        return self::TEXTJSON;
    }

    public function doAjaxDeletePost() {
        $adrmonitorruleid = XRequest::getValue('adrmonitorruleid', 0);
        $adrmonitorrule = ADRMonitorRule::getById($adrmonitorruleid);
        DBC::requireTrue($adrmonitorrule instanceof ADRMonitorRule, '药品不良反应监测规则不存在');

        $items = $adrmonitorrule->getItems();
        foreach ($items as $item) {
            $item->remove();
        }

        $adrmonitorrule->remove();

        return self::TEXTJSON;
    }
}
