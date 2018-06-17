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

// Debug::$debug = 'Dev';

class update_5961
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        // 获取letters 表中所有的记录id
        $letterIds = $this->getAllLetterids();
        $i = 0;

        foreach($letterIds as $letterId) {
            $i ++;
            if ($i >= 40) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $letter = Dao::getEntityById('Letter',$letterId);

            $sql = "select wx.id from wxtxtmsgs as wx left join letters as l on wx.letterid=l.id where l.id=:id";
            $bind[':id'] = $letter->id;
            $wxTxtMsgId = Dao::queryValue($sql,$bind);

            if(empty($wxTxtMsgId)){
                $sql = "select id from wxtxtmsgs where content=:content";
                $bindByContent[':content'] = $letter->content;
                $wxTxtMsgId = Dao::queryValue($sql,$bindByContent);
            }

            echo "++++++++".$wxTxtMsgId."-------\n\n";
            $letter->objid=empty($wxTxtMsgId)?0:$wxTxtMsgId;

        }
        $unitofwork->commitAndInit();
    }

    // 获取letters 表中所有的记录id
    private function getAllLetterids () {
        $sql = 'select id from letters where objid=0';
        $ids = Dao::queryValues($sql);
        return $ids;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_5836.php]=====");

$process = new update_5961();
$process->dowork();

Debug::trace("=====[cron][end][Output_5836.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
