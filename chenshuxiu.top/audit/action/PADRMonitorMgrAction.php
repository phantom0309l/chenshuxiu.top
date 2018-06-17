<?php

// PADRMonitorMgrAction
class PADRMonitorMgrAction extends AuditBaseAction
{

    public function doList() {
        $patientid = XRequest::getValue('patientid', 0);
        DBC::requireNotEmpty($patientid, 'patientid is null');
        $patient = Patient::getById($patientid);
        DBC::requireTrue($patient instanceof Patient, 'patient 不存在');

        $disease = $patient->disease;

        $padrmonitors = PADRMonitorDao::getLastPlanGroupListByPatientid($patientid);

        XContext::setValue('padrmonitors', $padrmonitors);
        XContext::setValue('patient', $patient);
        XContext::setValue('disease', $disease);

        return self::SUCCESS;
    }

    public function doAjaxObjPictures() {
        $padrmonitorid = XRequest::getValue('padrmonitorid', 0);
        DBC::requireNotEmpty($padrmonitorid, 'padrmonitorid is null');
        $padrmonitor = PADRMonitor::getById($padrmonitorid);
        DBC::requireTrue($padrmonitor instanceof PADRMonitor, 'padrmonitor 不存在');

        $arr = [];
        $objpictures = $padrmonitor->getObjPictures();
        foreach ($objpictures as $objpicture) {
            $arr[] = [
                'pictureid' => $objpicture->pictureid,
                'patientpictureid' => $objpicture->patientpictureid,
                'url' => $objpicture->getImgUrl(),
                'thumburl' => $objpicture->getThumbUrl(140, 140),
            ];
        }

        $this->result['data'] = [
            "objpictures" => $arr
        ];
        return self::TEXTJSON;
    }

