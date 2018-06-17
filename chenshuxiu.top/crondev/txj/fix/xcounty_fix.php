<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// 修复pipes表中doctorid为０的数据
class Xcounty_fix
{

    public function dowork () {
        $cronbegintime = XDateTime::now();
        $unitofwork = BeanFinder::get("UnitOfWork");

        $data =array(
            //东莞市
            441900 => array(
                "中堂镇", "东坑镇", "道滘镇", "沙田镇", "高埗镇", "石龙镇", "石排镇", "企石镇", "石碣镇",
                "洪梅镇", "麻涌镇", "桥头镇", "望牛墩镇", "茶山镇", "谢岗镇", "松山湖", "莞城区", "南城区",
                "长安镇", "寮步镇", "大岭山镇", "常平镇", "厚街镇", "万江区", "樟木头镇", "大朗镇", "塘厦镇",
                "凤岗镇", "清溪镇", "横沥镇", "东城区", "黄江镇", "虎门镇",
            ),
            //中山市
            442000 => array(
                "南朗镇", "小榄镇", "古镇", "坦洲镇", "黄圃镇", "三乡镇", "东凤镇", "横栏镇",
                "三角镇", "南头镇", "沙溪镇", "五桂山镇", "阜沙镇", "东升镇", "板芙镇",
                "神湾镇", "港口镇", "大涌镇", "火炬开发区", "民众镇", "沙朗镇", "城区",
            ),
            //三沙市
            460300 => array(
                "西沙群岛", "南沙群岛", "中沙群岛",
            ),
            //儋州市
            460400 => array(
                "那大镇", "和庆镇", "南丰镇", "大成镇", "雅星镇", "兰洋镇", "光村镇",
                "木棠镇", "海头镇", "峨蔓镇", "三都镇", "王五镇", "白马井镇", "中和镇",
                "排浦镇", "东成镇", "新州镇", "洋浦经济开发区", "富克镇", "西培农场",
                "西联农场", "蓝洋农场", "八一农场", "西华农场", "西庆农场", "西流农场",
                "新盈农场", "龙山农场", "红岭农场", "热作学院",
            ),
            //嘉峪关市
            620200 => array(
                "雄关区", "长城区", "镜铁区",
            ),
        );

        foreach($data as $xcityid => $items){
            foreach($items as $i => $xcounty_name){
                $index = $i + 1;
                $xcountyid = $xcityid + $index;
                $xcounty = Xcounty::getById($xcountyid);
                if($xcounty instanceof Xcounty){
                    echo "\n====[xcounty is have][{$xcountyid}]==========\n";
                    continue;
                }
                $row = array();
                $row["id"] = $xcountyid;
                $row["xcityid"] = $xcityid;
                $row["name"] = $xcounty_name;
                Xcounty::createByBiz($row);
                echo "\n====[xcityid][{$xcityid}][{$xcounty_name}][created]==========\n";
            }
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Xcounty_fix.php]=====");

$process = new Xcounty_fix();
$process->dowork();

Debug::trace("=====[cron][end][Xcounty_fix.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
