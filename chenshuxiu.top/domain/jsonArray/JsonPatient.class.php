<?php

class JsonPatient
{

    // jsonArray4Ipad 新版 by lkt
    public static function jsonArray4Ipad(Patient $patient, Doctor $doctor) {
        $pcard = $patient->getPcardByDoctorOrMasterPcard($doctor);

        $tmp = array();
        $tmp['patientid'] = $patient->id;
        $tmp['pcardid'] = $pcard->id;
        $tmp['createday'] = $patient->getCreateDay(); // 报到时间
        $tmp['createdaystr'] = '入组：' . $patient->getCreateDay(); // 报到时间
        $tmp['name'] = $patient->name;
        $tmp['attrstr'] = $patient->getAttrStr4Ipad();
        $tmp['mobile'] = $patient->getOneMobile();

        // 下次复诊日期
        $revisittkt_next = RevisitTktDao::getNextByPatient_Vaild($patient->id);
        if ($revisittkt_next instanceof RevisitTkt) {
            $revisittkt_next_date_str = '已约：' . $revisittkt_next->thedate;
            // $revisittkt_next_date = $revisittkt_next->thedate;
        } else {
            $revisittkt_next_date_str = '未约复诊';
            // $revisittkt_next_date = '';
        }
        $tmp['next_revisittkt_datestr'] = $revisittkt_next_date_str;

        // 详情页链接 H5
        $ipad_uri = Config::getConfig("ipad_uri");
        $tmp['url'] = "{$ipad_uri}/patientmgr/oneh5?token={$doctor->getToken()}&patientid={$patient->id}";

        // 嵌套pcard信息
        $tmp['pcard'] = JsonPcard::jsonArray($pcard);
        return $tmp;
    }

    // jsonArrayBase
    public static function jsonArrayBase(Patient $patient, $mydoctor = null) {
        $pcard = null;

        // MARK: - #4967 如果当前主治医生和该医生不存在上下级关系则不展示医生姓名
        $doctor_name = "";
        $master_pcard = $patient->getMasterPcard();
        if ($mydoctor instanceof Doctor) {
            // 只会取自己或者下级医生的Pcard
            $doctor_superior = Doctor_SuperiorDao::getOneBy2Doctorid($master_pcard->doctorid, $mydoctor->id);
            // 是自己下属的情况下，显示医生名字
            if ($doctor_superior instanceof Doctor_Superior) {
                $pcard = $master_pcard;
                $doctor_name = $pcard->doctor->name;
            } else {
                $mypcard = PcardDao::getByPatientidDoctorid($patient->id, $mydoctor->id);

                if ($mypcard instanceof Pcard) {
                    $pcard = $mypcard;
                } else {
                    $pcard = $master_pcard;
                }
            }
        } else {
            $pcard = $master_pcard;
            $doctor_name = $pcard->doctor->name;
        }

        $arr = array();
        $arr['diseaseid'] = $pcard->diseaseid;
        $arr['disease_name'] = $pcard->diseasename_show ? $pcard->diseasename_show : $pcard->disease->name;
        $arr['patientid'] = $patient->id;
        $arr['out_case_no'] = $pcard->out_case_no;

        $doctor_yangli = Doctor::getById(1);
        if ($patient->doctor->isYangLi() || $doctor_yangli->isSuperiorOfDoctor($patient->doctor)) {
            $arr['gohospital'] = '北医六院';
            $linkmans = LinkmanDao::getListByPatientid($patient->id);
            // 患者填写手机号所在的省份是 广东省、广西省、福建省、江西省、湖南省、海南省 就诊医院展示为深圳儿童医院
            foreach ($linkmans as $linkman) {
                if (in_array($linkman->xprovinceid, [440000, 450000, 350000, 360000, 430000, 460000])) {
                    $arr['gohospital'] = '深圳儿童医院';
                    break;
                }
            }
        }
        $arr['patientcardno'] = $pcard->patientcardno;
        $arr['patientcard_id'] = $pcard->patientcard_id;
        $arr['bingan_no'] = $pcard->bingan_no;
        $mobile = $patient->getMobiles();
        if ($mobile == "") {
            $arr['mobile'] = '未记录';
        } else {
            $arr['mobile'] = $mobile;
        }
        $doctorid = $pcard->doctorid;
        $arr['doctorid'] = $doctorid;
        $arr['name'] = $patient->name;
        $arr['py'] = $patient->getPy();
        $arr['pinyin'] = $patient->getPinyin();
        $arr['birthday'] = $patient->birthday;
        $arr['sexstr'] = $patient->getSexStrFix();
        $agestr = $patient->getAgeStr();
        if ('0' != $agestr && "" != $agestr) {
            $arr['agestr'] = $agestr . "岁";
        } else {
            $arr['agestr'] = "";
        }
        $arr['citystr'] = $patient->getXprovinceXcityStr();
        $arr['attrstr'] = $patient->getAttrStr();
        $arr['createday'] = $patient->getCreateDay(); // 数据创建时间
        $arr['baodaoday'] = $patient->getBaodaoDay(); // 报到时间
        $arr['updatetime'] = $patient->updatetime;
        $arr['last_visit_day'] = '-'; // 最近复诊时间
        $arr['last_medicine_str'] = $patient->getMedicinestr();

        $arr['is_live'] = $patient->is_live;

        $arr['doctor_name'] = $doctor_name;

        $medicine_monthcnt = '';
        if ($arr['diseaseid'] == 1) {
            $medicine_monthcnt = $patient->getMedicine_monthcnt_str();
        }
        if ($arr['diseaseid'] == 3 || $arr['diseaseid'] == 2) {
            $medicine_monthcnt = $patient->getMedicine_monthcnt_strOfMain();
        }
        $arr['medicine_monthcnt'] = $medicine_monthcnt;
        // $arr['adhd_trend'] = $patient->getTrendOfADHDScore();

        $arr['diseasetag'] = $pcard->complication;
        $patienttags = PatientTagDao::getListByPatientidDoctorid($patient->id, $doctorid);
        $tags = [];
        if ($patient->is_alk == 1) {
            $tags[] = 'ALK';
        }
        foreach ($patienttags as $patienttag) {
            $tags[] = $patienttag->patienttagtpl->name;
        }


        $arr['tags'] = $tags;

        return $arr;
    }

