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

class Copypatientdata
{
    private $copyfordoctorid = 13;

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $now = date("Y-m-d H:i:s", time());
        $sql = "select a.id
        from patients a
        inner join (
        select a.patientid as patientid, count(a.id) as cnt
        from pipes a
        inner join papers b on b.id=a.objid
        where a.objtype='Paper' and b.papertplid=100996299
        and a.patientid>0
        group by a.patientid
        ) p1 on p1.patientid=a.id
        inner join (
        select patientid as patientid, count(id) as cnt
        from pipes
        where objtype='DrugSheet'
        and patientid>0
        group by patientid
        ) p2 on p2.patientid=a.id
        where a.is_test=0 and status=1 and diseaseid=1 and a.clone_by_patientid=0
        and p1.cnt>10
        and p2.cnt>10
        order by p1.cnt desc, p2.cnt desc limit 20";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            echo "\n====[Patient][{$id}]===\n";
            $pcard = $patient->getMasterPcard();
            $user = $patient->getMasterUser();
            $wxuser = $patient->getMasterWxUser();
            if ($pcard instanceof Pcard && $user instanceof User && $wxuser instanceof WxUser) {

                // 创建新患者
                $newPatient = $this->createNewPatient($patient);

                // 创建新pcard
                $newPcard = $this->createNewPcard($newPatient, $pcard);

                // 创建user
                $newUser = $this->createNewUser($newPatient, $user);

                // 创建wxuser
                $newWxUser = $this->createNewWxUser($newUser, $wxuser);

                XPatientIndex::updateXPatientIndexName($newPatient->name, $newPatient);

                // 创建pipes
                $sql = "select id from pipes where patientid={$patient->id} order by id";
                $ids = Dao::queryValues($sql);
                $this->createNewPipes($ids, $newPatient, $newUser, $newWxUser);

                // 创建patientmedicinerefs
                $sql = "select id from patientmedicinerefs where patientid={$patient->id} order by id";
                $ids = Dao::queryValues($sql);
                $this->createNewPatientMedicineRefs($ids, $newPatient, $newUser, $newWxUser);

                // 创建linkmans
                $sql = "select id from linkmans where patientid={$patient->id} order by id";
                $ids = Dao::queryValues($sql);
                $this->createNewLinkmans($ids, $newPatient, $newUser, $newWxUser);

                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        $unitofwork->commitAndInit();
    }

    private function createNewPipes ($pipeids, $newPatient, $newUser, $newWxUser) {
        foreach ($pipeids as $id) {
            $pipe = Pipe::getById($id);

            if ($pipe->objtype == "WxPicMsg") {
                continue;
            }

            // 判断下newpipe是不是已经存在了
            $newid = $id - 60000000;
            $newPipe = Pipe::getById($newid);
            if ($newPipe instanceof Pipe) {
                continue;
            }
            if ($pipe instanceof Pipe) {
                $need_init_arr = array(
                    'id' => $newid,
                    'doctorid' => $this->copyfordoctorid,
                    'patientid' => $newPatient->id,
                    'userid' => $newUser->id,
                    'wxuserid' => $newWxUser->id);
                $newPipe = $this->createByEntity($pipe, $need_init_arr);

                // clone pipe对应的obj
                $newObj = $this->createNewPipeObj($pipe, $newPatient, $newUser, $newWxUser);
                if ($newObj) {
                    $newPipe->set4lock("objid", $newObj->id);
                }
            }
            echo "\n==[Pipe][{$id}]===\n";
        }
    }

