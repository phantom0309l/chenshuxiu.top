<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);
//Debug::$debug = 'Dev';

class Data_input
{
    private function checkHospital ($name) {
        $sql = "select id,name,shortname from hospitals where name like '%{$name}%' or shortname like '%{$name}%' limit 1 ";
        $row = Dao::queryRow($sql);

        return $row;
    }

    public function Hospital () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $hospitals = file("data/actldata/hospital.csv");
        unset($hospitals[0]);

        $cnt = count($hospitals);

        $fail = "";
        $list = [];
        foreach ($hospitals as $i => $hospital) {
            $hospital = str_replace('"', '', $hospital);
            $hospital = str_replace("\n", '', $hospital);
            /*
                "id";"version";"createtime";"updatetime";"name";"shortname";"logo_pictureid";"qr_logo_pictureid";"levelstr";"xprovinceid";"xcityid";"xcountyid";
                "content";"status";"can_public_zhengding";"salesrep";"region";"code"
                [0] => 23
                [1] => 1
                [2] => "2018-05-18 16:37:27"
                [3] => "2018-05-18 16:37:27"
                [4] => "安徽省立医院"
                [5] => "安徽省立医院"
                [6] => 0
                [7] => 0
                [8] => ""
                [9] => 340000
                [10] => 340100
                [11] => 0
                [12] => ""
                [13] => 0
                [14] => 1
                [15] => "黄黎华"
                [16] => "A-3"
                [17] => "105001"
             * */
            $fields = explode(';', $hospital);

            $hospitalname = $this->checkHospital($fields[4]);
            if (!empty($hospitalname)) {
                $list["$fields[0]"] = $hospitalname['id'];

                $fail .= $fields[0] . ";" . $fields[4] . " => " . $hospitalname['id'] . ";" . $hospitalname['name'] . "|" . $hospitalname['shortname']  . "\n";
            } else {
                $jsonarr = [
                    "salesrep" => $fields[15],
                    "region" => $fields[16],
                    "code" => $fields[17]
                ];

                $row = [];
                $row["createtime"] = $fields[2];
                $row["updatetime"] = $fields[2];
                $row["name"] = $fields[4];
                $row["shortname"] = $fields[5];
                $row["xprovinceid"] = $fields[9];
                $row["xcityid"] = $fields[10];
                $row["xcountyid"] = $fields[11];
                $row["content"] = $fields[12];
                $row["old_hospitalid"] = $fields[0];
                $row["remark"] = json_encode($jsonarr, JSON_UNESCAPED_UNICODE);
                Hospital::createByBiz($row);
            }

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";

                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }

        if ($fail) {
            $myfile = fopen("data/actldata/hospital_fix.php", "w") or die("Unable to open file!");
            fwrite($myfile, $fail);
            fclose($myfile);

            $idstr = json_encode($list, JSON_UNESCAPED_UNICODE);
            $myfile = fopen("data/actldata/hospital_fix_id.php", "w") or die("Unable to open file!");
            fwrite($myfile, $idstr);
            fclose($myfile);
        }

