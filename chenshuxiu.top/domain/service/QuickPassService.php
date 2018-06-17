<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/3/7
 * Time: 14:22
 */

class QuickPassService
{
    /**
     * 是否超时
     *
     * @param $starttime
     * @param $endtime
     * @return bool
     */
    public static function isTimeout($starttime, $endtime) {
        if ($endtime - $starttime > QuickPass_ServiceItem::appointedtime) {
            return true;
        }
        return false;
    }

    /**
     * 运营回复
     *
     * @param Patient $patient
     */
    public static function auditorReply(Patient $patient) {
        $optask = OpTaskDao::getOneByPatientUnicode($patient, 'PatientMsg:quickpass_msg');
        if ($optask instanceof OpTask) { // 有快速通行证任务
            $code = $optask->opnode->code;

            if ($code == 'finish' || $code == 'wait_patient_reply') { // 当前节点为 完成 || 等待患者回复 则不进行重复结算
                return;
            }

            // 节点流转了
            OpTaskEngine::flow_to_opnode($optask, 'wait_patient_reply');
        }
    }

    /**
     * 患者回复
     *
     * @param Patient $patient
     * @param WxUser $wxUser
     */
    public static function patientReply(Patient $patient, WxUser $wxUser, Pipe $pipe) {
        // 是否有未关闭的快速通行证任务
        $optask = OpTaskDao::getOneByPatientUnicode($patient, 'PatientMsg:quickpass_msg');
        if ($optask instanceof OpTask) { // 有快速通行证任务
            // 将任务节点流转到 等待运营回复
            $code = $optask->opnode->code;
            if ($code != 'root' && $code != 'wait_auditor_reply') { // 根节点、等待运营回复则不用流转
                OpTaskEngine::flow_to_opnode($optask, 'wait_auditor_reply');
            }
        } else {
            // 创建一条快速通行证任务
            $arr = [];
            $arr['pipeid'] = $pipe->id;
            $arr['level'] = 4;
            $arr['level_remark'] = '#5767 购买了快速通行证的患者，发送消息生成L4级别的快速通行证任务';

            $plantime = date('Y-m-d H:i:s');
            OpTaskService::createWxUserOpTask($wxUser, 'PatientMsg:quickpass_msg', $patient, $plantime, 0, $arr);
        }

        // 给运营发消息
        $content = "『快速通行证』{$patient->doctor->name}医生的患者{$patient->name}，发送了一条消息，请您及时处理。";
        $ename = 'QuickPass_ServiceOrder';
        PushMsgService::sendMsgToAuditorWithEnameBySystem($ename, $content);

        $userids = WebSocketService::getUseridsByEnameOfAuditorPushMsgTpl($ename);

        $title = "快速通行证患者消息";
        $body = "『快速通行证』{$patient->doctor->name}医生的患者{$patient->name}，发送了一条消息，请及时处理。";
        $tag = "quickpass_" . $patient->id;
        $data = [
            // 运营任务【快速通行证患者消息】，临时先用这个地址吧。
            'url' => Config::getConfig('audit_uri') . '/optaskmgr/listnew?optaskfilterid=609567566'
        ];
        $tpl = WebSocketService::getNotificationTpl($title, $body, $tag, $data);
        WebSocketService::push('wsquickpass', 'pushMessage', $tpl, $userids);
    }

