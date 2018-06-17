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

class Data_5383
{
    public function unique_rand($min, $max, $num) {
        //初始化变量为0
        $count = 0;
        //建一个新数组
        $return = array();
        while ($count < $num) {
            //在一定范围内随机生成一个数放入数组中
            $return[] = mt_rand($min, $max);
            //去除数组中的重复值用了“翻翻法”，就是用array_flip()把数组的key和value交换两次。这种做法比用 array_unique() 快得多。
            $return = array_flip(array_flip($return));
            //将数组的数量存入变量count中
            $count = count($return);
        }
        //为数组赋予新的键名
        shuffle($return);
        return $return;
    }

    public function huoyueDay () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $startdate = '2017-10-08';
        $enddate = date('Y-m-d');

        $data = [];
        $heads = [
            '日期',
            '活跃患者数',
            '有效患者数',
            '活跃率(%)'
        ];
        $list = [];
        while ($startdate <= $enddate) {
            $startdate_next = date('Y-m-d', strtotime($startdate) + 3600 * 24);
            $start_time = $startdate . ' 00:00:00';

            $sql = "select count(*)
                from patients 
                where diseaseid in (8,15,19,21) and is_test = 0 and is_live = 1 and id in (
                    select DISTINCT patientid
                    from pipes 
                    where objtype in ('WxTxtMsg','WxPicMsg','WxVoiceMsg','Paper','BedTkt','RevisitTkt','DrugSheet','CdrMeeting')
                    and createtime >= '{$start_time}' and createtime < '{$startdate_next}'
                ) ";
            $huoyuecnt = Dao::queryValue($sql);

            $sql = "select count(*)
                from patients 
                where diseaseid in (8,15,19,21) and is_test = 0 and is_live = 1 and createtime < '{$startdate_next}' ";
            $youxiaocnt = Dao::queryValue($sql);

            $rate = $huoyuecnt / $youxiaocnt * 100;
            $rate = round($rate, 2);

            $tmp = [
                $startdate,
                $huoyuecnt,
                $youxiaocnt,
                $rate
            ];

            $list[] = $tmp;

            $startdate = $startdate_next;
        }

        $data['日活'] = [
            'heads' => $heads,
            'data' => $list
        ];
        $fileurl = "/tmp/5383_day.xls";
        ExcelUtil::createExcelImp($data, $fileurl);

