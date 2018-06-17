<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Crontab_check
{
    public function doWork(){
        $crontab_file = ROOT_TOP_PATH . "/cron/crontab/crontab";

        $str = file_get_contents($crontab_file);
        $arr = explode("\n", $str);
        foreach($arr as $i => $line){
            $reg = "/^(.+)\s+www\s+(.+)$/";
            preg_match($reg, $line, $matchArr);
            if(count($matchArr)){
                $star_str = trim($matchArr[1]);
                $filename_str = $matchArr[2];

                //注释跳过
                if('#' == substr($star_str, 0, 1)){
                    continue;
                }

                //检查※数是否正确
                $star_cnt = count( explode(" ", $star_str) );
                $n = $i + 1;
                if($star_cnt == 5){
                    //echo "正确,[{$n}]\n";
                }else{
                    echo "\n错误,[{$n}]\n";
                    echo "a[{$star_str}]b[{$filename_str}]\n";
                }

                //检查文件是否存在
                $filename = explode(">>", $filename_str);
                $filename = trim($filename[0]);
                $filename = explode("/cron/", $filename);
                $filename = trim($filename[1]);
                $filename = explode(" ", $filename);
                $filename = trim($filename[0]);

                $filename = ROOT_TOP_PATH . "/cron/" . $filename;
                if( !file_exists($filename) ){
                    echo "file不存在 [{$n}][{$filename}]\n";
                }
            }else{
                //echo "没有匹配上\n";
            }

        }
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Crontab_check.php]=====");
$a = new Crontab_check();
$a->doWork();
Debug::trace("=====[cron][end][Crontab_check.php]=====");
echo "\n-----end----- " . XDateTime::now();
