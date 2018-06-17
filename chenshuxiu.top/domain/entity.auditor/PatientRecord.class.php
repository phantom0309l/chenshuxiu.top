<?php

/*
 * PatientRecord
 */
class PatientRecord extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid 仅用于修复时使用
            'userid',  // userid 仅用于修复时使用
            'patientid',  // patientid
            'patientrecordtplid', // patientrecordtplid
            'parent_patientrecordid', // 父id
            'type',  // 类型
            'code',  // 疾病分组
            'thedate',  // 日期
            'content',  // 一段文本内容, 备注
            'json_content',  // 其他字段, 用json存储
            'create_auditorid',  // 创建人
            'modify_auditorid'); // 最后修改人
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid',
            'create_auditorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["patientrecordtpl"] = array(
            "type" => "PatientRecordTpl",
            "key" => "patientrecordtplid");
        $this->_belongtos["create_auditor"] = array(
            "type" => "Auditor",
            "key" => "create_auditorid");
        $this->_belongtos["modify_auditor"] = array(
            "type" => "Auditor",
            "key" => "modify_auditorid");
        $this->_belongtos["parent_patientrecord"] = array(
            "type" => "PatientRecord",
            "key" => "parent_patientrecordid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["patientrecordtplid"] = $patientrecordtplid;
    // $row["parent_patientrecordid"] = $parent_patientrecordid;
    // $row["type"] = $type;
    // $row["thedate"] = $thedate;
    // $row["content"] = $content;
    // $row["json_content"] = $json_content;
    // $row["create_auditorid"] = $create_auditorid;
    // $row["modify_auditorid"] = $modify_auditorid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientRecord::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["patientrecordtplid"] = 0;
        $default["parent_patientrecordid"] = 0;
        $default["type"] = '';
        $default["code"] = '';
        $default["thedate"] = '';
        $default["content"] = '';
        $default["json_content"] = '';
        $default["create_auditorid"] = 0;
        $default["modify_auditorid"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getShortDesc () {
        $desc = "[{$this->thedate}] ";

        $data = $this->loadJsonContent();

        // print_r($data);

        switch ($this->type) {
            case 'chemo':
                $desc .= $data['protocol'];
                $desc .= $data['property'];
                $desc .= $data['period'];
                break;

            case 'untoward_effect':
                $desc .= $data['name'];
                $desc .= $data['degree'];
                $desc .= '级';
                break;

            case 'evaluate':
                $desc .= '评估';
                $desc .= $data['assess'];
                break;

            case 'dead':
                $desc .= '死亡';
                break;

            case 'wbc_treat':
                $desc .= '血常规治疗';
                $desc .= $data['name'];
                break;

            case 'wbc_checkup':
                $desc .= '血常规';
                $baixibao = $data['baixibao'];
                $xuehongdanbai = $data['xuehongdanbai'];
                // echo "xuehongdanbai";
                // print_r($data['xuehongdanbai']);
                $xuexiaoban = $data['xuexiaoban'];
                $zhongxingli = $data['zhongxingli'];

                if ($baixibao == '') {
                    $baixibao_desc = "未录入 ";
                } elseif ($baixibao >= 4) {
                    $baixibao_desc = " 0级";
                } elseif ($baixibao >= 3) {
                    $baixibao_desc = " 1级";
                } elseif ($baixibao >= 2) {
                    $baixibao_desc = " 2级";
                } elseif ($baixibao >= 1) {
                    $baixibao_desc = " 3级";
                } elseif ($baixibao >= 0 && $baixibao !== '') {
                    $baixibao_desc = " 4级";
                } else {
                    // 20170616 冯老师的诡异需求，白细胞不填，”白细胞”这三个字都不展示
                }
                $desc .= " 白细胞" . $baixibao_desc;

                if ($xuehongdanbai == '') {
                    $xuehongdanbai_desc = "未录入 ";
                } elseif ($xuehongdanbai >= 120) {
                    $xuehongdanbai_desc = " 0级";
                } elseif ($xuehongdanbai >= 100) {
                    $xuehongdanbai_desc = " 1级";
                } elseif ($xuehongdanbai >= 80) {
                    $xuehongdanbai_desc = " 2级";
                } elseif ($xuehongdanbai >= 65) {
                    $xuehongdanbai_desc = " 3级";
                } elseif ($xuehongdanbai >= 0) {
                    $xuehongdanbai_desc = " 4级";
                } else {
                    $xuehongdanbai_desc = " 未知级（原数为{$xuehongdanbai}）";
                }
                $desc .= " 血红蛋白" . $xuehongdanbai_desc;

                if ($xuexiaoban == '') {
                    $xuexiaoban_desc = "未录入 ";
                } elseif ($xuexiaoban >= 100) {
                    $xuexiaoban_desc = " 0级";
                } elseif ($xuexiaoban >= 75) {
                    $xuexiaoban_desc = " 1级";
                } elseif ($xuexiaoban >= 50) {
                    $xuexiaoban_desc = " 2级";
                } elseif ($xuexiaoban >= 25) {
                    $xuexiaoban_desc = " 3级";
                } elseif ($xuexiaoban >= 0) {
                    $xuexiaoban_desc = " 4级";
                } else {
                    $xuexiaoban_desc = " 未知级（原数为{$xuexiaoban}）";
                }
                $desc .= " 血小板" . $xuexiaoban_desc;

                if ($zhongxingli == '') {
                    $zhongxingli_desc = "未录入 ";
                } elseif ($zhongxingli >= 2) {
                    $zhongxingli_desc = " 0级";
                } elseif ($zhongxingli >= 1.5) {
                    $zhongxingli_desc = " 1级";
                } elseif ($zhongxingli >= 1) {
                    $zhongxingli_desc = " 2级";
                } elseif ($zhongxingli >= 0.5) {
                    $zhongxingli_desc = " 3级";
                } elseif ($zhongxingli >= 0) {
                    $zhongxingli_desc = " 4级";
                } else {
                    $zhongxingli_desc = " 未知级（原数为{$zhongxingli}）";
                }
                $desc .= " 中性粒细胞" . $zhongxingli_desc;

                break;

            case 'lkf_checkup':
                $desc .= '肝肾功';
                $lkf_alt = $data['lkf_alt'];
                $lkf_alp = $data['lkf_alp'];
                $lkf_tbil = $data['lkf_tbil'];
                $lkf_cr = $data['lkf_cr'];

                if ($lkf_alt == '') {
                    $lkf_alt_desc = "未录入 ";
                } elseif ($lkf_alt >= 1000) {
                    $lkf_alt_desc = "4级";
                } elseif ($lkf_alt >= 250) {
                    $lkf_alt_desc = "3级";
                } elseif ($lkf_alt >= 125) {
                    $lkf_alt_desc = "2级";
                } elseif ($lkf_alt >= 50) {
                    $lkf_alt_desc = "1级";
                } elseif ($lkf_alt >= 0) {
                    $lkf_alt_desc = "0级";
                } else {
                    $lkf_alt_desc = "未知级（原数为{$lkf_alt}）";
                }
                $desc .= " ALT " . $lkf_alt_desc;

                if ($lkf_alp == '') {
                    $lkf_alp_desc = "未录入 ";
                } elseif ($lkf_alp >= 2700) {
                    $lkf_alp_desc = "4级";
                } elseif ($lkf_alp >= 675) {
                    $lkf_alp_desc = "3级";
                } elseif ($lkf_alp >= 337.5) {
                    $lkf_alp_desc = "2级";
                } elseif ($lkf_alp >= 135) {
                    $lkf_alp_desc = "1级";
                } elseif ($lkf_alp >= 0) {
                    $lkf_alp_desc = "0级";
                } else {
                    $lkf_alp_desc = "未知级（原数为{$lkf_alp}）";
                }
                $desc .= " ALP " . $lkf_alp_desc;

                if ($lkf_tbil == '') {
                    $lkf_tbil_desc = "未录入 ";
                } elseif ($lkf_tbil >= 222) {
                    $lkf_tbil_desc = "4级";
                } elseif ($lkf_tbil >= 66.6) {
                    $lkf_tbil_desc = "3级";
                } elseif ($lkf_tbil >= 33.3) {
                    $lkf_tbil_desc = "2级";
                } elseif ($lkf_tbil >= 22.2) {
                    $lkf_tbil_desc = "1级";
                } elseif ($lkf_tbil >= 0) {
                    $lkf_tbil_desc = "0级";
                } else {
                    $lkf_tbil_desc = "未知级（原数为{$lkf_tbil}）";
                }
                $desc .= " TBIL " . $lkf_tbil_desc;

                if ($lkf_cr == '') {
                    $lkf_cr_desc = "未录入 ";
                } elseif ($lkf_cr >= 1040) {
                    $lkf_cr_desc = "4级";
                } elseif ($lkf_cr >= 312) {
                    $lkf_cr_desc = "3级";
                } elseif ($lkf_cr >= 156) {
                    $lkf_cr_desc = "2级";
                } elseif ($lkf_cr >= 104) {
                    $lkf_cr_desc = "1级";
                } elseif ($lkf_cr >= 0) {
                    $lkf_cr_desc = "0级";
                } else {
                    $lkf_cr_desc = "未知级（原数为{$lkf_cr}）";
                }
                $desc .= " Cr " . $lkf_cr_desc;

                break;
            default:
                break;
        }
        $desc .= $this->content;

        return $desc;
    }

    public function saveJsonContent (Array $arr) {
        $this->json_content = json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    public function loadJsonContent () {
        return json_decode($this->json_content, true);
    }

    // 获取某个值
    public function getValue ($key) {
        $data = $this->loadJsonContent();
        return $data[$key];
    }

    public function getChildren() {
        return PatientRecordDao::getChildrenByParentPatientRecordid($this->id);
    }


    public function getTitle () {
        $titles = self::getTitles();

        return $titles["{$this->code}_{$this->type}"];
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getTitles () {
        $titles = [
            'cancer_diagnose' => '诊断(肿瘤)',
            'cancer_staging' => '分期(肿瘤)',
            'cancer_operation' => '手术(肿瘤)',
            'cancer_chemo' => '化疗方案(肿瘤)',
            'cancer_untoward_effect' => '不良反应(肿瘤)',
            'cancer_evaluate' => '评估(肿瘤)',
            'cancer_wbc_treat' => '血常规治疗(肿瘤)',
            'cancer_wbc_checkup' => '血常规(肿瘤)',
            'cancer_genetic' => '基因检测(肿瘤)',
            'cancer_markers' => '标志物(肿瘤)',
            'nmo_wbc_treat' => '血常规治疗(nmo)',
            'nmo_wbc_checkup' => '血常规(nmo)',
            'nmo_diagnose' => '诊断(nmo)',
            'nmo_drug_pkg' => '用药方案(nmo)',
            'nmo_liver_checkup' => '肝肾功(nmo)',
            'nmo_liver_treat' => '肝肾功治疗(nmo)',
            'nmo_untoward_effect' => '不良事件(nmo)',
            'common_dead' => '死亡(通用)',
            'common_lose' => '失访(通用)',
            'common_other' => '其他(通用)'
        ];

        return $titles;
    }
}
