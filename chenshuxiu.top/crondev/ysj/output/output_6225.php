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


class Output_6225
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $wxPicMsgIds = $this->getWxPicMsgIds();   // 首次获取 1600 条记录

        $i = 0;
        $j = 1;
        $data = array();
        foreach ($wxPicMsgIds as $wxPicMsgId) {
            $temp = [];
            $i ++;
            if ($i >= 40) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $wxPicMsg = WxPicMsg::getById($wxPicMsgId);
            if($wxPicMsg instanceof  WxPicMsg == false) {
                continue;
            }

            if($j > 1500) {
                break;
            }
            $temp[] = $j++;
            $temp[] = 'https://photo.fangcunyisheng.com/'.$wxPicMsg->picture->picname.'.'.$wxPicMsg->picture->picext;
            $data[] = $temp;
        }

        $headarr = array(
            "序号",
            "图片url"
        );
        ExcelUtil::createForCron($data, $headarr, "/home/yangshujie/scale/output_picture_pull_" .date('md',time()).
            ".xlsx");
        $unitofwork->commitAndInit();
    }


    /***
     * 根据患者id 对数据进行排序
     * @param array $data
     * @return array
     */
    private function getWxPicMsgIds () {
        $sql = "SELECT id FROM wxpicmsgs WHERE objtype != '' AND createtime BETWEEN '2017-11-01' AND '2018-06-06' ORDER BY createtime DESC limit 1600";
        return Dao::queryValues($sql);
    }



}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_5836.php]=====");

$process = new Output_6225();
$process->dowork();

Debug::trace("=====[cron][end][Output_5836.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