    /**
     * 运营回复
     * 这个方法是在运营回复的时候，进行超时结算的，但是比较复杂，待完成。
     * 现方案为脚本扫描快速通行证任务，进行实时结算
     *
     * @param Patient $patient
     */
    public static function auditorReply_bak(Patient $patient) {
        $optask = OpTaskDao::getOneByPatientUnicode($patient, 'PatientMsg:quickpass_msg');
        if ($optask instanceof OpTask) { // 有快速通行证任务
            $code = $optask->opnode->code;

            if ($code == 'finish' || $code == 'wait_patient_reply') { // 当前节点为 完成 || 等待患者回复 则不进行重复结算
                return;
            }

            /* 1. 节点流转
             * 2. 看一下本月是否已经超时过，如果超时过，则不做后续处理；如果未超时过，则进行超时处理。
             * 3.
             *
             */

            // 先把节点流转了
            OpTaskEngine::flow_to_opnode($optask, 'wait_patient_reply');

            // 找到该月份的快速通行证服务
            $quickpass_serviceitem = QuickPass_ServiceItemDao::getValidOneByPatientAndTime($patient->id, date('Y-m-d H:i:s'));
            if ($quickpass_serviceitem instanceof QuickPass_ServiceItem) {

                if ($quickpass_serviceitem->is_timeout == 0) {  // item当前未超时

                    // TODO: 非工作时间生成的快速通行证任务 或 进行的节点流转，单独结算
                    // MARK: - 非公时间结算规则：下班时间发的消息，保证上班以后一个小时以内回复就不算超时


                    // 结算本次回复
                    if ($code == 'root') { // 根节点，以任务的创建时间为起始时间
                        if (QuickPass_ServiceItem::isWorkTime($optask->createtime)) {
                            $starttime = $optask->createtime;
                        } else { // 如果任务的创建时间为非公时间
                            if (FUtil::isHoliday()) { // 节假日

                            }
                            $hour_mintue_int = date('Hi', strtotime($optask->createtime));

                            $am_begin = QuickPass_ServiceItem::worktime['am']['start'];
                            $am_end = QuickPass_ServiceItem::worktime['am']['end'];
                            $pm_begin = QuickPass_ServiceItem::worktime['pm']['start'];
                            $pm_end = QuickPass_ServiceItem::worktime['pm']['end'];
                            if ($hour_mintue_int <= $am_begin && $hour_mintue_int >= $pm_end) { // 晚上非公时间创建的
                                $starttime = date('Y-m-d 10:00:00');
                            } elseif ($hour_mintue_int >= $am_end && $hour_mintue_int <= $pm_begin) { // 中午非公时间创建的
                                $starttime = date('Y-m-d 13:00:00');
                            }
                        }
                    } elseif ($code == 'wait_auditor_reply') { // 等待运营回复节点，以节点流转的时间为起始时间
                        $log = OpTaskOpNodeLogDao::getLastOneByOpTaskid($optask);
                        DBC::requireTrue($log instanceof OpTaskOpNodeLog, '未找到OpTaskOpNodeLog');
                        $starttime = $log->createtime;
                    } else { // 其它节点不会存在
                        DBC::requireTrue(false, '未知节点，无法结算，请修复');
                    }

                    $starttime = strtotime($starttime);

                    // 现在的时间为结束时间进行结算
                    $endtime = time();

                    // 是否超过一小时
                    if ($endtime - $starttime > 3600) {
                        // MARK: - 超过一小时，标记超时，由脚本进行实际退款
                        $quickpass_serviceitem->timeout($optask);
                    }

                } else { // item已经超时过，不需要进行结算，结束。

                }
            } else { // 未找到该月的快速通行证服务，不需要进行结算，结束。

            }

            // 再看本月之前是否超过时
            $quickpass_serviceitem = QuickPass_ServiceItemDao::getValidOneByPatientAndTime($patient->id, date('Y-m-d H:i:s'));
            if ($quickpass_serviceitem instanceof QuickPass_ServiceItem) {
                if ($quickpass_serviceitem->is_timeout == 0) {  // 之前未超时
                    $quickpass_serviceitem->timeout($optask);
                }
            }
            // 这里用任务的创建时间去找所在的item
        }

        // 是否拥有有效的快速通行证服务
        $quickpass_serviceitem = QuickPass_ServiceItemDao::getLastValidOneByPatientid($patient->id);
        if ($quickpass_serviceitem instanceof QuickPass_ServiceItem && $quickpass_serviceitem->isValidityPeriod()) {
            $optask = OpTaskDao::getOneByPatientUnicode($patient, 'PatientMsg:quickpass_msg');
            if ($optask instanceof OpTask) { // 有快速通行证任务
                $code = $optask->opnode->code;

                if ($code == 'finish' || $code == 'wait_patient_reply') { // 当前节点为 完成 || 等待患者回复 则不进行重复结算
                    return;
                }

                if ($code == 'root') {
                    // 以任务的创建时间为起始时间
                    $starttime = $optask->createtime;
                } elseif ($code == 'wait_auditor_reply') {
                    // 拿节点流转时间当做开始时间
                    $log = OpTaskOpNodeLogDao::getLastOneByOpTaskid($optask);
                    $starttime = $log->createtime;
                }

                $starttime = strtotime($starttime);

                // 现在的时间为结束时间进行结算
                $endtime = time();

                // 是否超过一小时
                if ($endtime - $starttime > 3600) {
                    // MARK: - 超过一小时，则打退款标记，由脚本进行实际退款
                    $quickpass_serviceitem->refund_optaskid = $optask->id;
                }
            }
        }
    }
}