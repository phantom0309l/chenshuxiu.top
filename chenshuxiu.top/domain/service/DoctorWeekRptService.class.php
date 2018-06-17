<?php
/**
 * Created by PhpStorm.
 * User: nigestream
 * Date: 2018/5/16
 * Time: 14:43
 */

class DoctorWeekRptService
{
    public static function getStData($params, $doctorId, $diseaseId, $weekend_date) {
        $tjuri = Config::getConfig('tj_uri');
        if (empty($tjuri)) {
            $retJson = self::getStDataFromDb($doctorId, $diseaseId, $weekend_date);
        } else {
            $url = $tjuri . '/doctor/statement4doctor';
            $retJson = FUtil::curlGet($url, $params, 5);
        }
        return $retJson;
    }
    //获取统计数据
    public static function getStDataFromDb($doctorId, $diseaseId, $weekend_date) {
        $ret = [];
        if ($diseaseId) {
            $entity = Rpt_week_doctor_dataDao::getByDoctorIdAndDiseaseIdOnWeekend($doctorId, $diseaseId, $weekend_date);
            $ret = json_decode($entity->data, true);
        } else {
            $list = Rpt_week_doctor_dataDao::getListByDoctorIdOnWeekend($doctorId, $weekend_date);
            foreach ($list as $key => $entity) {
                $one = json_decode($entity->data, true);
                if ($key == 0) {
                    $ret = $one;
                    continue;
                }
                foreach ($one as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        if (!isset($ret[$k1][$k2])) {
                            $ret[$k1][$k2] = $v2;
                        } else {
                            $ret[$k1][$k2] += $v2;
                        }
                    }
                    if (count($ret[$k1]) > 1 && $ret[$k1]['全部'] == 0) {
                        unset($ret[$k1]['全部']);
                    }
                }
            }
        }
        $retData = [
            'errno' => 0,
            'errmsg' => '',
            'data' => $ret,
        ];
        $retJson = json_encode($retData, JSON_UNESCAPED_UNICODE);

        return $retJson;
    }
}
