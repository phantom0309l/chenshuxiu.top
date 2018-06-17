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

class Add_jkw_hospital
{
    public static $totalCount=0;
    public function doWork() {
        $dir = "jiankangwang_data";
        $file_path = "{$dir}/jiankangwang_hospitalinfo_json.csv";
        $file = fopen($file_path, "a+");

        $jsons = fread($file,filesize($file_path));
        $arr = explode("\n", $jsons);

        $i = 0;
        $unitofwork = BeanFinder::get("UnitOfWork");
        foreach ($arr as $json) {
            if($i == 1000){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $i = 0;
            }
            // $unitofwork = BeanFinder::get("UnitOfWork");
            if("" == $json){
                echo "---------------json为空！---------------\n";
                continue;
            }

            $this->addOne($json);
            $i++;
        }
        $unitofwork = BeanFinder::get("UnitOfWork");
        $totalCount = self::$totalCount;
        echo "\n共抓取到 {$totalCount} 条数据";
        fclose($file);
    }

    private function addOne ($json) {
        $arr = json_decode($json, true);
        $jkw_hospitalid = $arr['jkw_hospitalid'];
        $jkw_hospital = Jkw_hospital::getById($jkw_hospitalid);
        if($jkw_hospital instanceof Jkw_hospital){
            echo "---------------已经生成过了！---------------\n";
            return;
        }
        if('' == $arr['name']){
            echo "---------------name为空！---------------\n";
            return;
        }

        // 新建地址
        $address = $this->createAddress($arr);

        // 新建爬取的医院
        $jkw_hospital_new = $this->createJkw_hospital($arr, $address);

        self::$totalCount++;
        echo "第" . self::$totalCount . "条\n";
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

    private function createJkw_hospital ($arr, $address) {
        if (isset($arr["bus_route"])) {
            $arr['bus_route'] = preg_replace('/(&#160;)/', '', $arr["bus_route"]);
        }
        if (isset($arr["brief"])) {
            $arr['brief'] = preg_replace('/(\s+\r)/', '', $arr["brief"]);
        }
        $hospital_row = array();
        $hospital_row["id"] = $arr['jkw_hospitalid'];
        $hospital_row['name'] = $arr['name'];
        $hospital_row['shortname'] = isset($arr['shortname']) ? $arr['shortname'] : "";
        $hospital_row['type'] = isset($arr['type']) ? $arr['type'] : "";
        $hospital_row['levelstr'] = isset($arr['levelstr']) ? $arr['levelstr'] : "";
        $hospital_row['mobile'] = isset($arr['mobile']) ? $arr['mobile'] : "";
        $hospital_row['addressid'] = $address->id;
        $hospital_row['picture_url'] = $arr['picture_url'];
        $hospital_row['president_name'] = isset($arr['president_name']) ? $arr['president_name'] : "";
        $hospital_row['found_year'] = isset($arr['found_year']) ? $arr['found_year'] : "";
        $hospital_row['department_cnt'] = isset($arr['department_cnt']) ? $arr['department_cnt'] : "";
        $hospital_row['employee_cnt'] = isset($arr['employee_cnt']) ? $arr['employee_cnt'] : "";
        $hospital_row['bed_cnt'] = isset($arr['bed_cnt']) ? $arr['bed_cnt'] : "";
        $hospital_row['is_yibao'] = $arr["yibao"];
        $hospital_row['website'] = isset($arr['website']) ? $arr['website'] : "";
        $hospital_row['postalcode'] = isset($arr['postalcode']) ? $arr['postalcode'] : "";
        $hospital_row['brief'] = isset($arr['brief']) ? $arr['brief'] : "";
        $hospital_row['bus_route'] = isset($arr['bus_route']) ? $arr['bus_route'] : "";
        $hospital_row['from_url'] = isset($arr['from_url']) ? $arr['from_url'] : "";
        $hospital = Jkw_hospital::createByBiz($hospital_row);

        return $hospital;
    }

}

$process = new Add_jkw_hospital();
$process->doWork();

echo "\n";
