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

class Output_5670
{
    public function dowork () {
        $sql = "select wxuserid, max(cnt) as maxcnt from (
                    select count(*) as cnt,wxuserid,objtype from gameplays where wxuserid in (
                    select id from wxusers where patientid in (
                    120629235,103643475,138,110,103150217,105819631,103998503,103145531,103825351,142,77,120022085,119,386,384,269,100582067,159092986,171692136,102765537,102999539,259088136,165094636,256458236,256416216,261621936,261597876,256478056,264573486,261633916,103439027,103441657,103441443,103436793,103438103,103443269,103444493,145,100684261,106171503,103148631,103434545,103439423,103441197,100761957,106533415,385,383,106538087,106170845,137,100969329,101755393,101715001,101681851,165072536,108227429,261638506,256437676,256385706,103434899,103149447,381,165166716,159064306,103145713,171289306,498096666
                )) and objtype != '' group by wxuserid, objtype
                )tt group by tt.wxuserid";

        $rows = Dao::queryRows($sql);
        $data = array();
        foreach ($rows as $a) {
            $maxcnt = $a["maxcnt"];
            $wxuserid = $a["wxuserid"];
            echo "[{$wxuserid}]\n";
            $wxuser = WxUser::getById($wxuserid);
            if( $wxuser instanceof WxUser ){
                $unitofwork = BeanFinder::get("UnitOfWork");
                $this->createExcelImp($wxuser, $maxcnt);
                $unitofwork->commitAndInit();
            }
        }
    }

    private function createExcelImp($wxuser, $maxcnt){
        $wxuserid = $wxuser->id;
        $patientid = $wxuser->patientid;

        $data = $this->getData($wxuser, $maxcnt);

        $headarr = $this->getHeadArr($maxcnt);

        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/game/output_{$wxuserid}_{$patientid}.xlsx");
    }

    private function getHeadArr($maxcnt){
        $headarr = array(
            "patientid",
            "wxuserid",
            "游戏名",
        );

        $temp = [];
        for($i=0; $i < $maxcnt; $i++){
            $index = $i + 1;
            $temp[] = $index;
        }
        return array_merge($headarr, $temp);
    }

    private function getData($wxuser, $maxcnt){
        $data = [];
        $wxuserid = $wxuser->id;
        $objtypes = $this->getObjtypes();
        foreach($objtypes as $objtype){
            $gamePlays = $this->getGamePlays($wxuser, $objtype);
            $gamePlay_cnt = count($gamePlays);

            if($gamePlay_cnt == 0){
                continue;
            }

            $temp = [];

            //patientid
            $temp[] = $wxuser->patientid;

            //wxuserid
            $temp[] = $wxuserid;

            //游戏名
            $game_name = $gamePlays[0]->game->name;
            $temp[] = $game_name;

            foreach($gamePlays as $gamePlay){
                $temp[] = $gamePlay->createtime;
            }

            //补齐
            $temp_cnt = count($temp);
            $left_cnt = $maxcnt-$temp_cnt-3;
            for($i=0; $i < $left_cnt; $i++){
                $temp[] = "";
            }

            $data[] = $temp;
        }
        return $data;
    }

    private function getObjtypes(){
        $arr = ["GameSwmCnt", "GameFlkCnt", "GameGngCnt", "GameMbCnt", "GameSocCnt", "GameSwmpCnt", "GameBlcCnt"];
        return $arr;
    }

    private function getGamePlays($wxuser, $objtype){
        $cond = " and wxuserid = :wxuserid and objtype = :objtype";
        $bind = [];
        $bind[":wxuserid"] = $wxuser->id;
        $bind[":objtype"] = $objtype;
        return Dao::getEntityListByCond("GamePlay", $cond, $bind);
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_5670.php]=====");

$process = new Output_5670();
$process->dowork();

Debug::trace("=====[cron][end][Output_5670.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
