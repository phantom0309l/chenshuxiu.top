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
class Add_doctor
{

    // 新建提交
    public function dowork () {
        $arr_lilly = array(
            "周国岭" => "杭州市第七人民医院",
            "周圆月" => "杭州市第七人民医院",
            "刘艳" => "杭州市第七人民医院",
            "丁凯景" => "杭州市第七人民医院",
            "朱晓华_杭州" => "杭州市第七人民医院",
            "陈玉燕" => "浙江省中医院",
            "方妍彤" => "杭州市第二人民医院",
            "潘黎明" => "湖州市妇幼保健院",
            "林敏" => "湖州市第三人民医院",
            "孔微" => "浙江省新华医院",
            "胡正" => "南京儿童医院",
            "谢玲" => "南京儿童医院",
            "李荣" => "南京儿童医院",
            "彭璐婷" => "南京儿童医院",
            "苏志暖" => "南京儿童医院",
            "陈静_南京" => "南京儿童医院",
            "朱萍" => "徐州儿童医院",
            "刘淑华_徐州" => "徐州第一人民医院",
            "刘亦芳" => "安徽省立儿童医院",
            "钟慧" => "安徽省精卫中心",
            "郎亚琴" => "杭州市第一人民医院",
            "项俊华" => "杭州市第一人民医院",
            "吴怡玲" => "杭州市第一人民医院",
            "沈根美" => "嘉兴市妇幼保健院",
            "李荣" => "浙江大学医学院附属儿童医院",
            "杨荣旺" => "浙江大学医学院附属儿童医院",
            "高维佳" => "浙江大学医学院附属儿童医院",
            "张雁翼" => "浙江大学医学院附属儿童医院",
            "王晓安" => "杭州市儿童医院",
            "陈克" => "杭州市儿童医院",
            "蒋茂莹" => "杭州市儿童医院",
            "陈一心" => "南京脑科医院",
            "王民洁" => "南京脑科医院",
            "王晨阳" => "南京脑科医院",
            "虞琳" => "南京脑科医院",
            "焦公凯" => "南京脑科医院",
            "张久平" => "南京脑科医院",
            "储康康" => "南京脑科医院",
            "方慧" => "南京脑科医院",
            "唐洪丽" => "中大医院",
            "韩新民" => "江苏省中医药",
            "陆小彦" => "首都医科大学附属北京儿童医院",
            "张纪水" => "首都医科大学附属北京儿童医院",
            "王芳" => "首都医科大学附属北京儿童医院",
            "闻芳" => "首都医科大学附属北京儿童医院",
            "王旭" => "首都医科大学附属北京儿童医院",
            "王晓慧" => "首都医科大学附属北京儿童医院",
            "郑华" => "首都医科大学附属北京儿童医院",
            "王梦阳" => "首都医科大学附属三博脑科医院",
            "黄秀琴" => "中国人民解放军陆军总医院",
            "李南平" => "中国人民解放军陆军总医院",
            "王贺茹" => "首都儿科研究所",
            "宋文红" => "首都儿科研究所",
            "王昕" => "首都儿科研究所",
            "朱彦丽" => "首都儿科研究所",
            "王珺" => "首都儿科研究所",
            "董静静" => "首都儿科研究所",
            "彭晓音" => "首都儿科研究所",
            "孙静" => "首都儿科研究所",
            "赵东红" => "北京大学第一医院",
            "王春刚" => "北京昌平中西医结合医院",
            "崔永华" => "首都医科大学附属北京安定医院",
            "杨建虹" => "首都医科大学附属北京安定医院",
            "贾军朴" => "首都医科大学附属北京安定医院",
            "梁月竹" => "首都医科大学附属北京安定医院",
            "杜海霞" => "首都医科大学附属北京安定医院",
            "闫俊娟" => "首都医科大学附属北京安定医院",
            "周玉明" => "首都医科大学附属北京安定医院",
            "于丽萍" => "首都医科大学附属北京安定医院",
            "张礼萍" => "首都医科大学附属北京宣武医院",
            "韩枚" => "首都医科大学附属北京宣武医院",
            "戚小红" => "首都医科大学附属北京宣武医院",
            "王玉凤" => "北大六院",
            "杨莉" => "北大六院",
            "李雪" => "北大六院",
            "曹庆久" => "北大六院",
            "王力芳" => "北大六院",
            "邢颖" => "华信医院",
            "秦炯" => "人民医院",
            "周晖" => "四川大学华西第二医院",
            "俞丹" => "四川大学华西第二医院",
            "蔡晓唐" => "四川大学华西第二医院",
            "母发光" => "四川省人民医院",
            "欧阳颖" => "四川省人民医院",
            "吴梅" => "成都市妇女儿童中心医院",
            "吴惧" => "成都市妇女儿童中心医院",
            "徐洋" => "成都市妇女儿童中心医院",
            "胡文广" => "成都市妇女儿童中心医院",
            "刘军_绵阳" => "绵阳市第三人民医院",
            "查彩慧" => "广州市妇女儿童医疗中心",
            "汪玲华" => "广州市妇女儿童医疗中心",
            "杨思渊" => "广州市妇女儿童医疗中心",
            "李志斌" => "广州市妇女儿童医疗中心",
            "欧婉杏" => "广州市妇女儿童医疗中心",
            "戴津" => "广州市妇女儿童医疗中心",
            "林海生" => "广州市妇女儿童医疗中心",
            "李建英" => "中山大学附属第三医院",
            "苏梅蕾" => "南方医院",
            "江慧敏" => "广州市妇女儿童医疗中心",
            "戚元丽" => "广东省人民医院",
            "曾小璐" => "广东省人民医院",
            "贾艳滨" => "暨南大学附属第一医院",
            "黄俏庭" => "暨南大学附属第一医院",
            "王馨" => "广东省妇幼保健院",
            "罗秋燕" => "越秀区儿童医院",
            "郑庆梅" => "顺德市伍仲佩纪念医院",
            "俞红" => "佛山市妇幼保健院",
            "黄赛君" => "佛山市妇幼保健院",
            "王玲" => "佛山市第三人民医院",
            "张洪宇" => "中山大学第附属第一医院",
            "程道猛" => "广州市惠爱医院",
            "李苏义" => "广州市惠爱医院",
            "麦思铭" => "广州市惠爱医院",
            "徐莉萍" => "广州市惠爱医院",
            "刘英华" => "广州医科大学附属第一医院",
            "陈强" => "珠海市妇幼保健院",
            "刘军_广州" => "广州医科大学附属第二医院",
            "钱兴国" => "广州医科大学附属荔湾医院",
            "庄志成" => "珠海市妇幼保健院",
            "周家秀" => "深圳儿童医院",
            "李泽华" => "深圳儿童医院",
            "张美燕" => "深圳儿童医院",
            "林鄞" => "深圳儿童医院",
            "周甄会" => "深圳儿童医院",
            "寇聪" => "深圳儿童医院",
            "杨斌让" => "深圳儿童医院",
            "冯哲" => "深圳市妇幼保健院",
            "黄彦科" => "深圳市南山妇幼保健院",
            "齐云柯" => "深圳市康宁医院",
            "高延" => "深圳市宝安区妇幼保健院",
            "曹丽娟" => "卢湾区妇幼保健院",
            "陈佳英" => "上海市儿童医院",
            "陈津津" => "上海市儿童医院",
            "陈丽琴" => "上海市儿童医院",
            "陈连红" => "上海市儿童医院",
            "姜莲" => "上海市儿童医院",
            "马士薇" => "上海市儿童医院",
            "裘建萍" => "上海市普陀区精神卫生中心",
            "王瑜" => "上海市儿童医院",
            "魏梅" => "上海市儿童医院",
            "贺影忠" => "上海儿童医学中心",
            "王纪文" => "上海儿童医学中心",
            "陈莹" => "上海儿童医学中心",
            "吴虹" => "上海儿童医学中心",
            "顾红亮" => "上海市浦东新区妇幼保健院",
            "林霞凤" => "上海市浦东新区精神卫生中心",
            "林国珍" => "上海瑞金医院",
            "史以珏" => "上海瑞金医院",
            "丁丽凤" => "上海儿童医学中心",
            "李斐" => "上海国际医学中心",
            "徐通" => "上海长征医院",
            "何英" => "上海长征医院",
            "周翊" => "上海长征医院",
            "帅瑞雪" => "上海长征医院",
            "柴毅明" => "复旦大学附属儿科医院",
            "王佶" => "复旦大学附属儿科医院",
            "周渊峰" => "复旦大学附属儿科医院",
            "张林妹" => "复旦大学附属儿科医院",
            "李文辉" => "复旦大学附属儿科医院",
            "钱嬿" => "上海长海医院",
            "薛敏波" => "上海交通大学医学院附属新华医院",
            "帅澜" => "上海交通大学医学院附属新华医院",
            "徐明玉" => "上海交通大学医学院附属新华医院",
            "夏卫萍" => "上海交通大学医学院附属新华医院",
            "陈静" => "上海市精神卫生中心",
            "江文庆" => "上海市精神卫生中心",
            "徐筠" => "上海市精神卫生中心",
            "张宏" => "上海市精神卫生中心",
            "蒋燕清" => "上海六一儿童医院",
            "史积善" => "上海六一儿童医院",
            "方拴锋" => "郑州儿童医院",
            "朱晓华_郑州" => "郑州儿童医院",
            "雷革非" => "山东大学齐鲁医院",
            "夏颖" => "青岛妇女儿童医院",
            "靳彦琴" => "郑州大学第三附属医院",
            "周长虹" => "青岛妇女儿童医院",
            "钱静" => "聊城人民医院",
            "匡桂芳" => "青岛妇女儿童医院",
            "刘秀梅" => "烟台毓璜顶医院",
            "黄晓玲" => "淄博妇幼保健院",
            "张云玲" => "洛阳市妇女儿童医疗保健中心",
            "张跃兵" => "济宁精神病防治院",
            "李燕" => "山东省千佛山医院",
            "刘心洁" => "山东大学齐鲁医院",
            "冀永娟" => "青岛妇女儿童医院",
            "陈娟" => "青岛妇女儿童医院",
            "邹华" => "青岛妇女儿童医院",
            "谢虹" => "泰安市妇幼保健院",
            "张华" => "东营市人民医院",
            "吕攀攀" => "滨州医学院附属医院",
            "刘芳" => "滨州医学院附属医院",
            "邢婕" => "德州妇幼保健院",
            "杨良政" => "山东大学齐鲁儿童医院",
            "赵冬梅" => "山东大学齐鲁儿童医院",
            "郝秀" => "山东大学第二医院",
            "田慧玲" => "临沂市妇幼保健院");

        $arr_lilly = [
            "闻芳" => "首都医科大学附属北京儿童医院",
            "吴康敏" => "四川大学华西第二医院",
            "王馨" => "广东省妇幼保健院",
            "靳彦琴" => "郑州大学第三附属医院"];

        $arr_doctor = array(
            "齐云柯" => "深圳市精神卫生中心",
            "黄赛君" => "佛山市妇幼保健院",
            "黄彦科" => "深圳市南山区妇幼保健院",
            "高维佳" => "浙江大学医学院附属儿童医院",
            "马士薇" => "上海交通大学附属儿童医院",
            "项俊华" => "杭州市第一人民医院",
            "韩新民" => "江苏省中医院",
            "陈静_南京" => "南京儿童医院",
            "陈静" => "武汉市妇女儿童医疗保健中心",
            "陈玉燕" => "浙江省中医院",
            "陈强" => "珠海市妇幼保健院",
            "陈娟" => "青岛市妇女儿童医院",
            "陈一心" => "南京脑科医院",
            "陆小彦" => "首都医科大学附属北京儿童医院",
            "钱嬿" => "上海长海医院",
            "钱兴国" => "广州医学院荔湾医院",
            "钟慧" => "合肥市第四人民医院",
            "郑庆梅" => "伍仲珮纪念医院",
            "郑华" => "首都医科大学附属北京儿童医院",
            "郎亚琴" => "杭州市第一人民医院",
            "邹华" => "青岛市妇女儿童医院",
            "邢颖" => "北京华信医院(清华大学第一附属医院)",
            "赵冬梅" => "济南市儿童医院",
            "贾艳滨" => "暨南大学附属第一医院",
            "贾军朴" => "北京安定医院",
            "谢虹" => "泰安市儿童医院",
            "谢玲" => "南京儿童医院",
            "虞琳" => "南京脑科医院",
            "蒋茂莹" => "杭州市儿童医院",
            "苏梅蕾" => "广州南方医院",
            "苏志暖" => "南京儿童医院",
            "胡正" => "南京儿童医院",
            "罗秋燕" => "广州市越秀区儿童医院",
            "程道猛" => "广州市惠爱医院",
            "秦炯" => "北京大学人民医院",
            "王贺茹" => "儿研所",
            "王瑜" => "上海交通大学附属儿童医院",
            "王珺" => "儿研所",
            "王玉凤" => "北医六院",
            "王民洁" => "南京脑科医院",
            "王晨阳" => "南京脑科医院",
            "王晓慧" => "首都医科大学附属北京儿童医院",
            "王晓安" => "杭州市儿童医院",
            "王佶" => "复旦大学附属儿科医院",
            "焦公凯" => "南京脑科医院",
            "潘黎明" => "湖州市妇幼保健院",
            "沈根美" => "嘉兴市妇幼保健院",
            "汪玲华" => "广州市妇女儿童医疗中心",
            "欧阳颖" => "四川省人民医院",
            "欧婉杏" => "广州妇女儿童医疗中心",
            "梁月竹" => "北京安定医院",
            "查彩慧" => "广州市妇女儿童医疗中心",
            "林鄞" => "广州市惠爱医院",
            "林海生" => "广州市妇女儿童医疗中心",
            "杨莉" => "北医六院",
            "杨荣旺" => "浙江大学医学院附属儿童医院",
            "杨良政" => "济南市儿童医院",
            "杨斌让" => "深圳市儿童医院",
            "杨思渊" => "广州市妇女儿童医疗中心",
            "杨建虹" => "北京安定医院",
            "李荣" => "浙江大学医学院附属儿童医院",
            "李苏义" => "广州市惠爱医院",
            "李燕" => "山东省千佛山医院",
            "李斐" => "上海新华医院",
            "李志斌" => "广州市妇女儿童医疗中心",
            "李建英" => "中山大学附属第三医院",
            "朱萍" => "徐州市儿童医院",
            "朱晓华_郑州" => "郑州市儿童医院",
            "朱晓华_杭州" => "杭州市第七人民医院",
            "曾小璐" => "广东省人民医院",
            "曹庆久" => "北医六院",
            "方拴锋" => "郑州市儿童医院",
            "方妍彤" => "杭州师范大学附属医院",
            "戴津" => "广州市妇女儿童医疗中心",
            "戚元丽" => "广东省人民医院",
            "徐通" => "长征医院",
            "徐莉萍" => "广州市惠爱医院",
            "徐筠" => "上海市精神卫生中心",
            "张雁翼" => "浙江大学医学院附属儿童医院",
            "张美燕" => "深圳市儿童医院",
            "张纪水" => "首都医科大学附属北京儿童医院",
            "张洪宇" => "中山大学附属第一医院",
            "张宏" => "上海市精神卫生中心",
            "张华" => "东营市人民医院",
            "张云玲" => "洛阳市妇女儿童医疗保健中心",
            "张久平" => "南京脑科医院",
            "庄志成" => "珠海市妇幼保健院",
            "帅澜" => "上海新华医院",
            "崔永华" => "北京安定医院",
            "寇聪" => "深圳市精神卫生中心",
            "宋文红" => "儿研所",
            "夏颖" => "青岛市妇女儿童医院",
            "夏卫萍" => "上海新华医院",
            "唐洪丽" => "东南大学附属中大医院",
            "周长虹" => "青岛市妇女儿童医院",
            "周翊" => "长征医院",
            "周甄会" => "深圳市儿童医院",
            "周家秀" => "深圳市儿童医院",
            "周圆月" => "杭州市第七人民医院",
            "周国岭" => "杭州市第七人民医院",
            "吴梅" => "成都市妇女儿童中心医院",
            "吴惧" => "成都新世纪妇女儿童医院",
            "匡桂芳" => "青岛市妇女儿童医院",
            "刘英华" => "广州医科大学附属第一医院",
            "刘芳" => "东营市妇幼保健院",
            "刘艳" => "武汉同济医院",
            "刘淑华_沈阳" => "沈阳市儿童医院",
            "刘淑华_徐州" => "徐州第一人民医院",
            "刘军_广州" => "广州医科大学附属第二医院",
            "刘军_绵阳" => "绵阳市第三人民医院",
            "刘亦芳" => "安徽省儿童医院",
            "冯哲" => "深圳市妇幼保健院",
            "冀永娟" => "青岛市妇女儿童医院",
            "储康康" => "南京脑科医院",
            "俞红" => "佛山市妇幼保健院");

        $need_add_doctor = array();
        foreach ($arr_lilly as $k => $v) {
            if (false == isset($arr_doctor[$k])) {
                $need_add_doctor[$k] = $v;
            }
        }

        foreach ($need_add_doctor as $k => $v) {
            echo "\n医生名：{$k}；医院名：{$v}";
            $unitofwork = BeanFinder::get("UnitOfWork");
            $this->addOne($k, $v);
            $unitofwork->commitAndInit();
        }
    }

