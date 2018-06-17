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
        $doctorid = 1294;
        $diseaseid = 22;
        $cond = ' AND doctorid=:doctorid AND diseaseid=:diseaseid GROUP BY patientid';
        $bind = [
            ':doctorid' => $doctorid,
            ':diseaseid' => $diseaseid,
        ];
        $pcards = Dao::getEntityListByCond('Pcard', $cond, $bind);
        $sets = [];
        $all_cnt = 0;
        $complication_cnt = 0;
        $male_cnt = 0;
        $female_cnt = 0;
        $baxiang_only_cnt = 0;
        $mianyi_only_cnt = 0;
        $both_medicine_cnt = 0;
        $stop_medicine_cnt = 0;
        $patients_nocomplication = [];
        foreach ($pcards as $pcard) {
            $patient = $pcard->patient;
            //忽略掉无效患者和测试患者
            if ($patient->status != 1 || $patient->is_test == 1) {
                continue;
            }
            $all_cnt ++;
            if ($pcard->complication || $patient->getTagRefs()) {
                $complication_cnt ++;
            } else {
                $patients_nocomplication[] = ['id' => $patient->id, 'name' => $patient->name];
            }
            if ($patient->sex == 1) {
                $male_cnt ++;
            } else if ($patient->sex == 2) {
                $female_cnt ++;
            }

            //服药情况

            $patientid = $patient->id;
            //$patientid = 215824197;
            //曾经使用的免疫抑制剂
            $sql = "SELECT DISTINCT (c.name) FROM `patientmedicinesheetitems` a
                INNER JOIN patientmedicinesheets b ON a.patientmedicinesheetid = b.id
                INNER JOIN medicines c ON a.medicineid = c.id
                WHERE b.doctorid=$doctorid AND b.patientid='{$patientid}'";
            $rows = Dao::queryRows($sql);
            $baxiangMedicines = ['波生坦','安立生坦','他达拉非','西地那非','伊洛前列素','曲前列尼尔','贝前列素','伐地那非'];
            $mianyiMedicines = ['环磷酰胺','赛可平','骁悉','扶异','他克莫司','硫唑嘌呤','纷乐','甲氨蝶呤','维柳芬','雷公藤多苷片','赛能','爱若华'];
            $medicine_names = [];
            foreach ($rows as $row) {
                $medicine_names[] = $row['name'];
            }

            $inter1 = array_intersect($medicine_names, $baxiangMedicines);
            $inter2 = array_intersect($medicine_names, $mianyiMedicines);

            //单纯服用PH靶向药患者人数
            if ($inter1 && !$inter2) {
                $baxiang_only_cnt ++;
            } else if (!$inter1 && $inter2) {
                $mianyi_only_cnt ++;
            } else if ($inter1 && $inter2) {
                $both_medicine_cnt ++;
            }

            //有过停药记录的患者人数
            $sql = "SELECT a.id FROM `patientmedicinesheetitems` a
                INNER JOIN patientmedicinesheets b ON a.patientmedicinesheetid = b.id
                WHERE b.doctorid=$doctorid AND b.patientid='{$patientid}' AND a.status=3";
            $value = Dao::queryValue($sql);
            if ($value) {
                $stop_medicine_cnt ++;
            }

        }
        //所有停药对应的患者人数
        $sql = "SELECT c.name, COUNT(DISTINCT b.patientid) as cnt FROM patientmedicinesheetitems a 
            INNER JOIN patientmedicinesheets b ON a.patientmedicinesheetid = b.id
            INNER JOIN medicines c ON a.medicineid = c.id
            INNER JOIN patients d ON b.patientid=d.id
            WHERE b.doctorid=$doctorid AND a.status=3 AND d.is_test=0
            GROUP BY c.name ";
        $rows = Dao::queryRows($sql);
        $stop_medicines = $rows;

        //各诊断对应的患者数
        $sql = "SELECT c.id AS tagid, c.name AS tagname, COUNT(DISTINCT b.id) AS cnt FROM tagrefs a
            INNER JOIN patients b ON a.`objid` = b.`id` AND a.`objtype` = 'Patient'
            INNER JOIN tags c ON a.`tagid` = c.id 
            WHERE b.`doctorid` = '$doctorid' AND b.`diseaseid`='$diseaseid' AND b.is_test=0 AND b.status=1
            GROUP BY c.`name`";
        $zhenduan_patients = Dao::queryRows($sql);
        $zhenduan_patients[] = [
            'tagid' => 0,
            'tagname' => '无诊断',
            'cnt' => $all_cnt - $complication_cnt,
        ];

        $data['one']['全部患者数'] = $all_cnt;
        $data['one']['男性患者'] = $male_cnt;
        $data['one']['女性患者'] = $female_cnt;
        $data['one']['有诊断的患者数'] = $complication_cnt;
        $data['one']['单纯服用PH靶向药'] = $baxiang_only_cnt;
        $data['one']['单纯服用免疫抑制剂'] = $mianyi_only_cnt;
        $data['one']['同时服用免疫抑制剂＋靶向药'] = $both_medicine_cnt;
        $data['one']['有过停药的患者'] = $stop_medicine_cnt;
        $data['停药'] = $stop_medicines;
        $data['无诊断患者'] = $patients_nocomplication;
        $data['zhenduan_patients'] = $zhenduan_patients;

        $this->export($data);
    }

    private function export($data) {
        if (empty($data) || !is_array($data)) {
            return false;
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle(trim($one['name']));
        $objPHPExcel->getProperties()->setCreator("fangcun");
        $objPHPExcel->getProperties()->setTitle("fangcun doctor data export");
        // 将数据写入到文件
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('统计');
        $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('B4C7E7');
        $objPHPExcel->getActiveSheet()->getStyle('A12:B12')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFE699');
        $lines = [];
        $lines[] = ['统计项', '统计值'];
        foreach ($data['one'] as $key => $one) {
            $lines[] = [$key, $one];
        }
        $lines[] = [];
        $lines[] = [];
        $lines[] = ['药品', '停药人数'] ;
        foreach ($data['停药'] as $one) {
            $lines[] = [$one['name'], $one['cnt']];
        }

        $lines[] = [];
        $lines[] = [];
        $lines[] = ['诊断', '患者人数'] ;
        foreach ($data['zhenduan_patients'] as $one) {
            $lines[] = [$one['tagname'], $one['cnt']];
        }

        $objPHPExcel->getActiveSheet()->fromArray($lines, // 赋值的数组
            NULL, // 忽略的值,不会在excel中显示
            'A1'); // 赋值的起始位置

        $workSheet = new PHPExcel_Worksheet($objPHPExcel, '无诊断患者'); // 创建一个工作表
        $objPHPExcel->addSheet($workSheet); // 插入工作表
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('B4C7E7');

        $lines = [];
        $lines[] = ['患者ID', '患者姓名'];
        foreach ($data['无诊断患者'] as $one) {
            $lines[] = [$one['id']."\t", $one['name']];
        }
        $objPHPExcel->getActiveSheet()->fromArray($lines, // 赋值的数组
            NULL, // 忽略的值,不会在excel中显示
            'A1'); // 赋值的起始位置

        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('./wangqian.xls');
    }
}

$obj = new ExportPatient();
$obj->run();

