<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 0);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

Config::setConfig("update_need_check_version", false);

class modify_menzhen_offset_daycnt
{
    public function run ($arr) {
        $j = 0;
        while ($j < 2) {
            try {
                $unitofwork = BeanFinder::get("UnitOfWork");

                $doctorid = $arr["doctorid"];
                $old_value = $arr["old_value"];
                $new_value = $arr["new_value"];

                if($old_value == $new_value){
                    break;
                }

                $flag = '';
                if ($old_value == 0 && $new_value > 0) {
                    // 提升患者等级
                    $flag = 'up';
                } elseif ($old_value > 0 && $new_value == 0) {
                    // 降低患者等级
                    $flag = 'down';
                }

                if ($flag) {
                    // 修改患者的等级
                    $sql = "select id from patients where doctorid = :doctorid";
                    $bind = [];
                    $bind[":doctorid"] = $doctorid;
                    $ids = Dao::queryValues($sql, $bind);
                    foreach ($ids as $id) {
                        $patient = Patient::getById($id);
                        if ($flag == 'up') {
                            if ($patient->level < PatientLevel::LEVEL_200) {
                                $patient->level = PatientLevel::LEVEL_200;
                            }
                        } else {
                            // 目前等级高于level_2则不降级
                            if ($patient->level == PatientLevel::LEVEL_200) {
                                $patient->level = PatientLevel::LEVEL_100;
                            }
                        }
                    }
                }

                $unitofwork->commitAndInit();
                break; // 跳出外层循环
            } catch (Exception $e) {
                $j ++;
                BeanFinder::clearBean("UnitOfWork");
                BeanFinder::clearBean("DbExecuter");
                $dbExecuter = BeanFinder::get('DbExecuter');
                $dbExecuter->reConnection();
            }
        }
        Debug::trace('menzhen_offset_daycnt success : doctorid=' . $doctorid);
        Debug::flushXworklog();

        return true;
    }
}

if ($argc < 2) {
    echo "usage: php " . basename(__FILE__) . " auditoroplog row\n";
    exit(1);
}

$json = $argv[1];
$arr = json_decode($json, true);
if (!is_array($arr)) {
    echo "menzhen_offset_daycnt arr $json is empty\n";
    exit(2);
}

$obj = new modify_menzhen_offset_daycnt();
$obj->run($arr);

Debug::flushXworklog();
