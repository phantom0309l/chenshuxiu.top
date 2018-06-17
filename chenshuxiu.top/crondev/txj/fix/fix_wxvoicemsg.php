<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// 修复pipes表中doctorid为０的数据
class Fix_wxvoicemsg
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from wxvoicemsgs where createtime > '2018-05-16' and createtime < '2018-05-17' and patientid not in (683861416)";
        //取出pipe中所有doctorid为0的pipeid
        $ids = Dao::queryValues($sql);

        $i = 0;
        foreach($ids as $id){
            sleep(2);
            $wxvoicemsg = WxVoiceMsg::getById($id);
            $url = $wxvoicemsg->getWxVoiceUrl4Fetch();
            echo "\n=====[id][{$id}]=======\n";
            echo "\n=====[url][{$url}]=======\n";

            $old_voiceid = $wxvoicemsg->voiceid;
            echo "\n=====[old_voiceid][{$old_voiceid}]=======\n";

            $voice = Voice::createByFetch($url, $wxvoicemsg->wxuserid);
            $wxvoicemsg->voiceid = $voice->id;

            $i++;
            if($i >= 100){
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_wxvoicemsg.php]=====");

$process = new Fix_wxvoicemsg();
$process->dowork();

Debug::trace("=====[cron][end][Fix_wxvoicemsg.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
