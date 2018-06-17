<?php

class OpTaskService
{

    // ----- OptLog ----- begin -----

    // 记录任务日志 OptLog
    public static function addOptLog($optask, $content, $auditorid = 0, $domode = '1', $jsoncontent = '') {
        if (false == $optask instanceof OpTask) {
            Debug::warn("OpTaskService::addOptLog optask is null ");
            return null;
        }

        $row = [];
        $row["optaskid"] = $optask->id;
        $row["auditorid"] = $auditorid;
        $row["domode"] = $domode;
        $row["content"] = $content;
        $row["jsoncontent"] = $jsoncontent;

        return OptLog::createByBiz($row);
    }

    // ----- OptLog ----- end -----

    // ----- 关闭任务 ----- begin -----

    // 关闭所有任务
    public static function closeAllOpTasksOfPatient(Patient $patient, $auditorid = 0) {
        $cond = "AND patientid = :patientid AND status != 1 ";
        $bind = [
            ":patientid" => $patient->id];
        $optasks = Dao::getEntityListByCond("OpTask", $cond, $bind);

        foreach ($optasks as $optask) {
            OpTaskService::addOptLog($optask, "[批量关闭]", $auditorid);
            OpTaskStatusService::changeStatus($optask, 1, $auditorid);
        }
    }

    // 删除实体关联的所有任务
    public static function removeAllOpTasksByObj(Entity $obj) {
        $optasks = OpTaskDao::getListByObj($obj);

        foreach ($optasks as $a) {
            $a->remove();
        }
    }

    // ----- 关闭任务 ----- end -----

    // ----- 创建任务基本函数 ----- begin -----

    // 创建Patient任务, 用于运营端
    // $unicode = "Paper:BaseInfo:collection"
    public static function createPatientOpTask(Patient $patient, $unicode, $obj = null, $plantime = '', $auditorid = 0, $arr = []) {
        return OpTaskService::createOpTaskByUnicode($wxuser = null, $patient, $doctor = null, $unicode, $obj, $plantime, $auditorid, $arr);
    }

    // 创建WxUser任务, 用于患者端
    // $unicode = "Paper:BaseInfo:collection"
    public static function createWxUserOpTask(WxUser $wxuser, $unicode, $obj = null, $plantime = '', $auditorid = 0, $arr = []) {
        return OpTaskService::createOpTaskByUnicode($wxuser, $patient = null, $doctor = null, $unicode, $obj, $plantime, $auditorid, $arr);
    }

    // 创建任务 createOpTaskByUnicode
    // $wxuser 和 $patient 必须其中一个不为 NULL
    // $doctor 可以为 NULL
    // $unicode = "Paper:BaseInfo:collection"
    public static function createOpTaskByUnicode($wxuser, $patient, $doctor, $unicode, $obj = null, $plantime = '', $auditorid = 0, $arr = []) {
        $optasktpl = OpTaskTplDao::getOneByUnicode($unicode);
        DBC::requireNotEmpty($optasktpl, "任务模板[{$unicode}]不存在");

        return OpTaskService::createOpTaskByOpTaskTpl($wxuser, $patient, $doctor, $optasktpl, $obj, $plantime, $auditorid, $arr);
    }

