<?php
/*
 * 催报到脚本实现 能使用的模板变量 offsetminutes offsethours offsetdays pushtime pushcontent
 * 可替换关键字 ##doctor##
 */
class CuiBaodaoCronTask extends CronTaskBase
{

    public function doWork (CronTask $crontask) {

        $vararray = $crontask->cronprocess->getVarArray();

        $pushcontent = $vararray['pushcontent'];

        $wxuser = $crontask->wxuser;
        Comment::addXuzheComment("[CuiBaodaoCronTask] [1] [{$crontask->id}] [{$wxuser->id}] ");
        // //////////////////////////////////////////////////////////////

        $doctorname = "主治";
        $ref_objid = $wxuser->ref_objid;
        if ($ref_objid) {
            $doctor = Doctor::getByid($ref_objid);
            if ($doctor instanceof Doctor) {
                $doctorname = $doctor->name;
                if ($doctor->id == 9)
                    return;
            }
        }

        $content = str_replace('##doctor##', $doctorname, $pushcontent);

        $wx_uri = Config::getConfig("wx_uri");
        $content = $content . " 报到地址：{$wx_uri}/baodao/baodao?gh=gh_9ab5c3a16ec6&openid={$wxuser->openid}";

        PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);

        $crontask->content = $content;

        Comment::addXuzheComment("[CuiBaodaoCronTask] [2] [{$crontask->id}] [{$wxuser->id}] {$content}");
    }
}
