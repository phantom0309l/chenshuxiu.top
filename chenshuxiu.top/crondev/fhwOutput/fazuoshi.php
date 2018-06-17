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

class fazuoshi
{
    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $fazuoshis = array();
        //H:/xuyan/xuyan_patientinfos
        $countHtml = count(glob("/tmp/htmls/*.html"));
        for ($j = 1 ; $j <= $countHtml ; $j++) {
            $contents = file_get_contents('/tmp/htmls/'.$j.'.html');

            preg_match_all('/<td>病历号：<\/td>\s*?<td>\s*?(.*?)\s*?<\/td>/is', $contents, $out_case_nos);
            preg_match_all('/<td>姓名：<\/td>\s*?<td>(.*?)<\/td>/is', $contents, $names);

            $htmlarr = explode('class="common_alert"', $contents);

            $count_m = 0;
            for ($i = 1 ; $i < count($htmlarr) ; $i++) {
                preg_match_all('/>\s*?([0-9].*?)\s*---/is', $htmlarr[$i], $jilushijians);
                preg_match_all('/<td>发作时间：<\/td>\s*\s+<td>\s*\s+(.*?)\s*<\/td>/is', $htmlarr[$i], $fazuoshijians);
                preg_match_all('/<td>发作症状：<\/td>\s*\s+<td>\s*\s+(.*?)\s*<\/td>/is', $htmlarr[$i], $fazuozhengzhuangs);
                preg_match_all('/<td>治疗方法：<\/td>\s*\s+<td>\s*\s+(.*?)\s*<\/td>/is', $htmlarr[$i], $zhiliaofangfas);
                preg_match_all('/<td>转归：<\/td>\s*\s+<td>\s*\s+(.*?)\s*<\/td>/is', $htmlarr[$i], $zhuanguis);
                preg_match_all('/<td>发作诱因：<\/td>\s*?<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $fazuoyouyins);
                preg_match_all('/<td>目前诊断：<\/td>\s*\s+<td>\s*\s+(.*?)\s*<\/td>/is', $htmlarr[$i], $muqianzhenduans);
                preg_match_all('/<td>备注：<\/td>\s*?<td\scolspan=\'5\'>\s*?\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $beizhus);

                for ($m = 0 ; $m < count($fazuoshijians[1]) ; $m++) {
                    echo $names[1][0].'-----------------'.$out_case_nos[1][0]."\n";

                    $fazuoshis[$j][$count_m]['病历号'] = trim($out_case_nos[1][0]);
                    $fazuoshis[$j][$count_m]['姓名'] = trim($names[1][0]);
                    $fazuoshis[$j][$count_m]['发作时间'] = trim($fazuoshijians[1][$m]);
                    $fazuoshis[$j][$count_m]['记录时间'] = trim($jilushijians[1][0]);

                    $fazuoshis[$j][$count_m]['发作症状'] = trim($fazuozhengzhuangs[1][$m]);
                    $fazuoshis[$j][$count_m]['治疗方法'] = trim($zhiliaofangfas[1][$m]);
                    $fazuoshis[$j][$count_m]['转归'] = trim($zhuanguis[1][$m]);
                    $fazuoshis[$j][$count_m]['发作诱因'] = trim($fazuoyouyins[1][$m]);
                    $fazuoshis[$j][$count_m]['目前诊断'] = trim($muqianzhenduans[1][$m]);
                    $fazuoshis[$j][$count_m]['备注'] = trim($beizhus[1][$m]);

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1001.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($fazuoshis));
        fclose($myfile);

        echo "============".count($fazuoshis);

        $unitofwork->commitAndInit();
    }
}

$fazuoshi = new fazuoshi();
$fazuoshi->dowork();
