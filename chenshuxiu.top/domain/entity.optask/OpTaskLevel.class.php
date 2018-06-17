<?php
/**
 * Created by PhpStorm.
 * User: liufei
 * Date: 18-3-9
 * Time: 下午1:17
 */

class OpTaskLevel extends Enum {
    public function __construct($initialValue = null){
        parent::construct($initialValue);
    }

    // 等级9 快速咨询
    const LEVEL_9 = 9;

    // 等级5 药物副反应检查跟进,Lilly患者,Lilly首次电话,Lilly(未审核),患者消息
    const LEVEL_5 = 5;

    // 等级4 VIP患者的【快速通行证消息】任务
    const LEVEL_4 = 4;

    // 等级3 有过支付行为的患者的【消息任务】
    const LEVEL_3 = 3;

    // 等级2 普通(默认级别)
    const LEVEL_2 = 2;

    // 等级1 低级别
    const LEVEL_1 = 1;
}