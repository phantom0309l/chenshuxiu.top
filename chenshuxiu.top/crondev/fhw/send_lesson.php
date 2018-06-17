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

class Send_lesson
{
    public function run () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $title = "吡非尼酮（艾思瑞）观察项目";
        $img_url = "https://photo.fangcunyisheng.com/b/06/b06816bd8f7c2ce4f553e12521a7be9f.jpeg";
        $content = "";
        $wx_uri = Config::getConfig("wx_uri");

        $patientids = $this->getPatientids();
//        $patientids = [674003526];

        $k = 0;
        $cnt = count($patientids);
        foreach ($patientids as $i => $patientid) {
            $patient = Patient::getById($patientid);

            $wxusers = WxUserDao::getListByPatient($patient);
            foreach ($wxusers as $wxuser) {
                $url = "{$wx_uri}/lesson/justforshow?lessonid=699749846&gh={$wxuser->wxshop->gh}";
                WxApi::trySendKefuMewsMsg($wxuser, $url, $title, $content, $img_url);
            }

            $i++;
            if ($i % 100 == 0) {
                sleep(1);
            }
            if ($i % 100 == 0) {
                $k += 100;
                echo $k . "/" . $cnt . "\n";
                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }

        $unitofwork->commitAndInit();
    }

    public function getPatientids () {
        $sql = "select distinct p.id
                from patients p
                inner join (
                    select patientid
                    from pcards
                    where diseaseid = 2 and (
                           complication like '%皮肌炎%' or complication like '%DM%'
                        or complication like '%多发性肌炎%' or complication like '%PM%'
                        or complication like '%临床无肌病性皮肌炎%' or complication like '%CADM%'
                        or complication like '%无肌病性皮肌炎%' or complication like '%ADM%'
                        or complication like '%低肌病性皮肌炎%' or complication like '%HDM%'
                        or complication like '%系统性硬化症%' or complication like '%SSc%'
                    )
                    group by patientid
                ) t1 on t1.patientid = p.id
                inner join (
                    select c.patientid
                    from xanswers a
                    inner join xquestions b on b.id = a.xquestionid
                    inner join (
                        select t.*
                        from (
                            select a.patientid, a.xanswersheetid
                            from checkups a
                            inner join checkuptpls b on b.id = a.checkuptplid
                            where b.xquestionsheetid = 103284844 and b.id = 103284836 and a.xanswersheetid > 0 and a.patientid > 0
                            order by a.check_date desc
                        ) t
                        group by t.patientid
                    ) c on c.xanswersheetid = a.xanswersheetid
                    where ((b.content = 'FVC%' and a.content > 0 and a.content < 70) or (b.content = 'DLco%' and a.content > 0 and a.content < 80))
                    group by c.patientid
                )t2 on t2.patientid = t1.patientid
                where t1.patientid not in (
                    select a.patientid
                    from patientmedicinesheets a
                    inner join patientmedicinesheetitems b on b.patientmedicinesheetid = a.id
                    where b.medicineid = 54
                    group by a.patientid
                ) ";

        return Dao::queryValues($sql);
    }
}

$test = new Send_lesson();
$test->run();
