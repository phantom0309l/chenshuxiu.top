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

class naojiyejiancha
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
                preg_match_all('/<tr>\s*<caption><span>脑脊液检查<\/span><\/caption>\s*<colgroup>\s*<col\sclass=\"width_code\"\/>\s*<col\sclass=\"width_idcard\"\/>\s*<col\sclass=\"width_code\"\/>\s*<col\sclass=\"width_idcard\"\/>\s*<\/colgroup>\s*<\/tr>\s*<tr>\s*<td>检查时间：<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>检查医院：<\/td>\s*<td>(.*?)<\/td>\s*<\/tr>/is', $htmlarr[$i], $Hospital_huayandates);

                //颜色
                preg_match_all('/<td>颜色：<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $njy1s);

                //压力
                preg_match_all('/<td>压力：<\/td>\s*<td>\s*(.*?)&nbsp;/is', $htmlarr[$i], $njy2s);
                //preg_match_all('/<td>压力：<\/td>\s*<td>\s*(.*?)/', $njy2s[0][0], $njy2s);

                //WBC细胞计数
                preg_match_all('/<td>WBC细胞计数：<\/td>\s*<td>\s*(.*?)&nbsp;/is', $htmlarr[$i], $njy3s);

                //细胞总数
                preg_match_all('/<td>细胞总数：<\/td>\s*<td>\s*(.*?)&nbsp;/is', $htmlarr[$i], $njy4s);

                //葡萄糖定量
                preg_match_all('/<td>葡萄糖定量：<\/td>\s*<td>\s*(.*?)&nbsp;/is', $htmlarr[$i], $njy5s);

                //氯化物定量
                preg_match_all('/<td>氯化物定量：<\/td>\s*<td>\s*(.*?)&nbsp;/is', $htmlarr[$i], $njy6s);

                //<td>蛋白定量：</td>
                preg_match_all('/<td>蛋白定量：<\/td>\s*<td\scolspan=\'3\'>\s*(.*?)&nbsp;/is', $htmlarr[$i], $njy7s);

                //(寡克隆带）
                preg_match_all('/<td>OCB\(寡克隆带）：<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $njy8s);

                preg_match_all('/<td>AQP4IgG：<\/td>\s*<td>\s*<label>\s*(.*?)\s*<\/label>/is', $htmlarr[$i], $njy9s);
                if ($njy9s[1][0]) {
                    if ($njy9s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $njy9s[1][0], $njy93s);
                        $njy9s = $njy93s;
                    }
                }

                preg_match_all('/<td>NMO-IgG:<\/td>\s*<td>\s*<label>\s*(.*?)\s*<\/label>/is', $htmlarr[$i], $njy10s);
                if ($njy10s[1][0]) {
                    if ($njy10s[1][0] == "<font color='red'>阳性</font>") {
                        preg_match_all('/>(.*?)</', $njy10s[1][0], $njy103s);
                        $njy10s = $njy103s;
                    }
                }

                //IgG合成率
                preg_match_all('/<td>IgG合成率：<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $njy11s);

                for ($m = 0 ; $m < count($njy1s[1]) ; $m++) {
                    $naojiyejianchas[$j][$count_m]['姓名'] = $names[1][0];
                    $naojiyejianchas[$j][$count_m]['病历号'] = $out_case_nos[1][0];
                    $naojiyejianchas[$j][$count_m]['检查医院'] = $Hospital_huayandates[2][$m];
                    $naojiyejianchas[$j][$count_m]['化验日期'] = $Hospital_huayandates[1][$m];
                    $naojiyejianchas[$j][$count_m]['录入日期'] = $jilushijians[1][0];

                    $naojiyejianchas[$j][$count_m]['颜色'] = $njy1s[1][$m];
                    $naojiyejianchas[$j][$count_m]['压力'] = $njy2s[1][$m];
                    $naojiyejianchas[$j][$count_m]['WBC细胞计数'] = $njy3s[1][$m];
                    $naojiyejianchas[$j][$count_m]['细胞总数'] = $njy4s[1][$m];
                    $naojiyejianchas[$j][$count_m]['葡萄糖定量'] = $njy5s[1][$m];
                    $naojiyejianchas[$j][$count_m]['氯化物定量'] = $njy6s[1][$m];
                    $naojiyejianchas[$j][$count_m]['蛋白定量'] = $njy7s[1][$m];
                    $naojiyejianchas[$j][$count_m]['OCB(寡克隆带）'] = $njy8s[1][$m];
                    $naojiyejianchas[$j][$count_m]['AQP4IgG'] = $njy9s[1][$m];
                    $naojiyejianchas[$j][$count_m]['NMO-IgG'] = $njy10s[1][$m];
                    $naojiyejianchas[$j][$count_m]['IgG合成率'] = $njy11s[1][$m];

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1010.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($naojiyejianchas));
        fclose($myfile);

        echo "============".count($naojiyejianchas);

        $unitofwork->commitAndInit();
    }
}

$naojiyejiancha = new naojiyejiancha();
$naojiyejiancha->dowork();
