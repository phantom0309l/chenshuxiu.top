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

class Fetch_jkw_hospital
{
    public static $totalCount=0;
    public static $try_cnt=0;
    public function doWork() {
        $dir = "jiankangwang_data";
        $file_path = "{$dir}/jiankangwang_hospitalinfo_sites.csv";
        $file = fopen($file_path, "a+");

        $download_urls = fread($file,filesize($file_path));
        $download_urls_arr = explode("\n", $download_urls);

        $i = 0;
        $unitofwork = BeanFinder::get("UnitOfWork");
        foreach ($download_urls_arr as $url) {
            if($i == 100){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $i = 0;
            }
            // $unitofwork = BeanFinder::get("UnitOfWork");
            if("" == $url){
                echo "---------------url为空！---------------\n";
                continue;
            }

            $this->addOne($url);
            $i++;
        }
        $unitofwork = BeanFinder::get("UnitOfWork");
        $totalCount = self::$totalCount;
        echo "\n共抓取到 {$totalCount} 条数据";
        fclose($file);
    }

    private function addOne ($url) {
        $arr_url = explode("/", $url);
        $jkw_hospitalid = $arr_url[count($arr_url)-1-1];
        $jkw_hospital = Jkw_hospital::getById($jkw_hospitalid);
        if($jkw_hospital instanceof Jkw_hospital){
            echo "---------------已经抓取过了！---------------\n";
            return;
        }

        $url = trim($url) . "jianjie.html";
        $rules = $this->getRules();
        $range = $this->getRange();
        while (1 == 1) {
            // 同一个页面尝试重复抓取3次，3次没抓到跳过
            if(self::$try_cnt >= 3){
                self::$try_cnt = 0;
                break;
            }
            echo "------------开始加载 {$url}------------\n";
            // 搜索页

            // 获取抓取一个页面内容的规则
            // echo "内存1:" . memory_get_usage() . "\n";
            $search_ql = QueryList::Query($url, $range, $rules, [], null, 'UTF-8', 'CP936');
            // echo "内存2:" . memory_get_usage() . "\n";

            // 返回状态不为200
            if ($search_ql->getState() != 200 || empty($search_ql->getHtml())) {
                echo "The received content is empty!\n";
                echo "\n--------------- 2s 后重试---------------\n";
                sleep(2);
                self::$try_cnt++;
                continue;
            }

            // 爬取数据
            $data = $search_ql->getData();

            if (empty($data) || empty($data[0])) {
                echo "数据抓取失败，重新加载 Search 页面\n";
                echo "\n--------------- 2s 后重试---------------\n";
                sleep(2);
                self::$try_cnt++;
                continue;
            }

            $data[0]['from_url'] = $url;
            $arr = $this->dealData($data[0]);

            if(false == isset($arr['name']) || '' == $arr['name']){
                break;
            }
            // 新建地址
            $address = $this->createAddress($arr);

            // 新建爬取的医院
            $jkw_hospital_new = $this->createJkw_hospital($jkw_hospitalid, $address, $arr);

            self::$totalCount++;
            break;
        }

    }

    private function getRules () {
        $arr = [
            "html" => ['.w960', 'text'],
        ];
        return $arr;
    }

    private function getRange () {
        $arr = [
            "province" => ['.w960 .bread_nav p a:eq(1)', 'text'],
            "city" => ['.w960 .bread_nav p a:eq(2)', 'text'],
            "area" => ['.w960 .bread_nav p a:eq(3)', 'text'],
            "name" => ['.w960 .hospital_name h1', 'text'],
            "picture_url" => ['.w960 .mainleft .border_wrap .hp_info .hpi_img a', 'href'],
            "shortname" => ['.w960 .mainleft .border_wrap .hp_info .hpi_content ul li:eq(0) span', 'text'],
            "type" => ['.w960 .mainleft .border_wrap .hp_info .hpi_content ul li:eq(1)', 'text', '-a -b'],
            "levelstr" => ['.w960 .mainleft .border_wrap .hp_info .hpi_content ul li:eq(2) span:eq(0)', 'text'],
            "mobile" => ['.w960 .mainleft .border_wrap .hp_info .hpi_content ul li:eq(3) span:eq(0)', 'text'],
            "address_str" => ['.w960 .mainleft .border_wrap .hp_info .hpi_content ul li:eq(4) span', 'text'],
            "president_name" => ['.w960 .mainleft .border_wrap .hpbasicinfo tr:eq(1) td:eq(1)', 'text'],
            "found_year" => ['.w960 .mainleft .border_wrap .hpbasicinfo tr:eq(1) td:eq(3)', 'text'],
            "department_cnt" => ['.w960 .mainleft .border_wrap .hpbasicinfo tr:eq(2) td:eq(3) u', 'text'],
            "employee_cnt" => ['.w960 .mainleft .border_wrap: .hpbasicinfo tr:eq(2) td:eq(5) u', 'text'],
            "bed_cnt" => ['.w960 .mainleft .border_wrap .hpbasicinfo tr:eq(3) td:eq(1)', 'text'],
            "yibao" => ['.w960 .mainleft .border_wrap .hpbasicinfo tr:eq(3) td:eq(5)', 'text'],
            "brief" => ['.w960 .mainleft .border_wrap .hpcontent', 'text'],
            "website" => ['.w960 .mainleft .border_wrap .contact tr:eq(0) td a', 'text'],
            "postalcode" => ['.w960 .mainleft .border_wrap .contact tr:odd:last .tdr', 'text'],
            "bus_route" => ['.w960 .mainleft .border_wrap .contact .lasttdr', 'text'],
        ];
        return $arr;
    }

