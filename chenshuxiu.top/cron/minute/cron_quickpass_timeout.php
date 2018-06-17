<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/12/4
 * Time: 15:57
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';
class cron_quickpass_timeout extends CronBase
{

    private $allcnt = 0;

    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'minute';
        $row["title"] = '每分钟, 处理快速通行证消息超时';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog() {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog() {
        return $this->allcnt > 0;
    }

    // 模板方法的实现, 重载
    public function doworkImp() {
        $allcnt = 0;

        // 找到所有打开的快速通行证任务
        $optasks = OpTaskDao::getListByUnicode('PatientMsg:quickpass_msg');
        echo "\n";
        echo count($optasks);
        echo "\n";
        foreach ($optasks as $optask) {
            $patient = $optask->patient;
            if ($patient instanceof Patient) {
                $remark = '';
                $remark .= $patient->name;
                $this->cronlog_content .= $patient->name;
                echo $patient->name;
                // 先判断是否还有快速通行证服务
                if ($patient->has_valid_quickpass_service()) {
                    $remark .= " | 开通了快速通行证服务";
                    $this->cronlog_content .= " | 开通了快速通行证服务";
                    echo " | 开通了快速通行证服务";
                    // 再判断所在月份是否已经退过款
                    $quickpass_serviceitem = QuickPass_ServiceItemDao::getValidOneByPatientAndTime($patient->id, $optask->createtime);
                    if ($quickpass_serviceitem instanceof QuickPass_ServiceItem) {
                        $remark .= " | 找到 {$optask->createtime} 所在月份的快速通行服务item";
                        $this->cronlog_content .= " | 找到 {$optask->createtime} 所在月份的快速通行服务item";
                        echo " | 找到 {$optask->createtime} 所在月份的快速通行服务item";
                        // 如果item未退款
                        if (!$quickpass_serviceitem->isRefund()) { // 未退款
                            $remark .= " | 未退款";
                            $this->cronlog_content .= " | 未退款";
                            echo " | 未退款";
                            $starttime = '';

                            $code = $optask->opnode->code;
                            if ($code == 'root') { // 当前处于根节点
                                $remark .= " | 当前处于根节点";
                                $this->cronlog_content .= " | 当前处于根节点";
                                echo " | 当前处于根节点";
                                $starttime = $optask->createtime;
                            } elseif ($code == 'wait_auditor_reply') { // 当前处于等待运营回复
                                $remark .= " | 当前处于等待运营回复节点";
                                $this->cronlog_content .= " | 当前处于等待运营回复节点";
                                echo " | 当前处于等待运营回复节点";
                                // 找到任务最后一次的节点流转log
                                $log = OpTaskOpNodeLogDao::getLastOneByOpTaskid($optask);
                                if (false == $log instanceof OpTaskOpNodeLog) {
                                    $this->cronlog_content .= " | 【处理快速通行证消息超时】未找到OpTaskOpNodeLog";
                                    echo " | 【处理快速通行证消息超时】未找到OpTaskOpNodeLog";
                                    Debug::warn('【处理快速通行证消息超时】未找到OpTaskOpNodeLog');
                                } elseif ($optask->opnodeid != $log->opnodeid) {
                                    $this->cronlog_content .= " | 【处理快速通行证消息超时】当前任务上的opnodeid != OpTaskOpNodeLog上的opnodeid";
                                    echo " | 【处理快速通行证消息超时】当前任务上的opnodeid != OpTaskOpNodeLog上的opnodeid";
                                    Debug::warn('【处理快速通行证消息超时】当前任务上的opnodeid != OpTaskOpNodeLog上的opnodeid');
                                }

                                $starttime = $log->createtime;
                            } else {
                                $this->cronlog_content .= " | 当前处于{$code}，跳过";
                                echo " | 当前处于{$code}，跳过";
                                continue;
                            }

                            if ($this->isTimeout($starttime)) { // 超时
                                $remark .= " | 超时退款";
                                $this->cronlog_content .= " | 超时退款";
                                echo " | 超时退款";
                                // 仅标记超时，退款由人工完成。
                                $quickpass_serviceitem->setTimeout($optask);

                                $quickpass_serviceitem->remark = $remark;
                                // 退款
//                                $quickpass_serviceitem->timeoutRefund();
//                                $this->cronlog_content .= " | 退款完成";
//                                echo " | 退款完成";

                                $allcnt++;
                            } else {
                                $this->cronlog_content .= " | 未超时";
                                echo " | 未超时";
                            }
                        } else {
                            $this->cronlog_content .= " | 已经退过款了";
                            echo " | 已经退过款了";
                        }
                    } else {
                        $this->cronlog_content .= " | 未找到 {$optask->createtime} 所在月份的快速通行服务item";
                        echo " | 未找到 {$optask->createtime} 所在月份的快速通行服务item";
                    }
                } else {
                    $this->cronlog_content .= " | 未开通快速通行证服务";
                    echo " | 未开通快速通行证服务";
                }
            }
            echo "\n";
        }


        $this->cronlog_brief = "cnt={$allcnt}";

        return $this->allcnt = $allcnt;
    }

    /**
     * 是否超时
     *
     * @param $time optask或opnodeflow的createtime
     * @return bool
     */
    private function isTimeout($time) {
        $endtime = time();

        if (QuickPass_ServiceItem::isWorkTime($time)) { // 工作时间
            $starttime = strtotime($time);
            if (QuickPassService::isTimeout($starttime, $endtime)) { // 超时
                return true;
            }
        } else { // 非公时间
            if (QuickPass_ServiceItem::isWorkTime()) { // 现在是工作时间
                $hour_mintue_int = date('Hi', strtotime($time));

                $am_begin = QuickPass_ServiceItem::worktime['am']['start'];
                $am_end = QuickPass_ServiceItem::worktime['am']['end'];
                $pm_begin = QuickPass_ServiceItem::worktime['pm']['start'];
                $pm_end = QuickPass_ServiceItem::worktime['pm']['end'];
                if ($hour_mintue_int <= $am_begin && $hour_mintue_int >= $pm_end) { // 晚上非公时间创建的
                    $starttime = strtotime(date('Y-m-d 10:00:00'));
                } elseif ($hour_mintue_int >= $am_end && $hour_mintue_int <= $pm_begin) { // 中午非公时间创建的
                    $starttime = strtotime(date('Y-m-d 13:00:00'));
                }

                if (QuickPassService::isTimeout($starttime, $endtime)) { // 超时
                    return true;
                }
            }
        }
        return false;
    }
}

// //////////////////////////////////////////////////////

$process = new cron_quickpass_timeout(__FILE__);
$process->dowork();
