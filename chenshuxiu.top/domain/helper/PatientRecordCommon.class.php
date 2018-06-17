<?php

/**
 * 通用运营备注
 * @author fhw
 *
 */
class PatientRecordCommon
{

    /**
     * NMO模板
     */
    public static function getPatientRecordTpls () {
        $arr = [];

        $arr['失访(通用)'] = "common/_lose.php";
        $arr['死亡(通用)'] = "common/_dead.php";
        $arr['其他(通用)'] = "common/_other.php";

        return $arr;
    }

    public static function getShortDesc(PatientRecord $patientrecord){
        $desc = "[{$patientrecord->thedate}] (通用) ";

        $data = $patientrecord->loadJsonContent();

        switch ($patientrecord->type) {
            case 'nickname':
                $desc .= "昵称：";
                $desc .= "[{$data['goodname']}] ";
                break;

            case 'dead':
                $desc .= "死亡 ";
                break;

            case 'other':
                $desc .= "其他 ";
                break;

            case 'lose':
                $desc .= "失访原因：[{$data['reason']}]";
                break;

            default:
                break;
        }
        $desc .= " 备注：" . $patientrecord->content;

        return $desc;
    }

    public static function getOptionByCode ($code) {
        $fun = $code."_options";
        return self::$$fun;
    }

    private static $reason_options = [
        '转院' => '转院',
        '非肿瘤患者' => '非肿瘤患者',
        '非入组医生患者' => '非入组医生患者',
        '不配合失联' => '不配合失联',
        '其他' => '其他'
    ];
}
