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

// Debug::$debug = 'Dev';

class Medicinetagadd
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $arr = array(
            "儿童用药" => array(
                "午时葡萄糖酸钙锌口服溶液10ml*18支",
                "澳诺金辛金丐特葡萄糖酸钙锌口服溶液10ml*24支",
                "达因伊可新维生素AD滴剂",
                "妈咪爱枯草杆菌二联活菌颗粒1g*10袋",
                "迪巧小儿碳酸钙D3颗粒1g*10袋",
                "惠氏钙尔奇D碳酸钙D3咀嚼片(II)0.3g*30T",
                "北京同仁堂小儿感冒颗粒12g*10袋",
                "上海施贵宝小施尔康小儿维生素咀嚼片30T"
            ),
            "肠胃用药" => array(
                "韩国韩林达吉复方消化酶胶囊20S",
                "本草纲目开胃健脾丸60g",
                "维仙优复方维生素U片30T",
                "北京同仁堂地榆槐角丸9g*10S",
                "同仁堂白花油10ml",
                "同仁堂布洛芬缓释胶囊0.3g*20s",
                "同仁堂解毒凉血合剂10ml*60支",
                "邦廸苯扎氯铵贴(防水)2.25cm*1.27cm*4片*20袋"
            ),
            "清热降火" => array(
                "河南宛西仲景牌黄连上清丸6g*10包",
                "博抉清火片60片",
                "星群夏桑菊颗粒冲剂2袋40包",
                "云南白药云丰黄连上清片48片",
                "众生众生丸60丸",
                "同仁堂京制牛黄解毒片10瓶",
                "太福四黄泻火片36片",
                "今辰黄连上清片48片"
            )
        );

        foreach ($arr as $key => $medicines) {
            $row = array();
            $row["typestr"] = "medicineForWww";
            $row["name"] = $key;
            $tag = Tag::createByBiz( $row );
            foreach( $medicines as $v ){
                $medicine = MedicineDao::getByName( $v );
                if( $medicine instanceof Medicine ){
                    $row = array();
                    $row["objtype"] = "Medicine";
                    $row["objid"] = $medicine->id;
                    $row["tagid"] = $tag->id;
                    TagRef::createByBiz( $row );
                }
            }
        }

        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Medicinetagadd.php]=====");

$process = new Medicinetagadd();
$process->dowork();

Debug::trace("=====[cron][end][Medicinetagadd.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
