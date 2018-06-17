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
//Debug::$debug = 'Dev';

class Data_6031
{
    public function work () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id
                from patients
                where diseaseid in (8,14,15,19,21) and is_live = 0 ";
        $ids = Dao::queryValues($sql);

        $list = [];
        foreach ($ids as $id) {
            $patient = Patient::getById($id);

            $sql = " select thedate from patientrecords where patientid = {$id} and code = 'common' and type = 'dead' order by thedate desc limit 1 ";
            $dead_date = Dao::queryValue($sql);

            $sql = " select json_content from patientrecords where patientid = {$id} and code = 'cancer' and type = 'diagnose' order by id desc limit 1 ";
            $json_content = Dao::queryValue($sql);
            $arr_content = json_decode($json_content, true);
            $diagnose_date = $arr_content['thedate'];

            $month_cnt = '未知';
            if ($dead_date && $diagnose_date) {
                $month_cnt = round((strtotime($dead_date) - strtotime($diagnose_date)) / (3600 * 24 * 30), 1);
            }

            $sql = " select json_content from patientrecords where patientid = {$id} and code = 'cancer' and type = 'staging' order by id desc limit 1 ";
            $json_content = Dao::queryValue($sql);
            $arr_content = json_decode($json_content, true);
            $staging = $arr_content['stage'];

            $list[] = [
                '患者姓名' => $patient->name,
                '入组时间' => $patient->createtime,
                '疾病' => $patient->disease->name,
                '年龄' => $patient->getAgeStr(),
                '性别' => $patient->getSexStr(),
                '诊断时间' => $diagnose_date ?? '',
                '死亡时间' => $dead_date ?? '',
                '生存' => $month_cnt ?? '',
                '分期' => $staging
            ];

        }

        // 杨中华 肿瘤 年龄 性别 诊断时间 死亡时间 月数 I
        $data = [
            '死亡患者' => [
                'heads' => [
                    '患者姓名',
                    '入组时间',
                    '疾病',
                    '年龄',
                    '性别',
                    '诊断时间',
                    '死亡时间',
                    '生存',
                    '分期'
                ],
                'data' => $list
            ]
        ];

        /*
         $data = [
            'sheet1name' => [
                'heads' => [
                    '姓名',
                    '年龄'
                ],
                'data' => [
                    [0] => [
                        'fanghanwen',
                        23
                    ],
                    [1] => [
                        'liufei',
                        23
                    ],
                    ...
                ]
            ],
            'sheet2name' => [
                'heads' => [
                    '姓名',
                    '学校',
                    '专业'
                ],
                'data' => [
                    [0] => [
                        'fanghanwen',
                        '清华大学',
                        '计算机科学与技术'
                    ],
                    [1] => [
                        'liufei',
                        '北京大学',
                        '历史系'
                    ],
                    ...
                ]
            ],
            ...
        ];
        $fileurl = "/tmp/certican/test.xls";
         */

        print_r($data);
        $fileurl = "/tmp/fhw/data/dead_6031.xls";

        ExcelUtil::createExcelImp($data, $fileurl);

        $unitofwork->commitAndInit();
    }


}

$test = new Data_6031();
$test->work();