    private function dealData ($data) {
        if(isset($data['picture_url'])){
            if(0 == preg_match("/^(http:\/\/).*$/", $data['picture_url'])){
                $data['picture_url'] = "http://yyk.99.com.cn" . $data['picture_url'];
            }
        }

        if(isset($data["yibao"])){
            if("医保" == $data["yibao"]){
                $data["yibao"] = 1;
            }else if("非医保" == $data["yibao"]){
                $data["yibao"] = 2;
            }else {
                $data["yibao"] = 0;
            }
        }

        if (isset($data["brief"])) {
            $data['brief'] = preg_replace('/(&#160;)/', '', $data["brief"]);
            $data['brief'] = preg_replace('/(\s+\r)/', '', $data["brief"]);
        }

        if (isset($data["website"])) {
            if(preg_match("/^(http:\/\/yyk).*$/", $data['website'])){
                $data['website'] = "";
            }
        }

        if (isset($data["bus_route"])) {
            $data['bus_route'] = preg_replace('/(&#160;)/', '', $data["bus_route"]);
        }

        return $data;
    }

    private function createAddress ($arr) {
        $address_row = array();
        $provinceid = 0;
        if(isset($arr['province'])){
            $provinceid = $this->getIdByCityName($arr['province']);
        }
        $cityid = 0;
        if(isset($arr['city'])){
            $cityid = $this->getIdByCityName($arr['city']);
        }
        $areaid = 0;
        if(isset($arr['area']) && false == empty($arr['area'])){
            $areaid = $this->getIdByCityName($arr['area']);
        }

//        $address_row['provinceid'] = $provinceid;
//        $address_row['cityid'] = $cityid;
//        $address_row['quid'] = $areaid;
//        $address_row['content'] = isset($arr['address_str']) ? $arr['address_str'] : "";
//        $address = Address::createByBiz($address_row);
//
//        return $address;
    }

    private function getIdByCityName ($name) {
        $sql = " select id from citys where name = '{$name}' ";
        $id = Dao::queryValue($sql);

        if(is_null($id)){
            $id = 0;
        }

        return $id;
    }

    private function createJkw_hospital ($jkw_hospitalid, $address, $arr) {
        $hospital_row = array();
        $hospital_row["id"] = $jkw_hospitalid;
        $hospital_row['name'] = $arr['name'];
        $hospital_row['shortname'] = isset($arr['shortname']) ? $arr['shortname'] : "";
        $hospital_row['type'] = isset($arr['type']) ? $arr['type'] : "";
        $hospital_row['levelstr'] = isset($arr['levelstr']) ? $arr['levelstr'] : "";
        $hospital_row['mobile'] = isset($arr['mobile']) ? $arr['mobile'] : "";
        $hospital_row['addressid'] = $address->id;
        $hospital_row['picture_url'] = isset($arr['picture_url']) ? $arr['picture_url'] : "";
        $hospital_row['president_name'] = isset($arr['president_name']) ? $arr['president_name'] : "";
        $hospital_row['found_year'] = isset($arr['found_year']) ? $arr['found_year'] : "";
        $hospital_row['department_cnt'] = isset($arr['department_cnt']) ? $arr['department_cnt'] : "";
        $hospital_row['employee_cnt'] = isset($arr['employee_cnt']) ? $arr['employee_cnt'] : "";
        $hospital_row['bed_cnt'] = isset($arr['bed_cnt']) ? $arr['bed_cnt'] : "";
        $hospital_row['is_yibao'] = isset($arr["yibao"]) ? $arr["yibao"] : "";
        $hospital_row['website'] = isset($arr['website']) ? $arr['website'] : "";
        $hospital_row['postalcode'] = isset($arr['postalcode']) ? $arr['postalcode'] : "";
        $hospital_row['brief'] = isset($arr['brief']) ? $arr['brief'] : "";
        $hospital_row['bus_route'] = isset($arr['bus_route']) ? $arr['bus_route'] : "";
        $hospital_row['from_url'] = isset($arr['from_url']) ? $arr['from_url'] : "";
        $hospital = Jkw_hospital::createByBiz($hospital_row);

        return $hospital;
    }

}

$process = new Fetch_jkw_hospital();
$process->doWork();

echo "\n";
