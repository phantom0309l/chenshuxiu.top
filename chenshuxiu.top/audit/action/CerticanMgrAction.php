<?php
// CerticanMgrAction
class CerticanMgrAction extends AuditBaseAction
{

    public function doList4Patient () {
        $patientid = XRequest::getValue('patientid', '0');
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "patient is null");

        $certicans = CerticanDao::getListByPatient($patient);

        XContext::setValue('patient', $patient);
        XContext::setValue('certicans', $certicans);

        return self::SUCCESS;
    }

    public function doAddJson () {
        $patientid = XRequest::getValue('patientid', '0');
        $patient = Patient::getById($patientid);
        $pcard = PcardDao::getByPatientidDoctorid($patientid, $patient->doctorid);
        DBC::requireNotEmpty($patient, "patient is null");

        $title = XRequest::getValue('title', '');
        $sub_title = XRequest::getValue('sub_title', '');
        $begin_date = XRequest::getValue('begin_date', '');

        $row = [];
        $row['patientid'] = $patientid;
        $row['doctorid'] = $patient->doctorid;
        $row['title'] = $title;
        $row['sub_title'] = $sub_title;
        $row['begin_date'] = $begin_date;
        $certican = Certican::createByBiz($row);

        // 同时生成填写记录
        for ($i = 0; $i < 21; $i++) {
            $plan_date = date('Y-m-d', strtotime($begin_date) + 3600 * 24 * $i);
            $certicanitem = CerticanItemDao::getByCerticanPlan_date($certican, $plan_date);
            if ($certicanitem instanceof CerticanItem) {
                $certican->remove();
                echo 'fail';
                return self::BLANK;
            }

            $row = [];
            $row["certicanid"] =  $certican->id;
            $row["plan_date"] = date('Y-m-d', strtotime($begin_date) + 3600 * 24 * $i);
            $certicanitem = CerticanItem::createByBiz($row);
        }

        // 发送模板消息
        $wx_uri = Config::getConfig("wx_uri");
        $url = $wx_uri . '/certicanitem/list?certicanid=' . $certican->id;

        $first = array(
            "value" => "依维莫司服药及不良反应表",
            "color" => "");
        $keyword2 = "【依维莫司服药及不良反应表】[{$certican->begin_date}]请您按时定期填写该表。";

        $keywords = array(
            array(
                "value" => "{$patient->name}",
                "color" => "#ff6600"),
                array(
                    "value" => $keyword2,
                    "color" => "#ff6600"));
        $content = WxTemplateService::createTemplateContent($first, $keywords);

        PushMsgService::sendTplMsgToWxUsersOfPcardByAuditor($pcard, $this->myauditor, 'followupNotice', $content, $url);

        echo 'ok';

        return self::BLANK;
    }

    public function doDownloadExcelJson () {
        $certicanid = XRequest::getValue('certicanid', 0);
        $certican = Certican::getById($certicanid);
        DBC::requireNotEmpty($certican, "certican is null");

        $certicanitems = CerticanItemDao::getListByCertican($certican);
        $data = [];
        $i = 0;
        foreach ($certicanitems as $a) {
            $list = [];

            // 验血
            $wbc_str = '';
            if ($a->is_fill == 1) {
	           if ($a->wbc) {
	               $wbc_str = "wbc:" . $a->wbc;
	           } else {
	               if ($a->is_wbc == 1) {
    	               $wbc_str = "已验";
	               } else {
	                   $wbc_str = "未验";
	               }
	           }
    	   }

            // 升白针
            $white_str = '';
    	    if ($a->is_fill == 1) {
                if ($a->is_white == 1) {
                    if ($a->white_dose) {
                        $white_str = "已注射：" . $a->white_dose . "ml";
                    } else {
                        $white_str = "已注射";
                    }
                } else {
                   $white_str = "未注射";
                }
            }

            // 验血
            $platelet_str = '';
            if ($a->is_fill == 1) {
               if ($a->is_platelet == 1) {
            	   if ($a->platelet_dose) {
            		   $platelet_str = "已注射：" . $a->platelet_dose . "ml";
            	   } else {
            		   $platelet_str = "已注射";
            	   }
               } else {
            	   $platelet_str = "未注射";
               }
            }

            $list[] = $a->plan_date;
            $list[] = ++$i;
            $list[] = $a->is_fill == 1 ? $a->drug_dose . "mg" : '';
            $list[] = $a->is_fill == 1 ? $a->adverse_content : '';
            $list[] = $wbc_str;
            $list[] = $white_str;
            $list[] = $platelet_str;
            $list[] = $a->is_fill == 1 ? '✔' : '✘';

            $data[] = $list;
        }

        $headarr = [
            '日期',
            '化疗天数',
            '服药剂量',
            '不良反应',
            '验血',
            '注射升白针',
            '注射升血小板',
            '填写状态'
        ];
        if (count($data) > 0) {
            ExcelUtil::createForWeb($data, $headarr, $certican->patientid . "_" . $certican->begin_date);
        }

        return self::BLANK;
    }

    public function doOne () {
        return self::SUCCESS;
    }

    public function doModify () {
        return self::SUCCESS;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }
}
