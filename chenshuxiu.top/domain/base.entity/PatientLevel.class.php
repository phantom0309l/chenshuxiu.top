<?php
/**
 * Created by PhpStorm.
 * User: fhw
 * Date: 18-3-9
 * Time: 上午11:58
 */

class PatientLevel extends Enum {
    public function __construct($initialValue = null){
        parent::construct($initialValue);
    }

    // 默认
    const default = self::LEVEL_100;

    // VIP患者等级
    const LEVEL_400 = 400;

    // 购药患者等级
    const LEVEL_300 = 300;

    // 开通开药门诊医生的患者等级
    const LEVEL_200 = 200;

    // 普通患者
    const LEVEL_100 = 100;
}