    // 创建任务 createOpTaskByOpTaskTpl
    // $arr : pipeid, auditorid, content, audit_remark
    public static function createOpTaskByOpTaskTpl($wxuser, $patient, $doctor, OpTaskTpl $optasktpl, $obj = null, $plantime = '', $auditorid = 0, $arr = []) {
        $wxuserid = 0;
        $userid = 0;
        $patientid = 0;
        $doctorid = 0;
        $diseaseid = 0;
        $optasktplid = $optasktpl->id;
        $objtype = '';
        $objid = 0;

        $plantime = $plantime ? $plantime : date('Y-m-d');
        $pipeid = isset($arr['pipeid']) ? $arr['pipeid'] : 0;
        $content = isset($arr['content']) ? $arr['content'] : '';
        $audit_remark = isset($arr['audit_remark']) ? $arr['audit_remark'] : '';
        $status = isset($arr['status']) ? $arr['status'] : 0;
        $level = isset($arr['level']) ? $arr['level'] : 0;
        $level_remark = isset($arr['level_remark']) ? $arr['level_remark'] : '';

        // TODO by sjp : 需要判断 arr 有没有多传其他值

        // 微信端发起
        if ($wxuser instanceof WxUser) {
            $wxuserid = $wxuser->id;
            $userid = $wxuser->userid;
            $patientid = $wxuser->patientid;
            $doctorid = $wxuser->doctorid;
        }

        // 其他端发起
        if ($patient instanceof Patient) {
            $patientid = $patient->id;
            $doctorid = $patient->doctorid;
            $diseaseid = $patient->diseaseid;
        }

        // 关联的医生和疾病
        if ($doctor instanceof Doctor) {
            $doctorid = $doctor->id;
            if ($patient instanceof Patient) {
                $pcard = PcardDao::getByPatientidDoctorid($patientid, $doctorid);
                if ($pcard instanceof Pcard) {
                    $diseaseid = $pcard->diseaseid;
                }
            }
        }

        // 如果需要, 则重新获取一下 Patient
        if (false == $patient instanceof Patient) {
            $patient = Patient::getById($patientid);
        }

        // 再次修正 doctorid, diseaseid
        if ($patient instanceof Patient) {

            if ($doctorid < 1) {
                $doctorid = $patient->doctorid;
            }

            if ($diseaseid < 1) {
                $diseaseid = $patient->diseaseid;
            }
        }

        // 关联实体
        if ($obj instanceof Entity) {
            $objtype = get_class($obj);
            $objid = $obj->id;
        }

        // 检查是否需要创建任务
        if (false == self::checkIsNeedCreateOpTask($patient, $optasktpl)) {
            return null;
        }

        // MARK: - #6216 法定节假日任务计划时间自动顺延
        // 肿瘤
        if (Disease::isCancer($patient->diseaseid)) {
            // 是否可以手动创建
            if ($optasktpl->is_can_handcreate == 1) {
                // 任务 是否节点流转
                $optask_is_flow = XContext::getSafeValue('optask_is_flow');
                // 任务 流转节点是否显示日期框
                $optask_is_show_next_plantime = XContext::getSafeValue('optask_is_show_next_plantime');

                // 是来自于节点流转，且不显示日期框
                if ($optask_is_flow == 1 && $optask_is_show_next_plantime != 1) {
                    $plantime = OpTaskService::skipHoliday($plantime);
                }

                // 使用完就清空
                XContext::setSafeValue('optask_is_flow', 0);
                XContext::setSafeValue('optask_is_show_next_plantime', 0);
            }
        }

        // 任务根节点
        $opnodeRoot = OpNodeDao::getByCodeOpTaskTplId('root', $optasktpl->id);
        $opnodeRootid = $opnodeRoot instanceof OpNode ? $opnodeRoot->id : 0;

        // 患者分组
        $pgroupid = $patient->getPgroupid();

        // 计算任务级别
        if (0 == $level) {
            list ($level, $level_remark) = self::calcLevel($patient, $optasktpl);
        }

        $row = [];
        $row['wxuserid'] = $wxuserid;
        $row['userid'] = $userid;
        $row["patientid"] = $patientid;
        $row["doctorid"] = $doctorid;
        $row["diseaseid"] = $diseaseid;
        $row["optasktplid"] = $optasktplid;
        $row["opnodeid"] = $opnodeRootid;
        $row["pgroupid"] = $pgroupid;
        $row["pipeid"] = $pipeid;
        $row['objtype'] = $objtype;
        $row['objid'] = $objid;
        $row['plantime'] = $plantime;
        $row["content"] = $content;
        $row["audit_remark"] = $audit_remark;
        $row["level"] = $level;
        $row["level_remark"] = $level_remark;
        $row["status"] = $status;
        $row['auditorid'] = $auditorid > 1 ? $auditorid : 0;
        $row['createauditorid'] = $auditorid;

        $optask = OpTask::createByBiz($row);

        if ($auditorid > 1) {
            // 异步创建运营操作日志
            $content = "【添加了 [{$optask->optasktpl->title}({$optask->id})({$optask->plantime})]任务】<br>";
            $row = [
                'auditorid' => $auditorid,
                'patientid' => $patient->id,
                'code' => 'optask',
                'content' => $content
            ];
            AuditorOpLog::nsqPush($row);
        }

        // 如果开启了自动消息的任务模版,生成第一个定时事件
        if ($optasktpl->is_auto_send == 1) {
            $optasktplcron = OpTaskTplCronDao::getByOptasktplidStep($optasktplid, 1);

            // 计算定时事件的执行时间
            if ($plantime > date('Y-m-d')) {
                $plan_exe_time = $plantime;
            } elseif ($plantime <= date('Y-m-d')) {
                if (time() >= strtotime(date('Y-m-d') . ' 09:55:00')) {
                    $plan_exe_time = date('Y-m-d', time() + 3600 * 24);
                } else {
                    $plan_exe_time = date('Y-m-d');
                }
            }

            if ($optasktplcron instanceof OpTaskTplCron) {
                $row = [];
                $row["optaskid"] = $optask->id;
                $row["optasktplcronid"] = $optasktplcron->id;
                $row["plan_exe_time"] = $plan_exe_time;
                $row["status"] = 0;
                $optaskcron = OpTaskCron::createByBiz($row);
            }
        }

        // opnode root节点创建日志
        if ($opnodeRoot instanceof OpNode) {
            // 记日志
            $row = [];
            $row['optaskid'] = $optask->id;
            $row['opnodeid'] = $opnodeRoot->id;
            $row['type'] = 'create';
            $row['auditorid'] = $optask->auditorid;
            $row['remark'] = "";
            $optaskopnodelog = OpTaskOpNodeLog::createByBiz($row);
        }

        // 任务日志记录
        OpTaskService::addOptLog($optask, "[任务创建] [obj][{$objtype}][{$objid}] [level={$level}] [plantime={$plantime}]", $auditorid);

        return $optask;
    }

