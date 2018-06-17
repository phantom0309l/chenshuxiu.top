<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-8-14
 * Time: 上午11:44
 */
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

class Pgroup_daily_urge extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 19:00 对昨天和前天的报到至今未入组的患者发送催入组模板';
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

        $totime = date('Y-m-d', time() - 86400);
        $fromtime = date('Y-m-d', time() - 3 * 86400);

        $sql = "SELECT a.id FROM patients a
        INNER JOIN pcards b ON b.patientid=a.id
        LEFT JOIN patientpgrouprefs c ON c.patientid=a.id
        WHERE c.id IS NULL AND a.status=1 and a.subscribe_cnt>0 AND b.diseaseid=1
            AND LEFT(a.createtime, 10) > :fromtime AND LEFT(a.createtime, 10) <= :totime ";

        $bind = [];
        $bind[':fromtime'] = $fromtime;
        $bind[':totime'] = $totime;

//        $sql = "SELECT id FROM patients WHERE id IN (105917937, 105948981, 106047061)";
//        $sql = "SELECT id FROM patients WHERE id IN (105443323, 104379491, 104403953)";

        $ids = Dao::queryValues($sql, $bind);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            //sunflower项目患者不催入组
            if($patient->isInHezuo("Lilly")){
                continue;
            }

            if (false == $patient->isUnderControl()) {
                continue;
            }

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $str = "医生助理";
                    $sendContent = "{$patient->name}家长，您还没有选择好课程吗？这些课程都是从行为上面去帮助孩子改善症状的，只要家长每天花5-10分钟，严格按照课程的方法去实施，对孩子的情况基本都是有改善的。
                    \n具体操作方法： 点击【在这学习】，选择您认为最适合孩子的课程后，咱们就可以开始学习了。";
                    $first = array(
                        "value" => "",
                        "color" => "");
                    $keywords = array(
                        array(
                            "value" => $str,
                            "color" => "#aaa"),
                        array(
                            "value" => $sendContent,
                            "color" => "#ff6600"));
                    $content = WxTemplateService::createTemplateContent($first, $keywords);
                    $url = Config::getConfig("wx_uri") . "/pgroup/show?openid={$wxuser->openid}";
                    PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
                }
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new Pgroup_daily_urge(__FILE__);
$process->dowork();
