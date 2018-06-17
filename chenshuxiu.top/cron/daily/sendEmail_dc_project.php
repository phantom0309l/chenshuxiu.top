<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 0);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");
require_once(ROOT_TOP_PATH . "/../core/util/email/class.phpmailer.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class SendEmail_dc_project extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::rpt;
        $row["when"] = 'month';
        $row["title"] = '每天, 11:30, 发送邮件, 患者项目收集结果';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    public function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = self::getIds();
//        $ids = [488138756];

        foreach ($ids as $i => $id) {
            $dc_patientplan = Dc_patientPlan::getById($id);
            $dc_patientplan->dc_patientplan_status = 1;

            $data = self::getPaperData($dc_patientplan);
            $time = time();
            $fileName = "{$dc_patientplan->patient->id}_{$time}.xls";
            $fileUrl = "/tmp/dc_patientplan/{$fileName}";

            ExcelUtil::createExcelImp($data, $fileUrl);

            $report_email = $dc_patientplan->dc_doctorproject->dc_project->report_email;
            $emailTitle = "{$dc_patientplan->patient->name}患者【{{$dc_patientplan->dc_doctorproject->dc_project->title}}】{$dc_patientplan->begin_date}到{$dc_patientplan->end_date}";

            self::sendEmail($report_email, $fileUrl, $fileName, $emailTitle);

            if ($dc_patientplan->dc_doctorproject->is_auto_open_next == 1) {
                self::openNext($dc_patientplan);
            }

            if ($i % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $unitofwork->commitAndInit();
    }

    // 开启下一次
    public static function openNext ($dc_patientplan) {
        $dc_doctorproject = $dc_patientplan->dc_doctorproject;
        $patient = $dc_patientplan->patient;
        $begin_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime($begin_date) + 3600 * 24 * $dc_doctorproject->period);
        $end_date = $end_date > $dc_doctorproject->end_date ? $dc_doctorproject->end_date : $end_date;

        $row = [];
        $row["title"] = $dc_doctorproject->dc_project->title;
        $row['dc_doctorprojectid'] = $dc_doctorproject->id;
        $row["patientid"] =  $patient->id;
        $row["doctorid"] =  $patient->doctorid;
        $row["begin_date"] = $begin_date;
        $row["end_date"] = $end_date;
        $row["papertplids"] = $dc_doctorproject->papertplids;
        $row["dc_patientplan_status"] =  0;
        $new_dc_patientplan = Dc_patientPlan::createByBiz($row);

        // create dc_patientplanitem
        for ($daycnt = 0; $daycnt <= $dc_doctorproject->period; $daycnt += $dc_doctorproject->frequency) {
            $plan_date = date('Y-m-d', strtotime($begin_date) + 3600 * 24 * $daycnt);

            $row = [];
            $row["dc_patientplanid"] =  $new_dc_patientplan->id;
            $row["patientid"] =  $patient->id;
            $row["doctorid"] =  $patient->doctorid;
            $row["plan_date"] = $plan_date;
            $row["submit_time"] = '';
            $dc_patientplanitem = Dc_patientPlanItem::createByBiz($row);
        }

        // 发送模板消息
        $wx_uri = Config::getConfig("wx_uri");
        $url = $wx_uri . '/dc_patientplanitem/list?dc_patientplanid=' . $new_dc_patientplan->id;

        $firstContent = $dc_doctorproject->send_content_tpl ? $dc_doctorproject->send_content_tpl : "请尽快填写{$patient->doctor->name}医生的随访量表";
        $first = [
            "value" => $firstContent,
            "color" => ""
        ];

        $keywords = [
            [
                "value" => "{$patient->name}",
                "color" => "#ff6600"
            ],
            [
                "value" => date('Y-m-d'),
                "color" => "#ff6600"
            ],
            [
                "value" => "请点击详情进行填写",
                "color" => "#ff6600"
            ]
        ];
        $content = WxTemplateService::createTemplateContent($first, $keywords);

        PushMsgService::sendTplMsgToPatientBySystem($patient, 'followupNotice', $content, $url);
    }

    public static function sendEmail ($reportEmail, $path, $filename, $emailTitle) {
        //邮件发送
        $mail = new PHPMailer();

        $mail->IsSMTP(); // send via SMTP
        $mail->Host = 'smtp.ym.163.com'; // SMTP servers
        $mail->SMTPAuth = true; // turn on SMTP authentication
        $mail->Username = 'product@fangcunyisheng.com'; // SMTP username 注意：普通邮件认证不需要加 @域名
        $mail->Password = 'Fcqx2015'; // SMTP password

        $mail->SetFrom('product@fangcunyisheng.com', '方寸医生运营后台');

        $mail->CharSet = "UTF8";
        $mail->Encoding = "base64";

        $mail->AddAddress($reportEmail, ""); // 收件人邮箱和姓名

        $mail->IsHTML(true); // send as HTML
        $mail->Subject = $emailTitle;
        $mail->Body='信息收集';
        $mail->AltBody = "text/html";

        // filename是附件上的名称，与$path绝对路径没啥关系，例如:$path=/tmp/123.xls   $filename=456.xls,发送的实际文件是123.xls，但是邮件附件上的名称是456.xls
        $mail->AddAttachment($path, $filename);
        echo $path . "\n";
        echo $filename . "\n";

        $mail->Send();
    }

    public static function getPaperData ($dc_patientplan) {
        $papertplids = explode(',', $dc_patientplan->papertplids);

        $dc_patientplanitems = Dc_patientPlanItemDao::getListByDc_patientplan($dc_patientplan);

        $papers = [];
        foreach ($papertplids as $papertplid) {
            $papertpl = PaperTpl::getById($papertplid);
            foreach ($dc_patientplanitems as $dc_patientplanitem) {
                $paper = PaperDao::getByPaperTplObjtypeObjid($papertpl, 'Dc_patientPlanItem', $dc_patientplanitem->id);
                $papers[$papertpl->id][$dc_patientplanitem->plan_date] = $paper;
            }
        }

        $data = [];
        foreach ($papers as $papertplid => $list) {
            $papertpl = PaperTpl::getById($papertplid);

            $datalist = self::paperToarr($papertpl, $list);
            $titles = self::getPaperTitles($papertpl);

            $data["{$papertpl->title}"] = [
                'heads' => $titles,
                'data' => $datalist
            ];
        }

        return $data;
    }

    // 将量表To数组
    public static function paperToarr ($papertpl, $papers) {
        $xquestionsheet = $papertpl->xquestionsheet;
        $questions = $xquestionsheet->getQuestions();

        $data = [];
        $titles = self::getPaperTitles($papertpl);
        foreach ($papers as $date => $a) {
            $list = [];

            $list[] = $date;

            // 没填的量表
            if (false == $a instanceof Paper) {
                for ($k = 1; $k < count($titles); $k++) {
                    $list[] = '未填';
                }
                $data[] = $list;

                continue;
            }

            $xanswersheet = $a->xanswersheet;

            foreach ($questions as $i => $q) {
                if ($q->isSection()) {
                    continue;
                }

                if ($q->isCaption()) {
                    continue;
                }

                $xanswer = $xanswersheet->getAnswer($q->id);
                // 有答案
                if ($xanswer instanceof XAnswer) {
                    foreach ($xanswer->getQuestionCtr()->getAnswerContents() as $t) {
                        $list[] = $t;
                    }
                } else {
                    if ($q->isMultText()) {
                        foreach ($q->getMultTitles() as $t) {
                            $list[] = '';
                        }

                    } else {
                        $list[] = '';
                    }
                }
            }

            $data[] = $list;
        }

        return $data;
    }

    public static function getPaperTitles ($papertpl) {
        $xquestionsheet = $papertpl->xquestionsheet;
        $questions = $xquestionsheet->getQuestions();

        $titles = [];
        $titles[] = '日期';
        foreach ($questions as $i => $q) {
            if ($q->isSection()) {
                continue;
            }

            if ($q->isCaption()) {
                continue;
            }

            if ($q->isMultText()) {
                foreach ($q->getMultTitles() as $t) {
                    $titles[] = "{$q->content}-{$t}";
                }
            } else {
                $titles[] = "{$q->content}";
            }
        }

        return $titles;
    }

    public static function getIds () {
        $yestoday = date('Y-m-d', time() - 3600 * 24 * 1);
        $sql = "select * from dc_patientplans where end_date = '{$yestoday}' ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }
}

$experience = new SendEmail_dc_project(__FILE__);
$experience->dowork();