    // 代患者修改
    public function doAjaxModifyPost() {
        $padrmonitorid = XRequest::getValue('padrmonitorid', 0);
        DBC::requireNotEmpty($padrmonitorid, 'padrmonitorid is null');
        $padrmonitor = PADRMonitor::getById($padrmonitorid);
        DBC::requireTrue($padrmonitor instanceof PADRMonitor, 'padrmonitor 不存在');

        $thedate = XRequest::getValue('thedate');
        DBC::requireNotEmpty($thedate, '请选择患者实际检查日期');

        $patient = $padrmonitor->patient;
        $ename = $padrmonitor->adrmonitorruleitem_ename;

        // 处理图片
        $pictureids = XRequest::getValue("pictureids", []);
        Debug::trace($pictureids);

        $objpictures = $padrmonitor->getObjPictures();
        Debug::trace("===========现有" . count($objpictures) . "条数据 obj picture");
        foreach ($objpictures as $objpicture) {
            // 存在于pictureids，不需要删除
            if (in_array($objpicture->pictureid, $pictureids)) {
                continue;
            }
            // 删除的时候，同时也将patienticture删除
            $patientpicture = PatientPictureDao::getByObj($objpicture);
            if ($patientpicture instanceof PatientPicture) {
                Debug::trace("===========删除：patientpictureid" . $patientpicture->id);
                $patientpicture->remove();
            }

            // 删除流
            $pipe = PipeDao::getByEntity($objpicture);
            if ($pipe instanceof Pipe) {
                Debug::trace("===========删除：pipeid" . $pipe->id);
                $pipe->remove();
            }

            $objpicture->remove();
            Debug::trace("===========删除：objpictureid" . $objpicture->id);
        }

        $pictures = Dao::getEntityListByIds("Picture", $pictureids);
        foreach ($pictures as $picture) {
            $obj_picture = null;

            $title = '';
            switch ($ename) {
                case 'ganshengong': // 肝肾功
//                    $obj_picture = LiverPictureDao::getByPicture($picture);
//                    if (false == $obj_picture instanceof LiverPicture) {
//                        $row = [];
//                        $row["patientid"] = $patient->id;
//                        $row["doctorid"] = $patient->doctorid;
//                        $row["objtype"] = 'PADRMonitor';
//                        $row["objid"] = $padrmonitor->id;
//                        $row["pictureid"] = $picture->id;
//                        $obj_picture = LiverPicture::createByBiz($row);
//                        Debug::trace("===========新建");
//                    }

                    $row = [];
                    $row["patientid"] = $patient->id;
                    $row["doctorid"] = $patient->doctorid;
                    $row["objtype"] = 'PADRMonitor';
                    $row["objid"] = $padrmonitor->id;
                    $row["pictureid"] = $picture->id;
                    $obj_picture = LiverPicture::createByBiz($row);
                    Debug::trace("===========新建");

                    $title = '不良反应-肝肾功图片';
                    Debug::trace("===========不良反应-肝肾功图片：" . $obj_picture->id);
                    break;
                case 'xuechanggui': // 血常规
//                    $obj_picture = WxPicMsgDao::getByPicture($picture);
//                    if (false == $obj_picture instanceof WxPicMsg) {
//                        $row = [];
//                        $row["patientid"] = $patient->id;
//                        $row["doctorid"] = $patient->doctorid;
//                        $row["objtype"] = 'PADRMonitor';
//                        $row["objid"] = $padrmonitor->id;
//                        $row["pictureid"] = $picture->id;
//                        $row["source"] = 'self';
//                        $row["send_by_objtype"] = 'Patient';
//                        $row["send_by_objid"] = $patient->id;
//                        $row["send_explain"] = 'padrmonitor';
//                        $obj_picture = WxPicMsg::createByBiz($row);
//                    }

                    $row = [];
                    $row["patientid"] = $patient->id;
                    $row["doctorid"] = $patient->doctorid;
                    $row["objtype"] = 'PADRMonitor';
                    $row["objid"] = $padrmonitor->id;
                    $row["pictureid"] = $picture->id;
                    $row["source"] = 'self';
                    $row["send_by_objtype"] = 'Patient';
                    $row["send_by_objid"] = $patient->id;
                    $row["send_explain"] = 'padrmonitor';
                    $obj_picture = WxPicMsg::createByBiz($row);
                    Debug::trace("===========新建");

                    $title = '不良反应-血常规图片';
                    Debug::trace("===========不良反应-血常规图片：" . $obj_picture->id);
                    break;
                default:
//                    $obj_picture = BasicPictureDao::getByPicture($picture);
//                    if (false == $obj_picture instanceof BasicPicture) {
//                        $row = [];
//                        $row["patientid"] = $patient->id;
//                        $row["doctorid"] = $patient->doctorid;
//                        $row["pictureid"] = $picture->id;
//                        $row["type"] = $ename;
//                        $row["objtype"] = 'PADRMonitor';
//                        $row["objid"] = $padrmonitor->id;
//                        $obj_picture = BasicPicture::createByBiz($row);
//                    }

                    $row = [];
                    $row["patientid"] = $patient->id;
                    $row["doctorid"] = $patient->doctorid;
                    $row["pictureid"] = $picture->id;
                    $row["type"] = $ename;
                    $row["objtype"] = 'PADRMonitor';
                    $row["objid"] = $padrmonitor->id;
                    $obj_picture = BasicPicture::createByBiz($row);
                    Debug::trace("===========新建");

                    $enamestr = $padrmonitor->getEnameStr();
                    $title = "不良反应-{$enamestr}图片";
                    Debug::trace("===========不良反应-{$enamestr}图片：" . $obj_picture->id);
                    break;
            }
            // 因为在WxPicMsg::createByBiz中已经创建了patientpicture，但是title没有修改，所以在这里提交工作单元
            BeanFinder::get('UnitOfWork')->commitAndInit();

            $patientpicture = PatientPictureDao::getByObj($obj_picture);
            if ($patientpicture instanceof PatientPicture) {
                $patientpicture->title = $title;
                Debug::trace("===========patientpicture 已存在：" . $patientpicture->id);
            } else {
                // 创建归档图片
                $row = [];
                $row["createtime"] = $obj_picture->createtime;
                $row["patientid"] = $patient->id;
                $row["doctorid"] = $patient->doctorid;
                $row["objtype"] = get_class($obj_picture);
                $row["objid"] = $obj_picture->id;
                $row["source_type"] = get_class($obj_picture);
                $row["thedate"] = date('Y-m-d');
                $row["title"] = $title;
                $patientpicture = PatientPicture::createByBiz($row);
                Debug::trace("===========创建 patientpicture：" . $patientpicture->id);
            }

            // 挂上归档图片id
            $obj_picture->patientpictureid = $patientpicture->id;

            // 入流
            $pipe = PipeDao::getByEntity($obj_picture);
            if (false == $pipe instanceof Pipe) {
                $pipe = Pipe::createByEntity($obj_picture);
                Debug::trace("===========入流 pipeid：" . $pipe->id);
            }
        }

        // 监测记录尚未填写
        if ($padrmonitor->status != 2) {
            $padrmonitor->submit_time = date("Y-m-d H:i:s");
            $padrmonitor->status = 2;
            Debug::trace("===========监测记录尚未填写");
        }
        $padrmonitor->the_date = $thedate;

        // 获取任务，进行节点流转
        $optask = $padrmonitor->getOpTask();
        if ($optask instanceof OpTask) {    // 有任务
            Debug::trace("===========有任务");
            if ($optask->status != 1) {
                Debug::trace("===========任务未关闭");
                // 节点流转
                OpTaskEngine::flow_to_opnode($optask, 'finish', $this->myauditor->id);
                Debug::trace("===========节点流转");
            }
        } else {
            Debug::trace("===========没有任务");
            // 生成任务: 不良反应监测任务
            $optask = OpTaskService::createPatientOpTask($patient, 'padrmonitor:monitor', $padrmonitor, $padrmonitor->plan_date, $this->myauditor->id);
            Debug::trace("===========生成任务");
            // 节点流转
            OpTaskEngine::flow_to_opnode($optask, 'finish', $this->myauditor->id);
            Debug::trace("===========节点流转");
        }

        return self::TEXTJSON;
    }

