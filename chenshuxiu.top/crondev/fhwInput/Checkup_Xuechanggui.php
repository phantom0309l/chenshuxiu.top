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

class Checkup_Xuechanggui
{
    public function dowork()
    {
        $json_string = file_get_contents("/tmp/checkups/1003.txt");
        $xuechangguis = json_decode($json_string, true);

        $excludePatients = array("李傅凤","石秋生","幕秀花");

        $i = 0;
        foreach ($xuechangguis as $b) {

            if(true == in_array($b[0]['姓名'], $excludePatients)){
                continue;
            }

            $i++;
            echo "\n-------------------第{$i}个患者  out_case_no:{$b[0]['病历号']}--------------------\n";
            foreach ($b as $a) {
                $unitofwork = BeanFinder::get("UnitOfWork");

                //使用静态工厂生成对象
                $checkup = CheckupFactory::produceXuechanggui();

                //初始化数据
                $checkup->init($a);

                //构造sheets
                $checkup->createSheets($a);

                //创建checkup，revisitrecord，xanswersheet，xanswer
                $checkup->createAll();

                //输出信息，方便查询
                $checkup->display();

                $unitofwork->commitAndInit();
            }
//             if ($i == 10) {
//                 exit;
//             }
        }
    }
}

$checkup_Xuechanggui = new Checkup_Xuechanggui();
$checkup_Xuechanggui->dowork();
