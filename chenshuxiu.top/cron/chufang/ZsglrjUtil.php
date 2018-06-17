<?php

class ZsglrjUtil
{

    // 方寸处方ID
    protected $prescriptionId;

    // 登录 cookie_str
    protected $cookie_str;

    // 患者编号
    protected $HZBH;

    // 门诊编号
    protected $MZBH;

    // 处方编号
    protected $CFBH;

    public function __construct ($prescriptionId) {
        $this->prescriptionId = $prescriptionId;
    }

    // 开处方流程
    public function doWork () {
        $prescriptionId = $this->prescriptionId;

        // 预登录页, 获取初始 cookie_str
        // ASP.NET_SessionId=xxxx;
        // safedog-flow-item=xxxx;
        echo "\n=====000 preLogin=====\n";
        sleep(1);
        echo $cookie_str = $this->preLogin();

        // 登录提交, 获取登录 cookie_str
        // CurrentSessionID=xxxx;
        // username=fcys; pwd=123456;
        // zsglrj.com=FKReadDate=20171019; YPCount=0; IsSave=1
        echo "\n=====001 login=====\n";
        sleep(1);
        echo $cookie_str = $this->login();
        $this->cookie_str = $cookie_str;

        // 门诊编号
        echo "\n=====011 CreateKBJL=====\n";
        sleep(1);
        $MZBH = $this->CreateKBJL();
        $this->MZBH = $MZBH;

        // 处方编号
        echo "\n=====012 CreateCFNumber=====\n";
        sleep(1);
        $CFBH = $this->CreateCFNumber();
        $this->CFBH = $CFBH;

        // 患者编号,人员编号
        echo "\n=====013 LoadRYXX=====\n";
        sleep(1);
        $HZBH = $this->LoadRYXX();
        $this->HZBH = $HZBH;

        // 患者看病信息提交

        echo "\n===== SaveChufang == beg =====\n";
        sleep(1);
        $unitofwork = BeanFinder::get('UnitOfWork');

        // 处方实体
        $prescription = Prescription::getById($prescriptionId);

        // 处方编号
        if ($prescription->chufang_cfbh) {
            echo "\n prescription[{$prescription->id}]->chufang_cfbh = {$prescription->chufang_cfbh}; continue;  \n";
            echo "\n===== SaveChufang == break =====\n";
            return;
        }

        // 患者
        $patient = $prescription->patient;

        // 药品
        $medicineInfoArr = [];
        $prescriptionItems = $prescription->getPrescriptionItems();

        if (count($prescriptionItems) < 1) {
            echo "\n=====prescriptionItem cnt = 0=====\n";
            echo "\n===== break =====\n";
            return;
        }

        foreach ($prescriptionItems as $item) {
            $medicineproduct = $item->medicineproduct;
            $shopproduct = ShopProductDao::getShopProductByObjtypeObjid('MedicineProduct', $medicineproduct->id);
            $arr = [];
            $arr['YYZL'] = $item->cnt; // 数量
            $arr['YPDJ'] = $shopproduct->price / 100; // 单价
            $medicineInfoArr[$medicineproduct->id] = $arr;
        }

        // 患者姓名
        $HZXM = "{$patient->name}";

        // 患者性别 0 男 1 女
        $HZXB = $patient->sex - 1;
        if ($HZXB < 0) {
            $HZXB = "0"; // 默认男
        }

        // 年龄 (岁)
        $NL = $patient->getAgeStr();

        // 发病日期
        $FBRQ = $prescription->getCreateDay();

        // 保存类型_人员
        $SaveType_RY = 'NEW';

        // 患者
        if ($patient->chufang_hzbh) {
            $SaveType_RY = 'SAVE';
            $HZBH = $patient->chufang_hzbh;

            // 修正患者编号
            $this->HZBH = $HZBH;

            echo "\n=====old patient->HZBH = {$HZBH}=====\n";
        } else {

            if ('HZ' != mb_substr($HZBH, 0, 2)) {
                echo "\n===== SaveChufang == break == HZBH=[{$HZBH}] =====\n";
                return;
            }

            $patient->chufang_hzbh = $HZBH;
        }

        $arr = [];
        $arr['HZBH'] = $HZBH; // 患者编号
        $arr['HZXM'] = $HZXM; // 患者姓名
        $arr['MZBH'] = $MZBH; // 门诊编号
        $arr['CFBH'] = $CFBH; // 处方编号
        $arr['HZXB'] = "{$HZXB}"; // 患者性别 0 男 1 女
        $arr['NL'] = "{$NL}"; // 年龄 (岁)
        $arr['SG'] = ''; // 身高 (cm)
        $arr['TZ'] = ''; // 体重 (kg)
        $arr['ZDXX'] = ''; // 诊断信息
        $arr['YSYZ'] = ''; // 医生医嘱
        $arr['FBRQ'] = "{$FBRQ}"; // 发病日期

        echo "\n=====SaveChufang-0=====\n";
        print_r($arr);
        echo "\n=====SaveChufang-1=====\n";
        $result = $this->SaveChufang($SaveType_RY, $arr, $medicineInfoArr);
        echo "\n=====SaveChufang-2=====\n";

        if ('CF' == mb_substr($CFBH, 0, 2)) {
            echo "\n=====SaveChufang-success=====\n";
            $prescription->chufang_cfbh = $CFBH;
        } else {
            echo "\n=====SaveChufang-fail : {$result}=====\n";

            $prescription->chufang_cfbh = "";
            Debug::warn("prescriptionId = {$prescriptionId} : SaveChufang-fail : {$result}");
            return;
        }

        $unitofwork->commitAndInit();

        echo "\n===== SaveChufang == end =====\n";

        echo "\n=====129 ChargeResult == beg =====\n";
        // 实收金额
        $SSJE = 0;
        if($prescription->shoporder instanceof ShopOrder){
            $SSJE = $prescription->shoporder->item_sum_price / 100;
        }
        $result = $this->ChargeResult($SSJE);

        if (mb_strpos($result, 'ccess') > 1) {
            echo "\n=====ChargeResult-success=====\n";
        } else {
            echo "\n=====ChargeResult-fail : {$result}=====\n";

            $unitofwork = BeanFinder::get('UnitOfWork');

            $prescription = Prescription::getById($prescriptionId);
            $prescription->chufang_cfbh = "";

            Debug::warn("prescriptionId = {$prescriptionId} : ChargeResult-fail : {$result}");

            $unitofwork->commitAndInit();
        }
        echo "\n=====129 ChargeResult == end =====\n";
    }

