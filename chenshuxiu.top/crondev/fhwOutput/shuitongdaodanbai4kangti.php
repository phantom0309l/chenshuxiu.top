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

class shuitongdaodanbai4kangti
{
    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $shuitongdaodanbai4kangtis = array();
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

                preg_match_all('/<tr>\s*<caption><span>水通道蛋白4抗体<\/span><\/caption>\s*<colgroup>\s*<col\sclass=\"width_laboratory\"\/>\s*<col\/>\s*<\/colgroup>\s*<\/tr>\s*<tr>\s*<td>检查医院<\/td>\s*<td\scolspan=\"\">(.*?)<\/td>\s*<\/tr>\s*<tr>\s*<td>化验日期<\/td>\s*<td\scolspan=\"\">(.*?)<\/td>/is', $htmlarr[$i], $Hospital_huayandates);

                preg_match_all('/<td>AQP4IgG<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $aqps);
                if ($aqps[1][0]) {
                    if ($aqps[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $aqps[1][0], $aqp3s);
                        $aqps = $aqp3s;
                    }
                }

                preg_match_all('/<td>NMO-IgG<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $nmos);
                if ($nmos[1][0]) {
                    if ($nmos[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $nmos[1][0], $nmo3s);
                        $nmos = $nmo3s;
                    }
                }

                for ($m = 0 ; $m < count($nmos[1]) ; $m++) {
                    $shuitongdaodanbai4kangtis[$j][$count_m]['姓名'] = $names[1][0];
                    $shuitongdaodanbai4kangtis[$j][$count_m]['病历号'] = $out_case_nos[1][0];
                    $shuitongdaodanbai4kangtis[$j][$count_m]['检查医院'] = $Hospital_huayandates[1][$m];
                    $shuitongdaodanbai4kangtis[$j][$count_m]['化验日期'] = $Hospital_huayandates[2][$m];
                    $shuitongdaodanbai4kangtis[$j][$count_m]['录入日期'] = $jilushijians[1][0];

                    $shuitongdaodanbai4kangtis[$j][$count_m]['AQP4IgG'] = $aqps[1][$m];
                    $shuitongdaodanbai4kangtis[$j][$count_m]['NMO-IgG'] = $nmos[1][$m];

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1009.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($shuitongdaodanbai4kangtis));
        fclose($myfile);

        echo "============".count($shuitongdaodanbai4kangtis);

        $unitofwork->commitAndInit();
    }
}

$shuitongdaodanbai4kangti = new shuitongdaodanbai4kangti();
$shuitongdaodanbai4kangti->dowork();
