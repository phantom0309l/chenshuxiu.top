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

class xuechanggui
{
    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $xuechangguis = array();
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

                preg_match_all('/<tr>\s*<caption><span>血常规<\/span><\/caption>\s*<colgroup>\s*<col\sclass=\"width_laboratory\"\/>\s*<col\sclass=\"width_desc\"\/>\s*<col\/>\s*<\/colgroup>\s*<\/tr>\s*<tr>\s*<td>检查医院<\/td>\s*<td\scolspan=\"3\">(.*?)<\/td>\s*<\/tr>\s*<tr>\s*<td>化验日期<\/td>\s*<td\scolspan=\"3\">(.*?)<\/td>/is', $htmlarr[$i], $Hospital_huayandates);

                preg_match_all('/<td>白细胞计数（WBC）<\/td>\s*<td>\s*(.*?)</is', $htmlarr[$i], $wbc1s);
                if ($wbc1s[1][0]) {
                    $wbcs = $wbc1s;
                } else {
                    preg_match_all('/<td>白细胞计数（WBC）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $wbc2s);
                    $wbcs = $wbc2s;
                }

                preg_match_all('/<td>红细胞计数（RBC）<\/td>\s*<td>\s*(.*?)</is', $htmlarr[$i], $rbc1s);
                if ($rbc1s[1][0]) {
                    $rbcs = $rbc1s;
                } else {
                    preg_match_all('/<td>红细胞计数（RBC）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $rbc2s);
                    $rbcs = $rbc2s;
                }

                preg_match_all('/<td>血红蛋白（HGB）<\/td>\s*<td>\s*(.*?)</is', $htmlarr[$i], $hgb1s);
                if ($hgb1s[1][0]) {
                    $hgbs = $hgb1s;
                } else {
                    preg_match_all('/<td>血红蛋白（HGB）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $hgb2s);
                    $hgbs = $hgb2s;
                }

                preg_match_all('/<td>血小板（PLT）<\/td>\s*<td>\s*(.*?)</is', $htmlarr[$i], $plt1s);
                if ($plt1s[1][0]) {
                    $plts = $plt1s;
                } else {
                    preg_match_all('/<td>血小板（PLT）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $plt2s);
                    $plts = $plt2s;
                }

                for ($m = 0 ; $m < count($wbcs[1]) ; $m++) {
                    $xuechangguis[$j][$count_m]['姓名'] = $names[1][0];
                    $xuechangguis[$j][$count_m]['病历号'] = $out_case_nos[1][0];
                    $xuechangguis[$j][$count_m]['检查医院'] = $Hospital_huayandates[1][$m];
                    $xuechangguis[$j][$count_m]['化验日期'] = $Hospital_huayandates[2][$m];
                    $xuechangguis[$j][$count_m]['录入日期'] = $jilushijians[1][0];
                    $xuechangguis[$j][$count_m]['白细胞计数（WBC）'] = $wbcs[1][$m];
                    $xuechangguis[$j][$count_m]['红细胞计数（RBC）'] = $rbcs[1][$m];
                    $xuechangguis[$j][$count_m]['血红蛋白（HGB）'] = $hgbs[1][$m];
                    $xuechangguis[$j][$count_m]['血小板（PLT）'] = $plts[1][$m];

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1003.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($xuechangguis));
        fclose($myfile);

        echo "============".count($xuechangguis);

        $unitofwork->commitAndInit();
    }
}

$xuechanggui = new xuechanggui();
$xuechanggui->dowork();
