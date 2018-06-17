<?php

// 患者管理
class PatientMgrAction extends AuditBaseAction
{

    public function doDefault () {
        return self::SUCCESS;
    }

    // 患者首页
    public function doIndex () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);

        XContext::setValue("patient", $patient);
        return self::SUCCESS;
    }

    // 患者列表
    public function dolist () {
        $keyword = XRequest::getValue("keyword", '');
        $doctor_name = XRequest::getValue("doctor_name", '');

        $fromdate = XRequest::getValue("fromdate", date("Y-m-d", strtotime("-169day", time())));
        $todate = XRequest::getValue("todate", date("Y-m-d", time()));

        $medicine_break_date = XRequest::getValue("medicine_break_date", "");

        $patient_type = XRequest::getValue("patient_type", "all");
        $pos = XRequest::getValue("pos", 0);
        $state = XRequest::getValue("state", "all");
        $daycnt = XRequest::getValue("daycnt", - 1);

        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);

        // 微信端通过传wxpatientid展示后台页
        $wxpatientid = XRequest::getValue("wxpatientid", 0);
        if ($wxpatientid) {
            $patient = Patient::getById($wxpatientid);
            $mydisease = $patient->disease;
        } else {
            $mydisease = $this->mydisease;
            if (false == $mydisease instanceof Disease) {
                $mydisease = Disease::getById(1);
            }
        }

        $sql_part = " where 1 = 1 ";
        $bind = [];
        $cond = "";

        if (1 == $mydisease->id) {
            $sql_part = " left join patientdrugstates c on c.patientid = a.id where 1 = 1 ";
            $bind = [];
            $cond = "";

            if ($fromdate) {
                $cond .= " and a.createtime >= :fromdate ";
                $bind[':fromdate'] = $fromdate;
            }

            if ($todate) {
                $cond .= " and a.createtime <= :todate ";
                $bind[':todate'] = date("Y-m-d", strtotime($todate) + 86400);
            }

            if ($daycnt > - 1) {
                $cond .= " and datediff(now(), a.createtime) = :daycnt ";
                $bind[':daycnt'] = $daycnt;
            }

            // 首次电话optasktpl
            $optasktpl_firstTel = OpTaskTplDao::getOneByUnicode('firstTel:audit');

            // 判断患者类别
            if ("ishezuo" == $patient_type) {
                $cond .= " and a.id in (select patientid from patient_hezuos where status=1) ";
            }
            if ("maybeinhezuo" == $patient_type) {
                $cond .= " and a.id in (select patientid from optasks where status=0 and optasktplid = {$optasktpl_firstTel->id} and level=5) ";
            }
            if ("nothezuo" == $patient_type) {
                $cond .= " and a.id not in (select patientid from patient_hezuos where status=1)
                    and a.id not in (select patientid from optasks where status=0 and optasktplid = {$optasktpl_firstTel->id} and level=5) ";
            }

            // 所在阶段
            if ($pos > 0) {
                $cond .= " and c.pos = :pos ";
                $bind[':pos'] = $pos;
            }

            // 判断用药情况
            if ("all" != $state) {
                $cond .= " and c.state = :state ";
                $bind[':state'] = $state;
            }
        }

        if ($doctor_name) {
            $bind = [];
            $cond = "";
            $cond .= " and b.name like :doctor_name and x.status = 1 ";
            $bind[':doctor_name'] = "%{$doctor_name}%";
        }

        $sql = "select distinct a.*
            from patients a
            inner join pcards x on x.patientid=a.id
            inner join doctors b on b.id = x.doctorid
            {$sql_part}";

        // 搜名字时,上面条件忽略
        if ($keyword) {
            $sql = "select distinct a.*
                from patients a
                inner join pcards x on x.patientid=a.id
                inner join xpatientindexs xpi on a.id = xpi.patientid
                where 1 = 1  ";

            // 重置 $bind
            $bind = [];

            if (XPatientIndex::isEqual($keyword)) {
                $cond = " and xpi.word = :word ";
                $bind[':word'] = "{$keyword}";
            } else {
                $cond = " and (xpi.word like :word or xpi.patientid = :patientid) ";
                $bind[':word'] = "%{$keyword}%";
                $bind[':patientid'] = $keyword;
            }
        } else {
            $cond .= " and a.subscribe_cnt > 0 and a.is_test = 0";
        }

        if ('' != $medicine_break_date) {
            $sql = "select distinct a.*
                from patients a
                inner join pcards x on x.patientid=a.id
                where 1 = 1  ";

            // 重置 $bind
            $bind = [];
            $cond = " and a.medicine_break_date = :medicine_break_date ";
            $bind[':medicine_break_date'] = "{$medicine_break_date}";
        }

        // 疾病筛选
        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond .= " and x.diseaseid in ($diseaseidstr) ";

        $cond .= " and a.status = 1 order by x.has_update desc, a.lastpipe_createtime desc ";

        $sql .= $cond;

        $patients = Dao::loadEntityList4Page("Patient", $sql, $pagesize, $pagenum, $bind);

        // 翻页begin
        $countSql = "select count(distinct a.id)
            from patients a
            inner join pcards x on x.patientid=a.id
            inner join xpatientindexs xpi on a.id = xpi.patientid
            inner join doctors b on b.id = x.doctorid {$sql_part} {$cond}";
        $cnt = Dao::queryValue($countSql, $bind);

        $url = "/patientmgr/list?keyword={$keyword}&doctor_name={$doctor_name}&fromdate={$fromdate}&todate={$todate}&medicine_break_date={$medicine_break_date}&patient_type={$patient_type}&pos={$pos}&state={$state}&daycnt={$daycnt}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        // 翻页end

        XContext::setValue("patient_type", $patient_type);
        XContext::setValue("pos", $pos);
        XContext::setValue("state", $state);
        XContext::setValue("daycnt", $daycnt);

        XContext::setValue("keyword", $keyword);
        XContext::setValue("doctor_name", $doctor_name);
        XContext::setValue("fromdate", $fromdate);
        XContext::setValue("todate", $todate);
        XContext::setValue("medicine_break_date", $medicine_break_date);
        XContext::setValue("patients", $patients);
        XContext::setValue("wxpatientid", $wxpatientid);

        // 获取跟进任务模板
        if ($mydisease->id == 1) {
            $optasktpls = OpTaskTplDao::getList_ADHD(" and code = 'follow' and status = 1 ");
        } else {
            $optasktpls = OpTaskTplDao::getList_NotADHD(" and code = 'follow' and status = 1 ");
        }
        XContext::setValue("optasktpls", $optasktpls);

        // 获取筛选分类
        $arr_filter = PipeTplService::getArrForFilter();
        XContext::setValue("arr_filter", $arr_filter);

        return self::SUCCESS;
    }

    public function doOne () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "patient is null");

        $pcard = $patient->getMasterPcard();
        DBC::requireNotEmpty($pcard, "pcard is null");

        $optasks = OpTaskDao::getListByPaitentStatus($patient, 0);
        $shopOrders = ShopOrderDao::getIsPayShopOrdersByPatientType($patient, ShopOrder::type_chufang);
        $papers = PaperDao::getListByPatientid($patientid);
        $patient_status_logs = Patient_Status_LogDao::getListByPatientid($patientid);

        XContext::setValue("patient", $patient);
        XContext::setValue("pcard", $pcard);
        XContext::setValue("optasks", $optasks);
        XContext::setValue("shopOrders", $shopOrders);
        XContext::setValue("papers", $papers);
        XContext::setValue('patient_status_logs', $patient_status_logs);
        return self::SUCCESS;
    }

    public function doPipesChart () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "patient is null");

        XContext::setValue("patient", $patient);
        return self::SUCCESS;
    }

    public function doGetPipesChartJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $sql = "select
            b.title as title,
            a.subdomain as subdomain,
            count(a.id) as cnt
            from pipes a
            inner join pipetpls b on b.id=a.pipetplid
            where a.patientid = :patientid group by a.pipetplid, a.subdomain";

        $bind = [];
        $bind[":patientid"] = $patientid;

        $arr = Dao::queryRows($sql, $bind);
        $subdomain_arr = [
            '' => '脚本',
            'admin' => '医生',
            'api' => '患者移动端',
            'audit' => '运营',
            'dapi' => 'pad端',
            'doctor' => '医生',
            'doctordb' => '医生数据库',
            'dwx' => '医生管理端',
            'hezuo' => '礼来合作端',
            'ipad' => 'pad端H5',
            'wx' => '患者微信端',
        ];
        $data = [];

        foreach ($arr as $k => $v) {
            $data[$k]["key"] = isset($subdomain_arr[$v['subdomain']]) ? $v['title'].'('.$subdomain_arr[$v['subdomain']].')' : $v['title'];
            $data[$k]["value"] = $v['cnt'];
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 方寸儿童管理服务平台患者用药详情
    public function doDrugDetail () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        if ($patient->diseaseid != 1) {
            XContext::setJumpPath("/patientmedicinetargetmgr/detailofpatient/?patientid=" . $patientid);
            return self::SUCCESS;
        }
        $patientmedicinerefs = array();
        $drugdata = array();
        if ($patient instanceof Patient) {
            $patientmedicinerefs = PatientMedicineRefDao::getAllListByPatient($patient);
            foreach ($patientmedicinerefs as $a) {
                $drugdata[$a->medicine->name] = $this->getDrugItemArr($a);
            }
        }
        $drugsheets = DrugSheetDao::getListByPatientid($patientid, " order by id desc ");

        $drug_frequency_arr = Medicine::get_drug_frequency_Arr_define();
        XContext::setValue('patient', $patient);
        XContext::setValue('patientmedicinerefs', $patientmedicinerefs);
        XContext::setValue('drugdata', $drugdata);
        XContext::setValue('drugsheets', $drugsheets);
        XContext::setValue("drug_frequency_arr", $drug_frequency_arr);
        return self::SUCCESS;
    }

    private function getDrugItemArr ($patientmedicineref) {
        $arr = array();
        $patientid = $patientmedicineref->patientid;
        $medicineid = $patientmedicineref->medicineid;
        $drugitems = DrugItemDao::getListByPatientidMedicineid($patientid, $medicineid);
        $drugitems = array_reverse($drugitems);
        if (count($drugitems) == 0) {
            return $arr;
        }
        $current_drugitem = array_shift($drugitems);
        $temp = array();
        $temp["item"] = $current_drugitem;
        $temp["keypoint"] = 1;
        $arr[] = $temp;

        foreach ($drugitems as $a) {
            $is_change = $this->drugItemIsChange($current_drugitem, $a);
            $temp = array();
            $temp["item"] = $a;
            $temp["keypoint"] = $is_change == true ? 1 : 0;
            $arr[] = $temp;
            if ($is_change) {
                $current_drugitem = $a;
            }
        }
        return $arr;
    }

    private function drugItemIsChange ($current_drugitem, $drugitem) {
        $value1 = $current_drugitem->value;
        $value2 = $drugitem->value;
        if ($value1 != $value2) {
            return true;
        }

        $drug_dose1 = $current_drugitem->drug_dose;
        $drug_dose2 = $drugitem->drug_dose;
        if ($drug_dose1 != $drug_dose2) {
            return true;
        }

        $drug_dose1 = $current_drugitem->drug_dose;
        $drug_dose2 = $drugitem->drug_dose;
        if ($drug_dose1 != $drug_dose2) {
            return true;
        }
        return false;
    }

    // 用药记录添加
    public function doDrugItemAddHtml () {
        $patientid = XRequest::getValue("patientid", 0);
        $medicineid = XRequest::getValue("medicineid", 0);
        $drug_frequency_arr = Medicine::get_drug_frequency_Arr_define();

        $patient = Patient::getById($patientid);
        $medicine = Medicine::getById($medicineid);
        $patientmedicineref = PatientMedicineRefDao::getByPatientMedicine($patient, $medicine);
        XContext::setValue('patient', $patient);
        XContext::setValue('medicine', $medicine);
        XContext::setValue('patientmedicineref', $patientmedicineref);
        XContext::setValue("drug_frequency_arr", $drug_frequency_arr);
        return self::SUCCESS;
    }

    // 停药操作页面
    public function doDrugStopHtml () {
        $patientid = XRequest::getValue("patientid", 0);
        $medicineid = XRequest::getValue("medicineid", 0);

        $patient = Patient::getById($patientid);
        $medicine = Medicine::getById($medicineid);
        $patientmedicineref = PatientMedicineRefDao::getByPatientidMedicineid($patientid, $medicineid);
        XContext::setValue('patient', $patient);
        XContext::setValue('medicine', $medicine);
        XContext::setValue('patientmedicineref', $patientmedicineref);
        return self::SUCCESS;
    }

    // 添加新的用药或用药记录
    public function doAddDrugItemJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $medicineid = XRequest::getValue("medicineid", 0);

        $first_start_date = XRequest::getValue("first_start_date", '');
        $record_date = XRequest::getValue("record_date", date("Y-m-d"));
        $content = XRequest::getValue("content", "");
        $value = XRequest::getValue("value", 0);
        $drug_dose = XRequest::getValue("drug_dose", "");
        $drug_frequency = XRequest::getValue("drug_frequency", "");

        $myauditor = $this->myauditor;
        $patient = Patient::getById($patientid);
        $medicine = Medicine::getById($medicineid);
        if ($medicine instanceof Medicine && $patient instanceof Patient) {
            $fiveIds = $patient->get5id();
            // 如果没有drugsheet则创建
            $thedate = date("Y-m-d");
            $drugsheet = DrugSheetDao::getOneByPatientidThedate($patientid, $thedate);
            if (false == $drugsheet instanceof DrugSheet) {
                $row = array();
                $row["thedate"] = $thedate;
                $row["auditorid"] = $myauditor->id;
                $row += $fiveIds;
                $drugsheet = DrugSheet::createByBiz($row);
                $pipe = Pipe::createByEntity($drugsheet);
            }

            // 创建drugitem
            $row = array();
            $row['drugsheetid'] = $drugsheet->id;
            $row['medicineid'] = $medicine->id;
            $row['type'] = 1;
            $row['value'] = $value;
            $row['drug_dose'] = $drug_dose;
            $row['record_date'] = $record_date;
            $row['auditorid'] = $myauditor->id;
            $row["content"] = $content;
            $row["drug_frequency"] = $drug_frequency;
            $row += $fiveIds;
            $drugitem = DrugItem::createByBiz($row);
            $drugsheet->is_nodrug = 0;

            $patientmedicineref = $patient->getRefWithMedicine($medicine, true);
            // 新生成的
            if ("0000-00-00" == $patientmedicineref->startdate) {
                $patientmedicineref->startdate = $record_date;
                $patientmedicineref->last_drugchange_date = $record_date;
                $patientmedicineref->wxuserid = $fiveIds["wxuserid"];
                $patientmedicineref->userid = $fiveIds["userid"];
                $patientmedicineref->doctorid = $fiveIds["doctorid"];
                $patientmedicineref->status = 1;
                $patientmedicineref->value = $value;
                $patientmedicineref->drug_dose = $drug_dose;
                $patientmedicineref->drug_frequency = $drug_frequency;
            } else {
                if ($this->mydisease->id > 1 && strtotime($record_date) < strtotime($patientmedicineref->first_start_date)) {
                    $patientmedicineref->first_start_date = $record_date;
                }

                // 填写时间大于等于最后变更时间
                if (strtotime($record_date) >= strtotime($patientmedicineref->last_drugchange_date)) {
                    // 先把状态置成1，应对停药后又新增用药的情况
                    $patientmedicineref->status = 1;
                    $patientmedicineref->last_drugchange_date = $record_date;
                    $patientmedicineref->value = $value;
                    $patientmedicineref->drug_dose = $drug_dose;
                    $patientmedicineref->drug_frequency = $drug_frequency;
                }
            }

            if('' != $first_start_date && $patientmedicineref->isNotFillFirstStartDate()){
                $patientmedicineref->first_start_date = $first_start_date;
            }
        }

        echo "ok";
        return self::BLANK;
    }

    // 停药
    public function doStopDrugJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $medicineid = XRequest::getValue("medicineid", 0);

        $record_date = XRequest::getValue("record_date", date("Y-m-d"));
        $content = XRequest::getValue("content", "");
        $stop_drug_type = XRequest::getValue("stop_drug_type", 0);

        $myauditor = $this->myauditor;
        $patient = Patient::getById($patientid);
        $medicine = Medicine::getById($medicineid);
        if ($medicine instanceof Medicine && $patient instanceof Patient) {
            $fiveIds = $patient->get5id();
            // 如果没有drugsheet则创建
            $thedate = date("Y-m-d");
            $drugsheet = DrugSheetDao::getOneByPatientidThedate($patientid, $thedate);
            if (false == $drugsheet instanceof DrugSheet) {
                $row = array();
                $row["thedate"] = $thedate;
                $row["auditorid"] = $myauditor->id;
                $row += $fiveIds;
                $drugsheet = DrugSheet::createByBiz($row);
                $pipe = Pipe::createByEntity($drugsheet);
            }

            // 创建drugitem
            $row = array();
            $row['drugsheetid'] = $drugsheet->id;
            $row['medicineid'] = $medicine->id;
            $row['type'] = 0;
            $row['value'] = 0;
            $row['drug_dose'] = "";
            $row['record_date'] = $record_date;
            $row['auditorid'] = $myauditor->id;
            $row["content"] = $content;
            $row += $fiveIds;
            $drugitem = DrugItem::createByBiz($row);
            $drugsheet->is_nodrug = 0;

            // 在添加drugitem记录的场景下，一定有了patientmedicineref
            $patientmedicineref = $patient->getRefWithMedicine($medicine);
            // 填写时间大于等于最后变更时间
            if (strtotime($record_date) >= strtotime($patientmedicineref->last_drugchange_date)) {
                // 先把状态置成1，应对停药后又新增用药的情况
                $patientmedicineref->status = 0;
                $patientmedicineref->last_drugchange_date = $record_date;
                if ($content) {
                    $patientmedicineref->remark = $content;
                }
                $patientmedicineref->stop_drug_type = $stop_drug_type;
            }
        }

        echo "ok";
        return self::BLANK;
    }

    // 标记患者为不用药
    public function doNoDrugJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        $myauditor = $this->myauditor;

        if ($patient instanceof Patient) {
            $thedate = date("Y-m-d");
            $drugsheet = DrugSheet::createOrGetDrugSheetByPatient($patient, $thedate);
            $drugsheet->remark = "不服药 | " . $myauditor->name . " | 备注";
            $drugsheet->is_nodrug = 1;
            $drugsheet->auditorid = $myauditor->id;
        }
        echo "ok";
        return self::BLANK;
    }

    public function doSearchForWxMall () {
        $keyword = XRequest::getValue("keyword", '');
        $patients = array();
        if ($keyword) {
            $bind = [];

            if (XPatientIndex::isEqual($keyword)) {
                $condQuery = " xpi.word = :word ";
                $bind[':word'] = "{$keyword}";
            } else {
                $condQuery = " xpi.word like :word ";
                $bind[':word'] = "%{$keyword}%";
            }
            $cond = " and ({$condQuery} or c.nickname like :nickname) and a.status = 1 and a.subscribe_cnt > 0 limit 200";
            $sql = "select distinct a.*
                from patients a
                inner join users b on b.patientid = a.id
                inner join wxusers c on c.userid = b.id
                inner join xpatientindexs xpi on a.id = xpi.patientid
                where 1 = 1 {$cond}";

            $bind[':nickname'] = "%{$keyword}%";
            $patients = Dao::loadEntityList('Patient', $sql, $bind);
        }
        XContext::setValue('patients', $patients);
        XContext::setValue('keyword', $keyword);
        return self::SUCCESS;
    }

    // 获取图片, 为啥放这里?
    public function doShowpicture () {
        $pictureurl = XRequest::getValue('pictureurl', '');
        // echo $pictureurl;exit;
        XContext::setValue('pictureurl', $pictureurl);

        return self::SUCCESS;
    }

    // 患者列表 / 报到列表
    public function dolistcond () {
        // 独立搜索条件
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        $patientid = XRequest::getValue('patientid', 0);
        $patient_name = XRequest::getValue('patient_name', '');

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);
        XContext::setValue('patientid', $patientid);
        XContext::setValue('patient_name', $patient_name);

        $tagid = XRequest::getValue("tagid", '');

        $hospitalid = XRequest::getValue("hospitalid", 0);
        $auditorid_market = XRequest::getValue("auditorid_market", 0);

        $fromdate = XRequest::getValue('fromdate', '0000-00-00');
        $todate = XRequest::getValue('todate', '0000-00-00');

        $woy = XRequest::getValue("woy", 0);
        if ($woy > 0) {
            $fromdate = XDateTime::getDateYmdByWoy($woy);
            $todate = XDateTime::getDateYmdByWoy($woy + 1);
        }

        $sex = XRequest::getValue("sex", - 1);
        $age = XRequest::getValue("age", 0);

        $mobile_placestr = XRequest::getValue('mobile_placestr');
        if ($mobile_placestr) {
            list ($xprovinceid, $xcityid, $xcountyid) = explode('|', $mobile_placestr);
        } else {
            $mobile_place = XRequest::getValue('mobile_place', []);
            $mobile_place = PatientAddressService::fixNull($mobile_place);
            $xprovinceid = $mobile_place['xprovinceid'];
            $xcityid = $mobile_place['xcityid'];
            $xcountyid = $mobile_place['xcountyid'];
        }

        $statustype = XRequest::getValue('statustype', 'all');

        $statusstr = XRequest::getValue('statusstr', 'all');

        $subscribetype = XRequest::getValue('subscribetype', '-1');

        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $hospitals = Dao::getEntityListByCond('Hospital');

        $ids = Doctor::getTestDoctorIdStr();
        $cond = " and id not in ( select id from patients where doctorid in ({$ids}) or doctorid > 10000  ) ";
        $bind = [];

        $url = "/patientmgr/listcond?1=1";

        // 写在上边可以被下面的覆盖
        XContext::setValue('hospitalid', $hospitalid);
        XContext::setValue('auditorid_market', $auditorid_market);
        XContext::setValue('statustype', $statustype);
        XContext::setValue('statusstr', $statusstr);
        XContext::setValue('subscribetype', $subscribetype);

        // 筛选医生
        if ($doctorid) {
            $url .= "&doctorid={$doctorid}";
            // 独立条件
            $cond = " and id in (select patientid from pcards where doctorid=:doctorid ) ";
            $bind[':doctorid'] = $doctorid;

            $doctor = Doctor::getById($doctorid);

            $hospitalid = 0;
            $auditorid_market = 0;

            // 修正回显
            XContext::setValue('hospitalid', $doctor->hospitalid);
            XContext::setValue('auditorid_market', $doctor->auditorid_market);
        }

        // 疾病筛选
        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond .= " and id in ( select patientid from pcards where diseaseid in ($diseaseidstr) ) ";

        // 筛选医院
        if ($hospitalid) {
            $url .= "&hospitalid={$hospitalid}";
            $cond .= " and id in (
                select a.patientid
                from pcards a
                inner join doctors b on b.id = a.doctorid
                where b.hospitalid = :hospitalid
                )";
            $bind[':hospitalid'] = $hospitalid;
        }

        // 筛选市场负责人
        if ($auditorid_market) {
            $url .= "&auditorid_market={$auditorid_market}";

            if ($statusstr == 'market') {
                $cond .= " and id in (
                    select a.patientid
                    from pcards a
                    inner join users b on b.patientid = a.patientid
                    inner join wxusers c on c.userid = b.id
                    inner join doctors d on d.id = a.doctorid
                    where d.auditorid_market = :auditorid_market
                    and ( c.unsubscribe_time = '0000-00-00 00:00:00' or (UNIX_TIMESTAMP(c.unsubscribe_time) - UNIX_TIMESTAMP(a.createtime) >= 86400) )
                    and (b.id < 10000 or b.id > 20000 )
                    and c.wx_ref_code != ''
                    and c.ref_objtype = 'Doctor'
                    ) and doubt_type = 0";
            } else {
                $cond .= " and id in (
                    select a.patientid
                    from pcards a
                    inner join doctors b on b.id = a.doctorid
                    where b.auditorid_market = :auditorid_market
                    )";
            }

            $bind[':auditorid_market'] = $auditorid_market;
        }

        if ($statustype != 'all') {
            $url .= "&statustype={$statustype}";

            $statustypearr = str_split($statustype, 1);

            $cond .= " and status = :status and auditstatus = :auditstatus and doctor_audit_status = :doctor_audit_status and is_live = :is_live ";
            $bind[':status'] = intval($statustypearr[0]);
            $bind[':auditstatus'] = intval($statustypearr[1]);
            $bind[':doctor_audit_status'] = intval($statustypearr[2]);
            $bind[':is_live'] = intval($statustypearr[3]);
        }

        if ($subscribetype != '-1') {
            $url .= "&subscribetype={$subscribetype}";

            if ($subscribetype == 1) {
                $cond .= " and subscribe_cnt > 0 ";
            } else {
                $cond .= " and subscribe_cnt = 0 ";
            }
        }

        // 开始时间
        if ($fromdate != '0000-00-00') {
            $url .= "&fromdate={$fromdate}";
            $cond .= " and left(createtime,10) >= :fromdate ";
            $bind[':fromdate'] = $fromdate;

            XContext::setValue('fromdate', $fromdate);
        }

        // 截至时间
        if ($todate != '0000-00-00') {
            $url .= "&todate={$todate}";
            $cond .= " and left(createtime,10) < :todate ";
            $bind[':todate'] = $todate;

            XContext::setValue('todate', $todate);
        }

        if ($sex >= 0) {
            $url .= "&sex={$sex}";
            $cond .= " and sex=:sex ";
            $bind[':sex'] = $sex;
        }

        // todo 加到一个patient静态方法
        $ages = array();
        $ages["0"] = '全部';
        for ($i = 3; $i < 60; $i ++) {
            $ages["$i"] = $i;
        }

        if ($age > 0) {
            $url .= "&age={$age}";
            $today = date("Y-m-d");
            $from = XDateTime::getNewDate($today, (- 1) * (365) * ($age + 1));
            $to = XDateTime::getNewDate($today, (- 1) * (365) * $age);
            $cond .= " and birthday between :from and :to ";
            $bind[':from'] = $from;
            $bind[':to'] = $to;
        }

        $addressCond = "";
        if ($xprovinceid) {
            $addressCond .= " and xprovinceid = :xprovinceid ";
            $bind[':xprovinceid'] = $xprovinceid;
        }
        if ($xcityid) {
            $addressCond .= " and xcityid = :xcityid ";
            $bind[':xcityid'] = $xcityid;
        }
        if ($xcountyid) {
            $addressCond .= " and xcountyid = :xcountyid ";
            $bind[':xcountyid'] = $xcountyid;
        }
        if ($addressCond) {
            $cond .= " and id in (
                select patientid
                from patientaddresss
                where 1 = 1 {$addressCond} and type = 'mobile_place'
            ) ";
        }
        $mobile_placestr = "{$xprovinceid}|{$xcityid}|{$xcountyid}";
        $url .= "&mobile_placestr={$mobile_placestr}";

        $url .= "&statusstr={$statusstr}";
        if ($statusstr == 'deleted') {
            $cond .= " and status = 0 ";
        } elseif ($statusstr == 'effective') {
            $cond .= " and status = 1 and subscribe_cnt > 0 ";
        } elseif ($statusstr == 'market') {
            $cond .= " and status = 1 ";
        }

        // tag筛选治疗阶段
        $tagEntitys = TagDao::getListByTypestr('treatmentPhase');
        $tags = [];
        $tags[0] = '未选择';
        foreach ($tagEntitys as $tagEntity) {
            $tags[$tagEntity->id] = $tagEntity->name;
        }

        XContext::setValue('tags', $tags);
        if ($tagid) {
            $cond .= " AND id IN ( SELECT a.id FROM patients a
                INNER JOIN tagrefs b ON a.id=b.objid
                INNER JOIN tags c ON b.tagid = c.id
            WHERE b.objtype='Patient' AND c.typestr = 'treatmentPhase' AND c.id=:tagid )";
            $bind[':tagid'] = $tagid;
            XContext::setValue('tagid', $tagid);
        }

        // 名称筛选
        if ($patient_name) {
            $bind = [];
            $url .= "&keyword=" . urlencode($patient_name);

            if (XPatientIndex::isEqual($patient_name)) {
                $condQuery = " and word = :word ";
                $bind[':word'] = "{$patient_name}";
            } else {
                $condQuery = " and word like :word ";
                $bind[':word'] = "%{$patient_name}%";
            }

            $cond = " and status = 1 and id in (
                select patientid
                from xpatientindexs
                where 1 = 1 {$condQuery}
                )";
        }

        $cond .= " order by id desc ";
        $patients = Dao::getEntityListByCond4Page("Patient", $pagesize, $pagenum, $cond, $bind);

        // 翻页begin
        $countSql = "select count(*) as cnt from patients where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        // 翻页end

        XContext::setValue("keyword", $keyword);
        XContext::setValue("sex", $sex);
        XContext::setValue("age", $age);
        XContext::setValue("ages", $ages);

        XContext::setValue('hospitals', $hospitals);
        XContext::setValue('patients', $patients);

        XContext::setValue('xprovinceid', $xprovinceid);
        XContext::setValue('xcityid', $xcityid);
        XContext::setValue('xcountyid', $xcountyid);

        $myauditor = $this->myauditor;
        if ($myauditor->isOnlyOneRole('market')) {
            return 'market';
        }
        return self::SUCCESS;
    }

    // ADHD下疑似无效患者列表
    public function dolistForDoubt () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $fromdate = XRequest::getValue("fromdate", "");
        $todate = XRequest::getValue("todate", "");
        $doctorid = XRequest::getValue("doctorid", 0);
        $auditorid_market = XRequest::getValue("auditorid_market", 0);

        $cond = " AND a.status=1 ";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond .= " and b.diseaseid in ($diseaseidstr) ";

        if ($fromdate) {
            $cond .= " and left(a.createtime, 10) >= :fromdate ";
            $bind[':fromdate'] = $fromdate;
        }

        if ($todate) {
            $cond .= " and left(a.createtime, 10) <= :todate ";
            $bind[':todate'] = $todate;
        }

        $ordercond = " order by b.createtime desc ";

        $sql = "select distinct a.*
                from patients a
                inner join pcards b on b.patientid = a.id
                where a.doubt_type=1 " . $cond . $ordercond;

        $countSql = "select count(*)
                from patients a
                inner join pcards b on b.patientid = a.id
                where a.doubt_type=1 " . $cond;

        if ($doctorid || $auditorid_market) {
            if ($doctorid) {
                $cond .= " and c.id = :doctorid ";
                $bind[':doctorid'] = $doctorid;
            }

            if ($auditorid_market) {
                $cond .= " and c.auditorid_market = :auditorid_market ";
                $bind[':auditorid_market'] = $auditorid_market;
            }

            $sql = "select distinct a.*
                from patients a
                inner join pcards b on b.patientid = a.id
                inner join doctors c on c.id = a.doctorid
                where a.doubt_type=1 " . $cond . $ordercond;

            $countSql = "select count(*)
                from patients a
                inner join pcards b on b.patientid = a.id
                inner join doctors c on c.id = b.doctorid
                where a.doubt_type=1 " . $cond;
        }
        $patients = Dao::loadEntityList4Page('Patient', $sql, $pagesize, $pagenum, $bind);

        // 翻页begin
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/patientmgr/listfordoubt?fromdate={$fromdate}&todate={$todate}&doctorid={$doctorid}&auditorid_market={$auditorid_market}";

        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        // 翻页end

        $arr = $this->getDoubtArrBy($fromdate, $todate);

        XContext::setValue('fromdate', $fromdate);
        XContext::setValue('todate', $todate);
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('auditorid_market', $auditorid_market);
        XContext::setValue('patients', $patients);
        XContext::setValue('arr', $arr);

        return self::SUCCESS;
    }

    private function getDoubtArrBy ($fromdate, $todate) {
        $cond = '';
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond .= " and b.diseaseid in ($diseaseidstr) ";

        if ($fromdate) {
            $cond .= " AND left(a.createtime, 10) >= :fromdate ";
            $bind[':fromdate'] = $fromdate;
        }

        if ($todate) {
            $cond .= " AND left(a.createtime, 10) <= :todate ";
            $bind[':todate'] = $todate;
        }

        $sql = "select d.name as name, count(*) as cnt
            from patients a
            inner join pcards b on b.patientid = a.id AND b.doctorid = a.first_doctorid
            inner join doctors c on c.id = a.first_doctorid
            inner join auditors d on d.id = c.auditorid_market
            where a.doubt_type=1 AND a.status=1 {$cond}
            GROUP BY c.auditorid_market";

        $temp = Dao::queryRows($sql, $bind);
        $arr = array();
        $arr[0] = array();
        $arr[1] = array();

        foreach ($temp as $k => $v) {
            $arr[0][$k] = $v["name"];
            $arr[1][$k] = $v["cnt"];
        }

        return $arr;
    }

    // 孤岛患者列表
    public function dolistNoUser () {
        $sql = "select distinct a.*
            from patients a
            inner join pcards x on x.patientid = a.id
            left join users b on b.patientid=a.id
            where b.id is null and ( a.diseaseid=1 or (a.diseaseid>1 and a.status=0) )  ";

        $patients = Dao::loadEntityList('Patient', $sql, []);

        XContext::setValue('patients', $patients);

        return self::SUCCESS;
    }

    // 迁移至历史表 (单条)
    public function doMvToPatientHistoryPost () {
        $patientid = XRequest::getValue("patientid", 0);

        $sql = self::genInsertSql_history('Pcard');
        $sql .= " \n ";
        $sql .= self::genSelectSql('Pcard', 'a');

        $sql .= " \n inner join patients b on a.patientid=b.id
            left join users c on c.patientid=b.id
            where c.id is null and b.id in ({$patientid}) ";

        Dao::executeNoQuery($sql);

        $sql = "delete a.*
            from pcards a
            inner join patients b on a.patientid=b.id
            left join users c on c.patientid=b.id
            where c.id is null and b.id in ({$patientid}) ";

        Dao::executeNoQuery($sql);

        $sql = self::genInsertSql_history('Patient');
        $sql .= " \n ";
        $sql .= self::genSelectSql('Patient', 'a');

        $sql .= " \n left join users b on b.patientid=a.id
            where b.id is null and a.id in ({$patientid}) ";

        Dao::executeNoQuery($sql);

        $sql = "delete a.*
            from patients a
            left join users b on b.patientid=a.id
            where b.id is null and a.id in ({$patientid}) ";

        Dao::executeNoQuery($sql);

        XContext::setJumpPath("/patientmgr/listNoUser");

        return self::SUCCESS;
    }

    // 迁移至历史表 (测试)
    public function doTestPatientMvToPatientHistoryPost () {
        $sql = self::genInsertSql_history('Pcard');
        $sql .= " \n ";
        $sql .= self::genSelectSql('Pcard', 'a');

        $sql .= " \n inner join patients b on b.id = a.patientid
            left join users c on c.patientid=b.id
            where c.id is null and b.name like '%测试%' ";

        Dao::executeNoQuery($sql);

        $sql = "delete a.*
            from pcards a
            inner join patients b on a.patientid=b.id
            left join users c on c.patientid=b.id
            where c.id is null and b.name like '%测试%' ";

        Dao::executeNoQuery($sql);

        $sql = self::genInsertSql_history('Patient');
        $sql .= " \n ";
        $sql .= self::genSelectSql('Patient', 'a');

        $sql .= " \n left join users b on b.patientid=a.id
            where b.id is null and a.name like '%测试%' ";

        Dao::executeNoQuery($sql);

        $sql = "delete a.*
            from patients a
            left join users b on b.patientid=a.id
            where b.id is null and a.name like '%测试%' ";

        Dao::executeNoQuery($sql);

        XContext::setJumpPath("/patientmgr/listNoUser");

        return self::SUCCESS;
    }

    // 设置患者为测试
    public function doSetPatientTestJson () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "患者不存在!!!");

        if ($patient->is_test == 0) {
            $patient->is_test = 1;
            echo "成功设置为测试患者!";
        } else {
            echo "患者原来就是测试患者，设置失败!";
        }

        return self::BLANK;
    }

    // 设置患者为非测试
    public function doSetPatientNormalJson () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "患者不存在!!!");

        if ($patient->is_test == 1) {
            $patient->is_test = 0;
            echo "成功设置为正常患者!";
        } else {
            echo "患者原来就是正常患者，设置失败!";
        }

        return self::BLANK;
    }

    // genInsertSql 生成 insert 语句
    private static function genInsertSql_history ($entityName = 'Patient') {
        $arr1 = [
            'id',
            'version',
            'createtime',
            'updatetime'];

        $arr2 = $entityName::getKeysDefine();
        $arr = array_merge($arr1, $arr2);

        $columns = [];
        foreach ($arr as $column) {
            $columns[] = "`$column`";
        }
        $columns = implode(", ", $columns);

        $table = strtolower($entityName) . 'historys';

        return $sql = " insert into $table ($columns) ";
    }

    // genSelectSql 生成 select 语句
    private static function genSelectSql ($entityName = 'Patient', $alias = '') {
        $arr1 = [
            'id',
            'version',
            'createtime',
            'updatetime'];

        $arr2 = $entityName::getKeysDefine();
        $arr = array_merge($arr1, $arr2);

        $columns = [];
        foreach ($arr as $column) {
            if ($alias) {
                $column = "$alias.`$column`";
            }
            $columns[] = $column;
        }
        $columns = implode(", ", $columns);

        $table = strtolower($entityName) . 's';

        return $sql = " select $columns from $table $alias ";
    }

    // ####################################
    // 专门管理患者信息新页面的函数 by xuzhe
    // ####################################

    // doChartHtml
    public function doChartHtml () {
        $patientid = XRequest::getValue("patientid", 0);
        $writer = XRequest::getValue("writer", "all");

        $patient = Patient::getById($patientid);

        $conners = PaperToEchartsService::getConnersChartData($patientid);

        $writers = PaperDao::getWritersByPatientid($patientid);

        XContext::setValue("patientid", $patientid);
        XContext::setValue("writer", $writer);
        XContext::setValue("writers", $writers);
        XContext::setValue("conners", $conners);
        return self::SUCCESS;
    }

    // doPatientBaseHtml
    public function doPatientBaseHtml () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);

        XContext::setValue("patient", $patient);

        $disease = $patient->disease;

        DBC::requireNotEmpty($disease, "[{$patient->name}][{$patient->id}]的diseaseid为0,请运营及时查看");

        $papertpls = $disease->getPaperTpls();
        XContext::setValue("papertpls", $papertpls);

        $patientpgrouprefs = PatientPgroupRefDao::getListByPatientid($patientid);
        $pgroups_manage = PgroupDao::getListByDiseaseid($disease->id, " and showinaudit=1 and typestr= 'manage' ");
        $pgroups_lab = PgroupDao::getListByDiseaseid($disease->id, " and showinaudit=1 and typestr= 'lab' ");
        XContext::setValue("patientpgrouprefs", $patientpgrouprefs);
        XContext::setValue("pgroups_manage", $pgroups_manage);
        XContext::setValue("pgroups_lab", $pgroups_lab);

        $drugsheet_nearly2 = DrugSheetDao::getListByPatientid($patientid, " order by id desc limit 2");
        XContext::setValue("drugsheet_nearly2", $drugsheet_nearly2);

        $shopOrders = ShopOrderDao::getIsPayShopOrdersByPatientType($patient, ShopOrder::type_chufang);
        XContext::setValue("shopOrders", $shopOrders);

        return self::SUCCESS;
    }

    // 变更医生
    public function doChangeDoctor () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        XContext::setValue("patient", $patient);
        return self::SUCCESS;
    }

    public function doChangeDoctorPost () {
        $patientid = XRequest::getValue("patientid", 0);
        $to_doctorid = XRequest::getValue("to_doctorid", 0);
        $patient = Patient::getById($patientid);
        $doctor = Doctor::getById($to_doctorid);
        if ($patient instanceof Patient && $doctor instanceof Doctor) {
            $pcard = $patient->getPcardByDoctorid($patient->doctorid);
            $createuser = $patient->createuser;
            $wxuser = $createuser->createwxuser;

            $pcard->set4lock("doctorid", $to_doctorid);
            $patient->set4lock("doctorid", $to_doctorid);
            // $patient->first_doctorid = $to_doctorid;
            $wxuser->doctorid = $to_doctorid;
            if ($wxuser->ref_pcode == "DoctorCard") {
                $wxuser->ref_objid = $to_doctorid;
                $wxuser->wx_ref_code = $doctor->code;
            }
        }
        XContext::setJumpPath("/usermgr/listforpatient?preMsg=" . urlencode('变更成功'));
        return self::SUCCESS;
    }

    public function doModifyPatientGroupJson () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "patient is null");

        $patientgroupid = XRequest::getValue('patientgroupid', 0);
        if ($patient->patientgroupid != $patientgroupid) {
            if (Disease::isNMO($patient->diseaseid)) {
                if ($patient->patientgroupid == 7) {
                    // 删除未发送的定时消息
                    $plan_txtmsgs = Plan_txtMsgDao::getUnsentListByPatientidCode($patient->id, 'nmo_btl');
                    foreach ($plan_txtmsgs as $plan_txtmsg) {
                        $plan_txtmsg->remove();
                    }
                }
            }

            if ($patientgroupid == 0) {
                $content = "【修改了 [患者分组] [{$patient->patientgroup->title}] => [无分组]】<br>";

                $patient->patientgroupid = $patientgroupid;
            } else {
                $patientgroup = PatientGroup::getById($patientgroupid);

                if ($patient->patientgroup instanceof PatientGroup) {
                    $content = "【修改了 [患者分组] [{$patient->patientgroup->title}] => [{$patientgroup->title}]】<br>";
                } else {
                    $content = "【修改了 [患者分组] [无分组] => [{$patientgroup->title}]】<br>";
                }

                DBC::requireNotEmpty($patientgroup, "patientgroup is null");
                $patient->patientgroupid = $patientgroup->id;

                // #6035 NMO新加入『倍泰龙组』的患者，入组即发送『药物治疗满意度评分』量表。
                $papertpl = PaperTpl::getById(599386386);
                if ($papertpl instanceof PaperTpl && $patientgroupid == PatientGroup::beitailongid) {
                    $wx_uri = Config::getConfig("wx_uri");
                    $url = "{$wx_uri}/paper/wenzhen/?papertplid={$papertpl->id}";

                    $first = array(
                        "value" => "药物治疗满意度评分",
                        "color" => "#ff6600");
                    $keywords = array(
                        array(
                            "value" => $patient->name,
                            "color" => "#aaa"),
                        array(
                            "value" => date("Y-m-d H:i:s"),
                            "color" => "#aaa"),
                        array(
                            "value" => '您好，注射倍泰龙期间医生需要关注您的使用情况以及药品疗效，故注射期间您需要每个月填写一次【药物治疗满意度评分】量表，以便医生关注您的治疗情况。',
                            "color" => "#ff6600"));
                    $content = WxTemplateService::createTemplateContent($first, $keywords);

                    PushMsgService::sendTplMsgToPatientBySystem($patient, 'followupNotice', $content, $url);

                    // 6037 NMO『倍泰龙组』的患者每月发送一次『药物治疗满意度评分』量表。
                    $row = [];
                    $row["patientid"] = $patientid;
                    $row["auditorid"] = $this->myauditor->id;
                    $row["objtype"] = 'Patient';
                    $row["objid"] = $patient->id;
                    $row["type"] = 1;
                    $row["code"] = 'nmo_btl';
                    $row["plan_send_time"] = date('Y-m-d', strtotime("+1 months"));
                    Plan_txtMsg::createByBiz($row);
                }
            }

            // 异步创建运营操作日志
            $row = [
                'auditorid' => $this->myauditor->id,
                'patientid' => $patient->id,
                'code' => 'patientgroup',
                'content' => $content
            ];
            AuditorOpLog::nsqPush($row);
        }

        echo 'ok';

        return self::BLANK;
    }

    public function doModifyPatientStageJson () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "patient is null");

        $patientstageid = XRequest::getValue('patientstageid', 0);
        if ($patient->patientstageid != $patientstageid) {
            if ($patientstageid == 0) {
                $content = "【修改了 [患者阶段] [{$patient->patientstage->title}] => [无阶段]】<br>";

                $patient->patientstageid = $patientstageid;
            } else {
                $patientstage = PatientStage::getById($patientstageid);

                if ($patient->patientstage instanceof PatientStage) {
                    $content = "【修改了 [患者阶段] [{$patient->patientstage->title}] => [{$patientstage->title}]】<br>";
                } else {
                    $content = "【修改了 [患者阶段] [无阶段] => [{$patientstage->title}]】<br>";
                }

                DBC::requireNotEmpty($patientstage, "patientstage is null");
                $patient->patientstageid = $patientstage->id;
            }

            // 异步创建运营操作日志
            $row = [
                'auditorid' => $this->myauditor->id,
                'patientid' => $patient->id,
                'code' => 'patientstage',
                'content' => $content
            ];
            AuditorOpLog::nsqPush($row);
        }

        echo 'ok';

        return self::BLANK;
    }

    // 审核通过,发消息,跳到关联患者（auditor_pass）
    public function doPassPost () {
        $patientid = XRequest::getValue("patientid", 0);
        $this->doPassJson();
        XContext::setJumpPath("/patientmgr/list4bind?patientid={$patientid}&preMsg=" . urlencode('审核通过'));
        return self::SUCCESS;
    }

    // 患者报到审核: 通过（auditor_pass）
    public function doPassJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "Patient为空");
        $myauditor = $this->myauditor;
        // 运营通过
        PatientStatusService::auditor_pass($patient, $myauditor);
        $patient->audittime = XDateTime::now();
        Pipe::createByEntity($patient, "pass");

        $this->joinWxGroup($patient);
        // 先改成通知一个有效的wxuser
        $wxuser = $patient->getMasterWxUser();
        if ($wxuser instanceof WxUser) {
            $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'register_pass');
            if ($patient->doctor->isHezuo("Lilly")) {
                $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_register_pass_byAuditor');
            }
            PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $myauditor, $content);
        }
        echo "ok";

        return self::BLANK;
    }

    // 审核拒绝,发消息,跳到关联患者（auditor_refuse）
    public function doRefusePost () {
        $patientid = XRequest::getValue("patientid", 0);
        $this->refuseImp(false);
        XContext::setJumpPath("/patientmgr/list4bind?patientid={$patientid}&preMsg=" . urlencode('审核拒绝'));
        return self::SUCCESS;
    }

    // 报到审核拒绝 （auditor_refuse）
    public function doRefuseJson () {
        return $this->refuseImp(true);
    }

    // refuseImp
    private function refuseImp ($isSendMsg = true) {
        $patientid = XRequest::getValue("patientid", 0);
        $content = XRequest::getValue('content', '');
        $myauditor = $this->myauditor;

        $patient = Patient::getById($patientid);

        $auditremark = $patient->auditremark;
        $patient->auditremark = $auditremark . "\n" . $content;

        // 运营拒绝
        PatientStatusService::auditor_refuse($patient, $myauditor);
        $patient->auditremark = $patient->auditremark . $auditremark;
        $patient->audittime = XDateTime::now();

        Pipe::createByEntity($patient, "refuse");

        if ($isSendMsg) {
            // 先改成通知一个有效的wxuser
            $wxuser = $patient->getMasterWxUser();
            if ($wxuser instanceof WxUser) {
                $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'register_refuse');
                PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $myauditor, $content);
            }
        }

        echo "ok";

        return self::BLANK;
    }

    // 运营上线
    public function doOnlinePost () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        $myauditor = $this->myauditor;
        DBC::requireNotEmpty($patient, "Patient为空");

        $this->joinWxGroup($patient);

        PatientStatusService::auditor_online($patient, $this->myauditor);
        Pipe::createByEntity($patient, "online");

        $wxuser = $patient->getMasterWxUser();
        if ($wxuser instanceof WxUser) {
            if ($patient->doctor->isHezuo("Lilly")) {
                $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_register_pass_byAuditor');
                PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $myauditor, $content);
            }
        }

        XContext::setJumpPath("/patientmgr/list4bind?patientid={$patientid}&preMsg=" . urlencode('已手工上线'));
        return self::SUCCESS;
    }

    // 运营下线
    public function doOfflinePost () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "Patient为空");

        PatientStatusService::auditor_offline($patient, $this->myauditor);
        Pipe::createByEntity($patient, "offline");

        XContext::setJumpPath("/patientmgr/list4bind?patientid={$patientid}&preMsg=" . urlencode('已手工下线'));
        return self::SUCCESS;
    }

    // 把user绑定到其他patient上
    public function doMergePatientPost () {
        $to_patientid = XRequest::getValue("to_patientid", 0);
        DBC::requireNotEmpty($to_patientid, "to_patientid为空");
        $to_patient = Patient::getById($to_patientid);

        $from_userid = XRequest::getValue("from_userid", 0);
        DBC::requireNotEmpty($from_userid, "userid为空");
        $from_user = User::getById($from_userid);
        $myauditor = $this->myauditor;

        $result = PatientMergeService::mergeImp($from_user, $to_patient, $myauditor);
        DBC::requireTrue($result, "当前情况不能合并，有问题请请联系技术");
        XContext::setJumpPath(
                "/patientmgr/list4bind?patientid={$to_patientid}&preMsg=" . urlencode("已合并 from_userid[{$from_userid}] -> to_patientid[{$to_patientid}] "));
        return self::SUCCESS;
    }

    // 加入对应分组
    private function joinWxGroup ($patient) {
        $wxusers = $patient->getWxUsers();
        foreach ($wxusers as $wxuser) {
            // 方寸儿童管理服务平台，非礼来患者加入开药门诊分组
            if (1 == $patient->diseaseid && false == $patient->doctor->isHezuo("Lilly")) {
                $wxuser->joinWxGroupOfADHD();
                continue;
            }
            // 肺癌，胃癌，癌症wxuser报到后加入:报到后的微信组
            $wxuser->joinWxGroup("baodao_after");
        }
    }

    // 开启自动锁定
    public function doAuto_lock_patient_openJson () {
        $this->myauditor->is_auto_lock_patient = 1;

        $output = $this->result;
        XContext::setValue('json', $output);
        return self::TEXTJSON;
    }

    // 关闭自动锁定
    public function doAuto_lock_patient_closeJson () {
        $this->myauditor->is_auto_lock_patient = 0;

        $output = $this->result;
        XContext::setValue('json', $output);
        return self::TEXTJSON;
    }

    // 锁定
    public function doLockJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);

        $errno = 0;
        $errmsg = "";

        // 抢患者
        if ($patient->auditorid > 0 && $patient->auditorid != $this->myauditor->id) {
            $errmsg = "患者[{$patient->name}] 责任人 [{$patient->auditor->name}] => [{$this->myauditor->name}]";
        } else {
            $errmsg = "已锁定患者[{$patient->name}]";
        }

        $patient->auditorid = $this->myauditor->id;
        $patient->auditor_lock_time = date('Y-m-d H:i:s');

        $output = $this->result;
        $output['errno'] = $errno;
        $output['errmsg'] = $errmsg;
        $output['data'] = [];
        $output['data']['auditorid'] = $this->myauditor->id;
        $output['data']['auditor_name'] = $this->myauditor->name;
        $output['data']['lock_title'] = $patient->getLock_titleForAudit($this->myauditor);

        XContext::setValue('json', $output);

        return self::TEXTJSON;
    }

    // 解锁
    public function doUnlockJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);

        $errno = 0;
        $errmsg = "";

        // 只能对自己锁定的解锁
        if ($patient->auditorid == $this->myauditor->id) {
            $patient->auditorid = 0;
            $errmsg = "已解锁患者 [{$patient->name}]";
        } else {
            $errno = 1;
            $errmsg = "解锁失败, 患者[{$patient->name}] 责任人为 [{$patient->auditor->name}]";
        }

        $output = $this->result;
        $output['errno'] = $errno;
        $output['errmsg'] = $errmsg;
        $output['data'] = [];
        $output['data']['auditorid'] = 0;
        $output['data']['auditor_name'] = '无';
        $output['data']['lock_title'] = $patient->getLock_titleForAudit($this->myauditor);
        XContext::setValue('json', $output);

        return self::TEXTJSON;
    }

    // 释放全部的锁定
    public function doUnlock_allJson () {
        $cond = "AND auditorid = :auditorid ";
        $bind = [];
        $bind[':auditorid'] = $this->myauditor->id;

        $patients = Dao::getEntityListByCond('Patient', $cond, $bind);
        foreach ($patients as $a) {
            $a->auditorid = 0;
        }

        $cnt = count($patients);

        $output = $this->result;
        $output['errno'] = 0;
        $output['errmsg'] = "共释放{$cnt}个患者";
        $output['data'] = [];
        $output['data']['cnt'] = $cnt;

        XContext::setValue('json', $output);

        return self::TEXTJSON;
    }

    // 下线患者
    public function doOfflineJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $auditremark = XRequest::getValue("auditremark", '');

        $patient = Patient::getById($patientid);

        if ($patient instanceof Patient) {
            // 人工下线
            PatientStatusService::auditor_offline($patient, $this->myauditor, $auditremark);
        }
        echo 'ok';

        return self::BLANK;
    }

    // 设置患者死亡
    public function doDeadJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $auditremark = XRequest::getValue("auditremark", '');

        $patient = Patient::getById($patientid);

        if ($patient instanceof Patient) {

            // 修改状态
            PatientStatusService::auditor_dead($patient, $this->myauditor, $auditremark);

            // 关闭用药核对以及不良反应监控
            $patient->is_medicine_check = 0;
            $patient->is_adr_monitor = 0;

            // 关闭患者的所有任务
            OpTaskService::closeAllOpTasksOfPatient($patient, $this->myauditor->id);
        }
        echo 'ok';

        return self::BLANK;
    }

    // 患者复活
    public function doReviveJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $auditremark = XRequest::getValue("auditremark", '');

        $patient = Patient::getById($patientid);

        if ($patient instanceof Patient) {
            // 修改状态
            PatientStatusService::reLive($patient, $this->myauditor, $auditremark);
        }
        echo 'ok';

        return self::BLANK;
    }

    // 关联患者审核
    public function doList4Bind () {
        $patientid = XRequest::getValue("patientid", 0);

        $patient = Patient::getById($patientid);
        $patients = PatientDao::getListByName($patient->name);

        XContext::setValue('thePatient', $patient);
        XContext::setValue('patients', $patients);

        return self::SUCCESS;
    }

    // 修改疾病
    public function doModifyDisease () {
        $pcardid = XRequest::getValue('pcardid', 0);
        $pcard = Pcard::getById($pcardid);

        XContext::setValue('pcard', $pcard);

        return self::SUCCESS;
    }

    public function doModifyDiseasePost () {
        $pcardid = XRequest::getValue('pcardid', 0);
        $pcard = Pcard::getById($pcardid);
        $diseaseid = XRequest::getValue('diseaseid', 0);
        DBC::requireNotEmpty($diseaseid, "diseaseid is null");

        $patient = $pcard->patient;

        // 修改任务的diseaseid
        $cond = " and patientid = :patientid and doctorid = :doctorid and diseaseid = :diseaseid ";
        $bind = [
            ':patientid' => $patient->id,
            ':doctorid' => $pcard->doctorid,
            ':diseaseid' => $pcard->diseaseid];
        $optasks = Dao::getEntityListByCond('OpTask', $cond, $bind);
        foreach ($optasks as $optask) {
            $optask->set4lock('diseaseid', $diseaseid);
        }

        // 修改patient中的diseaseid
        if ($patient->doctorid == $pcard->doctorid && $patient->diseaseid == $pcard->diseaseid) {
            $patient->set4lock('diseaseid', $diseaseid);
        }

        // 修改pcard中的diseaseid
        $pcard->set4lock('diseaseid', $diseaseid);

        XContext::setJumpPath("/patientmgr/modifydisease?pcardid=" . $pcardid);
        return self::SUCCESS;
    }

    // 修改页的显示
    public function doModify () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        $pcard = $patient->getMasterPcard();
        $patientaddress = PatientAddressDao::getByTypePatientid('mobile_place', $patient->id);
        $patient_status_logs = Patient_Status_LogDao::getListByPatientid($patientid);

        XContext::setValue("patient", $patient);
        XContext::setValue("pcard", $pcard);
        XContext::setValue("patientaddress", $patientaddress);
        XContext::setValue("patient_status_logs", $patient_status_logs);
        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $patientid = XRequest::getValue("patientid", 0);
        $out_case_no = XRequest::getValue("out_case_no", 0);
        $patientcardno = XRequest::getValue('patientcardno', '');
        $patientcard_id = XRequest::getValue('patientcard_id', '');
        $bingan_no = XRequest::getValue('bingan_no', '');
        $name = XRequest::getValue("name", '');
        $mother_name = XRequest::getValue("mother_name", '');
        $level = XRequest::getValue("level", '');
        $sex = XRequest::getValue("sex", '');
        $birthday = XRequest::getValue("birthday", '0000-00-00');
        $prcrid = XRequest::getValue("prcrid", 0);
        $last_incidence_date = XRequest::getValue("last_incidence_date", '0000-00-00');
        $auditremark = XRequest::getValue("auditremark", '');

        $mobile_place = XRequest::getValue('mobile_place', []);
        $mobile_place = PatientAddressService::fixNull($mobile_place);

        $patient = Patient::getById($patientid);
        $pcard = $patient->getMasterPcard();
        $pcard->out_case_no = $out_case_no;
        $pcard->patientcardno = $patientcardno;
        $pcard->patientcard_id = $patientcard_id;
        $pcard->bingan_no = $bingan_no;
        $patient->name = $name;
        $patient->mother_name = $mother_name;
        $patient->level = $level;
        $patient->sex = $sex;
        $patient->birthday = $birthday;
        $patient->prcrid = $prcrid;

        $pcard->last_incidence_date = $last_incidence_date;

        PatientAddressService::updatePatientAddress($mobile_place, 'mobile_place', $patient->id, false);

        $patient->auditremark = $auditremark;

        // 添加XPatientIndex
        XPatientIndex::updateAllXPatientIndex($patient);

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/patientmgr/modify?patientid=" . $patientid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 测试患者修改页的显示
    public function doTestmodify () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        $hospitals = Dao::getEntityListByCond("Hospital", " AND id not in (5)");

        XContext::setValue('hospitals', $hospitals);
        XContext::setValue("patient", $patient);
        return self::SUCCESS;
    }

    // 修改测试患者提交
    public function doTestModifyPost () {
        $patientid = XRequest::getValue("patientid", 0);
        $doctorid = XRequest::getValue("doctorid", 0);

        $patient = Patient::getById($patientid);
        $doctor = Doctor::getById($doctorid);
        $patient->name = "";
        $patient->set4lock("doctorid", $doctorid);

        // 重新进入审核,相当于自动未通过，进入审核
        PatientStatusService::auto_refuse($patient);

        $pcard = $patient->getMasterPcard();
        $pcard->first_visit_date = "0000-00-00";

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/patientmgr/testmodify?patientid=" . $patientid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 患者留言列表
    public function doListforletter () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $sql = "select distinct a.*
            from patients a
            inner join pcards x on x.patientid=a.id
            where a.status=1 and a.subscribe_cnt>0 and x.diseaseid=1
            order by a.id asc ";
        $patients = Dao::loadEntityList4Page("Patient", $sql, $pagesize, $pagenum, []);
        XContext::setValue("patients", $patients);

        // 翻页begin
        $countSql = "select count( distinct(a.id) )
            from patients a
            inner join pcards x on x.patientid=a.id
            where a.status=1 and a.subscribe_cnt>0 and x.diseaseid=1
            order by a.id asc";
        $cnt = Dao::queryValue($countSql, []);

        $url = "/patientmgr/listforletter";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        // 翻页end

        return self::SUCCESS;
    }

    public function doGetContentForLetterHtml () {
        $patientid = XRequest::getValue("patientid", 0);

        $patient = Patient::getById($patientid);
        $pipes = PipeDao::getAllPipesOfPatient($patient->id, " and objtype in ('WxTxtMsg', 'LessonUserRef', 'PatientNote')");
        $result = array();
        foreach ($pipes as $pipe) {
            $sameContentArr = $this->getSameLetterContentArr($pipe);
            foreach ($sameContentArr as $item) {
                $result[] = $item;
            }
        }
        XContext::setValue("result", $result);
        return self::SUCCESS;
    }

    // 患者基本信息合并列表
    public function doMergeAlreadyList () {
        // 包括孤岛患者
        $sql = "
            select name,count(*) as cnt,
                min(createtime) as min_createtime,
                max(createtime) as max_createtime,
                group_concat(id) as ids,
                group_concat(doctorid) as doctorids,
                group_concat(birthday) as birthdays
            from patients
            where name<>'' and doctorid <> 145
            group by name
            HAVING cnt > 1 order by max_createtime desc";

        // 不包括孤岛患者
        $sql = "select name,count(*) as cnt,
                min(createtime) as min_createtime,
                max(createtime) as max_createtime,
                group_concat(id) as ids,
                group_concat(doctorid) as doctorids,
                group_concat(birthday) as birthdays
            from  (
            select distinct a.*
            from patients a
            inner join users b on b.patientid=a.id
            ) tt  where name<>'' and doctorid <> 145
            group by name
            HAVING cnt > 1 order by max_createtime desc;";

        $repetition_patients = Dao::queryRows($sql, []);
        $patient_cnt = count($repetition_patients);

        XContext::setValue('repetition_patients', $repetition_patients);
        XContext::setValue('patient_cnt', $patient_cnt);

        return self::SUCCESS;
    }

    // 患者基本信息合并
    public function doMergeAlready () {
        $patientids = XRequest::getValue('patientids', '');
        $diff_patientids = XRequest::getValue('diff_patientids', '');

        $idarr = explode(',', $patientids);
        $patients = Dao::getEntityListByIds("Patient", $idarr);

        XContext::setValue('patients', $patients);
        XContext::setValue('patientids', $patientids);
        XContext::setValue('diff_patientids', $diff_patientids);

        return self::SUCCESS;
    }

    public function doMergePost () {
        $patientid = XRequest::getValue('patientid', 0);

        DBC::requireNotEmpty($patientid, 'patientid is null');
        $patient = Patient::getById($patientid);

        $keys = XRequest::getValue('keys', array());
        $values = XRequest::getUnSafeValue('values', array());

        foreach ($keys as $i => $a) {
            $patient->$a = $values[$i];
        }

        echo 'success';
        return self::BLANK;
    }

    public function doDiffPatientHtml () {
        $patientids = XRequest::getValue('patientids', '');

        $idarr = explode(',', $patientids);
        $patientA = Patient::getById($idarr[0]);
        $patientB = Patient::getById($idarr[1]);

        $pcardA = $patientA->getMasterPcard();
        $pcardB = $patientB->getMasterPcard();

        // DBC::requireNotEmpty($pcardA, "pcard is null");
        // DBC::requireNotEmpty($pcardB, "pcard is null");

        if ($pcardA instanceof Pcard) {
            $pcardA->getLastComplication();
        }
        if ($pcardB instanceof Pcard) {
            $pcardB->getLastComplication();
        }

        XContext::setValue('patientA', $patientA);
        XContext::setValue('patientB', $patientB);

        XContext::setValue('pcardA', $pcardA);
        XContext::setValue('pcardB', $pcardB);

        return self::SUCCESS;
    }

    private function getSameLetterContentArr ($pipe) {
        $objtype = $pipe->objtype;
        $obj = $pipe->obj;
        $result = array();
        if (count($obj)) {
            if ($objtype == 'WxTxtMsg' || $objtype == 'PatientNote') {
                $content = $obj->content;
                if ($this->isSameLetter($content)) {
                    $temp = array();
                    $temp['createtime'] = $obj->createtime;
                    $temp['content'] = $content;
                    $temp['objtype'] = $objtype;
                    $temp['pipeid'] = $pipe->id;
                    $result[] = $temp;
                }
            } else {
                $xanswersheets = XAnswerSheet::getListByPatientidObjtypeObjid($pipe->patientid, $objtype, $obj->id);
                foreach ($xanswersheets as $xanswersheet) {
                    $xanswers = XAnswer::getArrayOfXAnswerSheet($xanswersheet);
                    foreach ($xanswers as $xanswer) {
                        if ($this->isSameLetter($xanswer->content)) {
                            $temp = array();
                            $temp['createtime'] = $obj->createtime;
                            $temp['content'] = $xanswer->content;
                            $temp['objtype'] = $objtype;
                            $temp['pipeid'] = 0;
                            $result[] = $temp;
                        }
                    }
                }
            }
        }
        return $result;
    }

    private function isSameLetter ($content) {
        $len = mb_strlen($content, 'utf-8');
        if ($len < 11) {
            return null;
        }
        $regex = '/谢|幸好|多亏/';

        if (preg_match($regex, $content)) {
            return true;
        }
        return null;
    }

    public function doModifyNextpmstimePost () {
        $patientid = XRequest::getValue('patientid', 0);
        $next_pmsheet_time = XRequest::getValue('next_pmsheet_time', '');

        $patient = Patient::getById($patientid);

        $pcard = $patient->getMasterPcard();
        $pcard->next_pmsheet_time = $next_pmsheet_time;

        XContext::setJumpPath("/patientmgr/list?keyword=" . urlencode($patient->name));

        return self::BLANK;
    }

    public function doDeleteNextpmstimePost () {
        $patientid = XRequest::getValue('patientid', 0);

        $patient = Patient::getById($patientid);

        $pcard = $patient->getMasterPcard();
        $pcard->next_pmsheet_time = '0000-00-00 00:00:00';

        XContext::setJumpPath("/patientmgr/list?keyword=" . urlencode($patient->name));

        return self::BLANK;
    }

    // 加减new标记,只有运营后台在用，马上迁移
    public function dochangeNewJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $isnew = XRequest::getValue("isnew", 0);

        $patient = Patient::getById($patientid);

        $pcard = $patient->getMasterPcard();
        $pcard->has_update = $isnew;

        echo 'ok';

        return self::BLANK;
    }

    // doDoubtJson 置无效患者(根据传递的值置状态)
    public function doDoubtJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $doubt_type = XRequest::getValue("doubt_type", 0);
        $content = XRequest::getValue("content", "");

        $patient = Patient::getById($patientid);
        $opsremark = $patient->opsremark;

        $patient->doubt_type = $doubt_type;
        if ($content) {
            $patient->opsremark = $opsremark . "\n" . $content;
        }

        echo 'ok';

        return self::BLANK;
    }

    // doMedicineBreakDateChangeJson 修改患者的药物中断日期
    public function doMedicineBreakDateChangeJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $medicine_break_date = XRequest::getValue("medicine_break_date", "");

        $patient = Patient::getById($patientid);
        $patient->medicine_break_date = $medicine_break_date;

        echo 'ok';
        return self::BLANK;
    }

    public function doGetListCheckCt () {
        $fromdate = XRequest::getValue('fromdate');
        $todate = XRequest::getValue('todate');

        $xquestionsheet = XQuestionSheet::getById(274498306);

        // 没有ename，所有先id写死 274498306
        $sql = "select distinct a.patientid,a.createtime
        from xanswersheets a
        inner join xanswers b on b.xanswersheetid = a.id
        inner join xansweroptionrefs c on c.xanswerid = b.id
        where a.xquestionsheetid = {$xquestionsheet->id} and c.xoptionid in (274501631,274501632) ";

        // 开始日期
        if ($fromdate) {
            $sql .= " and a.createtime >= '{$fromdate} 00:00:00' ";
        }

        // 截至日期
        if ($todate) {
            $sql .= " and a.createtime <= '{$todate} 23:59:59' ";
        }

        $sql .= " ORDER BY a.createtime DESC";

        $rows = Dao::queryRows($sql);

        $list = [];
        if ($rows) {
            foreach ($rows as $row) {
                $patient = Patient::getById($row['patientid']);
                $pcard = $patient->getMasterPcard();

                $arr = [];
                if ($patient instanceof Patient && $pcard instanceof Pcard) {
                    $arr['patient'] = $patient;
                    $arr['pcard'] = $pcard;
                    $arr['createtime'] = $row['createtime'];

                    $list[] = $arr;
                }
            }
        }

        XContext::setValue('list', $list);
        XContext::setValue('fromdate', $fromdate);
        XContext::setValue('todate', $todate);

        return self::SUCCESS;
    }

    // 用户页为echarts对象加载数据
    public function doGet_adhd_dataJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $writer = XRequest::getValue("writer", "all");

        $arr = PaperToEchartsService::getResultOfAdhd_ivForWeb($patientid, 10, $writer);

        echo json_encode($arr, JSON_UNESCAPED_UNICODE);

        return self::BLANK;
    }

    // 添加运营备注
    public function doAddOpsRemarkJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $content = XRequest::getValue("content", "");

        $patient = Patient::getById($patientid);

        if ($patient instanceof Patient && $patient->opsremark != $content) {
            $content_log = "【修改了[患者文本备注] <br>[{$patient->opsremark}]<br> => <br>[{$content}]】<br>";

            $patient->opsremark = $content;

            // 异步记录运营操作日志
            $row = [
                'auditorid' => $this->myauditor->id,
                'patientid' => $patient->id,
                'code' => 'patientremark',
                'content' => $content_log
            ];
            AuditorOpLog::nsqPush($row);
        }

        echo "ok";
        return self::BLANK;
    }

    public function doDoctorlistJson () {
        $hospitalid = XRequest::getValue("hospitalid", 0);

        $hospital = Hospital::getById($hospitalid);

        $doctors = $hospital->getDoctors();
        $json = array();
        foreach ($doctors as $a) {

            $arr['doctor_id'] = $a->id;
            $arr['doctor_name'] = $a->name;
            $json[] = $arr;
        }

        echo json_encode($json, JSON_UNESCAPED_UNICODE);

        return self::BLANK;
    }

    // 运营每日查看回复信息
    // sql注入漏洞 TODO by sjp 20170503
    public function doPushMsgDay () {
        $selectauditorid = XRequest::getValue('selectauditorid', 0);
        $date = XRequest::getValue('date', '0000-00-00');
        $pushmsgnum = XRequest::getValue('pushmsgnum', 5);

        $pushmsgnum = intval($pushmsgnum);

        $cond = "";
        $bind = [];

        $fromtime = '0000-00-00';
        $totime = '0000-00-00';

        if ($date != '0000-00-00') {
            $fromtime = $date . ' 10:00:00';
            $totime = $date . ' 19:30:00';

            $cond .= " and createtime >= :fromtime and createtime <= :totime ";
            $bind[':fromtime'] = $fromtime;
            $bind[':totime'] = $totime;
        }

        if ($selectauditorid) {

            $cond .= " and send_by_objtype = 'Auditor' and send_by_objid = :send_by_objid and send_by_objid <> 0
                order by rand() limit {$pushmsgnum} ";
            $bind[':send_by_objid'] = $selectauditorid;

            $pushmsgs = Dao::getEntityListByCond('PushMsg', $cond, $bind);
        } else {
            $auditorids = CtrHelper::getYunyingAuditorCtrArray(false);

            $list = array();
            $pushmsgtmp = array();
            foreach ($auditorids as $k => $v) {

                $condtmp = '';
                if ($date != '0000-00-00') {
                    $condtmp = " and createtime >= '{$fromtime}' and createtime <= '{$totime}' and send_by_objtype = 'Auditor' and send_by_objid = {$k} order by rand() limit {$pushmsgnum} ";
                } else {
                    $condtmp = " and send_by_objtype = 'Auditor' and send_by_objid = {$k} order by rand() limit {$pushmsgnum} ";
                }
                $pushmsgtmp = Dao::getEntityListByCond("PushMsg", $condtmp);
                $list = array_merge($list, $pushmsgtmp);
            }

            $pushmsgs = $list;
        }

        XContext::setValue('selectauditorid', $selectauditorid);
        XContext::setValue('date', $date);
        XContext::setValue('pushmsgnum', $pushmsgnum);
        XContext::setValue('pushmsgs', $pushmsgs);

        return self::SUCCESS;
    }

    // 需要运营审核的患者
    public function doNeedAuditList () {
        $auditorid = XRequest::getValue('auditorid', 0);
        $auditor = Auditor::getById($auditorid);

        $diseaseids = $auditor->getDiseaseIdArr();
        $diseaseidstr = implode(',', $diseaseids);

        $optasktpl = OpTaskTplDao::getOneByUnicode('baodao:Patient');

        $cond = " and optasktplid = {$optasktpl->id} and status = 0 and diseaseid in ($diseaseidstr) and doctorid != 33 order by createtime desc ";
        $optasks = Dao::getEntityListByCond('OpTask', $cond);

        $list = [];
        foreach ($optasks as $optask) {
            if ($optask->patient instanceof Patient) {
                $list[] = $optask;
            }
        }

        XContext::setValue('auditor', $this->myauditor);
        XContext::setValue('list', $list);

        return self::SUCCESS;
    }
}