    private function addOne ($doctor_name, $hospital_name) {
        // $username = strtolower(PinyinUtilNew::Word2PY($doctor_name));
        $username = strtolower(PinyinUtil::Pinyin($doctor_name));

        if ($doctor_name == '王馨') {
            $username = 'wangxin';
        }

        $user = UserDao::getByUserName($username);

        if ($user instanceof User) {
            echo " {$username}已存在,建议修改登录名为 医院拼音+医生拼音 ";
            return;
        } else {
            echo "$username adduser";
        }

        $hospital = $this->getHospital($hospital_name);

        if (false == $hospital instanceof Hospital) {
            echo " {$username}找不到医院，手动查验吧！！！ ";
            return;
        }

        $disease = Disease::getById(1);

        $row = array();
        $row["username"] = $username;
        $row["password"] = $username . rand(300, 999);
        $row["name"] = $doctor_name;
        $user = User::createByBiz($row);
        echo "\n新建user成功：{$user->id}";

        $row = array();
        $row["id"] = 1 + Dao::queryValue('select max(id) as maxid from doctors where id < 10000');
        $row["userid"] = $user->id;
        $row["name"] = $doctor_name;
        $row["auditorid_yunying"] = 0;
        $row["auditorid_market"] = 0;
        $row["auditorid_createby"] = 0;

        $row["hospitalid"] = $hospital->id;

        $row["code"] = $username;

        $row["pdoctorid"] = 0;
        $row["status"] = 1;

        $doctor = Doctor::createByBiz($row);
        echo "\n新建doctor成功：{$doctor->id}";

        // 新建doctorwxshopref
        $wxshop = WxShopDao::getByDiseaseid(1);
        $row = [];
        $row['doctorid'] = $doctor->id;
        $row['wxshopid'] = $wxshop->id;
        $doctorwxshopref = DoctorWxShopRef::createByBiz($row);
        $doctorwxshopref->check_qr_ticket();

        // 新建doctorDiseaseRef
        $row = array();
        $row["doctorid"] = $doctor->id;
        $row["diseaseid"] = 1;
        $doctorDiseaseRef = DoctorDiseaseRef::createByBiz($row);
    }

    private function getHospital ($hospital_name) {
        $arr_change_hospital = array(
            '湖州市第三人民医院' => '湖州市妇幼保健院',
            '中国人民解放军陆军总医院' => '中国人民解放军总医院',
            '首都儿科研究所' => '儿研所',
            '首都医科大学附属北京安定医院' => '北京安定医院',
            '首都医科大学附属北京宣武医院' => '首都医科大学宣武医院',
            '北大六院' => '北医六院',
            '四川大学华西第二医院' => '华西妇产儿童医院',
            '深圳儿童医院' => '深圳市儿童医院',
            '上海儿童医学中心' => '上海交通大学医学院附属上海儿童医学中心',
            '上海交通大学医学院附属新华医院' => '上海新华医院');

        if (isset($arr_change_hospital[$hospital_name])) {
            $hospital_name = $arr_change_hospital[$hospital_name];
        }

        return Dao::getEntityByCond('Hospital', " and (name like '%{$hospital_name}%' or shortname like '%{$hospital_name}%') ");
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Add_doctor.php]=====");

$process = new Add_doctor();
$process->dowork();

Debug::trace("=====[cron][end][Add_doctor.php]=====");
// Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
