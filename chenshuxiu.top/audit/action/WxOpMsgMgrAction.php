<?php

class WxOpMsgMgrAction extends AuditBaseAction
{

    // 医助消息列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $patient_name = XRequest::getValue('patient_name', '');

        if ($this->mydisease instanceof Disease) {
            $doctors = DoctorDao::getListByDiseaseid($this->mydisease->id);
        }

        // $sql = "SELECT max(id) as id,max(createtime) as
        // createtime,doctorid,patientid
        // FROM wxopmsgs
        // WHERE 1 = 1 ";
        $sql = "SELECT t.maxid as wxopmsgid, p.patientid, p.doctorid,
                    IFNULL(t.cnt,0) AS cnt, t.lastreplytime as createtime
                FROM pcards p
                LEFT JOIN (
                    SELECT max(id) as maxid, MAX(createtime) AS lastreplytime, COUNT(*) AS cnt, patientid
  	                FROM wxopmsgs
                    where status = 1
                    GROUP BY patientid
	              ) t ON t.patientid = p.patientid
                where 1 = 1
        ";

        $bind = [];

        $cond = " and p.patient_name != '' ";

        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond .= " and p.diseaseid in ($diseaseidstr) ";

        if ($doctorid) {
            $cond .= " and p.doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($patient_name) {
            $cond .= " and p.patient_name like :patient_name ";
            $bind[':patient_name'] = "%{$patient_name}%";
        }

        // $sql .= " group by doctorid,patientid order by createtime desc";

        $sql .= $cond;
        $offset = ($pagenum - 1) * $pagesize;
        $sql .= " ORDER BY t.lastreplytime DESC limit {$offset} , {$pagesize}";

        $wxopmsg_group = Dao::queryRows($sql, $bind);

        // 翻页begin
        $countSql = "select count(*) as cnt from pcards p where 1=1 and status = 1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/wxopmsgmgr/list?doctorid={$doctorid}&patient_name={$patient_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        // 翻页end

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('patient_name', $patient_name);
        XContext::setValue('doctors', $doctors);
        XContext::setValue('wxopmsg_group', $wxopmsg_group);

        return self::SUCCESS;
    }

    // 医助发送消息
    public function doReply () {
        $patientid = XRequest::getValue('patientid', 0);

        $patient = Patient::getById($patientid);

        XContext::setValue('patient', $patient);

        return self::SUCCESS;
    }

    // 医助发送消息
    public function doReplyOptask () {
        $patientid = XRequest::getValue('patientid', 0);

        $patient = Patient::getById($patientid);

        XContext::setValue('patient', $patient);

        return self::SUCCESS;
    }

    // 列表 of 患者
    public function doListHtml () {
        $patientid = XRequest::getValue('patientid', 0);
        $page_size = XRequest::getValue('page_size', 100);
        $offsetcreatetime = XRequest::getValue('offsetcreatetime', '0000-00-00 00:00:00');

        $page_size = intval($page_size);

        $patient = Patient::getById($patientid);

        $cond = "";
        $bind = [];

        if ($patientid) {
            $cond .= " and patientid = :patientid";
            $bind[':patientid'] = $patientid;
        }

        if ($offsetcreatetime != '0000-00-00 00:00:00') {
            $cond .= " and createtime < :createtime ";
            $bind[':createtime'] = $offsetcreatetime;
        }

        $cond .= " and status = 1 order by createtime desc limit {$page_size}";

        $wxopmsgs = Dao::getEntityListByCond('WxOpMsg', $cond, $bind);

        XContext::setValue('doctorid', $patient->doctorid);
        XContext::setValue('patient', $patient);
        XContext::setValue('wxopmsgs', $wxopmsgs);

        return self::SUCCESS;
    }

    // 发送消息
    public function doReplyJson () {
        $patientid = XRequest::getValue('patientid', 0);
        $content = XRequest::getValue('content', '');

        $auditorid = $this->myauditor->id;
        $patient = Patient::getById($patientid);

        $doctorid = $patient->doctorid;
        $diseaseid = $patient->diseaseid;

        $row = array();
        $row['patientid'] = $patientid;
        $row['doctorid'] = $doctorid;
        $row['auditorid'] = $this->myauditor->id;
        $row['content'] = $content;
        $row['isnew'] = 1;
        $row['status'] = 1;

        $wxopmsg = WxOpMsg::createByBiz($row);

        $pipe = Pipe::createByEntity($wxopmsg);

        $unitOfWork = BeanFinder::get('UnitOfWork');
        $unitOfWork->commitAndInit();

        if ($pipe instanceof Pipe) {

            // 调用小米推送的方法
            // done pcard fix
            $mipushresult = MiPush::pushMessage($doctorid, $diseaseid, $patientid, $patient->name, $pipe->id);

            // done pcard fix
            UmengPush::push2Ios($doctorid, $diseaseid, $patientid, $patient->name, $pipe->id);

            // 工作单元提交了 失败的状态就治不了？？？？
            if ($mipushresult == "error") {
                $wxopmsg = WxOpMsg::getById($wxopmsg->id);
                $wxopmsg->status = 0;

                echo "failed";
                return self::BLANK;
            }
        }

        echo "fine";
        return self::BLANK;
    }
}
