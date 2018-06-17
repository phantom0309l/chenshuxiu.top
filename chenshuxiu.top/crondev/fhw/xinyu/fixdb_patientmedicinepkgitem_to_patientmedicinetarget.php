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

class Fixdb_patientmedicinepkgitem_to_patientmedicinetarget
{
    public function dowork () {
        echo "[fixdb_patientmedicinepkgitem_to_patientmedicinetarget begin]\n";

        $sql = "select *
                from (
                  select *
                  FROM revisitrecords
                  where patientmedicinepkgid > 0
                  order by thedate desc
                )tt
                GROUP BY patientid;";

        $revisitrecords = Dao::loadEntityList('RevisitRecord',$sql);

        $unitofwork = BeanFinder::get("UnitOfWork");

        foreach ($revisitrecords as $revisitrecord) {
            if( $revisitrecord->patientmedicinepkg instanceof PatientMedicinePkg ){
                $revisitrecord->patientmedicinepkg->updatePatientMedicineTarget();
            }
        }

        $unitofwork->commitAndInit();

        echo "[fixdb_patientmedicinepkgitem_to_patientmedicinetarget end]";
    }
}

$fixdb_patientmedicinepkgitem_to_patientmedicinetarget = new Fixdb_patientmedicinepkgitem_to_patientmedicinetarget();
$fixdb_patientmedicinepkgitem_to_patientmedicinetarget->dowork();