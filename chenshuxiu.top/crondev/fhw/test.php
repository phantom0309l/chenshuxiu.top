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

class Test
{
    public function work () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.id
                from patients a
                left join plan_txtmsgs b on b.patientid = a.id and b.code = 'auto_send_paper'
                where b.id is null and a.doctorid = 1697 ";
        $ids = Dao::queryValues($sql);

        print_r($ids);

        foreach ($ids as $id) {
            Plan_txtMsgService::createILD(0, $id);
        }

        $unitofwork->commitAndInit();
    }

    public function fix_mobile_xpatientindex () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from patients where diseaseid = 3 and patientgroupid = 7";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            // 6037 NMO『倍泰龙组』的患者每月发送一次『药物治疗满意度评分』量表。
            $row = [];
            $row["patientid"] = $id;
            $row["auditorid"] = 1;
            $row["objtype"] = 'Patient';
            $row["objid"] = $id;
            $row["type"] = 1;
            $row["code"] = 'nmo_btl';
            $row["plan_send_time"] = '2018-05-18 10:00:00';
            $pt = Plan_txtMsg::createByBiz($row);

            echo $pt->id . " " . $pt->plan_send_time . "\n";
        }

        $unitofwork->commitAndInit();
    }

    public function LCQ () {
        $paper = Paper::getById(659401416);

        // 5831 计算LCQ分数
        $is_LCQ = false;
        $lcqs = [
            'shengli' => [
                'data' => [1, 2, 3, 9, 10, 11, 14, 15],
                'divisor' => 8,
                'sum' => 0
            ],
            'xinli' => [
                'data' => [4, 5, 6, 12, 13, 16, 17],
                'divisor' => 7,
                'sum' => 0
            ],
            'shehui' => [
                'data' => [7, 8, 18, 19],
                'divisor' => 4,
                'sum' => 0
            ]
        ];
        if ($paper->papertplid == 130883366) {
            $is_LCQ = true;
        }

        $xanswerStr = array();
        if ($paper->hasAnswerSheet()) {
            $xanswerarr = array();
            foreach ($paper->getAnswerSheet()->getAnswers() as $xanswer) {

                if (false == $xanswer->isDefaultHide()) {
                    $xanswerarr['question'] = $xanswer->getQuestionCtr()->getQaHtmlQuestionContent();
                    $xanswerarr['xanswer'] = $xanswer->getQuestionCtr()->getQaHtmlAnswerContent();

                    $xanswerStr[] = $xanswerarr;
                }

                if ($is_LCQ) {
                    foreach ($lcqs as $title => $lcq) {
                        if (in_array($xanswer->pos, $lcq['data'])) {
                            $lcqs["{$title}"]['sum'] += $xanswer->score;
                        }
                    }
                }
            }
        }

        $sum = 0;
        foreach ($lcqs as $title => $lcq) {
            $lcqs["{$title}"]['sum'] = round($lcqs["{$title}"]['sum'] / $lcq['divisor'], 1);

            $sum += $lcqs["{$title}"]['sum'];
        }

        echo $sum . "\n";

    }

    public function ILDPH_Report () {
        $wwwrooturl = "https://audit.fangcunyisheng.com";

        $sql = "select a.id, a.doctorid
                from patients a
                inner join reports b on b.patientid = a.id
                where a.diseaseid = 2 and b.createtime >= '2018-03-29' and b.createtime < '2018-04-29' ";
        $ids = Dao::queryRows($sql);

        echo "ILD: 2018-03-29 2018-04-29 \n";
        foreach ($ids as $id) {
            $url = "{$wwwrooturl}/reportmgr/listbypatient?patientid={$id['id']}&doctorid={$id['doctorid']}";

            echo $id['id'] . " => " . $url . "\n";
        }

        $sql = "select a.id, a.doctorid
                from patients a
                  inner join reports b on b.patientid = a.id
                where a.diseaseid = 22 and b.createtime >= '2018-01-29' and b.createtime < '2018-04-29' ";
        $ids = Dao::queryRows($sql);

        echo "\nPH: 2018-01-29 2018-04-29\n";
        foreach ($ids as $id) {
            $url = "{$wwwrooturl}/reportmgr/listbypatient?patientid={$id['id']}&doctorid={$id['doctorid']}";

            echo $id['id'] . " => " . $url . "\n";
        }
    }

    public function Hospital () {
        $hospitals = file("data/hospital.csv");

//        $titles = explode('|', $hospitals[0]);
        unset($hospitals[0]);

        $fail = "";
        foreach ($hospitals as $hospital) {
            // 520|赣州|江西|赣州医学院第一附属医院|陈蓉琴|A-3|117009
            $row = explode('|', $hospital);

            /*
            id bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'id',
            version int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'version',
            createtime datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'createtime',
            updatetime datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'updatetime',
            name varchar(64) NOT NULL DEFAULT '' COMMENT '医院名称',
            shortname varchar(32) NOT NULL DEFAULT '' COMMENT '短名称',
            logo_pictureid bigint(20) NOT NULL DEFAULT '0' COMMENT '医院logo图片',
            qr_logo_pictureid bigint(20) NOT NULL COMMENT '做名片用',
            levelstr varchar(64) NOT NULL DEFAULT '' COMMENT '等级',
            xprovinceid bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '省id',
            xcityid bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '市id',
            xcountyid bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '区id',
            content varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
            status tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
            can_public_zhengding tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '能否配置正丁药品',
            salesrep varchar(64) NOT NULL DEFAULT '' COMMENT '销售负责人?',
            region varchar(10) NOT NULL DEFAULT '' COMMENT '区域,自定义的区域？',
            code varchar(20) NOT NULL DEFAULT '' COMMENT '医院编号',

             [0] => 730
            [1] => 北京市
            [2] => 北京市
            [3] => 中国人民解放军第三〇六医院
            [4] => 谭钦
            [5] => A-2
            [6] =>
             * */

            $sql = "select id from fcqxdb.xprovinces where name like '%{$row[2]}%' ";
            $xprovinceid = Dao::queryValue($sql);

            $sql = "select id from fcqxdb.xcitys where name like '%{$row[1]}%' ";
            $xcityid = Dao::queryValue($sql);

            if ($xprovinceid < 1) {
                echo "xprovinceid {$row[2]}\n";
                $fail .= implode('|', $row);
                continue;
            }
            if ($xcityid < 1) {
                echo "xcityid {$row[1]}\n";
                $fail .= implode('|', $row);
                continue;
            }

            $row[6] = str_replace("\r\n", "", $row[6]);

            $createtime = date('Y-m-d H:i:s');
            $sql = "INSERT INTO actldata.hospitals
                            (
                              id,version,createtime,updatetime,name,shortname,logo_pictureid,qr_logo_pictureid,levelstr,xprovinceid,xcityid,xcountyid,content,status,can_public_zhengding,salesrep,region,code
                            )
                            VALUES
                            (
                              $row[0],1,'$createtime','$createtime','$row[3]','$row[3]',0,0,'',$xprovinceid,$xcityid,0,'',0,1,'$row[4]','$row[5]','$row[6]'
                            )";
            Dao::executeNoQuery($sql);
        }

        $myfile = fopen("data/hospitalfail.php", "w") or die("Unable to open file!");
        fwrite($myfile, $fail);
        fclose($myfile);
    }

    public function fixHospital () {
        /*
        37|常熟市|江苏省|常熟市第一人民医院|倪雪峰|A-3|116029
        38|常熟市|江苏省|常熟市中医院|倪雪峰|A-3|116024
        90|武昌市|湖北省|湖北省人民医院|仲磊|A-1|113002
        182|西昌市 |四川省|四川凉山州第一人民医院|黄海丰|A-1|123015
        245|乌鲁木齐市|新疆维吾尔族自治区|中国人民解放军兰州军区乌鲁木齐总医院|王继文|A-1|124002
        315|梧州市|广西省|广西梧州市人民医院|余丹|A-5|
        358|未知|未知|未知|||
        461|襄樊|湖北|襄阳市第一人民医院|仲磊|A-1|113014
        517|平湖|江苏|平湖第二人民医院|倪雪峰|A-3|116031
        542|马海|内蒙古|马海市人民医院 |冀常龙|A-2|0
        616|柳州市|广西省|柳州市工人医院|余丹|A-5|
        673|清远市|广州省|清远市人民医院|潘远基|A-5|
        707|卫辉市|河南省|新乡医学院第一附属医院|张松辉|A-3|
        713|余姚市|浙江省|余姚市人民医院|赵伟淼|A-3|

            [0] => 730
            [1] => 苏州市 320500 320581
            [2] => 江苏省
            [3] => 中国人民解放军第三〇六医院
            [4] => 谭钦
            [5] => A-2
            [6] =>
         * */
        $list = [
            [37,320500,320000,'常熟市第一人民医院','倪雪峰','A-3','116029', 320581],
            [38,320500,320000,'常熟市中医院','倪雪峰','A-3','116024', 320581],
            [90,420100,420000,'湖北省人民医院','仲磊','A-1','113002', 0],
            [182,513400,510000,'四川凉山州第一人民医院','黄海丰','A-1','123015', 513401],
            [245,650100,650000,'中国人民解放军兰州军区乌鲁木齐总医院','王继文','A-1','124002', 0],
            [315,450400,450000,'广西梧州市人民医院','余丹','A-5','', 0],
            [358,0,0,'','','','', 0],
            [461,420600,420000,'襄阳市第一人民医院','仲磊','A-1','113014', 0],
            [517,0,320000,'平湖第二人民医院','倪雪峰','A-3','116031', 0],
            [542,0,150000,'马海市人民医院','冀常龙','A-2','0', 0],
            [616,450200,450000,'柳州市工人医院','余丹','A-5','', 0],
            [673,441800,440000,'清远市人民医院','潘远基','A-5','', 0],
            [707,410700,410000,'新乡医学院第一附属医院','张松辉','A-3','', 410781],
            [713,330200,330000,'余姚市人民医院','赵伟淼','A-3','', 330281]
        ];

        $createtime = date('Y-m-d H:i:s');
        foreach ($list as $row) {
            $sql = "INSERT INTO actldata.hospitals
                            (
                              id,version,createtime,updatetime,name,shortname,logo_pictureid,qr_logo_pictureid,levelstr,xprovinceid,xcityid,xcountyid,content,status,can_public_zhengding,salesrep,region,code
                            )
                            VALUES
                            (
                              $row[0],1,'$createtime','$createtime','$row[3]','$row[3]',0,0,'',$row[2],$row[1],$row[7],'',0,1,'$row[4]','$row[5]','$row[6]'
                            )";
            Dao::executeNoQuery($sql);
        }

        print_r($list);
    }

    public function Doctor () {
        $doctors = file("data/doctor.csv");

//        $titles = explode('|', $hospitals[0]);
        unset($doctors[0]);

        $fail = "";
        // 17|26|杨媛华|13911773607|呼吸科||2016-01-02 00:00:00|13911773607||True
        foreach ($doctors as $i => $doctor) {
            $row = explode('|', $doctor);

            /*
              `id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'id',
              `version` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'version',
              `createtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'createtime',
              `updatetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'updatetime',
              `userid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'userid',
              `hospitalid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'hospitalid',
              `name` varchar(64) NOT NULL DEFAULT '' COMMENT '姓名',
              `sex` tinyint(4) NOT NULL DEFAULT '0' COMMENT '性别, 值为1时是男性，值为2时是女性，值为0时是未知',
              `title` varchar(64) NOT NULL DEFAULT '' COMMENT '职称',
              `department` varchar(64) NOT NULL DEFAULT '' COMMENT '部门科室',
              `headimg_pictureid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '头像图片id',
              `code` varchar(64) NOT NULL DEFAULT '' COMMENT '编码',
              `pdoctorid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '对应『科室医生』id',
              `patients_referencing` varchar(64) NOT NULL DEFAULT '' COMMENT '编码',
              `first_patient_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '首个患者加入时间',
              `is_sign` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '医生是否签约，0：未签约，1：已签约',
              `menzhen_offset_daycnt` int(11) unsigned NOT NULL DEFAULT '168' COMMENT '医生允许患者报到n天后开启门诊，值为0时，表示永不开启',
              `menzhen_pass_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '门诊开通时间',
              `is_audit_chufang` tinyint(4) NOT NULL DEFAULT '0' COMMENT '医生是否需要审核处方。0：不审核；1：审核',
              `audit_chufang_pass_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'audit_chufang_pass_time',
              `brief` text NOT NULL,
              `be_good_at` text NOT NULL COMMENT '擅长',
              `tip` text NOT NULL,
              `scheduletip` text NOT NULL,
              `bulletin` varchar(500) NOT NULL DEFAULT '' COMMENT '公告',
              `is_bulletin_show` tinyint(4) NOT NULL DEFAULT '0' COMMENT '门诊公告是否展示',
              `is_new_pipe` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否有新的流 dwx_pipe by 医生',
              `module_pushmsg` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '模块开关,医患直接交流',
              `module_audit` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '模块开关,是否医生审核患者报到',
              `bedtkt_pass_content` text NOT NULL COMMENT '约床位,医生默认通过理由',
              `bedtkt_refuse_content` text NOT NULL COMMENT '约床位,医生默认拒绝理由',
              `is_allow_bedtkt` tinyint(4) NOT NULL DEFAULT '0' COMMENT '开通住院预约',
              `is_treatment_notice` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否开启就诊须知。0：没有；1：有',
              `auditorid_yunying` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '运营责任人',
              `auditorid_market` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '市场责任人',
              `auditorid_createby` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
              `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
              `auditstatus` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核状态',
              `auditorid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'auditorid',
              `doctorgroupid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '医生组id',
              `auditremark` varchar(256) NOT NULL DEFAULT '' COMMENT '审核备注',
              `service_remark` varchar(500) NOT NULL DEFAULT '' COMMENT '服务备注',
              `hospital_name` varchar(64) NOT NULL DEFAULT '' COMMENT '旧字段要废弃',
              `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '医生手机号',
              `email` varchar(32) NOT NULL DEFAULT '' COMMENT '邮箱',
              `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
              `adminid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '管理员id',
              `sign` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态',
              `sourcestr` varchar(64) NOT NULL DEFAULT '' COMMENT '来源渠道',
              `lastcachetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '上次缓存时间',
              `lastreadtasktime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后看任务的时间',
              `lastschedule_updatetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '门诊最后一次更新时间',
              `is_alk` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否为ALK项目医生',
             * */
            /*
                [0] => 6754
                [1] => 96
                [2] => 秦瑾
                [3] => 18702749169
                [4] => 心内科
                [5] =>
                [6] => 2017-12-20 16:16:00.883000000
                [7] => 18702749169
                [8] => 19
                [9] => False
            id,hospitalid,name,phone,department,email,insertime,password,adminid,sign;
             * */
            $id = $row[0];
            $createtime = $row[6];
            $updatetime = $createtime;
            $hospitalid = $row[1] > 0 ? $row[1] : 0;
            $name = $row[2];
            $mobile = $row[3];
            $department = $row[4];
            $email = $row[5];
            $password = $row[7];
            $adminid = $row[8] > 0 ? $row[8] : 0;
            $sign = str_replace("\r\n", "", $row[9]);
            if ($sign == 'False') {
                $sign = 0;
            } elseif ($sign == 'True') {
                $sign = 1;
            } else {
                $sign = -1;
            }

            try {
                // 4|23|钱龙||风湿免疫科||2016-01-02 00:00:00|||
                $sql = "insert into actldata.doctors 
                    (id,createtime,updatetime,hospitalid,name,department,mobile,email,password,adminid,sign,brief,be_good_at,tip,scheduletip,bedtkt_pass_content,bedtkt_refuse_content) 
                    values 
                    ($id,'$createtime','$updatetime',$hospitalid,'$name','$department','$mobile','$email','$password',$adminid, $sign,'','','','','','') ";
                Dao::executeNoQuery($sql);
            } catch (Exception $e) {
                $fail .= $doctor;
                echo 'Message: ' .$e->getMessage();
            }

            if ($i % 100 == 0) {
                echo "\n";
            } else {
                echo ".";
            }
        }

        if ($fail) {
            $myfile = fopen("data/doctorfail.php", "w") or die("Unable to open file!");
            fwrite($myfile, $fail);
            fclose($myfile);
        }
        echo $fail . "\n";

//        print_r($doctors);
    }

    public function Department () {
        $departments = file("data/department.csv");

//        $titles = explode('|', $hospitals[0]);
        unset($departments[0]);

        $fail = "";
        foreach ($departments as $department) {
            $row = explode('|', $department);

            $id = $row[0];
            $createtime = date('Y-m-d H:i:s');
            $updatetime = $createtime;
            $name = $row[1];
            $sql = "insert into actldata.departments (id,createtime,updatetime,name) values ($id,'$createtime','$updatetime','$name') ";

            try {
                Dao::executeNoQuery($sql);
            } catch (Exception $e) {
                $fail .= $department;
            }
        }

        if ($fail) {
            $myfile = fopen("data/departmentfail.php", "w") or die("Unable to open file!");
            fwrite($myfile, $fail);
            fclose($myfile);

            echo $fail . "\n";
        }
    }

    public function Patient () {
        $patients = file("data/patient.csv");

//        $titles = explode('|', $hospitals[0]);
        unset($patients[0]);

        $fail = "";
        foreach ($patients as $patient) {
            $row = explode('|', $patient);

            /*
            [0] => 5508
            [1] => 杨启唐
            [2] => 13706466725
            [3] => 涧西区
            [4] =>
            [5] =>
            [6] => 85
            [7] => 呼吸内科
            [8] => 3380
            [9] => 0
            [10] => 0
            [11] =>
            [12] =>
            [13] =>
            [14] =>
            [15] =>
            [16] =>
            [17] =>
            [18] => 0
            [19] => 13706466725@163.com
            [20] => 2018-05-14 00:35:34.820000000
            [21] => 8719071
            [22] => 5
            [23] =>
            5471|廖泓皓|17362928382|新洲区阳逻街|||203|心内科|2177|0|0||||||||0|1025365020@qq.com|2017-12-27 15:39:50.147000000|dly548084|5|
             * */
            //
            $id = $row[0];
            $createtime = $row[20];
            $updatetime = $createtime;
            $doctorid = $row[8] != '' ? $row[8] : 0;
            $first_doctorid = $doctorid;
            $diseaseid = 22;
            $name = $row[1];
            $mobile = $row[2];
            $address = $row[3];
            $hospitalid = $row[6] != '' ? $row[6] : 0;
            $department = $row[7];
            $isself = $row[9] != '' ? $row[9] : -1;
            $adminid = $row[10] != '' ? $row[10] : 0;
            $isrecord = $row[11] != '' ? $row[11] : -1;
            $followuptime = $row[13] == '' ? '0000-00-00 00:00:00' : $row[13];
            $followupid = $row[14] != '' ? $row[14] : 0;
            $followuptype = $row[15] != '' ? $row[15] : -1;
            $followupcount = $row[16] != '' ? $row[16] : 0;
            $followupnextime = $row[17] == '' ? '0000-00-00 00:00:00' : $row[17];
            $type = $row[18];
            $email = $row[19];
            $password = $row[21];
            $ruzutujing = $row[22] != '' ? $row[22] : -1;
            $droptype = $row[23];

            $sql = "insert into actldata.patients
                    (
                        id,createtime,updatetime,doctorid,first_doctorid,diseaseid,name,mobile,address,hospitalid,department,isself,adminid,isrecord,followuptime,followupid,followuptype,followupcount,followupnextime,
                        type,email,password,ruzutujing,droptype,
                        children,past_main_history,past_other_history,family_history,smoke_history,menstruation_history,childbearing_history,allergy_history,drink_history,general_history,infect_history,
                        trauma_history,special_contact_history,opsremark,other_contacts,remark
                    )
                    values
                    (
                        $id,'$createtime','$updatetime',$doctorid,$first_doctorid,$diseaseid,'$name','$mobile','$address',$hospitalid,'$department',$isself,$adminid,$isrecord,'$followuptime',$followupid,$followuptype,$followupcount,'$followupnextime',
                        $type,'$email','$password',$ruzutujing,'$droptype',
                        '','','','','','','','','','','','','','','',''
                    ) ";
            try {
                Dao::executeNoQuery($sql);
            } catch (Exception $e) {
                echo $sql . "\n";
                $fail .= $patient;
            }
        }

        if ($fail) {
            $myfile = fopen("data/patientfail.php", "w") or die("Unable to open file!");
            fwrite($myfile, $fail);
            fclose($myfile);

            echo $fail . "\n";
        }

//        print_r($patients);
    }

    public function DoctorDiseaseRef () {
        $sql = "select id from actldata.doctors ";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $i => $doctorid) {
            $id = $i + 1;
            $version = 1;
            $createtime = date('Y-m-d H:i:s');
            $updatetime = $createtime;
            $diseaseid = 22;
            $sql = "insert into actldata.doctordiseaserefs (id,version,createtime,updatetime,doctorid,diseaseid) 
                    values ($id,$version,'$createtime','$updatetime',$doctorid,$diseaseid) ";
            Dao::executeNoQuery($sql);
        }
    }

    public function Problem () {
        $problems = file("data/problem.csv");

//        $titles = explode('|', $hospitals[0]);
        unset($problems[0]);

        foreach ($problems as $problem) {
            $row = explode('|', $problem);

            $id = $row[0];
            $createtime = date('Y-m-d H:i:s');
            $updatetime = $createtime;
            $name = $row[1];
            $multiplechoice = $row[2];
            $parentid = str_replace("\r\n", "", $row[3]);

            $sql = "insert into actldata.problems (id,createtime,updatetime,name,multiplechoice,parentid)
                    values ($id,'$createtime','$updatetime','$name',$multiplechoice,$parentid) ";
            Dao::executeNoQuery($sql);
        }
    }

    public function Answer () {
        $answers = file("data/answer.csv");

//        $titles = explode('|', $hospitals[0]);
        unset($answers[0]);

        foreach ($answers as $answer) {
            $row = explode(',', $answer);

            /*
                Id,ProblemId,Name
                [0] => 2
                [1] => 3
                [2] => 有
             * */
            $id = $row[0];
            $createtime = date('Y-m-d H:i:s');
            $updatetime = $createtime;
            $problemid = $row[1];
            $name = str_replace("\r\n", "", $row[2]);

            $sql = "insert into actldata.answers (id,createtime,updatetime,problemid,name)
                    values ($id,'$createtime','$updatetime',$problemid,'$name') ";
            Dao::executeNoQuery($sql);
        }
    }

    public function ProblemList () {
        $problemlists = file("data/problemlist.csv");

//        $titles = explode('|', $hospitals[0]);
        unset($problemlists[0]);

        $cnt = count($problemlists);

        foreach ($problemlists as $i => $problemlist) {
            $row = explode('|', $problemlist);

            /*
                [0] => 5461
                [1] => 446
                [2] => 170
                [3] => 0
                [4] => 1986-10-01
             * */
            $id = $row[0];
            $createtime = date('Y-m-d H:i:s');
            $updatetime = $createtime;
            $problemparentid = $row[1];
            $problemid = $row[2];
            $answerid = $row[3];
            $text = str_replace("\r\n", "", $row[4]);

            $sql = "insert into actldata.problemlists (id,createtime,updatetime,problemparentid,problemid,answerid,text)
                    values ($id,'$createtime','$updatetime',$problemparentid,$problemid,$answerid,'$text') ";
            Dao::executeNoQuery($sql);

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";
            } else {
                echo ".";
            }
        }
    }

    public function ProblemParent () {
        $problemparents = file("data/problemparent.csv");

//        $titles = explode('|', $hospitals[0]);
        unset($problemparents[0]);

        $cnt = count($problemparents);

        foreach ($problemparents as $i => $problemparent) {
            $row = explode('|', $problemparent);

            /*
                [0] => 446
                [1] => 165
                [2] => 20
                [3] => 34
                [4] => 2016-02-23 14:16:16.990000000
                [5] => 2016-02-23 14:16:16.990000000
                [6] => 2
                [7] => 1
                [8] =>
                Id|ProblemId|AdminId|PatientId|InsertTime|UpdateTime|Type|FinishType|FollowUpTypeId
             * */
            $id = $row[0];
            $createtime = $row[4];
            $updatetime = $row[5];
            $problemid = $row[1];
            $adminid = $row[2];
            $patientid = $row[3];
            $type = $row[6];
            $finishtype = $row[7];
            $followuptypeid = str_replace("\r\n", "", $row[8]);

            $sql = "insert into actldata.problemparents (id,createtime,updatetime,problemid,adminid,patientid,type,finishtype,followuptypeid)
                    values ($id,'$createtime','$updatetime',$problemid,$adminid,$patientid,$type,$finishtype,'$followuptypeid') ";
            Dao::executeNoQuery($sql);

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";
            } else {
                echo ".";
            }
        }
    }

    public function fixsql () {
        $sql = "explain select * from patients where doctorid = 477 ";
        $rows = Dao::queryRows($sql);
        print_r($rows);
    }

    public static function fixRows($rows) {
        $data = [];
        foreach ($rows as $row) {
            $data["{$row[id]}"] = $row;
        }

        return $data;
    }

    public function Complication () {
        $complications = file("data/complication.csv");

//        $titles = explode('|', $hospitals[0]);
        unset($complications[0]);

        $maxid = Dao::queryValue("select max(id) from actldata.simplesheets ");
        $nextid = $maxid + 1;

        $cnt = count($complications);

        foreach ($complications as $i => $complications) {
            $row = explode('|', $complications);

            /*
                [0] => 2016-05-20 10:41:21.927000000
                [1] => 30
                [2] => 先天性心脏病相关肺动脉高压
                [3] => 房间隔缺损
             * */
            $id = $nextid + $i;
            $createtime = $row[0];
            $updatetime = $createtime;
            $patientid = $row[1];
            $simplesheettplid = 697725516;
            $thedate = $createtime;
            $row[3] = str_replace("\r\n", "", $row[3]);

            $data = [
                "动脉高压类型" => $row[2] . "," . $row[3]
            ];
            $content = json_encode($data, JSON_UNESCAPED_UNICODE);

            $sql = "insert into actldata.simplesheets (id,createtime,updatetime,patientid,simplesheettplid,thedate,content)
                    values ($id,'$createtime','$updatetime',$patientid,'$simplesheettplid','$thedate','$content') ";
            Dao::executeNoQuery($sql);

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";
            } else {
                echo ".";
            }
        }
    }

    public function Data_problem_2 () {
        $sql = "select id,patientid
                from actldata.problemparents
                where problemid = 2 ";
        $ids = Dao::queryRows($sql);
        $cnt = count($ids);

        $need_time_ids = [12,13,14,15,16,17,18];

        $maxid = Dao::queryValue("select max(id) from actldata.simplesheets");
        $nextid = $maxid + 1;

        foreach ($ids as $i => $id) {
            $problemparentid = $id['id'];
            $patientid = $id['patientid'];

            $createtime = Dao::queryValue("select createtime from actldata.problemparents where id = {$problemparentid} ");

            $sql = "select a.id,a.name, b.text
                    from actldata.problems a
                    left join actldata.problemlists b on b.problemid = a.id and b.problemparentid = {$problemparentid}
                    where a.ParentId = 2 ";
            $rows = Dao::queryRows($sql);

            $rows = self::fixRows($rows);

            $data = [];
            foreach ($rows as $is_need_time_id => $row) {
                if ($row['name'] != '时间') {
                    $data["{$row['name']}"] = $row['text'];
                }

                if (in_array($is_need_time_id, $need_time_ids)) {
                    $timekey = $is_need_time_id + 187;
                    $data["{$row['name']}时间"] = $rows["{$timekey}"]['text'];
                }
            }

            $id = $nextid++;
            $updatetime = $createtime;
            $simplesheettplid = 697802756;
            $thedate = $createtime;
            $content = json_encode($data, JSON_UNESCAPED_UNICODE);

            $sql = "insert into actldata.simplesheets (id,createtime,updatetime,patientid,simplesheettplid,thedate,content)
                    values ($id,'$createtime','$updatetime',$patientid,$simplesheettplid,'$thedate','$content') ";
            $sql = str_replace('null', '""', $sql);
            Dao::executeNoQuery($sql);

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";
            } else {
                echo ".";
            }

//            $list[] = $data;
        }
    }

    public function Data_problem_20 () {
        $sql = "select id,patientid
                from actldata.problemparents
                where problemid = 20 ";
        $ids = Dao::queryRows($sql);
        $cnt = count($ids);

        $need_zz_s = [
            "21" => "22",
            "23" => "24",
            "25" => "26",
            "27" => "28",
            "32" => "33",
            "34" => "35",
            "36" => "37",
            "38" => "39",
            "40" => "41",
            "42" => "44"
        ];

        $maxid = Dao::queryValue("select max(id) from actldata.simplesheets");
        $nextid = $maxid + 1;

        foreach ($ids as $i => $id) {
            $problemparentid = $id['id'];
            $patientid = $id['patientid'];

            $createtime = Dao::queryValue("select createtime from actldata.problemparents where id = {$problemparentid} ");

            $sql = "select a.id,a.name, b.text
                    from actldata.problems a
                    left join actldata.problemlists b on b.problemid = a.id and b.problemparentid = {$problemparentid}
                    where a.ParentId = 20 ";
            $rows = Dao::queryRows($sql);

            $rows = self::fixRows($rows);

            $data = [];
            foreach ($rows as $is_need_zz_id => $row) {
                $row['name'] = str_replace(',', '|', $row['name']);

                if (!in_array($is_need_zz_id, $need_zz_s)) {
                    $data["{$row['name']}"] = $row['text'];
                }

                if (array_key_exists($is_need_zz_id, $need_zz_s)) {
                    $data["{$row['name']}"] = $row['text'];

                    $key = $need_zz_s["{$is_need_zz_id}"];
                    if ("右心衰竭症状" == $row['name']) {
                        $data["{$row['name']}进展"] = $rows["{$key}"]['text'];
                    } else {
                        $data["{$row['name']}病程"] = $rows["{$key}"]['text'];
                    }
                }
            }

            $id = $nextid++;
            $updatetime = $createtime;
            $simplesheettplid = 697827396;
            $thedate = $createtime;
            $content = json_encode($data, JSON_UNESCAPED_UNICODE);

            $sql = "insert into actldata.simplesheets (id,createtime,updatetime,patientid,simplesheettplid,thedate,content)
                    values ($id,'$createtime','$updatetime',$patientid,$simplesheettplid,'$thedate','$content') ";
            $sql = str_replace('null', '""', $sql);
            Dao::executeNoQuery($sql);

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";
            } else {
                echo ".";
            }
        }
    }

    public function Data_problem_165 () {
        $sql = "select id,patientid
                from actldata.problemparents
                where problemid = 165 ";
        $ids = Dao::queryRows($sql);
        $cnt = count($ids);

        $maxid = Dao::queryValue("select max(id) from actldata.simplesheets ");
        $nextid = $maxid + 1;

        foreach ($ids as $i => $id) {
            $problemparentid = $id['id'];
            $patientid = $id['patientid'];

            $createtime = Dao::queryValue("select createtime from actldata.problemparents where id = {$problemparentid} ");

            $sql = "select a.id,a.name, b.text
                    from actldata.problems a
                    left join actldata.problemlists b on b.problemid = a.id and b.problemparentid = {$problemparentid}
                    where a.ParentId = 165 ";
            $rows = Dao::queryRows($sql);

            $data = [];
            foreach ($rows as $row) {
                $data["{$row['name']}"] = $row['text'];
            }

            $id = $nextid++;
            $updatetime = $createtime;
            $simplesheettplid = 697829236;
            $thedate = $createtime;
            $content = json_encode($data, JSON_UNESCAPED_UNICODE);

            $sql = "insert into actldata.simplesheets (id,createtime,updatetime,patientid,simplesheettplid,thedate,content)
                    values ($id,'$createtime','$updatetime',$patientid,$simplesheettplid,'$thedate','$content') ";
            $sql = str_replace('null', '""', $sql);
            Dao::executeNoQuery($sql);

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";
            } else {
                echo ".";
            }
        }
    }

    public function Data_problem_187 () {
        $sql = "select id,patientid
                from actldata.problemparents
                where problemid = 187 ";
        $ids = Dao::queryRows($sql);
        $cnt = count($ids);

        $maxid = Dao::queryValue("select max(id) from actldata.simplesheets ");
        $nextid = $maxid + 1;

        foreach ($ids as $i => $id) {
            $problemparentid = $id['id'];
            $patientid = $id['patientid'];

            $createtime = Dao::queryValue("select createtime from actldata.problemparents where id = {$problemparentid} ");

            $sql = "select a.id,a.name, b.text
                    from actldata.problems a
                    left join actldata.problemlists b on b.problemid = a.id and b.problemparentid = {$problemparentid}
                    where a.ParentId = 187 ";
            $rows = Dao::queryRows($sql);

            $data = [];
            foreach ($rows as $row) {
                $data["{$row['name']}"] = $row['text'];
            }

            $id = $nextid++;
            $updatetime = $createtime;
            $simplesheettplid = 697830356;
            $thedate = $createtime;
            $content = json_encode($data, JSON_UNESCAPED_UNICODE);

            $sql = "insert into actldata.simplesheets (id,createtime,updatetime,patientid,simplesheettplid,thedate,content)
                    values ($id,'$createtime','$updatetime',$patientid,$simplesheettplid,'$thedate','$content') ";
            $sql = str_replace('null', '""', $sql);
            Dao::executeNoQuery($sql);

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";
            } else {
                echo ".";
            }
        }
    }

    public function Followtypelist () {
        $followtypelists = file("data/followtypelist.csv");

//        $titles = explode('|', $hospitals[0]);
        unset($followtypelists[0]);

        $cnt = count($followtypelists);

        foreach ($followtypelists as $i => $followtypelist) {
            $row = explode('|', $followtypelist);

            /*
                Id|FId1|Name1|FId2|Name2|FId3|Name3|FId4|Name4|FType|Other|InsertTime|FId5|Name5
                Id[0] => 6
                FId1[1] => 1
                Name1[2] => 呼入电话
                FId2[3] => 6
                Name2[4] => 预约
                FId3[5] => 17
                Name3[6] => 活动预约
                FId4[7] => 33
                Name4[8] => 按开放活动分类
                FType[9] => 0
                Other[10] =>
                InsertTime[11] =>
                FId5[12] =>
                Name5[13] =>
             * */
            $id = $row[0];
            $createtime = $row[11];
            $updatetime = $createtime;
            $fid1 = $row[1];
            $name1 = $row[2];
            $fid2 = $row[3];
            $name2 = $row[4];
            $fid3 = $row[5];
            $name3 = $row[6];
            $fid4 = $row[7];
            $name4 = $row[8];
            $ftype = $row[9] == '' ? 0 : $row[9];
            $other = $row[10];
            $fid5 = $row[12] == '' ? 0 : $row[12];
            $name5 = str_replace("\r\n", "", $row[13]) ?? '';

            $sql = "insert into actldata.followtypelists (id,createtime,updatetime,fid1,name1,fid2,name2,fid3,name3,fid4,name4,fid5,name5,ftype,other)
                    values ($id,'$createtime','$updatetime',$fid1,'$name1',$fid2,'$name2',$fid3,'$name3',$fid4,'$name4',$fid5,'$name5',$ftype,'$other') ";
            Dao::executeNoQuery($sql);

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";
            } else {
                echo ".";
            }
        }
    }

    public function Patientfollow () {
        $sql = "select patientid,followuptypeid
                from actldata.problemparents
                where followuptypeid <> ''
                group by patientid,followuptypeid ";
        $rows = Dao::queryRows($sql);
        $cnt = count($rows);

        $id = 0;
        foreach ($rows as $i => $row) {
            $patientid = $row['patientid'];
            $followuptypeid = $row['followuptypeid'];

            $sql = "select id,createtime,name1,name4
                    from actldata.followtypelists
                    where id in ($followuptypeid)
                    group by createtime,name1 ";
            $patientfollows = Dao::queryRows($sql);

            foreach ($patientfollows as $patientfollow) {
                $id += 1;
                $createtime = $patientfollow['createtime'];
                $updatetime = $createtime;
                $type1 = $patientfollow['name1'];
                $type2 = $patientfollow['name4'];

                $sql = "insert into actldata.patientfollows (id,createtime,updatetime,patientid,type1,type2)
                        values ($id,'$createtime','$updatetime',$patientid,'$type1','$type2') ";
                Dao::executeNoQuery($sql);
            }

            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";
            } else {
                echo ".";
            }
        }
    }

    public function fixBedtkt () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $bedtktids = [534171376, 625777806, 649511466];

        foreach ($bedtktids as $bedtktid) {
            $bedtkt = BedTkt::getById($bedtktid);

            // 患者取消住院预约，则关闭对应的住院预约审核任务
            $optask = OpTaskDao::getOneByObjUnicode($bedtkt, 'audit:bedtkt', true);
            if ($optask instanceof OpTask) {
                OpTaskStatusService::changeStatus($optask, 1);
            }

            // 患者取消住院预约，创建患者取消住院预约任务
            echo "{$bedtktid} before:{$bedtkt->status} => ";
            $bedtkt->setPatientCancelStatus();
            echo "after:{$bedtkt->status} \n";
            $logcontent = "患者取消住院预约\n期望入住日期：{$bedtkt->want_date}";
            $bedtkt->saveLog('patient_cancel', $logcontent);
        }

        $unitofwork->commitAndInit();
    }

    public function fixoptask () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select distinct a.id
                from optasks a
                inner join optlogs b on b.optaskid = a.id
                inner join opnodes c on c.id = a.opnodeid
                where a.status = 1 and c.code != 'finish' and b.content like '%=> [完成]%' and a.optasktplid in (
                    select optasktplid
                    from opnodes
                    where code = 'finish'
                    group by optasktplid
                ) ";
        $ids = Dao::queryValues($sql);
        $cnt = count($ids);

        $i = 1;
        foreach ($ids as $id) {
            $optask = OpTask::getById($id);

            $opnode = OpNodeDao::getByCodeOpTaskTplId('finish', $optask->optasktplid);

            if ($opnode instanceof OpNode) {
                $optask->opnodeid = $opnode->id;
            }

            if ($i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i $rate\n";

                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
            $i ++;
        }

        $unitofwork->commitAndInit();
    }
}

$test = new Test();
//$test->Hospital();
//$test->fixHospital();
//$test->Department();
//$test->Patient();
//$test->DoctorDiseaseRef();
//$test->Problem();
//$test->ProblemList();
//$test->ProblemParent();
//$test->Answer();
//$test->fixsql();
//$test->Complication();
//$test->Data_problem_2();
//$test->Data_problem_20();
//$test->Data_problem_165();
//$test->Data_problem_187();
//$test->Followtypelist();
//$test->Patientfollow();
//$test->fixBedtkt();
$test->fixoptask();