    // 跳过节假日
    private static function skipHoliday($plantime) {
        // 如果遇到节假日，日期顺延
        $time = strtotime($plantime);
        $thedate = date('Y-m-d');
        $thetime = strtotime($thedate);
        // 如果计划时间>现在才进行时期顺延
        if ($time > $thetime) {
            // 相差天数
            $diff_day_cnt = ($time - $thetime) / 86400;
            $day_cnt = 1;
            while ($day_cnt <= $diff_day_cnt) {
                $thedate = date('Y-m-d', strtotime($thedate) + 86400);
                if (!FUtil::isHoliday($thedate)) {
                    $day_cnt++;
                }
            }

            $plantime = $thedate;
        }

        return $plantime;
    }

    // 是否需要任务
    private static function checkIsNeedCreateOpTask($patient, $optasktpl) {

        // patient 不存在
        if (false == $patient instanceof Patient) {
            Debug::warn("===== checkIsNeedCreate : patient不存在");
            return false;
        }

        // optasktpl 不存在
        if (false == $optasktpl instanceof OpTaskTpl) {
            Debug::warn("===== optasktpl不存在，请排查获取optasktpl时方法是否正确");
            return false;
        }

        // 默认模板必须生成
        if ($optasktpl->isDefault_optasktpl()) {
            return true;
        }

        // optasktpl 无效
        if ($optasktpl->isClosed()) {
            Debug::warn("===== optasktpl[{$optasktpl->getUnicode()}] 已为无效");
            return false;
        }

        // #4457 失活组、拒绝组不会生成新的任务 NMO
        if (in_array($patient->patientgroupid, [
            3,
            4])) {
            Debug::trace("===== #4457 失活组、拒绝组不会生成新的任务 NMO");
            return false;
        }

        // 无效患者, 不生成任务，消息任务除外
        if ($patient->isDoubt() && ($optasktpl->code != 'PatientMsg' || $optasktpl->subcode != 'message')) {
            return false;
        }

        // 黑名单患者, 不生成任务
        if ($patient->isOnTheBlackList()) {
            return false;
        }

        // 患者死亡，不创建任何新的任务，消息任务除外
        if ($patient->is_live == 0 && ($optasktpl->code != 'PatientMsg' || $optasktpl->subcode != 'message')) {
            Debug::trace("===== 患者死亡，不创建任何新的任务，消息任务除外");
            return false;
        }

        // 多动症, 检查
        if (1 == $patient->diseaseid) {
            $is_in_hezuo = $patient->isInHezuo("Lilly");

            // 礼来项目用户不生成分组作业任务
            $code = $optasktpl->code;
            if ($code == 'hwk' && $is_in_hezuo) {
                return false;
            }

            // 礼来项目用户加入项目时的服药时长是2个月（含）及以上，只生成消息任务
            if ($is_in_hezuo) {
                $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid('Lilly', $patient->id);

                if ($patient_hezuo->drug_monthcnt_when_create >= 2 && $code != 'PatientMsg' && $code != 'follow' && $code != 'wenzhen') {
                    return false;
                }
            }
        }

        return true;
    }