    // 129 支付, 设置为收费
    private function ChargeResult ($SSJE) {
        $cookie_str = $this->cookie_str;

        $HZBH = $this->HZBH;
        $MZBH = $this->MZBH;
        $CFBH = $this->CFBH;

        $ZJE = sprintf("%.2f", $SSJE);

        $arr = [];
        $arr["SSJE"] = "{$SSJE}"; // 现金支付
        $arr["YHJE"] = "0"; // 优惠金额
        $arr["ZJE"] = "{$ZJE}"; // 实收金额?
        $arr["HZBH"] = $HZBH;
        $arr["MZBH"] = $MZBH;
        $arr["FYZT"] = "true";
        $arr["SZJE"] = "0"; // 赊账金额
        $arr["SRJE"] = "0.00"; // 舍入金额
        $arr["YHKJE"] = "0"; // 银行卡
        $arr["YBKJE"] = "0"; // 医保卡
        $arr["ZPJE"] = "0"; //
        $arr["ZLJE"] = "0";
        $arr["GDCF_Array"] = "{$CFBH},N";
        $arr["MemberPayAmount"] = "0"; // 会员卡?
        $arr["WeiXinPayAmount"] = "0"; // 微信
        $arr["ALiPayAmount"] = "0"; // 支付宝
        $arr["MemberID"] = "";
        $arr["FirstRecordCFBH"] = $CFBH;

        $data = json_encode($arr, JSON_UNESCAPED_UNICODE);

        $CJRQ = date("Y-m-d");

        $Referer = "http://www.zsglrj.cn/SFGL/CFSFFY.aspx?HZBH={$HZBH}&CJRQ={$CJRQ}&ZDZT=&MZBH={$MZBH}&IsAllotPart=N";

        $file = "129_ChargeResult.txt";
        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_Referer_]", $Referer, $cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $cmd = str_replace("[_data_]", $data, $cmd);

        echo "\n===============xxx=================\n";
        print_r($arr);
        echo "\n===============aaa=================\n";
        $jsonStr = system($cmd);
        echo "\n===============ccc=================\n";

        $len = mb_strlen($jsonStr);
        $result = mb_substr($jsonStr, 1, $len - 5);

