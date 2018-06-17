<?php

class DealwithTplService
{

    // 生成下拉框
    public static function getCtrArrayForPatient($diseasegroupid) {
        $diseasegroup = DiseaseGroup::getById($diseasegroupid);

        $arr = [];
        $arr["noselect"] = '请选择回复模板...';

        //多动症组暂时不看全部
        if (2 != $diseasegroupid) {
            $arr["all:{$diseasegroupid}|title"] = "全部 = 通用 + {$diseasegroup->name} | 标题排序";
            $arr["all:{$diseasegroupid}|groupstr"] = "全部 = 通用 + {$diseasegroup->name} | 分组显示";
        }

        // self::getCtrArrayForPatient_fix($diseasegroupid, $arr);

        $groupstrs = DealwithTplDao::getGroupstrArray();
        foreach ($groupstrs as $groupstr) {
            $dealwithTpls = DealwithTplDao::getDealwithTplListByGroupstr($groupstr, $diseasegroupid);
            $cnt = count($dealwithTpls);
            if ($cnt > 0) {
                $arr["groupstr:{$groupstr}|{$diseasegroupid}"] = "{$groupstr} | {$cnt}";
            }
        }

        return $arr;
    }

    // 暂时不用了
    private static function getCtrArrayForPatient_fix($diseasegroupid, $arr) {
        $diseasegroup = DiseaseGroup::getById($diseasegroupid);
        $diseases = $diseasegroup->getDiseases();

        $dealwithTpls = DealwithTplDao::getDealwithTplListByCommon();
        $cnt = count($dealwithTpls);

        if ($cnt > 0) {
            $arr["common:0"] = "通用 (不分疾病) | {$cnt}";
        }

        $dealwithTpls = DealwithTplDao::getDealwithTplListByDiseasegroupid($diseasegroupid);
        $cnt = count($dealwithTpls);

        if ($cnt > 0) {
            $arr["diseasegroup:{$diseasegroupid}"] = "疾病组 | {$diseasegroup->name} (不分疾病) | {$cnt}";
        }

        foreach ($diseases as $a) {
            $dealwithTpls = DealwithTplDao::getDealwithTplListByDiseaseid($a->id);
            $cnt = count($dealwithTpls);
            if ($cnt > 0) {
                $arr["disease:{$a->id}"] = "疾病 | {$a->name} | {$cnt}";
            }
        }
    }

    // 全部(通用+疾病组+疾病), 分组
    public static function getDealwithTpls_all_ByDiseasegroupid($diseasegroupid) {
        $arr = [];

        $groupstrs = DealwithTplDao::getGroupstrArray();
        foreach ($groupstrs as $groupstr) {
            $arr1 = DealwithTplDao::getDealwithTplListByGroupstr($groupstr, $diseasegroupid);
            $cnt = count($arr1);
            if ($cnt > 0) {
                $arr[] = "----- {$groupstr} - {$cnt} -----";
                foreach ($arr1 as $a) {
                    $arr[] = $a;
                }
            }
        }

        return $arr;
    }

    private static function getDealwithTpls_all_ByDiseasegroupid_fix($diseasegroupid) {
        $diseasegroup = DiseaseGroup::getById($diseasegroupid);
        $diseases = $diseasegroup->getDiseases();

        $arr = [];

        $arr1 = DealwithTplDao::getDealwithTplListByCommon();
        $cnt = count($arr1);
        if ($cnt > 0) {
            $arr[] = "----- 通用 (不分疾病组) - {$cnt} -----";

            foreach ($arr1 as $a) {
                $arr[] = $a;
            }
        }

        $arr1 = DealwithTplDao::getDealwithTplListByDiseasegroupid($diseasegroupid);
        $cnt = count($arr1);

        if ($cnt > 0) {
            $arr[] = "----- 疾病组 | {$diseasegroup->name} (不分疾病) - {$cnt} -----";

            foreach ($arr1 as $a) {
                $arr[] = $a;
            }
        }

        foreach ($diseases as $a) {
            $arr1 = DealwithTplDao::getDealwithTplListByDiseaseid($a->id);
            $cnt = count($arr1);
            if ($cnt > 0) {
                $arr[] = "----- 疾病 | {$a->name} - {$cnt} -----";

                foreach ($arr1 as $a) {
                    $arr[] = $a;
                }
            }
        }

        return $arr;
    }
}
