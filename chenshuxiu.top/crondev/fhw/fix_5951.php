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

class Fix_5951
{
    public function work () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $patients = [
            "张巧花" => "20170605",
            "李群亭" => "20161128",
            "孔令杰" => "20120611",
            "房秀权" => "20171020",
            "段跃进" => "20171018",
            "代安宁" => "20170824",
            "国铁樑" => "20170325",
            "付兆海" => "20170511",
            "王书莉" => "20110519",
            "李景兴" => "20170112",
            "霍天珍" => "20171101",
            "代全英" => "20131218",
            "董早" => "20121206",
            "周振全" => "20170316",
            "常旭" => "20150408",
            "高岳" => "20161208",
            "王素敬" => "20091021",
            "李宇军" => "20170823",
            "王书莉" => "20160310",
            "要继军" => "20171108",
            "要继军" => "20171108",
            "李来徽" => "20171227",
            "杨熙" => "20171206",
            "李妙娃" => "20171216",
            "许振杰" => "20171010",
            "邓增翠" => "20170727",
            "陈永昌" => "20160330",
            "杨云凤" => "20151121",
            "郑宝贞" => "20170725",
            "汪春峰" => "20170224",
            "史殿武" => "20171120",
            "贾煜" => "20150930",
            "冯小冲" => "20170227",
            "李付恋" => "20170224",
            "徐本生" => "20170224",
            "邱来兴" => "20170331",
            "于明海" => "20170926",
            "刘三女" => "20171123",
            "杨井荣" => "20171030",
            "金娜" => "20170711",
            "刘存久" => "20171120",
            "刘广洲" => "20171101",
            "刘春合" => "20171009",
            "宫显霞" => "20171208",
            "李广山" => "20150814",
            "张春霄" => "20171120",
            "刘姣林" => "20171206",
            "黄亚萍" => "20171124",
            "李建强" => "20170525",
            "李广德" => "20170615",
            "高孝" => "20170505",
            "李学文" => "20170401",
            "田祥生" => "20180116",
            "邹尚德" => "20170519",
            "闫福春" => "20161212",
            "郭兆良" => "20170320",
            "王永裕" => "20160506",
            "林川" => "20150527",
            "成建武" => "20170526",
            "王成民" => "20170419",
            "黄前荣" => "20121116",
            "戴玉香" => "20171121",
            "汪银成" => "20141014",
            "陈远超" => "20171115",
            "万林华" => "20170830",
            "刘秀芬" => "20170901",
            "巴小有" => "20171015"
        ];

        $cancerdiseaseidstr = Disease::getCancerDiseaseidsStr();
        foreach ($patients as $name => $date) {
            $cond = " and name = '{$name}' and diseaseid in ({$cancerdiseaseidstr}) ";
            $patients = Dao::getEntityListByCond("Patient", $cond);
            $cnt = count($patients);

            if ($cnt == 1) {
                $patient = $patients[0];
                $thedate = $this->getDate($date);

                $arr = [
                    "thedate" => $thedate,
                    "position" => "not",
                    "position_other" => "",
                    "diagnose_start" => "not",
                    "diagnose_start_other" => "",
                    "special" => "not",
                    "special_other" => "",
                    "shift_thedate" => "",
                    "shift_position" => "",
                    "shift_position_other" => ""
                ];

                $row = [];
                $row["patientid"] = $patient->id;
                $row["code"] = "cancer";
                $row["type"] = "diagnose";
                $row["thedate"] = date('Y-m-d');
                $row["json_content"] = json_encode($arr, JSON_UNESCAPED_UNICODE);
                $row["create_auditorid"] = 1;
                $patientrecord = PatientRecord::createByBiz($row);
            } elseif ($cnt == 0) {
                echo "{$name} 患者不存在\n";
            } elseif ($cnt > 1) {
                echo "{$name} 在肿瘤下有{$cnt}个重名患者\n";
            }
        }

        $unitofwork->commitAndInit();
    }

    public function getDate ($str) {
        $year = substr($str, 0, 4);
        $month = substr($str, 4, 2);
        $day = substr($str, 6, 2);

        return "{$year}-{$month}-{$day}";
    }
}

$test = new Fix_5951();
$test->work();