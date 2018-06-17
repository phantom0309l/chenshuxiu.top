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
读取老库中的所有分页页面
*/
class getpage
{
    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");
        for ($i = 1; $i <= 99 ; $i++) {
            $bash = 'curl "http://case.msnmobase.com/index.php?m=patients&c=index&a=main&page='.$i.'" -H "Accept-Encoding: gzip, deflate, sdch" -H "Accept-Language: zh,en;q=0.8,zh-CN;q=0.6" -H "Upgrade-Insecure-Requests: 1" -H "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36" -H "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8" -H "Referer: http://case.msnmobase.com/index.php?m=patients&c=index&a=main&page=87" -H "Cookie: PHPSESSID=d7730093d32f8ed33dc62b53c068652f; EKvIz_auth=476aAQNTCAkIUgcAVVIHBFMCWVUAAVsGAgQHDVcAAAMEeA1nZCJpYHAgdmYxJnNiNSIwL1phVAl9JHNtL2xoaSZ7MHRjAXldaSB2cS0xdFMjIRYna3VUVWInQmEheFV9KXsnRnghT39sI0AFNjFVVyM; EKvIz__userid=cdf0AFNUBwYDAVZTAwJdBwABAgtUVANUAwFXVQdT; EKvIz__username=a377AFIEVlQEUwMJUlNQWlNZBwNfAl0MDg0EXFFBQhhSDxRFD1cO; EKvIz__groupid=a78dVAlTU1YECAZVVlpUUFIBUlJTBFAHAARXU1YC; EKvIz__modelid=a24bB1ZRVAVSCQhSUVxSBgMHAgYJCgUGWlYAAQUDVA; EKvIz__nickname=a20cBlNTBQIJVQgGAQQCUAJTDAZQAF1dXANZVwNARRhQC0hDDgBd; Click to edit; sYQDUGqqzHusername=; _GPSLSC=XRYTVe3Fj0!H9yXxB0O4y!QG3A_qkJZB" -H "Connection: keep-alive" -H "Cache-Control: max-age=0" --compressed > /tmp/pages/'.$i.'.html';
            exec($bash, $output, $returnval);
        }

        $unitofwork->commitAndInit();
    }
}

$getpage = new getpage();
$getpage->dowork();
