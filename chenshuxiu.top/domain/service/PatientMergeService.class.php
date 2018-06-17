<?php

class PatientMergeService
{
    public static function mergeImp(User $from_user, Patient $to_patient, Auditor $myauditor){
        $from_patient = $from_user->patient;
        DBC::requireNotEmpty($from_patient, "from_patient");

        $from_doctorid = $from_patient->doctorid;
        $to_doctorid = $to_patient->doctorid;

        $from_diseaseid = $from_patient->diseaseid;
        $to_diseaseid = $to_patient->diseaseid;

        if( ($from_doctorid != $to_doctorid) && ($from_diseaseid != $to_diseaseid) ){
            Debug::warn("auditor[{$myauditor->name}]userid[{$from_user->id}], 医生和疾病均不匹配，不能够合并");
            return false;
        }

        $users = $from_patient->getUsers();
        if( count($users) > 1 ){
            //拆的逻辑
            $create_user = $from_patient->createuser;
            if($create_user->id == $from_user->id){
                Debug::warn("auditor[{$myauditor->name}]userid[{$from_user->id}], 源user不能再改变patientid");
                return false;
            }
            Debug::warn("拆分患者需要执行[dbfix_wxuserid_userid_patientid]脚本");
            self::mergeNeedDoBase($from_user, $to_patient);

            //pcard拆分逻辑
            $the_patient = PatientDao::getByCreateuserid($from_user->id);

            if($the_patient instanceof Patient){
                $the_pcards = PcardDao::getListByCreatePatient($the_patient);
                foreach($the_pcards as $the_pcard){
                    $to_the_pcard = PcardDao::getByPatientidDoctorid($to_patient->id, $the_pcard->doctorid);
                    if(false == $to_the_pcard instanceof Pcard){
                        $the_pcard->fixPatientId($to_patient->id);
                    }
                }
            }else{
                Debug::warn("通过from_user[{$from_user->id}],未找到patient");
            }
        }else{
            //合的逻辑
            self::mergeNeedDoBase($from_user, $to_patient);
            // 下线
            PatientStatusService::auditor_offline($from_patient, $myauditor);

            self::pcardPatientidFix($from_patient, $to_patient);

            // 修改各个表中patientid
            self::replace_patientid($from_patient, $to_patient);
        }

        // 下面代码通过$to_patient取找users;
        // 因为from_user的patientid刚赋值to_patientid，需要手动提交工作单元;
        BeanFinder::get("UnitOfWork")->commitAndInit();
        self::joinWxGroup($to_patient);
        return true;
    }

    private static function mergeNeedDoBase($from_user, $to_patient){
        $from_patient = $from_user->patient;
        // 修正 from_user->patientid, 该函数里面也修了wxuser上的patient
        $from_user->fixPatientId($to_patient->id);
        if ($from_patient->subscribe_cnt > 0) {
            $from_patient->subscribe_cnt --;
        }

        if ($from_patient->wxuser_cnt > 0) {
            $from_patient->wxuser_cnt --;
        }
        $to_patient->subscribe_cnt ++;
        $to_patient->wxuser_cnt ++;

        // 将from_patient的联系人修改为to_patient的联系人
        $from_linkmans = LinkmanDao::getListByUseridPatientid($from_user->id, $from_patient->id);
        foreach ($from_linkmans as $linkman) {
            $linkman->set4lock('patientid', $to_patient->id);

            // 修改mobile对应的Xpatientindex的patientid
            XPatientIndex::updateXpatientIndexMobilePatientid($from_patient->id, $to_patient->id, $linkman->mobile);
        }

        // 徐雁后台录入的数据,需要修正createuserid
        if ($to_patient->createuserid == 0) {
            $to_patient->createuserid = $from_user->id;
        }
    }

    private static function pcardPatientidFix($from_patient, $to_patient){
        //if ($from_patient->doctorid != $to_patient->doctorid) {
            $from_pcards = PcardDao::getListByPatient($from_patient);
            $to_pcards = PcardDao::getListByPatient($to_patient);

            foreach ($from_pcards as $from_pcard) {
                if (self::hasSameDoctorid($from_pcard, $to_pcards)) {
                    continue;
                }

                // 修正 pcard->patientid
                $from_pcard->fixPatientId($to_patient->id);
                $from_pcard->auditremark .= "\n患者合并{$from_patient->id}to{$to_patient->id}";
            }
        //}
    }