        return $result;
    }

    // 119 保存看病信息(处方)
    private function SaveChufang ($SaveType_RY, $arr, $medicineInfoArr) {
        $cookie_str = $this->cookie_str;

        $arr = $this->initPost($SaveType_RY, $arr, $medicineInfoArr);

        if (empty($arr)) {
            return "药品尚未录入到处方系统";
        }

        $data = json_encode($arr, JSON_UNESCAPED_UNICODE);

        $file = "119_SaveKBXX.txt";
        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $cmd = str_replace("[_data_]", $data, $cmd);

        echo "\n===============xxx=================\n";
        print_r($arr);
        echo "\n===============aaa=================\n";
        // echo $cmd;
        // echo "\n===============bbb=================\n";
        // exit();
        $jsonStr = system($cmd);
        // echo "\n===============ccc=================\n";

        $len = mb_strlen($jsonStr);
        return $result = mb_substr($jsonStr, 1, $len - 5);
    }

    // initPost
    private function initPost ($SaveType_RY, $arr, $medicineInfoArr) {
        $HZBH = $arr['HZBH']; // 患者编号
        $HZXM = $arr['HZXM']; // 患者姓名
        $MZBH = $arr['MZBH']; // 门诊编号
        $CFBH = $arr['CFBH']; // 处方编号
        $HZXB = $arr['HZXB']; // 患者性别 0 男 1 女
        $NL = $arr['NL'] ? $arr['NL'] : '10'; // 年龄 (岁)
        $SG = $arr['SG'] ? $arr['SG'] : ''; // 身高 (cm)
        $TZ = $arr['TZ'] ? $arr['TZ'] : ''; // 体重 (kg)
        $ZDXX = $arr['ZDXX']; // 诊断信息
        $YSYZ = $arr['YSYZ']; // 医生医嘱
        $FBRQ = $arr['FBRQ']; // 发病日期

        $arr = [];
        $arr['HZBH'] = $HZBH; // 患者编号
        $arr['HZXXBJsonData'] = [];
        $arr['KBJLBJsonData'] = [];
        $arr['CFZBJsonData'] = [];
        $arr['mz'] = $MZBH; // 门诊编号
        $arr['cf'] = $CFBH; // 处方编号
        $arr['GMXX_JsonData'] = '';
        $arr['AGMS'] = '';
        $arr['function_name'] = 'Saves';

        $HZXXBJsonData = [];
        $HZXXBJsonData['SaveType_RY'] = $SaveType_RY;
        $HZXXBJsonData['SaveType_KB'] = 'NEW';
        $HZXXBJsonData['HZBH'] = $HZBH; // 患者编号
        $HZXXBJsonData['HZXM'] = $HZXM; // 患者姓名
        $HZXXBJsonData['HZXB'] = $HZXB; // 患者性别 0 男 1 女
        $HZXXBJsonData['NLDW'] = '1'; // 年龄单位, 岁
        $HZXXBJsonData['NL'] = $NL; // 年龄
        $HZXXBJsonData['SG'] = $SG; // 身高 (cm)
        $HZXXBJsonData['TZ'] = $TZ; // 体重 (kg)
        $HZXXBJsonData['HYZK'] = '0'; // 婚姻状况
        $HZXXBJsonData['DHHM'] = ''; // 电话号码, 联系电话
        $HZXXBJsonData['XXDZ'] = ''; // 详细地址
        $HZXXBJsonData['FBRQ'] = $FBRQ; // 发病日期
        $HZXXBJsonData['ZYBZ'] = '';
        $HZXXBJsonData['GMS'] = ''; // 过敏史
        $arr['HZXXBJsonData'] = json_encode($HZXXBJsonData, JSON_UNESCAPED_UNICODE);

        $KBJLBJsonData = [];
        $KBJLBJsonData['SaveType_CF'] = 'NEW';
        $KBJLBJsonData['SaveType_RY'] = $SaveType_RY;
        $KBJLBJsonData['SaveType_KB'] = 'NEW';
        $KBJLBJsonData['YBGH'] = '0'; // 医保挂号
        $KBJLBJsonData['GHDH'] = '0'; // 挂号DH
        $KBJLBJsonData['MZBH'] = $MZBH;
        $KBJLBJsonData['YSBH'] = '1029'; // 医生编号
        $KBJLBJsonData['YSXM'] = '王福升';
        $KBJLBJsonData['KSBH'] = '0';
        $KBJLBJsonData['KSXM'] = '全科';
        $KBJLBJsonData['YSYZ'] = $YSYZ; // 医生医嘱
        $KBJLBJsonData['HZBH'] = $HZBH;
        $KBJLBJsonData['HZXM'] = $HZXM;
        $KBJLBJsonData['ZDXX'] = $ZDXX; // 诊断信息
        $KBJLBJsonData['DHHM'] = ''; // 电话号码, 联系电话
        $KBJLBJsonData['NL'] = $NL; // 年龄
        $KBJLBJsonData['NLDW'] = '1'; // 年龄单位, 岁
        $KBJLBJsonData['HZXB'] = $HZXB; // 患者性别 0 男 1 女
        $KBJLBJsonData['FBRQ'] = $FBRQ; // 发病日期
        $KBJLBJsonData['ZDLB'] = '复诊';
        $KBJLBJsonData['LXDH'] = ''; // 联系电话, 电话号码
        $KBJLBJsonData['HYZK'] = '0'; // 婚姻状况
        $KBJLBJsonData['XXDZ'] = ''; // 详细地址
        $KBJLBJsonData['ZYBZ'] = '';
        $KBJLBJsonData['SG'] = $SG; // 身高 (cm)
        $KBJLBJsonData['TZ'] = $TZ; // 体重 (kg)
        $KBJLBJsonData['GMS'] = ''; // 过敏史
        $arr['KBJLBJsonData'] = json_encode($KBJLBJsonData, JSON_UNESCAPED_UNICODE);

        $CFZBJsonData = [];
        $CFZBJsonData['Page'] = 'KB';
        $CFZBJsonData['GHBH'] = '0';
        $CFZBJsonData['hidMZGHBH'] = '0';
        $CFZBJsonData['ZYBZ'] = '';
        $CFZBJsonData['MZBH'] = $MZBH;
        $CFZBJsonData['YSBH'] = '1029';
        $CFZBJsonData['YSXM'] = '王福升';
        $CFZBJsonData['CFBH'] = $CFBH; // 处方编号
        $CFZBJsonData['CFLX'] = '1'; // 处方类型, 1 xy 西药处方, 2 zy 中药处方, 3 jc 检查项目处方
        $CFZBJsonData['CFZT'] = '0';
        $CFZBJsonData['IllnessAbstract'] = '';
        $CFZBJsonData['CheckPurpose'] = '';
        $CFZBJsonData['SaveType_CF'] = 'NEW';
        $CFZBJsonData['SaveType_RY'] = $SaveType_RY;
        $CFZBJsonData['SaveType_KB'] = 'NEW';
        $CFZBJsonData['rows'] = [];
        $CFZBJsonData['FjFyRows'] = [];

        $ypJson = file_get_contents(dirname(__FILE__) . "/curl.txt/001_yp.txt");
        $ypArr = json_decode($ypJson, true);

        $demo = array(
            'XH' => '1',  // 序号
            'MRSL' => '100',  // 单次用量
            'YYTS' => '1',  // 天数
            'MCYL' => '100',  // 单次用量
            'YYZL' => '2',  // 总量
            'BZ' => '1',  // 备注
            'YPZL' => '2',  // 总量
            'YPDJ' => '5.0000'); // 药品单价

        $rows = [];
        $i = 0;
        foreach ($medicineInfoArr as $medicineProductId => $fixArr) {
            $row = $ypArr[$medicineProductId];

            // 跳过
            if (false == isset($row)) {
                echo "\n====\n medicineProductId = {$medicineProductId}, not in zsglrj \n====\n";
                return false;
            }

            $i ++;

            $row['XH'] = "{$i}";

            // 单次用量
            if (isset($fixArr['MRSL'])) {
                $row['MRSL'] = $fixArr['MRSL'];
                $row['MCYL'] = $fixArr['MRSL'];
            }

            // 单次用量
            if (isset($fixArr['MCYL'])) {
                $row['MRSL'] = $fixArr['MCYL'];
                $row['MCYL'] = $fixArr['MCYL'];
            }

            // 天数
            if (isset($fixArr['YYTS'])) {
                $row['YYTS'] = $fixArr['YYTS'];
            }

            // 总量
            if (isset($fixArr['YYZL'])) {
                $row['YYZL'] = $fixArr['YYZL'];
                $row['YPZL'] = $fixArr['YYZL'];
            }

            // 总量
            if (isset($fixArr['YPZL'])) {
                $row['YYZL'] = $fixArr['YPZL'];
                $row['YPZL'] = $fixArr['YPZL'];
            }

            // 备注
            if (isset($fixArr['BZ'])) {
                $row['BZ'] = $fixArr['BZ'];
            }

            // 药品单价
            if (isset($fixArr['YPDJ'])) {
                $row['YPDJ'] = $fixArr['YPDJ'];
            }

            $rows[] = $row;
        }
        $CFZBJsonData['rows'] = $rows;
        $FjFyRows = [];
        // $FjFyRows[] = array(
        // 'FYMC' => '注射费',
        // 'FYJE' => '10',
        // 'FYBH' => '44486');
        // $FjFyRows[] = array(
        // 'FYMC' => '材料费',
        // 'FYJE' => '5',
        // 'FYBH' => '44485');
        $CFZBJsonData['FjFyRows'] = $FjFyRows;
        $arr['CFZBJsonData'] = json_encode($CFZBJsonData, JSON_UNESCAPED_UNICODE);
        return $arr;
    }

    // 000 预登陆
    public function preLogin () {
        $url = "http://www.zsglrj.cn/LoginNew.aspx";
        $header = array(
            'GET /LoginNew.aspx HTTP/1.1',
            'Host: www.zsglrj.cn',
            'Accept-Encoding:gzip, deflate',
            'Accept-Language:zh-CN,zh;q=0.8,en;q=0.6',
            'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Cache-Control:max-age=0',
            'Connection:keep-alive',
            "Content-Type:text/html; charset=utf-8",
            'Referer: http://www.zsglrj.cn/conter_top.aspx',
            'Upgrade-Insecure-Requests:1',
            'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36');
        return self::exec_url_get_cookie_str($url, $header);
    }

    // 001 登录
    private function login () {
        $cookie_str = $this->cookie_str;

        // 使用上面保存的cookies再次访问
        $url = "http://www.zsglrj.cn/LoginNew.aspx";
        $header = [];
        $header[] = "Cookie: {$cookie_str}";
        $post_data = array(
            "mobile" => "fcys",
            "password" => "123456",
            "submit" => '登录');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); // 设置头信息的地方
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $return_content = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $result_header = substr($return_content, 0, $header_size);
        $result_header = trim($result_header);
        $count = preg_match_all('/Set-Cookie:(.*?);/', $result_header, $matchs);
        $arr = array();
        foreach ($matchs[1] as $a) {
            $arr[] = trim($a);
        }
        curl_close($ch);
        // echo "\n=====result_header==begin==\n";
        // echo $result_header;
        // echo "\n=====result_header==end==\n";
        // print_r($arr);
        $cookie_str2 = implode('; ', $arr);
        $FKReadDate = date('Ymd');
        $str = "-H 'Cookie: {$cookie_str}; {$cookie_str2}; username=fcys; pwd=123456; zsglrj.com=FKReadDate={$FKReadDate}; YPCount=0; IsSave=1'";
        $filename = dirname(__FILE__) . "/test000_cookie.txt";
        file_put_contents($filename, $str);
        return $str;
    }

    // 011 门诊编号
    private function CreateKBJL () {
        $cookie_str = $this->cookie_str;

        $file = "011_CreateKBJL.txt";
        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $jsonStr = system($cmd);
        $len = mb_strlen($jsonStr);
        $MZBH = mb_substr($jsonStr, 1, $len - 5);
        $MZBH = trim($MZBH);
        return $MZBH;
    }

    // 012 生成处方编号
    private function CreateCFNumber () {
        $cookie_str = $this->cookie_str;

        $file = "012_CreateCFNumber.txt";
        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $jsonStr = system($cmd);
        $len = mb_strlen($jsonStr);
        $CFBH = mb_substr($jsonStr, 1, $len - 5);
        $CFBH = trim($CFBH);
        return $CFBH;
    }

    // 013 人员信息, 患者编号
    private function LoadRYXX () {
        $cookie_str = $this->cookie_str;

        $file = "013_LoadRYXX.txt";
        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $jsonStr = system($cmd);
        $len = mb_strlen($jsonStr);
        $HZBH = mb_substr($jsonStr, 1, $len - 5);
        $HZBH = trim($HZBH);
        return $HZBH;
    }

    // 执行url, 获取新cookie
    private static function exec_url_get_cookie_str ($url, $header) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); // 设置头信息的地方
        curl_setopt($ch, CURLOPT_HEADER, 1); // 取得返回头信息
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $return_content = curl_exec($ch);
        // 返回码, 200
        // $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $result_header = substr($return_content, 0, $header_size);
        $result_header = trim($result_header);
        $count = preg_match_all('/Set-Cookie:(.*?);/', $result_header, $matchs);
        $arr = array();
        foreach ($matchs[1] as $a) {
            $arr[] = trim($a);
        }
        curl_close($ch);
        // echo "\n=====result_header==begin==\n";
        // echo $result_header;
        // echo "\n=====result_header==end==\n";
        // print_r($arr);
        return implode('; ', $arr);
    }

    // //////////////////////////
    private function doWorkFix () {
        $cookie_str = $this->cookie_str;

        // 患者信息,人员信息
        if (0) {
            echo "\n=====014 SelectRYXX=====\n";
            sleep(1);
            echo $HZBH = 'HZ201710190013';
            echo "\n=====014-1=====\n";
            $arr = $this->SelectRYXX();
            echo "\n=====014-2=====\n";
            print_r($arr);
            echo "\n";
        }

        // 获取看病数据, 姓名搜索
        if (0) {
            echo "\n=====015 GetKBDataList=====\n";
            sleep(1);
            echo $name = '方寸';
            echo "\n=====015-1=====\n";
            $arr = $this->GetKBDataList($name);
            print_r($arr);
            echo "\n";
        }

        // 年龄To出生日期
        if (0) {
            echo "\n=====111 AgeToCSRQ=====\n";
            sleep(1);
            echo $age = 15;
            echo "\n=====111-1=====\n";
            $CSRQ = $this->AgeToCSRQ($age);
            echo "\n=====111-2=====\n";
            echo $CSRQ;
            echo "\n";
        }

        // 获取当前处方
        if (0) {
            echo "\n=====112 GetCurrentCfNum=====\n";
            sleep(1);
            echo $MZBH = 'CF201710230008';
            echo "\n=====112-1=====\n";
            $result = $this->GetCurrentCfNum();
            echo "\n=====112-2=====\n";
            print_r($result);
            echo "\n";
        }

        // 验证药品库存
        if (0) {
            echo "\n=====113 ValidateYpKc=====\n";
            sleep(1);
            echo $ypxx = '[药品信息]';
            echo "\n=====113-1=====\n";
            $result = $this->ValidateYpKc($ypxx);
            echo "\n=====113-2=====\n";
            print_r($result);
            echo "\n";
        }

        // GetGMXXInfo
        if (0) {
            echo "\n=====201 GetGMXXInfo=====\n";
            sleep(1);
            echo $HZBH = 'HZ201710190013';
            echo "\n=====201-1=====\n";
            $result = $this->GetGMXXInfo();
            echo "\n=====201-2=====\n";
            print_r($result);
            echo "\n";
        }

        // GetGMXXInfoMX
        if (0) {
            echo "\n=====202 GetGMXXInfoMX=====\n";
            sleep(1);
            echo $HZBH = 'HZ201710190013';
            echo "\n=====202-1=====\n";
            $result = $this->GetGMXXInfoMX();
            echo "\n=====202-2=====\n";
            print_r($result);
            echo "\n";
        }

        // GetYpDwList1
        if (0) {
            echo "\n=====301 GetYpDwList1=====\n";
            sleep(1);
            $ypbhArray = [
                2017000171,
                2017000172,
                2017000173];

            echo "\n=====301-1=====\n";
            $result = $this->GetYpDwList1($ypbhArray);
            echo "\n=====301-2=====\n";
            print_r($result);
            echo "\n";
        }
    }

    // 014 选择人员信息, 患者编号
    private function SelectRYXX () {
        $cookie_str = $this->cookie_str;
        $HZBH = $this->HZBH;

        $file = "014_SelectRYXX.txt";
        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $cmd = str_replace("[_HZBH_]", $HZBH, $cmd);
        $jsonStr = system($cmd);
        $len = mb_strlen($jsonStr);
        $jsonStr = mb_substr($jsonStr, 1, $len - 5);
        $jsonStr = str_replace('\"', '"', $jsonStr);
        return $arr = json_decode($jsonStr, true);
    }

    // 015 获取看病数据, 姓名搜索
    private function GetKBDataList ($name) {
        $cookie_str = $this->cookie_str;

        $file = "015_GetKBDataList.txt";
        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $cmd = str_replace("[_name_]", $name, $cmd);
        $jsonStr = system($cmd);
        echo "\n===============aaa=================\n";
        $len = mb_strlen($jsonStr);
        $jsonStr = mb_substr($jsonStr, 0, $len - 3);
        echo "\n===============bbb=================\n";
        $jsonStr = str_replace('\"', '"', $jsonStr);
        $jsonStr = str_replace(':new', ':"new', $jsonStr);
        $jsonStr = str_replace(')),', '))",', $jsonStr);
        echo "\n===============ccc=================\n";
        return $arr = json_decode($jsonStr, true);
    }

    // 111 年龄To出生日期
    private function AgeToCSRQ ($age) {
        $cookie_str = $this->cookie_str;

        $file = "111_AgeToCSRQ.txt";
        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $cmd = str_replace("[_age_]", $age, $cmd);
        $jsonStr = system($cmd);
        echo "\n===============aaa=================\n";
        $len = mb_strlen($jsonStr);
        return $CSRQ = mb_substr($jsonStr, 1, $len - 5);
    }

    // 112 获取当前门诊( 根据门诊编号, 获取处方编号 )
    private function GetCurrentCfNum () {
        $cookie_str = $this->cookie_str;
        $MZBH = $this->MZBH;

        $file = "112_GetCurrentCfNum.txt";
        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $cmd = str_replace("[_MZBH_]", $MZBH, $cmd);
        $jsonStr = system($cmd);
        echo "\n===============aaa=================\n";
        $len = mb_strlen($jsonStr);
        return $CfNum = mb_substr($jsonStr, 1, $len - 5);
    }

    // 113 验证药品库存
    private function ValidateYpKc ($ypxx) {
        $cookie_str = $this->cookie_str;

        $file = "113_ValidateYpKc.txt";
        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $data = '{"Pc_JsonData":"{\"rows\":[{\"YPBH\":\"2017000153\",\"YYZL\":\"7\",\"YPMC\":\"ZSD\"},{\"YPBH\":\"2015000473\",\"YYZL\":\"6\",\"YPMC\":\"XYP\"}]}"}';
        $cmd = str_replace("[_data_]", $data, $cmd);
        $jsonStr = system($cmd);
        echo "\n===============aaa=================\n";
        $len = mb_strlen($jsonStr);
        return $CfNum = mb_substr($jsonStr, 1, $len - 5);
    }

    // 201_GetGMXXInfo
    private function GetGMXXInfo () {
        $cookie_str = $this->cookie_str;
        $HZBH = $this->HZBH;

        $file = "201_GetGMXXInfo.txt";

        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $cmd = str_replace("[_HZBH_]", $HZBH, $cmd);

        $jsonStr = system($cmd);

        echo "\n===============aaa=================\n";
        $len = mb_strlen($jsonStr);
        return $result = mb_substr($jsonStr, 1, $len - 5);
    }

    // 202_GetGMXXInfoMX
    private function GetGMXXInfoMX () {
        $cookie_str = $this->cookie_str;
        $HZBH = $this->HZBH;

        $file = "202_GetGMXXInfoMX.txt";

        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $cmd = str_replace("[_HZBH_]", $HZBH, $cmd);

        $jsonStr = system($cmd);

        echo "\n===============aaa=================\n";
        $len = mb_strlen($jsonStr);
        return $result = mb_substr($jsonStr, 1, $len - 5);
    }

    // 301_GetYpDwList1, 药品
    private function GetYpDwList1 ($ypbhArray) {
        $cookie_str = $this->cookie_str;

        // $file = "301_GetYpDwList1.data.txt";
        // $data = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $arr = [];
        $arr['YPBH'] = $ypbhArray;

        echo "\n===============aaa=================\n";
        $data = json_encode($arr);

        $file = "301_GetYpDwList1.txt";
        $cmd = file_get_contents(dirname(__FILE__) . "/curl.txt/{$file}");
        $cmd = trim($cmd);
        $cmd = str_replace("[_cookie_]", $cookie_str, $cmd);
        $cmd = str_replace("[_data_]", $data, $cmd);

        $jsonStr = system($cmd);

        echo "\n===============bbb=================\n";
        $len = mb_strlen($jsonStr);
        $jsonStr = mb_substr($jsonStr, 1, $len - 5);
        $jsonStr = str_replace('\"', '"', $jsonStr);

        return json_decode($jsonStr, true);
    }
}
