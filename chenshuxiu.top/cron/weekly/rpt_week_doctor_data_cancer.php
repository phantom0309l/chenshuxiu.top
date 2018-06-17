<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Rpt_week_doctor_data_cancer extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'week';
        $row["title"] = '每周一, 2:00 跑上一个星期数据';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $enddate = date('Y-m-d', time() - 86400);
        $startdate = date('Y-m-d', strtotime($enddate) - 6 * 86400 );
        echo "\n{$startdate} 到 {$enddate}\n";

        $doctorids = $this->getDoctorids();
        $cnt = count($doctorids);

        $untowardeffects_doctorids = $this->getPatientRecordDoctorids('untoward_effect', $startdate, $enddate);
        $dead_doctorids = $this->getPatientRecordDoctorids('dead', $startdate, $enddate);
        $i = 0;
        foreach ($doctorids as $row) {
            $data = [
                "doctor_name" => '',
                "startdate" => $startdate,
                "enddate" => $enddate,
                "untowardeffect" => [],
                "os" => [],
                "patientcnt" => [],
                "age" => [],
                "disease" => [],
                "dead_peroid" => []
            ];

            $doctorid = $row['id'];
            $doctor_name = $row['name'];

            $data['doctor_name'] = $doctor_name;

            if (in_array($doctorid, $untowardeffects_doctorids)) {
                $untowardeffects = $this->untowardeffect($doctorid, $startdate, $enddate);
                $data['untowardeffect'] = $untowardeffects;
            }

            if (in_array($doctorid, $dead_doctorids)) {
                $deads = $this->os($doctorid, $startdate, $enddate);
                $data['os'] = $deads;
            }

            $data['patientcnt'] = $this->patientcnt($doctorid);
            $data['age'] = $this->age($doctorid);
            $data['disease'] = $this->disease($doctorid);
            $data['dead_peroid'] = $this->dead_peroid($doctorid);

            $rpt_week_cancer_doctor = Rpt_Week_Cancer_DoctorDao::getByDoctoridWeekendDate($doctorid, $enddate);
            if (false == $rpt_week_cancer_doctor instanceof Rpt_Week_Cancer_Doctor) {
                $row = [];
                $row['doctorid'] = $doctorid;
                $row['diseaseid'] = 0;
                $row['weekend_date'] = date('Y-m-d', time() - 1 * 86400);   // 星期一跑的脚本，取昨天也就是星期天的日期
                $row['data'] = json_encode($data, JSON_UNESCAPED_UNICODE);
                Rpt_Week_Cancer_Doctor::createByBiz($row);
            } else {
                $rpt_week_cancer_doctor->data = json_encode($data, JSON_UNESCAPED_UNICODE);
            }

            $i ++;
            if ($i > 0 && $i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i/$cnt  $rate\n";

                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }

        if ($i % 100 != 0) {
            echo "$cnt/$cnt 100%\n";
        }

        $unitofwork->commitAndInit();
    }

    public function dead_peroid ($doctorid) {
        $sql = "select count(distinct a.id)
                from patients a
                inner join patientrecords b on b.patientid = a.id
                where a.doctorid = {$doctorid} and b.type = 'staging' and b.json_content like '%\"stage\":\"IV\"%' ";
        $peroidcnt = Dao::queryValue($sql);

        $sql = "select count(distinct a.id)
                from patients a
                inner join patientrecords b on b.patientid = a.id
                where a.doctorid = {$doctorid} and b.type = 'dead' ";
        $deadcnt = Dao::queryValue($sql);

        $list = [
            "dead" => $deadcnt,
            "peroid" => $peroidcnt
        ];

        return $list;
    }

    public function disease ($doctorid) {
        $sql = "select c.name,count(a.id) as cnt
                from patients a
                inner join doctordiseaserefs b on b.diseaseid = a.diseaseid and b.doctorid = a.doctorid
                inner join diseases c on c.id = b.diseaseid
                where a.doctorid = {$doctorid}
                group by b.diseaseid ";
        $rows = Dao::queryRows($sql);

        $list = [];
        $sum = 0;
        foreach ($rows as $row) {
            $sum += $row['cnt'];
        }

        foreach ($rows as $i => $row) {
            $rate = round($row['cnt'] / $sum, 4) * 100 . "%";

            $list["{$row['name']}"] = [
                'cnt' => "{$row['cnt']}",
                'rate' => $rate
            ];
        }

        return $list;
    }

    public function age ($doctorid) {
        $sql = "select (YEAR(NOW()) - YEAR(birthday)) div 10 as 'range',count(*) as cnt
                from patients
                where doctorid = {$doctorid} and birthday <> '0000-00-00' and is_test = 0 
                group by (YEAR(NOW()) - YEAR(birthday)) div 10 ";
        $rows = Dao::queryRows($sql);

        $all_cnt = Dao::queryValue("select count(*) from patients where doctorid = {$doctorid} and birthday <> '0000-00-00' and is_test = 0 ");

        $list = [];
        $cnt_30 = 0;
        $cnt_80 = 0;
        $list["<30"] = 0;
        foreach ($rows as $row) {
            switch ($row['range']) {
                case 0:
                    $cnt_30 += $row['cnt'];
                    break;
                case 1:
                    $cnt_30 += $row['cnt'];
                    break;
                case 2:
                    $cnt_30 += $row['cnt'];
                    break;
                case 3:
                    $list["30-40"] = [
                        'cnt' => $row['cnt'],
                        'rate' => round($row['cnt'] / $all_cnt, 2) * 100 . "%"
                    ];
                    break;
                case 4:
                    $list["40-50"] = [
                        'cnt' => $row['cnt'],
                        'rate' => round($row['cnt'] / $all_cnt, 2) * 100 . "%"
                    ];
                    break;
                case 5:
                    $list["50-60"] = [
                        'cnt' => $row['cnt'],
                        'rate' => round($row['cnt'] / $all_cnt, 2) * 100 . "%"
                    ];
                    break;
                case 6:
                    $list["60-70"] = [
                        'cnt' => $row['cnt'],
                        'rate' => round($row['cnt'] / $all_cnt, 2) * 100 . "%"
                    ];
                    break;
                case 7:
                    $list["70-80"] = [
                        'cnt' => $row['cnt'],
                        'rate' => round($row['cnt'] / $all_cnt, 2) * 100 . "%"
                    ];
                    break;
                case 8:
                case 9:
                case 10:
                case 11:
                case 12:
                case 13:
                    $cnt_80  += $row['cnt'];
                    break;
                default:
                    break;
            }
        }
        $list["<30"] = [
            'cnt' => "{$cnt_30}",
            'rate' => round($cnt_30 / $all_cnt, 2) * 100 . "%"
        ];
        $list[">80"] = [
            'cnt' => "{$cnt_80}",
            'rate' => round($cnt_80 / $all_cnt, 2) * 100 . "%"
        ];

        return $list;
    }

    // 性别分布
    public function patientcnt ($doctorid) {
        $sql = "select sex,count(*) as cnt
                from patients
                where doctorid = {$doctorid}
                group by sex ";
        $rows = Dao::queryRows($sql);

        $sum = 0;
        foreach ($rows as $row) {
            $sum += $row['cnt'];
        }

        $list = [
            'unknown' => [
                'cnt' => '0',
                'rate' => '0%'
            ],
            'man' => [
                'cnt' => '0',
                'rate' => '0%'
            ],
            'women' => [
                'cnt' => '0',
                'rate' => '0%'
            ],
            'all' => [
                'cnt' => '0',
                'rate' => '100%'
            ]
        ];
        foreach ($rows as $row) {
            $key = "";
            $rate = round($row['cnt'] / $sum, 2) * 100 . "%";
            switch ($row['sex']) {
                case 0:
                    $key = 'unknown';
                    break;
                case 1:
                    $key = 'man';
                    break;
                case 2:
                    $key = 'women';
                    break;
                default:
                    break;
            }

            $list["{$key}"] = [
                'cnt' => $row['cnt'],
                'rate' => $rate
            ];
        }
        $list['all'] = [
            'cnt' => "{$sum}",
            'rate' => '100%'
        ];
        return $list;
    }

    // os
    public function os ($doctorid, $startdate, $enddate) {
        /*
         *
         * */
        $enddate .= " 23:59:59";
        $sql = "select b.name as 'patient_name',c.name as 'disease_name',a.thedate as 'dead_date',e.json_content
                from patientrecords a
                inner join patientrecords e on e.patientid = a.patientid
                inner join patients b on b.id = a.patientid
                inner join diseases c on c.id = b.diseaseid
                inner join doctordiseaserefs d on d.diseaseid = b.diseaseid and d.doctorid = b.doctorid
                where a.type = 'dead' and e.type = 'diagnose' and b.doctorid = {$doctorid} and a.createtime >= '{$startdate}' and a.createtime <= '{$enddate}' ";
        $rows = Dao::queryRows($sql);

        $list = [];
        foreach ($rows as $row) {
            $item = json_decode($row['json_content'], true);

            $zhenduan_date = $item['thedate'];
            if ($zhenduan_date) {
                $os = (strtotime($row['dead_date']) - strtotime($zhenduan_date)) / (3600 * 24 * 30);
                $os = round($os, 1);
                $list[] = [
                    "patient_name" => $row['patient_name'],
                    "disease_name" => $row['disease_name'],
                    "zhenduan_date" => $zhenduan_date,
                    "dead_date" => $row['dead_date'],
                    "os" => "{$os}"
                ];
            }
        }

        return $list;
    }

    // 不良反应
    public function untowardeffect ($doctorid, $startdate, $enddate) {
        $enddate .= " 23:59:59";
        $sql = "select b.name as 'patient_name',c.name as 'disease_name',a.json_content
                from patientrecords a
                inner join patients b on b.id = a.patientid
                inner join diseases c on c.id = b.diseaseid
                where a.type = 'untoward_effect' and b.doctorid = {$doctorid} and a.createtime >= '{$startdate}' and a.createtime <= '{$enddate}' 
                order by a.patientid ";
        $rows = Dao::queryRows($sql);

        $list = [];
        foreach ($rows as $row) {
            $item = json_decode($row['json_content'], true);

            if ($item['degree'] > 0) {
                $list[] = [
                    "patient_name" => $row['patient_name'],
                    "disease_name" => $row['disease_name'],
                    "name" => $item['name'],
                    "degree" => $item['degree']
                ];
            }
        }

        return $list;
    }

    // 获取有运营备注的医生
    public function getPatientRecordDoctorids ($type, $startdate, $enddate) {
        $enddate .= " 23:59:59";
        $sql = "select b.doctorid
                from patientrecords a
                inner join patients b on b.id = a.patientid
                where a.type = '{$type}' and a.createtime >= '{$startdate}' and a.createtime <= '{$enddate}'
                group by b.doctorid
                order by null ";
        $doctorids = Dao::queryValues($sql);

        return $doctorids;
    }

    public function getDoctorids () {
        $cancer_diseaseidstr = Disease::getCancerDiseaseidsStr();
        $sql = "select distinct a.id, a.name
                from doctors a
                inner join doctordiseaserefs b on b.doctorid = a.id
                inner join doctorconfigs c on c.doctorid = a.id
                inner join doctorconfigtpls d on d.id = c.doctorconfigtplid
                where b.diseaseid in ($cancer_diseaseidstr) and d.code = 'rpt_send' 
                and c.status = 1";

        return Dao::queryRows($sql);
    }
}

// //////////////////////////////////////////////////////

echo __FILE__ . "\n";

$process = new Rpt_week_doctor_data_cancer(__FILE__);
$process->dowork();