    // jsonArrayForDapp
    public static function jsonArrayForDapp(Patient $patient) {
        $arr = JsonPatient::jsonArrayBase($patient);

        // $arr['patienttagstr'] = $patient->getPatientTagStr(); // 当前标签
        $arr['last_adhd_score'] = $patient->getLastADHDScore();
        $arr['tagstrs'] = array(
            "所患疾病 : " . $patient->disease->name,
            "用药情况 : " . $arr['last_medicine_str'],
            "用药时长 : " . $arr['medicine_monthcnt'],
            "最新SNAP-IV得分 : " . $patient->getLastADHDScore());

        $arr['cells'] = array(
            array(
                "k" => "adhdchart",
                "v" => "SNAP-IV评估量表统计",
                "icon" => "http://img.fangcunyisheng.com/dapi/chart_adhd.png"),
            array(
                "k" => "adhddrugchat",
                "v" => "用药情况统计",
                "icon" => "http://img.fangcunyisheng.com/dapi/chart_drug_time.png"),
            array(
                "k" => "adhddongtai",
                "v" => "对话",
                "icon" => "http://img.fangcunyisheng.com/dapi/dongtai.png"),
            array(
                "k" => "adhdsheets",
                "v" => "评估历史",
                "icon" => "http://img.fangcunyisheng.com/dapi/answersheet.png"));

        return $arr;
    }

    // jsonArrayForDapp_List ios version < 1.19
    public static function jsonArrayForDapp_List(Patient $patient, Doctor $doctor) {
        $arr = JsonPatient::jsonArrayBase($patient);

        $arr['patienttagstr'] = ''; // $patient->getPatientTagStr (); // 当前标签
        $arr['last_adhd_score'] = 0; // $patient->getLastADHDScore ();//列表页没用
        $arr['last_wxopmsg_content'] = ""; // 兼容闪退的问题
        $arr['new_msg_cnt'] = '0';
        $arr['cells'] = array(
            array(
                "k" => "当前用药：",
                "v" => $arr['last_medicine_str'],
                "icon" => "http://img.fangcunyisheng.com/dapi/drug.png"),
            array(
                "k" => "用药时长：",
                "v" => $arr['medicine_monthcnt'],
                "icon" => "http://img.fangcunyisheng.com/dapi/drugtime.png"));

        if ($arr['diseaseid'] == 1) {
            $arr['cells'][] = array(
                "k" => "上次就诊：",
                "v" => $arr['last_visit_day'],
                "icon" => "http://img.fangcunyisheng.com/dapi/last_visit_time.png");
        }

        $pcard = $patient->getPcardByDoctorOrMasterPcard($doctor);
        if ($pcard->out_case_no != '') {
            array_unshift($arr['cells'],
                array(
                    "k" => " 病 历 号 ：",
                    "v" => $pcard->out_case_no,
                    "icon" => "http://img.fangcunyisheng.com/dapi/out_case_no.png"));
        }

        return $arr;
    }

