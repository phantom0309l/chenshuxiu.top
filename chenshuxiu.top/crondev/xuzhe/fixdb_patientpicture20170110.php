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

class Fixdb_PatientPicture
{

    public function dowork () {

        echo "\n [Fixdb_PatientPicture] begin ";
        $unitofwork = BeanFinder::get("UnitOfWork");
        $wxpicmsgids = Dao::queryValues(" select id from wxpicmsgs ");

        foreach( $wxpicmsgids as $k => $wxpicmsgid ){
            $wxpicmsg = WxPicMsg::getById($wxpicmsgid);
            echo "\nWxPicMsgid [$k] {$wxpicmsg->id}";

            $row = array();
            $row["createtime"] =  $wxpicmsg->createtime;
            $row["wxuserid"] =  $wxpicmsg->wxuserid;
            $row["userid"] =  $wxpicmsg->userid;
            $row["patientid"] =  $wxpicmsg->patientid;
            $row["doctorid"] =  $wxpicmsg->doctorid;
            $row["objtype"] = 'WxPicMsg';
            $row["objid"] =  $wxpicmsg->id;

            PatientPicture::createByBiz($row);

            if( $k % 100 == 0){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }
        $unitofwork->commitAndInit();

        $unitofwork = BeanFinder::get("UnitOfWork");
        $checkuppictures = Dao::getEntityListByCond('CheckupPicture');

        foreach ($checkuppictures as $k => $checkuppicture) {
            echo "\nCheckupPictureid [$k] {$checkuppicture->id}";

            $row = array();
            $row["createtime"] =  $checkuppicture->createtime;
            $row["wxuserid"] =  $checkuppicture->wxuserid;
            $row["userid"] =  $checkuppicture->userid;
            $row["patientid"] =  $checkuppicture->patientid;
            $row["doctorid"] =  $checkuppicture->doctorid;
            $row["objtype"] = 'CheckupPicture';
            $row["objid"] =  $checkuppicture->id;

            PatientPicture::createByBiz($row);

            if( $k % 100 == 0){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }
        $unitofwork->commitAndInit();

        echo "\n [Fixdb_PatientPicture] finished \n";

    }
}

$process = new Fixdb_PatientPicture();
$process->dowork();
