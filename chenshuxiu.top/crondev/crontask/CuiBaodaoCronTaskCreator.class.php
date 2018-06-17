<?php
/*
 * 催报到脚本模板 ########################################### 能提供的模板变量 [1]
 * offsetminutes 间隔分钟 [2] offsethours 间隔小时 [3] offsetdays 间隔天 [4] pushtime 推送时刻
 * [5] pushcontent 推送文本 可替换关键字 ##doctor##
 * ########################################### 与 CuiBaodaoCronTask 互为一对
 * ########################################### 提供给运营配置的说明书 20160330
 * 此模板用于配置不同疾病的催报到脚本，目前支持按间隔分钟/小时/天，或每天的某一时刻推送。 并且在同一疾病下，支持针对没有报到患者的若干个的推送脚本组合。
 * 注意：以下所有变量请看说明慎重填写 offsetminutes 是间隔分钟，他指定脚本在未报到用户在关注后间隔多长时间（分钟）推送消息。
 * 此变量如果使用，offsethours，offsetdays，pushtime 将无效。如果不需要使用此变量，请填入数字0
 * offsethours是间隔小时，他指定脚本在未报到用户在关注后间隔多长时间（小时）推送消息。 此变量如果使用，
 * offsetdays，pushtime将无效。如果不需要使用此变量，请填入数字0 offsetdays
 * 是间隔天，他指定脚本在未报到用户在关注后间隔多长时间（天）推送消息。 此变量必须和 pushtime
 * 一起使用,表示隔多少天后的几点推送。如果不需要使用此变量，请填入数字0 pushtime 是推送时刻，他指定某一时刻推送。如果 offsetdays 为
 * 0 ，则是每天的 pushtime 指定时刻都会执行一次。 如果 offsetdays 不为 0，则表示隔多少天后的几点推送，时刻由 pushtime
 * 指定。格式为 20:01:01 pushcontent 是推送的文本。运营可使用 符号 ##doctor## 来确定医生姓名将要出现的位置。
 * 目前默认规则： [1] 一个微信用户在相同脚本配置下生成的脚本中(在当前模板创建的脚本中，以疾病和序列号作为唯一区分)，只会被推送一次 [2]
 * 一个微信用户在当前脚本配置下，如果同一个系列（疾病）中，序列号（pos）比当前小的脚本还未推送，则当前序列号（pos）的脚本不会执行
 */
class CuiBaodaoCronTaskCreator extends CronTaskCreatorBase
{

    public function doWork (CronProcess $cronprocess) {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $diseaseid = $cronprocess->diseaseid;
        $vararray = $cronprocess->getVarArray();

        $offsetminutes = $vararray['offsetminutes'];
        $offsethours = $vararray['offsethours'];
        $offsetdays = $vararray['offsetdays'];
        $pushtime = $vararray['pushtime'];

        Comment::addXuzheComment("[CuiBaodaoCronTaskCreator] [1] {$cronprocess->id} ");

        $lastcronprocessid = 0;
        if ($cronprocess->pos > 1) {
            $lastcronprocess = CronProcessDao::getByTasktypeDiseaseid($cronprocess->tasktype, $diseaseid, $cronprocess->pos - 1);
            $lastcronprocessid = $lastcronprocess->id;
        }

        Comment::addXuzheComment("[CuiBaodaoCronTaskCreator] [2] {$cronprocess->id} ## {$lastcronprocessid} ");

        $cond = '';
        if ($diseaseid > 0) {
            $wxshop = WxShopDao::getByDiseaseid($diseaseid);
            $cond .= " and w.wxshopid = {$wxshop->id}";
        }
        if ($offsetminutes > 0) {
            $offsetminutesbefore = date('Y-m-d H:i:s', time() - $offsetminutes * 60);
            $cond .= " and w.createtime <= '{$offsetminutesbefore}'";
        } elseif ($offsethours > 0) {
            $offsethoursbefore = date('Y-m-d H:i:s', time() - $offsethours * 60 * 60);
            $cond .= " and w.createtime <= '{$offsethoursbefore}'";
        } else {
            if ($offsetdays > 0) {
                $offsetdaysbefore = date('Y-m-d 00:00:00', time() - ($offsetdays - 1) * 60 * 60 * 24);
                $cond .= " and w.createtime <= '{$offsetdaysbefore}' ";
            }
            $now = date('H:i:s');
            $cond .= " and '{$pushtime}' <= '{$now}' ";
        }

        $cond .= ' AND w.userid > 10000 and  w.userid < 20000  ';

        $sql = "SELECT w.* FROM wxusers w
             inner JOIN users u ON u.id = w.userid
             WHERE u.patientid = 0 AND w.ref_pcode = 'DoctorCard' " . $cond;

        Comment::addXuzheComment("[CuiBaodaoCronTaskCreator] [3] {$cronprocess->id} ## {$sql} ");

        $wxusers = Dao::loadEntityList('WxUser', $sql);

        foreach ($wxusers as $wxuser) {

            // 已经创建过crontask的跳过
            $thiscrontask = CronTaskDao::getByCronprocessidWxuserid($cronprocess->id, $wxuser->id);
            if ($thiscrontask instanceof CronTask) {
                continue;
            }

            // 上一个pos的crontask没有生成的也跳过
            if ($lastcronprocessid) {
                $lastcrontask = CronTaskDao::getByCronprocessidWxuserid($lastcronprocessid, $wxuser->id);
                if (false == $lastcrontask instanceof CronTask) {
                    continue;
                }
            }

            $row = array(
                'wxuserid' => $wxuser->id,
                'userid' => $wxuser->userid,
                'patientid' => $wxuser->user->patientid,
                'doctorid' => $wxuser->doctorid, // done pcard fix
                'cronprocessid' => $cronprocess->id,
                'tasktype' => $cronprocess->tasktype,
                'iswait' => 1,
                'isdone' => 0);

            $crontask = CronTask::createByBiz($row);

            // 老数据,生成任务,直接置关闭.
            if ($crontask->wxuser->createtime < '2016-04-09 00:00:00') {
                $crontask->iswait = 0;
                $crontask->isdone = 1;
                $crontask->content = '老数据任务,直接关闭';
            }

            Comment::addXuzheComment("[CuiBaodaoCronTaskCreator] [4] {$cronprocess->id} ## {$crontask->id} ");
        }

        $unitofwork->commitAndInit();
    }
}
