<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Rpt_day_doctor_data_cancer extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'week';
        $row["title"] = '每天凌晨, 1:00 跑昨天数据';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog() {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog() {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $startdate = date('Y-m-d', time() - 86400);
        $enddate = date('Y-m-d');

        $doctorids = $this->getDoctorids();
        $cnt = count($doctorids);

        $i = 0;
        foreach ($doctorids as $doctorid) {
            $emphasiss = $this->emphasis($doctorid, $startdate, $enddate);
            $patientids_emphasis = array_keys($emphasiss);

            $adverses = $this->adverses($doctorid, $startdate, $enddate);
            $patientids_adverse = array_keys($adverses);

            $patientids = array_merge($patientids_emphasis, $patientids_adverse);
            $actives = $this->actives($doctorid, $startdate, $enddate, $patientids);

            // 将重点患者和严重不良反应相同患者合并
            $emphasis_adverses_all = [];
            $commons = [];
            foreach ($emphasiss as $patientid => $emphasis) {
                if (array_key_exists($patientid, $adverses)) {
                    $commons[] = $patientid;
                } else {
                    $emphasis_adverses_all["{$patientid}"] = $emphasis;
                }
            }
            foreach ($adverses as $patientid => $adverse) {
                if (!in_array($patientid, $commons)) {
                    $emphasis_adverses_all["{$patientid}"] = $adverse;
                }
            }
            foreach ($commons as $patientid) {
                $emphasis_adverses_all["{$patientid}"] = [
                    'patientid' => $patientid,
                    'name' => $emphasiss["{$patientid}"]['name'],
                    'age' => $emphasiss["{$patientid}"]['age'],
                    'sex' => $emphasiss["{$patientid}"]['sex'],
                    'disease_name' => $emphasiss["{$patientid}"]['disease_name'],
                    'content' => $emphasiss["{$patientid}"]['content'] . "；" . $adverses["{$patientid}"]['content']
                ];
            }

            // 合并活跃患者
            foreach ($actives as $patientid => $active) {
                $emphasis_adverses_all["{$patientid}"] = $active;
            }

            // 如果三项都为空，则不记录，也不发送给医生
            if (empty($emphasiss) && empty($adverses) && empty($emphasis_adverses_all)) {
                continue;
            }

            $data = [
                'thedate' => $startdate,
                'emphasis' => array_values($emphasiss),
                'adverses' => array_values($adverses),
                'all' => array_values($emphasis_adverses_all)
            ];

            $row = [];
            $row["doctorid"] = $doctorid;
            $row["diseaseid"] = 0;
            $row["day_date"] = $startdate;
            $row["data"] = json_encode($data, JSON_UNESCAPED_UNICODE);
            Rpt_Day_Cancer_Doctor::createByBiz($row);

            $i++;
            if ($i % 100 == 0) {
                $rate = round($i / $cnt, 2) * 100 . "%";
                echo "$i/$cnt  $rate\n";

                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }

        echo "$cnt/$cnt  100%\n";

        $unitofwork->commitAndInit();
    }

    /**
     * 重点患者
     *
     * @param $doctorid
     * @param $startdate
     * @param $enddate
     * @return array
     */
    public function emphasis($doctorid, $startdate, $enddate) {
        $sql = "select a.patientid, b.name, c.name as 'disease_name', if(b.sex = 1, '男', '女') as sex, year(now()) - year(b.birthday) as age, group_concat(a.title SEPARATOR ' ') as 'content'
                from patienttodaymarks a
                inner join patients b on b.id = a.patientid
                inner join diseases c on c.id = b.diseaseid
                where b.doctorid = {$doctorid} and a.thedate >= '{$startdate}' and a.thedate < '{$enddate}'
                group by a.patientid ";
        $rows = Dao::queryRows($sql);

        $list = [];
        foreach ($rows as $row) {
            $list["{$row['patientid']}"] = [
                'patientid' => $row['patientid'],
                'name' => $row['name'],
                'sex' => $row['sex'],
                'age' => $row['age'],
                'disease_name' => $row['disease_name'],
                'content' => $row['content']
            ];
        }

        return $list;
    }

    /**
     * 严重不良反应
     *
     * @param $doctorid
     * @param $startdate
     * @param $enddate
     * @return array
     */
    public function adverses($doctorid, $startdate, $enddate) {
        $sql = "select a.patientid, b.name, c.name as 'disease_name',if(b.sex = 1, '男', '女') as sex, year(now()) - year(b.birthday) as age, group_concat(a.json_content SEPARATOR '|') as 'content'
                from patientrecords a
                inner join patients b on b.id = a.patientid
                inner join diseases c on c.id = b.diseaseid
                where b.doctorid = {$doctorid} and a.createtime >= '{$startdate}' and a.createtime < '{$enddate}'
                and (a.json_content like '%\"degree\":\"4\"%' or a.json_content like '%\"degree\":\"3\"%')
                group by a.patientid ";
        $rows = Dao::queryRows($sql);

        $list = [];
        foreach ($rows as $row) {
            $contents = explode('|', $row['content']);

            $contentlist = [];
            foreach ($contents as $content) {
                $arr = json_decode($content, true);
                $contentlist[] = $arr['name'] . $arr['degree'] . "级";
            }
            $contentstr = implode(' ', $contentlist);

            $list["{$row['patientid']}"] = [
                'patientid' => $row['patientid'],
                'name' => $row['name'],
                'sex' => $row['sex'],
                'age' => $row['age'],
                'disease_name' => $row['disease_name'],
                'content' => $contentstr
            ];
        }

        return $list;
    }

    /**
     * 活跃患者
     *
     * @param $doctorid
     * @param $startdate
     * @param $enddate
     * @param $patientids
     * @return array
     */
    public function actives($doctorid, $startdate, $enddate, $patientids) {
        $sql = "select a.patientid, b.name, c.name as 'disease_name',if(b.sex = 1, '男', '女') as sex, year(now()) - year(b.birthday) as age
                from pipes a
                inner join patients b on b.id = a.patientid
                inner join diseases c on c.id = b.diseaseid
                where a.objtype in ('WxTxtMsg','WxPicMsg','Paper') and b.doctorid = {$doctorid} and a.createtime >= '{$startdate}' and a.createtime < '{$enddate}'
                group by a.patientid ";
        $rows = Dao::queryRows($sql);

        $list = [];
        foreach ($rows as $row) {
            if (!in_array($row['patientid'], $patientids)) {
                $list["{$row['patientid']}"] = [
                    'patientid' => $row['patientid'],
                    'name' => $row['name'],
                    'sex' => $row['sex'],
                    'age' => $row['age'],
                    'disease_name' => $row['disease_name']
                ];
            }
        }

        return $list;
    }

    public function getDoctorids() {
        $cancer_diseaseidstr = Disease::getCancerDiseaseidsStr();
        $sql = "select distinct a.id
                from doctors a
                inner join doctordiseaserefs b on b.doctorid = a.id
                inner join doctorconfigs c on c.doctorid = a.id
                inner join doctorconfigtpls d on d.id = c.doctorconfigtplid
                where b.diseaseid in ($cancer_diseaseidstr) and d.code = 'pipe_list_push' 
                and c.status = 1";

        return Dao::queryValues($sql);
    }
}

// //////////////////////////////////////////////////////

echo __FILE__ . "\n";

$process = new Rpt_day_doctor_data_cancer(__FILE__);
$process->dowork();