    private static function calcLevel(Patient $patient, OpTaskTpl $optasktpl) {

        // 方寸儿童管理服务平台方向基于患者当前等级生成什么level的任务
        // 默认level字段为2
        $level = 2;
        $level_remark = '';

        if (1 == $patient->diseaseid) {

            // Lilly 患者
            $is_in_hezuo = $patient->isInHezuo("Lilly");
            if ($is_in_hezuo) {
                $level = 5;
                $level_remark = 'Lilly 患者';
            }

            // lilly 医生, 电话医生
            // 首次电话任务生成的级别判断，是不是合作医生，是合作医生就生成level=5;
            $doctor = $patient->doctor;
            $is_hezuo = $doctor->isHezuo("Lilly");
            if ($is_hezuo && "firstTel" == $optasktpl->code) {
                $level = 5;
                $level_remark = 'Lilly 首次电话';
            }

            // 非lilly患者的消息任务
            if (false == $is_in_hezuo && 'PatientMsg' == $optasktpl->code && $patient->hasPayShopOrderNearlyDay(2)) {
                $level = 4;
                $level_remark = OpTask::getLevelRemark('afterpay');
            }

            // 需审核lilly首次电话任务患者的消息任务
            $optask = OpTaskDao::getOneByPatientUnicode($patient, 'firstTel:audit');
            if (false == $is_in_hezuo && 'PatientMsg' == $optasktpl->code && ($optask instanceof OpTask && 5 == $optask->level && 0 == $optask->status)) {
                $level = 5;
                $level_remark = 'Lilly(未审核) 患者消息';
            }
        }

        return array(
            $level,
            $level_remark);
    }

    // ----- 创建任务基本函数 ----- end -----

    // ----- 任务创建或修改 ----- begin -----

    // 创建或更新, 同一个Patient, 同一个OpTaskTpl, 只能有一个未关闭任务的情况
    public static function tryCreateOpTaskByPatient(Patient $patient, $unicode, $obj = null, $plantime = '', $auditorid = 0, $arr = []) {
        $optasktpl = OpTaskTplDao::getOneByUnicode($unicode);
        DBC::requireNotEmpty($optasktpl, "任务模板[{$unicode}]不存在");

        $optask = OpTaskDao::getOneByPatientOptasktpl($patient, $optasktpl, true);

        // 20180206: 因为$optask可能在本工作单元中已经被关闭了, 所以加了->isOpen的判断
        if ($optask instanceof OpTask && $optask->isOpen()) {
            $content = isset($arr['content']) ? $arr['content'] : '';
            self::resetOpTask($optask, $obj, $plantime, $content, $auditorid);
        } else {
            $optask = OpTaskService::createOpTaskByOpTaskTpl($wxuser = null, $patient, $doctor = null, $optasktpl, $obj, $plantime, $auditorid, $arr);
        }

        return $optask;
    }

