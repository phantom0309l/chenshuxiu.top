<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

$sql = "select a.*
from users a
inner join doctors b on b.userid=a.id
inner join doctordiseaserefs c on c.doctorid = b.id
where c.diseaseid = 1 and password='fb604cd0c745c4fe2e5566c2ceaad6fc'
";

$users = Dao::loadEntityList('User', $sql);

foreach ($users as $i => $a) {
    echo "\n{$i} {$a->id} {$a->username} ";
    echo $password = $a->username . rand(300, 999);
    $a->modifyPassword($password);
}

$unitofwork = BeanFinder::get("UnitOfWork");
$unitofwork->commitAndInit();