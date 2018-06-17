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

class WxPicMsgFetchProcess extends CronBase
{

    private $cnt = 0;

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'minute';
        $row["title"] = '每分钟, 补抓5分钟前的微信发得图片';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return $this->cnt > 0;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        echo "\n";
        echo $sql = " delete from pictures where picname='b/40/b40184b40ae5a546cc6e386218009714' ";
        echo "\n";
        echo Dao::executeNoQuery($sql);

        echo "\n";
        echo $sql = "update wxpicmsgs a
                left join pictures b on b.id=a.pictureid
                set a.pictureid=0
                where a.pictureid > 0 and b.id is null ";
        echo "\n";
        echo Dao::executeNoQuery($sql);
        echo "\n";

        $to_time = date('Y-m-d H:i:s', time() - 300);

        $ids = Dao::queryValues(" select id from wxpicmsgs where ( pictureid=0 or pictureid=445230749 ) and wxpicurl<>'' and createtime < '{$to_time}' ");

        $cnt = count($ids);

        foreach ($ids as $i => $id) {

            $wxpicmsg = WxPicMsg::getById($id);
            echo "\n $i / {$cnt} [ $id ][ {$wxpicmsg->wxpicurl} ] => ";

            $picture = Picture::createByFetch($wxpicmsg->getWxPicUrl4Fetch());
            if ($picture instanceof Picture) {

                $photo_uri = Config::getConfig("photo_uri");
                $photourl = $photo_uri . "/" . $picture->getFilePath();

                if ($picture->picname == 'b/40/b40184b40ae5a546cc6e386218009714') {

                    // 图片上传失败
                    $wxpicmsg->set4lock("pictureid", 445233609);

                    echo "[errpic] [ {$picture->id} ][ $photourl ]";

                    Debug::warn("图片抓取失败: wxpicmsgid = {$id}");
                } else {
                    $wxpicmsg->set4lock("pictureid", $picture->id);

                    echo "[ {$picture->id} ][ $photourl ]";
                }
            } else {
                // 没抓取下来
                $wxpicmsg->set4lock("pictureid", 445233609);

                echo "[errpic] [ picture is null ][ $photourl ]";

                Debug::warn("图片抓取失败: wxpicmsgid = {$id}");
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }
        $unitofwork->commitAndInit();

        $this->cronlog_brief = "cnt={$cnt}";

        $this->cnt = $cnt;

        return $cnt;
    }
}

// //////////////////////////////////////////////////////

$process = new WxPicMsgFetchProcess(__FILE__);
$cnt = $process->dowork();
