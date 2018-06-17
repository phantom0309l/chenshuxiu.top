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

class SendEmail_certican extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::rpt;
        $row["when"] = 'month';
        $row["title"] = '每天, 11:00, 发送邮件, 秦燕项目：依维莫司';
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

    public function getIds () {
        $ids = [];

        $thedate = date('Y-m-d', time() - 3600 * 24 * 21);

        $sql = "select id from certicans where begin_date = '{$thedate}' ";
        $ids = Dao::queryValues($sql);

//         $ids = [459751196, 459776506];

        return $ids;
    }

    public function doworkImp()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $data = $this->getDataArr();
        $filenames = $this->createExcel($data);

        $this->sendEmail($filenames);

        $unitofwork->commitAndInit();
    }

    public function getDataArr () {
        $certicanids = $this->getIds();

        foreach ($certicanids as $id) {
            $certican = Certican::getById($id);

            $certicanitems = CerticanItemDao::getListByCertican($certican);
            $i = 0;
            $data = [];
            foreach ($certicanitems as $a) {
                $tmp = [];

                // 验血
                $wbc_str = '';
                if ($a->is_fill == 1) {
                    $a->fixWbc();
                    if ($a->wbc) {
                        $wbc_str = $a->wbc;
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

                $tmp[] = $a->plan_date;
                $tmp[] = ++$i;
                $tmp[] = $a->is_fill == 1 ? $a->drug_dose . "mg" : '';
                $tmp[] = $a->is_fill == 1 ? $a->adverse_content : '';
                $tmp[] = $wbc_str;
                $tmp[] = $white_str;
                $tmp[] = $platelet_str;
                $tmp[] = $a->is_fill == 1 ? '✔' : '✘';

                $data[] = $tmp;
            }

            $timetmp = time();
            $list["{$certican->patient->name}_{$certican->begin_date}_{$timetmp}|{$certican->patient->name}_{$certican->title}_{$certican->sub_title}"] = $data;
        }

        return $list;
    }

    public function createExcel ($list) {
        $fileNames = [];

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

        foreach ($list as $fileurl => $data) {
            $titles = explode("|", $fileurl);
            ExcelUtil::createForCron($data, $headarr, "/tmp/certican/" . $titles[0] . ".xls"); 

            $fileNames[] = [
                'path' => "/tmp/certican/" . $titles[0] . ".xls",
                'name' => $titles[0] . ".xls",
                'title' => $titles[1]
            ];
            print_r($fileNames);
        }

        return $fileNames;
    }

    public function sendEmail ($fileNames) {
        foreach ($fileNames as $fileName) {
            $titles = explode('_', $fileName['title']);
            print_r($titles);

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

            $mail->AddAddress('laixuemei@fangcunyisheng.com', ""); // 收件人邮箱和姓名

            $mail->IsHTML(true); // send as HTML
            $mail->Subject = "【依维莫司服药临床项目】患者【{$titles[0]}】已完成[{$titles[1]}方案]第[{$titles[2]}程]的记录表";
            $mail->Body='无';
            $mail->AltBody = "text/html";

            $mail->AddAttachment($fileName['path'], $fileName['name']);

            $mail->Send();
        }
    }
}

$experience = new SendEmail_certican(__FILE__);
$experience->dowork();
