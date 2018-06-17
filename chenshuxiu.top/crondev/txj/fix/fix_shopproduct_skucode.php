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

// Debug::$debug = 'Dev';

class Fix_shopproduct_skucode
{
    public function dowork () {

        $arr = array(
            "静灵口服液(安生),10ml*24支" => 6937562106182,
"静灵口服液(安生),10ml*10支" => 6937562100302,
"地牡宁神口服液(童奥特),10ml*10支" => 6915368006998,
"地牡宁神口服液（童奥特）,10ml*6支" => 6915368008022,
"可乐定透皮贴片(瑞福莱),1mg*1贴" => 6956768000018,
"可乐定透皮贴片(瑞福莱),1.5mg*1贴" => 6956768000056,
"小儿智力糖浆(葵花牌),10ml*10支" => 6943116400248,
"小儿智力糖浆(上龙牌),10ml*10支" => 6947605658011,
"小儿智力糖浆(新汇),10ml*10支" => 6931691600092,
"小儿黄龙颗粒(专注宁),6袋装" => 6922173703578,
"阿立哌唑片(安律凡),5mg*10片" => 6938711900606,
"多动宁胶囊,每粒装0.38g" => 6925437800129,
"奥拉西坦胶囊(欧来宁),0.4g*24s" => 6916119040674,
"赖氨肌醇维B12口服溶液(迪儿慧聪) 120ml" => 6921186678224,
"赖氨肌醇维B12口服溶液(信福爱) 90ml" => 6937388350059,
"菖麻熄风片(济仁宝宝),0.53g*24片" => 6921400003627,
"脑蛋白水解物口服液(万通),10ml*10支" => 6932067401312,
"碳酸钙D3颗粒(朗迪),10袋装" => 6937667109187,
"利培酮片(维思通),1mg" => 6922154600230,
"盐酸舍曲林片(左洛复),50mg" => 6958703500072,
"盐酸文拉法辛缓释胶囊(怡诺思),75mg/粒" => 6921361209052,
"盐酸文拉法辛缓释片(博乐欣),75mg*14片/盒" => 6926756410365,
"盐酸硫必利片(天新),0.1g" => 6941155710724,
"肌苷片,100片/瓶,0.2g" => 6930829910652,
"《儿童注意力训练父母手册》" => 9787561433485,
"《儿童注意力训练手册》" => 9787561432792,
"《培养孩子注意力的36种方法》" => 9787210071952,
"《教子书坊·培养孩子专注力的66个细节》" => 9787121186547,
"《5分钟玩出专注力》（套装共4册）" => 9787510135217,
"《125游戏提升孩子专注力》（套装全3册）" => 9787543698260,
"汉臣氏 益生菌粉婴幼儿童肠胃四联菌2.5g*36包" => 6946044309492,
"台湾金普洛儿童维生素B群 " => 4718000680233,
"澳洲swisse儿童青少年复合维生素矿物质咀嚼片" => 9311770589970,
"澳洲Nature's Way佳思敏儿童DHA深海鱼油" => 9314807022525,
"挪威BP青少年儿童深海鱼油增强记忆力DHA" => 7072270020302,
"意念方舟脑电波意念控制玩具" => 6959308600013,
"3D立体魔方 注意力训练开发智力玩具" => 6947911216035,
"盐酸昂丹司琼片(欧贝) 4mg*12s" => 6915798000900,
"阿瑞匹坦胶囊(意美) 125mg*1s+80mg*2s" => 6951283550376,
"利可君片(吉贝尔) 20mg*48s" => 6937528410100,
"升血调元颗粒 5g*8袋" => 6927632201503,
"生血丸(达仁堂) 5g*6袋/盒 每付重5g" => 6905395002308,
"生血宝合剂(健得) 100ml/盒 每瓶装100ml" => 6914405123094,
"生白口服液(梦阳) 20ml*6支/盒" => 6953040700024,
"升血小板胶囊(郝其军) 2×12粒/板/盒" => 6920723400014,
"养正合剂(步长) 10支/盒" => 6950077609115,
"芪胶升白胶囊(汉方) 36s/盒" => 6941764301009,
"双环醇片(百赛诺) 25mg*9s*2板" => 6936517200128,
"多烯磷脂酰胆碱胶囊(易善复) /盒,228mg*24s" => 6955206300024,
"谷胱甘肽片(阿拓莫兰) 0.1g*24片/盒" => 6922436100281,
"乌苯美司胶囊(百士欣),15粒/盒,10mg" => 6911322033994,
"贞芪扶正胶囊(扶正) 48s/盒 " => 6944157950013,
"消癌平片(金马) 0.32g*18s*4板" => 6926414002314,
"消癌平滴丸(他平) 0.35g*90s" => 6970899010036,
"参一胶囊(亚泰) 10mg*16s" => 6927301600033,
"西黄丸(克难) 3g*6袋/盒 每20丸重1g" => 6906952010576,
"榄香烯口服乳(金港) 6支/盒 20ml:0.2g" => 6901811005229,
"参丹散结胶囊(绿因) 0.4g*48s*1盒" => 6933515400406,
"槐耳颗粒(金克) 每盒装6包" => 6931121445118,
"康莱特软胶囊 每瓶72粒" => 6920917769286,
"威麦宁胶囊(华颐) 0.4g*60粒/瓶" => 6931790100080,
"安康欣胶囊(inking) 每瓶45粒" => 6923560010866,
"西黄丸(九寨沟) 3g*2瓶/盒" => 6923163700911,
"金龙胶囊(李建生) 30粒/瓶/盒" => 6970856529991,
"华蟾素胶囊(东泰) 每盒装18粒" => 6936838940291,
"盐酸埃克替尼片(凯美纳) 21片/盒" => 6914483211850,
"安替可胶囊(sinl) 0.22g*24s/盒" => 6928098400585,
"甲磺酸伊马替尼片 0.1g*60s/盒" => 6924647722290,
"康力欣胶囊(名扬) 12粒×3版/盒" => 6937951420073,
"康力欣胶囊(名扬) 12粒×4版/盒" => 6937951420219,
"碳酸钙片(协达利),100片/盒" => 6942656500029,
"复方环磷酰胺片(泰魁),24片/盒" => 6938817514769,
"雷公藤多苷片(得恩德),50片/盒,10mg" => 6901804060877,
"乙酰半胱氨酸胶囊(易维适),24粒/盒" => 6916783880002,
"他克莫司胶囊(赛福开),50粒,1mg" => 6900233883859,
"骨化三醇软胶囊(盖三淳),10粒/盒" => 6922799406143,
"硫酸羟氯喹片(纷乐),14片/盒" => 6934938300021,
"吗替麦考酚酯分散片(赛可平),0.25g*40片" => 6900233882111,
"乙酰半胱氨酸泡腾片(富露施),2片/板*2板,600mg" => 6925406100014,
"《自我治疗小儿多动症》" => 9787513210164,
"太极小儿智力糖浆" => 6918163020787,
"碳酸钙D3颗粒(朗迪),30袋装" => 6937667109378,
"盐酸哌甲酯缓释片（专注达）" => 6922154600643,
"阿法骨化醇片" => 6922436100229,
"和胃疗疳颗粒" => 6907911200663,
);
$i = 0;
        foreach($arr as $key =>$v ){
            $unitofwork = BeanFinder::get("UnitOfWork");
            $cond = " and title=:title";
            $bind = [];
            $bind[':title'] = $key;
            $shopProduct = Dao::getEntityByCond('ShopProduct', $cond, $bind);
            if($shopProduct instanceof ShopProduct){
                $i++;
                echo "\n\n-----[{$i}][{$key}]----- \n";
                $shopProduct->sku_code = $v;
            }else{
                echo "\n\n-----[not find][{$key}][{$v}]----- \n";
            }
            $unitofwork->commitAndInit();

        }
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_shopproduct_skucode.php]=====");

$process = new Fix_shopproduct_skucode();
$process->dowork();

Debug::trace("=====[cron][end][Fix_shopproduct_skucode.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
