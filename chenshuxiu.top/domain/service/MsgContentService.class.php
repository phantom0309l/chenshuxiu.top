<?php
// 名称: MsgContentService
// 备注: 消息模板服务类
// 创建: 20171026 by lj
class MsgContentService
{
    // 文本替换
    public static function transform4RevisitTkt ( $content, $revisittkt ){
        $patient = $revisittkt->patient;
        $doctor = $revisittkt->doctor;
        $schedule = $revisittkt->schedule;
        $scheduletpl = $schedule->scheduletpl;

        $content = str_replace('#patient_name#', $patient->name, $content);
        $content = str_replace('#thedate#', $revisittkt->thedate, $content);
        $content = str_replace('#doctor_name#', $doctor->name, $content);

        $content = str_replace('#begin_hour#', $schedule->getDaypartStr(), $content);
        $content = str_replace('#address#', $scheduletpl->tip, $content);
        $content = str_replace('#dow#', $schedule->getDowStr(), $content);

        return $content;
    }
}