    private function createNewPipeObj ($pipe, $newPatient, $newUser, $newWxUser) {
        $newObj = null;
        $arr = array(
            "Paper",
            "DrugItem",
            "WxTxtMsg",
            "PushMsg");
        $objtype = $pipe->objtype;
        if (in_array($objtype, $arr)) {
            $objid = $pipe->objid;
            $newid = ($objid - 60000000) < 0 ? $objid + 40000000 : $objid - 60000000;

            // 防止重复追加
            $newObj = $objtype::getById($newid);
            if ($newObj) {
                echo "\n==[{$objtype}][{$id}][done]===\n";
                return $newObj;
            }

            $obj = $objtype::getById($objid);
            if ($obj) {
                $need_init_arr = array(
                    'id' => $newid,
                    'doctorid' => $this->copyfordoctorid,
                    'patientid' => $newPatient->id,
                    'userid' => $newUser->id,
                    'wxuserid' => $newWxUser->id);

                // 如果是pushmsg 还需要修正content里的患者姓名
                if ($objtype == "PushMsg") {
                    $content = $this->getContentFixForPushMsg($obj);
                    $need_init_arr['content'] = $content;
                }
                $newObj = $this->createByEntity($obj, $need_init_arr);
            }
            echo "\n==[Pipe][{$objtype}][{$objid}]===\n";
        }
        return $newObj;
    }

    private function getContentFixForPushMsg ($pushmsg) {
        $sendway = $pushmsg->sendway;
        $patient = $pushmsg->patient;
        $doctor = $pushmsg->doctor;
        $content = $pushmsg->content;
        if ($patient instanceof Patient) {
            if ($sendway == "wechat_template") {
                $contentArr = json_decode($content, true);
                $content = json_encode($contentArr, JSON_UNESCAPED_UNICODE);
            }

            $content = $this->getContentFix($content, $patient, $doctor);
        }
        return $content;
    }

    private function getContentFix ($content, $patient, $doctor) {
        // 筛选过滤患者名
        $name = $patient->name;
        if ($name) {
            $star = $this->getPrivacyName($name);
            $content = str_replace($name, $star, $content);
        }

        // 筛选过滤医生名、医院名
        if($doctor instanceof Doctor){
            $doctorname = $doctor->name;
            $doctorname_else = substr($doctor->name, 0, 1) . '大夫';
            $hospitalname = "";
            $hospital = $doctor->hospital;
            if($hospital instanceof Hospital){
                $hospitalname = $doctor->hospital->name;
            }
            $filterArr = array(
                $doctorname => "扁鹊大夫",
                $doctorname_else => "方寸",
                $hospitalname => "方寸医院");
            foreach ($filterArr as $key => $v) {
                $content = str_replace($key, $v, $content);
            }
        }

        return $content;
    }

    private function createNewPatientMedicineRefs ($patientmedicinerefids, $newPatient, $newUser = null, $newWxUser = null) {
        foreach ($patientmedicinerefids as $id) {
            $patientmedicineref = PatientMedicineRef::getById($id);

            // 判断下newpatientmedicineref是不是已经存在了
            $newid = $id - 60000000;
            $newPatientmedicineref = PatientMedicineRef::getById($newid);
            if ($newPatientmedicineref instanceof PatientMedicineRef) {
                continue;
            }
            if ($patientmedicineref instanceof PatientMedicineRef) {
                $need_init_arr = array(
                    'id' => $newid,
                    'wxuserid' => $newWxUser->id,
                    'userid' => $newUser->id,
                    'patientid' => $newPatient->id);
                $newPatientmedicineref = $this->createByEntity($patientmedicineref, $need_init_arr);
            }
            echo "\n==[PatientMedicineRef][{$id}]===\n";
        }
    }

    private function createNewLinkmans ($linkmanids, $newPatient, $newUser = null, $newWxUser = null) {
        foreach ($linkmanids as $id) {
            $linkman = Linkman::getById($id);

            // 判断下newlinkman是不是已经存在了
            $newid = $id - 60000000;
            $newlinkman = Linkman::getById($newid);
            if ($newlinkman instanceof Linkman) {
                continue;
            }
            if ($linkman instanceof Linkman) {
                $name = $this->getPrivacyName($linkman->name);
                $need_init_arr = array(
                    'id' => $newid,
                    'name' => $name,
                    'mobile' => substr($linkman->mobile, 0, 3) . '********',
                    'wxuserid' => $newWxUser->id,
                    'userid' => $newUser->id,
                    'patientid' => $newPatient->id);
                $newlinkman = $this->createByEntity($linkman, $need_init_arr);
            }
            echo "\n==[Linkman][{$id}]===\n";
        }
    }

