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

class gangongneng
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

                preg_match_all('/<tr>\s*<caption><span>肝功能\s<\/span><\/caption>\s*<colgroup>\s*<col\sclass=\"width_laboratory\"\/>\s*<col\sclass=\"width_desc\"\/>\s*<col\/>\s*<\/colgroup>\s*<\/tr>\s*<tr>\s*<td>检查医院<\/td>\s*<td\scolspan=\"3\">(.*?)<\/td>\s*<\/tr>\s*<tr>\s*<td>化验日期<\/td>\s*<td\scolspan=\"3\">(.*?)<\/td>/is', $htmlarr[$i], $Hospital_huayandates);

                preg_match_all('/<td>丙氨酸氨基转移酶（ALT）<\/td>\s*<td>\s*(.*?)</', $htmlarr[$i], $alt1s);
                if ($alt1s[1][0]) {
                    $alts = $alt1s;
                } else {
                    preg_match_all('/<td>丙氨酸氨基转移酶（ALT）<\/td>\s*<td>\s*(.*?)\s*<\/td>/', $htmlarr[$i], $alt2s);
                    $alts = $alt2s;
                }

                preg_match_all('/<td>天冬氨酸氨基转移酶（AST）<\/td>\s*<td>\s*(.*?)</', $htmlarr[$i], $ast1s);
                if ($ast1s[1][0]) {
                    $asts = $ast1s;
                } else {
                    preg_match_all('/<td>天冬氨酸氨基转移酶（AST）<\/td>\s*<td>\s*(.*?)\s*<\/td>/', $htmlarr[$i], $ast2s);
                    $asts = $ast2s;
                }

                preg_match_all('/<td>γ-谷氨酰转肽酶（GGT）<\/td>\s*<td>\s*(.*?)</', $htmlarr[$i], $ggt1s);
                if ($ggt1s[1][0]) {
                    $ggts = $ggt1s;
                } else {
                    preg_match_all('/<td>γ-谷氨酰转肽酶（GGT）<\/td>\s*<td>\s*(.*?)\s*<\/td>/', $htmlarr[$i], $ggt2s);
                    $ggts = $ggt2s;
                }

                for ($m = 0 ; $m < count($alts[1]) ; $m++) {
                    echo $names[1][0].'-----------------'.$out_case_nos[1][0]."\n";

                    $xuechangguis[$j][$count_m]['姓名'] = $names[1][0];
                    $xuechangguis[$j][$count_m]['病历号'] = $out_case_nos[1][0];
                    $xuechangguis[$j][$count_m]['检查医院'] = $Hospital_huayandates[1][$m];
                    $xuechangguis[$j][$count_m]['化验日期'] = $Hospital_huayandates[2][$m];
                    $xuechangguis[$j][$count_m]['录入日期'] = $jilushijians[1][0];

                    $xuechangguis[$j][$count_m]['丙氨酸氨基转移酶（ALT）'] = $alts[1][$m];
                    $xuechangguis[$j][$count_m]['天冬氨酸氨基转移酶（AST）'] = $asts[1][$m];
                    $xuechangguis[$j][$count_m]['γ-谷氨酰转肽酶（GGT）'] = $ggts[1][$m];

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1004.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($xuechangguis));
        fclose($myfile);

        echo "============".count($xuechangguis);

        $unitofwork->commitAndInit();
    }
}

$gangongneng = new gangongneng();
$gangongneng->dowork();
