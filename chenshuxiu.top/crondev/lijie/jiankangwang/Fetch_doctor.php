<?php
/**
 * Created by Atom.
 * User: Jerry
 * Date: 2017/8/2
 * Time: 9:14
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");
//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

date_default_timezone_set('UTC');

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';
require ROOT_TOP_PATH . '/domain/third.party/QueryList/phpQuery.php';
require ROOT_TOP_PATH . '/domain/third.party/QueryList/QueryList.php';
require '../../../../core/util/PinyinUtil.new.class.php';

use QL\QueryList;

class Fetch_doctor
{

    public function doWork() {
        $fetch_url = "http://www.haodf.com/doctor/DE4r0BCkuHzduTWZaHGUIHuKomU0X.htm";
        $rules = $this->getRules();
        $range = $this->getRange();

        // mb_internal_encoding("UTF-8");
        $search_ql = QueryList::Query($fetch_url, $range, $rules, [], null, 'UTF-8', 'GB2312', true);

        // 返回状态不为200
        if ($search_ql->getState() != 200 || empty($search_ql->getHtml())) {
            echo "抓取页面返回错误!";
            return self::BLANK;
        }
        // print_r($search_ql);

        // 爬取数据
        $datas = $search_ql->getData();

        if (empty($datas)) {
            echo "抓取数据为空！";
            return self::BLANK;
        }

        // $data = $datas[0];

        print_r($datas);
    }

    private function getRules () {
        $arr = [
            "html" => ['.doctor_about', 'text'],
        ];
        return $arr;
    }

    private function getRange () {
        $arr = [
            "name" => ['.doctor_about', 'text'],
            // "headimg_url" => ['.doctor_about .middletr .tbody .ys_tx img', 'src'],
            // "title" => ['.doctor_about .middletr .tbody tr>td[valign="top"]:eq(2)', 'text'],
            // "department" => ['.doctor_about .middletr .tbody tr>td>a>h2', 'text'],
            // "brief" => ['.doctor_about .middletr .tbody tr>td[colspan="3"] #full', 'text', '-span'],
            // "be_good_at" => ['.doctor_about .middletr .tbody tr>td[colspan="3"] #full_DoctorSpecialize', 'text', '-span'],
        ];
        return $arr;
    }

}

$process = new Fetch_doctor();
$process->doWork();

echo "抓取完毕！\n";
