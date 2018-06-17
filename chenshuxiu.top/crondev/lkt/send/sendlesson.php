<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class SendLesson
{

    public function dowork() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "SELECT patientid
                FROM (
                    SELECT a.*, d.id AS patientid
                    FROM xansweroptionrefs a
                    LEFT JOIN xanswers b ON a.xanswerid = b.id
                    LEFT JOIN xanswersheets c ON b.xanswersheetid = c.id
                    LEFT JOIN patients d ON c.patientid = d.id
                    WHERE b.xquestionid = 106314773
                    AND d.doctorid = 33
                    ORDER BY a.createtime DESC
                ) t1
                LEFT JOIN patients b ON t1.patientid = b.id
                WHERE xoptionid IN (106314774, 106314775, 106314776, 106314777, 106314778, 106314779)
                GROUP BY patientid ";

        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach ($wxusers as $wxuser) {
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $first = array(
                        "value" => "协和医院【学会自我管理,我们明天会更好】主题患教会\n",
                        "color" => "");
                    $keywords = [
                        [
                            "value" => $patient->name,
                            "color" => "#aaa"
                        ],
                        [
                            "value" => date('Y-m-d'),
                            "color" => "#ff6600"
                        ],
                        [
                            "value" => "点击查看",
                            "color" => "#ff6600"
                        ]
                    ];
                    $content = WxTemplateService::createTemplateContent($first, $keywords);
                    $url = Config::getConfig("wx_uri") . "/lesson/justforshow?lessonid=695588266&gh={$wxuser->wxshop->gh}";

                    PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "followupNotice", $content, $url);
                }
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][SendLesson.php]=====");

$process = new SendLesson();
$process->dowork();

Debug::trace("=====[cron][end][SendLesson.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
