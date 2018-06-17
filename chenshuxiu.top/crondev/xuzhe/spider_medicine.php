<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Spider_Medicine
{

    private function write2file ($filename, $dataarr) {
        $file = fopen("/home/xuzhe/medicine/{$filename}.txt", 'w+');
        $filecontent = '';
        foreach( $dataarr as $data ){
            $filecontent .= "{$data} \n\n\n";
        }
        fwrite($file, $filecontent);
        echo "\n[{$filename}] 储存完毕";
    }

    public function dowork () {
        echo "\n [Spider_Medicine] begin ";

        $urlArr = array(
            '男科用药'=>array(
                'pages' => 60,
                'menuurl' => 'http://m.jianke.com/list-0111-1-',
            ),
            '心脑血管'=>array(
                'pages' => 246,
                'menuurl' => 'http://m.jianke.com/list-0106-1-',
            ),
            '风湿跌打'=>array(
                'pages' => 211,
                'menuurl' => 'http://m.jianke.com/list-0104-1-',
            ),
            '皮肤用药'=>array(
                'pages' => 207,
                'menuurl' => 'http://m.jianke.com/list-0103-1-',
            ),
            '肝胆胰类'=>array(
                'pages' => 89,
                'menuurl' => 'http://m.jianke.com/list-0110-1-',
            ),
            '胃肠疾病'=>array(
                'pages' => 293,
                'menuurl' => 'http://m.jianke.com/list-0102-1-',
            ),
            '神经系统'=>array(
                'pages' => 85,
                'menuurl' => 'http://m.jianke.com/list-0105-1-',
            ),
            '呼吸系统'=>array(
                'pages' => 212,
                'menuurl' => 'http://m.jianke.com/list-0118-1-',
            ),
            '泌尿系统'=>array(
                'pages' => 31,
                'menuurl' => 'http://m.jianke.com/list-0112-1-',
            ),
            '妇科用药'=>array(
                'pages' => 229,
                'menuurl' => 'http://m.jianke.com/list-0108-1-',
            ),
            '儿科用药'=>array(
                'pages' => 106,
                'menuurl' => 'http://m.jianke.com/list-0109-1-',
            ),
            '解热镇痛'=>array(
                'pages' => 210,
                'menuurl' => 'http://m.jianke.com/list-0101-1-',
            )
        );

        $onepageurlcnt = 10;
        $contentArr = array();

        $cntnum = 1;
        foreach( $urlArr as $filename=>$arr){
            $pagecnt = $arr['pages'];
            $menuurl = $arr['menuurl'];

            $dataarr = array();

            for( $i = 1; $i <= $pagecnt; $i++ ){
                $page = file_get_contents($menuurl.$i.'.html');

                preg_match_all("/ <a href=\"\/product\/([0-9]*?).html\">/is",$page,$urls);
                $urls_num = $urls[1];
                for( $j = 0; $j < $onepageurlcnt; $j++ ) {
                    $onemedicine = file_get_contents("http://m.jianke.com/product/{$urls_num[$j]}.html");

                    preg_match("/<dt>药品名称(.*?)<p>生产企业：<span>(.*?)<\/span><\/p>/is",$onemedicine,$contents_pre);
//                    preg_match("/\/ueditor\/php\/upload\/image\/(.*?)\.(jpg|png)/is",$onearticle,$picuris);
//                    preg_match("/\/ueditor\/php\/upload\/image\/(.*?)<\/p><p><br\/><\/p><p>/is",$onearticle,$contents_pre);
//                    preg_match("/<\/p><p>(.*?)<\/p><p><br\/><\/p><p>/is",$contents_pre[0],$contents);
//
                    $content_pre2 = preg_replace("/(<br(\s*)\/>|<\/p>|<\/dt>|<\/dd>)/is","@@@",$contents_pre[0]);
                    $content_pre3 = preg_replace("/(&nbsp)/is"," ",$content_pre2);
                    $content_pre4 = preg_replace("/(\s+)/is"," ",$content_pre3);
                    $content = preg_replace("/@@@/is","\n",$content_pre4);
//
                    $content = strip_tags($content);
//                    $contentArr[] = array(
//                        'title'=>$titles[1],
//                        'brief'=>$briefs[1][$j],
//                        'content'=>$content
//                    );
//
//                    echo "\n[{$cntnum}/100] {$urls_last[$j]}";
//                    $cntnum++;

                    $dataarr[] = $content;
                }

                echo "\n[{$filename}][{$i}/{$pagecnt}] 抓取完成";
            }
            $this->write2file($filename,$dataarr);

        }

        echo "\n [Spider_Medicine] finished \n";

    }
}

$process = new Spider_Medicine();
$process->dowork();
