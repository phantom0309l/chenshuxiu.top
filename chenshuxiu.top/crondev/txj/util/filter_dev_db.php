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

class Filter_dev_db
{

    public function dowork () {
        // 在patient name修改前修pushmsg里的患者姓名
        // $this->filterPushmsgs();
        $this->fixPatientName();
        $this->fixWxUserNickName();
        $this->fixUserNameAndMobile();
    }

    private function filterPushmsgs () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select id from pushmsgs";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $pushmsg = PushMsg::getById($id);
            if ($pushmsg instanceof PushMsg) {
                echo "\n====[PushMsg][{$id}]===\n";
                $content = $this->getContentFix($pushmsg);
                $pushmsg->content = $content;
                $i ++;
                if ($i >= 1000) {
                    $i = 0;
                    $unitofwork->commitAndInit();
                    $unitofwork = BeanFinder::get("UnitOfWork");
                }
            }
        }
        $unitofwork->commitAndInit();
    }

    private function fixPatientName () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select id from patients";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            if ($patient instanceof Patient) {
                echo "\n====[Patient][{$id}]===\n";
                $name = $patient->name;
                if ($name) {
                    $name = $this->getPrivacyName($name);
                    $patient->name = $name;
                }
                $i ++;
                if ($i >= 50) {
                    $i = 0;
                    $unitofwork->commitAndInit();
                    $unitofwork = BeanFinder::get("UnitOfWork");
                }
            }
        }
        $unitofwork->commitAndInit();
    }

    private function fixWxUserNickName () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select id from wxusers";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $wxuser = WxUser::getById($id);
            if ($wxuser instanceof WxUser) {
                echo "\n====[WxUser][{$id}]===\n";
                $nickname = $wxuser->nickname;
                if ($nickname) {
                    $nickname = $this->getPrivacyName($nickname);
                    $wxuser->nickname = $nickname;
                }
                $i ++;
                if ($i >= 50) {
                    $i = 0;
                    $unitofwork->commitAndInit();
                    $unitofwork = BeanFinder::get("UnitOfWork");
                }
            }
        }
        $unitofwork->commitAndInit();
    }

    private function fixUserNameAndMobile () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select id from users";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $user = User::getById($id);
            if ($user instanceof User) {
                echo "\n====[User][{$id}]===\n";
                $name = $user->name;
                $mobile = $user->mobile;
                $phone = $user->phone;
                if ($name) {
                    $name = $this->getPrivacyName($name);
                    $user->name = $name;
                }
                if ($mobile) {
                    $user->mobile = 13800000000 + $id;
                }
                if ($phone) {
                    $user->phone = 13900000000 + $id;
                }
                $i ++;
                if ($i >= 50) {
                    $i = 0;
                    $unitofwork->commitAndInit();
                    $unitofwork = BeanFinder::get("UnitOfWork");
                }
            }
        }
        $unitofwork->commitAndInit();
    }

    private function getContentFix ($pushmsg) {
        $sendway = $pushmsg->sendway;
        $patient = $pushmsg->patient;
        $content = $pushmsg->content;
        if ($patient instanceof Patient) {
            if ($sendway == "wechat_template") {
                $contentArr = json_decode($content, true);
                $content = json_encode($contentArr, JSON_UNESCAPED_UNICODE);
            }

            $name = $patient->name;
            if ($name) {
                $name = mb_substr($name, 1, null, 'utf-8');
                echo "\n==[name][{$name}]===\n";
                $nameLen = mb_strlen($name);
                $star = "";
                for ($i = 0; $i < $nameLen; $i ++) {
                    $star .= "*";
                }
                $content = str_replace($name, $star, $content);

                // 筛选过滤下敏感词例如：
                $filterArr = array(
                    "儿研所" => "方寸医院");
                foreach ($filterArr as $key => $v) {
                    $content = str_replace($key, $v, $content);
                }
            }
        }
        return $content;
    }

    private function getPrivacyName ($name) {
        if (empty($name)) {
            return "";
        }
        $nameFirst = mb_substr($name, 0, 1, 'utf-8');
        $len = mb_strlen($name);
        $nameSecond = "";
        for ($i = 0; $i < $len - 1; $i ++) {
            $nameSecond .= "*";
        }
        return $nameFirst . $nameSecond;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][filter_dev_db.php]=====");

$process = new Filter_dev_db();
$process->dowork();

Debug::trace("=====[cron][end][filter_dev_db.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
