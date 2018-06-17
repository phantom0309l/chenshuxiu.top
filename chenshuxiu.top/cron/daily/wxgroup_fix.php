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

class Wxgroup_fix extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 03:30 更新wxuser分组';
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

        $sql = "select id from wxusers where wxshopid=1 and subscribe=1";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            echo "\nid[{$id}]\n";
            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $wxuser = WxUser::getById($id);
            if($wxuser instanceof WxUser){
                $patient = $wxuser->user->patient;
                if($patient instanceof Patient){

                    //sunflower项目的优先级高于患者等级业务，如果是合作患者，跳过患者等级脚本。
                    if($patient->isInHezuo("Lilly")){
                        continue;
                    }

                    //礼来项目，有未关闭首次电话任务的患者跳过
                    $optasktpl_firstTel = OpTaskTplDao::getOneByUnicode('firstTel:audit');
                    $optask = OpTaskDao::getOneByPatient($patient, " and optasktplid = {$optasktpl_firstTel->id} and level = 5 ");
                    if($optask instanceof OpTask){
                        $is_closed = $optask->isClosed();
                        if(!$is_closed){
                            continue;
                        }
                    }

                    $doctor = $patient->doctor;
                    if($doctor instanceof Doctor){
                        $wxuser->joinWxGroupOfADHD();
                    }
                }
            }
        }

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new Wxgroup_fix(__FILE__);
$process->dowork();
