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

class Plan_qdxz_paper extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 19:00, 给患者发送气道狭窄患者量表';
        return $row;
    }

    protected function needFlushXworklog() {
        return true;
    }

    protected function needCronlog() {
        return $this->cronlog_brief > 0;
    }

    protected function doworkImp() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getIds();

        $brief = 0;
        $logcontent = '';

        $papertpl = PaperTplDao::getByEname('dyspnea');

        foreach ($ids as $id) {
            $plan_qdxz = Plan_qdxz::getById($id);

            // 发送模板消息
            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/plan_qdxz/one?plan_qdxzid={$id}&papertplid={$papertpl->id}";

            $first = [
                "value" => "呼吸困难量表",
                "color" => ""
            ];

            $keywords = [
                [
                    "value" => "{$plan_qdxz->patient->name}",
                    "color" => "#ff6600"
                ],
                [
                    "value" => date('Y-m-d'),
                    "color" => "#ff6600"
                ],
                [
                    "value" => "请点击详情进行填写",
                    "color" => "#ff6600"
                ]
            ];
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToWxUserBySystem($plan_qdxz->wxuser, 'followupNotice', $content, $url);

            $plan_qdxz->status = 1;

            $brief ++;
            $logcontent .= $plan_qdxz->id . " ";

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        echo "\n{$this->cronlog_brief} {$this->cronlog_content} \n";

        $unitofwork->commitAndInit();
    }

    private function getIds () {
        $today = date('Y-m-d');

        $sql = "select id
            from plan_qdxzs
            where plan_date = '{$today}' and status = 0 ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }
}

$test = new Plan_qdxz_paper(__FILE__);
$test->dowork();
