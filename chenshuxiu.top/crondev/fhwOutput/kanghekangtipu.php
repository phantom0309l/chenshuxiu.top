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

class kanghekangtipu
{
    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $kanghekangtipus = array();
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

                preg_match_all('/<tr>\s*<caption><span>抗核抗体谱<\/span><\/caption>\s*<colgroup>\s*<col\sclass=\"width_laboratory\"\/>\s*<col\/>\s*<\/colgroup>\s*<\/tr>\s*<tr>\s*<td>检查医院<\/td>\s*<td\scolspan=\"\">(.*?)<\/td>\s*<\/tr>\s*<tr>\s*<td>化验日期<\/td>\s*<td\scolspan=\"\">(.*?)<\/td>/is', $htmlarr[$i], $Hospital_huayandates);

                preg_match_all('/<td>抗核抗体（IgG型）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_1_s);
                if ($khkt_1_s[1][0]) {
                    if ($khkt_1_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_1_s[1][0], $khkt_1_3s);
                        $khkt_1_s = $khkt_1_3s;
                    }
                }

                preg_match_all('/<td>抗双链DNA抗体（IgG型）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_2_s);
                if ($khkt_2_s[1][0]) {
                    if ($khkt_2_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_2_s[1][0], $khkt_2_3s);
                        $khkt_2_s = $khkt_2_3s;
                    }
                }

                preg_match_all('/<td>抗双链DNA抗体（IgG型）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_3_s);
                if ($khkt_3_s[1][0]) {
                    if ($khkt_3_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_3_s[1][0], $khkt_3_3s);
                        $khkt_3_s = $khkt_3_3s;
                    }
                }

                preg_match_all('/<td>抗细胞浆抗体<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_4_s);
                if ($khkt_4_s[1][0]) {
                    if ($khkt_4_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_4_s[1][0], $khkt_4_3s);
                        $khkt_4_s = $khkt_4_3s;
                    }
                }

                preg_match_all('/<td>抗中心粒抗体<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_5_s);
                if ($khkt_5_s[1][0]) {
                    if ($khkt_5_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_5_s[1][0], $khkt_5_3s);
                        $khkt_5_s = $khkt_5_3s;
                    }
                }

                preg_match_all('/<td>抗Sm抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_6_s);
                if ($khkt_6_s[1][0]) {
                    if ($khkt_6_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_6_s[1][0], $khkt_6_3s);
                        $khkt_6_s = $khkt_6_3s;
                    }
                }

                preg_match_all('/<td>抗RNP抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_7_s);
                if ($khkt_7_s[1][0]) {
                    if ($khkt_7_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_7_s[1][0], $khkt_7_3s);
                        $khkt_7_s = $khkt_7_3s;
                    }
                }

                preg_match_all('/<td>抗SSA抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_8_s);
                if ($khkt_8_s[1][0]) {
                    if ($khkt_8_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_8_s[1][0], $khkt_8_3s);
                        $khkt_8_s = $khkt_8_3s;
                    }
                }

                preg_match_all('/<td>抗SSB抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_9_s);
                if ($khkt_9_s[1][0]) {
                    if ($khkt_9_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_9_s[1][0], $khkt_9_3s);
                        $khkt_9_s = $khkt_9_3s;
                    }
                }

                preg_match_all('/<td>抗Sc1-70抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_10_s);
                if ($khkt_10_s[1][0]) {
                    if ($khkt_10_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_10_s[1][0], $khkt_10_3s);
                        $khkt_10_s = $khkt_10_3s;
                    }
                }

                preg_match_all('/<td>抗Jo-1抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_11_s);
                if ($khkt_11_s[1][0]) {
                    if ($khkt_11_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_11_s[1][0], $khkt_11_3s);
                        $khkt_11_s = $khkt_11_3s;
                    }
                }

                preg_match_all('/<td>抗核糖体抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_12_s);
                if ($khkt_12_s[1][0]) {
                    if ($khkt_12_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_12_s[1][0], $khkt_12_3s);
                        $khkt_12_s = $khkt_12_3s;
                    }
                }

