<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Data_5723
{
    public function datacollection () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.name as 'patientname', b.name as 'doctorname', b.department, c.name as 'hospitalname', a.createtime, d.thedate,  TIMESTAMPDIFF(DAY,a.createtime,d.thedate) as 'day', round(TIMESTAMPDIFF(DAY,a.createtime,d.thedate) / 30, 1) as 'month'
                from patients a
                inner join doctors b on b.id = a.doctorid
                inner join hospitals c on c.id = b.hospitalid
                inner join patientrecords d on d.patientid = a.id
                where a.is_live = 0 and d.type = 'dead'
                order by day desc ";
        $list = Dao::queryRows($sql);

        $data = [
            '死亡患者' => [
                'heads' => [
                    '患者姓名',
                    '医生姓名',
                    '医生所属科室',
                    '医生所属医院',
                    '入组时间',
                    '死亡日期',
                    '死亡-入组(天)',
                    '死亡-入组(月)'
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

//        print_r($data);
        $fileurl = "/tmp/fhw/data/dead_02.xls";

        ExcelUtil::createExcelImp($data, $fileurl);

        $unitofwork->commitAndInit();
    }
}

$test = new Data_5723();
$test->datacollection();
