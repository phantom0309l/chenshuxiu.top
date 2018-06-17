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

class NewUser_Citystr_fixProcess extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'minute';
        $row["title"] = '每10钟, 根据手机号计算省市名称';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return false;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {

        $unitofwork = BeanFinder::get("UnitOfWork");
        $ids = Dao::queryValues(" select id from users where provincestr='' and mobile<>'' ");

        $cnt = count($ids);

        foreach ($ids as $i => $id) {
            $user = User::getById($id);

            echo "\n ===================== ";
            echo "\n $i / $cnt id = " . $id;

            echo " ";
            echo $mobile = $user->mobile;

            $user->fixCityStr();

            echo " ";
            echo $user->provincestr;
            echo " ";
            echo $user->citystr;

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();

        return $cnt;
    }

}

// //////////////////////////////////////////////////////

$process = new NewUser_Citystr_fixProcess(__FILE__);
$process->dowork();
