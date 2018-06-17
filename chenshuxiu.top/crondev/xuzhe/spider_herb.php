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

class Spider_Herb
{

    public function dowork () {
        echo "\n [Spider_Herb] begin ";

        $url = "http://www.zhongyoo.com/name/page_";
        $dataarr = array();

        $cnt = 1;

        for( $i = 1; $i <= 41; $i++ ){
            $page = file_get_contents($url.$i.'.html');

            preg_match_all("/class=\"title\">(.*?)<\/a><\/strong>/is",$page,$words);
            $unitofwork = BeanFinder::get("UnitOfWork");

            foreach( $words[1] as $word ){
                $word = mb_convert_encoding($word,'utf-8','gbk');

                $herb = Dao::getEntityByCond('Herb'," and name = :name",array(":name"=>$word));
                if( false == $herb instanceof Herb){
                    break;
                }

                $row = array();
                $row['name'] = $word;
                Herb::createByBiz($row);

                echo "\n".$word;

            }
            $unitofwork->commitAndInit();

        }

        echo "\n [Spider_Herb] finished \n";

    }
}

$process = new Spider_Herb();
$process->dowork();
