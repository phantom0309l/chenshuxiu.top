<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "512M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

error_reporting(E_ALL ^ E_NOTICE);

TheSystem::init(__FILE__);

class Import_chufang_zsglrj_cn
{

    public function dowork () {
        //$this->login1();
        //$this->login2();
        //$this->chufangPost();

        //登录post
        $post = array(
            "mobile" => "fcys",
            "password" => "123456",
            "submit" => '登录'
        );

        //登录地址
        $url = "http://www.zsglrj.cn/LoginNew.aspx";

        //cookie保存路径
        $cookie = dirname(__FILE__) . '/cookie.txt';

        //模拟登录
        $this->login_post($url, $cookie, $post);

        //获取某个页面
        //$url2 = "http://www.zsglrj.cn/head.htm#";
        //$content = $this->get_content($url2, $cookie);

        $url4 = "http://www.zsglrj.cn/main.htm";
        $content = $this->get_content($url4, $cookie);

        //尝试提交数据
        //$url3 = "http://www.zsglrj.cn/ajaxpro/MK.ZS.Web.KB.KBGL,MK.ZS.Web.ashx";
        //$content = $this->get_content($url3, $cookie);
        //$post_data = $this->initPost();

        //$this->postdata($url3, $cookie, $post_data);
        //$post_data = json_encode($post_data, JSON_UNESCAPED_UNICODE);
        //$content = $this->get_content($url2, $cookie);

        //删除cookie文件
        @unlink($cookie);

        echo "123{$content}";
    }

