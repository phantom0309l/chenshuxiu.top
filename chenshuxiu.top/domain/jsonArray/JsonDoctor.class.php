<?php

class JsonDoctor
{
    // jsonArray
    public static function jsonArray (Doctor $doctor) {
        $arr = array();
        $arr['doctorid'] = $doctor->id;
        $arr['diseaseid'] = $doctor->getMasterDisease()->id;
        $arr['userid'] = $doctor->userid;
        $arr['token'] = $doctor->getToken();
        $arr['name'] = $doctor->name;
        $arr['title'] = $doctor->title;
        $arr['department'] = $doctor->department;
        $arr['hospital_name'] = $doctor->hospital->name;
        $arr['dm_schedule_url'] = UrlFor::dmAppSchedule();
        $arr['dm_task_url'] = UrlFor::dmAppTasks();
        $arr['qr_url'] = $doctor->getMasterDoctorWxShopRef()->getQrUrl();
        $arr['diseasename'] = $doctor->getMasterDisease()->name;
        $arr['module_pushmsg'] = $doctor->module_pushmsg;
        return $arr;
    }

    // jsonArray4Ipad
    public static function jsonArray4Ipad (Doctor $doctor) {
        $arr = JsonDoctor::jsonArrayForIpad_imp($doctor);

        $token = $doctor->getToken();
        $arr['ipad_schedule_url'] = Config::getConfig("ipad_uri") . "/scheduletplmgr/listH5?token={$token}";

        $arr['disease_arr'] = JsonDoctor::get_disease_arr_ofDoctor($doctor);
        $arr['wxshop_arr'] = JsonDoctor::get_wxshop_arr_ofDoctor($doctor);

        return $arr;
    }

    // jsonArrayForIpad
    public static function jsonArrayForIpad (Doctor $doctor) {
        $arr = JsonDoctor::jsonArrayForIpad_imp($doctor);

        $arr['dm_schedule_url'] = UrlFor::dmAppSchedule();

        // 考虑删除, 如果ios也不用的话 --begin--
        $arr['module_pushmsg'] = $doctor->module_pushmsg;
        $arr['qr_url'] = $doctor->getMasterDoctorWxShopRef()->getQrUrl();
        $arr['diseaseid'] = $doctor->getMasterDisease()->id;
        $arr['disease_name'] = $doctor->getMasterDisease()->name;
        // 考虑删除, 如果ios也不用的话 --end--

        $arr['disease_arr'] = JsonDoctor::get_disease_arr_ofDoctor($doctor);

        return $arr;
    }

    // jsonArrayForIpad_imp
    public static function jsonArrayForIpad_imp (Doctor $doctor) {
        $arr = array();
        $arr['doctorid'] = $doctor->id;
        $arr['userid'] = $doctor->userid;
        $arr['token'] = $doctor->getToken();
        $arr['name'] = $doctor->name;
        $arr['sex'] = $doctor->sex;
        $arr['title'] = $doctor->title;
        $arr['department'] = $doctor->department;
        $arr['hospital_name'] = $doctor->hospital->name;
        $arr['diseasenamesstr'] = $doctor->getDiseaseNamesStr();

        return $arr;
    }

    // get_disease_arr_ofDoctor 医生关联的疾病
    // 创建: 20170419 by sjp 自Doctor迁移过来
    public static function get_disease_arr_ofDoctor (Doctor $doctor) {
        $arr = array();
        foreach ($doctor->getDoctorDiseaseRefs() as $ref) {
            $tmp = array();
            $tmp['id'] = $ref->diseaseid; // 向前兼容
            $tmp['diseaseid'] = $ref->diseaseid;
            $tmp['name'] = $ref->disease->name;

            // 20170419 TODO by sjp : 这个 qr_url 迁移至 get_wxshop_arr
            $tmp['qr_url'] = $ref->getOneDoctorWxShopRef()->getQrUrl();

            $arr[] = $tmp;
        }

        return $arr;
    }

    // get_wxshop_arr_ofDoctor 医生关联的服务号
    // 创建: 20170419 by sjp
    public static function get_wxshop_arr_ofDoctor (Doctor $doctor) {
        $arr = array();
        foreach ($doctor->getDoctorWxShopRefs() as $ref) {
            $tmp = array();
            $tmp['id'] = $ref->wxshopid; // 向前兼容
            $tmp['wxshopid'] = $ref->wxshopid;
            $tmp['name'] = $ref->wxshop->name;
            $tmp['disease_names_str'] = $doctor->getDiseaseNamesStr();
            $tmp['qr_url'] = $ref->getQrUrl();

            $arr[] = $tmp;
        }

        return $arr;
    }
}