        $unitofwork->commitAndInit();
    }

    public function huoyueWeek () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $startdate = '2017-10-08';
        $enddate = date('Y-m-d');

        $data = [];
        $heads = [
            '日期',
            '活跃患者数',
            '有效患者数',
            '活跃率(%)'
        ];
        $list = [];
        while ($startdate <= $enddate) {
            $startdate_next = date('Y-m-d', strtotime($startdate) + 3600 * 24 * 7);
            $start_time = $startdate . ' 00:00:00';

            $sql = "select count(*)
                from patients 
                where diseaseid in (8,15,19,21) and is_test = 0 and is_live = 1 and id in (
                    select DISTINCT patientid
                    from pipes 
                    where objtype in ('WxTxtMsg','WxPicMsg','WxVoiceMsg','Paper','BedTkt','RevisitTkt','DrugSheet','CdrMeeting')
                    and createtime >= '{$start_time}' and createtime < '{$startdate_next}'
                ) ";
            $huoyuecnt = Dao::queryValue($sql);

            $sql = "select count(*)
                from patients 
                where diseaseid in (8,15,19,21) and is_test = 0 and is_live = 1 and createtime < '{$startdate_next}' ";
            $youxiaocnt = Dao::queryValue($sql);

            $rate = $huoyuecnt / $youxiaocnt * 100;
            $rate = round($rate, 2);

            $tmp = [
                $startdate . "-" . $startdate_next,
                $huoyuecnt,
                $youxiaocnt,
                $rate
            ];

            $list[] = $tmp;

            $startdate = $startdate_next;
        }

        $data['周活'] = [
            'heads' => $heads,
            'data' => $list
        ];
        $fileurl = "/tmp/5383_week.xls";
        ExcelUtil::createExcelImp($data, $fileurl);

        $unitofwork->commitAndInit();
    }

    public function huoyueMonth () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $startdate = '2017-07-01';
        $enddate = date('Y-m-d');

        $data = [];
        $heads = [
            '日期',
            '活跃患者数',
            '有效患者数',
            '活跃率(%)'
        ];
        $list = [];
        while ($startdate <= $enddate) {
            $startdate_next = date('Y-m-d', strtotime($startdate) + 3600 * 24 * 30);
            $start_time = $startdate . ' 00:00:00';

            $sql = "select count(*)
                from patients 
                where diseaseid in (8,15,19,21) and is_test = 0 and is_live = 1 and id in (
                    select DISTINCT patientid
                    from pipes 
                    where objtype in ('WxTxtMsg','WxPicMsg','WxVoiceMsg','Paper','BedTkt','RevisitTkt','DrugSheet','CdrMeeting')
                    and createtime >= '{$start_time}' and createtime < '{$startdate_next}'
                ) ";
            $huoyuecnt = Dao::queryValue($sql);

            $sql = "select count(*)
                from patients 
                where diseaseid in (8,15,19,21) and is_test = 0 and is_live = 1 and createtime < '{$startdate_next}' ";
            $youxiaocnt = Dao::queryValue($sql);

            $rate = $huoyuecnt / $youxiaocnt * 100;
            $rate = round($rate, 2);

            $tmp = [
                $startdate . "-" . $startdate_next,
                $huoyuecnt,
                $youxiaocnt,
                $rate
            ];

            $list[] = $tmp;

            $startdate = $startdate_next;
        }

        $data['月活'] = [
            'heads' => $heads,
            'data' => $list
        ];
        $fileurl = "/tmp/5383_month.xls";
        ExcelUtil::createExcelImp($data, $fileurl);

        $unitofwork->commitAndInit();
    }

    public function xuechanggui () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $times = [
            [
                'sheetname' => '7月到10月',
                'start_time' => '2017-07-01',
                'end_time' => '2017-11-01'
            ],
            [
                'sheetname' => '11月',
                'start_time' => '2017-11-01',
                'end_time' => '2017-12-01'
            ],
            [
                'sheetname' => '12月到目前',
                'start_time' => '2017-12-01',
                'end_time' => '2018-01-01'
            ]
        ];

        $data = [];
        $heads = [
            '患者姓名',
            '血常规次数',
            '日期'
        ];
        foreach ($times as $time) {
            $sheetname = $time['sheetname'];
            $start_time = $time['start_time'];
            $end_time = $time['end_time'];

            $sql = "select distinct patientid
                from patientrecords
                where code = 'cancer' and type = 'wbc_checkup' and thedate >= '{$start_time}' and thedate < '{$end_time}'
                and patientid in (
                  select id 
                  from patients 
                  where is_test = 0 and diseaseid in (8,15,19,21)
                ) ";
            $patientids = Dao::queryValues($sql);

            $cnt = count($patientids);
            if ($cnt < 100) {
                $num = $this->unique_rand(1, $cnt, $cnt);
            } else {
                $num = $this->unique_rand(1, $cnt, 100);
            }

            $selected_patientids = [];
            foreach ($num as $i) {
                $selected_patientids[] = $patientids[$i - 1];
            }

            $list = [];
            foreach ($selected_patientids as $patientid) {
                $patient = Patient::getById($patientid);

                $cond = " and patientid = {$patientid} and type = 'wbc_checkup' and code = 'cancer' and thedate >= '{$start_time}' and thedate < '{$end_time}' order by thedate asc ";
                $bind = [];

                $patientrecords = Dao::getEntityListByCond('PatientRecord', $cond, $bind);
                $cnt = count($patientrecords);

                $thedateStr = "";
                foreach ($patientrecords as $patientrecord) {
                    $thedateStr .= "{$patientrecord->thedate},";
                }

                $tmp = [
                    $patient->name,
                    $cnt,
                    $thedateStr
                ];

                $list[] = $tmp;
            }

            $data["{$sheetname}"] = [
                'heads' => $heads,
                'data' => $list
            ];
        }

        $fileurl = "/tmp/5383_wbc_all.xls";
        ExcelUtil::createExcelImp($data, $fileurl);

        $unitofwork->commitAndInit();
    }

    public function chemo () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $times = [
            [
                'sheetname' => '7月到10月',
                'start_time' => '2017-06-24',
                'end_time' => '2017-11-07'
            ],
            [
                'sheetname' => '11月',
                'start_time' => '2017-10-24',
                'end_time' => '2017-12-07'
            ],
            [
                'sheetname' => '12月到目前',
                'start_time' => '2017-11-24',
                'end_time' => '2018-01-01'
            ]
        ];

        $data = [];
        $heads = [
            '患者姓名',
            '二周方案',
            '三周方案',
            '四周方案',
            '未知周期'
        ];
        foreach ($times as $time) {
            $sheetname = $time['sheetname'];
            $start_time = $time['start_time'];
            $end_time = $time['end_time'];

            $sql = "select distinct patientid
                from patientrecords
                where type = 'chemo' and code = 'cancer' and thedate >= '{$start_time}' and thedate < '{$end_time}' 
                and patientid in (
                  select id 
                  from patients 
                  where is_test = 0 and diseaseid in (8,15,19,21)
                ) ";
            $patientids = Dao::queryValues($sql);

            $cnt = count($patientids);
            if ($cnt < 100) {
                $num = $this->unique_rand(1, $cnt, $cnt);
            } else {
                $num = $this->unique_rand(1, $cnt, 100);
            }

            $selected_patientids = [];
            foreach ($num as $i) {
                $selected_patientids[] = $patientids[$i - 1];
            }

            $list = [];
            foreach ($selected_patientids as $patientid) {
                $patient = Patient::getById($patientid);

                $cond = " and patientid = {$patientid} and type = 'chemo' and code = 'cancer' and thedate >= '{$start_time}' and thedate < '{$end_time}' ";
                $bind = [];

                $patientrecords = Dao::getEntityListByCond('PatientRecord', $cond, $bind);

                $two_cnt = 0;
                $three_cnt = 0;
                $four_cnt = 0;
                $not_cnt = 0;
                foreach ($patientrecords as $patientrecord) {
                    $content = json_decode($patientrecord->json_content, true);

                    if ($content['cycle'] == '两周方案') {
                        $two_cnt++;
                    } elseif ($content['cycle'] == '三周方案') {
                        $three_cnt++;
                    } elseif ($content['cycle'] == '四周方案') {
                        $four_cnt++;
                    } else {
                        $not_cnt++;
                    }
                }
                $tmp = [];
                $tmp[0] = $patient->name;
                $tmp[1] = $two_cnt;
                $tmp[2] = $three_cnt;
                $tmp[3] = $four_cnt;
                $tmp[4] = $not_cnt;

                $list[] = $tmp;
            }

            $data["{$sheetname}"] = [
                'heads' => $heads,
                'data' => $list
            ];
        }

        $fileurl = "data/5383_chemo_7_11.xls";
        ExcelUtil::createExcelImp($data, $fileurl);

        $unitofwork->commitAndInit();
    }
}

$test = new Data_5383();
$test->chemo();
$test->xuechanggui();
$test->huoyueDay();
$test->huoyueWeek();
$test->huoyueMonth();
