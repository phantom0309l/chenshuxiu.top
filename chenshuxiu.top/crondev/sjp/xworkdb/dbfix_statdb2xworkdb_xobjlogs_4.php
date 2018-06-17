<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "1024M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Dbfix_statdb2xworkdb_xobjlogs_4
{

    public function dowork () {
        $tablenos = [];

        for ($i = 1000; $i < 1256; $i ++) {
            $tablenos[] = $i;
        }

        $tablenos[] = '201611';
        $tablenos[] = '201612';
        for ($i = 1; $i < 13; $i ++) {
            $tablenos[] = "2017" . sprintf("%02d", $i);
        }

        $objtypes = [];
        $objtypes[] = 'Rpt_date_db';
        $objtypes[] = 'Rpt_date_patientpgroup';
        $objtypes[] = 'Rpt_date_patient';
        $objtypes[] = 'Rpt_date_table';
        $objtypes[] = 'Rpt_date_wxuser';
        $objtypes[] = 'Rpt_doctor_month';
        $objtypes[] = 'Rpt_patient_month_settle';
        $objtypes[] = 'Rpt_patient_month';
        $objtypes[] = 'Rpt_patient';
        $objtypes[] = 'Rpt_week_doctor_patient';
        $objtypes[] = 'Rpt_week_ketang';
        $objtypes[] = 'Rpt_week_patient';
        $objtypes[] = 'Rpt_wxuser';
        $objtypes[] = 'PatientLog';
        $objtypes[] = 'PatientTask';
        $objtypes[] = 'PatientWork';
        $objtypes[] = 'PvLog';
        $objtypes[] = 'Ticket';
        $objtypes[] = 'Sumpile';
        $objtypes[] = 'DemoHotel';
        $objtypes[] = 'DemoRoom';
        $objtypes[] = 'DoctorSchedule';
        $objtypes[] = 'CronLog';
        $objtypes[] = 'PMSideeffectPlan';
        $objtypes[] = 'ReportReply';
        $objtypes[] = 'YishiOplog';

        $sum_cnt = 0;

        foreach ($tablenos as $a) {

            foreach ($objtypes as $objtype) {
                $sql = "select count(*) from xobjlogs{$a} where objtype = :objtype ";
                $bind = [];
                $bind[':objtype'] = $objtype;
                $cnt = Dao::queryValue($sql, $bind, 'xworkdb');

                $sum_cnt += $cnt;

                if ($cnt > 0) {
                    echo "\n[{$a}:{$objtype}] += {$cnt} = $sum_cnt ";
                    $sql = "delete from xobjlogs{$a} where objtype = :objtype ";
                    Dao::executeNoQuery($sql, $bind, 'xworkdb');
                } else {

                    echo ".";
                }
            }
        }

        echo "\n";
    }
}

$process = new Dbfix_statdb2xworkdb_xobjlogs_4();
$process->dowork();
echo "\n";