    //模拟登录
    private function login_post($url, $cookie, $post){
        $curl = curl_init();//初始化curl模块
        curl_setopt($curl, CURLOPT_URL, $url);//登录提交的地址
        curl_setopt($curl, CURLOPT_HEADER, 0);//是否显示头信息
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);//是否自动显示返回的信息
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie); //设置Cookie信息保存在指定的文件中
        curl_setopt($curl, CURLOPT_POST, 1);//post方式提交
        //curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));//要提交的信息
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));//要提交的信息
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
        curl_exec($curl);//执行cURL
        curl_close($curl);//关闭cURL资源，并且释放系统资源
    }

    //模拟提交数据
    private function postdata($url, $cookie, $post){
        $curl = curl_init();//初始化curl模块
        curl_setopt($curl, CURLOPT_URL, $url);//登录提交的地址
        curl_setopt($curl, CURLOPT_HEADER, 0);//是否显示头信息
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie); //设置Cookie信息保存在指定的文件中
        curl_setopt($curl, CURLOPT_POST, 1);//post方式提交
        //curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));//要提交的信息
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);//要提交的信息
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        curl_exec($curl);//执行cURL
        curl_close($curl);//关闭cURL资源，并且释放系统资源
    }

    //登录成功后获取数据
    function get_content($url, $cookie) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //读取cookie
        $rs = curl_exec($ch); //执行cURL抓取页面内容
        curl_close($ch);
        return $rs;
    }

    public function login1 () {
        $cookie_file = dirname(__FILE__) . '/cookie.txt';
        echo "\n\n========================1========================\n\n";

        // 先获取cookies并保存
        $url = "http://www.zsglrj.cn/LoginNew.aspx";

        echo $url;

        $ch = curl_init($url); // 初始化
        curl_setopt($ch, CURLOPT_HEADER, 0); // 不返回header部分
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回字符串，而非直接输出
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); // 存储cookies
        $response = curl_exec($ch);
        curl_close($ch);
    }

    public function login2 () {
        $cookie_file = dirname(__FILE__) . '/cookie.txt';
        echo "\n\n========================2========================\n\n";

        // 使用上面保存的cookies再次访问
        $url = "http://www.zsglrj.cn/LoginNew.aspx";

        $post_data = array(
            "mobile" => "fcys",
            "password" => "123456",
            "submit" => '登录');

        echo $url;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);

        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); // 使用上面获取的cookies
        $response = curl_exec($ch);
        curl_close($ch);

        print_r($response);
    }

    public function chufangPost () {
        $cookie_file = dirname(__FILE__) . '/cookie.txt';
        echo "\n\n========================3-1========================\n\n";

        $url = "http://www.zsglrj.cn/ajaxpro/MK.ZS.Web.KB.KBGL,MK.ZS.Web.ashx";

        $post_data = $this->initPost();

        $post_data = json_encode($post_data, JSON_UNESCAPED_UNICODE);

        echo $post_data;
        echo "\n\n========================3-2========================\n\n";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file); // 使用上面获取的cookies
        $response = curl_exec($curl);
        curl_close($curl);

        var_dump($response);
        echo "\n\n========================3-3========================\n\n";
    }

    private function testJson () {
        $str = '{"HZBH":"HZ201709270004","HZXXBJsonData":"{\"SaveType_RY\":\"NEW\",\"SaveType_KB\":\"NEW\",\"HZBH\":\"HZ201709270004\",\"HZXM\":\"方寸测试1\",\"HZXB\":\"0\",\"NLDW\":\"1\",\"NL\":\"12\",\"SG\":\"134\",\"TZ\":\"56\",\"HYZK\":\"0\",\"DHHM\":\"78901234\",\"XXDZ\":\"567890\",\"FBRQ\":\"2017-09-27\",\"ZYBZ\":\"\",\"GMS\":\"\"}","KBJLBJsonData":"{\"SaveType_CF\":\"NEW\",\"SaveType_RY\":\"NEW\",\"SaveType_KB\":\"NEW\",\"YBGH\":\"0\",\"GHDH\":\"0\",\"MZBH\":\"MZ201709270006\",\"YSBH\":\"1029\",\"YSXM\":\"王福升\",\"KSBH\":\"0\",\"KSXM\":\"全科\",\"YSYZ\":\"5678\",\"HZBH\":\"HZ201709270004\",\"HZXM\":\"方寸测试1\",\"ZDXX\":\"1234\",\"DHHM\":\"78901234\",\"NL\":\"12\",\"NLDW\":\"1\",\"HZXB\":\"0\",\"FBRQ\":\"2017-09-27\",\"ZDLB\":\"复诊\",\"LXDH\":\"78901234\",\"HYZK\":\"0\",\"XXDZ\":\"567890\",\"ZYBZ\":\"\",\"SG\":\"134\",\"TZ\":\"56\",\"GMS\":\"\"}","CFZBJsonData":"{\"Page\":\"KB\",\"GHBH\":\"0\",\"hidMZGHBH\":\"0\",\"ZYBZ\":\"\",\"MZBH\":\"MZ201709270006\",\"YSBH\":\"1029\",\"YSXM\":\"王福升\",\"CFBH\":\"CF201709270006\",\"CFLX\":\"1\",\"CFZT\":\"0\",\"IllnessAbstract\":\"\",\"CheckPurpose\":\"\",\"SaveType_CF\":\"NEW\",\"SaveType_RY\":\"NEW\",\"SaveType_KB\":\"NEW\",\"rows\":[{\"YPBH\":\"2015000098\",\"CanUseYB\":\"0\",\"PC\":\"CGD201707180001\",\"YPMC\":\"酒精\",\"FPXMBH\":\"1\",\"PrintName\":\"酒精\",\"YPLBBH\":\"1\",\"GG\":\"100毫升/瓶*1/瓶\",\"MRSL\":\"100\",\"YYTS\":\"1\",\"PD\":\"1\",\"MCYL\":\"100\",\"JXDWBH\":\"毫升\",\"YYZL\":\"2\",\"YYZCS\":\"1\",\"DJ\":\"5\",\"YFZT\":\"\",\"YFBH\":\"42\",\"YFMC\":\"口服\",\"BZ\":\"1\",\"SFBH\":\"\",\"DDW\":\"瓶\",\"XDW\":\"瓶\",\"YPZLDW\":\"1\",\"HSL2\":\"1\",\"ZH\":\"1\",\"LRRBH\":\"1029\",\"LRRMC\":\"王福升\",\"XH\":\"1\",\"HSBL\":\"1.0000\",\"ZLDWMC\":\"瓶\",\"ZLDWBH\":\"4\",\"YPZL\":\"2\",\"YPDJ\":\"5.0000\",\"ZYYFBH\":\"\"},{\"YPBH\":\"2017000151\",\"CanUseYB\":\"0\",\"PC\":\"CGD201709190001\",\"YPMC\":\"择思达\",\"FPXMBH\":\"1\",\"PrintName\":\"择思达\",\"YPLBBH\":\"1\",\"GG\":\"10mg/粒*7/盒\",\"MRSL\":\"10\",\"YYTS\":\"1\",\"PD\":\"2\",\"MCYL\":\"10\",\"JXDWBH\":\"mg\",\"YYZL\":\"3\",\"YYZCS\":\"1\",\"DJ\":\"1\",\"YFZT\":\"\",\"YFBH\":\"2\",\"YFMC\":\"静脉注射\",\"BZ\":\"2\",\"SFBH\":\"\",\"DDW\":\"盒\",\"XDW\":\"粒\",\"YPZLDW\":\"1\",\"HSL2\":\"7\",\"ZH\":\"1\",\"LRRBH\":\"1029\",\"LRRMC\":\"王福升\",\"XH\":\"2\",\"HSBL\":\"1.0000\",\"ZLDWMC\":\"盒\",\"ZLDWBH\":\"1\",\"YPZL\":\"3\",\"YPDJ\":\"1.0000\",\"ZYYFBH\":\"\"},{\"YPBH\":\"2015000045\",\"CanUseYB\":\"0\",\"PC\":\"CGD201708080002\",\"YPMC\":\"风油精\",\"FPXMBH\":\"1\",\"PrintName\":\"风油精\",\"YPLBBH\":\"1\",\"GG\":\"3mg/瓶*20/盒\",\"MRSL\":\"3\",\"YYTS\":\"1\",\"PD\":\"3\",\"MCYL\":\"3\",\"JXDWBH\":\"mg\",\"YYZL\":\"1\",\"YYZCS\":\"1\",\"DJ\":\"60\",\"YFZT\":\"\",\"YFBH\":\"1\",\"YFMC\":\"外用\",\"BZ\":\"3\",\"SFBH\":\"\",\"DDW\":\"盒\",\"XDW\":\"瓶\",\"YPZLDW\":\"1\",\"HSL2\":\"20\",\"ZH\":\"2\",\"LRRBH\":\"1029\",\"LRRMC\":\"王福升\",\"XH\":\"3\",\"HSBL\":\"1.0000\",\"ZLDWMC\":\"盒\",\"ZLDWBH\":\"1\",\"YPZL\":\"1\",\"YPDJ\":\"60.0000\",\"ZYYFBH\":\"\"},{\"YPBH\":\"2015000200\",\"CanUseYB\":\"0\",\"PC\":\"CGD201708110001\",\"YPMC\":\"咽炎片\",\"FPXMBH\":\"1\",\"PrintName\":\"咽炎片\",\"YPLBBH\":\"1\",\"GG\":\"0.25g/片*24/盒\",\"MRSL\":\"0.25\",\"YYTS\":\"1\",\"PD\":\"4\",\"MCYL\":\"0.25\",\"JXDWBH\":\"g\",\"YYZL\":\"5\",\"YYZCS\":\"1\",\"DJ\":\"11\",\"YFZT\":\"\",\"YFBH\":\"8\",\"YFMC\":\"冲服\",\"BZ\":\"4\",\"SFBH\":\"\",\"DDW\":\"盒\",\"XDW\":\"片\",\"YPZLDW\":\"1\",\"HSL2\":\"24\",\"ZH\":\"2\",\"LRRBH\":\"1029\",\"LRRMC\":\"王福升\",\"XH\":\"4\",\"HSBL\":\"1.0000\",\"ZLDWMC\":\"盒\",\"ZLDWBH\":\"1\",\"YPZL\":\"5\",\"YPDJ\":\"11.0000\",\"ZYYFBH\":\"\"}],\"FjFyRows\":[{\"FYMC\":\"注射费\",\"FYJE\":\"10\",\"FYBH\":\"44486\"},{\"FYMC\":\"材料费\",\"FYJE\":\"5\",\"FYBH\":\"44485\"}]}","mz":"MZ201709270006","cf":"CF201709270006","GMXX_JsonData":"","AGMS":"","function_name":"Saves"}';

        $arr = json_decode($str, true);

        $arr['HZXXBJsonData'] = json_decode($arr['HZXXBJsonData'], true);
        $arr['KBJLBJsonData'] = json_decode($arr['KBJLBJsonData'], true);
        $arr['CFZBJsonData'] = json_decode($arr['CFZBJsonData'], true);
        $arr['GMXX_JsonData'] = json_decode($arr['GMXX_JsonData'], true);

        print_r($arr);
    }

    private function initPost () {
        $arr = [];
        $arr['HZBH'] = 'HZ201709300004';
        $arr['HZXXBJsonData'] = [];
        $arr['KBJLBJsonData'] = [];
        $arr['CFZBJsonData'] = [];
        $arr['mz'] = 'MZ201709270006';
        $arr['cf'] = 'CF201709270006';
        $arr['GMXX_JsonData'] = '';
        $arr['AGMS'] = '';
        $arr['function_name'] = 'Saves';

        $HZXXBJsonData = [];
        $HZXXBJsonData['SaveType_RY'] = 'NEW';
        $HZXXBJsonData['SaveType_KB'] = 'NEW';
        $HZXXBJsonData['HZBH'] = 'HZ201709300008';
        $HZXXBJsonData['HZXM'] = '方寸3';
        $HZXXBJsonData['HZXB'] = '0';
        $HZXXBJsonData['NLDW'] = '1';
        $HZXXBJsonData['NL'] = '12';
        $HZXXBJsonData['SG'] = '134';
        $HZXXBJsonData['TZ'] = '56';
        $HZXXBJsonData['HYZK'] = '0';
        $HZXXBJsonData['DHHM'] = '78901234';
        $HZXXBJsonData['XXDZ'] = '567890';
        $HZXXBJsonData['FBRQ'] = '2017-09-27';
        $HZXXBJsonData['ZYBZ'] = '';
        $HZXXBJsonData['GMS'] = '';

        $arr['HZXXBJsonData'] = json_encode($HZXXBJsonData, JSON_UNESCAPED_UNICODE);

        $KBJLBJsonData = [];
        $KBJLBJsonData['SaveType_CF'] = 'NEW';
        $KBJLBJsonData['SaveType_RY'] = 'NEW';
        $KBJLBJsonData['SaveType_KB'] = 'NEW';
        $KBJLBJsonData['YBGH'] = '0';
        $KBJLBJsonData['GHDH'] = '0';
        $KBJLBJsonData['MZBH'] = 'MZ201709270006';
        $KBJLBJsonData['YSBH'] = '1029';
        $KBJLBJsonData['YSXM'] = '王福升';
        $KBJLBJsonData['KSBH'] = '0';
        $KBJLBJsonData['KSXM'] = '全科';
        $KBJLBJsonData['YSYZ'] = '5678';
        $KBJLBJsonData['HZBH'] = 'HZ201709300008';
        $KBJLBJsonData['HZXM'] = '方寸4';
        $KBJLBJsonData['ZDXX'] = '1234';
        $KBJLBJsonData['DHHM'] = '78901234';
        $KBJLBJsonData['NL'] = '12';
        $KBJLBJsonData['NLDW'] = '1';
        $KBJLBJsonData['HZXB'] = '0';
        $KBJLBJsonData['FBRQ'] = '2017-09-27';
        $KBJLBJsonData['ZDLB'] = '复诊';
        $KBJLBJsonData['LXDH'] = '78901234';
        $KBJLBJsonData['HYZK'] = '0';
        $KBJLBJsonData['XXDZ'] = '567890';
        $KBJLBJsonData['ZYBZ'] = '';
        $KBJLBJsonData['SG'] = '134';
        $KBJLBJsonData['TZ'] = '56';
        $KBJLBJsonData['GMS'] = '';

        $arr['KBJLBJsonData'] = json_encode($KBJLBJsonData, JSON_UNESCAPED_UNICODE);

        $CFZBJsonData = [];
        $CFZBJsonData['Page'] = 'KB';
        $CFZBJsonData['GHBH'] = '0';
        $CFZBJsonData['hidMZGHBH'] = '0';
        $CFZBJsonData['ZYBZ'] = '';
        $CFZBJsonData['MZBH'] = 'MZ201709270006';
        $CFZBJsonData['YSBH'] = '1029';
        $CFZBJsonData['YSXM'] = '王福升';
        $CFZBJsonData['CFBH'] = 'CF201709270006';
        $CFZBJsonData['CFLX'] = '1';
        $CFZBJsonData['CFZT'] = '0';
        $CFZBJsonData['IllnessAbstract'] = '';
        $CFZBJsonData['CheckPurpose'] = '';
        $CFZBJsonData['SaveType_CF'] = 'NEW';
        $CFZBJsonData['SaveType_RY'] = 'NEW';
        $CFZBJsonData['SaveType_KB'] = 'NEW';
        $CFZBJsonData['rows'] = [];
        $CFZBJsonData['FjFyRows'] = [];
        $rows = [];
        $rows[] = array(
            'YPBH' => '2015000098',
            'CanUseYB' => '0',
            'PC' => 'CGD201707180001',
            'YPMC' => '酒精',
            'FPXMBH' => '1',
            'PrintName' => '酒精',
            'YPLBBH' => '1',
            'GG' => '100毫升/瓶*1/瓶',
            'MRSL' => '100',
            'YYTS' => '1',
            'PD' => '1',
            'MCYL' => '100',
            'JXDWBH' => '毫升',
            'YYZL' => '2',
            'YYZCS' => '1',
            'DJ' => '5',
            'YFZT' => '',
            'YFBH' => '42',
            'YFMC' => '口服',
            'BZ' => '1',
            'SFBH' => '',
            'DDW' => '瓶',
            'XDW' => '瓶',
            'YPZLDW' => '1',
            'HSL2' => '1',
            'ZH' => '1',
            'LRRBH' => '1029',
            'LRRMC' => '王福升',
            'XH' => '1',
            'HSBL' => '1.0000',
            'ZLDWMC' => '瓶',
            'ZLDWBH' => '4',
            'YPZL' => '2',
            'YPDJ' => '5.0000',
            'ZYYFBH' => '');

        $CFZBJsonData['rows'] = $rows;

        $FjFyRows = [];
        $FjFyRows[] = array(
            'FYMC' => '注射费',
            'FYJE' => '10',
            'FYBH' => '44486');
        $FjFyRows[] = array(
            'FYMC' => '材料费',
            'FYJE' => '5',
            'FYBH' => '44485');

        $CFZBJsonData['FjFyRows'] = $FjFyRows;

        $arr['CFZBJsonData'] = json_encode($CFZBJsonData, JSON_UNESCAPED_UNICODE);

        return $arr;
    }
}

$time = date('Y-m-d H:i:s');
echo "\n{$time} ==== init ====\n";

$process = new Import_chufang_zsglrj_cn();
$process->dowork();

$time = date('Y-m-d H:i:s');
echo "\n{$time} ==== end ====\n";
