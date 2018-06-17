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

class Fixdb_excel_to_json_test
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        require_once 'H:/workspace/PHPExcel/Classes/PHPExcel.php';     //修改为自己的目录
        //header("Content-Type:text/json;charset=utf-8");

        /*--------------------------------------------获取文件名start--------------------------------------------*/
        header("Content-Type: text/json; charset=gb2312");
        $handler = opendir('H:/Patient/output');
        $fileroot = "H:/Patient/output/";
        //2、循环的读取目录下的所有文件
        //其中$filename = readdir($handler)是每次循环的时候将读取的文件名赋值给$filename，为了不陷于死循环，所以还要让$filename !== false。一定要用!==，因为如果某个文件名如果叫’0′，或者某些被系统认为是代表false，用!=就会停止循环
        $filenames = array();
        while( ($filename = readdir($handler)) !== false )
        {
              //3、目录下都会有两个文件，名字为’.'和‘..’，不要对他们进行操作
              if($filename != "." && $filename != ".." && (strpos($filename, '.xls') || strpos($filename, '.xlsx')))
              {
                  $filenames[] = $fileroot.$filename;
                  //echo $fileroot.$filename."<br>";
              }
        }
        //5、关闭目录
        closedir($handler);

        $PHPReader = new PHPExcel_Reader_Excel2007();
        $PHPExcel = $PHPReader->load("H:/patient/output/1.xls");
        $countSheet =  $PHPExcel->getSheetCount();
        for ($i = 0 ; $i < $countSheet ; $i++) {
            if($i = 0){
                $table = 1;
                $content = 2;
            }else{
                $table = 2;
                $content = 3;
            }

            //用来获取表头,1.xls的量表最全，所有用它来去表头
            $PHPExcel = $PHPReader->load("H:/patient/output/1.xls");
            $currentSheetTableHead = $PHPExcel->getSheet(0);
            $allColumnTableHead = $currentSheetTableHead->getHighestDataColumn();
            $allRowTableHead = $currentSheetTableHead->getHighestDataRow();
            $titles = array();
            for($currentColumnTableHead= 65;$currentColumnTableHead <= ord($allColumnTableHead); $currentColumnTableHead++){
                $titles[] = $currentSheetTableHead->getCellByColumnAndRow($currentColumnTableHead - 65,$table)->getValue();/**ord()将字符转为十进制数*/
            }

            //获取表数据
            $patientinfos = array();
            $j = 0;
            foreach ($filenames as $file) {
                $PHPExcel = $PHPReader->load($file);
                $currentSheet = $PHPExcel->getSheet($i);

                if($currentSheet == null){
                    continue;
                }

                $allColumn = $currentSheet->getHighestDataColumn();
                $allRow = $currentSheet->getHighestDataRow();

                /**从第二行开始输出，因为excel表中第一行为列名*/
                for($currentRow = $content;$currentRow <= $allRow;$currentRow++){
                    /**从第A列开始输出: A=65*/
                    for($currentColumn= 65;$currentColumn <= ord($allColumn); $currentColumn++){
                        $count = $currentColumn - 65;
                        $val = $currentSheet->getCellByColumnAndRow($count,$currentRow)->getValue();/**ord()将字符转为十进制数*/
                        $title = $titles[$count];
                        $patientinfos[$j]["$title"] = $val;
                    }
                    $j++;
                }
            }

            /*--------------------------------------------将json数组写入文件中--------------------------------------------*/
            $myfile = fopen("H:/Patient/zuihou/$i.txt", "w") or die("Unable to open file!");
            fwrite($myfile, json_encode($patientinfos));
            fclose($myfile);

            unset($patientinfos);
            unset($titles);
        }

        $unitofwork->commitAndInit();
    }
}

$fixdb_excel_to_json_test = new Fixdb_excel_to_json_test();
$fixdb_excel_to_json_test->dowork();
