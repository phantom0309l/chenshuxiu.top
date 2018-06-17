<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/util/email/Internet_Email.class.php");
require_once(ROOT_TOP_PATH . "/../core/util/email/class.phpmailer.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class SendDFXYHZAnswerSheetToMail extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::rpt;
        $row["when"] = 'week';
        $row["title"] = '每周的周二/周五, 16:03, 发送多发性硬化症患者调查问卷到liangxiaoyu邮箱';
        return $row;
    }

    protected function needFlushXworklog() {
        return true;
    }

    protected function needCronlog() {
        return false;
    }

    protected function doworkImp() {
        $fileName = $this->answerToXls();
        $result = $this->sendEmail([$fileName]);

        if ($result) {
            unlink($fileName['file']);
        }
    }

    /**
     * 读取数据
     */
    public function answerToXls() {
        $xquestionsheetid = 581068626;
        $cond = ' and xquestionsheetid = :xquestionsheetid and patientid not in (710839246, 689380836, 599949986) order by id desc ';
        $bind[':xquestionsheetid'] = $xquestionsheetid;
        $xanswersheets = Dao::getEntityListByCond('XAnswerSheet', $cond, $bind);
        $date = date('Y-m-d');
        $objPHPExcel = new PHPExcel();
        foreach ($xanswersheets as $k => $xanswersheet) {
            if ($k > 0) { // 因为默认有一页, 所有从第二开始
                $objPHPExcel->createSheet(); // 创建内置表
            }
            $objPHPExcel->setActiveSheetIndex($k); // 从0开始
            $currentSheet = $objPHPExcel->getActiveSheet(); // 获取当前活动sheet

            $sql = "SELECT content 
                    FROM xanswers 
                    WHERE xquestionid = 634107766 AND xanswersheetid = {$xanswersheet->id} ";
            $value = Dao::queryValue($sql);

            $ignoreStrs = array('*', ':', '/', '\\', '?', '[', ']');
            foreach ($ignoreStrs as $ignoreStr) {
                $name = mb_str_replace($ignoreStr, ' ', $value);
            }

            $currentSheet->setTitle($name ?? 'sheet');
            $currentSheet->setCellValue('A1', '问题')->setCellValue('B1', '答案'); // A1表示第一行的第一列 B1表示第一行的第二列,以此类推...
            $currentSheet->setCellValue('A2', '填写时间')->setCellValue('B2', $xanswersheet->createtime);
            $xanswerarr = array();
            $j = 3;
            foreach ($xanswersheet->getAnswers() as $xanswer) {
                if (false == $xanswer->isDefaultHide()) {
                    $xanswerarr['question'] = '';
                    $xanswerarr['xanswer'] = '';
                    $xanswerarr['question'] = $this->replaceBr($xanswer->getQuestionCtr()->getQaHtmlQuestionContent());
                    $xanswerarr['xanswer'] = $this->replaceBr($xanswer->getQuestionCtr()->getQaHtmlAnswerContent());
                    $currentSheet->setCellValue('A' . $j, $xanswerarr['question'])->setCellValue('B' . $j, $xanswerarr['xanswer']);
                    $j++; // 每循环一次换一行写入数据
                }
            }
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $fileName = [
            'file' => "多发性硬化症患者调查问卷 {$date}.xls"
        ];
        $objWriter->save($fileName['file']);

        return $fileName;
    }

    private function replaceBr($text) {
        return preg_replace('/<br\\s*?\/??>/i', '', $text);
    }

    /**
     * @param $fileNames
     * @throws phpmailerException
     * @return bool
     */
    public function sendEmail($fileNames) {
        foreach ($fileNames as $fileName) {
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

            $mail->AddAddress('liangxiaoyu@fangcunyisheng.com', ""); // 收件人邮箱和姓名

            $mail->IsHTML(true); // send as HTML
            $date = date('Y-m-d');
            $mail->Subject = "多发性硬化症患者调查问卷 {$date}";
            $mail->Body = '无';
            $mail->AltBody = "text/html";

            $mail->AddAttachment($fileName['file'], $fileName['file']);

            $mail->Send();
        }

        return true;
    }
}

$send = new SendDFXYHZAnswerSheetToMail(__FILE__);
$send->dowork();