    // jsonArrayForDappNew_List ios version 1.20
    public static function jsonArrayForDappNew_List(Patient $patient, Doctor $doctor) {
        $arr = JsonPatient::jsonArrayBase($patient);

        $arr['last_wxopmsg_content'] = WxOpMsgDao::getLastWxOpMsg($patient->id);
        $arr['new_msg_cnt'] = WxOpMsgDao::getNewMsgCnt($patient->id);

        $arr['patienttagstr'] = ''; // $patient->getPatientTagStr (); // 当前标签
        $arr['last_adhd_score'] = 0; // $patient->getLastADHDScore ();//列表页没用

        $arr['cells'] = array(
            array(
                "k" => "当前用药：",
                "v" => $arr['last_medicine_str'],
                "icon" => "http://img.fangcunyisheng.com/dapi/drug.png"),
            array(
                "k" => "用药时长：",
                "v" => $arr['medicine_monthcnt'],
                "icon" => "http://img.fangcunyisheng.com/dapi/drugtime.png"));

        if ($arr['diseaseid'] == 1) {
            $arr['cells'][] = array(
                "k" => "上次就诊：",
                "v" => $arr['last_visit_day'],
                "icon" => "http://img.fangcunyisheng.com/dapi/last_visit_time.png");
        }
        if ($arr['diseasetag'] != '暂无') {
            $arr['cells'][] = array(
                "k" => " 合 并 症 ：",
                "v" => $arr['diseasetag'],
                "icon" => "http://img.fangcunyisheng.com/dapi/diseasetag2.png");
        }

        $pcard = $patient->getPcardByDoctorOrMasterPcard($doctor);
        if ($pcard->out_case_no != '') {
            array_unshift($arr['cells'],
                array(
                    "k" => " 病 历 号 ：",
                    "v" => $pcard->out_case_no,
                    "icon" => "http://img.fangcunyisheng.com/dapi/out_case_no.png"));
        }

        return $arr;
    }

    // jsonArrayForPad_List
    public static function jsonArrayForPad_List(Patient $patient, Doctor $doctor) {
        $arr = JsonPatient::jsonArrayBase($patient);

        $arr['mobile'] = $patient->getMobiles();

        $pcard = $patient->getPcardByDoctorOrMasterPcard($doctor);
        $out_case_no = $pcard->out_case_no;
        if ($out_case_no == "") {
            $out_case_no = "-";
        }
        $arr['out_case_no'] = $out_case_no;

        $revisittkt_ago = RevisitTktDao::getLastOfPatient_Vaild_Ago($patient->id, $doctor->id);

        $revisittkt_ago_str = '';
        if ($revisittkt_ago instanceof RevisitTkt) {
            $revisittkttime = $revisittkt_ago->thedate;
        } else {
            $revisittkt_ago_str = '无历史记录';
        }

        $arr['cells'] = array(
            array(
                "k" => "入组：",
                "v" => $patient->createtime),
            array(
                "k" => "诊断：",
                "v" => $patient->getTagNamesStr("Disease")),
            array(
                "k" => "用药：",
                "v" => $arr['last_medicine_str']),
            array(
                "k" => "上次复诊：",
                "v" => $revisittkt_ago_str),
            array(
                "k" => "手机号：",
                "v" => $patient->getMobiles()));

        $dapi_uri = Config::getConfig("dapi_uri");
        $arr['url'] = "{$dapi_uri}/patientmgr/one?patientid={$patient->id}";

        $revisittkt_next = RevisitTktDao::getNextByPatient_Vaild($patient->id);

        $button_title_revisittkt = '复诊预约';
        $button_title_pkg = '医嘱用药';

        if ($revisittkt_next instanceof RevisitTkt) {
            $arr['next_revisittkt_date'] = '已约 ' . $revisittkt_next->thedate;
            $button_title_revisittkt = '修改预约';
        } else {
            $arr['next_revisittkt_date'] = '下次复诊：未知';
        }

        $revisitrecord = RevisitRecordDao::getByPatientidToday($patient->id);
        if ($revisitrecord instanceof RevisitRecord) {
            if (false == $revisitrecord->patientmedicinepkg instanceof PatientMedicinePkg) {
                $button_title_pkg = '修改用药';
            }
            if ($revisitrecord->revisittkt instanceof RevisitTkt) {
                $button_title_revisittkt = '修改预约';
            }
        }

        $arr['button_title_revisittkt'] = $button_title_revisittkt;
        $arr['button_title_pkg'] = $button_title_pkg;
        return $arr;
    }

