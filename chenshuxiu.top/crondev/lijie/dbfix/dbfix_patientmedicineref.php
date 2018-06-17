<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Dbfix_patientmedicineref extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'rightnow';
        $row["title"] = '修复扁鹊医生名下复制的患者的patientmedicineref数据！';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    public function doWorkImp()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from patients where clone_by_patientid>0 and doctorid=13;";

        $patientids = Dao::queryValues($sql);

        //copy的patientids
        foreach ($patientids as $patientid) {
            $patient = Patient::getById($patientid);
            $user = $patient->getMasterUser();
            $wxuser = $patient->getMasterWxUser();
            $clone_by_patientid = $patient->clone_by_patientid;
            echo "\n当前处理从【clone_by_patientid={$clone_by_patientid}】患者copy的数据";

            //patientmedicinerefs表中的 复制数据id=原始数据id-60000000
            //找到患者所有的复制数据
            $sql = "select * from patientmedicinerefs where id in
            (
                select min(id)
                from patientmedicinerefs
                where userid>0
                and patientid = :patientid
                group by patientid, medicineid
                having count(id) > 1
            )";

            $bind = [];
            $bind[":patientid"] = $clone_by_patientid;
            $patientmedicinerefs = Dao::loadEntityList('PatientMedicineRef', $sql, $bind);

            //copy的patientmedicinerefs
            foreach ($patientmedicinerefs as $patientmedicineref) {
                $patientmedicinerefid = $patientmedicineref->id;

                //原始数据
                $real_patientmedicineref = PatientMedicineRef::getById($patientmedicinerefid + 60000000);
                if ($real_patientmedicineref instanceof PatientMedicineRef){
                    if(2 == $patientmedicineref->version){
                        //patientmedicineref只修改一次patientid
                        $patientmedicineref->wxuserid = $wxuser->id;
                        $patientmedicineref->userid = $user->id;
                        $patientmedicineref->patientid = $patientid;
                    }else {
                        //patientmedicineref数据被多次修改
                        if($real_patientmedicineref->updatetime < '2018-04-20 16:19:16'){
                            $temp = $real_patientmedicineref; //备份

                            $real_patientmedicineref->status = $patientmedicineref->status;
                            $real_patientmedicineref->stop_drug_type = $patientmedicineref->stop_drug_type;
                            $real_patientmedicineref->first_start_date = $patientmedicineref->first_start_date;
                            $real_patientmedicineref->startdate = $patientmedicineref->startdate;
                            $real_patientmedicineref->stopdate = $patientmedicineref->stopdate;
                            $real_patientmedicineref->last_drugchange_date = $patientmedicineref->last_drugchange_date;
                            $real_patientmedicineref->value = $patientmedicineref->value;
                            $real_patientmedicineref->unit = $patientmedicineref->unit;
                            $real_patientmedicineref->drug_dose = $patientmedicineref->drug_dose;
                            $real_patientmedicineref->drug_frequency = $patientmedicineref->drug_frequency;
                            $real_patientmedicineref->remark = $patientmedicineref->remark;

                            $patientmedicineref->status = $temp->status;
                            $patientmedicineref->stop_drug_type = $temp->stop_drug_type;
                            $patientmedicineref->first_start_date = $temp->first_start_date;
                            $patientmedicineref->startdate = $temp->startdate;
                            $patientmedicineref->stopdate = $temp->stopdate;
                            $patientmedicineref->last_drugchange_date = $temp->last_drugchange_date;
                            $patientmedicineref->value = $temp->value;
                            $patientmedicineref->unit = $temp->unit;
                            $patientmedicineref->drug_dose = $temp->drug_dose;
                            $patientmedicineref->drug_frequency = $temp->drug_frequency;
                            $patientmedicineref->remark = $temp->remark;

                            $patientmedicineref->wxuserid = $wxuser->id;
                            $patientmedicineref->userid = $user->id;
                            $patientmedicineref->patientid = $patientid;
                        }else {
                            echo "需手动处理的。【patientmedicineref={$patientmedicinerefid}】";
                        }
                    }
                }else {
                    echo "\n未找到复制数据【patientmedicinerefid=={$patientmedicinerefid}】对应的原始数据。\n";
                }
            }
        }

        $unitofwork->commitAndInit();
    }

}

$dbfix_ismust = new Dbfix_patientmedicineref(__FILE__);
$dbfix_ismust->dowork();
