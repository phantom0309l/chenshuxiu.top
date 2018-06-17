<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

include_once(dirname(__FILE__) . "/Common/CheckupFactory.class.php");

TheSystem::init(__FILE__);

class Checkup_Zhenduan
{
    public function getFixStr($str, $default = '')
    {
        if (empty($str)) {
            return $default;
        }

        return trim(str_replace(array(
            "\n",
            "\t"), "", $str));
    }

    public function getPatientByOut_case_no($out_case_no)
    {
        $cond = " and out_case_no = '{$out_case_no}' and doctorid = 33 ";

        return Dao::getEntityByCond("Patient", $cond);
    }

    public function isBoundWxUser(Patient $patient){
        $users = $patient->getUsers();

        $wxuserCount = 0;
        foreach ($users as $user){
            if($user instanceof User){
                $wxuser = WxUserDao::getMasterWxUserByUserId($user->id,7);
                if($wxuser instanceof WxUser){
                    $wxuserCount++;
                }
            }
        }

        if($wxuserCount > 0){
            return true;
        }else{
            return false;
        }
    }

    public function dowork()
    {
        $json_string = file_get_contents("/tmp/checkups/1014.txt");
        $zhenduans = json_decode($json_string, true);

        $excludePatients = array("李傅凤","石秋生","幕秀花");

        $i = 0;
        foreach ($zhenduans as $b) {

            if(true == in_array($b[0]['姓名'], $excludePatients)){
                continue;
            }

            $patient = $this->getPatientByOut_case_no($this->getFixStr($b[0]['病历号']));

            $isBoundWxuser = $this->isBoundWxUser($patient);

            $i++;
            if($isBoundWxuser){
                echo "\n-------------------out_case_no:{$b[0]['病历号']} ------------------------------------------------------------第{$i}个患者   绑定微信--------------------\n";
            }else{
                echo "\n-------------------out_case_no:{$b[0]['病历号']} ------------------------------------------------------------第{$i}个患者   未绑定微信--------------------\n";
            }

            foreach ($b as $a) {
                $unitofwork = BeanFinder::get("UnitOfWork");

                if($isBoundWxuser){
                    if($patient instanceof Patient){
                        $medicine = MedicineDao::getByName($this->getFixStr($a['药物']));
                        if($medicine instanceof Medicine){
                            $patient->doDrug($medicine, $this->getFixStr($a['录入日期'],'0000-00-00'));
                            echo "---------------------------[medicineid = {$medicine->id}]---------------------------\n";
                        }
                    }
                }else{
                    //使用静态工厂生成对象
                    $checkup = CheckupFactory::produceZhiliao();

                    //初始化数据
                    $checkup->init($a);

                    //构造sheets
                    $checkup->createSheets($a);

                    //创建checkup，revisitrecord，xanswersheet，xanswer
                    $checkup->createAll();

                    //输出信息，方便查询
                    $checkup->display();
                }

                $unitofwork->commitAndInit();
            }
//             if ($i == 10) {
//                 exit;
//             }
        }
    }
}

$checkup_Zhenduan = new Checkup_Zhenduan();
$checkup_Zhenduan->dowork();
