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

$sql = "SELECT a.id, a.patientid, a.doctorid, a.medicineid, a.first_start_date, a.last_drugchange_date, a.status FROM patientmedicinerefs a  
    INNER JOIN patients b ON a.patientid=b.id
    WHERE b.diseaseid >1 AND a.medicineid<>0 AND first_start_date <> '0000-00-00' AND last_drugchange_date <> '0000-00-00'
    ";
echo $sql, "\n";
$rows = Dao::queryRows($sql);

$unitofwork = BeanFinder::get("UnitOfWork");
$i = 1;
foreach ($rows as $row) {
    echo $i++, "\t";
    $patientid = $row['patientid'];
    echo $patientid, "\t";
    $doctorid = $row['doctorid'];
    $medicineid = $row['medicineid'];
    $first_start_date = $row['first_start_date'];
    $last_drugchange_date = $row['last_drugchange_date'];
    $status = $row['status'] == 0 ? 3 : $row['status'];
    $patient = Patient::getById($patientid);
    if (!$patient) {
        echo '患者' . $patientid . "不存在 \t";
        continue;
    }
    echo $patient->name, " \t";
    //获取患者首次用药详情
    $cond = ' AND patientid=:patientid AND doctorid=:doctorid AND medicineid=:medicineid AND record_date=:record_date';
    $bind = [
        ':patientid' => $patientid,
        ':doctorid' => $doctorid,
        ':medicineid' => $medicineid,
        ':record_date' => $first_start_date,
    ];
    $drugItem = Dao::getEntityByCond('DrugItem', $cond, $bind);
    //获取患者最新用药详情
    $cond = ' AND patientid=:patientid AND doctorid=:doctorid AND medicineid=:medicineid AND record_date=:record_date';
    $bind = [
        ':patientid' => $patientid,
        ':doctorid' => $doctorid,
        ':medicineid' => $medicineid,
        ':record_date' => $last_drugchange_date,
    ];
    $drugItem = Dao::getEntityByCond('DrugItem', $cond, $bind);
    ////////////////////////
    ////////输入数据准备完毕
    ////////////////////////

    ///////////首次用药时间
    $cond = ' AND patientid=:patientid AND doctorid=:doctorid AND thedate=:thedate';
    $bind = [
        ':patientid' => $patientid,
        ':doctorid' => $doctorid,
        ':thedate' => $first_start_date,
    ];
    $pmsheet = Dao::getEntityByCond('PatientMedicineSheet', $cond, $bind);
    if (!$pmsheet) {
        //创建pmsheet
        $fiveIds = $patient->get5Id();
        $row0 = [];
        $row0['wxuserid'] = $fiveIds['wxuserid'];
        $row0['userid'] = $fiveIds['userid'];
        $row0['patientid'] = $patientid;
        $row0['doctorid'] = $doctorid; // done pcard fix
        $row0['thedate'] = $first_start_date;
        $row0['auditorid'] = 10022;//chenshigang
        $row0['auditstatus'] = 1;
        $row0['auditremark'] = 'datafix';
        $row0['audittime'] = '2016-08-21 19:19:19';
        $row0['content'] = '';
        $row0['createby'] = 'Auditor';

        $pmsheet = PatientMedicineSheet::createByBiz($row0);
        echo "[首次]创建sheet \t";

        //创建pmsheetitem
        $row0 = [];
        $row0['patientmedicinesheetid'] = $pmsheet->id;
        $row0['medicineid'] = $medicineid;
        $row0['status'] = $status;//正常用药
        $row0['auditorid'] = 10022;
        $row0['drug_date'] = $first_start_date;
        $row0['createby'] = 'Auditor';
        $row0['auditlog'] = '';
        $row0['auditremark'] = 'datafix';
        $row0['drug_dose'] = $drugItem->drug_dose ?? '';
        $row0['drug_frequency'] = $drugItem->drug_frequency ?? '';
        $pmsitem = PatientMedicineSheetItem::createByBiz($row0);
        echo "[首次]创建item \t";
    } else {
        $cond = ' AND medicineid=:medicineid AND patientmedicinesheetid=:patientmedicinesheetid AND drug_date=:drug_date';
        $bind = [
            ':medicineid' => $medicineid,
            ':patientmedicinesheetid' => $pmsheet->id,
            ':drug_date' => $first_start_date,
        ];
        $pmsitem = Dao::getEntityByCond('PatientMedicineSheetItem', $cond, $bind);
        if (!$pmsitem) {
            //创建pmsheetitem
            $row0 = [];
            $row0['patientmedicinesheetid'] = $pmsheet->id;
            $row0['medicineid'] = $medicineid;
            $row0['status'] = $status;//正常用药
            $row0['auditorid'] = 10022;
            $row0['drug_date'] = $first_start_date;
            $row0['createby'] = 'Auditor';
            $row0['auditlog'] = '';
            $row0['auditremark'] = 'datafix';
            $row0['drug_dose'] = $drugItem->drug_dose ?? '';
            $row0['drug_frequency'] = $drugItem->drug_frequency ?? '';
            $pmsitem = PatientMedicineSheetItem::createByBiz($row0);
            echo "[首次]存在sheet创建item \t";
        }
    }

    ///////////最新用药时间
    $cond = ' AND patientid=:patientid AND doctorid=:doctorid AND thedate=:thedate';
    $bind = [
        ':patientid' => $patientid,
        ':doctorid' => $doctorid,
        ':thedate' => $last_drugchange_date,
    ];
    $pmsheet = Dao::getEntityByCond('PatientMedicineSheet', $cond, $bind);
    if (!$pmsheet) {
        //创建pmsheet
        $fiveIds = $patient->get5Id();
        $row0 = [];
        $row0['wxuserid'] = $fiveIds['wxuserid'];
        $row0['userid'] = $fiveIds['userid'];
        $row0['patientid'] = $patientid;
        $row0['doctorid'] = $doctorid; // done pcard fix
        $row0['thedate'] = $last_drugchange_date;
        $row0['auditorid'] = 10022;//chenshigang
        $row0['auditstatus'] = 1;
        $row0['auditremark'] = 'datafix';
        $row0['audittime'] = '2016-08-21 19:19:19';
        $row0['content'] = '';
        $row0['createby'] = 'Auditor';

        $pmsheet = PatientMedicineSheet::createByBiz($row0);
        echo "[末次]创建sheet \t";

        //创建pmsheetitem
        $row0 = [];
        $row0['patientmedicinesheetid'] = $pmsheet->id;
        $row0['medicineid'] = $medicineid;
        $row0['status'] = $status;//正常用药
        $row0['auditorid'] = 10022;
        $row0['drug_date'] = $last_drugchange_date;
        $row0['createby'] = 'Auditor';
        $row0['auditlog'] = '';
        $row0['auditremark'] = 'datafix';
        $row0['drug_dose'] = $drugItem->drug_dose ?? '';
        $row0['drug_frequency'] = $drugItem->drug_frequency ?? '';
        $pmsitem = PatientMedicineSheetItem::createByBiz($row0);
        echo "[末次]创建item \t";
    } else {
        $cond = ' AND medicineid=:medicineid AND patientmedicinesheetid=:patientmedicinesheetid AND drug_date=:drug_date';
        $bind = [
            ':medicineid' => $medicineid,
            ':patientmedicinesheetid' => $pmsheet->id,
            ':drug_date' => $last_drugchange_date,
        ];
        $pmsitem = Dao::getEntityByCond('PatientMedicineSheetItem', $cond, $bind);
        if (!$pmsitem) {
            //创建pmsheetitem
            $row0 = [];
            $row0['patientmedicinesheetid'] = $pmsheet->id;
            $row0['medicineid'] = $medicineid;
            $row0['status'] = $status;//正常用药
            $row0['auditorid'] = 10022;
            $row0['drug_date'] = $last_drugchange_date;
            $row0['createby'] = 'Auditor';
            $row0['auditlog'] = '';
            $row0['auditremark'] = 'datafix';
            $row0['drug_dose'] = $drugItem->drug_dose ?? '';
            $row0['drug_frequency'] = $drugItem->drug_frequency ?? '';
            $pmsitem = PatientMedicineSheetItem::createByBiz($row0);
            echo "[末次]存在创建item \t";
        }
    }
    echo "\n";
    if ($i % 100 == 0) {
       $unitofwork->commitAndInit();
    }
}
$unitofwork->commitAndRelease();

Debug::flushXworklog();
