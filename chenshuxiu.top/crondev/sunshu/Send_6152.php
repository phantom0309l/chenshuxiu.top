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
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Send_6152
{
    function doWork() {//读数据
        $unitofwork = BeanFinder::get("UnitOfWork");
        $filename = '20180518xuyan.xls';
        $objReader = PHPExcel_IOFactory::createReaderForFile($filename);//准备打开文件
        $objPHPExcel = $objReader->load($filename);//载入文件
        $sheet = $objPHPExcel->getSheet(0);// 读取第一個工作表
        $highestRow = $sheet->getHighestRow();// 取得总行数
        $highestColumm = $sheet->getHighestColumn();// 取得总列数
        /** 循环读取每个单元格的数据 */
        $i = 0;
        $k = 0;
        $cnt = count($highestColumm);
        for ($row = 2; $row <= $highestRow; $row++)    //行号从1开始
        {
            $column = 'A';
            $name = $sheet->getCell($column . $row)->getValue();
            $columntwo = 'B';
            $mobile = $sheet->getCell($columntwo . $row)->getValue();
            $sql = "SELECT a.*
                FROM patients a
                LEFT JOIN linkmans b ON a.id = b.patientid
                WHERE a.name = '{$name}'
                AND doctorid=33
                AND b.mobile = '{$mobile}'";
            $patient = Dao::loadEntity('Patient', $sql);
            if ($patient instanceof Patient) {
                $this->send($patient);

                $i++;
                if ($i % 100 == 0) {
                    $k += 100;
                    echo $k . "/" . $cnt . "\n";
                    $unitofwork->commitAndInit();
                } else {
                    echo ".";
                }
            }

            $unitofwork->commitAndInit();

        }
    }

    public function send(Patient $patient) {
        $wxusers = WxUserDao::getListByPatient($patient);
        echo count($wxusers);
        if (count($wxusers) > 0) {
            foreach ($wxusers as $wxuser) {
                echo $wxuser->id . "\n";
                $content = "您好，我们已经收到了您的参会申请，现需要跟您明确一下，2018-05-27（本周日）举办的患教会您是否可以如约到场？具体参会人员为几位？";
                PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);
            }
        }

    }

}

$data = new Send_6152();
$data->doWork();
