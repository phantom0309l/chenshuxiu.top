<?php
include_once("CheckupBase.class.php");

abstract class CheckupImp implements CheckupBase
{
    protected $out_case_no;
    protected $patient;
    protected $user;
    protected $check_date = '0000-00-00';     //录入日期
    protected $createtime = '0000-00-00';     //化验日期，评估日期，发作日期
    protected $checkuptplid;
    protected $checkup;
    protected $hospitalstr = '';
    protected $revisitrecord;
    protected $sheets = array();

    public function getPatientByOut_case_no($out_case_no)
    {
        $cond = " and out_case_no = '{$out_case_no}'  and doctorid = 33 ";

        return Dao::getEntityByCond('Patient', $cond);
    }

    public function setPatientAndMyselfUser()
    {
        $this->patient = self::getPatientByOut_case_no($this->out_case_no);
        //获取本人user
        $this->user = UserDao::getMyselfByPatientid($this->patient->id);
    }

    public function createCheckup()
    {
        $row = array();
        $row["patientid"] = $this->patient->id;
        $row["doctorid"] = $this->patient->doctorid;
        $row["checkuptplid"] = $this->checkuptplid;
        $row["check_date"] = $this->check_date;
        $row["createtime"] = $this->createtime;
        $row['hospitalstr'] = $this->hospitalstr;

        $this->checkup = Checkup::createByBiz($row);
    }

    public function createRevisitRecord()
    {
        $revisitrecord = RevisitRecordDao::getByPatientidThedate($this->patient->id, $this->createtime);
        if (false == $revisitrecord instanceof RevisitRecord) {
            $row = array();

            $row['userid'] = $this->user->id ? $this->user->id : 0;
            $row['patientid'] = $this->patient->id;
            $row['doctorid'] = 33;
            $row['thedate'] = $this->createtime;

            $this->revisitrecord = RevisitRecord::createByBiz($row);
        }
    }

    public function createXanswerSheet()
    {
        if ($this->user instanceof User) {
            $patient_or_user = $this->user;
        } else {
            $patient_or_user = $this->patient;
        }

        $maxXAnswer = XWendaService::doPost($this->sheets, $patient_or_user, 'Checkup', $this->checkup->id);
        $this->checkup->xanswersheetid = $maxXAnswer->xanswersheetid;
    }

    public function createAll()
    {
        $this->setPatientAndMyselfUser();
        $this->createCheckup();
        $this->createRevisitRecord();
        $this->createXanswerSheet();
    }

    //下拉或单选加其他
    public function selectAndOther($xquestionid, $option)
    {
        $xoption = XOption::getByXQuestionidAndContent($xquestionid, $option);
        if (false == $xoption instanceof XOption) {
            $xoption = XOption::getByXQuestionidAndContent($xquestionid, '其他');
            $content = $option;
        } else {
            $content = '';
        }

        $arr = array();
        $arr['options'][0] = $xoption->id;
        $arr['content'] = $content;

        return $arr;
    }

    public function display()
    {
        echo "[patient={$this->patient->id}]";
        echo "[out_case_no={$this->patient->out_case_no}]";
        echo "[checkup={$this->checkup->id}]";
        if ($this->revisitrecord instanceof RevisitRecord) {
            echo "[revisitrecord={$this->revisitrecord->id}]";
        } else {
            echo "[revisitrecord already]";
        }
        echo "[xanswersheet={$this->checkup->xanswersheetid}]\n";
    }

    public function getFixStr($str, $default = '')
    {
        if (empty($str) && $str != '0') {
            return $default;
        }

        $str = trim(str_replace(array("\n", "\t"), "", $str));
        $str = trim(str_replace(array("（"), "(", $str));
        $str = trim(str_replace(array("）"), ")", $str));

        return $str;
    }

    public function getCheckuptplidByEnameAndDoctorid($ename, $doctorid)
    {
        $cond = " and ename = '{$ename}' and doctorid = {$doctorid} ";

        return Dao::getEntityByCond("CheckupTpl", $cond)->id;
    }
}
