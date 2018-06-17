<?php

/**
 * MPN运营备注体系
 * @author fhw
 *
 */
class PatientRecordMPN
{

    /**
     * MPN模板
     */
    public static function getPatientRecordTpls () {
        $arr = [];

        return $arr;
    }

    public static function getOptionByCode ($code) {
        $fun = $code . "_options";
        return self::$$fun;
    }

    // ----- 多疾病 ----- MPN -----
    private static $diagnose_options = [
        '骨髓纤维化' => '骨髓纤维化',
        '慢性粒细胞白血病' => '慢性粒细胞白血病',
        '红细胞增多症' => '红细胞增多症',
        '原发性血小板增多症' => '原发性血小板增多症',
        '特发性血小板增多症' => '特发性血小板增多症',
        '朗格汉斯细胞增生症' => '朗格汉斯细胞增生症'];
    // ----- 多疾病 ----- MPN -----
}
