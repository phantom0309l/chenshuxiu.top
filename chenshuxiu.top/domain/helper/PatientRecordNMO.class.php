<?php

/**
 * nmo运营备注体系
 * @author fhw
 *
 */
class PatientRecordNMO
{

    /**
     * NMO模板
     */
    public static function getPatientRecordTpls () {
        $arr = [];

        $arr['不良事件(NMO)'] = "nmo/_untoward_effect.php";
        $arr['血常规治疗(NMO)'] = "nmo/_wbc_treat.php";
        $arr['肝肾功治疗(NMO)'] = "nmo/_liver_treat.php";
        $arr['血常规(NMO)'] = "nmo/_wbc_checkup.php";
        $arr['肝肾功(NMO)'] = "nmo/_liver_checkup.php";
        $arr['用药方案(NMO)'] = "nmo/_drug_pkg.php";
        $arr['诊断(NMO)'] = "nmo/_diagnose.php";

        return $arr;
    }

    public static function getOptionByCode ($code) {
        $fun = $code . "_options";
        return self::$$fun;
    }

    public static function getShortDesc (PatientRecord $patientrecord) {
        $desc = "[{$patientrecord->thedate}] (NMO) ";

        $data = $patientrecord->loadJsonContent();

        switch ($patientrecord->type) {
            case 'untoward_effect':
                $desc .= "不良事件：";
                $desc .= "[{$data['name']}] ";
                break;

            case 'wbc_treat':
                $desc .= '血常规治疗：';
                $desc .= "[{$data['name']}] ";
                break;

            case 'liver_treat':
                $desc .= '肝肾功治疗：';
                $desc .= "[{$data['name']}] ";
                break;

            case 'wbc_checkup':
                $desc .= '血常规：';
                foreach ($data as $key => $v) {
                    $desc .= "[{$key}:{$v}] ";
                }
                break;

            case 'liver_checkup':
                $desc .= '肝肾功：';
                foreach ($data as $key => $v) {
                    $desc .= "[{$key}:{$v}] ";
                }
                break;

            case 'dead':
                $desc .= '死亡';
                break;

            case 'drug_pkg':
                $desc .= "用药方案：";
                foreach ($data as $key => $v) {
                    $desc .= "[{$v}] ";
                }
                break;

            case 'diagnose':
                $desc .= "诊断：";
                foreach ($data as $key => $v) {
                    $desc .= "[{$v}] ";
                }
                break;
            default:
                break;
        }
        $desc .= " 备注：" . $patientrecord->content;

        return $desc;
    }

    // ----- 多疾病 ----- NMO -----
    private static $diagnose_options = [
        '待确诊' => '待确诊',
        '视神经脊髓炎谱系疾病' => '视神经脊髓炎谱系疾病',
        '多发性硬化' => '多发性硬化',
        '临床孤立综合征' => '临床孤立综合征',
        '复发性视神经炎' => '复发性视神经炎',
        '复发性脊髓炎' => '复发性脊髓炎',
        '其他' => '其他'];

    private static $untoward_effect_type_options = [
        'WBC' => 'WBC',
        'NEUT#' => 'NEUT#',
        'AST' => 'AST',
        'ALT' => 'ALT',
        '视力障碍' => '视力障碍',
        '麻木' => '麻木',
        '疼痛' => '疼痛',
        '痛性痉挛' => '痛性痉挛',
        '皮疹' => '皮疹',
        '感冒' => '感冒',
        '发烧' => '发烧',
        '感染' => '感染',
        '劳累' => '劳累',
        '带状疱疹' => '带状疱疹',
        '消化道反应' => '消化道反应',
        '停药' => '停药',
        '用药错误' => '用药错误'];

    private static $wbc_treat_type_options = [
        "停免疫抑制剂" => "停免疫抑制剂",
        "停免疫抑制剂＋升白针" => "停免疫抑制剂＋升白针"];

    private static $liver_treat_type_options = [
        "停免疫抑制剂＋保肝药" => "停免疫抑制剂＋保肝药",
        "不停免疫抑制剂＋保肝药" => "不停免疫抑制剂＋保肝药"];
    // ----- 多疾病 ----- NMO -----
}