                preg_match_all('/<td>增值性核抗原抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_13_s);
                if ($khkt_13_s[1][0]) {
                    if ($khkt_13_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_13_s[1][0], $khkt_13_3s);
                        $khkt_13_s = $khkt_13_3s;
                    }
                }

                preg_match_all('/<td>抗组蛋白抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_14_s);
                if ($khkt_14_s[1][0]) {
                    if ($khkt_14_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_14_s[1][0], $khkt_14_3s);
                        $khkt_14_s = $khkt_14_3s;
                    }
                }

                preg_match_all('/<td>抗Ro\s52抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_15_s);
                if ($khkt_15_s[1][0]) {
                    if ($khkt_15_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_15_s[1][0], $khkt_15_3s);
                        $khkt_15_s = $khkt_15_3s;
                    }
                }

                preg_match_all('/<td>抗PM-Scl抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_16_s);
                if ($khkt_16_s[1][0]) {
                    if ($khkt_16_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_16_s[1][0], $khkt_16_3s);
                        $khkt_16_s = $khkt_16_3s;
                    }
                }

                preg_match_all('/<td>抗核小体抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_17_s);
                if ($khkt_17_s[1][0]) {
                    if ($khkt_17_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_17_s[1][0], $khkt_17_3s);
                        $khkt_17_s = $khkt_17_3s;
                    }
                }

                preg_match_all('/<td>抗着丝点B抗体（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_18_s);
                if ($khkt_18_s[1][0]) {
                    if ($khkt_18_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_18_s[1][0], $khkt_18_3s);
                        $khkt_18_s = $khkt_18_3s;
                    }
                }

                preg_match_all('/<td>抗线粒体抗体M2亚型（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_19_s);
                if ($khkt_19_s[1][0]) {
                    if ($khkt_19_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_19_s[1][0], $khkt_19_3s);
                        $khkt_19_s = $khkt_19_3s;
                    }
                }

                preg_match_all('/<td>DNP乳胶凝集试验（LIA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $khkt_20_s);
                if ($khkt_20_s[1][0]) {
                    if ($khkt_20_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $khkt_20_s[1][0], $khkt_20_3s);
                        $khkt_20_s = $khkt_20_3s;
                    }
                }

                for ($m = 0 ; $m < count($khkt_16_s[1]) ; $m++) {
                    $kanghekangtipus[$j][$count_m]['姓名'] = $names[1][0];
                    $kanghekangtipus[$j][$count_m]['病历号'] = $out_case_nos[1][0];
                    $kanghekangtipus[$j][$count_m]['检查医院'] = $Hospital_huayandates[1][$m];
                    $kanghekangtipus[$j][$count_m]['化验日期'] = $Hospital_huayandates[2][$m];
                    $kanghekangtipus[$j][$count_m]['录入日期'] = $jilushijians[1][0];

                    $kanghekangtipus[$j][$count_m]['抗核抗体（IgG型）'] = $khkt_1_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗双链DNA抗体（IgG型）'] = $khkt_2_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗细胞浆抗体'] = $khkt_4_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗中心粒抗体'] = $khkt_5_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗Sm抗体（LIA）'] = $khkt_6_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗RNP抗体（LIA）'] = $khkt_7_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗SSA抗体（LIA）'] = $khkt_8_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗SSB抗体（LIA）'] = $khkt_9_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗Sc1-70抗体（LIA）'] = $khkt_10_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗Jo-1抗体（LIA）'] = $khkt_11_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗核糖体抗体（LIA）'] = $khkt_12_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['增值性核抗原抗体（LIA）'] = $khkt_13_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗组蛋白抗体（LIA）'] = $khkt_14_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗Ro 52抗体（LIA）'] = $khkt_15_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗PM-Scl抗体（LIA）'] = $khkt_16_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗核小体抗体（LIA）'] = $khkt_17_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗着丝点B抗体（LIA）'] = $khkt_18_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['抗线粒体抗体M2亚型（LIA）'] = $khkt_19_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['DNP乳胶凝集试验（LIA）'] = $khkt_20_s[1][$m];

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1008.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($kanghekangtipus));
        fclose($myfile);

        echo "============".count($kanghekangtipus);

        $unitofwork->commitAndInit();
    }
}

$kanghekangtipu = new kanghekangtipu();
$kanghekangtipu->dowork();
