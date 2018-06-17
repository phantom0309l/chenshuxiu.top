<?php
/**
 * Created by PhpStorm.
 * User: fanghanwen
 * Date: 2018/1/31
 * Time: 10:25
 */
class OpTaskFilterService {
    // 获取一个过滤器
    public static function getOneByCreate_auditorid ($create_auditorid) {
        $cond = " and create_auditorid = :create_auditorid and title <> '' order by id asc limit 1";
        $bind = [
            ':create_auditorid' => $create_auditorid
        ];

        $optaskfilter = Dao::getEntityByCond('OpTaskFilter', $cond, $bind);

        if (false == $optaskfilter instanceof OpTaskFilter) {
            $optaskfilter = OpTaskFilterService::getTempByCreate_auditorid($create_auditorid);
        }

        return $optaskfilter;
    }

    // 获取临时过滤器
    public static function getTempByCreate_auditorid ($create_auditorid) {
        $cond = " and create_auditorid = :create_auditorid and title = '' ";
        $bind = [
            ':create_auditorid' => $create_auditorid
        ];

        $optaskfilter = Dao::getEntityByCond('OpTaskFilter', $cond, $bind);
        if (false == $optaskfilter instanceof OpTaskFilter) {
            $row = [];
            $row["title"] = '';
            $row["filter_json"] = json_encode([], JSON_UNESCAPED_UNICODE);
            $row["is_public"] = 0;
            $row["create_auditorid"] = $create_auditorid;
            $row["remark"] = '个人私有临时过滤器，有且只有一个';
            $optaskfilter = OpTaskFilter::createByBiz($row);
        }

        return $optaskfilter;
    }

    public static function FixShows ($shows, Auditor $auditor) {
        /*
         {
            "mgtplanstr":["全部"],
            "auditorstr":["[--我--]"],
            "doctorgroupstr":[],
            "doctorstr":["0 全部"],
            "diseasegroupstr":["全部"],
            "patientgroupstr":[],
            "optasktplstr":[],
            "statusstr":[],
            "plantimestr":["全部"],
            "levelstr":[],
            "baodaotimestr":["报到时间：距今100天以内"]
        }
         * */

        //患者组
        if ($shows['patientgroupstr'][0] == '全部') {
            unset($shows['patientgroupstr']);
        }

        //患者组
        if ($shows['patientstagestr'][0] == '全部') {
            unset($shows['patientstagestr']);
        }

        //患者组
        if ($shows['optasktplstr'][0] == '全部') {
            unset($shows['optasktplstr']);
        }

        //医生组
        if ($shows['doctorgroupstr'][0] == '全部') {
            unset($shows['doctorgroupstr']);
        }

        //患者组
        if ($shows['levelstr'][0] == '全部') {
            unset($shows['levelstr']);
        }

        //管理计划
        if ($shows['mgtplanstr'][0] == '全部') {
            unset($shows['mgtplanstr']);
        }

        //负责人
        if ($shows['auditorstr'][0] == '[全部]') {
            unset($shows['auditorstr']);
        }

        //医生
        if ($shows['doctorstr'][0] == '0 全部') {
            unset($shows['doctorstr']);
        }

        //状态
        if ($shows['statusstr'][0] == '全部') {
            unset($shows['statusstr']);
        }

        //患者组
        if ($auditor->diseasegroupid > 0) {
            $shows['diseasegroupstr'][0] = $auditor->diseasegroup->name;
        } else {
            if ($shows['diseasegroupstr'][0] == '全部') {
                unset($shows['diseasegroupstr']);
            }
        }

        //患者组
        if ($shows['plantimestr'][0] == '全部') {
            unset($shows['plantimestr']);
        }

        return $shows;
    }

    public static function FixnegativeToZero ($numbers) {
        if (in_array(-1, $numbers)) {
            $have_negative = 0;
            foreach ($numbers as $i => $number) {
                if ($number == -1) {
                    $have_negative = 1;
                    break;
                }
            }

            if ($have_negative == 1) {
                $numbers[$i] = 0;
            }
        }

        return $numbers;
    }
}