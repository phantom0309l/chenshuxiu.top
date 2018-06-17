<?php

// PatientPictureMgrAction
class PatientPictureMgrAction extends AuditBaseAction
{

    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        $patientid = XRequest::getValue('patientid', 0);
        $patient_name = XRequest::getValue('patient_name', '');

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);
        XContext::setValue('patientid', $patientid);
        XContext::setValue('patient_name', $patient_name);

        $ppstatus = XRequest::getValue("ppstatus", - 1);

        $diseasegroupid = XRequest::getValue('diseasegroupid', 0);
        $type = XRequest::getValue('type', 'all');

        $fromdate = XRequest::getValue("fromdate", '');
        $todate = XRequest::getValue("todate", '');

        $cond = " ";
        $url = "/patientpicturemgr/list?1=1";
        $bind = [];
        $mydisease = $this->mydisease;

        if ($mydisease instanceof Disease) {
            $doctors = DoctorDao::getListByDiseaseid($mydisease->id, true);
        }

        if ($fromdate) {
            $cond .= " and pp.createtime >= :fromdate ";
            $bind[':fromdate'] = $fromdate;
            $url .= "&fromdate=" . $fromdate;
        }

        if ($todate) {
            $cond .= " and pp.createtime <= :todate ";
            $bind[':todate'] = $todate;
            $url .= "&todate=" . $todate;
        }

        if ($doctorid) {
            $cond .= " and p.doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
            $url .= "&doctorid=" . $doctorid;
        }

        if ($ppstatus >= 0) {
            $cond .= " and pp.status = :ppstatus ";
            $bind[':ppstatus'] = $ppstatus;
            $url .= "&ppstatus=" . $ppstatus;
        }

        if ($diseasegroupid) {
            $cond .= " and p.diseaseid in (select id from diseases where diseasegroupid = :diseasegroupid ) ";
            $bind[':diseasegroupid'] = $diseasegroupid;
            $url .= "&diseasegroupid=" . $diseasegroupid;
        }

        if ($type && $type != 'all') {
            $cond .= " and pp.type = :type ";
            $bind[':type'] = $type;
            $url .= "&type=" . $type;
        }

        if ($patient_name) {
            if (XPatientIndex::isEqual($patient_name)) {
                $cond .= " and xpi.word = :word ";
                $bind[':word'] = "{$patient_name}";
            } else {
                $cond .= " and xpi.word like :word ";
                $bind[':word'] = "%{$patient_name}%";
            }

            $url .= "&keyword=" . $patient_name;
        }

        $sql = "select distinct pp.*
                from patientpictures pp
                inner join patients p on pp.patientid=p.id
                inner join xpatientindexs xpi on p.id = xpi.patientid
                where 1=1
                ";

        $cond .= " order by pp.createtime desc ";

        $sql .= $cond;
        $patientpictures = Dao::loadEntityList4Page("PatientPicture", $sql, $pagesize, $pagenum, $bind);

        // 翻页begin
        $countSql = "select count(distinct pp.id) as cnt
                    from patientpictures pp
                    inner join patients p on pp.patientid=p.id
                    inner join xpatientindexs xpi on p.id = xpi.patientid
                    where 1=1 " . $cond;
        // 分页
        $cnt = Dao::queryValue($countSql, $bind);
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("diseasegroupid", $diseasegroupid);
        XContext::setValue("type", $type);
        XContext::setValue("fromdate", $fromdate);
        XContext::setValue("todate", $todate);
        XContext::setValue("ppstatus", $ppstatus);
        XContext::setValue('doctors', $doctors);
        XContext::setValue("patientpictures", $patientpictures);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    public function doOne () {
        $patientpictureid = XRequest::getValue("patientpictureid", 0);

        $patientpicture = PatientPicture::getById($patientpictureid);
        DBC::requireNotEmpty($patientpicture, "patientpicture is null");
        DBC::requireNotEmpty($patientpicture->obj, "[{$patientpicture->id}]->obj is null");

        XContext::setValue("patientpicture", $patientpicture);
        return self::SUCCESS;
    }

    public function doWbc () {
        $patientpictureid = XRequest::getValue("patientpictureid", 0);

        $patientpicture = PatientPicture::getById($patientpictureid);

        $patientrecordid = intval(substr($patientpicture->content, 0, 9));
        $patientrecord = PatientRecord::getById($patientrecordid);

        $thedate = '';
        $baixibao = '';
        $xuehongdanbai = '';
        $xuexiaoban = '';
        $zhongxingli = '';

        if ($patientrecord instanceof PatientRecord) {
            $data = $patientrecord->loadJsonContent();
            $thedate = $patientrecord->thedate;

            $baixibao = $data['baixibao'];
            $xuehongdanbai = $data['xuehongdanbai'];
            $xuexiaoban = $data['xuexiaoban'];
            $zhongxingli = $data['zhongxingli'];
        }

        XContext::setValue("patientpicture", $patientpicture);
        XContext::setValue("thedate", $thedate);
        XContext::setValue("baixibao", $baixibao);
        XContext::setValue("xuehongdanbai", $xuehongdanbai);
        XContext::setValue("xuexiaoban", $xuexiaoban);
        XContext::setValue("zhongxingli", $zhongxingli);
        return self::SUCCESS;
    }

    public function doWbcPost () {
        $patientpictureid = XRequest::getValue("patientpictureid", 0);
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));
        $baixibao = XRequest::getValue("baixibao", '');
        $xuehongdanbai = XRequest::getValue("xuehongdanbai", '');
        $xuexiaoban = XRequest::getValue("xuexiaoban", '');
        $zhongxingli = XRequest::getValue("zhongxingli", '');

        $patientpicture = PatientPicture::getById($patientpictureid);

        $patientrecordid = intval(substr($patientpicture->content, 0, 9));
        $patientrecord = PatientRecord::getById($patientrecordid);

        $data = [];

        if ($patientrecord instanceof PatientRecord) {
            $patientrecord->thedate = $thedate;
        } else {
            $row = [];
            $row["patientid"] = $patientpicture->patientid;
            $row["type"] = 'wbc_checkup';
            $row["code"] = 'cancer';
            $row["thedate"] = $thedate;
            $row["create_auditorid"] = $this->myauditor->id;

            $patientrecord = PatientRecord::createByBiz($row);
        }

        $data['baixibao'] = $baixibao;
        $data['xuehongdanbai'] = $xuehongdanbai;
        $data['xuexiaoban'] = $xuexiaoban;
        $data['zhongxingli'] = $zhongxingli;

        $patientrecord->saveJsonContent($data);

        $patientpicture->content = "{$patientrecord->id}(若为血常规或肝肾功类型图片，前面的数字不能修改，备注内容在后面添加即可)";
        $patientpicture->modifyStatus(1);

        XContext::setJumpPath("/patientpicturemgr/wbc?patientpictureid={$patientpictureid}");

        return self::BLANK;
    }

    // 肝肾功 Liver Kidney Function
    public function doLkf () {
        $patientpictureid = XRequest::getValue("patientpictureid", 0);

        $patientpicture = PatientPicture::getById($patientpictureid);

        $patientrecordid = intval(substr($patientpicture->content, 0, 9));
        $patientrecord = PatientRecord::getById($patientrecordid);

        $thedate = '';
        $lkf_alt = '';
        $lkf_alp = '';
        $lkf_tbil = '';
        $lkf_cr = '';

        if ($patientrecord instanceof PatientRecord) {
            $data = $patientrecord->loadJsonContent();
            $thedate = $patientrecord->thedate;

            $lkf_alt = $data['lkf_alt'];
            $lkf_alp = $data['lkf_alp'];
            $lkf_tbil = $data['lkf_tbil'];
            $lkf_cr = $data['lkf_cr'];
        }

        XContext::setValue("patientpicture", $patientpicture);
        XContext::setValue("thedate", $thedate);
        XContext::setValue("lkf_alt", $lkf_alt);
        XContext::setValue("lkf_alp", $lkf_alp);
        XContext::setValue("lkf_tbil", $lkf_tbil);
        XContext::setValue("lkf_cr", $lkf_cr);
        return self::SUCCESS;
    }

    public function doLkfPost () {
        $patientpictureid = XRequest::getValue("patientpictureid", 0);
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));
        $lkf_alt = XRequest::getValue("lkf_alt", '');
        $lkf_alp = XRequest::getValue("lkf_alp", '');
        $lkf_tbil = XRequest::getValue("lkf_tbil", '');
        $lkf_cr = XRequest::getValue("lkf_cr", '');

        $patientpicture = PatientPicture::getById($patientpictureid);

        $patientrecordid = intval(substr($patientpicture->content, 0, 9));
        $patientrecord = PatientRecord::getById($patientrecordid);

        $data = [];

        if ($patientrecord instanceof PatientRecord) {
            $patientrecord->thedate = $thedate;
        } else {
            $row = [];
            $row["patientid"] = $patientpicture->patientid;
            $row["type"] = 'lkf_checkup';
            $row["code"] = 'cancer';
            $row["thedate"] = $thedate;
            $row["create_auditorid"] = $this->myauditor->id;

            $patientrecord = PatientRecord::createByBiz($row);
        }

        $data['lkf_alt'] = $lkf_alt;
        $data['lkf_alp'] = $lkf_alp;
        $data['lkf_tbil'] = $lkf_tbil;
        $data['lkf_cr'] = $lkf_cr;

        $patientrecord->saveJsonContent($data);

        $patientpicture->content = "{$patientrecord->id}(若为血常规或肝肾功类型图片，前面的数字不能修改，备注内容在后面添加即可)";
        $patientpicture->modifyStatus(1);

        XContext::setJumpPath("/patientpicturemgr/lkf?patientpictureid={$patientpictureid}");

        return self::BLANK;
    }

    public function doChangeTypeJson () {
        $patientpictureid = XRequest::getValue("patientpictureid", 0);
        $type = XRequest::getValue("type", 'not');

        $type_values = PatientPicture::getTypes(false);
        if (! array_key_exists($type, $type_values)) {
            echo "fail";
        } else {
            $patientpicture = PatientPicture::getById($patientpictureid);
            if ($patientpicture instanceof PatientPicture) {
                $patientpicture->type = $type;

                echo "success";
            } else {
                echo "fail";
            }
        }

        return self::BLANK;
    }

    public function doChangeStatusPost () {
        $patientpictureid = XRequest::getValue("patientpictureid", 0);
        $status = XRequest::getValue("status", 0);

        $patientpicture = PatientPicture::getById($patientpictureid);
        $patientpicture->modifyStatus($status);

        XContext::setJumpPath("/patientpicturemgr/one?patientpictureid={$patientpictureid}");
        return self::SUCCESS;
    }

    public function doChangeObjPost () {
        $patientpictureid = XRequest::getValue("patientpictureid", 0);
        $objtype = XRequest::getValue("objtype", '');

        $patientpicture = PatientPicture::getById($patientpictureid);
        $patientpicture->changeObj($objtype);

        XContext::setJumpPath("/patientpicturemgr/one?patientpictureid={$patientpictureid}");
        return self::SUCCESS;
    }

    public function doPPicListHtml () {
        $patientpictureid = XRequest::getValue("patientpictureid", 0);
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $patientpicture = PatientPicture::getById($patientpictureid);

        if ($patientpicture->patient instanceof Patient) {
            $patientpictures_date = PatientPictureDao::getListByPatientThedate($patientpicture->patient, $thedate, 'WxPicMsg');
        } else {
            $patientpictures_date = [];
            // Debug::warn("patientpictrue[{$patientpicture->id}]
        // 没有patient，估计是患者没报到，但是上传了图片,访问:[{$this->myauditor->name}]");
        }

        XContext::setValue("patientpicture", $patientpicture);
        XContext::setValue("patientpictures_date", $patientpictures_date);
        XContext::setValue("thedate", $thedate);
        return self::SUCCESS;
    }

    public function doSheetTplHtml () {
        $thisppid = XRequest::getValue("thisppid", 0);
        $targetppid = XRequest::getValue("targetppid", 0);

        $patientpicture = PatientPicture::getById($thisppid);

        if ($this->mydisease instanceof Disease) {
            $picturedatasheettpls = PictureDataSheetTplDao::getListByDiseaseid($this->mydisease->id);
        } else {
            $picturedatasheettpls = Dao::getEntityListByCond('PictureDataSheetTpl');
        }

        XContext::setValue("patientpicture", $patientpicture);
        XContext::setValue("targetppid", $targetppid);
        XContext::setValue("picturedatasheettpls", $picturedatasheettpls);
        return self::SUCCESS;
    }

    public function doSheetHtml () {
        $thisppid = XRequest::getValue("thisppid", 0);
        $targetppid = XRequest::getValue("targetppid", 0);
        $picturedatasheettplid = XRequest::getValue("picturedatasheettplid", 0);
        $picturedatasheetid = XRequest::getValue("picturedatasheetid", 0);

        $thispp = PatientPicture::getById($thisppid);

        $picturedatasheettpl = PictureDataSheetTpl::getById($picturedatasheettplid);

        if ($picturedatasheettpl instanceof PictureDataSheetTpl) {
            $qas = $picturedatasheettpl->getQaArr();
            $sheettitle = $picturedatasheettpl->title;
        } else {
            $picturedatasheet = PictureDataSheet::getById($picturedatasheetid);
            $qas = $picturedatasheet->getQaArr();
            $sheettitle = $picturedatasheet->picturedatasheettpl->title;
        }
        XContext::setValue("thispp", $thispp);
        XContext::setValue("targetppid", $targetppid);
        XContext::setValue("picturedatasheettplid", $picturedatasheettplid);
        XContext::setValue("picturedatasheetid", $picturedatasheetid);
        XContext::setValue("sheettitle", $sheettitle);
        XContext::setValue("qas", $qas);
        return self::SUCCESS;
    }

    public function doSaveSheetJson () {
        $thisppid = XRequest::getValue("thisppid", 0);
        $targetppid = XRequest::getValue("targetppid", 0);
        $picturedatasheettplid = XRequest::getValue("picturedatasheettplid", 0);
        $picturedatasheetid = XRequest::getValue("picturedatasheetid", 0);
        $thedate = XRequest::getValue("thedate", date("Y-m-d"));
        $title = XRequest::getValue("title", '');
        $sheetcontent = XRequest::getValue("sheetcontent", '');
        $content = XRequest::getValue("content", '');

        $thispp = PatientPicture::getById($thisppid);
        $targetpp = PatientPicture::getById($targetppid);

        $savepp = $thispp;
        if ($targetpp instanceof PatientPicture) {
            $savepp = $targetpp->getMainPatientPicture();
        }

        $savepp->thedate = $thedate;
        $savepp->title = $title;
        $savepp->content = $content;

        $picturedatasheet = PictureDataSheet::getById($picturedatasheetid);

        if ($picturedatasheet instanceof PictureDataSheet) {
            $picturedatasheet->answercontents = $sheetcontent;
        } else {
            $row = array();
            $row['picturedatasheettplid'] = $picturedatasheettplid;
            $row['patientpictureid'] = $savepp->id;
            $row['thedate'] = $thedate;
            $row['answercontents'] = $sheetcontent;

            PictureDataSheet::createByBiz($row);
        }

        if ($targetpp instanceof PatientPicture) {
            $thispp->outGroup();
            $thispp->joinGroup($savepp);
        }
        $thispp->setDone();

        echo 'ok';
        return self::BLANK;
    }

    public function doDeleteSheetJson () {
        $picturedatasheetid = XRequest::getValue("picturedatasheetid", 0);
        $picturedatasheet = PictureDataSheet::getById($picturedatasheetid);

        if ($picturedatasheet instanceof PictureDataSheet) {
            $picturedatasheet->remove();
        }
        echo 'ok';
        return self::BLANK;
    }

    // 更改content字段（ocr结果手动调整）
    public function doChangeContentPost() {
        $pateintInfo = urldecode(XRequest::getValue('patientInfo', ''));
        $items = urldecode(XRequest::getValue('items', ''));
        $drugName = urldecode(XRequest::getValue('drugName', ''));
        $drugList = urldecode(XRequest::getValue('drugList', ''));
        $type = XRequest::getValue('type', 1);
        $patientPicId = XRequest::getValue('picId', 0);

        $patientPic = PatientPicture::getById($patientPicId);
        DBC::requireTrue($patientPic instanceof PatientPicture, '图片不存在');

        $arr['patientInfo'] = $this->strs2Arr($pateintInfo, 4);
        $arr['items'] = $this->strs2Arr($items, 5);
        $arr['drugName'] = $this->strs2Arr($drugName, 2);
        $arr['drugList'] = $this->strs2Arr($drugList, 2);

        $this->changeContent($patientPic, $arr, $type);

        $contentJson = $patientPic->ocrjson;
        $contentArr = json_decode($contentJson, true);

        XContext::setValue("ocrArrformdata", $contentArr);
        XContext::setSuccessTemplate('ocrtextmgr/onehtml.tpl.php');
        return self::SUCCESS;
    }

    private function strs2Arr($str, $num) {
        $strArr = explode('&', $str);
        $resultArr = array();
        $tempArr = array();

        foreach ($strArr as $key => $item) {
            $arr = explode('=', $item);
            $tempArr[$arr[0]] = $arr[1];
            if (($key + 1) % $num == 0) {
                $resultArr[] = $tempArr;
                $tempArr = array();
            }
        }
        return $resultArr;
    }

    private function changeContent($patientPicture, Array $arr, $type = 1) {
        $contentArr = json_decode($patientPicture->ocrjson);

        if ($type == 1) {
            $contentArr->patientInfo = array_slice($arr['patientInfo'], 0, 3);
            $contentArr->date = array_slice($arr['patientInfo'], 3, 1);
            $contentArr->items = $arr['items'];
        } elseif ($type == 2) {
            $contentArr->drugName = $arr['drugName'];
            $contentArr->drugFactory = $arr['drugFactory'];
        } elseif ($type == 3) {
            $contentArr->patientInfo = array_slice($arr['patientInfo'], 0, 3);
            $contentArr->date = array_slice($arr['patientInfo'], 3, 1);
            $contentArr->drugList = $arr['drugList'];
        }

        $contentArr->lastChangeTime = date('Y-m-d H:i:s');
        $contentArr->lastChangeAuditId = $this->myauditor->id;
        $contentArr->lastChangeAuditName = $this->myauditor->name;
        $contentArr->status = 1;

        $contentJson = json_encode($contentArr,JSON_UNESCAPED_UNICODE );
        $patientPicture->ocrjson = $contentJson;
    }
}
