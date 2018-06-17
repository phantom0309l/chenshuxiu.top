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

class yingxiangjiancha
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

                /*
                <tr>\s*<caption><span>脑脊液检查<\/span><\/caption>\s*<colgroup>\s*<col\sclass=\"width_code\"\/>\s*<col\sclass=\"width_idcard\"\/>\s*<col\sclass=\"width_code\"\/>\s*<col\sclass=\"width_idcard\"\/>\s*<\/colgroup>\s*<\/tr>\s*<tr>\s*<td>检查时间：<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>检查医院：<\/td>\s*<td>(.*?)<\/td>\s*<\/tr>
                */
                preg_match_all('/<tr id=\'showcheckIMG_\'>\s*<td>([0-9].*?)<\/td>\s*<td>(.*?)<\/td>\s*<td>(.*?)<\/td>\s*<td>\s*<span>(.*?)<\/span>\s*<\/td>/is', $htmlarr[$i], $alls);

                for ($m = 0 ; $m < count($alls[1]) ; $m++) {
                    $naojiyejianchas[$j][$count_m]['姓名'] = $names[1][0];
                    $naojiyejianchas[$j][$count_m]['病历号'] = $out_case_nos[1][0];
                    $naojiyejianchas[$j][$count_m]['化验日期'] = $alls[1][$m];
                    $naojiyejianchas[$j][$count_m]['录入日期'] = $jilushijians[1][0];

                    $naojiyejianchas[$j][$count_m]['项目'] = $alls[2][$m];
                    $naojiyejianchas[$j][$count_m]['子项目'] = $alls[3][$m];
                    $naojiyejianchas[$j][$count_m]['备注'] = $alls[4][$m];

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1011.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($naojiyejianchas));
        fclose($myfile);

        echo "============".count($naojiyejianchas);

        $unitofwork->commitAndInit();
    }
}

$yingxiangjiancha = new yingxiangjiancha();
$yingxiangjiancha->dowork();
