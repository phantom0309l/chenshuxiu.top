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

/*
读取所有患者页面
*/
class getpatienthtmls
{
    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $patientinfourl = array();
        $countPage = count(glob("/tmp/pages/*.html"));
        for ($i = 1; $i <= $countPage; $i++) {
            $dom = new DOMDocument();
            $dom->loadHTMLFile('/tmp/pages/'.$i.'.html');
            $xml = simplexml_import_dom($dom);

            for ($j = 3 ; $j <= 12 ; $j++) {
                $links = $xml->xpath('//*[@id="colortbl"]/tr['.$j.']/td[10]/a');
                $hrefarr = $links[0]['href'];
                $linkarr = (array)$hrefarr;
                $patientinfourl[] = $linkarr[0];
            }
        }

        foreach ($patientinfourl as $key => $value) {
            $bash = 'curl "'.$value.'" -H "Accept-Encoding: gzip, deflate, sdch" -H "Accept-Language: zh,en;q=0.8,zh-CN;q=0.6" -H "Upgrade-Insecure-Requests: 1" -H "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36" -H "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8" -H "Referer: http://case.msnmobase.com/index.php?m=patients&c=index&a=main&page=1" -H "Cookie: Click to edit; PHPSESSID=c0f88d4fc5cc50d73cafd60b10a91b7d; sYQDUGqqzHusername=xuyanpumch; EKvIz_auth=f2d5CVFVAAkBBFYBAQYAUQtcVQACUFFWUVUCXFlSWl9RJzBSKDNXe2Z2EWRzZAp2aycNI34mVAwhIQgkKDEELDYyMAAzKmFgdXUBZHx2J1hhJDcgZiZUNiEwEyAmMTIgBjE3dCQkV3NgdlcBcmogbm8; EKvIz__userid=cff2AVFRBggEAgJTVQJZVwADBl1VWlQHBlMFAFEH; EKvIz__username=6707B1JWAlQDCQgHVQJUAVRRBAZSU1cJWgJSA1ccEBoEWUNCDwVa; EKvIz__groupid=ac51UQABAFQJUVNTVFcCAVcFA1cGAwMDBgUBAgcK; EKvIz__modelid=2200CAgIUQcCU1NVU1cEAwoJBFAIUgZWDgBVDgUFBg; EKvIz__nickname=7a47VAABA1QGCAdUBABRVQVSDVdRDgMKAA1UXAJLEE1ZW0ERXVJb; _GPSLSC=c9TDo8cjCb!XRYTVe3Fj0!H9yXxB0O4y!QG3A_qkJZB" -H "Connection: keep-alive" --compressed > /tmp/htmls/'.($key+1).'.html';
            exec($bash, $output, $returnval);
        }
        echo "\n-----------------".count($patientinfourl)."-----------------\n";

        $unitofwork->commitAndInit();
    }
}

$getpatienthtmls = new Getpatienthtmls();
$getpatienthtmls->dowork();
