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

class xiyizhenduan
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

                <tr>\s*<td>诊断：<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>备注：<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<\/tr>

                <tr>\s*<td>诊断：<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>备注：<\/td>\s*(.*?)\s*<\/tr>

                <table border="0" cellpadding="0" cellspacing="0" class="common_tbl">
                    <tr>
                        <caption><span>西医诊断</span></caption>
                        <colgroup>
                            <col class="width_desc" />
                            <col class="" />
                            <col class="width_desc" />
                            <col class="width" />
                        </colgroup>
                    </tr>
                    <tr>
                        <td>诊断：</td>
                        <td>
                            其他(SLE+脊髓炎)
                        </td>
                        <td>备注：</td>
                        <td>
                        </td>
                    </tr>
                </table>

                <tr>
                <td>诊断：</td>
                <td>
                其他(NMO+PSS)
                </td>
                <td>备注：</td>
                <td>
                眼科会诊：Schimer: 0，0。BUT：2，2。FL：阳性，阳性。
                口腔科会诊：唾液流率：0。腮腺造影：主导管扩张，分支导管减少，末梢破坏，排空不完全。
                唇线活检：970168，散在及灶性LC浸润。	</td>
                </tr>

                <tr>\s*<td>诊断：<\/td>\s*(.*?)\s*<\/tr>

                */
                preg_match_all('/<td>诊断：<\/td>\s*<td>\s*(.*?)\s*<\/td>\s*<td>备注：<\/td>\s*<td>\s*(.*?)<\/td>\s*<\/tr>/is', $htmlarr[$i], $alls);

                for ($m = 0 ; $m < count($alls[1]) ; $m++) {
                    $naojiyejianchas[$j][$count_m]['姓名'] = $names[1][0];
                    $naojiyejianchas[$j][$count_m]['病历号'] = $out_case_nos[1][0];
                    $naojiyejianchas[$j][$count_m]['录入日期'] = $jilushijians[1][0];

                    $naojiyejianchas[$j][$count_m]['诊断'] = $alls[1][$m];
                    $naojiyejianchas[$j][$count_m]['备注'] = $alls[2][$m];

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1012.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($naojiyejianchas));
        fclose($myfile);

        echo "============".count($naojiyejianchas);

        $unitofwork->commitAndInit();
    }
}

$xiyizhenduan = new xiyizhenduan();
$xiyizhenduan->dowork();