    private function createByEntity ($entity, $need_init_arr = array()) {
        $C = get_class($entity);
        $keyArr = $C::getKeysDefine();
        $row = array();
        foreach ($keyArr as $key) {
            $row[$key] = $entity->$key;
        }

        $row['createtime'] = $entity->createtime;
        $row['updatetime'] = $entity->updatetime;

        if(isset($entity->content)){
            $content = $this->getContentFix($entity->content, $patient, $doctor);
            $row['content'] = $content;
        }

        if(isset($entity->remark)){
            $remark = $this->getContentFix($entity->remark, $patient, $doctor);
            $row['remark'] = $remark;
        }

        if(isset($entity->doctorid)){
            $row['doctorid'] = $this->copyfordoctorid;
        }

        $need_init_arr += $row;

        /*
         * foreach ($need_init_arr as $key => $value) { $row[$key] = $value; }
         */

        return $C::createByBiz($need_init_arr);
    }

    private function createNewPatient ($patient) {
        $name = $this->getPrivacyName($patient->name);
        $mother_name = $this->getPrivacyName($user->mother_name);
        $need_init_arr = array(
            'doctorid' => $this->copyfordoctorid,
            'name' => $name,
            'mother_name' => $mother_name,
            'clone_by_patientid' => $patient->id
             );
        return $this->createByEntity($patient, $need_init_arr);
    }

    private function createNewPcard ($newPatient, $pcard) {
        $patient_name = $this->getPrivacyName($pcard->patient_name);
        $need_init_arr = array(
            'patientid' => $newPatient->id,
            'doctorid' => $this->copyfordoctorid,
            'patient_name' => $patient_name,
             );
        return $this->createByEntity($pcard, $need_init_arr);
    }

    private function createNewUser ($newPatient, $user) {
        $name = $this->getPrivacyName($user->name);
        $unionid = "clone_" . $this->getRandStr(22);
        $need_init_arr = array(
            'xcode' => XCode::getNextCode("userxcode"),
            'unionid' => $unionid,
            'patientid' => $newPatient->id,
            'mobile' => substr($user->mobile, 0, 3) . '********',
            'name' => $name);

        $newUser = $this->createByEntity($user, $need_init_arr);
        // 重置createuserid
        $newPatient->createuserid = $newUser->id;
        return $newUser;
    }

    private function createNewWxUser ($newUser, $wxuser) {
        $nickname = $this->getPrivacyName($wxuser->nickname);
        $openid = "clone_" . $this->getRandStr(22);
        $need_init_arr = array(
            'userid' => $newUser->id,
            'openid' => $openid,
            'unionid' => $newUser->unionid,
            'wx_ref_code' => 'ADHD_bjtest_fangcun',
            'nickname' => $nickname,
            'doctorid' => $this->copyfordoctorid);
        return $this->createByEntity($wxuser, $need_init_arr);
    }

    private function getRandStr ($len = 30) {
        $t = "";
        $str = '1234567890abcdefghijklmnopqrstuvwxyz';
        for ($i = 0; $i < $len; $i ++) {
            $j = rand(0, 35);
            $t .= $str[$j];
        }
        return $t;
    }

    private function getPrivacyName ($name) {
        if (empty($name)) {
            return "***";
        }
        $nameFirst = mb_substr($name, 0, 1, 'utf-8');
        $len = mb_strlen($name);
        $nameSecond = "";
        for ($i = 0; $i < $len - 1; $i ++) {
            $nameSecond .= "*";
        }
        return $nameFirst . $nameSecond;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][copypatientdata.php]=====");

$process = new Copypatientdata();
$process->dowork();

Debug::trace("=====[cron][end][copypatientdata.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
