<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
ini_set('date.timezone', 'Asia/Shanghai');
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class getpatient
{

    public function getSexNum ($sexstr) {
        $arr = self::getValue($sexstr);

        if (trim($arr) == '男') {
            return 1;
        } else {
            return 2;
        }
    }

    public function getValue ($obj) {
        $arr1 = (array) $obj;

        return $arr1[0];
    }

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $patientinfos = array();
        $countHtml = count(glob("/tmp/htmls/*.html"));
        for ($i = 1; $i <= $countHtml; $i ++) {
            $dom = new DOMDocument();
            $dom->loadHTMLFile('/tmp/htmls/' . $i . '.html');
            $xml = simplexml_import_dom($dom);

            $out_case_no = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[3]/td[2]'));
            $name = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[3]/td[4]'));
            $sexstr = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[3]/td[6]'));

            $birthday = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[4]/td[2]'));
            $nation = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[4]/td[4]'));
            $career = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[4]/td[6]'));

            $prcrid = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[5]/td[2]'));
            $marry_status = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[5]/td[4]'));
            $address = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[5]/td[6]'));

            $native_place = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[6]/td[2]'));

            $phone_myself = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[8]/td[2]'));
            $mobile_myself = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[8]/td[4]'));
            $email_myself = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[8]/td[6]'));

            $shipstr_lianxiren = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[10]/td[2]'));

            $name_lianxiren = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[11]/td[2]'));
            $phone_lianxiren = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[11]/td[4]'));
            $mobile_lianxiren = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[11]/td[6]'));

            $create_doc_date = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[13]/td[2]'));

            $hy_num = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[15]/td[2]'));
            $hy_date = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[16]/td[2]'));
            $sy_num = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[17]/td[2]'));
            $sy_date = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[18]/td[2]'));

            $childbearing_history = $hy_num . "|" . $sy_num . "|" . $hy_date . "|" . $sy_date;

            $autoimmune_illness = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[20]/td[2]'));
            $other_illness = array_shift($xml->xpath('/html/body/div[1]/table[1]/tr[20]/td[4]'));

            echo "------------------------------ " . $i . "\n";

            $patientinfos[$i]['医生id'] = 33;
            $patientinfos[$i]['疾病id'] = 3;
            $patientinfos[$i]['病历号'] = trim(self::getValue($out_case_no));
            $patientinfos[$i]['姓名'] = trim(self::getValue($name));
            $patientinfos[$i]['性别'] = trim(self::getSexNum($sexstr));
            $patientinfos[$i]['生日'] = trim(self::getValue($birthday));
            $patientinfos[$i]['民族'] = trim(self::getValue($nation));
            $patientinfos[$i]['职业'] = trim(self::getValue($career));
            $patientinfos[$i]['身份证'] = trim(self::getValue($prcrid));
            $patientinfos[$i]['婚姻'] = trim(self::getValue($marry_status));
            $patientinfos[$i]['住址'] = trim(self::getValue($address));
            $patientinfos[$i]['籍贯'] = trim(self::getValue($native_place));
            $patientinfos[$i]['生育史'] = trim($childbearing_history);
            $patientinfos[$i]['自身免疫病'] = trim(self::getValue($autoimmune_illness));
            $patientinfos[$i]['其他疾病'] = trim(self::getValue($other_illness));

            // ------------------------------------------user_myself--------------------------------------------------------
            $patientinfos[$i]['本人固话'] = trim(self::getValue($phone_myself));
            $patientinfos[$i]['本人手机'] = trim(self::getValue($mobile_myself));
            $patientinfos[$i]['本人邮箱'] = trim(self::getValue($email_myself));
            $patientinfos[$i]['本人关系'] = '本人';
            $patientinfos[$i]['本人登录名'] = trim(self::getValue($name));
            $patientinfos[$i]['本人user的名字'] = trim(self::getValue($name));
            // ------------------------------------------user_myself--------------------------------------------------------

            // ------------------------------------------user_lianxiren--------------------------------------------------------
            $patientinfos[$i]['联系人固话'] = trim(self::getValue($phone_lianxiren));
            $patientinfos[$i]['联系人手机'] = trim(self::getValue($mobile_lianxiren));
            $patientinfos[$i]['联系人关系'] = trim(self::getValue($shipstr_lianxiren));
            $patientinfos[$i]['联系人登录名'] = trim(self::getValue($name_lianxiren));
            $patientinfos[$i]['联系人user的名字'] = trim(self::getValue($name_lianxiren));

            $patientinfos[$i]['就诊卡医生'] = 33;
            $patientinfos[$i]['就诊卡疾病'] = 3;
            $patientinfos[$i]['建档日期'] = trim(self::getValue($create_doc_date));
        }

        $myfile = fopen("/tmp/checkups/1000.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($patientinfos));
        fclose($myfile);

        echo "============" . count($patientinfos);

        $unitofwork->commitAndInit();
    }
}

$getpatient = new getpatient();
$getpatient->dowork();
