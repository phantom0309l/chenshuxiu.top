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

class Spider_Article
{

    public function dowork () {

        $menuurl = "http://jk.centv.cn/yiyaobaojian/";
        $articleurl4preg = "http://jk.centv.cn/2016/yiyaobaojian_";
        $picurlbefore = "http://jk.centv.cn";
        $picurlsrc = "/ueditor/php/upload/image/20160728/1469692506465206.jpg";

        $pagecnt = 10;
        $onepageurlcnt = 10;
        $contentArr = array();

        $cntnum = 1;
        for( $i = 1; $i <= $pagecnt; $i++ ){
            if( $i == 1 ){
                $page = file_get_contents($menuurl.'index.html');
            }else{
                $page = file_get_contents($menuurl.$i.'.html');
            }

            preg_match_all("/http:\/\/jk.centv.cn\/2016\/yiyaobaojian_(.*)\.html/",$page,$urls);

            $urls_new = array_unique($urls[0]);
            $urls_last = array();
            foreach( $urls_new as $url ){
                $urls_last[] = $url;
            }

            preg_match_all("/<div\sclass=\"cont\">(.*?)<a/is",$page,$briefs);

            for( $j = 0; $j < $onepageurlcnt; $j++ ) {
                $onearticle = file_get_contents($urls_last[$j]);

                preg_match("/<h1 class=\"content-tit\">(.*?)<\/h1>/is",$onearticle,$titles);
                preg_match("/\/ueditor\/php\/upload\/image\/(.*?)\.(jpg|png)/is",$onearticle,$picuris);
                preg_match("/\/ueditor\/php\/upload\/image\/(.*?)<\/p><p><br\/><\/p><p>/is",$onearticle,$contents_pre);
                preg_match("/<\/p><p>(.*?)<\/p><p><br\/><\/p><p>/is",$contents_pre[0],$contents);

                $content = preg_replace("/(<br(\s*)\/>|<\/p>)/is","\n",$contents[1]);

                $content = strip_tags($content);
                $contentArr[] = array(
                    'title'=>$titles[1],
                    'pic'=>$picurlbefore.$picuris[0],
                    'brief'=>$briefs[1][$j],
                    'content'=>$content
                );

                echo "\n[{$cntnum}/100] {$urls_last[$j]}";
                $cntnum++;
            }
        }

        echo "\n [Spider_Article] begin ";
        $courses = CourseDao::getListByGroupstr('show_in_index');
        $course = $courses[0];
        $cntnum = 1;
        $errorcnt = 1;

        foreach( $contentArr as $arr ){
            $unitofwork = BeanFinder::get("UnitOfWork");

            $picture = Picture::createByFetch($arr['pic']);
            if ( false == $picture instanceof Picture ){
                echo "\n[{$cntnum}/100] 储存失败 [{$errorcnt}]";
                $cntnum++;
                $errorcnt++;
                continue;
            }

            if( $arr['title'] == null || $arr['brief'] == null || $arr['content'] == null ){
                echo "\n[{$cntnum}/100] 储存失败 [{$errorcnt}] ";
                $cntnum++;
                $errorcnt++;
                continue;
            }

            $row = array();
            $row['title'] = $arr['title'];
            $row['brief'] = $arr['brief'];
            $row['content'] = $arr['content'];
            $row['pictureid'] = $picture->id;

            $lesson = Lesson::createByBiz($row);

            $row = array();
            $row['courseid'] = $course->id;
            $row['lessonid'] = $lesson->id;

            $courselessonref = CourseLessonRef::createByBiz($row);
            echo "\n[{$cntnum}/100] 写入完毕";
            $cntnum++;
            $unitofwork->commitAndInit();
        }

        echo "\n [Spider_Article] finished \n";

    }
}

$process = new Spider_Article();
$process->dowork();