    // jsonArrayForDapp_One
    public static function jsonArrayForDapp_One(Patient $patient, Doctor $doctor) {
        $arr = JsonPatient::jsonArrayBase($patient);

        $arr['patienttagstr'] = ''; // $patient->getPatientTagStr (); // 当前标签
        $arr['last_pipe'] = ($patient->lastpipe instanceof Pipe) ? $patient->lastpipe->pipetpl->title : '';
        $arr['last_adhd_score'] = 0; // $patient->getLastADHDScore ();//列表页没用

        if ($arr['diseaseid'] == 3) {
            $pcard = $patient->getPcardByDoctorOrMasterPcard($doctor);

            $incidenceTime = $pcard->getDescStrOfLast_incidence_date2Today();
            $arr['cells'] = array(
                array(
                    "k" => "电话：",
                    "v" => $arr['mobile'],
                    "icon" => ""),
                array(
                    "k" => " 病 历 号 ：",
                    "v" => $pcard->out_case_no,
                    "icon" => ""),
                array(
                    "k" => " 身份证号 ：",
                    "v" => $patient->prcrid,
                    "icon" => ""),
                array(
                    "k" => "合并症/诊断：",
                    "v" => $arr['diseasetag'],
                    "icon" => ""),
                array(
                    "k" => "距离上次复发时间：",
                    "v" => $incidenceTime,
                    "icon" => ""),
                array(
                    "k" => "用药情况：",
                    "v" => $arr['last_medicine_str'],
                    "icon" => ""));
        } else {
            $arr['cells'] = array(
                array(
                    "k" => "电话：",
                    "v" => $arr['mobile'],
                    "icon" => ""),
                array(
                    "k" => "合并症：",
                    "v" => $arr['diseasetag'],
                    "icon" => ""),
                array(
                    "k" => "用药情况：",
                    "v" => $arr['last_medicine_str'],
                    "icon" => ""));
        }

        return $arr;
    }

    // jsonArrayForIpad
    public static function jsonArrayForIpad(Patient $patient, Doctor $doctor) {
        $pcard = $patient->getPcardByDoctorOrMasterPcard($doctor);

        $tmp = array();
        $tmp['patientid'] = $patient->id;
        $tmp['pcardid'] = $pcard->id;
        $tmp['createday'] = $patient->getCreateDay(); // 报到时间
        $tmp['name'] = $patient->name;
        $tmp['sexstr'] = $patient->getSexStrFix();

        $agestr = $patient->getAgeStr();
        if ('0' != $agestr && "" != $agestr) {
            $tmp['agestr'] = $agestr;
        } else {
            $tmp['agestr'] = "";
        }
        $tmp['attrstr'] = $patient->getAttrStr();
        $tmp['mobile'] = $patient->getOneMobile();
        $tmp['mobiles'] = $patient->getMobiles();

        $tmp['diseasetag'] = $patient->getTagNamesStr("Disease");
        $tmp['diseaseid'] = $pcard->diseaseid;
        $tmp['disease_name'] = $pcard->disease->name;

        $tmp['complication'] = $pcard->getLastComplication();

        $tmp['out_case_no'] = $pcard->out_case_no ? $pcard->out_case_no : '-';
        $tmp['patientcardno'] = $pcard->patientcardno ? $pcard->patientcardno : '-';
        $tmp['patientcard_id'] = $pcard->patientcard_id ? $pcard->patientcard_id : '-';
        $tmp['bingan_no'] = $pcard->bingan_no ? $pcard->bingan_no : '-';

        $tmp['createdaystr'] = '入组：' . $patient->getCreateDay(); // 报到时间

        $revisittkt_next = RevisitTktDao::getNextByPatient_Vaild($patient->id);

        if ($revisittkt_next instanceof RevisitTkt) {
            $revisittkt_next_date_str = '已约：' . $revisittkt_next->thedate;
            // $revisittkt_next_date = $revisittkt_next->thedate;
        } else {
            $revisittkt_next_date_str = '未约复诊';
            // $revisittkt_next_date = '';
        }
        $tmp['next_revisittkt_datestr'] = $revisittkt_next_date_str;

        $ipad_uri = Config::getConfig("ipad_uri");

        $tmp['url'] = "{$ipad_uri}/patient/oneh5?token={$doctor->getToken()}&patientid={$patient->id}";
        $tmp['history_url'] = "{$ipad_uri}/patient/historyh5?token={$doctor->getToken()}&patientid={$patient->id}";

        return $tmp;
    }
}
