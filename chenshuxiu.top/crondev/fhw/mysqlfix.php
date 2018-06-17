<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);
//Debug::$debug = 'Dev';

class Mysqlfix
{
    public function input () {
        $unitofwork = BeanFinder::get("UnitOfWork");

//        $sql = "select id
//                from optasks
//                where id >= 200000000 and id < 300000000 ";
        $sql = "select id
                from optasks
                where id >= 700000000 and id < 800000000 ";
        $ids = Dao::queryValues($sql);

        /*
        `o_doctorid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'doctorid',
        `o_doctorgroupid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'doctorgroupid',
        `o_diseasegroupid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'diseasegroupid',
        `o_optasktplid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'optasktplid',
        `o_opnodeid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'opnodeid',
        `o_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0：初始化  1：完成并关闭  2：系统脚本关闭  3：未完成关闭 4：只完成未关闭',
        `o_plantime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '计划跟进时间',
        `op_o_level` tinyint(4) NOT NULL DEFAULT '2' COMMENT '任务等级，默认先为2级',
        `op_p_level` tinyint(4) NOT NULL DEFAULT '2' COMMENT '患者等级，取反，例如 4取反为-4',
        `p_auditorid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '责任人',
        `p_createtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
        `p_patientstageid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '患者阶段',
        `p_patientgroupid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '患者分组',
        `p_mgtplanid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '责任人',
        `p_is_see` tinyint(4) NOT NULL DEFAULT '1' COMMENT '可以直接在任务页看到的患者 1：可以看到 0：直接看到',
         * */
        $i = 0;
        $all = count($ids);
        foreach ($ids as $id) {
            $sql = "select patientid,doctorid,diseaseid,optasktplid,opnodeid,status,plantime,level
                    from optasks 
                    where id = {$id} ";
            $optaskfileds = Dao::queryRow($sql);

            /*
            [id] => 200015288
            [doctorid] => 477
            [doctorgroupid] => 298301116
            [diseasegroupid] => 3
            [optasktplid] => 123261855
            [opnodeid] => 0
            [status] => 0
            [plantime] => 2017-04-01 15:21:51
            [o_level] => -2
            [auditorid] => 0
            [createtime] => 2016-11-14 11:03:55
            [patientstageid] => 1
            [patientgroupid] => 2
            [mgtplanid] => 0
            [auditstatus] => 1
            [p_level] => -100
             * */
            $sql = "select a.id,a.doctorid,d.doctorgroupid,c.diseasegroupid,a.optasktplid,a.opnodeid,a.status,a.plantime,a.level as 'o_level',
                           b.level as 'p_level',b.auditorid,b.createtime,b.patientstageid,b.patientgroupid,b.mgtplanid,b.status,b.auditstatus
                    from optasks a 
                    inner join patients b on b.id = a.patientid 
                    inner join diseases c on c.id = a.diseaseid 
                    inner join doctors d on d.id = a.doctorid 
                    where a.id = {$id} ";
            $indexFileds = Dao::queryRow($sql);

            if (empty($indexFileds)) {
                continue;
            }

            // order by level desc, plantime asc, patient_level desc
            $indexFileds['o_level'] *= -1;
            $indexFileds['p_level'] *= -1;

            if ($indexFileds['status'] == 1 || ($indexFileds['status'] == 0 && $indexFileds['auditstatus'] == 0)) {
                $indexFileds['p_is_see'] = 1;
            } else {
                $indexFileds['p_is_see'] = 0;
            }

            $sql = "insert into xoptindexs 
                    (
                        id,o_doctorid,o_doctorgroupid,o_diseasegroupid,o_optasktplid,o_opnodeid,o_status,o_plantime,op_o_level,
                        op_p_level,p_auditorid,p_createtime,p_patientstageid,p_patientgroupid,p_mgtplanid,p_is_see
                    )
                    values 
                    (
                        {$indexFileds['id']},{$indexFileds['doctorid']},{$indexFileds['doctorgroupid']},{$indexFileds['diseasegroupid']},
                        {$indexFileds['optasktplid']},{$indexFileds['opnodeid']},{$indexFileds['status']},'{$indexFileds['plantime']}',{$indexFileds['o_level']},
                        {$indexFileds['p_level']},{$indexFileds['auditorid']},'{$indexFileds['createtime']}',{$indexFileds['patientstageid']},
                        {$indexFileds['patientgroupid']},{$indexFileds['mgtplanid']},{$indexFileds['p_is_see']}
                    )";
            Dao::executeNoQuery($sql);

            $i ++;

            $rate = round($i / $all, 2) * 100 . "%";
            if ($i % 100 == 0) {
                echo "{$i}/{$all} {$rate}\n";

                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
//            echo $sql . "\n";
//
//            print_r($indexFileds);
        }

        if ($i % 100 != 0) {
            echo "{$i}/{$all}\n";
        }

        $unitofwork->commitAndInit();
    }
}

$test = new Mysqlfix();
$test->input();
