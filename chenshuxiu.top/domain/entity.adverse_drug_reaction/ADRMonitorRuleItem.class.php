<?php

/*
 * ADRMonitorRule
 * 药品不良反应监测规则
 */
class ADRMonitorRuleItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'adrmonitorruleid',  // adrmonitorruleid
            'week_from',  // 周期
            'week_to',  // 周期，最大∞
            'week_interval',  // 间隔
            'ename' // 监测项目
);
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'medicineid',
            'diseaseid',
            'doctorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["adrmonitorrule"] = array(
            "type" => "ADRMonitorRule",
            "key" => "adrmonitorruleid");
    }

    // $row = array();
    // $row["adrmonitorruleid"] = $adrmonitorruleid;
    // $row["week_from"] = $week_from;
    // $row["week_to"] = $week_to;
    // $row["week_interval"] = $week_interval;
    // $row["ename"] = $ename;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "ADRMonitorRule::createByBiz row cannot empty");

        $default = array();
        $default["adrmonitorruleid"] = 0;
        $default["week_from"] = 0;
        $default["week_to"] = 99999;
        $default["week_interval"] = 0;
        $default["ename"] = '';

        $row += $default;
        return new self($row);
    }

    // 监测项目
    public static function getItemTpls () {
        return [
            'xuechanggui' => '血常规',
            'ganshengong' => '肝肾功',
            'yandihuangban' => '眼底黄斑',
            'ningxuegongneng' => '凝血功能',
            'xueyaonongdu' => '血药浓度',
            'xuedianjiezhi' => '血电解质',
            'weight' => '体重',
            'waizhouxueronghejiyin' => '外周血融合基因',
            ];
    }

    public static function getItemStr ($ename) {
        $tpls = self::getItemTpls();
        return $tpls[$ename];
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
}
