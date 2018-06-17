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

class Delete_one_checkup
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

    public function dowork()
    {
        //从文件中导入徐雁患者
        $json_string = file_get_contents("/tmp/1000.txt");
        $patientinfos = json_decode($json_string, true);

        // $patientids = $this->getDeletePatientidArr();

        $i = 0;
        foreach ($patientinfos as $a) {
            $unitofwork = BeanFinder::get("UnitOfWork");

            $i++;

            $patient = $this->getPatientByOut_case_no($this->getFixStr($a['病历号']));
            if(false == $patient instanceof Patient){
                continue;
            }
            echo "[---------------------------------------第{$i}个患者:{$patient->name} {$a['病历号']}----------------------------------------]\n";

            //删除所有checkup
            // $checkups = $this->getCheckupsByPatientid($patient->id);

            //删除单个类型的checkup
            $checkups = $this->getCheckupsByPatientidAndCheckuptplid($patient->id, 107372399);

            foreach ($checkups as $checkup) {
                //删除xanswer
                $this->deleteXanswers($checkup->xanswersheetid);

                //删除xanswersheet
                $this->deleteXanswerSheet($checkup);

                //删除checkup
                $checkup->remove();
                echo "[delete Patient id = {$patient->id}]\n";
            }

//             $revisitrecords = RevisitRecordDao::getListByPatientidDoctorid($patient->id, 33);

//             //删除revisitrecord
//             foreach ($revisitrecords as $revisitrecord) {
//                 echo "[-----------------------------{$revisitrecord->id}-------------------------------]\n";
//                 $revisitrecord->remove();
//             }

            $unitofwork->commitAndInit();
        }
    }
}

$delete_one_checkup = new Delete_one_checkup();
$delete_one_checkup->dowork();
