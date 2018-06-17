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
Debug::$debug_mergexworklog = false;

class WxUserListFetchProcess extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 02:00, 抓取遗漏的wxusers';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        echo "\n\n----- fetchHeadimgs ----- 000 -----" . XDateTime::now();

        $this->fetchHeadimgs(1000);

        echo "\n\n----- updateWxUserDetails ----- 111 ----- " . XDateTime::now();

        $this->updateWxUserDetails(1000);

        echo "\n\n----- fetchOpenids ----- 222 -----" . XDateTime::now();

        $this->fetchOpenids();

        echo "\n\n----- updateWxUserDetails ----- 333 -----" . XDateTime::now();

        $this->updateWxUserDetails(3000);

        echo "\n\n----- fetchHeadimgs ----- 444 -----" . XDateTime::now();

        $this->fetchHeadimgs(3000);
    }

    public function fetchOpenids ($cnt = 1000) {
        $i = 0;
        while (true) {
            $i ++;
            try {
                $this->fetchOpenidsImp($cnt);
                break;
            } catch (Exception $ex) {}

            if ($i > 5) {
                break;
            }
        }
    }

    public function updateWxUserDetails ($cnt = 1000) {
        $i = 0;
        while (true) {
            $i ++;
            try {
                $this->updateWxUserDetailsImp($cnt);
                break;
            } catch (Exception $ex) {}

            if ($i > 5) {
                break;
            }
        }
    }

    public function fetchHeadimgs ($cnt = 1000) {
        $i = 0;
        while (true) {
            $i ++;
            try {
                $this->fetchHeadimgsImp($cnt);
                break;
            } catch (Exception $ex) {}

            if ($i > 5) {
                break;
            }
        }
    }

    public function fetchOpenidsImp () {
        $begintime = XDateTime::now();

        $unitofwork = BeanFinder::get("UnitOfWork");

        $wxshopids = Dao::queryValues("select id from wxshops where type=1 and id not in (4,5) order by id ");

        foreach ($wxshopids as $wxshopid) {

            $wxshop = WxShop::getById($wxshopid);

            $cnt = WxApi::fetchWxUserList($wxshop);

            echo "\nshopid={$wxshopid} cnt={$cnt}";
        }

        $unitofwork->commitAndInit();
    }

    // 更新用户信息
    public function updateWxUserDetailsImp ($cnt = 1000) {
        $ids = Dao::queryValues(" select id from wxusers where subscribe=1 order by id desc limit {$cnt} ");

        echo "\n\n========[updateWxUserDetails][begin]===========\n";

        $cnt = count($ids);

        $unitofwork = BeanFinder::get("UnitOfWork");

        foreach ($ids as $i => $id) {

            if ($i % 100 == 0) {
                echo "\n" . date('Y-m-d H:i:s') . " {$i} / {$cnt} = ";
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            echo ".";
            // echo " " . $wxuser->nickname;

            $wxuser = WxUser::getById($id);

            if (false == $wxuser->wxshop->isAuthServiceNo()) {
                echo " jump ";
                continue;
            }

            // 重新抓取数据
            WxApi::fetchWxUser($wxuser);

            if ($wxuser->user instanceof User && '' == $wxuser->user->unionid) {
                $wxuser->user->unionid = $wxuser->unionid;
            }
        }

        $unitofwork->commitAndInit();

        echo "\n\n========[updateWxUserDetails][end]===========\n";
    }

    public function fetchHeadimgsImp ($cnt = 1000) {
        $sql = "select id
                from wxusers
                where headimgpictureid=0 and headimgurl<>''
                order by id desc
                limit {$cnt}
                ";

        $ids = Dao::queryValues($sql, []);

        $cnt = count($ids);

        echo "\n\n========[fetchHeadimgs][begin]===========\n";

        $unitofwork = BeanFinder::get("UnitOfWork");
        foreach ($ids as $i => $id) {

            if ($i % 100 == 0) {
                echo "\n" . date('Y-m-d H:i:s') . " {$i} / {$cnt} = ";
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            echo ".";

            $wxuser = WxUser::getById($id);
            $wxuser->fetchHeadImgPicture();
        }

        $unitofwork->commitAndInit();
        echo "\n\n========[fetchHeadimgs][end]===========\n";
    }
}

// //////////////////////////////////////////////////////

$process = new WxUserListFetchProcess(__FILE__);
$process->dowork();
