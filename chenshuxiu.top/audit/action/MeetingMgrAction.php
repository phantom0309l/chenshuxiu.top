<?php

/**
 * Created by PhpStorm.
 * User: fhw
 * Date: 16-6-1
 * Time: 下午2:04
 */

class MeetingMgrAction extends AuditBaseAction
{

    public function doList()
    {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', '');
        $patient_name = XRequest::getValue('patient_name', '');

        $sql = "SELECT p.patientid, p.doctorid, IFNULL(tt.cnt,0) AS cnt, tt.lastmeetingtime
                FROM pcards p
                LEFT JOIN (
                  select patientid , max(createtime) as lastmeetingtime, count(*) as cnt
                  from meetings
                  group by patientid
                ) tt ON tt.patientid = p.patientid
                where tt.cnt > 0 ";
        $cond = " ";
        $bind = [];

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

        $sql .= $cond;
        $countSql = $sql;
        $offset = ($pagenum - 1) * $pagesize;
        $sql .= " ORDER BY tt.lastmeetingtime DESC limit {$offset} , {$pagesize}";

        $meeting_group = Dao::queryRows($sql, $bind);

        // 翻页begin
        $cnt = count(Dao::queryRows($countSql, $bind));
        $url = "/meetingmgr/list?doctorid={$doctorid}&patient_name={$patient_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        // 翻页end

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('patient_name', $patient_name);
        XContext::setValue('meeting_group', $meeting_group);

        return self::SUCCESS;
    }

    public function doOnehtml()
    {
        $patientid = XRequest::getValue('patientid', 0);

        $patient = Patient::getById($patientid);
        $meetings = MeetingDao::getListByPatient($patientid);

        Xcontext::setValue('meetings', $meetings);
        Xcontext::setValue('patient', $patient);

        return self::SUCCESS;
    }

    /*天润融通*/
    //外呼接口
    public function doCdrCallJson () {
        $mobile = XRequest::getValue('mobile', '');
        $type = XRequest::getValue('type', '');
        DBC::requireNotEmpty($mobile,'mobile 为空');

        $enterpriseId = Config::getConfig('cdr_enterpriseid');
        $cno = $this->myauditor->$type;
        $pwd = Config::getConfig('cdr_cno_pwd');
        $pwdmd5 = md5($pwd);
        $customerNumber = $mobile;
        $sync = 0;

        $params = array (
            'enterpriseId' => $enterpriseId,
            'cno' => $cno,
            'pwd' => $pwdmd5,
            'customerNumber' => $customerNumber,
            'sync' => $sync,
        );

        $url = "http://api.clink.cn/interface/PreviewOutcall";

        //因为对方是异步调用，暂时不做超时处理了
        $str = FUtil::curlGet($url, $params, 5);
        $result = json_decode($str, true);

        $cdrstatuscode = CdrMeeting::getCdrStatusArr();

        if ($result['res'] == 0) {
            echo $cdrstatuscode["0{$sync}"];
        } elseif ($cdrstatuscode["{$result['res']}"]) {
            echo $cdrstatuscode["{$result['res']}"];
        } else {
            Debug::warn(__METHOD__ . " request: $url result: $str");
        }

        return self::BLANK;
    }

    //获取在线坐席
    public function doOnlineSeatsJson() {
        $enterpriseId = Config::getConfig('cdr_enterpriseid');
        $userName = Config::getConfig('cdr_userame');
        $pwd = Config::getConfig('cdr_pwd');
        $pwdEncrypted = md5($pwd);

        $list = array();
        foreach (Config::getConfig('cdr_queue') as $queueid) {
            $queueQid = $enterpriseId . $queueid;
            $url = "http://api.clink.cn/interface/queueMonitoring/QueueMonitoring";
            $params = array (
                'enterpriseId' => $enterpriseId,
                'userName' => $userName,
                'pwd' => $pwdEncrypted,
                'queueQids' => $queueQid,
            );
            $ret = FUtil::curlGet($url, $params, 5);
            $arr = json_decode($ret, true);
            if (!$arr['queueStatus'][0]['memberStatus']) {
                $this->result['errno'] = -1;
                $this->result['errmsg'] = '获取坐席数据失败';
                return self::TEXTJSON;
            }
            foreach ($arr['queueStatus'][0]['memberStatus'] as $one) {
                if ($one['loginStatus'] != 'offline') {
                    $seat = array (
                        'cid' => $one['cid'],
                        'cno' => str_replace($enterpriseId, '', $one['cid']),
                        'cname' => $one['cname'],
                        'statusDesc' => !$one['pauseDescription'] ? '空闲' : $one['pauseDescription'],
                    );
                    $list[] = $seat;
                }
            }
        }

        $this->result['data'] = $list;

        return self::TEXTJSON;
    }
}
