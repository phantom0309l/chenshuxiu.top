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

// Debug::$debug = 'Dev';

class Create_user_by_wxuser
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select id from wxusers where userid=0";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $wxuser = WxUser::getById($id);
            if ($wxuser instanceof WxUser) {
                echo "\n====[WxUser][{$id}]===\n";

                $userid = $this->findUserid($id);
                $createtime = $wxuser->createtime;
                $updatetime = $wxuser->updatetime;
                $unionid = $wxuser->unionid;
                $name = $wxuser->nickname;
                $provincestr = $wxuser->province;
                $citystr = $wxuser->city;
                $auditremark = "有wxuser没有user,新补user";

                $row = array();
                $row['id'] = $userid;
                $row['createtime'] = $createtime;
                $row['updatetime'] = $updatetime;
                $row['unionid'] = $unionid;
                $row['name'] = $name;
                $row['provincestr'] = $provincestr;
                $row['citystr'] = $citystr;
                $row['auditremark'] = $auditremark;
                $user = User::createByBiz($row);

                $wxuser->set4lock("userid", $user->id);

                $i ++;
                if ($i >= 100) {
                    $i = 0;
                    $unitofwork->commitAndInit();
                    $unitofwork = BeanFinder::get("UnitOfWork");
                }
            }
        }
        $unitofwork->commitAndInit();
    }

    private function findUserid ($wxuserid) {
        $userid = $wxuserid;
        $user = User::getById($userid);
        while ($user instanceof User) {
            $userid += 1;
            $user = User::getById($userid);
        }
        return $userid;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg]create_user_by_wxuser.php]=====");

$process = new Create_user_by_wxuser();
$process->dowork();

Debug::trace("=====[cron][end][create_user_by_wxuser.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
