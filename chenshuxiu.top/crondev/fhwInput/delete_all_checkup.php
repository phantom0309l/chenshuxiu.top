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

class Delete_all_checkup
{
    public function getListByDoctoridPrcrid($doctorid, $prcrid)
    {
        $cond = " and doctorid = :doctorid and prcrid = :prcrid ";
        $bind[':doctorid'] = $doctorid;
        $bind[':prcrid'] = $prcrid;

        return Dao::getEntityListByCond("Patient", $cond, $bind);
    }

    public function getXanswerByXanswersheetid($xanswersheetid)
    {
        $cond = " and xanswersheetid = :xanswersheetid ";
        $bind = [];
        $bind[':xanswersheetid'] = $xanswersheetid;

        return Dao::getEntityListByCond("XAnswer", $cond, $bind);
    }

    public function deleteAll($arr)
    {
        foreach ($arr as $a) {
            $a->remove();
        }
    }

    public function deleteXanswers($xanswersheetid)
    {
        $xanswers = $this->getXanswerByXanswersheetid($xanswersheetid);

        foreach ($xanswers as $xanswer) {
            if ($xanswer instanceof XAnswer) {
                $xansweroptionrefs = XAnswerOptionRef::getArrayOfXAnswer($xanswer);

                foreach ($xansweroptionrefs as $xansweroptionref) {
                    if ($xansweroptionref instanceof XAnswerOptionRef) {
                        $xansweroptionref->remove();
                    }
                }

                $xanswer->remove();
            }
        }
    }

    public function deleteXanswerSheet($checkup)
    {
        $xanswersheet = XAnswerSheet::getById($checkup->xanswersheetid);
        if ($xanswersheet instanceof XAnswerSheet) {
            $xanswersheet->remove();
        }
    }

    public function deletePcard($patient)
    {
        $cond = " and out_case_no = '{$patient->out_case_no}' and doctorid = {$patient->doctorid} ";

        $pcards = Dao::getEntityListByCond("Pcard", $cond);

        $this->deleteAll($pcards);
    }

    public function getDeletePatientidArr()
    {
        $sql = "select id
            from patients
            where doctorid=33
            and id not in(101976279,104917507,104916375,105124383)
            and id > 104526311 ";

        return Dao::queryValues($sql);
    }

    public function getDeletePcardidArr()
    {
        $sql = "select id
            from pcards
            where doctorid=33
            and patientid not in(101976279,104917507,104916375,105124383)
            and patientid > 104526311";

        return Dao::queryValues($sql);
    }

    public function getPatientByOut_case_no($out_case_no)
    {
        $cond = " and out_case_no = '{$out_case_no}'  and doctorid = 33 ";

        return Dao::getEntityByCond('Patient', $cond);
    }

    public function getCheckupsByPatientidAndCheckuptplid($patientid, $checkuptplid)
    {
        $cond = ' and status = 0 and patientid=:patientid and checkuptplid = :checkuptplid
            order by id desc ';

        $bind = array(
            ':patientid' => $patientid ,
            ':checkuptplid' => $checkuptplid
        );

        return Dao::getEntityListByCond('Checkup', $cond, $bind);
    }

    public function getCheckupsByPatientid($patientid)
    {
        $cond = ' and status = 0 and patientid=:patientid order by id desc ';

        $bind = array(':patientid' => $patientid);

        return Dao::getEntityListByCond('Checkup', $cond, $bind);
    }

    public function getFixStr($str, $default = '')
    {
        if (empty($str)) {
            return $default;
        }

        return trim(str_replace(array(
            "\n",
            "\t"), "", $str));
    }

    public function getCheckuptplidByEnameAndDoctorid($ename, $doctorid)
    {
        $cond = " and ename = '{$ename}' and doctorid = {$doctorid} ";

        return Dao::getEntityByCond("CheckupTpl", $cond)->id;
    }

    public function getCheckuptplids(){
        $checkuptplids = array();

        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('edss', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('ena', 33);;
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('fazuoshi', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('gangongneng', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('jiazhuangxiangongneng', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('kanghekangtipu', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('naojiyejiancha', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('shengongneng', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('shuitongdaodanbai4kangti', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('xuechanggui', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('yingxiangjiancha', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('bencizhenliaoyizhu', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('zhenduan', 33);
        $checkuptplids[] = $this->getCheckuptplidByEnameAndDoctorid('yongyao', 33);

        return $checkuptplids;
    }

    public function dowork()
    {
        //从文件中导入徐雁患者
        $json_string = file_get_contents("/tmp/1000.txt");
        $patientinfos = json_decode($json_string, true);

        // $patientids = $this->getDeletePatientidArr();

        $checkuptplids = $this->getCheckuptplids();

        $i = 0;
        foreach ($patientinfos as $a) {
            $unitofwork = BeanFinder::get("UnitOfWork");

            $i++;
            $patient = $this->getPatientByOut_case_no($this->getFixStr($a['病历号']));
            if(false == $patient instanceof Patient){
                continue;
            }
            echo "[---------------------------------------第{$i}个患者:{$patient->name} {$a['病历号']}----------------------------------------]\n";

            //删除所有检查
            foreach ($checkuptplids as $checkuptplid){
                //删除单个类型的checkup
                $checkups = $this->getCheckupsByPatientidAndCheckuptplid($patient->id, $checkuptplid);

                foreach ($checkups as $checkup) {
                    if($checkup->id >= 107083651){
                        //删除xanswer
                        $this->deleteXanswers($checkup->xanswersheetid);

                        //删除xanswersheet
                        $this->deleteXanswerSheet($checkup);

                        echo "[delete Patient id = {$patient->id} , checkupid = {$checkup->id}]\n";

                        //删除checkup
                        $checkup->remove();
                    }
                }
            }

            //删除revisitrecord
            $revisitrecords = RevisitRecordDao::getListByPatientidDoctorid($patient->id, 33);
            foreach ($revisitrecords as $revisitrecord) {
                if($revisitrecord->id >= 107083652){
                    echo "[-----------------------------{$revisitrecord->id}-------------------------------]\n";
                    $revisitrecord->remove();
                }
            }

            $unitofwork->commitAndInit();
        }
    }
}

$delete_all_checkup = new Delete_all_checkup();
$delete_all_checkup->dowork();
