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

class jiazhuangxiangongneng
{
    public function getFix($str)
    {
        $arr = explode('<span', $str);

        return $arr[0];
    }

    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $jiazhuangxiangongnengs = array();
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

                preg_match_all('/<tr>\s*<caption><span>甲状腺功能<\/span><\/caption>\s*<colgroup>\s*<col\sclass=\"width_laboratory\"\/>\s*<col\sclass=\"width_desc\"\/>\s*<col\/>\s*<\/colgroup>\s*<\/tr>\s*<tr>\s*<td>检查医院<\/td>\s*<td\scolspan=\"3\">(.*?)<\/td>\s*<\/tr>\s*<tr>\s*<td>化验日期<\/td>\s*<td\scolspan=\"3\">(.*?)<\/td>/is', $htmlarr[$i], $Hospital_huayandates);

                preg_match_all('/<td>游离三碘甲状腺原氨酸<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $jzx1s);
                preg_match_all('/<td>游离甲状腺素<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $jzx2s);
                preg_match_all('/<td>三碘甲状腺原氨酸<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $jzx3s);
                preg_match_all('/<td>甲状腺素<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $jzx4s);
                preg_match_all('/<td>促甲状腺激素<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $jzx5s);
                preg_match_all('/<td>甲状腺球蛋白抗体<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $jzx6s);
                preg_match_all('/<td>甲状腺过氧化物酶抗体<\/td>\s*<td>\s*(.*?)\s*<\/td>/is', $htmlarr[$i], $jzx7s);

                for ($m = 0 ; $m < count($jzx1s[1]) ; $m++) {
                    $jiazhuangxiangongnengs[$j][$count_m]['姓名'] = $names[1][0];
                    $jiazhuangxiangongnengs[$j][$count_m]['病历号'] = $out_case_nos[1][0];
                    $jiazhuangxiangongnengs[$j][$count_m]['检查医院'] = $Hospital_huayandates[1][$m];
                    $jiazhuangxiangongnengs[$j][$count_m]['化验日期'] = $Hospital_huayandates[2][$m];
                    $jiazhuangxiangongnengs[$j][$count_m]['录入日期'] = $jilushijians[1][0];

                    $jiazhuangxiangongnengs[$j][$count_m]['游离三碘甲状腺原氨酸'] = self::getFix($jzx1s[1][$m]);
                    $jiazhuangxiangongnengs[$j][$count_m]['游离甲状腺素'] = self::getFix($jzx2s[1][$m]);
                    $jiazhuangxiangongnengs[$j][$count_m]['三碘甲状腺原氨酸'] = self::getFix($jzx3s[1][$m]);
                    $jiazhuangxiangongnengs[$j][$count_m]['甲状腺素'] = self::getFix($jzx4s[1][$m]);
                    $jiazhuangxiangongnengs[$j][$count_m]['促甲状腺激素'] = self::getFix($jzx5s[1][$m]);
                    $jiazhuangxiangongnengs[$j][$count_m]['甲状腺球蛋白抗体'] = self::getFix($jzx6s[1][$m]);
                    $jiazhuangxiangongnengs[$j][$count_m]['甲状腺过氧化物酶抗体'] = self::getFix($jzx7s[1][$m]);

                    $count_m++;
                }
            }
        }

        $myfile = fopen("/tmp/checkups/1007.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($jiazhuangxiangongnengs));
        fclose($myfile);

        echo "============".count($jiazhuangxiangongnengs);

        $unitofwork->commitAndInit();
    }
}

$jiazhuangxiangongneng = new jiazhuangxiangongneng();
$jiazhuangxiangongneng->dowork();
