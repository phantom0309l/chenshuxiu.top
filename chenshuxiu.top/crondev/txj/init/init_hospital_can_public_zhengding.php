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

// Debug::$debug = 'Dev';

class Init_hospital_can_public_zhengding
{
    public function dowork () {
        $sql = "select id from hospitals
where id in (
367,19,33,29,59,147,2,143,12,30,78,81,594,38,61,431,202,218,136, 26,137,607,606,23,
24,227,198,162,36,204,233,87,89,76,8,372,111,374,319,243,382,293,10,190,269,366,176,433,125,434,126,216,73,71,72,215,171,
35,450,531,278,20,569,445,442,304,33,419,1,39,180,473,472,27,520,449,247,443,368,21,188,156,441,447,446,439,440,451,164,146,464,155,254,353,469,294,468,547,568,567,62,14,16,15,228,471,206,416,435,13,31,348,68,341,339,79,589,32,69,70,96,603,52,60,221,544,67,555,488,554,272,22,516,561,543,491,138,51,308,583,25,23,106,132,438,370,135,196,37,535,134,65,512,510,511,532,64,209,487,536,371,537,521,205,99,509,550,98,97,265,100,101,203,545,546,103,102,411,189,456,139,489,436,437,66,130,502,289,113,117,564,114,115,457,116,394,508,393,506,503,507,323,429,430,428,86,85,128,562,563,482,131,407,377,376,406,402,392,120,373,403,404,300,401,121,405,3,408,400,11,398,467,375,17,399,385,413,45,41,329,483,386,383,43,414,384,522,42,109,48,47,380,379,108,56,381,301,10,9,466,465,235,150,6,176,187,529,236,261,528,391,326,427,565,477,493,476,245,492,494,273,552,241,553,530,557,461,527,538,462,240,540,539,239,541,542,558,501,112,474,559,125,459,513,458,486,579,498,499,497,518,410,124,549,517,500,259,455,74,110,533,534,571,127,123,479,481,480,95
)";

        $ids = Dao::queryValues($sql);
        $i = 0;
        $unitofwork = BeanFinder::get("UnitOfWork");
        foreach ($ids as $id) {
            $hospital = Hospital::getById($id);
            if ($hospital instanceof Hospital) {
                $i ++;
                echo "\n====[$i][{$id}]===\n";
                $hospital->can_public_zhengding = 0;
            }
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_hospital_can_public_zhengding.php]=====");

$process = new Init_hospital_can_public_zhengding();
$process->dowork();

Debug::trace("=====[cron][end][Init_hospital_can_public_zhengding.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
