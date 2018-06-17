<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);
//initDebugFlag(true);

// Debug::$debug = 'Dev';

class SLScale
{

    protected $patientid = null;
    protected $patientname = null;
    protected $name = null;
    protected $writer = null;
    protected $writeDate = null;

    public function dowork () {
        $dataArr = $this->getInitData();
        foreach ($dataArr as $item) {
            $this->doneOne( $item["id"], $item["name"]);
        }
    }

    private function doneOne($papertplid, $filename){
        $unitofwork = BeanFinder::get("UnitOfWork");

        $objPHPExcel = $this->getObjPHPExcel($filename);

        $sql = "select a.id
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = 537 and a.status=1
            and a.name in ('刘一正','佟雨泽','杨雨泽','周楷杰','张启轩','李致远','高宏博','耿浩铭','明泳桐','赵承骞','王芃睿','李若萱','李金泽')
            group by a.id";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            $patientid = $this->patientid = $id;
            $patientname = $this->patientname = $patient->name;

            $papers = PaperDao::getListByPatientidPapertplid( $id, $papertplid );
            foreach( $papers as $paper ){
                $writer = $this->writer = $paper->writer;
                $writeDate = $this->writeDate = $paper->createtime;

                $temp = $this->getBaseArr($papertplid);
                $answers = $this->getAnswers( $paper->xanswersheetid );
                $result = array_merge($temp, $answers);
                $this->insertOne( $objPHPExcel, $filename, $result );

            }
            $i ++;
            if ($i >= 10) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }
        $unitofwork->commitAndInit();
    }

    private function getBaseArr($papertplid){
        //常规
        if(100996299 == $papertplid){
            $temp = array($this->patientname, $this->writeDate, $this->writer);
        }

        //brief
        if(103089966 == $papertplid){
            $temp = array($this->patientname, $this->writeDate, $this->writer);
        }

        //weiss
        if(103089442 == $papertplid){
            $temp = array($this->patientname, $this->writeDate, $this->writer);
        }
        return $temp;

    }

    private function getInitData(){
        $arr = array(
            array(
                "id" => 100996299,
                "name" => "SL_DQ18条.xlsx"
            ),
            array(
                "id" => 103089966,
                "name" => "SL_BRIEF.xlsx"
            ),
            array(
                "id" => 103089442,
                "name" => "SL_WEISS.xlsx"
            )
        );
        return $arr;
    }

    private function insertOne( $objPHPExcel, $filename, $arr ){
        $currentSheet = $objPHPExcel->getActiveSheet();
        $row = $currentSheet->getHighestDataRow() + 1;
        foreach ($arr as $i => $a) {
            $currentSheet->setCellValueByColumnAndRow($i, $row, $a);
        }
        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $fileurl = "/home/taoxiaojin/scale/{$filename}";
        $objWriter->save($fileurl);

    }

    private function getObjPHPExcel($filename){
        $objPHPExcel = new PHPExcel();
        $PHPReader = new PHPExcel_Reader_Excel2007();
        $path = "/home/taoxiaojin/scale";
        $filepath = $path."/{$filename}";
        // 检测子目录是否存在;
        if (! is_dir($path)){
            mkdir($path, 0777, true); // 不存在则创建;
        }
        // 检测文件是否存在;
        if(! file_exists($filepath)){
            file_put_contents($filepath, '', FILE_APPEND);
        }
        $objPHPExcel = $PHPReader->load($filepath);
        $objPHPExcel->addSheet(new PHPExcel_Worksheet);
        return $objPHPExcel;
    }

    private function getAnswers($xanswersheetid){
        $sql = "select a.pos as pos, b.content as content
                from xanswers a
                left join xansweroptionrefs b on b.xanswerid = a.id
                where a.xanswersheetid = :xanswersheetid order by a.pos";
        $bind = array();
        $bind[":xanswersheetid"] = $xanswersheetid;
        $arr = Dao::queryRows($sql, $bind);
        $result = array();

        foreach ($arr as $a) {
            $t = explode(".", $a["pos"]);
            if( count($t)>1 ) {
                continue;
            }
            $str = "未填写";
            if( $a["content"] ){
                $str = $a["content"];
            }
            $result[] = $str;
        }
        return $result;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][SLScale.php]=====");

$process = new SLScale();
$process->dowork();

Debug::trace("=====[cron][end][SLScale.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
