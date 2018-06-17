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

class Checkup_Fazuoshi
{
    public function dowork()
    {
        $json_string = file_get_contents("H:/Patient/zuihou/1001.txt");
        $fazuoshis = json_decode($json_string, true);

        $excludePatients = array("李傅凤","石秋生","幕秀花");
        $includePatients = array("张亚娟","王小龙","范得明");

        $zhangyajuan = array();

        $i = 0;
        $zhangyajuan[$i]['病历号'] = '1852603';
        $zhangyajuan[$i]['姓名'] = '范得明';
        $zhangyajuan[$i]['发作时间'] = '2010-09-01';
        $zhangyajuan[$i]['记录时间'] = '2010-09-01';
        $zhangyajuan[$i]['发作症状'] = '脊髓(运动),脊髓(感觉)';
        $zhangyajuan[$i]['治疗方法'] = '其他';
        $zhangyajuan[$i]['转归'] = '部分缓解';
        $zhangyajuan[$i]['发作诱因'] = '无';
        $zhangyajuan[$i]['目前诊断'] = '无';
        $zhangyajuan[$i]['备注'] = '';

        $fazuoshis[] = $zhangyajuan;

        $myfile = fopen("H:/Patient/zuihou/1001.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($fazuoshis));
        fclose($myfile);
    }
}

$checkup_Fazuoshi = new Checkup_Fazuoshi();
$checkup_Fazuoshi->dowork();
