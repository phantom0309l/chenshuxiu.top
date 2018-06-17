<?php
// XPatientIndex
// 患者索引词

// owner by xuzhe
// create by xuzhe
// review by sjp 20160628
class XPatientIndex extends Entity
{

    // 不需要记录xobjlog
    public function notXObjLog () {
        return true;
    }

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'word',  // 关键词
                'type',  // 关键词类型名:name, pinyin, py, mobile, out_case_no, patientcardno, patientcard_id, bingan_no
            'patientid',  // patientid
            'refresh_time');
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
    }

    // $row = array();
    // $row["word"] = $word;
    // $row["type"] = $type;
    // $row["patientid"] = $patientid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "XPatientIndex::createByBiz row cannot empty");

        $entity = XPatientIndexDao::getByPatientidWord($row["patientid"], $row["word"]);

        // 重复刷新
        if ($entity instanceof XPatientIndex) {
            $entity->refresh_time = date("Y-m-d H:i:s");
            return $entity;
        }

        if (empty($row['word'])) {
            return null;
        }

        $default = array();
        $default["word"] = '';
        $default["type"] = '';
        $default["patientid"] = 0;
        $default["refresh_time"] = date("Y-m-d H:i:s");

        $row += $default;
        return new self($row);
    }

    // 创建对象
    public static function createByWord ($word, $type, Patient $patient) {
        if (! $word) {
            return;
        }

        $row = [];
        $row['word'] = $word;
        $row['type'] = $type;
        $row['patientid'] = $patient->id;

        return self::createByBiz($row);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    // 判断是否全是中文（中文名字）
    public static function isChineseName ($name) {
        if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $name)>0) {
            return true;
        } else {
            return false;
        }
    }

    // 判断是否全是英文
    public static function isEnglishName ($name) {
        if(preg_match("/^[a-zA-Z\s]+$/",$name)){
            return true;
        } else {
            return false;
        }
    }

    // 切割名字
    public static function cutName ($name) {
        $ch_arr = [];
        $str_len = mb_strlen($name);
        for ($i = 0; $i < $str_len; $i++) {
            $ch_arr[] = mb_substr($name, $i, 1);
        }

        $str_arr = [];
        for ($i = 0; $i < $str_len; $i++) {
            $str_arr[] = $ch_arr[$i];

            for ($m = $i + 1; $m < $str_len; $m++) {
                $str = $ch_arr[$i];
                for ($j = $i + 1; $j <= $m; $j++) {
                    $str .= $ch_arr[$j];
                }
                $str_arr[] = $str;
            }
        }

        return $str_arr;
    }

    // 更新 XPatientIndex 非name
    public static function updateXPatientIndexNotName ($word, $type, Patient $patient) {
        if ($type == 'name') {
            return;
        }

        self::createByWord($word, $type, $patient);
    }

    // 更新 XPatientIndex 为name
    public static function updateXPatientIndexName ($word, Patient $patient) {
        $ch_arr = self::cutName($word);
        foreach ($ch_arr as $word) {
            self::createByWord($word, 'name', $patient);
            self::createByWord(PinyinUtilNew::Word2PY($word, ''), 'pinyin', $patient);
            self::createByWord(strtolower(PinyinUtilNew::Word2PY($word)), 'py', $patient);
        }
    }

    // 更新 单患者 全部
    public static function updateAllXPatientIndex (Patient $patient) {
        $time1 = time();

        // 创建新的或刷新
        self::createAllXPatientIndex($patient);

        // 获取数据库里的
        $xpatientindexs = XPatientIndexDao::getListByPatientid($patient->id);

        // 删掉过期的
        foreach ($xpatientindexs as $xpatientindex) {
            // 号码的特殊处理
            if ($xpatientindex->type == 'mobile') {
                continue;
            }

            $time2 = strtotime($xpatientindex->refresh_time);

            if ($time2 < $time1) {
                $xpatientindex->remove();
            }
        }
    }

    public static function createAllXPatientIndex (Patient $patient) {

        // name
        self::updateXPatientIndexName($patient->name, $patient);

        // not name
        $list = [];
        $list[] = ['type' => 'prcrid', 'value' => $patient->prcrid];
        $pcards = PcardDao::getListByPatient($patient);
        foreach ($pcards as $pcard) {
            $list[] = ['type' => 'out_case_no', 'value' => $pcard->out_case_no];
            $list[] = ['type' => 'patientcardno', 'value' => $pcard->patientcardno];
            $list[] = ['type' => 'patientcard_id', 'value' => $pcard->patientcard_id];
            $list[] = ['type' => 'bingan_no', 'value' => $pcard->bingan_no];
        }

        foreach ($list as $a) {
            self::updateXPatientIndexNotName($a['value'], $a['type'], $patient);
        }
    }

    // 删除号码的xpatientindex
    public static function deleteXpatientIndexMobile (Patient $patient, $mobile) {
        $xpatientindex = XPatientIndexDao::getByPatientidTypeWord($patient->id, 'mobile', $mobile);
        if ($xpatientindex instanceof XPatientIndex) {
            $xpatientindex->remove();
        }

        $mobile4 = substr($mobile, - 4);
        $xpatientindex = XPatientIndexDao::getByPatientidTypeWord($patient->id, 'mobile', $mobile4);
        if ($xpatientindex instanceof XPatientIndex) {
            $xpatientindex->remove();
        }
    }

    // 修改号码的xpatientindex的patientid
    public static function updateXpatientIndexMobilePatientid ($from_patientid, $to_patientid, $mobile) {
        $xpatientindex = XPatientIndexDao::getByPatientidTypeWord($from_patientid, 'mobile', $mobile);
        if ($xpatientindex instanceof XPatientIndex) {
            $xpatientindex->set4lock('patientid', $to_patientid);
        }
        $mobile4 = substr($mobile, - 4);
        $xpatientindex = XPatientIndexDao::getByPatientidTypeWord($from_patientid, 'mobile', $mobile4);
        if ($xpatientindex instanceof XPatientIndex) {
            $xpatientindex->set4lock('patientid', $to_patientid);
        }
    }

    public static function addXPatientIndexMobile (Patient $patient, $mobile) {
        if (empty($mobile)) {
            return;
        }

        $mobile4 = substr($mobile, -4);

        $row = [];
        $row["word"] = $mobile;
        $row["type"] = 'mobile';
        $row["patientid"] = $patient->id;
        XPatientIndex::createByBiz($row);

        $row = [];
        $row["word"] = $mobile4;
        $row["type"] = 'mobile';
        $row["patientid"] = $patient->id;
        XPatientIndex::createByBiz($row);
    }

    // 检测索引是否有常量
    public static function isEqual ($word) {
        $sql = "select count(*)
            from xpatientindexs
            where word = :word ";
        $bind = [
            ':word' => $word
        ];

        $cnt = Dao::queryValue($sql, $bind);

        return $cnt > 0;
    }

    // 检测索引是否可以左like
    public static function isLeftLike ($word) {
        $sql = "select count(*)
            from xpatientindexs
            where word like :word ";
        $bind = [
            ':word' => "{$word}%"
        ];

        $cnt = Dao::queryValue($sql, $bind);

        return $cnt > 0;
    }
}
