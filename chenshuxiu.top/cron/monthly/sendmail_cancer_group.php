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

class Sendmail_cancer_group extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::rpt;
        $row["when"] = 'month';
        $row["title"] = '每月1日, 18:00, 发送邮件, 肿瘤市场绩效';
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

    public function doworkImp()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $result = [];
        $sql = "SELECT * FROM auditors WHERE NAME IN ('田学超','梁建敏','常萍')";
        $staff_rows = Dao::queryRows($sql);

        foreach ($staff_rows as $staff_row) {
            $sql = "SELECT * FROM doctors WHERE auditorid_market = " . $staff_row['id'];
            $doctor_rows = Dao::queryRows($sql);

            $temp_d = [];
            foreach ($doctor_rows as $doctor_row) {
                $sql = "SELECT count(*) AS cnt,MONTH(createtime) AS time FROM pcards WHERE patient_name NOT LIKE \"%测试%\" AND doctorid =" . $doctor_row['id'] . " GROUP BY MONTH(createtime)";
                $experiences = Dao::queryRows($sql);

                $sql = "SELECT patient_name AS name ,MONTH(createtime) AS time FROM pcards WHERE patient_name NOT LIKE \"%测试%\" AND doctorid = " . $doctor_row['id'];
                $patients = Dao::queryRows($sql);

                $sql = "SELECT name FROM hospitals WHERE id = " . $doctor_row['hospitalid'];
                $temp_d[$doctor_row['id']]['hospital'] = Dao::queryRow($sql)['name'];

                $temp_m = [];
                foreach ($experiences as $experience) {
                    $temp_p = [];
                    foreach ($patients as $patient) {
                        if ($patient['time'] == $experience['time']) {
                            $temp_p[] = $patient['name'];
                        }
                    }
                    $temp_m[$experience['time']]['cnt'] = $experience['cnt'];
                    $temp_m[$experience['time']]['patients'] = $temp_p;
                }
                $temp_d[$doctor_row['id']]['id'] = $doctor_row['id'];
                $temp_d[$doctor_row['id']]['name'] = $doctor_row['name'];
                $temp_d[$doctor_row['id']]['months'] = $temp_m;
            }
            $result[$staff_row['id']]['id'] = $staff_row['id'];
            $result[$staff_row['id']]['name'] = $staff_row['name'];
            $result[$staff_row['id']]['name'] = $staff_row['name'];
            $result[$staff_row['id']]['doctors'] = $temp_d;
        }
        unset($temp_d);
        unset($temp_m);
        unset($temp_p);

        //计算活跃月数
        define('ACTIVE_COUNT',4); //大于等于

        // 每月初统计上月的绩效，统计到11月份，即12月初最后一次统计。
        if( 8 == date('m') ) {
            define('MONTH_FROM', 5);
            define('MONTH_TO', 7);
        }else if( 9 <= date('m') && date('m') <=12 ){
            define('MONTH_FROM', date('m')-1);
            define('MONTH_TO', date('m')-1);
        }else{
            DBC::requireNotNull(null,"脚本超过使用期限");
        }

        foreach($result as $staff_key => $staff_value){

            foreach($staff_value['doctors'] as $doctor_key => $doctor_value){

                $new_active_month = 0;
                for($i=MONTH_FROM;$i<=MONTH_TO;$i++){ //todo 月份硬编码
                    if(isset($doctor_value['months'][$i]) &&
                        $doctor_value['months'][$i] >= ACTIVE_COUNT ){
                        $new_active_month = $i;
                        break;
                    }
                }
                $result[$staff_key]['doctors'][$doctor_key]['new_active_month'] = $new_active_month;
            }
        }

        $attachment_list = [];
        foreach ($result as $staff_row) {

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("fangcun");
            $objPHPExcel->getProperties()->setTitle("fangcun experience");
            $objPHPExcel->removeSheetByIndex(0);

            for ($month = MONTH_FROM; $month <= MONTH_TO; $month++) { //todo 月份硬编码

                $workSheet = new PHPExcel_Worksheet($objPHPExcel, $staff_row['name'] . $month . '月'); // 创建一个工作表
                $objPHPExcel->addSheet($workSheet); // 插入工作表

                $list_all = [];
                $list_active = [];
                $list_inactive = [];
                $list_new_active = [];

                foreach ($staff_row['doctors'] as $doctor_row) {
                    $temp = [];
                    $temp['name'] = $doctor_row['name'];
                    $temp['hospital'] = $doctor_row['hospital'];
                    $temp['patient_count'] = $doctor_row['months'][$month]['cnt'] != NULL ? $doctor_row['months'][$month]['cnt'] : "0";

                    $list_all[] = $temp;        //全部

                    if (null != $doctor_row['months'][$month]['cnt'] &&
                        $doctor_row['months'][$month]['cnt'] >= ACTIVE_COUNT
                    ) {    //活跃
                        $list_active[] = $temp;

                        if (0 != $doctor_row['new_active_month']
                            && $month == $doctor_row['new_active_month']
                        ) {     //新增活跃

                            $list_new_active[] = $temp;
                        }
                    } else {                      //不活跃
                        $list_inactive[] = $temp;
                    }
                }

                $temp = [0 => ['全部医生', '医院', '新增患者数', '', '活跃医生', '医院', '新增患者数', '', '不活跃医生', '医院', '新增患者数', '', '新增活跃医生', '医院', '新增患者数', '']];
                $objPHPExcel->getSheetByName($staff_row['name'] . $month . '月')->fromArray($temp, // 赋值的数组
                    NULL, // 忽略的值,不会在excel中显示
                    'A1'); // 赋值的起始位置

                $objPHPExcel->getSheetByName($staff_row['name'] . $month . '月')->fromArray($list_all, // 赋值的数组
                    NULL, // 忽略的值,不会在excel中显示
                    'A2'); // 赋值的起始位置
                $objPHPExcel->getSheetByName($staff_row['name'] . $month . '月')->fromArray($list_active, // 赋值的数组
                    NULL, // 忽略的值,不会在excel中显示
                    'E2'); // 赋值的起始位置
                $objPHPExcel->getSheetByName($staff_row['name'] . $month . '月')->fromArray($list_inactive, // 赋值的数组
                    NULL, // 忽略的值,不会在excel中显示
                    'I2'); // 赋值的起始位置
                $objPHPExcel->getSheetByName($staff_row['name'] . $month . '月')->fromArray($list_new_active, // 赋值的数组
                    NULL, // 忽略的值,不会在excel中显示
                    'M2'); // 赋值的起始位置
            }

            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
//            $filePath = "D:\\Wnmp\\html\\fcdev\\fangcunyisheng.com\\crondev\\slz\\". $staff['id'] .".xls";
            $filePath = "/tmp/3890_". $staff_row['name'] .".xls";
            $objWriter->save($filePath);

            //邮件用文件路径
            $temp = [];
            $temp['path'] = $filePath;
            $temp['name'] = $staff_row['name'];
            $attachment_list[] = $temp;
        }

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

        $mail->AddAddress('guowenjun@fangcunyisheng.com', ""); // 收件人邮箱和姓名
        $mail->AddCC('chenxiang@fangcunyisheng.com', ""); // 抄送邮箱和姓名

        $mail->IsHTML(true); // send as HTML
        $mail->Subject = '肿瘤市场绩效';
        $mail->Body='肿瘤市场绩效';
        $mail->AltBody = "text/html";
        foreach($attachment_list as $attachment) {
            $mail->AddAttachment($attachment['path'],$attachment['name'].'.xls');
        }

        $mail->Send();

        $unitofwork->commitAndInit();
    }
}

$experience = new Sendmail_cancer_group(__FILE__);
$experience->dowork();
