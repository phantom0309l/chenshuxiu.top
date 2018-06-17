<?php

/*
 * Pcard, 就诊卡
 */
class Pcard extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'last_scan_time',  // 最后一次扫此pcard医生二维码的时间
            'patientid',  // patientid
            'doctorid',  // doctorid
            'diseaseid',  // diseaseid
            'diseasename_show',  // 文本字段，运营给患者添加疾病（咱们没有的疾病），仅仅用于显示
            'patient_name',  // patient_name
            'groupstr4doctor',  // 基于医生的患者分组,钱英
            'create_doc_date',  // 建档日期
            'out_case_no',  // 院内病历号
            'patientcardno',  // 院内就诊卡号
            'patientcard_id',  // 院内患者ID(就诊卡上)
            'bingan_no',  // 院内病案号(冯伟非让加的)
            'fee_type',  // 费用类型
            'scientific_no',  // 科研编号
            'complication',  // 诊断
            'first_happen_date',  // 首发时间
            'first_visit_date',  // 首次就诊时间
            'last_incidence_date',  // 上次发病日期
            'has_update',  // 有更新
            'lastpipeid',  // 最后流id
            'lastpipe_createtime',  // 最后一次用户行为时间
            'send_pmsheet_status',  // 患者核对用药情况
            'next_pmsheet_time',  // 预定发送时间
            'status',  // 就诊卡状态
            'auditstatus',  // 审核状态
            'auditorid',  // auditorid
            'auditremark',  // 审核备注
            'audittime',  // 审核通过时间
            'create_patientid',  // create_patientid
            'remark_doctor'); // 医生备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid',
            'doctorid',
            'diseaseid',
            'lastpipeid',
            'auditorid',
            'create_patientid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");

        $this->_belongtos["lastpipe"] = array(
            "type" => "Pipe",
            "key" => "lastpipeid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
        $this->_belongtos["create_patient"] = array(
            "type" => "Patient",
            "key" => "create_patientid");
    }

    // $row = array();
    // $row["last_scan_time"] = XDateTime::now();
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["diseaseid"] = $diseaseid;
    // $row["diseasename_show"] = $diseasename_show;
    // $row["patient_name"] = $patient_name;
    // $row["groupstr4doctor"] = $groupstr4doctor;
    // $row["create_doc_date"] = $create_doc_date;
    // $row["out_case_no"] = $out_case_no;
    // $row["patientcardno"] = $patientcardno;
    // $row["patientcard_id"] = $patientcard_id;
    // $row["bingan_no"] = $bingan_no;
    // $row["fee_type"] = $fee_type;
    // $row["scientific_no"] = $scientific_no;
    // $row["complication"] = $complication;
    // $row["first_happen_date"] = $first_happen_date;
    // $row["first_visit_date"] = $first_visit_date;
    // $row["last_incidence_date"] = $last_incidence_date;
    // $row["has_update"] = $has_update;
    // $row["lastpipeid"] = $lastpipeid;
    // $row["lastpipe_createtime"] = $lastpipe_createtime;
    // $row["send_pmsheet_status"] = $send_pmsheet_status;
    // $row["next_pmsheet_time"] = $next_pmsheet_time;
    // $row["status"] = $status;
    // $row["auditstatus"] = $auditstatus;
    // $row["auditorid"] = $auditorid;
    // $row["auditremark"] = $auditremark;
    // $row["audittime"] = $audittime;
    // $row["create_patientid"] = $create_patientid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Pcard::createByBiz row cannot empty");

        $default = array();
        $default["last_scan_time"] = '0000-00-00 00:00:00';
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["diseaseid"] = 0;
        $default["diseasename_show"] = '';
        $default["patient_name"] = '';
        $default["groupstr4doctor"] = '';
        $default["create_doc_date"] = '0000-00-00';
        $default["out_case_no"] = '';
        $default["patientcardno"] = '';
        $default['patientcard_id'] = '';
        $default['bingan_no'] = '';
        $default["fee_type"] = '';
        $default["scientific_no"] = '';
        $default["complication"] = '';
        $default["first_happen_date"] = '0000-00-00';
        $default["first_visit_date"] = '0000-00-00';
        $default["last_incidence_date"] = '0000-00-00';
        $default["has_update"] = 0;
        $default["lastpipeid"] = 0;
        $default["lastpipe_createtime"] = '0000-00-00';
        $default["send_pmsheet_status"] = 0;
        $default["next_pmsheet_time"] = '0000-00-00 00:00:00';
        $default["status"] = 1;
        $default["auditstatus"] = 1;
        $default["auditorid"] = 0;
        $default["auditremark"] = '初始化';
        $default["audittime"] = '';
        $default["create_patientid"] = 0;
        $default["remark_doctor"] = '';

        $row += $default;
        $pcard = new self($row);

        // #4469 姜玉良(1410)，要能看到徐飞(1292)和彭冉(1293)入的患者,
        if (in_array($pcard->doctorid, [1292,1293])) {
            $jiangyuliang_pcard = PcardDao::getByPatientidDoctorid($pcard->patientid, 1410);
            if (false == $jiangyuliang_pcard instanceof Pcard) {
                // 创建就诊卡
                $row = array();
                $row["last_scan_time"] = $pcard->last_scan_time;
                $row["create_patientid"] = $pcard->patientid;
                $row["patientid"] = $pcard->patientid;
                $row["doctorid"] = 1410;
                $row["diseaseid"] = $pcard->diseaseid;
                $row["patient_name"] = $pcard->patient->name;
                $row["out_case_no"] = $pcard->out_case_no;
                $row["patientcardno"] = $pcard->patientcardno;
                $row["patientcard_id"] = $pcard->patientcard_id;
                $row["bingan_no"] = $pcard->bingan_no;
                $row["complication"] = $pcard->complication;
                $row["has_update"] = 1;
                $newpcard = Pcard::createByBiz($row);
            }
        }

        self::pushCache($row['patientid'], $pcard);
        return $pcard;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 修正 pcard->patientid
    public function fixPatientId ($patientid) {
        if (empty($patientid)) {
            $patientid = 0;
        }
        $this->set4lock('patientid', $patientid);
    }

    // pcardKvInfo4ipad
    public function pcardKvInfo4ipad () {
        $arr = $this->pcardKvInfo();
        return $arr;
    }

    public function getValueFix ($name) {
        $func_name = "get" . $name;
        if (method_exists($this, $func_name)) {
            return $this->{$func_name}();
        } else {
            return $this->$name;
        }
    }

    // pcardKvInfo
    public function pcardKvInfo () {
        $arr = array();

        // $arr[] = array(
        // 'title' => '诊断',
        // 'k' => 'complication',
        // 'v' => $this->getLastComplication(),
        // 'type' => 'input',
        // 'option' => array());

        $arr[] = array(
            'title' => '院内病历号',
            'k' => 'out_case_no',
            'v' => $this->out_case_no,
            'type' => 'input',
            'option' => array());

        $arr[] = array(
            'title' => '院内就诊卡号',
            'k' => 'patientcardno',
            'v' => $this->patientcardno,
            'type' => 'input',
            'option' => array());

        $arr[] = array(
            'title' => '院内患者ID',
            'k' => 'patientcard_id',
            'v' => $this->patientcard_id,
            'type' => 'input',
            'option' => array());

        $arr[] = array(
            'title' => '院内病案号',
            'k' => 'bingan_no',
            'v' => $this->bingan_no,
            'type' => 'input',
            'option' => array());

        $arr[] = array(
            'title' => '费用类型',
            'k' => 'fee_type',
            'v' => $this->fee_type,
            'type' => 'radio',
            'option' => array(
                "国家医保",
                "公费",
                "自费",
                "商业保险",
                "其他"));

        $arr[] = [
            'title' => '备注',
            'k' => 'remark_doctor',
            'v' => $this->remark_doctor,
            'type' => 'input',
            'option' => []];

        return $arr;
    }

    public function pcardKvInfoModify ($data) {
        $default = array();

        $default["complication"] = $this->complication;
        $default["out_case_no"] = $this->out_case_no;
        $default["patientcardno"] = $this->patientcardno;
        $default["patientcard_id"] = $this->patientcard_id;
        $default["bingan_no"] = $this->bingan_no;
        $default["fee_type"] = $this->fee_type;
        $default["remark_doctor"] = $this->remark_doctor;

        $data += $default;

        $this->complication = $data['complication'];
        $this->out_case_no = $data['out_case_no'];
        $this->patientcardno = $data['patientcardno'];
        $this->patientcard_id = $data['patientcard_id'];
        $this->bingan_no = $data['bingan_no'];
        $this->fee_type = $data['fee_type'];
        $this->remark_doctor = $data['remark_doctor'];
    }

    // 得到所有持有此pcard的wxuser
    public function getWxUsers () {
        return WxUserDao::getListByPcard($this);
    }

    public function tktinformation () {
        $arr = array();

        $arr[] = array(
            'k' => 'complication',
            'title' => '诊断',
            'v' => $this->complication,
            'type' => 'input',
            'option' => array());

        $arr[] = array(
            'k' => 'out_case_no',
            'title' => '病历号',
            'v' => $this->out_case_no,
            'type' => 'input',
            'option' => array());

        $arr[] = array(
            'k' => 'patientcardno',
            'title' => '就诊卡号',
            'v' => $this->patientcardno,
            'type' => 'input',
            'option' => array());

        $arr[] = array(
            'k' => 'patientcard_id',
            'title' => '患者ID',
            'v' => $this->patientcard_id,
            'type' => 'input',
            'option' => array());

        $arr[] = array(
            'k' => 'bingan_no',
            'title' => '病案号',
            'v' => $this->bingan_no,
            'type' => 'input',
            'option' => array());

        return $arr;
    }

    public function tktinformationmodify ($data) {
        $default = array();

        $default["complication"] = $this->complication;
        $default["out_case_no"] = $this->out_case_no;
        $default["patientcardno"] = $this->patientcardno;
        $default["patientcard_id"] = $this->patientcard_id;
        $default["bingan_no"] = $this->bingan_no;

        $data += $default;

        $this->complication = $data['complication'];
        $this->out_case_no = $data['out_case_no'];
        $this->patientcardno = $data['patientcardno'];
        $this->patientcard_id = $data['patientcard_id'];
        $this->bingan_no = $data['bingan_no'];
    }

    // 院内识别ID
    public function getYuanNeiStr ($separator = ',') {
        $str = "";

        if ($this->out_case_no) {
            $str .= "病历号: {$this->out_case_no}";
        }

        $str = trim($str);

        if ($this->patientcardno) {

            if ($str) {
                $str .= " {$separator} ";
            }

            $str .= "就诊卡: {$this->patientcardno}";
        }

        $str = trim($str);

        if ($this->patientcard_id) {
            if ($str) {
                $str .= " {$separator} ";
            }

            $str .= "患者ID: {$this->patientcard_id}";
        }

        $str = trim($str);

        if ($this->bingan_no) {
            if ($str) {
                $str .= " {$separator} ";
            }

            $str .= "病案号: {$this->bingan_no}";
        }

        return "院内识别ID[ {$str} ]";
    }

    // 获取病程：现在时间-首发时间
    public function getBingcheng () {
        $today = date('Y-m-d');

        if ($this->getFirstHappenDate() != '0000-00-00' && $this->getFirstHappenDate() != '') {

            $dateStr = FUtil::getYmdBetween2Date($this->getFirstHappenDate(), $today);

            // $year = floor($months / 12);
            // $month = $months % 12;

            // $dateStr = "";
            // if($year > 0){
            // $dateStr .= "{$year}年";
            // }
            // $dateStr .= "{$month}月";

            return $dateStr;
        } else {
            return '未知';
        }
    }

    public function getFirstHappenDate () {
        $cond = " and patientid = :patientid AND checkuptplid = :checkuptplid
        ORDER BY check_date ASC
        LIMIT 1 ";

        $bind = [];
        $bind[':patientid'] = $this->patientid;
        $bind[':checkuptplid'] = 106312063;

        $checkup = Dao::getEntityByCond("Checkup", $cond, $bind);

        if ($checkup instanceof Checkup) {
            return $checkup->check_date;
        }

        return $this->getCol('first_happen_date');
    }

    public function getLastComplication () {
        if ($this->doctorid == 33) {
            $sql = "select if(a.content != '' and (c.content = '其它' or c.content = '其他'), a.content, c.content)
            from xanswers a
            inner join xquestions d on d.id = a.xquestionid
            left join xansweroptionrefs b on b.xanswerid = a.id
            left join xoptions c on c.id = b.xoptionid
            inner join (
                select distinct t1.*
                from checkups t1
                inner join checkuptpls t2 on t2.id = t1.checkuptplid
                where t2.doctorid = :doctorid and t2.ename = :ename and t1.patientid = :patientid
            )tt on tt.xanswersheetid = a.xanswersheetid
            where d.content = :content and c.content <> ''
            order by tt.check_date desc
            limit 1 ";

            $bind = [];
            $bind[':doctorid'] = $this->doctorid;
            $bind[':patientid'] = $this->patientid;
            $bind[':ename'] = 'zhenduan';
            $bind[':content'] = '诊断';

            $complication = Dao::queryValue($sql, $bind);
            if ($complication) {
                $this->complication = $complication;

                return $complication;
            } else {
                return $this->complication;
            }
        } else {
            return $this->complication;
        }
    }

    public function getSelectedComplicationArr () {
        $arr = array();
        $complication = trim($this->complication);
        if (! empty($complication)) {
            $arr = explode(" ", $complication);
        }
        return $arr;
    }

    // 患者上次发作时间距今天的时间
    public function getDescStrOfLast_incidence_date2Today ($hide_day = false) {
        $last_incidence_date = $this->last_incidence_date;

        if ($last_incidence_date == '0000-00-00') {
            return '未知';
        }

        $today = date('Y-m-d');

        $descstr = '';
        $yearSpanNum = XDateTime::getYearSpan($last_incidence_date, $today);
        if ($yearSpanNum > 0) {
            $descstr .= " $yearSpanNum 年 ";
        }

        $monthSpanNum = XDateTime::getMonthSpan($last_incidence_date, $today);
        $monthSpanNum_mod = $monthSpanNum % 12;
        if ($monthSpanNum_mod > 0) {
            $descstr .= " $monthSpanNum_mod 个月 ";
        }

        if (! $hide_day) {
            $daySpanNum = XDateTime::getDaySpan($last_incidence_date, $today);
            $daySpanNum_mod = $daySpanNum % 30;
            if ($daySpanNum_mod > 0 && $yearSpanNum == 0) {
                $descstr .= " $daySpanNum_mod 天 ";
            }
        }

        if (empty($descstr)) {
            $descstr = '不到一个月';
        }

        return $descstr;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 内存缓存
    private static $_pcardCache = array();

    // 从内存缓存中获取
    public static function getFromCache ($patientid) {
        $key = "{$patientid}";
        if (isset(self::$_pcardCache[$key])) {
            return self::$_pcardCache[$key];
        } else {
            return false;
        }
    }

    // 压人内存缓存
    private static function pushCache ($patientid, Pcard $pcard) {
        $key = "{$patientid}";
        self::$_pcardCache[$key] = $pcard;
    }
}
