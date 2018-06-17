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

// Debug::$debug = 'Dev';

class Init_paper_ename
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $arr = array(
            1 => array(
                0 => 4,
                1 => 15,
            ),
            2 => array(
                0 => 17,
                1 => 48,
            ),
            3 => array(
                0 => 50,
                1 => 76,
            ),
            4 => array(
                0 => 78,
                1 => 271,
            ),
            5 => array(
                0 => 273,
                1 => 341,
            ),
            6 => array(
                0 => 343,
                1 => 369,
            ),
            7 => array(
                0 => 371,
                1 => 418,
            ),
            8 => array(
                0 => 420,
                1 => 485,
            ),
            9 => array(
                0 => 487,
                1 => 542,
            ),
            10 => array(
                0 => 544,
                1 => 561,
            ),
            11 => array(
                0 => 563,
                1 => 590,
            ),
            12 => array(
                0 => 592,
                1 => 620,
            ),
            13 => array(
                0 => 622,
                1 => 653,
            ),
            14 => array(
                0 => 655,
                1 => 668,
            ),
            15 => array(
                0 => 670,
                1 => 686,
            ),
            16 => array(
                0 => 688,
                1 => 708,
            ),
            17 => array(
                0 => 710,
                1 => 718,
            ),
            18 => array(
                0 => 720,
                1 => 743,
            ),
            19 => array(
                0 => 745,
                1 => 764,
            ),
            20 => array(
                0 => 766,
                1 => 777,
            ),
            21 => array(
                0 => 779,
                1 => 784,
            ),
            22 => array(
                0 => 786,
                1 => 800,
            ),
            23 => array(
                0 => 802,
                1 => 813,
            ),
            24 => array(
                0 => 815,
                1 => 823,
            )
        );

        foreach ($arr as $k => $v) {
            $frompos = $v[0];
            $topos = $v[1];

            $sql = " update xquestions a
            inner join xquestionsheets b on b.id=a.xquestionsheetid
            set a.ename = 'section_{$k}'
            where b.id=283554386 and a.type='Radio'
            and a.pos between {$frompos} and {$topos} ";

            $modifycnts = Dao::executeNoQuery($sql);
        }

        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_A'
where b.id=283554386 and a.type='Radio'
and a.pos between 826 and 830 ";
        Dao::executeNoQuery($sql);

        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_B'
where b.id=283554386 and a.type='Radio'
and a.pos between 832 and 835 ";
        Dao::executeNoQuery($sql);

        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_C'
where b.id=283554386 and a.type='Radio'
and a.pos between 836 and 841 ";
        Dao::executeNoQuery($sql);

        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_D'
where b.id=283554386 and a.type='Radio'
and a.pos between 843 and 869 ";
        Dao::executeNoQuery($sql);

        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_1'
where b.id=284387626 and a.type='Radio'
and a.pos between 6 and 8 ";
        Dao::executeNoQuery($sql);
        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_2'
where b.id=284387626 and a.type='Radio'
and a.pos between 11 and 37 ";
        Dao::executeNoQuery($sql);
        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_3'
where b.id=284387626 and a.type='Radio'
and a.pos between 40 and 43 ";
        Dao::executeNoQuery($sql);
        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_4'
where b.id=284387626 and a.type='Radio'
and a.pos between 47 and 476 ";
        Dao::executeNoQuery($sql);
        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_A'
where b.id=284387626 and a.type='Radio'
and a.pos between 480 and 490 ";
        Dao::executeNoQuery($sql);
        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_B'
where b.id=284387626 and a.type='Radio'
and a.pos between 493 and 497 ";
        Dao::executeNoQuery($sql);
        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_C'
where b.id=284387626 and a.type='Radio'
and a.pos between 500 and 514 ";
        Dao::executeNoQuery($sql);
        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_D'
where b.id=284387626 and a.type='Radio'
and a.pos between 517 and 521 ";
        Dao::executeNoQuery($sql);
        $sql = " update xquestions a
inner join xquestionsheets b on b.id=a.xquestionsheetid
set a.ename = 'section_E'
where b.id=284387626 and a.type='Radio'
and a.pos between 524 and 530 ";
        Dao::executeNoQuery($sql);

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_paper_ename.php]=====");

$process = new Init_paper_ename();
$process->dowork();

Debug::trace("=====[cron][end][Init_paper_ename.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
