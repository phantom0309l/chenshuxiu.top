<?php

// BedTktMgrAction
class BedTktMgrAction extends AuditBaseAction
{

    public function doList () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        $patientid = XRequest::getValue('patientid', 0);
        $patient_name = XRequest::getValue('patient_name', '');

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);
        XContext::setValue('patientid', $patientid);
        XContext::setValue('patient_name', $patient_name);

        $sql = "select distinct a.*
                from bedtkts a
                inner join patients b on b.id = a.patientid
                inner join pcards c on c.patientid = b.id
                where a.status = 0 ";

        $cond = "";
        $bind = [];

        if ($patientid) {
            $cond .= " and b.id = :patientid ";
            $bind[':patientid'] = $patientid;
        } else {
            if ($patient_name) {
                $cond .= " and b.name like :name ";
                $bind[':name'] = "%{$patient_name}%";
            }
        }

        if ($doctorid) {
            $cond .= " and a.doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        $cond .= " order by a.id desc ";

        $sql .= $cond;

        $bedtkts = Dao::LoadEntityList('BedTkt', $sql, $bind);

        XContext::setValue('bedtkts', $bedtkts);

        return self::SUCCESS;
    }

    public function doModify () {
        $bedtktid = XRequest::getValue('bedtktid', 0);
        $bedtkt = BedTkt::getById($bedtktid);
        DBC::requireNotEmpty($bedtkt, "bedtkt is null");

        $bedtktconfig = BedTktConfigDao::getAllowByDoctoridType($bedtkt->doctorid, $bedtkt->typestr);
        $content = json_decode($bedtktconfig->content, true);

        $extra_info = json_decode($bedtkt->extra_info, true);

        XContext::setValue('extra_info', $extra_info);
        XContext::setValue('content', $content);
        XContext::setValue('bedtkt', $bedtkt);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $bedtktid = XRequest::getValue('bedtktid', 0);
        $bedtkt = BedTkt::getById($bedtktid);
        DBC::requireNotEmpty($bedtkt, "bedtkt is null");

        $bedtktconfig = BedTktConfigDao::getAllowByDoctoridType($bedtkt->doctorid, $bedtkt->typestr);
        $content = json_decode($bedtktconfig->content, true);
        if ($content['is_feetype_show'] == 1) {
            $fee_type = XRequest::getValue('fee_type', '');
            $bedtkt->fee_type = $fee_type;
        }
        if ($content['is_plandate_show'] == 1) {
            $want_date = XRequest::getValue('want_date', '');
            $bedtkt->want_date = $want_date;
        }

        $bedtkt->extra_info = json_encode($_POST, JSON_UNESCAPED_UNICODE);
        if ($content['is_zhuyuan_photo_show'] == 1) {
            $bedtktpictureids = XRequest::getValue('bedtktpictureids', []);

            foreach ($bedtktpictureids as $pictureid) {
                $row["wxuserid"] = $bedtkt->wxuserid;
                $row["userid"] = $bedtkt->userid;
                $row["patientid"] = $bedtkt->patientid;
                $row["doctorid"] = $bedtkt->doctorid;
                $row["bedtktid"] = $bedtkt->id;
                $row["pictureid"] = $pictureid;

                $obj_picture = BedTktPicture::createByBiz($row);
            }
        }
        if ($content['is_xuechanggui_photo_show'] == 1) {
            $wxpicmsgids = XRequest::getValue('wxpicmsgids', []);

            foreach ($wxpicmsgids as $pictureid) {
                $row["wxuserid"] = $bedtkt->wxuserid;
                $row["userid"] = $bedtkt->userid;
                $row["patientid"] = $bedtkt->patientid;
                $row["doctorid"] = $bedtkt->doctorid;
                $row["objtype"] = 'BedTkt';
                $row["objid"] = $bedtkt->id;
                $row["pictureid"] = $pictureid;
                $row["send_by_objtype"] = 'Auditor';
                $row["send_by_objid"] = $this->myauditor->id;
                $row["send_explain"] = 'help_patient';

                $obj_picture = WxPicMsg::createByBiz($row);
            }
        }
        if ($content['is_gangongneng_photo_show'] == 1) {
            $liverpictureids = XRequest::getValue('liverpictureids', []);

            foreach ($liverpictureids as $pictureid) {
                $row["wxuserid"] = $bedtkt->wxuserid;
                $row["userid"] = $bedtkt->userid;
                $row["patientid"] = $bedtkt->patientid;
                $row["doctorid"] = $bedtkt->doctorid;
                $row["objtype"] = 'BedTkt';
                $row["objid"] = $bedtkt->id;
                $row["pictureid"] = $pictureid;

                $obj_picture = LiverPicture::createByBiz($row);
            }
        }
        if ($content['is_xindiantu_show'] == 1) {
            $xindiantuids = XRequest::getValue('xindiantuids', []);

            foreach ($xindiantuids as $pictureid) {
                $row['pictureid'] = $pictureid;
                $row["wxuserid"] = $bedtkt->wxuserid;
                $row["userid"] = $bedtkt->userid;
                $row["patientid"] = $bedtkt->patientid;
                $row["doctorid"] = $bedtkt->doctorid;
                $row['type'] = 'xindiantu';
                $row['objtype'] = get_class($bedtkt);
                $row['objid'] = $bedtkt->id;
                $obj_picture = BasicPicture::createByBiz($row);
            }
        }
        if ($content['is_xueshuantanlitu_show'] == 1) {
            $xueshuantanlituids = XRequest::getValue('xueshuantanlituids', []);

            foreach ($xueshuantanlituids as $pictureid) {
                $row['pictureid'] = $pictureid;
                $row["wxuserid"] = $bedtkt->wxuserid;
                $row["userid"] = $bedtkt->userid;
                $row["patientid"] = $bedtkt->patientid;
                $row["doctorid"] = $bedtkt->doctorid;
                $row['type'] = 'xueshuantanlitu';
                $row['objtype'] = get_class($bedtkt);
                $row['objid'] = $bedtkt->id;
                $obj_picture = BasicPicture::createByBiz($row);
            }
        }
        if ($content['is_fengshimianyijiancha_show'] == 1) {
            $fengshimianyijianchaids = XRequest::getValue('fengshimianyijianchaids', []);

            foreach ($fengshimianyijianchaids as $pictureid) {
                $row['pictureid'] = $pictureid;
                $row["wxuserid"] = $bedtkt->wxuserid;
                $row["userid"] = $bedtkt->userid;
                $row["patientid"] = $bedtkt->patientid;
                $row["doctorid"] = $bedtkt->doctorid;
                $row['type'] = 'fengshimianyijiancha';
                $row['objtype'] = get_class($bedtkt);
                $row['objid'] = $bedtkt->id;
                $obj_picture = BasicPicture::createByBiz($row);
            }
        }
        if ($content['is_shuqianqitajiancha_show'] == 1) {
            $shuqianqitajianchaids = XRequest::getValue('shuqianqitajianchaids', []);

            foreach ($shuqianqitajianchaids as $pictureid) {
                $row['pictureid'] = $pictureid;
                $row["wxuserid"] = $bedtkt->wxuserid;
                $row["userid"] = $bedtkt->userid;
                $row["patientid"] = $bedtkt->patientid;
                $row["doctorid"] = $bedtkt->doctorid;
                $row['type'] = 'shuqianqitajiancha';
                $row['objtype'] = get_class($bedtkt);
                $row['objid'] = $bedtkt->id;
                $obj_picture = BasicPicture::createByBiz($row);
            }
        }

        if ($bedtkt->status == 0) {
            $bedtkt->setWillAuditorStatus();
        }

        // 生成任务: 住院审核 (实体唯一 BedTkt)
        $optask = OpTaskService::tryCreateOpTask_audit_bedtkt($bedtkt, null, $this->myauditor->id);

        // MARK: - 王颖亦，公小蕾，转给李孝远
        if ($bedtkt->doctorid == 477 || $bedtkt->doctorid == 803) {
            $patient = $bedtkt->patient;
            $pcard = PcardDao::getByPatientidDoctorid($patient->id, 1002);
            if (false == $pcard instanceof Pcard) {
                $row = array();
                $row['create_patientid'] = $patient->id;
                $row["patientid"] = $patient->id;
                $row["doctorid"] = 1002;
                $row["diseaseid"] = $patient->diseaseid;
                $row["patient_name"] = $patient->name;
                $pcard = Pcard::createByBiz($row);
            }
            $bedtkt->set4lock('doctorid', 1002);
        }

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/bedtktmgr/modify?bedtktid=" . $bedtktid . "&preMsg=" . urlencode($preMsg));

        return self::SUCCESS;
    }

    // 运营查看
    public function doShowHtml () {
        $bedtktid = XRequest::getValue('bedtktid', 0);
        DBC::requireNotEmpty($bedtktid, "bedtktid为空");
        $bedtkt = BedTkt::getById($bedtktid);
        DBC::requireNotEmpty($bedtkt, "bedtkt为空");

        $bedtktconfig = BedTktConfigDao::getByDoctoridType($bedtkt->doctorid, $bedtkt->typestr);
        DBC::requireNotEmpty($bedtktconfig, "{$bedtkt->doctor->name} 没有配置住院预约 {$bedtkt->typestr}");
        DBC::requireTrue($bedtktconfig->is_allow_bedtkt == 1, "{$bedtkt->doctor->name} 没有开启住院预约 {$bedtkt->typestr}");

        // 住院证
        $zhuyuans = $bedtkt->getBedTktPictures();
        // 血常规
        $xuechangguis = $bedtkt->getWxPicMsgs();
        // 肝肾功
        $gangongnengs = $bedtkt->getLiverPictures();
        // 心电图
        $xindiantus = $bedtkt->getXindiantuPictures();
        // 血栓弹力图
        $xueshuantanlitus = $bedtkt->getXueshuantanlituPictures();
        // 风湿免疫检查
        $fengshimianyijianchas = $bedtkt->getFengshimianyijianchaPictures();
        // 术前其他检查
        $shuqianqitajianchas = $bedtkt->getXindiantuPictures();

        $papertpls = PaperTplDao::getListByGroupstr('bedtkt');

        $paperlists = array();
        $bedtktpaperrefs = BedTktPaperRefDao::getListByBedTkt($bedtkt);

        foreach ($bedtktpaperrefs as $bedtktpaperref) {
            $paper = Paper::getById($bedtktpaperref->paperid);
            if ($paper instanceof Paper) {
                $paperlists["{$paper->ename}"] = $paper;
            }
        }

        XContext::setValue('bedtkt', $bedtkt);
        XContext::setValue('zhuyuans', $zhuyuans);
        XContext::setValue('xuechangguis', $xuechangguis);
        XContext::setValue('gangongnengs', $gangongnengs);
        XContext::setValue('xindiantus', $xindiantus);
        XContext::setValue('xueshuantanlitus', $xueshuantanlitus);
        XContext::setValue('fengshimianyijianchas', $fengshimianyijianchas);
        XContext::setValue('shuqianqitajianchas', $shuqianqitajianchas);

        XContext::setValue('bedtktconfig', $bedtktconfig);
        XContext::setValue('paperlists', $paperlists);

        return self::SUCCESS;
    }

    // 运营设置应住日期
    public function doSetPlan_dateJson () {
        $bedtktid = XRequest::getValue('bedtktid', 0);
        DBC::requireNotEmpty($bedtktid, "bedtktid为空");
        $bedtkt = BedTkt::getById($bedtktid);
        DBC::requireNotEmpty($bedtkt, "bedtkt为空");
        $plan_date = XRequest::getValue('plan_date', '0000-00-00');

        if ($plan_date != '0000-00-00') {
            $bedtkt->plan_date = $plan_date;

            echo "success";
        } else {
            echo "fail";
        }

        return self::BLANK;
    }

    // 运营审核通过
    public function doPassJson () {
        $bedtktid = XRequest::getValue('bedtktid', 0);
        DBC::requireNotEmpty($bedtktid, "bedtktid为空");
        $bedtkt = BedTkt::getById($bedtktid);
        DBC::requireNotEmpty($bedtkt, "bedtkt为空");

        $myauditor = $this->myauditor;

        $bedtkt->setAuditorPassStatus();
        $bedtkt->audit_time = date('Y-m-d H:i:s', time());
        $logcontent = "医助审核通过患者的住院预约\n医助：{$myauditor->name}\n应住院日期：{$bedtkt->plan_date}";
        $bedtkt->saveLog('auditor_pass', $logcontent, $myauditor->id);

        $wxuser = $bedtkt->wxuser;
        $doctor = $bedtkt->doctor;

        DBC::requireNotEmpty($bedtkt->typestr, "{$bedtkt->id} typestr 为空");
        $bedtktconfig = BedTktConfigDao::getByDoctoridType($bedtkt->doctorid, $bedtkt->typestr);
        DBC::requireNotEmpty($bedtktconfig, "{$bedtkt->doctor->name} 没有配置住院预约 {$bedtkt->typestr}");
        DBC::requireTrue($bedtktconfig->is_allow_bedtkt == 1, "{$bedtkt->doctor->name} 没有开启住院预约 {$bedtkt->typestr}");

        $config_content = json_decode($bedtktconfig->content, true);

        if ($config_content['is_auditpass_notice_open'] == 1) {
            // 发通知
            $first = array(
                "value" => "住院申请通过",
                "color" => "");
            $keyword2 = $config_content['auditpass_notice_content'] ? $config_content['auditpass_notice_content'] : "您的住院申请已通过审核，请保持电话畅通会有医生与您联系";

            $keywords = array(
                array(
                    "value" => "{$doctor->name}",
                    "color" => "#ff6600"),
                array(
                    "value" => $keyword2,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToWxUserByAuditor($wxuser, $myauditor, "doctornotice", $content);
        }

        $doctorconfig = $doctor->getConfigByCode('bedtkt_audit_pass');

        if ($doctorconfig->status) {
            $dwx_uri = Config::getConfig("dwx_uri");
            // MARK: - #3785
            $url = $dwx_uri . "/#/bedtkt/list?bedtktid={$bedtkt->id}&sex={$bedtkt->patient->sex}";

            $first = array(
                "value" => "新有患者申请住院，可点击详情进行处理",
                "color" => "#009900");

            $keywords = array(
                array(
                    "value" => $bedtkt->patient->name,
                    "color" => ""),
                array(
                    "value" => $bedtkt->plan_date,
                    "color" => ""));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            Dwx_kefuMsgService::sendTplMsgToDoctorByAuditor($doctor, $myauditor, "BedTktAuditNotice", $content, $url);

            // #4130, 协和风湿免疫科, 也发给 王迁 一份
            if ($doctor->id == 1294) {
                $doctor_fix = Doctor::getById(32);
                Dwx_kefuMsgService::sendTplMsgToDoctorByAuditor($doctor_fix, $myauditor, "BedTktAuditNotice", $content, $url);
            }
        }

        echo "success";
        return self::BLANK;
    }

    // 获取patientpictureid
    public function doGoPatientPicture () {
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);
        DBC::requireNotEmpty($objtype, 'objtype is null');
        DBC::requireNotEmpty($objid, 'objid is null');

        $entity = Dao::getEntityById($objtype, $objid);

        $patientpicture = PatientPictureDao::getByObj($entity);
        if (false == $patientpicture instanceof PatientPicture) {
            $row = array();
            $row["createtime"] = $entity->createtime;
            $row["wxuserid"] = $entity->wxuserid;
            $row["userid"] = $entity->userid;
            $row["patientid"] = $entity->patientid;
            $row["doctorid"] = $entity->doctorid;
            $row["objtype"] = $objtype;
            $row["objid"] = $entity->id;
            $row["source_type"] = $objtype;
            $patientpicture = PatientPicture::createByBiz($row);
        }

        $entity->patientpictureid = $patientpicture->id;

        XContext::setJumpPath("/patientpicturemgr/one?patientpictureid={$patientpicture->id}");
        return self::SUCCESS;
    }

    // 运营审核拒绝
    public function doRefuseJson () {
        $bedtktid = XRequest::getValue('bedtktid', 0);
        $bedtkt = BedTkt::getById($bedtktid);
        DBC::requireNotEmpty($bedtktid, "bedtktid为空");
        DBC::requireNotEmpty($bedtkt, "bedtkt为空");

        $wxuser = $bedtkt->wxuser;

        $myauditor = $this->myauditor;

        $bedtkt->setAuditorRefuseStatus();
        $bedtkt->audit_time = date('Y-m-d H:i:s', time());
        $logcontent = "医助审核不通过患者的住院预约\n医助：{$myauditor->name}\n";
        $bedtkt->saveLog('auditor_refuse', $logcontent, $myauditor->id);

        DBC::requireNotEmpty($bedtkt->typestr, "{$bedtkt->id} typestr 为空");
        $bedtktconfig = BedTktConfigDao::getByDoctoridType($bedtkt->doctorid, $bedtkt->typestr);
        DBC::requireNotEmpty($bedtktconfig, "{$bedtkt->doctor->name} 没有配置住院预约 {$bedtkt->typestr}");
        DBC::requireTrue($bedtktconfig->is_allow_bedtkt == 1, "{$bedtkt->doctor->name} 没有开启住院预约 {$bedtkt->typestr}");

        $config_content = json_decode($bedtktconfig->content, true);

        if ($config_content['is_auditrefuse_notice_open'] == 1) {
            // 发通知
            $first = array(
                "value" => "住院申请被拒绝",
                "color" => "");
            $keyword2 = $config_content['auditrefuse_notice_content'] ? $config_content['auditrefuse_notice_content'] : "您的住院申请未通过，如有问题请与我们联系";

            $keywords = array(
                array(
                    "value" => "{$bedtkt->doctor->name}",
                    "color" => "#ff6600"),
                array(
                    "value" => $keyword2,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToWxUserByAuditor($wxuser, $myauditor, "doctornotice", $content);
        }

        echo "success";
        return self::BLANK;
    }

    // 删除图片
    public function doDeletePic () {
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);

        $entityPic = Dao::getEntityById($objtype, $objid);
        if ($entityPic instanceof Entity) {
            // 删除的时候，同时也将patienticture删除
            $patientpicture = PatientPictureDao::getByObj($entityPic);
            if ($patientpicture instanceof PatientPicture) {
                $patientpicture->remove();
            }

            // 同时也删除流
            $pipe = PipeDao::getByEntity($entityPic);
            if ($pipe instanceof Pipe) {
                $pipe->remove();
            }

            // 删除图片
            $entityPic->remove();

            echo "success";
        } else {
            echo "fail";
        }

        return self::BLANK;
    }
}
