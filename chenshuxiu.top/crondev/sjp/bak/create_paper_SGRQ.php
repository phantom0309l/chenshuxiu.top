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

class create_paper_SGRQ
{

    private $qsheet = null;

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $this->qsheet = XQuestionSheet::getBySn('sgrq');

        $poss = array(
            1,
            2,
            3,
            4);
        $op_contents = array();
        $op_contents[] = '一周中绝大部分时间';
        $op_contents[] = '一周中有几天';
        $op_contents[] = '一月中有几天';
        $op_contents[] = '仅在肺部感染时';
        $op_contents[] = '没有';

        foreach ($poss as $pos) {
            $this->createOptionsOfQuestion($pos, $op_contents);
        }

        $pos = 5;
        $op_contents = array();
        $op_contents[] = '超过3次';
        $op_contents[] = '3次发作';
        $op_contents[] = '2次发作';
        $op_contents[] = '1次发作';
        $op_contents[] = '没有发作';
        $this->createOptionsOfQuestion($pos, $op_contents);

        $pos = 6;
        $op_contents = array();
        $op_contents[] = '一周或更长时间';
        $op_contents[] = '3天或更长时间';
        $op_contents[] = '1至2天';
        $op_contents[] = '不超过1天';
        $op_contents[] = '没有发作';
        $this->createOptionsOfQuestion($pos, $op_contents);

        $pos = 7;
        $op_contents = array();
        $op_contents[] = '没有一天正常';
        $op_contents[] = '1到2天正常';
        $op_contents[] = '3到4天正常';
        $op_contents[] = '几乎每一天都正常';
        $op_contents[] = '每一天都正常';
        $this->createOptionsOfQuestion($pos, $op_contents);

        $pos = 8;
        $op_contents = array();
        $op_contents[] = '否';
        $op_contents[] = '是';
        $this->createOptionsOfQuestion($pos, $op_contents);

        $pos = 9;
        $op_contents = array();
        $op_contents[] = '呼吸困难严重影响了我的全部生活';
        $op_contents[] = '呼吸困难影响了我的全部生活';
        $op_contents[] = '呼吸困难影响了我的部分生活';
        $op_contents[] = '呼吸困难没有影响我的生活';
        $this->createOptionsOfQuestion($pos, $op_contents);

        $pos = 10;
        $op_contents = array();
        $op_contents[] = '我的呼吸问题使我完全停止工作';
        $op_contents[] = '我的呼吸问题影响我的工作或使我变换工作';
        $op_contents[] = '我的呼吸问题不影响我的工作';
        $op_contents[] = '我没有工作';
        $this->createOptionsOfQuestion($pos, $op_contents);

        // 11-50
        $op_contents = array();
        $op_contents[] = '是';
        $op_contents[] = '否';

        for ($i = 11; $i < 51; $i ++) {
            $this->createOptionsOfQuestion($i, $op_contents);
        }

        // $unitofwork->commitAndInit ();
    }

    private function createOptionsOfQuestion ($pos, array $op_contents) {
        // 5题
        $q = $this->qsheet->getQuestionByPos($pos);
        echo "\n\n" . $pos . " " . $q->content;

        foreach ($op_contents as $str) {
            $row = array();
            $row["xquestionid"] = $q->id;
            $row["content"] = $str;
            $row["checked"] = 0;
            $option = XOption::createByBiz($row);

            echo "\n    " . $str;
        }
    }
}

$process = new create_paper_SGRQ();
$process->dowork();

echo "\n";