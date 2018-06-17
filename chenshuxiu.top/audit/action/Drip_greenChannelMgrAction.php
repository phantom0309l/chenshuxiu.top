<?php

// Drip_greenChannelAction
class Drip_greenChannelMgrAction extends AuditBaseAction
{

    public function doConfirmPostJson() {
        $drip_greenchannelid = XRequest::getValue('drip_greenchannelid');
        $drip_greenchannel = Drip_greenChannel::getById($drip_greenchannelid);
        if (false == $drip_greenchannel instanceof Drip_greenChannel) {
            $this->returnError('绿色通道申请不存在');
        }

        $patientid = XRequest::getValue('patientid');
        $patient = Patient::getById($patientid);
        if (false == $patient instanceof Patient) {
            $this->returnError('患者不存在');
        }

        $actualdate = XRequest::getValue('actualdate');
        if (!$actualdate) {
            $this->returnError('请选择实际就诊日期');
        }

        $content = XRequest::getValue('content');
        if (!$content) {
            $this->returnError('请填写发送给患者的内容');
        }

        $drip_greenchannel->status = 2;
        $drip_greenchannel->actualdate = $actualdate;
        $drip_greenchannel->content = $content;

        $content = "[就诊日期：{$actualdate}]" . $content;
        PushMsgService::sendTxtMsgToPatientBySystem($patient, $content);

        return self::TEXTJSON;
    }

    public function doList() {
        $drip_greenChannels = Drip_greenChannelDao::getAllList();

        XContext::setValue('drip_greenChannels', $drip_greenChannels);
        return self::SUCCESS;
    }
}
