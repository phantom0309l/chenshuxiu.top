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

class EDSS
{
    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $naojiyejianchas = array();
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

                preg_match_all('/<th><span>评估时间<\/span><\/th>\s*<th>\s*<span>视觉<\/span>\s*<\/th>\s*<th>\s*<span>脑干<\/span>\s*<\/th>\s*<th>\s*<span>锥体束<\/span>\s*<\/th>\s*<th>\s*<span>小脑<\/span>\s*<\/th>\s*<th>\s*<span>感觉<\/span>\s*<\/th>\s*<th>\s*<span>大小便<\/span>\s*<\/th>\s*<th>\s*<span>大脑<\/span>\s*<\/th>\s*<th>\s*<span>行动<\/span>\s*<\/th>\s*<th>\s*<span>行走距离<\/span>\s*<\/th>\s*<th><span>得分<\/span><\/th>\s*<\/tr>\s*<tr>\s*<td>\s*([0-9].*?)\s*<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<\/tr>/is', $htmlarr[$i], $alls);

                for ($m = 0 ; $m < count($alls[1]) ; $m++) {
                    echo '-----------------'.$out_case_nos[1][0]."\n";

                    $naojiyejianchas[$j][$count_m]['姓名'] = $names[1][0];
                    $naojiyejianchas[$j][$count_m]['病历号'] = $out_case_nos[1][0];
                    $naojiyejianchas[$j][$count_m]['录入日期'] = $jilushijians[1][0];

                    $naojiyejianchas[$j][$count_m]['评估时间'] = $alls[1][$m];
                    $naojiyejianchas[$j][$count_m]['视觉'] = $alls[2][$m];
                    $naojiyejianchas[$j][$count_m]['脑干'] = $alls[3][$m];
                    $naojiyejianchas[$j][$count_m]['锥体束'] = $alls[4][$m];
                    $naojiyejianchas[$j][$count_m]['小脑'] = $alls[5][$m];
                    $naojiyejianchas[$j][$count_m]['感觉'] = $alls[6][$m];
                    $naojiyejianchas[$j][$count_m]['大小便'] = $alls[7][$m];
                    $naojiyejianchas[$j][$count_m]['大脑'] = $alls[8][$m];
                    $naojiyejianchas[$j][$count_m]['行动'] = $alls[9][$m];
                    $naojiyejianchas[$j][$count_m]['行走距离'] = $alls[10][$m];
                    $naojiyejianchas[$j][$count_m]['得分'] = $alls[11][$m];

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1002.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($naojiyejianchas));
        fclose($myfile);

        echo "============".count($naojiyejianchas);

        $unitofwork->commitAndInit();
    }
}

$EDSS = new EDSS();
$EDSS->dowork();
