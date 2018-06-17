<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
ini_set('date.timezone', 'Asia/Shanghai');
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Zhiliao
{
    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $zhiliaos = array();
        $countHtml = count(glob("/tmp/htmls/*.html"));
        for ($j = 1 ; $j <= $countHtml ; $j++) {
            $contents = file_get_contents('/tmp/htmls/'.$j.'.html');

            preg_match_all('/<td>病历号：<\/td>\s*?<td>\s*?(.*?)\s*?<\/td>/is', $contents, $out_case_nos);
            preg_match_all('/<td>姓名：<\/td>\s*?<td>(.*?)<\/td>/is', $contents, $names);

            $htmlarr = explode('class="common_alert"', $contents);

            $count_m = 0;
            for ($i = 1 ; $i < count($htmlarr) ; $i++) {
                //就诊时间
                preg_match_all('/>\s*?([0-9].*?)\s*---/is', $htmlarr[$i], $jilushijians);

                preg_match_all('/<th><span>药物<\/span><\/th>\s*<th><span>开始用药<\/span><\/th>\s*<th><span>停药时间<\/span><\/th>\s*<th><span>停药原因<\/span><\/th>\s*<th><span>备注<\/span><\/th>\s*<\/tr>\s*<tr>\s*<td>\s*(.*?)\s*<\/td>\s*<td>(.*?)<\/td>\s*<td>(.*?)<\/td>\s*<td>(.*?)<\/td>\s*<td>(.*?)<\/td>\s*<\/tr>/is', $htmlarr[$i], $alls);

                print_r($alls);

                for ($m = 0 ; $m < count($alls[1]) ; $m++) {
                    $zhiliaos[$j][$count_m]['姓名'] = $names[1][0];
                    $zhiliaos[$j][$count_m]['病历号'] = $out_case_nos[1][0];
                    $zhiliaos[$j][$count_m]['录入日期'] = $jilushijians[1][0];

                    $zhiliaos[$j][$count_m]['药物'] = $alls[1][$m];
                    $zhiliaos[$j][$count_m]['开始用药'] = $alls[2][$m];
                    $zhiliaos[$j][$count_m]['停药时间'] = $alls[3][$m];
                    $zhiliaos[$j][$count_m]['停药原因'] = $alls[4][$m];
                    $zhiliaos[$j][$count_m]['备注'] = $alls[5][$m];

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1014.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($zhiliaos));
        fclose($myfile);

        echo "============".count($zhiliaos);

        $unitofwork->commitAndInit();
    }
}
$zhiliao = new Zhiliao();
$zhiliao->dowork();
