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

class Fix_citys extends CronBase
{
    // 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'month';
        $row["title"] = '每月1号,00:00 更新citys ';
        return $row;
    }

    // 重载
    protected function needFlushXworklog() {
        return true;
    }

    // 重载
    protected function needCronlog() {
        return true;
    }

    // 重载
    protected function doworkImp() {
        // 国家统计局行政区代码
        $url = 'http://www.stats.gov.cn/tjsj/tjbz/xzqhdm/';

        $ch = curl_init();
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);

        preg_match_all('/<div\sclass=\"center_list\">\s*?(.*?)\s*?<\/div>/is', $file_contents, $new);
        DBC::requireNotEmpty($new[1][0], "获取最新的省市区代码页面失败 1001");

        preg_match_all('/<li>\s*?<a\shref=\"(.*?)\"/is', $new[1][0], $newhtml);
        DBC::requireNotEmpty($new[1][0], "获取最新的省市区代码页面失败 1002");

        $htmlurl = str_replace('./', '', $newhtml[1][0]);
        $url .= $htmlurl;

        $ch = curl_init();
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);

        preg_match_all('/<p\sclass=\"MsoNormal\">\s*?(.*?)\s*?<\/p>/is', $file_contents, $pronce_code);
        DBC::requireNotEmpty($pronce_code[1], "获取最新的省市区代码页面失败 1003");

        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;

        foreach ($pronce_code[1] as $a) {
            preg_match_all('/<span\slang=\"EN-US\">(.*?)<span>/is', $a, $code);
            preg_match_all('/<span\sstyle=\"font-family:\s宋体\">(.*?)<\/span>/is', $a, $str);

            // 去空格，半角和全角
            if (count($str[1]) > 1) {
                $code = str_replace(' ','',$code[1][0]);
                $code = str_replace('　','',$code);
                $str = str_replace(' ','',$str[1][1]);
                $str = str_replace('　','',$str);
            } else {
                $code = str_replace(' ','',$code[1][0]);
                $code = str_replace('　','',$code);
                $str = str_replace(' ','',$str[1][0]);
                $str = str_replace('　','',$str);
            }

            if ($str == '市辖区') {
                continue;
            }
            $city = City::getById($code);
            if ($city instanceof City) {
                if ($city->name != $str) {
                    $i ++;

                    Debug::warn("{$i} update city[{$city->code}]:[{$city->name}] => [{$str}]");
                    $city->name = $str;
                }
            } else {
                $i ++;

                $row = [];
                $row['id'] = $code;
                $row['code'] = $code;
                $row['name'] = $str;

                $city = City::createByBiz($row);

                Debug::warn("{$i} create city[{$city->code}]:[{$city->name}] => [{$str}]");
            }

            $city->last_check_date = date('Y-m-d');
            echo "{$city->id} => {$city->name}\n";
        }

        $unitofwork->commitAndInit();
    }

}

$test = new Fix_citys(__FILE__);
$test->dowork();