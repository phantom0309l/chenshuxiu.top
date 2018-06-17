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

class Fixdb_xanswersheet
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        echo "\n=====================\n";
        echo date('H:i:s');
        echo "\n";

        echo $sql = "select a.id, a.score, b.score as new_score
from xansweroptionrefs a
inner join xoptions b on b.id = a.xoptionid
where a.score <> b.score";

        $rows = Dao::queryRows($sql);

        echo "\n=====================\n";
        echo date('H:i:s');
        echo "\n";

        sleep(3);

        $cnt = count($rows);

        foreach ($rows as $i => $row) {
            $id = $row['id'];
            $xansweroptionref = XAnswerOptionRef::getById($id);
            $xansweroptionref->score = $row['new_score'];

            echo "\n $i / $cnt : $id : {$row['score']} -> {$row['new_score']}";
        }

        $unitofwork->commitAndInit();

        echo "\n=====================\n";
        echo date('H:i:s');
        echo "\n";

        echo $sql = "select a.id, a.score, tt.sum_score
from xanswers a
inner join (
    select xanswerid , sum(score) as sum_score
    from  xansweroptionrefs
    group by xanswerid
) tt on tt.xanswerid = a.id
where a.score <> tt.sum_score";

        $rows = Dao::queryRows($sql);

        echo "\n=====================\n";
        echo date('H:i:s');
        echo "\n";

        sleep(3);

        $cnt = count($rows);

        foreach ($rows as $i => $row) {
            $id = $row['id'];
            $xanswer = XAnswer::getById($id);
            $xanswer->score = $row['sum_score'];

            echo "\n $i / $cnt : $id : {$row['score']} -> {$row['sum_score']}";
        }

        $unitofwork->commitAndInit();

        echo "\n=====================\n";
        echo date('H:i:s');
        echo "\n";

        echo $sql = "select a.id , a.score , tt.sum_score
from xanswersheets a
inner join (
    select xanswersheetid , sum(score) as sum_score
    from  xanswers
    group by xanswersheetid
) tt on tt.xanswersheetid = a.id
where a.score <> tt.sum_score";

        $rows = Dao::queryRows($sql);

        echo "\n=====================\n";
        echo date('H:i:s');
        echo "\n";

        sleep(3);

        $cnt = count($rows);

        foreach ($rows as $i => $row) {
            $id = $row['id'];
            $xanswersheet = XAnswerSheet::getById($id);
            $xanswersheet->score = $row['sum_score'];

            echo "\n $i / $cnt : $id : {$row['score']} -> {$row['sum_score']}";
        }

        $unitofwork->commitAndInit();
    }
}

$process = new Fixdb_xanswersheet();
$process->dowork();
