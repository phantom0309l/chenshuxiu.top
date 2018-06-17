<?php

/**
 * ILD_PH运营备注体系
 * @author fhw
 *
 */
class PatientRecordILD_PH
{

    /**
     * ILD_PH模板
     */
    public static function getPatientRecordTpls () {
        $arr = [];

        return $arr;
    }

    public static function getOptionByCode ($code) {
        $fun = $code . "_options";
        return self::$$fun;
    }

    // ----- 多疾病 ----- ILD_PH -----
    private static $diagnose_options = [
        '特发性肺动脉高压' => '特发性肺动脉高压',
        '慢性血栓栓塞性肺动脉高压' => '慢性血栓栓塞性肺动脉高压',
        '结缔组织病肺动脉高压' => '结缔组织病肺动脉高压',
        '先天性心脏病肺动脉高压' => '先天性心脏病肺动脉高压',
        '慢性肺病肺动脉高压' => '慢性肺病肺动脉高压',
        '其他' => '其他'];
    // ----- 多疾病 ----- ILD_PH -----
}
