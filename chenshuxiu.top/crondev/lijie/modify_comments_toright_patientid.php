<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-7-14
 * Time: 上午11:44
 */
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

class Modify_comments_toright_patientid
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "SELECT id FROM comments WHERE content LIKE '%已发送催用药消息%' ";

        $ids = Dao::queryValues($sql);

        $i = 0;
        foreach($ids as $id){
            $comment = Comment::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $content = $comment->content;

//            $a = preg_match_all('/[0-9]{2}/', $content, $arr);

//            $a = preg_replace('/\D/s', '', $content);

            $arr = split(' ', $content);

            $length = count($arr);
            $index = $length - 2;
            $a = $arr[$index];

            if($a){
                $patientid = $a;
            }
            echo ("\n========".$patientid);

            if($patientid){
                $patient = Patient::getById($patientid);
                if($patient instanceof Patient){
                    $user = $patient->getMasterUser();

                    $comment->set4lock('userid', $user->id);
                    $comment->set4lock('patientid', $patientid);
                    $comment->typestr = "reminddrug";
                }
            }

            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Modify_comments_toright_patientid.php]=====");

$process = new Modify_comments_toright_patientid();
$process->dowork();

Debug::trace("=====[cron][end][Modify_comments_toright_patientid.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
