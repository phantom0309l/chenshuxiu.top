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

TheSystem::init(__FILE__);

/*
脚本作用：
使用PHPExcel读取excel时，发现不能读取文件名带有中文的文件，例如 方汉文.xls
所以把文件名字修改为能识别字符，这里使用数字，因为要读取的文件过多，所有批量修改
全部文件名。
*/
class Fixdb_modifywenjian_name
{
    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        header("Content-Type: text/html; charset=gb2312");
        $handler = opendir('../fhw/xuyan_patientinfo/');
        $fileroot = "../fhw/xuyan_patientinfo/";
        /*
        其中$filename = readdir($handler)是每次循环的时候将读取的文件名赋值给$filename，为了不陷于死循环，所以还要让$filename !== false。
        一定要用!==，因为如果某个文件名如果叫’0′，或者某些被系统认为是代表false，用!=就会停止循环
        */
        $i = 1;
        while (($filename = readdir($handler)) !== false) {
            //echo basename($filename);
              //3、目录下都会有两个文件，名字为’.'和‘..’，不要对他们进行操作
              if ($filename != "." && $filename != ".." && strpos($filename, '.txt')) {
                  //4、进行处理
                  echo $fileroot.$filename."<br>";
                  rename($fileroot.$filename, $fileroot.$i.'.html');
                  $i++;
              }
        }
        //5、关闭目录
        closedir($handler);

        $unitofwork->commitAndInit();
    }
}

$fixdb_modifywenjian_name = new Fixdb_modifywenjian_name();
$fixdb_modifywenjian_name->dowork();