    // 创建或更新, 同一个Patient, 同一个Doctor, 同一个OpTaskTpl, 只能有一个未关闭任务的情况
    public static function tryCreateOpTaskByPatientDoctor(Patient $patient, Doctor $doctor, $unicode, $obj = null, $plantime = '', $auditorid = 0, $arr = []) {
        $optasktpl = OpTaskTplDao::getOneByUnicode($unicode);
        DBC::requireNotEmpty($optasktpl, "任务模板[{$unicode}]不存在");

        // 如果有已存在的任务且未关闭, 则任务切换到初始状态
        $optask = OpTaskDao::getOneByPatientidDoctoridOptasktplid($patient->id, $doctor->id, $optasktpl->id);

        if ($optask instanceof OpTask && $optask->isOpen()) {
            $content = isset($arr['content']) ? $arr['content'] : '';
            self::resetOpTask($optask, $obj, $plantime, $content, $auditorid);
        } else {
            $optask = OpTaskService::createOpTaskByOpTaskTpl($wxuser = null, $patient, $doctor, $optasktpl, $obj, $plantime, $auditorid, $arr);
        }

        return $optask;
    }

    // 创建或更新, 同一个Obj, 只能有一个任务的情况
    public static function tryCreateOpTaskByObj($wxuser, $patient, $doctor, $unicode, Entity $obj, $plantime = '', $auditorid = 0, $arr = []) {
        $optasktpl = OpTaskTplDao::getOneByUnicode($unicode);
        DBC::requireNotEmpty($optasktpl, "任务模板[{$unicode}]不存在");

        // obj已关联的任务
        $optask = OpTaskDao::getOneByObj($obj);

        if ($optask instanceof OpTask) {
            $content = isset($arr['content']) ? $arr['content'] : '';
            self::resetOpTask($optask, $obj, $plantime, $content, $auditorid);
        } else {
            $optask = OpTaskService::createOpTaskByOpTaskTpl($wxuser, $patient, $doctor, $optasktpl, $obj, $plantime, $auditorid, $arr);
        }

        return $optask;
    }

    // 任务重置
    private static function resetOpTask(OpTask $optask, $obj, $plantime, $content = '', $auditorid) {
        // 计划完成时间
        $plantime = $plantime ? $plantime : date('Y-m-d');

        // 切换到初始状态
        OpTaskStatusService::changeStatus($optask, 0, $auditorid);

        // 切换节点
        $root_opnode = OpNodeDao::getByCodeOpTaskTplId('root', $optask->optasktpl->id);
        $optask->opnodeid = $root_opnode->id;

        // 更改计划时间
        $optask->plantime = $plantime;

        // 实体有更新
        if ($obj instanceof Entity) {
            $optask->objtype = get_class($obj);
            $optask->objid = $obj->id;
        }

        // 内容有更新
        if ($content) {
            $optask->content = $content;
        }

        // TODO by 任务重置 记个日志
        OpTaskService::addOptLog($optask, "[任务重置] : ...", $auditorid);
    }

    // ----- 任务创建或修改 ----- end -----

    // ----- 定制任务创建接口 ----- begin -----

    // 创建住院预约关联任务 OpTaskTpl [BedTkt:audit:bedtkt]
    // 运营端 或 患者端
    public static function tryCreateOpTask_audit_bedtkt(BedTkt $bedtkt, $wxuser = null, $auditorid = 0, $exArr = []) {
        // 没有用到
        // $pipe = PipeDao::getByEntity($bedtkt);
        $doctorid = $bedtkt->doctorid;

        // 477 王颖轶, 1002 李孝远
        if ($bedtkt->doctorid == 1002) {
            $doctorid = 477;
        }

        $doctor = Doctor::getById($doctorid);

        $arr = [];
        $arr['content'] = "患者期望于{$bedtkt->want_date}入院";

        // 生成任务: 住院预约审核 (实体唯一 BedTkt)
        $optask = OpTaskService::tryCreateOpTaskByObj($wxuser, $bedtkt->patient, $doctor, 'audit:bedtkt', $bedtkt, $plantime = '', $auditorid, $arr);

        return $optask;
    }

