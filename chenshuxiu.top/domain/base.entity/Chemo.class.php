<?php

/*
 * 化疗实体
 */
class Chemo extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'type',  // 化疗性质
            'stage',  // 疗程
            'startdate',  // 化疗开始时间
            'pkg_name',  // 化疗方案
            'pkg_items',  // 具体用药
            'effect_name',  // 疗效
            'effect_content',  // 疗效评价
            'sideeffect_items',  // 不良反应
            'x_yes',  // 同步放疗
            'x_startdate',  // 放疗日期
            'x_part',  // 放疗部位
            'x_type',  // 放疗模式
            'x_dose',  // 放疗剂量
            'x_timespan',  // 放疗时长
            'hospital',  // 化疗医院
            'progress_date',  // 进展日期
            'progress_reason',  // 进展原因
            'status',  // 状态
            'remark'); // 备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
    }

    // $row = array();
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["type"] = $type;
    // $row["treatment"] = $treatment;
    // $row["starttime"] = $starttime;
    // $row["method"] = $method;
    // $row["medicineuse"] = $medicineuse;
    // $row["effect"] = $effect;
    // $row["effectcoment"] = $effectcoment;
    // $row["badreaction"] = $badreaction;
    // $row["status"] = $status;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Chemo::createByBiz row cannot empty");

        $default = array();
        $default['wxuserid'] = 0;
        $default['userid'] = 0;
        // $default['patientid'] = 0; // 需要传进来
        $default['doctorid'] = 0;
        $default["type"] = '';
        $default["stage"] = '';
        $default["startdate"] = '';
        $default["pkg_name"] = '';
        $default["pkg_items"] = '';
        $default["effect_name"] = '';
        $default["effect_content"] = '';
        $default["sideeffect_items"] = '';
        $default['x_yes'] = '';
        $default['x_startdate'] = '';
        $default['x_part'] = '';
        $default['x_type'] = '';
        $default['x_dose'] = '';
        $default['x_timespan'] = '';
        $default['hospital'] = '';
        $default['progress_date'] = '';
        $default['progress_reason'] = '';
        $default["status"] = 1;
        $default["remark"] = '';

        $row += $default;

        if (is_array($row['pkg_items'])) {
            $row['pkg_items'] = json_encode($row['pkg_items'], JSON_UNESCAPED_UNICODE);
        }

        if (is_array($row['sideeffect_items'])) {
            $row['sideeffect_items'] = json_encode($row['sideeffect_items'], JSON_UNESCAPED_UNICODE);
        }

        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getPkg_items () {
        if (is_string($this->_cols['pkg_items'])) {
            return json_decode($this->_cols['pkg_items'], true);
        }
        return $this->_cols['pkg_items'];
    }

    public function getSideeffect_items () {
        if (is_string($this->_cols['sideeffect_items'])) {
            return json_decode($this->_cols['sideeffect_items'], true);
        }
        return $this->_cols['sideeffect_items'];
    }
}
