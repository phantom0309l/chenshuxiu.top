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

class ENA
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

                preg_match_all('/<tr>\s*<caption><span>抗可溶性核抗原（ENA）抗体<\/span><\/caption>\s*<colgroup>\s*<col\sclass=\"width_laboratory\"\/>\s*<col\sclass=\"width_desc\"\/>\s*<col\/>\s*<\/colgroup>\s*<\/tr>\s*<tr>\s*<td>检查医院<\/td>\s*<td\scolspan=\"3\">(.*?)<\/td>\s*<\/tr>\s*<tr>\s*<td>化验日期<\/td>\s*<td\scolspan=\"3\">(.*?)<\/td>/is', $htmlarr[$i], $Hospital_huayandates);

                preg_match_all('/<td>（双扩散法）抗Sm抗体（Sm）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $sms);
                if ($sms[1][0]) {
                    if ($sms[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $sms[1][0], $sm3s);
                        $sms = $sm3s;
                    }
                }

                preg_match_all('/<td>（双扩散法）抗RNP抗体（RNP）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $rnps);
                if ($rnps[1][0]) {
                    if ($rnps[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $rnps[1][0], $rnp3s);
                        $rnps = $rnp3s;
                    }
                }

                preg_match_all('/（SSA）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $ssas);
                if ($ssas[1][0]) {
                    if ($ssas[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $ssas[1][0], $ssa3s);
                        $ssas = $ssa3s;
                    }
                }

                preg_match_all('/（SSB）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $ssbs);
                if ($ssbs[1][0]) {
                    if ($ssbs[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $ssbs[1][0], $ssb3s);
                        $ssbs = $ssb3s;
                    }
                }

                preg_match_all('/（Sm_）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $sm_s);
                if ($sm_s[1][0]) {
                    if ($sm_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $sm_s[1][0], $sm_3s);
                        $sm_s = $sm_3s;
                    }
                }

                preg_match_all('/（RNP_）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $rnp_s);
                if ($rnp_s[1][0]) {
                    if ($rnp_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $rnp_s[1][0], $rnp_3s);
                        $rnp_s = $rnp_3s;
                    }
                }

                preg_match_all('/（SAA_）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $ssa_s);
                if ($ssa_s[1][0]) {
                    if ($ssa_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $ssa_s[1][0], $ssa_3s);
                        $ssa_s = $ssa_3s;
                    }
                }

                preg_match_all('/（SSB_）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $ssb_s);
                if ($ssb_s[1][0]) {
                    if ($ssb_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $ssb_s[1][0], $ssb_3s);
                        $ssb_s = $ssb_3s;
                    }
                }

                preg_match_all('/（\sScl_70_）<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $scl_70_s);
                if ($scl_70_s[1][0]) {
                    if ($scl_70_s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $scl_70_s[1][0], $scl_70_3s);
                        $scl_70_s = $scl_70_3s;
                    }
                }

                preg_match_all('/（\sJo-l\s\)<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $jo_ls);
                if ($jo_ls[1][0]) {
                    if ($jo_ls[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $jo_ls[1][0], $jo_l3s);
                        $jo_ls = $jo_l3s;
                    }
                }

                preg_match_all('/\(\srRNP\s\)<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $rrnps);
                if ($rrnps[1][0]) {
                    if ($rrnps[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $rrnps[1][0], $rrnp3s);
                        $rrnps = $rrnp3s;
                    }
                }

                for ($m = 0 ; $m < count($sms[1]) ; $m++) {
                    echo $names[1][0].'-----------------'.$out_case_nos[1][0]."\n";

                    $kanghekangtipus[$j][$count_m]['姓名'] = $names[1][0];
                    $kanghekangtipus[$j][$count_m]['病历号'] = $out_case_nos[1][0];
                    $kanghekangtipus[$j][$count_m]['检查医院'] = $Hospital_huayandates[1][$m];
                    $kanghekangtipus[$j][$count_m]['化验日期'] = $Hospital_huayandates[2][$m];
                    $kanghekangtipus[$j][$count_m]['录入日期'] = $jilushijians[1][0];

                    $kanghekangtipus[$j][$count_m]['（双扩散法）抗Sm抗体（Sm）'] = $sms[1][$m];
                    $kanghekangtipus[$j][$count_m]['（双扩散法）抗RNP抗体（RNP）'] = $rnps[1][$m];
                    $kanghekangtipus[$j][$count_m]['( 双扩散法 ) 抗SSA抗体（SSA）'] = $ssas[1][$m];
                    $kanghekangtipus[$j][$count_m]['( 双扩散法 ) 抗SSB抗体（SSB）'] = $ssbs[1][$m];
                    $kanghekangtipus[$j][$count_m]['(印记法)Sm （Sm_）'] = $sm_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['(印记法)抗RNP抗体（RNP_）'] = $rnp_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['(印记法)抗SSA （SAA_）'] = $ssa_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['(印记法)抗SSB （SSB_）'] = $ssb_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['(印记法)抗Scl_70抗体 （ Scl_70_）'] = $scl_70_s[1][$m];
                    $kanghekangtipus[$j][$count_m]['(印记法)抗Jo-l抗体（ Jo-l )'] = $jo_ls[1][$m];
                    $kanghekangtipus[$j][$count_m]['(印记法)抗rRNP抗体 ( rRNP )'] = $rrnps[1][$m];

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1006.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($kanghekangtipus));
        fclose($myfile);

        echo "============".count($kanghekangtipus);

        $unitofwork->commitAndInit();
    }
}

$ena = new ENA();
$ena->dowork();