    // 创建复诊(加号单)提醒任务 OpTaskTpl [remind:RevisitTkt]
    public static function createOpTask_remind_RevisitTkt(RevisitTkt $revisittkt, $auditorid = 0, $exArr = []) {
        $thedate = $revisittkt->schedule->thedate;
        $day_7 = date('Y-m-d', strtotime($thedate) - 7 * 24 * 3600);
        $day_3 = date('Y-m-d', strtotime($thedate) - 3 * 24 * 3600);

        $plantime = "";
        if (strtotime(date('Y-m-d')) <= strtotime($day_7)) {
            $plantime = $day_7;
        } elseif (strtotime(date('Y-m-d')) <= strtotime($day_3)) {
            $plantime = $day_3;
        } else {
            return null;
        }

        // 生成任务: 复诊预约提醒
        return OpTaskService::createOpTaskByUnicode(null, $revisittkt->patient, $revisittkt->doctor, 'remind:RevisitTkt', $revisittkt, $plantime, $auditorid);
    }

    // 创建评估任务 OpTaskTpl [:evaluate:]
    public static function createOpTask_audit_evaluate(WxUser $wxuser, Paper $paper) {
        $thresholdtime = date('Y-m-d 19:00:00', time());

        if (time() > strtotime($thresholdtime)) {
            $fromtime = date('Y-m-d 12:00:00', time());
            $totime = date('Y-m-d 12:00:00', time() + 3600 * 24 * 1);
            $plantime = date('Y-m-d', time() + 3600 * 24 * 1);
        } else {
            $fromtime = date('Y-m-d 12:00:00', time() - 3600 * 24 * 1);
            $totime = date('Y-m-d 12:00:00', time());
            $plantime = date('Y-m-d', time());
        }

        $patient = $paper->patient;

        $optasks = OpTaskDao::getListByPatientUnicodeStatus($patient, 'audit:evaluate', 0, $fromtime, $totime);
        if (false == empty($optasks)) {
            return $optasks[0];
        }

        // 生成任务: 评估量表审核
        return OpTaskService::createWxUserOpTask($wxuser, 'audit:evaluate', $paper, $plantime);
    }

    // ----- 定制任务创建接口 ----- end -----

    // ----- 任务节点流转时, 生成新任务 ----- begin -----

    // 创建开始于约定治疗日期的[肿瘤不良反应治疗]任务
    public static function tryCreateOpTask_reaction_treat(Patient $patient, $obj = null, $auditorid = 0, $exArr = []) {
        // 页面传过来
        $plantime = XRequest::getValue('next_plantime', date('Y-m-d'));

        // 生成任务: 肿瘤不良反应治疗任务 (患者唯一)
        return OpTaskService::tryCreateOpTaskByPatient($patient, 'reaction:treat', $obj, $plantime, $auditorid);
    }

    // 创建7天后的[肿瘤不良反应观察]任务
    public static function tryCreateOpTask_reaction_observe(Patient $patient, $obj = null, $auditorid = 0, $exArr = []) {
        // 7天后
        $plantime = date('Y-m-d', time() + 86400 * 7);

        // 生成任务: 肿瘤不良反应观察任务 (患者唯一)
        return OpTaskService::tryCreateOpTaskByPatient($patient, 'reaction:observe', $obj, $plantime, $auditorid);
    }

