<?php

/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/1/26
 * Time: 16:35
 */
class OpTaskTpl_order_QuickConsultOrder extends OpTaskTplBase
{

    /**
     * 根任务=>处理中
     */
    public static function flow_root_to_dispose (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        $quickconsultorder = $optask->obj;
        if ($quickconsultorder instanceof QuickConsultOrder) {
            $quickconsultorder->accept($auditorid);

            // 给患者发送消息
            $wxuser = $quickconsultorder->wxuser;
            $auditor = Auditor::getById($auditorid);
            $content = '医生团队已经开始处理你的快速咨询，请保持电话和网络畅通，稍后会有专人通过微信或电话与你联系。';
            PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $auditor, $content);
        }
    }

    /**
     * 处理中=>完成
     */
    public static function flow_dispose_to_finish (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        $quickconsultorder = $optask->obj;
        if ($quickconsultorder instanceof QuickConsultOrder) {
            $quickconsultorder->finish();

            // 给患者发送消息
            $wxuser = $quickconsultorder->wxuser;
            $auditor = Auditor::getById($auditorid);
            $content = '本次快速咨询已处理完毕。感谢你对医生团队的支持与配合。如有其他问题，可再次与我们联系。';
            PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $auditor, $content);
        }
    }
}