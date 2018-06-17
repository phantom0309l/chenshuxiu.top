<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Patient_xuyan
{
    public function getFixStr($str, $default = '')
    {
        if (empty($str)) {
            return $default;
        }

        return trim(str_replace(array(
           "\n",
           "\t"), "", $str));
    }

    public function checkNameAndOut_case_no($out_case_no_old)
    {
        $patient = $this->getPatientByOut_case_no($out_case_no_old);

        if ($patient instanceof Patient) {
            return "modify";
        } else {
            return "add";
        }
    }

    public function createPatient($arr)
    {
        $row = array();
        $row['out_case_no'] = $this->getFixStr($arr['病历号']);
        $row['name'] = $this->getFixStr($arr['姓名']);
        $row['sex'] = $this->getFixStr($arr['性别']);
        $row['nation'] = $this->getFixStr($arr['民族']);
        $row['birthday'] = $this->getFixStr($arr['生日'], '0000-00-00');
        $row['career'] = $this->getFixStr($arr['职业']);
        $row['marry_status'] = $this->getFixStr($arr['婚姻']);
        $row['prcrid'] = $this->getFixStr($arr['身份证']);
        $row['address'] = $this->getFixStr($arr['住址']);
        $row['native_place'] = $this->getFixStr($arr['籍贯']);
        $row['autoimmune_illness'] = $this->getFixStr($arr['自身免疫病']);
        $row['other_illness'] = $this->getFixStr($arr['其他疾病']);
        $row['childbearing_history'] = $this->getFixStr($arr['生育史']);
        $row['diseaseid'] = 3;
        $row['doctorid'] = 33;
        $row['mobile'] = $this->getFixStr($arr['本人手机']);
        $row['other_contacts'] = $arr['联系人user的名字'] . "|" . $arr['联系人关系'] . "|" . $arr['联系人手机'];
        $row['email'] = $this->getFixStr($arr['本人邮箱']);
        $row['remark'] = "添加";

        $row['status'] = 3;

        $patient = Patient::createByBiz($row);

        return $patient;
    }

    public function createPcard($patient, $arr)
    {
        $row = array();
        $row['create_doc_date'] = $this->getFixStr($arr['建档日期'], '0000-00-00');
        $row['out_case_no'] = $patient->out_case_no;
        $row['doctorid'] = 33;
        $row['diseaseid'] = 3;
        $row['patientid'] = $patient->id;
        $row['patient_name'] = $patient->name;

        $pcard = Pcard::createByBiz($row);

        return $pcard;
    }

    public function getPatientByOut_case_no($out_case_no)
    {
        $cond = " and out_case_no = '{$out_case_no}' and doctorid = 33 ";

        return Dao::getEntityByCond("Patient", $cond);
    }

    private $modifyArr = array();

    public function modifyField($obj, $k, $v)
    {
        if ($v) {
            if($obj->$k != $v){
                $tempValue = $obj->$k;
                if(empty($tempValue) || $obj->$k == '0000-00-00'){
                    $obj->$k = $v;
                }else{
                    echo "({$k}:{$obj->$k}->{$v})\r\n";
                    $className = get_class($obj);
                    $this->modifyArr["{$className}->{$k}"] = $v;
                }
            }
        }
    }

    public function modifyPatient(Patient $patient, $arr)
    {
        echo "\r\n[modify patient,patientid = {$patient->id}]\r\n";
        $this->modifyField($patient, 'sex', $this->getFixStr($arr['性别']));
        $this->modifyField($patient, 'birthday', $this->getFixStr($arr['生日']));
        $this->modifyField($patient, 'native_place', $this->getFixStr($arr['籍贯']));
        $this->modifyField($patient, 'prcrid', $this->getFixStr($arr['身份证']));
        $this->modifyField($patient, 'nation', $this->getFixStr($arr['民族']));
        $this->modifyField($patient, 'marry_status', $this->getFixStr($arr['婚姻']));
        $this->modifyField($patient, 'career', $this->getFixStr($arr['职业']));
        $this->modifyField($patient, 'address', $this->getFixStr($arr['住址']));
        $this->modifyField($patient, 'childbearing_history', $this->getFixStr($arr['生育史']));
        $this->modifyField($patient, 'autoimmune_illness', $this->getFixStr($arr['自身免疫病']));
        $this->modifyField($patient, 'other_illness', $this->getFixStr($arr['其他疾病']));
        $this->modifyField($patient, 'mobile', $this->getFixStr($arr['本人手机']));
        $this->modifyField($patient, 'email', $this->getFixStr($arr['本人邮箱']));
        $this->modifyField($patient, 'other_contacts', $this->getFixStr($arr['联系人user的名字']) . "|" . $this->getFixStr($arr['联系人关系']) . "|" . $this->getFixStr($arr['联系人手机']));

        //如果有user则修改user
        $user = UserDao::getMyselfByPatientid($patient->id);
        if ($user instanceof User) {
            echo "\r\n[modify myuser,id = {$user->id}]\r\n";

            $this->modifyField($user, 'mobile', $this->getFixStr($arr['本人手机']));
            $this->modifyField($user, 'phone', $this->getFixStr($arr['本人固话']));
            $this->modifyField($user, 'email', $this->getFixStr($arr['本人邮箱']));
        }

        $user_lianxiren = UserDao::getLianxirenByPatientid($patient->id);
        if ($user_lianxiren instanceof User) {
            echo "\r\n[modify lianxirenuser,id = {$user_lianxiren->id}]\r\n";

            $this->modifyField($user_lianxiren, 'mobile', $this->getFixStr($arr['联系人手机']));
            $this->modifyField($user_lianxiren, 'phone', $this->getFixStr($arr['联系人固话']));
            $this->modifyField($user_lianxiren, 'shipstr', $this->getFixStr($arr['联系人关系']));
            $this->modifyField($user_lianxiren, 'name', $this->getFixStr($arr['联系人user的名字']));
        }

        //如果有pcard则修改，没有则新建
        $pcard = $patient->getMasterPcard();
        if ($pcard instanceof Pcard) {
            echo "\r\n[modify pcard,id = {$pcard->id}]\r\n";

            $this->modifyField($pcard, 'create_doc_date', $this->getFixStr($arr['建档日期']));
            $this->modifyField($pcard, 'out_case_no', $this->getFixStr($arr['病历号']));
        } else {
            $pcard = $this->createPcard($patient, $arr);

            echo "\r\n[create pcard,id = {$pcard->id}]\r\n";
        }

        $patient->remark = empty($this->modifyArr) ? '' : json_encode($this->modifyArr,JSON_UNESCAPED_UNICODE);
        $this->modifyArr = array();

        return $patient;
    }

    public function dowork()
    {

        // 从文件中导入徐雁患者
        $json_string = file_get_contents("H:/Patient/zuihou/1000.txt");
        $patientinfos = json_decode($json_string, true);

        $excludePatients = array("李傅凤","石秋生","幕秀花");

        $i = 0;
        foreach ($patientinfos as $arr) {
            $unitofwork = BeanFinder::get("UnitOfWork");

            if(true == in_array($this->getFixStr($arr['姓名']), $excludePatients)){
                continue;
            }

            $out_case_no = $this->getFixStr($arr['病历号']);

            $patient = $this->getPatientByOut_case_no($out_case_no);
            if (false == $patient instanceof Patient) {
                echo "-----------------------create patient begin--------------------\r\n";

                //姓名和病历号都不相同，新建
                $patient = $this->createPatient($arr);

                $pcard = $this->createPcard($patient, $arr);

                echo "[create patient and pcard ,patientid = {$patient->id}, pcard = {$pcard->id}]\n";

                echo "-----------------------create patient end--------------------\r\n\r\n";
            } else {
                echo "-----------------------modify patient begin--------------------\r\n";

                echo "[modify patient or user or pcard]\r\n";

                //姓名和病历号都相同，修改
                $patient = $this->modifyPatient($patient, $arr);

                echo "-----------------------modify patient end--------------------\r\n\r\n";
            }

            $i++;

            $unitofwork->commitAndInit();
        }

        echo "==========================" . $i;
    }
}

$patient_xuyan = new Patient_xuyan();
$patient_xuyan->dowork();