    // 创建[血常规收集任务]
    public static function tryCreateOpTask_wbc_collection(Patient $patient, $obj = null, $auditorid = 0, $exArr = []) {
        $thedate = XRequest::getValue('next_plantime', date('Y-m-d')); // #5756

        // 如果当前日期($thedate)
        // +7天在下个方案前一周之内。即2周方案不会生成、3周方案在7天以上不会生成。4周方案在14天以上不会生成。
        if ($obj instanceof PatientRecord && $obj->type == 'chemo' && $obj->code == 'cancer') {
            $patientrecord = $obj;

            $cycle = $patientrecord->getValue('cycle');

            // 下次方案时间提前3天 #5826
            $dayTill = 21 - 3;
            switch ($cycle) {
                case '两周方案':
                    $dayTill = 14 - 3;
                    break;
                case '三周方案':
                    $dayTill = 21 - 3;
                    break;
                case '四周方案':
                    $dayTill = 28 - 3;
                    break;
                case '未知周期':
                    $dayTill = 21 - 3;
                    break;
            }

            $patientrecord_date = $patientrecord->thedate;

            // 截止日期
            $plantime_till = date('Y-m-d', strtotime($patientrecord_date) + 86400 * $dayTill);

            // 计划日期
            $plantime = date('Y-m-d', strtotime($thedate) + 86400 * 7);

            if ($plantime < $plantime_till) {
                // 生成任务: 血常规收集任务 (患者唯一)
                return OpTaskService::createPatientOpTask($patient, 'wbc:collection', $obj, $plantime, $auditorid);
            }
        }

        return null;
    }

    // 生成任务: 开始于治疗日期的[血常规治疗任务]任务
    public static function tryCreateOpTask_wbc_treat(Patient $patient, $obj, $auditorid = 0, $exArr = []) {
        // 页面传值
        $thedate = XRequest::getValue('next_plantime', date('Y-m-d'));
        $plantime = date('Y-m-d', strtotime($thedate) + 86400 * 3);

        // 生成任务: 血常规观察任务 (患者唯一)
        return OpTaskService::createPatientOpTask($patient, 'wbc:treat', $obj, $plantime, $auditorid);
    }

    // 生成任务: 血常规检查日期3天后的[血常规观察任务]任务
    public static function tryCreateOpTask_wbc_observe_after_3days(Patient $patient, $obj, $auditorid = 0, $exArr = []) {
        // 页面传值
        $thedate = XRequest::getValue('next_plantime', date('Y-m-d'));
        $plantime = date('Y-m-d', strtotime($thedate) + 86400 * 3);

        // 生成任务: 血常规观察任务 (患者唯一)
        return OpTaskService::createPatientOpTask($patient, 'wbc:observe', $obj, $plantime, $auditorid);
    }

    // 生成任务: 肿瘤定期随访任务 (患者唯一)
    public static function tryCreateOpTask_Regular_follow(Patient $patient, $auditorid = 0, $exArr = []) {
        $types = TagRef::getPatientType($patient);

        $plantime = '';
        if ($types['zhunbeihualiao']) {
            $plantime = date('Y-m-d', time() + 3600 * 24 * 7);
        } elseif ($types['zhunbeishoushu']) {
            $plantime = date('Y-m-d', time() + 3600 * 24 * 7);
        } elseif ($types['wuliaoqi']) {
            $plantime = date('Y-m-d', time() + 3600 * 24 * 24);
        } elseif ($types['shengcunstatus']) {
            $plantime = date('Y-m-d', time() + 3600 * 24 * 28);
        }

        if ($plantime) {
            // 生成任务: 肿瘤定期随访任务 (患者唯一)
            return OpTaskService::tryCreateOpTaskByPatient($patient, 'Regular:follow', null, $plantime, $auditorid);
        }

        return null;
    }

    // ----- 任务节点流转时, 生成新任务 ----- end -----

    // 尝试根据$cond 条件随机获取 $num 个 optaskid
    // 返回值为 optaskid 组成的数组
    public static function tryGetRandOptaskIds($optaskidsSql, $bind, $num) {
        $arr = array();

        $optaskids = Dao::queryValues($optaskidsSql, $bind);
        if (!empty($optaskids)) {
            if ($num > count($optaskids)) {
                shuffle($optaskids);
                $arr = $optaskids;
            } else {
                $keys = array_rand($optaskids, $num);
                foreach ($keys as $key) {
                    $arr[] = $optaskids[$key];
                }
            }
        }
        return $arr;
    }
}
