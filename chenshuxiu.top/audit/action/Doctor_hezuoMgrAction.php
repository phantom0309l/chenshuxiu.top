<?php

class Doctor_hezuoMgrAction extends AuditBaseAction
{

    // 新建
    public function doAdd () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);

        XContext::setValue("doctor", $doctor);
        return self::SUCCESS;
    }

    public function doAddPost () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $company = XRequest::getValue("company", "");
        $doctor_code = XRequest::getValue("doctor_code", "");
        $name = XRequest::getValue("name", "");
        $sex = XRequest::getValue("sex", 0);
        $title1 = XRequest::getValue("title1", "");
        $title2 = XRequest::getValue("title2", "");
        $hospital_name = XRequest::getValue("hospital_name", "");
        $department = XRequest::getValue("department", "");
        $json = XRequest::getUnSafeValue("json", "");

        if ('' == $doctor_code || '' == $company) {
            XContext::setJumpPath("/doctor_hezuomgr/add?doctorid={$doctorid}&preMsg=" . urlencode("请填写doctor_code和company"));
            return self::SUCCESS;
        }

        $doctor_hezuo = Doctor_hezuoDao::getOneByCompanyDoctorCode("Lilly", $doctor_code);
        if ($doctor_hezuo instanceof Doctor_hezuo) {
            XContext::setJumpPath("/doctor_hezuomgr/modify?doctor_hezuoid={$doctor_hezuo->id}");

            return self::SUCCESS;
        }

        $row = array();
        $row["doctorid"] = $doctorid;
        $row["company"] = $company;
        $row["doctor_code"] = $doctor_code;
        $row["name"] = $name;
        $row["sex"] = $sex;
        $row["title1"] = $title1;
        $row["title2"] = $title2;
        $row["hospital_name"] = $hospital_name;
        $row["department"] = $department;
        $row["json"] = $json;

        Doctor_hezuo::createByBiz($row);

        XContext::setJumpPath("/doctor_hezuomgr/list");

        return self::SUCCESS;
    }

    // 列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);

        $status = XRequest::getValue("status", - 1);
        $doctor_name = XRequest::getValue("doctor_name", "");

        $bind = [];
        $cond = "";
        if ($status == 2) {
            $cond .= " and id in (
                select a.id
                from doctor_hezuos a
                inner join doctors b on a.name=b.name
                where a.doctorid=0) ";
        } elseif ($status == 3) {
            $cond .= " and doctorid>0 and hospital_name_2 = '' ";
        } elseif ($status == 4) {
            $cond .= " and doctorid>0 and hospital_name_2 != '' ";
        } elseif ($status == 5) {
            $cond .= " and doctorid=0 and hospital_name_2 != '' ";
        } elseif ($status > - 1) {
            // 按状态搜
            $cond .= " and status = :status";
            $bind[':status'] = $status;
        }

        // 模糊搜索患者名
        if ($doctor_name != "") {
            $cond .= " and name like :doctor_name";
            $bind[':doctor_name'] = "%{$doctor_name}%";
        }

        $cond .= " order by id ";

        $doctor_hezuos = Dao::getEntityListByCond4Page("Doctor_hezuo", $pagesize, $pagenum, $cond, $bind);

        $countSql = "select count(*) as cnt from doctor_hezuos where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctor_hezuomgr/list?status={$status}&doctor_name={$doctor_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("doctor_name", $doctor_name);
        XContext::setValue("status", $status);

        XContext::setValue("doctor_hezuos", $doctor_hezuos);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    public function doModify () {
        $doctor_hezuoid = XRequest::getValue("doctor_hezuoid", 0);

        $doctor_hezuo = Doctor_hezuo::getById($doctor_hezuoid);

        XContext::setValue("doctor_hezuo", $doctor_hezuo);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $doctor_hezuoid = XRequest::getValue("doctor_hezuoid", 0);
        $title1 = XRequest::getValue("title1", "");
        $title2 = XRequest::getValue("title2", "");
        $hospital_name = XRequest::getValue("hospital_name", "");
        $department = XRequest::getValue("department", "");
        $marketer_name = XRequest::getValue("marketer_name", "");
        $city_name_bymarketer = XRequest::getValue("city_name_bymarketer", "");
        $area_bymarketer = XRequest::getValue("area_bymarketer", "");
        $json = XRequest::getValue("json", "");

        $doctor_hezuo = Doctor_hezuo::getById($doctor_hezuoid);
        $doctor_hezuo->title1 = $title1;
        $doctor_hezuo->title2 = $title2;
        $doctor_hezuo->hospital_name = $hospital_name;
        $doctor_hezuo->department = $department;
        $doctor_hezuo->marketer_name = $marketer_name;
        $doctor_hezuo->city_name_bymarketer = $city_name_bymarketer;
        $doctor_hezuo->area_bymarketer = $area_bymarketer;
        if ('' != $json) {
            $doctor_hezuo->json = json_encode($json);
        }

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/doctor_hezuomgr/modify?doctor_hezuoid=" . $doctor_hezuoid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 与方寸医生自身医生做关联
    public function doRelation () {
        $doctor_hezuoid = XRequest::getValue("doctor_hezuoid", 0);
        $doctor_hezuo = Doctor_hezuo::getById($doctor_hezuoid);
        $doctor_name = $doctor_hezuo->name;
        $doctors = DoctorDao::getListByName($doctor_name);

        XContext::setValue("doctor_hezuo", $doctor_hezuo);
        XContext::setValue("doctors", $doctors);
        return self::SUCCESS;
    }

    // 与方寸医生自身医生做关联
    public function doRelationJson () {
        $doctor_hezuoid = XRequest::getValue("doctor_hezuoid", 0);
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor_hezuo = Doctor_hezuo::getById($doctor_hezuoid);

        if ($doctor_hezuo instanceof Doctor_hezuo) {
            $doctor_hezuo->doctorid = $doctorid;
        }

        echo "ok";
        return self::BLANK;
    }

    // 开通合作
    public function doPassHezuoJson () {
        $doctor_hezuoid = XRequest::getValue("doctor_hezuoid", 0);
        $doctor_hezuo = Doctor_hezuo::getById($doctor_hezuoid);

        if ($doctor_hezuo instanceof Doctor_hezuo) {
            $doctor_hezuo->pass();
        }

        echo "ok";
        return self::BLANK;
    }

    // 关闭合作
    public function doCloseHezuoJson () {
        $doctor_hezuoid = XRequest::getValue("doctor_hezuoid", 0);
        $doctor_hezuo = Doctor_hezuo::getById($doctor_hezuoid);

        if ($doctor_hezuo instanceof Doctor_hezuo) {
            $doctor_hezuo->close();
        }

        echo "ok";
        return self::BLANK;
    }

    public function doCreateDoctor () {
        $doctor_hezuoid = XRequest::getValue("doctor_hezuoid", 0);
        $username = XRequest::getValue("username", '');

        $doctor_hezuo = Doctor_hezuo::getById($doctor_hezuoid);

        echo '<pre>';

        if ($doctor_hezuo->doctor instanceof Doctor) {
            echo "already doctorid = {$doctor_hezuo->doctorid}";
            return self::blank;
        }

        $doctor_name = $doctor_hezuo->name;
        $hospital_name = $doctor_hezuo->hospital_name;
        $hospital_name_2 = $doctor_hezuo->hospital_name_2;

        $usernames = [];
        if ($username) {
            $usernames[] = $username;
        }
        $usernames[] = strtolower(PinyinUtilNew::Word2PY($doctor_name, '')); // pinyin
        $usernames[] = strtolower(PinyinUtilNew::Word2PY($doctor_name)); // py

        $user = null;
        $str = '';
        foreach ($usernames as $username) {
            $user = UserDao::getByUserName($username);
            if (false == $user instanceof User) {
                break;
            } else {
                $str .= " {$username}";
            }
        }

        if ($user instanceof User) {
            echo "\n already username = {$str}";
            return self::blank;
        }

        $hospital = $this->getHospital($hospital_name, $hospital_name_2);

        if (false == $hospital instanceof Hospital) {
            echo "\n {$hospital_name} {$hospital_name_2} 找不到医院，手动查验吧！！！ ";
            return self::blank;
        }

        $disease = Disease::getById(1);

        $row = array();
        $row["username"] = $username;
        $row["password"] = $username . rand(300, 999);
        $row["name"] = $doctor_name;
        $user = User::createByBiz($row);
        echo "\n 新建user成功：{$user->id}";

        $row = array();
        $row["id"] = 1 + Dao::queryValue('select max(id) as maxid from doctors where id < 10000');
        $row["userid"] = $user->id;
        $row["name"] = $doctor_name;
        $row["auditorid_yunying"] = 0;
        $row["auditorid_market"] = 0;
        $row["auditorid_createby"] = 0;

        $row["hospitalid"] = $hospital->id;

        $row["code"] = $username;

        $row["pdoctorid"] = 0;
        $row["status"] = 1;

        $doctor = Doctor::createByBiz($row);
        echo "\n 新建doctor成功：{$doctor->id}";

        $doctor_hezuo->doctorid = $doctor->id;

        // 新建doctorwxshopref
        $wxshop = WxShopDao::getByDiseaseid(1);
        $row = [];
        $row['doctorid'] = $doctor->id;
        $row['wxshopid'] = $wxshop->id;
        $doctorwxshopref = DoctorWxShopRef::createByBiz($row);
        $doctorwxshopref->check_qr_ticket();

        // 新建doctorDiseaseRef
        $row = array();
        $row["doctorid"] = $doctor->id;
        $row["diseaseid"] = 1;
        $doctorDiseaseRef = DoctorDiseaseRef::createByBiz($row);

        XContext::setJumpPath("/Doctor_hezuoMgr/Relation?doctor_hezuoid={$doctor_hezuoid}");

        return self::blank;
    }

    private function getHospital ($hospital_name, $hospital_name_2) {
        $arr_change_hospital = array(
            '湖州市第三人民医院' => '湖州市妇幼保健院',
            '中国人民解放军陆军总医院' => '中国人民解放军总医院',
            '首都儿科研究所' => '儿研所',
            '首都医科大学附属北京安定医院' => '北京安定医院',
            '首都医科大学附属北京宣武医院' => '首都医科大学宣武医院',
            '北大六院' => '北医六院',
            '四川大学华西第二医院' => '华西妇产儿童医院',
            '深圳儿童医院' => '深圳市儿童医院',
            '上海儿童医学中心' => '上海交通大学医学院附属上海儿童医学中心',
            '上海交通大学医学院附属新华医院' => '上海新华医院');

        if (isset($arr_change_hospital[$hospital_name])) {
            $hospital_name = $arr_change_hospital[$hospital_name];
        }

        $cond = " and (name like '%{$hospital_name}%' or shortname like '%{$hospital_name}%' or name like '%{$hospital_name_2}%' or shortname like '%{$hospital_name_2}%') ";

        return Dao::getEntityByCond('Hospital', $cond);
    }

    public function doLilly_zhuoka () {
        $doctor_hezuoid = XRequest::getValue("doctor_hezuoid", 0);
        $doctor_hezuo = Doctor_hezuo::getById($doctor_hezuoid);

        if ($doctor_hezuo->doctor instanceof Doctor) {
            NameCardHelper::lilly_zhuoka($doctor_hezuo, $doctor_hezuo->hospital_name);
        }

        return self::blank;
    }

    public function doLilly_patient_page_back () {
        $doctor_hezuoid = XRequest::getValue("doctor_hezuoid", 0);
        $doctor_hezuo = Doctor_hezuo::getById($doctor_hezuoid);

        if ($doctor_hezuo->doctor instanceof Doctor) {
            NameCardHelper::lilly_patient_page_back($doctor_hezuo, $doctor_hezuo->hospital_name);
        }

        return self::blank;
    }

    public function doLilly_patient_page_back_20170919 () {
        $doctor_hezuoid = XRequest::getValue("doctor_hezuoid", 0);
        $doctor_hezuo = Doctor_hezuo::getById($doctor_hezuoid);

        if ($doctor_hezuo->doctor instanceof Doctor) {
            NameCardHelper::lilly_patient_page_back_20170919($doctor_hezuo, $doctor_hezuo->hospital_name);
        }

        return self::blank;
    }
}