        $unitofwork->commitAndInit();
    }

    public function checkDoctor ($name, $hospitalname) {
        $sql = "select a.id,a.name
                from doctors a 
                inner join hospitals b on b.id = a.hospitalid
                where a.name = '{$name}' and (b.name like '%{$hospitalname}%' or b.shortname like '%{$hospitalname}%') ";
        $row = Dao::queryRow($sql);

        return $row;
    }

    public function Doctor () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $hospital_unhnown = Hospital::getById(10000);
        if (false == $hospital_unhnown instanceof Hospital) {
            $row = [];
            $row["id"] = 10000;
            $row["createtime"] = date('Y-m-d H:i:s');
            $row["updatetime"] = date('Y-m-d H:i:s');
            $row["name"] = "爱可泰隆未知医院";
            $row["shortname"] = "爱可泰隆未知医院";
            $hospital_unhnown = Hospital::createByBiz($row);
        }

        $doctor_unknown = Doctor::getById(10000);
        if (false == $doctor_unknown instanceof Doctor) {
            $row = [];
            $row["id"] = 10000;
            $row["createtime"] = date('Y-m-d');
            $row["updatetime"] = date('Y-m-d');
            $row["hospitalid"] = 0;
            $row["name"] = '肺动脉高压未知医生';
            Doctor::createByBiz($row);
            $unitofwork->commitAndInit();
        }

        $doctors = file("data/actldata/doctor.csv");
        unset($doctors[0]);

        $cnt = count($doctors);

        // 旧id => 新id
        $sql = "select id,old_hospitalid from hospitals where old_hospitalid > 0 ";
        $rows = Dao::queryRows($sql);

        $ids = [];
        foreach ($rows as $row) {
            $ids["{$row['old_hospitalid']}"] = $row['id'];
        }

        $idstr = file("data/actldata/hospital_fix_id.php");
        $arr = json_decode($idstr[0], true);
        foreach ($arr as $old => $new) {
            $ids["{$old}"] = $new;
        }

        $old_newids = $ids;

        $fail = "";
        $list = [];
        foreach ($doctors as $i => $doctor) {
            $doctor = str_replace('"', '', $doctor);
            $doctor = str_replace("\n", '', $doctor);
            /*
                "id";"createtime";"updatetime";"hospitalid";"name";"department";"mobile";"email";"password";"adminid";"sign"
                id          [0] => 4
                createtime  [1] => 2016-01-02 00:00:00
                updatetime  [2] => 2016-01-02 00:00:00
                hospitalid  [3] => 23
                name        [4] => 钱龙
                department  [5] => 风湿免疫科
                mobile      [6] =>
                email       [7] =>
                password    [8] =>
                adminid     [9] => 0
                sign        [10] => -1
             * */
            $fields = explode(';', $doctor);

            $hospitalname = Dao::queryValue("select name from actldata.hospitals where id = {$fields[3]} ");
            $doctorname = $this->checkDoctor($fields[4], $hospitalname);
            if (!empty($doctorname)) {
                $list["$fields[0]"] = $doctorname['id'];

                $fail .= $fields[0] . ";" . $fields[4] . ";" . $hospitalname . " => " . $doctorname['id'] . ";" . $doctorname['name'] . ";" . $hospitalname . "\n";
            } else {
                $jsonarr = [
                    "adminid" => $fields[9]
                ];

                $hospitalid = $old_newids["{$fields[3]}"];
                if (!$hospitalid) {
                    $hospitalid = $hospital_unhnown->id;
                }

                $row = [];
                $row["createtime"] = $fields[1];
                $row["updatetime"] = $fields[2];
                $row["hospitalid"] = $hospitalid;
                $row["name"] = $fields[4];
                $row["department"] = $fields[5];
                $row["mobile"] = $fields[6];
                $row["email"] = $fields[7];
                $row["old_doctorid"] = $fields[0];
                $row["remark"] = json_encode($jsonarr, JSON_UNESCAPED_UNICODE);
                $doctor = Doctor::createByBiz($row);

                // user
                $row = [];
                $row["username"] = $doctor->mobile;
                $row["mobile"] = $doctor->mobile;
                $row["password"] = $fields[8];
                $row["name"] = $doctor->name;
                $row["shipstr"] = "本人";
                $user = User::createByBiz($row);

                $doctor->set4lock('userid', $user->id);

                // doctordiseaseref
                $row = [];
                $row["doctorid"] = $doctor->id;
                $row["diseaseid"] = 22;
                DoctorDiseaseRef::createByBiz($row);
            }

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";

                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }

        if ($fail) {
            $myfile = fopen("data/actldata/doctor_fix.php", "w") or die("Unable to open file!");
            fwrite($myfile, $fail);
            fclose($myfile);

            $idstr = json_encode($list, JSON_UNESCAPED_UNICODE);
            $myfile = fopen("data/actldata/doctor_fix_id.php", "w") or die("Unable to open file!");
            fwrite($myfile, $idstr);
            fclose($myfile);
        }

        $unitofwork->commitAndInit();
    }

    public function checkPatient ($name, $doctorid) {
        if ($name && $doctorid) {
            $sql = "select id,name from patients where name = '{$name}' and doctorid = {$doctorid} ";
            $row = Dao::queryRow($sql);

            return $row;
        } else {
            return null;
        }
    }

    public function Patient () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $patients = file("data/actldata/patient.csv");
        unset($patients[0]);

        $cnt = count($patients);

        $sql = "select id,old_doctorid from doctors where old_doctorid > 0 ";
        $rows = Dao::queryRows($sql);

        $ids = [];
        foreach ($rows as $row) {
            $ids["{$row['old_doctorid']}"] = $row['id'];
        }

        $idstr = file("data/actldata/doctor_fix_id.php");
        $arr = json_decode($idstr[0], true);
        foreach ($arr as $old => $new) {
            $ids["{$old}"] = $new;
        }

        $old_newids = $ids;

        $id = 90010000;

        $fail = "";
        $list = [];
        foreach ($patients as $i => $patient) {
            $patient = str_replace('"', '', $patient);
            $patient = str_replace("\n", '', $patient);
            /*
               "id";"createtime";"updatetime";"doctorid";"first_doctorid";"diseaseid";"name";"address";"isself";"adminid";"isrecord";"followuptime";
               "followupid";"followuptype";"followupcount";"followupnextime";"type";"password";"ruzutujing";"droptype";"mobile";"email"
                id [0] => 22
                createtime [1] => 2016-01-01 11:20:19
                updatetime [2] => 2016-01-01 11:20:19
                doctorid [3] => 793
                first_doctorid [4] => 793
                diseaseid [5] => 22
                name [6] => 侯冬霞
                address [7] => 山东省济南市槐荫区
                isself [8] => 1
                adminid [9] => 0
                isrecord [10] => -1
                followuptime [11] => 2017-08-03 15:09:52
                followupid [12] => 23
                followuptype [13] => 0
                followupcount [14] => 1
                followupnextime [15] => 0000-00-00 00:00:00
                type [16] => 7
                password [17] => 316755560LLXX
                ruzutujing [18] => 2
                droptype [19] => 死亡
                mobile [20] => 15153117889
                email [21] =>
             * */
            $fields = explode(';', $patient);

            $jsonarr = [
                "address" => $fields[7],
                "isself" => $fields[8],
                "adminid" => $fields[9],
                "isrecord" => $fields[10],
                "followuptime" => $fields[11],
                "followupid" => $fields[12],
                "followuptype" => $fields[13],
                "followupcount" => $fields[14],
                "followupnextime" => $fields[15],
                "type" => $fields[16],
                "password" => $fields[17],
                "ruzutujing" => $fields[18],
                "droptype" => $fields[19]
            ];

            $doctorid = $old_newids["{$fields[3]}"];

            if (!$doctorid) {
                $doctorid = 10000;
            }

            $patientnamearr = $this->checkPatient($fields[6], $doctorid);
            if (!empty($patientnamearr)) {
                $list["$fields[0]"] = $patientnamearr['id'];

                $fail .= $fields[0] . ";" . $fields[6] . " => " . $patientnamearr['id'] . ";" . $patientnamearr['name'] . "\n";
            } else {
                $row = [];
                $row["id"] = $id;
                $row["createtime"] = $fields[1];
                $row["updatetime"] = $fields[2];
                $row["doctorid"] = $doctorid;
                $row["first_doctorid"] = $doctorid;
                $row["diseaseid"] = 22;
                $row["name"] = $fields[6];
                $row["mobile"] = $fields[20];
                $row["email"] = $fields[21];
                $row["old_patientid"] = $fields[0];
                $row["remark"] = json_encode($jsonarr, JSON_UNESCAPED_UNICODE);
                $patient = Patient::createByBiz($row);

                $id ++;

                // pcard
                $row = [];
                $row["createtime"] = $patient->createtime;
                $row["updatetime"] = $patient->createtime;
                $row["patientid"] = $patient->id;
                $row["patient_name"] = $patient->name;
                $row["doctorid"] = $patient->doctorid;
                $row["diseaseid"] = $patient->diseaseid;
                Pcard::createByBiz($row);

                // user
                if ($fields[11] == 1) {
                    $shipstr = "本人";
                } else {
                    $shipstr = "非本人";
                }
                $row = [];
                $row["patientid"] = $patient->id;
                $row["username"] = $patient->mobile;
                $row["mobile"] = $patient->mobile;
                $row["password"] = $fields[20];
                $row["sasdrowp"] = md5($fields[20]);
                $row["name"] = $patient->name;
                $row["shipstr"] = $shipstr;
                $user = User::createByBiz($row);

                $patient->set4lock('createuserid', $user->id);

                // 创建Linkman
                LinkmanService::updateByUserMobile($user, $patient->mobile);

                // 添加一条流
                $pipetpl = PipeTplDao::getOneByObjtypeAndObjcode('Patient', 'add');
                $row = [];
                $row["createtime"] = $patient->createtime;
                $row["wxuserid"] = 0;
                $row["userid"] = $user->id;
                $row["patientid"] = $patient->id;
                $row["doctorid"] = $patient->doctorid;
                $row["pipetplid"] = $pipetpl->id;
                $row["objtype"] = 'Patient';
                $row["objid"] = $patient->id;
                $row["content"] = '患者入组';
                Pipe::createByBiz($row);

                // 添加一个任务
                OpTaskService::createOpTaskByUnicode(null, $patient, $patient->doctor,'common:default_optasktpl', null, date('Y-m-d'), 1, ["status" => 1]);
            }

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";

                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
            $unitofwork->commitAndInit();
        }

        if ($fail) {
            $myfile = fopen("data/actldata/patient_fix.php", "w") or die("Unable to open file!");
            fwrite($myfile, $fail);
            fclose($myfile);

            $idstr = json_encode($list, JSON_UNESCAPED_UNICODE);
            $myfile = fopen("data/actldata/patient_fix_id.php", "w") or die("Unable to open file!");
            fwrite($myfile, $idstr);
            fclose($myfile);
        }

        $unitofwork->commitAndInit();
    }

    public function PatientFollows () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $patientfollows = file("data/actldata/patientfollow.csv");
        unset($patientfollows[0]);

        $cnt = count($patientfollows);

        $sql = "select id,old_patientid from patients where old_patientid > 0 ";
        $rows = Dao::queryRows($sql);

        $ids = [];
        foreach ($rows as $row) {
            $ids["{$row['old_patientid']}"] = $row['id'];
        }

        $idstr = file("data/actldata/patient_fix_id.php");
        $arr = json_decode($idstr[0], true);
        foreach ($arr as $old => $new) {
            $ids["{$old}"] = $new;
        }

        $old_newids = $ids;

        foreach ($patientfollows as $i => $patientfollow) {
            $patientfollow = str_replace('"', '', $patientfollow);

            /*
                "id";"version";"createtime";"updatetime";"patientid";"type1";"type2"
                id [0] => 1
                version [1] => 1
                createtime [2] => "2016-08-29 13:26:04"
                updatetime [3] => "2016-08-29 13:26:04"
                patientid [4] => 22
                type1 [5] => "呼出电话"
                type2 [6] => "用药核实"
             * */
            $fields = explode(';', $patientfollow);

            $id = $fields[0];
            $version = $fields[1];
            $createtime = $fields[2];
            $updatetime = $fields[3];
            $patientid = $old_newids["{$fields[4]}"];
            $type1 = $fields[5];
            $type2 = $fields[6];

            if ($patientid) {
                $sql = "insert into patientfollows (id,version,createtime,updatetime,patientid,type1,type2)
                    values ($id,$version,'$createtime','$updatetime',$patientid,'$type1','$type2') ";
                Dao::executeNoQuery($sql);

                // 添加一条流
                $doctorid = Dao::queryValue("select doctorid from patients where id = {$patientid} ");
                $pipetpl = PipeTplDao::getOneByObjtypeAndObjcode('PatientFollow', 'actldata');
                $row = [];
                $row["createtime"] = $createtime;
                $row["updatetime"] = $updatetime;
                $row["patientid"] = $patientid;
                $row["doctorid"] = $doctorid;
                $row["pipetplid"] = $pipetpl->id;
                $row["objtype"] = 'PatientFollow';
                $row["objid"] = $patientid;
                $row["content"] = "随访类型：{$type1},随访目的：{$type2}";
                Pipe::createByBiz($row);
            }

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";

                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
            $unitofwork->commitAndInit();
        }

        $unitofwork->commitAndInit();
    }

    public function drugstore () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $drugstores = file("data/actldata/drugstore.csv");
        unset($drugstores[0]);

        $cnt = count($drugstores);

        foreach ($drugstores as $i => $drugstore) {
            /*
            Id|Promary|City|PharmacyName|Adress|Phone|OrderId
            Id [0] => 256
            Promary [1] => 安徽
            City [2] => 合肥
            PharmacyName [3] => 合肥市上药众协大药房有限公司
            Adress [4] => 合肥市水阳江路凌水苑7栋101号
            Phone [5] => 63438107
            OrderId [6] =>
             * */
//            echo $drugstore . "\n";
            $fields = explode('|', $drugstore);

            $id = $fields[0];
            $createtime = date('Y-m-d H:i:s');
            $updatetime = $createtime;
            $title = $fields[3];
            $content = $fields[4];
            $mobile = $fields[5];

            $sql = "select id from xprovinces where name like '{$fields[1]}%' ";
            $xprovinceid = Dao::queryValue($sql);

            $sql = "select id from xcitys where name like '{$fields[2]}%' ";
            $xcityid = Dao::queryValue($sql);

            $xprovinceid = $xprovinceid > 0 ? $xprovinceid : 0;
            $xcityid = $xcityid > 0 ? $xcityid : 0;

            $sql = "insert into drugstores (id,createtime,updatetime,title,xprovinceid,xcityid,content,mobile)
                    values ($id,'$createtime','$updatetime','$title',$xprovinceid,$xcityid,'$content','$mobile') ";
            Dao::executeNoQuery($sql);
//            echo $sql . "\n";

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";

                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }

        $unitofwork->commitAndInit();
    }

    public function simplesheet () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $simplesheets = file("data/actldata/sim.csv");
        unset($simplesheets[0]);

        $cnt = count($simplesheets);

        $sql = "select id,old_patientid from patients where old_patientid > 0 ";
        $rows = Dao::queryRows($sql);

        $ids = [];
        foreach ($rows as $row) {
            $ids["{$row['old_patientid']}"] = $row['id'];
        }

        $idstr = file("data/actldata/patient_fix_id.php");
        $arr = json_decode($idstr[0], true);
        foreach ($arr as $old => $new) {
            $ids["{$old}"] = $new;
        }

        $old_newids = $ids;

        foreach ($simplesheets as $i => $simplesheets) {
            $patientfollow = str_replace('"', '', $simplesheets);

            /*
                "id";"version";"createtime";"updatetime";"wxuserid";"userid";"patientid";"simplesheettplid";"thedate";"content"
                id [0] => 2
                version [1] => 1
                createtime [2] => 2016-05-20 10:41:22
                updatetime [3] => 2016-05-20 10:41:22
                wxuserid [4] => 0
                userid [5] => 0
                patientid [6] => 30
                simplesheettplid [7] => 697725516
                thedate [8] => 2016-05-20
                content [9] => {动脉高压类型:先天性心脏病相关肺动脉高压,房间隔缺损}
             * */
            $fields = explode(';', $patientfollow);

            $content = str_replace('{', '{"', $fields[9]);
            $content = str_replace(':', '":"', $content);
            $content = str_replace('}', '"}', $content);
            if ($fields[7] != 697725516) {
                $content = str_replace(',', '","', $content);
            }
            $fields[9] = $content;

            $id = $fields[0];
            $version = $fields[1];
            $createtime = $fields[2];
            $updatetime = $fields[3];
            $patientid = $old_newids["{$fields[6]}"];
            $simplesheettplid = $fields[7];
            $thedate = $fields[8];
            $content = $fields[9];

            if ($patientid) {
                $sql = "insert into simplesheets (id, version, createtime, updatetime, patientid, simplesheettplid, thedate, content)
                        values ($id, $version, '$createtime', '$updatetime', $patientid, $simplesheettplid, '$thedate', '$content')";
                Dao::executeNoQuery($sql);
            }

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i/$cnt $rate\n";

                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
            $unitofwork->commitAndInit();
        }

        $unitofwork->commitAndInit();
    }
}

$test = new Data_input();
//$test->Hospital();
//$test->Doctor();
//$test->Patient();
//$test->PatientFollows();
//$test->drugstore();
$test->simplesheet();