    private static function hasSameDoctorid ($from_pcard, $to_pcards) {
        foreach ($to_pcards as $to_pcard) {
            if ($from_pcard->doctorid == $to_pcard->doctorid) {
                return true;
            }
        }
        return false;
    }

    // 修改各个表中patientid
    // $findp 寻找 $replacep 替换
    private static function replace_patientid (Patient $findp, Patient $replacep) {
        $find_patientid = $findp->id;
        $replace_patientid = $replacep->id;

        // 忽略
        $ignore_table_arr = array(
            'wxusers', // 由明确代码处理
            'patientpgrouprefs',  // 由其他逻辑修改
            'users',  // 由明确代码处理
            'pcards',  // 不能动
            'pcardhistorys',  // 不能动
            'xpatientindexs'); // 不能动

        $revisitrecords_f = RevisitRecordDao::getListByPatientid($find_patientid);
        $revisitrecords_r = RevisitRecordDao::getListByPatientid($replace_patientid);

        $thedate_f = array();
        $thedate_r = array();

        foreach ($revisitrecords_f as $a) {
            $thedate_f[] = $a->thedate;
        }

        foreach ($revisitrecords_r as $a) {
            $thedate_r[] = $a->thedate;
        }

        $thedate_t = array_intersect($thedate_r, $thedate_f);

        if (false == empty($thedate_t)) {
            $ignore_table_arr[] = 'revisitrecords';
        }

        $sql = "show tables";
        $tables = Dao::queryValues($sql, []);

        $tables_patientid = array();
        $tables_objid = array();

        foreach ($tables as $table) {
            if (in_array($table, $ignore_table_arr)) {
                continue;
            }

            $sql = "show full fields from {$table}";

            $fields = Dao::queryRows($sql, []);

            foreach ($fields as $field) {
                if ($field['field'] == 'patientid') {
                    $tables_patientid[] = $table;
                }
                if ($field['field'] == 'objid') {
                    $tables_objid[] = $table;
                }
            }
        }

        // 修改 find_patientid => replace_patientid
        foreach ($tables_patientid as $tablename) {
            $entityType = self::table2entityType($tablename);
            if (null == $entityType) {
                continue;
            }

            $cond = " and patientid=:patientid ";
            $bind = array(
                ':patientid' => $find_patientid);

            $entitys = Dao::getEntityListByCond($entityType, $cond, $bind);

            foreach ($entitys as $entity) {
                $entity->set4lock('patientid', $replace_patientid);
            }
        }

        // objtype = Patient , 修正 objid
        foreach ($tables_objid as $tablename) {
            $entityType = self::table2entityType($tablename);
            if (null == $entityType) {
                continue;
            }

            $cond = " and objtype=:objtype and objid=:objid ";
            $bind = array(
                ':objtype' => 'Patient',
                ':objid' => $find_patientid);

            $entitys = Dao::getEntityListByCond($entityType, $cond, $bind);

            foreach ($entitys as $entity) {
                $entity->set4lock('objid', $replace_patientid);
            }
        }
    }

    private static function table2entityType ($table) {
        global $lowerclasspath;

        $tabl = substr($table, 0, strlen($table) - 1);
        return $lowerclasspath[$tabl];
    }

    // 加入对应分组
    private static function joinWxGroup ($patient) {
        $wxusers = $patient->getWxUsers();
        foreach ($wxusers as $wxuser) {
            // 方寸儿童管理服务平台，非礼来患者加入开药门诊分组
            if (1 == $patient->diseaseid && false == $patient->doctor->isHezuo("Lilly")) {
                $wxuser->joinWxGroupOfADHD();
                continue;
            }
            // 肺癌，胃癌，癌症wxuser报到后加入:报到后的微信组
            $wxuser->joinWxGroup("baodao_after");
        }
    }

}
