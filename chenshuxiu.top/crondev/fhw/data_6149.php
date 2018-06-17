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

class Data_6149
{
    public function work () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $optasktplids = [
            '445440206' => '肿瘤用药核对'
        ];

        $i = 0;
        $finish_cnt = 0;
        foreach ($optasktplids as $optasktplid => $title) {
            $sql = "select id,patientid,left(first_plantime, 10) as 'first_plantime'
                    from optasks
                    where diseaseid in (8,14,15,19,21)
                    and optasktplid = {$optasktplid} and first_plantime >= '2018-05-17' and first_plantime < '2018-05-25'
                    order by patientid,first_plantime";
            $rows = Dao::queryRows($sql);

            $list = [];
            foreach ($rows as $row) {
                $optaskid = $row['id'];

                $optask = OpTask::getById($optaskid);
                $finish = $optask->opnode->title;

                $sql = "select b.name,a.createtime,a.content
                        from optlogs a
                        left join auditors b on b.id = a.auditorid
                        where a.auditorid > 0 and a.optaskid = {$optaskid}
                        order by a.createtime desc ";
                $logs = Dao::queryRows($sql);

                $logstr = "";
                foreach ($logs as $log) {
                    $logstr .= $log['createtime'] . " " . $log['name'] . " " . $log['content'] . "\n";
                }

                $patienturl = "https://audit.fangcunyisheng.com/optaskmgr/listnew?patientid={$row['patientid']}";
                $list[] = [$patienturl, $row['first_plantime'], $logstr,$finish];

                $i++;
                if ($i % 100 == 0) {
                    echo "{$i}\n";
                } else {
                    echo ".";
                }
            }

            $data["{$title}"] = [
                'heads' => [
                    '患者id',
                    '任务根时间',
                    '操作日志',
                    '状态'
                ],
                'data' => $list
            ];
        }

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
        Dao::executeNoQuery($sql);

        echo $finish_cnt . "\n";

//        print_r($data);
        $fileurl = "/tmp/fhw/data/6242_01.xls";

        ExcelUtil::createExcelImp($data, $fileurl);

        $unitofwork->commitAndInit();
    }


}

$test = new Data_6149();
$test->work();
