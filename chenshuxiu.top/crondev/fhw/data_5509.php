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
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Data_5509
{
    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $unitofwork->commitAndInit();
    }

    public function do55091 () {
        $sql = "select name,id,opsremark
            from patients 
            where doctorid = 364 ";
        $rows = Dao::queryRows($sql);

        $list = [];
        foreach ($rows as $row) {
            $patientrecords = PatientRecordDao::getParentListByPatientidCodeType($row['id'], 'cancer', 'untoward_effect');

            $i = 0;
            foreach ($patientrecords as $patientrecord) {
                $content = json_decode($patientrecord->json_content, true);

                if ($i == 0) {
                    $remark = $row['opsremark'];
                } else {
                    $remark = "";
                }
                $i++;
                $list[] = [
                    '患者姓名' => $row['name'],
                    '不良反应时间' => $patientrecord->thedate,
                    '不良反应名称' => $content['name'],
                    '级别' => $content['degree'],
                    '运营备注' => $remark
                ];
            }
        }

        print_r($list);

        $data = [
            '不良反应' => [
                'heads' => [
                    '患者姓名',
                    '不良反应时间',
                    '不良反应名称',
                    '级别',
                    '运营备注'
                ],
                'data' => $list
            ]
        ];

        $fileurl = "data/5509_buliangfanying_all.xls";
        ExcelUtil::createExcelImp($data, $fileurl);
    }

    public function do5509(){
        $sql = "select patientid
            from patientmedicinetargets
            where medicineid = 624 and doctorid = 364
            group by patientid ";
        $ids = Dao::queryValues($sql);

        $list = [];
        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            if (false == $patient instanceof Patient) {
                continue;
            }

            $cond = ' AND patientid=:patientid AND doctorid=:doctorid AND medicineid = 624 ';
            $bind = [
                ':patientid' => $patient->id,
                ':doctorid' => $patient->doctorid
            ];
            $pmTarget = Dao::getEntityByCond('PatientMedicineTarget', $cond, $bind);
            $drugStatus = $pmTarget->getNewestDrugStatus();
            $statusDesc = $pmTarget->getDrugStatusDesc($drugStatus);
            $list[] = [
                '患者姓名' => $patient->name,
                '医嘱日期' => $pmTarget->getRecordDate(),
                '用药日期' => $pmTarget->getNewestDrugTime(),
                '剂量/频次' =>$pmTarget->drug_dose . "/" . $pmTarget->drug_frequency,
                '状态' => $statusDesc,
                '运营备注' => $pmTarget->auditremark
            ];

            $pmsitems = $pmTarget->getPMSheetItems();
            foreach ($pmsitems as $pmsitem) {
                $list[] = [
                    '患者姓名' => $patient->name,
                    '医嘱日期' => '',
                    '用药日期' => $pmsitem->getDrugDate(),
                    '剂量/频次' =>$pmsitem->drug_dose . "/" . $pmsitem->drug_frequency,
                    '状态' => $pmsitem->getStatusDesc(),
                    '运营备注' => $pmsitem->auditremark
                ];
            }
        }

        print_r($list);

        $data = [
            '任务统计' => [
                'heads' => [
                    '患者姓名',
                    '医嘱日期',
                    '用药日期',
                    '剂量/频次',
                    '状态',
                    '运营备注'
                ],
                'data' => $list
            ]
        ];

        $fileurl = "data/5509_medicine_all.xls";
        ExcelUtil::createExcelImp($data, $fileurl);
    }
}

$test = new Data_5509();
$test->do5509();
$test->do55091();
