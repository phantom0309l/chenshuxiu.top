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

class QYScale
{

    protected $patientid = null;
    protected $nickname = null;
    protected $name = null;
    protected $sex = null;
    protected $age = null;
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

        /*$sql = "select a.id
                from patients a
                inner join pcards b on b.patientid = a.id
                where b.doctorid = 151 group by a.id";*/
        $sql = "select id from patients where id in (
            101681851,384,165072536,103441657,119,101755393,
            103436793,256458236,383,103435479,103150217,106533415,
            103439027,106170845,103643475,261638506,261621936,
            256416216,102999539,100761957,256437676,106538087,
            103441443,102765537,165094636,261597876,256478056,
            165463716,103444493,103443269,103148631,269,100684261,
            385,110,103145531,145,261633916,103438103,100582067,
            103434545,100969329,103441197,108227429,101715001,
            256385706,159092986,137,171289306,264573486,259088136,159064306,
            171692136,165166716,103439423,106171503,103145713,105819631,
            103434899,386,103149447,103998503,120022085,381
            )";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            $patientid = $this->patientid = $id;

            $wxuser = $patient->createuser->createwxuser;
            $nickname = $this->nickname = $wxuser->nickname;
            $name = $this->name = PinyinUtilNew::Word2PY($patient->name, '');

            $sex = $this->sex = $this->getFixSex($patient->sex);
            $age = $this->age = $this->getAge($patient->birthday);
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
        //weiss
        if(103089442 == $papertplid){
            $temp = array($this->writeDate,$this->patientid, $this->nickname, $this->writer, $this->name, $this->sex, $this->age, $this->writeDate );
        }

        //常规
        if(100996299 == $papertplid){
            $temp = array($this->writeDate,$this->patientid, $this->nickname, $this->name, $this->writer );
        }

        //执行功能
        $arr = array(101826557,101845791,101846381);
        if( in_array($papertplid, $arr) ){
            $temp = array($this->writeDate,$this->patientid, $this->nickname, $this->name );
        }

        //青少年
        if(103085936 == $papertplid){
            $temp = array($this->writeDate,$this->patientid, $this->nickname, $this->name );
        }

        //conners
        if(100997473 == $papertplid){
            $temp = array($this->writeDate,$this->patientid, $this->nickname, $this->name );
        }

        //brief
        if(103089966 == $papertplid){
            $temp = array($this->writeDate,$this->patientid, $this->nickname, $this->name, $this->sex, "年级未知", $this->age, $this->writer, "填表人受教育年限未知", "填表人职业未知", "填表人收入未知", "儿童来自城市还是农村未知" );
        }
        return $temp;

    }

    private function getInitData(){
        $arr = array(
            array(
                "id" => 103089442,
                "name" => "WEISS.xlsx"
            ),
            array(
                "id" => 100996299,
                "name" => "DQ18条.xlsx"
            ),
            array(
                "id" => 101826557,
                "name" => "执行功能1-3.xlsx"
            ),
            array(
                "id" => 101845791,
                "name" => "执行功能4-5.xlsx"
            ),
            array(
                "id" => 101846381,
                "name" => "执行功能6-8.xlsx"
            ),
            array(
                "id" => 103085936,
                "name" => "青少年生活事件.xlsx"
            ),
            array(
                "id" => 103089966,
                "name" => "BRIEF.xlsx"
            ),
            array(
                "id" => 100997473,
                "name" => "C.xlsx"
            ),
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
        $fileurl = "/home/taoxiaojin/qy4/{$filename}";
        $objWriter->save($fileurl);

    }

    private function getObjPHPExcel($filename){
        $objPHPExcel = new PHPExcel();
        $PHPReader = new PHPExcel_Reader_Excel2007();
        $fileurl = "/home/taoxiaojin/qy4/{$filename}";
        $objPHPExcel = $PHPReader->load($fileurl);
        return $objPHPExcel;
    }

    private function getFixSex($sex){
        $str = "未知";
        if($sex == 1){
            $str = "男";
        }
        if($sex == 2){
            $str = "女";
        }
        return $str;
    }

    private function getAge($birthday){
        if( "0000-00-00" == $birthday ){
            return "未知";
        }

        $d1 = date("Y", strtotime($birthday));
        $d2 = date("Y", time());
        return $d2 - $d1;
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
Debug::trace("=====[cron][beg][QYScale.php]=====");

$process = new QYScale();
$process->dowork();

Debug::trace("=====[cron][end][QYScale.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
