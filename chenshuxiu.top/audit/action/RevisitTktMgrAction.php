<?php

// RevisitTktMgrAction
class RevisitTktMgrAction extends AuditBaseAction
{

    public function doList () {
        $word = XRequest::getValue('word', '');
        $doctorid = XRequest::getValue('doctorid', 0);
        $status = XRequest::getValue('status', 1);
        $isclosed = XRequest::getValue('isclosed', - 1);
        $auditstatus = XRequest::getValue('auditstatus', - 1);
        $fromdate = XRequest::getValue('fromdate', '');
        $todate = XRequest::getValue('todate', '');

        $cond = "";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();

        $cond .= " and patientid in (
            select patientid
            from pcards
            where diseaseid in ($diseaseidstr)
        ) ";

        if ($doctorid) {
            $cond .= " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($status != - 1) {
            $cond .= " and status = :status ";
            $bind[':status'] = $status;
        }

        if ($isclosed != - 1) {
            $cond .= " and isclosed = :isclosed ";
            $bind[':isclosed'] = $isclosed;
        }

        if ($auditstatus != - 1) {
            $cond .= " and auditstatus = :auditstatus ";
            $bind[':auditstatus'] = $auditstatus;
        }

        // 开始日期
        if ($fromdate) {
            $cond .= ' and thedate >= :fromdate ';
            $bind[':fromdate'] = $fromdate;
        }

        // 截至日期
        if ($todate) {
            $cond .= ' and thedate <= :todate ';
            $bind[':todate'] = $todate;
        }

        $sql = "select * from revisittkts where 1 = 1 " . $cond;

        if ($word) {
            $doctorid = 0;
            $status = - 1;
            $isclosed = - 1;
            $auditstatus = - 1;
            $fromdate = '';
            $todate = '';

            $cond = " left join users u on u.id = r.userid
                      left join patients p on p.id = r.patientid
                      where p.id in (
                          select patientid
                          from xpatientindexs
                          where word = :word
                      ) ";
            $bind = [];
            $bind[':word'] = $word;

            $sql = " select distinct r.* from revisittkts r " . $cond;
        }

        $sql .= " order by id asc";

        $revisittkts = Dao::loadEntityList('RevisitTkt', $sql, $bind);

        XContext::setValue('word', $word);
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('status', $status);
        XContext::setValue('isclosed', $isclosed);
        XContext::setValue('auditstatus', $auditstatus);
        XContext::setValue('fromdate', $fromdate);
        XContext::setValue('todate', $todate);
        XContext::setValue('revisittkts', $revisittkts);

        return self::SUCCESS;
    }

    public function doOneHtml () {
        $revisittktid = XRequest::getValue('revisittktid', 0);

        $revisittkt = RevisitTkt::getById($revisittktid);

        XContext::setValue('revisittkt', $revisittkt);

        return self::SUCCESS;
    }

    public function doModify () {
        $revisittktid = XRequest::getValue('revisittktid', 0);

        $revisittkt = RevisitTkt::getById($revisittktid);

        XContext::setValue('revisittkt', $revisittkt);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $revisittktid = XRequest::getValue('revisittktid', 0);
        $isclosed = XRequest::getValue('isclosed', - 1);
        $closeremark = XRequest::getValue('closeremark', '');
        $auditstatus = XRequest::getValue('auditstatus', - 1);
        $auditremark = XRequest::getValue('auditremark', '');

        $revisittkt = RevisitTkt::getById($revisittktid);
        $revisittkt->isclosed = $isclosed;
        $revisittkt->closeremark = $closeremark;
        $revisittkt->auditstatus = $auditstatus;
        $revisittkt->auditremark = $auditremark;

        if ($isclosed == 1) {
            $revisittkt->status = 0;
        }

        if ($auditstatus == 2) {
            $revisittkt->isclosed = 1;
        }

        XContext::setJumpPath('/revisittktmgr/list');
    }

    public function doRefuseJson () {
        $myauditor = $this->myauditor;

        $revisittktid = XRequest::getValue("revisittktid", 0);
        $auditremark = XRequest::getValue("auditremark", '');

        $revisittkt = RevisitTkt::getById($revisittktid);
        $revisittkt->refuse($auditremark);
        $revisittkt->set4lock('auditorid', $myauditor->id);

        echo "fine";
        return self::BLANK;
    }

    public function doPassJson () {
        $revisittktid = XRequest::getValue("revisittktid", 0);
        $revisittkt = RevisitTkt::getById($revisittktid);

        $myauditor = $this->myauditor;

        // MARK: - 因为#4397的需求，我把这段代码抽到了 RevisitTktService, 方便患者端服用 by lkt
        RevisitTktService::auditPass($myauditor, $revisittkt);

        echo "fine";
        return self::BLANK;
    }

    public function doConfirmJson () {
        $revisittktid = XRequest::getValue("revisittktid", 0);
        $auditremark = XRequest::getValue("auditremark", '');

        $revisittkt = RevisitTkt::getById($revisittktid);
        $revisittkt->status = 1;

        echo "fine";
        return self::BLANK;
    }

    public function doCancelJson () {
        $revisittktid = XRequest::getValue("revisittktid", 0);

        $revisittkt = RevisitTkt::getById($revisittktid);
        $revisittkt->status = 0;

        echo "fine";
        return self::BLANK;
    }

    public function doModifythedateJson () {
        $revisittktid = XRequest::getValue("revisittktid", 0);
        $thedate = XRequest::getValue("thedate", '0000-00-00');

        $revisittkt = RevisitTkt::getById($revisittktid);
        if (false == $revisittkt instanceof RevisitTkt) {
            echo "fail";
            return self::BLANK;
        }
        $schedule = ScheduleDao::getByDoctorThedate($revisittkt->doctor, $thedate);
        if ($schedule instanceof Schedule) {
            $revisittkt->thedate = $thedate;
            $revisittkt->set4lock('scheduleid', $schedule->id);
            $revisittkt->send_cnt = 0;

            // 删除[复诊预约提醒]任务, 保持唯一
            $optask = OpTaskDao::getOneByObjUnicode($revisittkt, 'remind:RevisitTkt', false);
            if ($optask instanceof OpTask) {
                $optask->remove();
            }

            // 重新生成任务: 复诊预约提醒
            OpTaskService::createOpTask_remind_RevisitTkt($revisittkt, $this->myauditor->id);

            echo "success";
        } else {
            echo "fail";
        }

        return self::BLANK;
    }
}
