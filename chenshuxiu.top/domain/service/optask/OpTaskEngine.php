<?php

// 任务工作流引擎
class OpTaskEngine
{

    // 任务节点切换(流转) optaskFlow
    public static function flow(OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        Debug::trace("===== OpTaskEngine::flow ===== begin =====");

        // #6216 法定节假日任务计划时间自动后延
        // 任务 是否流转
        XContext::setSafeValue('optask_is_flow', 1);
        // 任务 流转节点是否显示日期框
        XContext::setSafeValue('optask_is_show_next_plantime', $opnodeflow->to_opnode->is_show_next_plantime);

        $optasktpl = $optask->optasktpl;

        // 必须遵循配置流转
        if ($optask->opnodeid != $opnodeflow->from_opnodeid) {
            Debug::warn("optask[{$optask->id}]->opnode[{$optask->opnodeid}] <> flow[{$opnodeflow->id}]->from[{$opnodeflow->from_opnodeid}]");
            Debug::trace("===== OpTaskEngine::flow ===== end =====");
            return false;
        }

        if ($auditorid > 1) {
            // 异步创建运营操作日志
            $content = "【修改了 [{$optask->optasktpl->title}({$optask->id})({$optask->plantime})]任务 节点切换[{$optask->opnode->code}] => [{$opnodeflow->to_opnode->code}]】<br>";
            $row = [
                'auditorid' => $auditorid,
                'patientid' => $optask->patientid,
                'code' => 'optask',
                'content' => $content
            ];
            AuditorOpLog::nsqPush($row);
        }

        // 切换到新节点, TODO 按说应该放在 to_xxx_before后, to_xxx前
        $optask->opnodeid = $opnodeflow->to_opnodeid;

        // 节点切换日志
        // 节点切换日志与节点切换处理类存在与否无关
        $row = [];
        $row['optaskid'] = $optask->id;
        $row['opnodeid'] = $opnodeflow->to_opnodeid;
        $row['type'] = $opnodeflow->type;
        $row['auditorid'] = $auditorid;
        $row['remark'] = "";
        $optaskopnodelog = OpTaskOpNodeLog::createByBiz($row);

        // 任务日志记录
        $content = "[节点切换] [{$opnodeflow->from_opnode->title}] => [{$opnodeflow->to_opnode->title}]";
        if ($opnodeflow->to_opnode->code == 'appoint_follow') { // 约定跟进，把日期记到log里
            $content .= " [跟进时间=" . $exArr['next_plantime'] . "]";
        }
        OpTaskService::addOptLog($optask, $content, $auditorid);

        // 处理类存在?
        $class_name = "OpTaskTpl_{$optasktpl->code}_{$optasktpl->subcode}";
        if (class_exists($class_name)) {
            Debug::trace(json_encode($exArr));

            $methods = [];
            // 钩子1 : 离开节点时
            $methods[] = "leave_{$opnodeflow->from_opnode->code}";
            // 钩子2 : 流处理, 自己实现
            $methods[] = "flow_{$opnodeflow->from_opnode->code}_to_{$opnodeflow->to_opnode->code}";
            // 钩子3 : 进入节点时, 自己实现
            $methods[] = "to_{$opnodeflow->to_opnode->code}_before";
            // 钩子4 : 进入节点时, 可以自己实现
            $methods[] = "to_{$opnodeflow->to_opnode->code}";
            // 钩子5 : 进入节点时, 自己实现
            $methods[] = "to_{$opnodeflow->to_opnode->code}_after";

            foreach ($methods as $method) {
                if (method_exists($class_name, $method)) {
                    Debug::trace("===== {$class_name}::{$method} ===== begin =====");

                    $class_name::$method($optask, $opnodeflow, $auditorid, $exArr);

                    Debug::trace("===== {$class_name}::{$method} ===== end =====");
                } else {
                    Debug::trace("===== {$class_name}::{$method} method not exists  =====");
                }
            }
        } else {
            Debug::trace("===== {$class_name} class not exists  =====");

            $methods = [];
            // 钩子1 : 离开节点时
            $methods[] = "leave_{$opnodeflow->from_opnode->code}";
            // 钩子2 : 进入节点时
            $methods[] = "to_{$opnodeflow->to_opnode->code}";

            foreach ($methods as $method) {
                if (method_exists('OpTaskTplBase', $method)) {
                    Debug::trace("===== OpTaskTplBase::{$method} ===== begin =====");
                    OpTaskTplBase::$method($optask, $opnodeflow, $auditorid, $exArr);
                    Debug::trace("===== OpTaskTplBase::{$method} ===== end =====");
                }
            }
        }

        Debug::trace("===== OpTaskEngine::flow ===== end =====");
        return true;
    }

    // 任务节点切换(流转) flow_to_opnode
    public static function flow_to_opnode(OpTask $optask, $to_opnode_code, $auditorid = 0, $exArr = []) {
        Debug::trace("===== OpTaskEngine::flow_to_opnode ===== begin =====");

        $from_opnode = $optask->opnode;
        $to_opnode = OpNodeDao::getByCodeOpTaskTplId($to_opnode_code, $optask->optasktplid);

        $opnodeflow = OpNodeFlowDao::getByFrom_opnodeTo_opnode($from_opnode, $to_opnode);

        DBC::requireNotEmpty($opnodeflow, "任务流 OpTask[{$optask->id}]: [{$from_opnode->id}] => [{$to_opnode->id}], 不存在");

        OpTaskEngine::flow($optask, $opnodeflow, $auditorid, $exArr);

        Debug::trace("===== OpTaskEngine::flow_to_opnode ===== end =====");
    }
}
