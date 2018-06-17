<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class ExportPatient {
    public function __construct() {

    }

    public function run() {
        $doctorid = 32;
        $diseaseid = 2;
        $cond = ' AND doctorid=:doctorid AND diseaseid=:diseaseid';
        $bind = [
            ':doctorid' => $doctorid,
            ':diseaseid' => $diseaseid,
        ];
        $pcards = Dao::getEntityListByCond('Pcard', $cond, $bind);
        $sets = [];
        foreach ($pcards as $pcard) {
            $patient = $pcard->patient;
            if ($patient->status != 1) {
                continue;
            }
            $data = [];
            $data['姓名'] = $patient->name;
            $data['性别'] = $patient->getSexStr();
            $data['生日'] = $patient->birthday;
            $data['诊断'] = $pcard->complication;
            //判断诊断是否已确认
            $sql = "SELECT id FROM patienttags WHERE patientid={$patient->id} AND doctorid={$doctorid} AND init_name='诊断已确认'";
            $ret = Dao::queryValue($sql);
            if ($ret) {
                $data['诊断已确认'] = '是';
            } else {
                $data['诊断已确认'] = '否';
            }

            //$patientid = '120344595';
            //CTD首诊时间（首次确诊日期）
            $ctdQuestionid = '107481115';
            $sql = "SELECT a.content FROM xanswers a 
                INNER JOIN xanswersheets b on a.xanswersheetid = b.id 
                WHERE b.patientid='{$patient->id}' AND a.xquestionid='{$ctdQuestionid}' AND a.content <> '' ORDER BY a.id ASC LIMIT 1";
            $row = Dao::queryRow($sql);
            $data['CTD首诊日期'] = $row['content'];

            //ILD首诊时间（首次确诊日期）
            $ildQuestionid = '107483251';
            $sql = "SELECT a.content FROM xanswers a 
                INNER JOIN xanswersheets b on a.xanswersheetid = b.id 
                WHERE b.patientid='{$patient->id}' AND a.xquestionid='{$ildQuestionid}' AND a.content <> '' ORDER BY a.id ASC LIMIT 1";
            $row = Dao::queryRow($sql);
            $data['ILD首诊日期'] = $row['content'];

            //末次访视日期
            $revisitRecord = Dao::getEntityByCond('RevisitRecord', " AND doctorid=$doctorid AND patientid={$patient->id} ORDER BY thedate DESC");
            if ($revisitRecord) {
                $data['末次访视日期'] = $revisitRecord->thedate;
            } else {
                $data['末次访视日期'] = '';
            }

            //做过的肺功能次数
            $sql = "SELECT COUNT(*) FROM checkuptpls a 
                INNER JOIN checkups b ON a.id = b.checkuptplid
                WHERE a.ename='feigongneng' AND a.doctorid={$doctorid} AND b.patientid={$patient->id}";
            $ret = Dao::queryValue($sql);
            $data['肺功能次数'] = $ret;

            //曾经使用的免疫抑制剂
            $sql = "SELECT DISTINCT (c.name) FROM `patientmedicinesheetitems` a
                INNER JOIN patientmedicinesheets b ON a.patientmedicinesheetid = b.id
                INNER JOIN medicines c ON a.medicineid = c.id
                WHERE b.doctorid=$doctorid AND b.patientid='{$patient->id}'";
            $rows = Dao::queryRows($sql);
            $mianyiMedicines = ['环磷酰胺', '环孢素', '赛可平', '骁悉', '扶异', '他克莫司', '普乐可复', '异力抗', '纷乐', '硫唑嘌呤', '赛能', '依木兰', '爱若华', '妥抒', '雷公藤多苷片', '甲氨蝶呤', '维柳粉', '尚杰'];
            $medicinestr = '';
            $isARS = '否';
            foreach ($rows as $row) {
                if (in_array($row['name'], $mianyiMedicines)) {
                    $medicinestr .= $row['name'] . ',';
                }

                if ($row['name'] == '艾思瑞' || $row['name'] == '吡非尼酮') {
                    $isARS = '是';
                }
            }
            $medicinestr = rtrim($medicinestr, ',');
            $data['免疫抑制剂'] = $medicinestr;

            //是否服用过艾思瑞（吡非尼酮）
            $data['用过艾思瑞'] = $isARS;

            $sets[] = $data;
        }
        $this->export($sets);
    }

    private function export($data) {
        if (empty($data) || !is_array($data)) {
            return false;
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("fangcun");
        $objPHPExcel->getProperties()->setTitle("fangcun doctor data export");
        // 将数据写入到文件
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('默认');
        $lines = [];
        foreach ($data as $key => $one) {
            if ($key == 0) {
                $titles = array_keys($one);
                $lines[] = $titles;
            }
            $lines[] = array_values($one);
        }
        $objPHPExcel->getActiveSheet()->fromArray($lines, // 赋值的数组
            NULL, // 忽略的值,不会在excel中显示
            'A1'); // 赋值的起始位置
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('./wangqian.xls');
    }
}

$obj = new ExportPatient();
$obj->run();