    public function doAjaxDeletePost() {
        $padrmonitorid = XRequest::getValue('padrmonitorid', 0);
        DBC::requireNotEmpty($padrmonitorid, 'padrmonitorid is null');
        $padrmonitor = PADRMonitor::getById($padrmonitorid);
        DBC::requireTrue($padrmonitor instanceof PADRMonitor, 'padrmonitor 不存在');

        $padrmonitor->remove();

        return self::TEXTJSON;
    }

    public function doAjaxAddOptaskPost() {
        $padrmonitorid = XRequest::getValue('padrmonitorid', 0);

        $padrmonitor = PADRMonitor::getById($padrmonitorid);
        DBC::requireTrue($padrmonitor instanceof PADRMonitor, 'padrmonitor 不存在');

        // 生成任务: 不良反应监测任务 (实体唯一 PADRMonitor)
        $plantime = $padrmonitor->plan_date;
        OpTaskService::tryCreateOpTaskByObj($wxuser = null, $padrmonitor->patient, $doctor = null, 'padrmonitor:monitor', $padrmonitor, $plantime,
            $this->myauditor->id);

        return self::TEXTJSON;
    }

    // 不良反应监测
    public function doSend() {
        $padrmonitorid = XRequest::getValue('padrmonitorid', 0);
        $padrmonitor = PADRMonitor::getById($padrmonitorid);
        DBC::requireTrue($padrmonitor instanceof PADRMonitor, 'padrmonitor 不存在');

        $patient = $padrmonitor->patient;
        DBC::requireTrue($patient instanceof Patient, 'patient 不存在');

        $doctorid = XRequest::getValue('doctorid', 0);
        if ($doctorid == 0) {
            $doctorid = $patient->doctorid;
        }
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, 'doctor 不存在');

        $diseaseid = XRequest::getValue('diseaseid', 0);

        XContext::setValue('padrmonitor', $padrmonitor);
        XContext::setValue('doctor', $doctor);
        XContext::setValue('patient', $patient);
        XContext::setValue('diseaseid', $diseaseid);

        return self::SUCCESS;
    }

    // ajax修改监测日
    public function doAjaxModifyDay() {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        DBC::requireTrue($patient instanceof Patient, 'patient 不存在');

        $weekday = XRequest::getValue('weekday', 1);
        $patient->adrmonitor_weekday = $weekday;

        return self::TEXTJSON;
    }

    // 发送监测日历给患者
    public function doAjaxSend() {
        $padrmonitorid = XRequest::getValue('padrmonitorid', 0);
        DBC::requireNotEmpty($padrmonitorid, 'padrmonitorid is null');
        $padrmonitor = PADRMonitor::getById($padrmonitorid);
        DBC::requireTrue($padrmonitor instanceof PADRMonitor, '不良反应监测不存在');

        $doctor = $padrmonitor->doctor;
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $pcard = $doctor->getPcardByPatientid($padrmonitor->patientid);
        DBC::requireTrue($pcard instanceof Pcard, 'Pcard不存在');

        $medicine = $padrmonitor->medicine;
        DBC::requireTrue($medicine instanceof Medicine, '药品不存在');

        $wx_uri = Config::getConfig("wx_uri");
        $url = $wx_uri . '/padrmonitor/one?padrmonitorid=' . $padrmonitorid;

        $first = array(
            "value" => "不良反应监测",
            "color" => "");
        $keyword2 = "{$padrmonitor->patient->name}患者，你好。你近期的{$medicine->scientificname}({$medicine->name})药物的副反应监测已汇总，点击详情查看更多";

        $keywords = array(
            array(
                "value" => "{$pcard->doctor->name}",
                "color" => "#ff6600"),
            array(
                "value" => $keyword2,
                "color" => "#ff6600"));
        $content = WxTemplateService::createTemplateContent($first, $keywords);

        PushMsgService::sendTplMsgToWxUsersOfPcardByAuditor($pcard, $this->myauditor, 'followupNotice', $content, $url);

        return self::TEXTJSON;
    }
}
