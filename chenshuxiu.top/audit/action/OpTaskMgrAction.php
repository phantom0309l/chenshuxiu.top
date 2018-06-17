<?php

class OpTaskMgrAction extends AuditBaseAction
{
    
    // 运营任务列表之过滤器版
    public function doListNew() {
        $patientid = XRequest::getValue("patientid", 0);
        $word = XRequest::getValue("patient_name", '');
        $pagesize = XRequest::getValue("pagesize", 50);
        
        // ----- ----- 获取前端需要的参数 ----- -----
        // 获取Optasktpls
        $optasktpls = OpTaskTplDao::getList();
        
        // 获取optaskfilters
        $public_optaskfilters = OpTaskFilterDao::getPublicList();
        $private_optaskfilters = OpTaskFilterDao::getPrivateListByCreateauditorid($this->myauditor->id);
        
        // 获取筛选分类
        $arr_filter = PipeTplService::getArrForFilter();
        
        XContext::setValue("optasktpls", $optasktpls);
        XContext::setValue("myauditor", $this->myauditor);
        XContext::setValue("mydisease", $this->mydisease);
        
        XContext::setValue("public_optaskfilters", $public_optaskfilters);
        XContext::setValue("private_optaskfilters", $private_optaskfilters);
        XContext::setValue("arr_filter", $arr_filter);
        
        // ----- ----- 获取前端需要的参数 ----- -----
        
        // 搜索
        if ($word) {
            $optasks = $this->search($patientid, $word);
            $optasks = $this->mergeOptasks($optasks, $pagesize * 10);
            XContext::setValue('optasks', $optasks);
            return self::SUCCESS;
        }
        
        $optaskfilterid = XRequest::getValue('optaskfilterid', 0);
        $optaskfilter = OpTaskFilter::getById($optaskfilterid);
        if ($optaskfilter instanceof OpTaskFilter) {
            if ($optaskfilter->is_public == 0 && $optaskfilter->create_auditorid != $this->myauditor->id) {
                // 别人私有的过滤器不能使用
                $optaskfilter = OpTaskFilterService::getOneByCreate_auditorid($this->myauditor->id);
            }
        } else {
            $optaskfilters = OpTaskFilterDao::getListByCreateauditorid($this->myauditor->id);
            
            $optaskfilter = OpTaskFilterService::getOneByCreate_auditorid($this->myauditor->id);
            
            // 运营首次进入的时候，不查询，给他一个空白页，让他们自己配
            if (count($optaskfilters) == 0) {
                $private_optaskfilters = [];
                $private_optaskfilters[] = $optaskfilter;
                
                return self::SUCCESS;
            }
        }
        
        $configs = json_decode($optaskfilter->filter_json, true);
        
        // 负责人
        $myauditor = $this->myauditor;
        $auditorid = $auditorid = $configs['auditor'][0] ?? 0;
        
        // [--我--]
        if ($auditorid == -2) {
            $auditorid = $myauditor->id;
        }
        
        $optasks = [];
        
        // [我+未分配] 或 [全部]
        if ($auditorid == -1 || $auditorid == -3) {
            // 先查自己的
            $optasks = $this->listnewfilter($optaskfilter, $myauditor->id);
            
            // 数目不够, 查未分配的 或 全部
            if (count($optasks) < $pagesize) {
                
                // 未分配
                if ($auditorid == -1) {
                    $auditorid = 0;
                }
                
                $optasks_0 = $this->listnewfilter($optaskfilter, $auditorid);
                $optasks = array_merge($optasks, $optasks_0);
            }
        } else {
            // 指定责任人
            $optasks = $this->listnewfilter($optaskfilter, $auditorid);
        }
        
        // 任务合并
        $optasks = $this->mergeOptasks($optasks, $pagesize);
        XContext::setValue('optasks', $optasks);
        
        return self::SUCCESS;
    }
    
    // 相同患者的任务合并为一条
    private function mergeOptasks($optasks_100, $cnt = 50) {
        $patientids = [];
        $optasks = [];
        foreach ($optasks_100 as $a) {
            // 跳过相同患者的
            if (in_array($a->patientid, $patientids)) {
                continue;
            }
            
            $patientids[] = $a->patientid;
            $optasks[] = $a;
            
            // 只留10个用于处理
            if (count($patientids) >= $cnt) {
                break;
            }
        }
        return $optasks;
    }
    
    // 走搜索
    private function search($patientid, $word) {
        $pagesize = XRequest::getValue("pagesize", 50);
        $pagenum = XRequest::getValue("pagenum", 1);
        
        $condQuery = "";
        $bind = [];
        
        if ($patientid) {
            $condQuery .= " and xpi.patientid = :patientid ";
            $bind[':patientid'] = "{$patientid}";
        } else {
            if (XPatientIndex::isEqual($word)) {
                $condQuery .= " and xpi.word = :word ";
                $bind[':word'] = "{$word}";
            } else {
                $condQuery .= " and (xpi.word like :word or xpi.patientid = :patientid) ";
                $bind[':word'] = "{$word}%";
                $bind[':patientid'] = $word;
            }
        }
        
        $sql = "select *
        from optasks
        where patientid in (
        select xpi.patientid
        from xpatientindexs xpi
        inner join patients b on b.id = xpi.patientid
        where 1 = 1 {$condQuery} and ( b.status = 1 or (b.status = 0 and b.auditstatus = 0) or b.is_live = 0)
        )
        order by level desc, plantime asc, id asc
        ";
        
        $optasks = Dao::loadEntityList4Page("OpTask", $sql, $pagesize * 10, $pagenum, $bind);
        
        $countSql = "select count(distinct a.id) as optask_cnt, count(distinct a.patientid) as patient_cnt
            from optasks a
            inner join patients b on a.patientid = b.id
            inner join xpatientindexs xpi on xpi.patientid = b.id
            where 1 = 1 {$condQuery} and ( b.status = 1 or (b.status = 0 and b.auditstatus = 0) or b.is_live = 0) ";
        $row = Dao::queryRow($countSql, $bind);
        
        $cnt = $row['optask_cnt'];
        $url = "/optaskmgr/listnew?patient_name={$word}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize * 10, $url);
        
        XContext::setValue("patient_name", $word);
        XContext::setValue("optask_cnt", $row['optask_cnt']);
        XContext::setValue("patient_cnt", $row['patient_cnt']);
        XContext::setValue("pagelink", $pagelink);
        
        return $optasks;
    }
    
    // 走过滤器
    private function listnewfilter(OpTaskFilter $optaskfilter, $auditorid = 0) {
        $pagesize = XRequest::getValue("pagesize", 50);
        $pagenum = XRequest::getValue("pagenum", 1);
        
        $configs = json_decode($optaskfilter->filter_json, true);
        
        // optasks a; patients b
        $condOptask = "";
        $condPatient = "";
        $bind = [];
        
        // 只查询锁定
        if ($auditorid == -4) {
            $condPatient .= " and b.auditorid > 0 ";
        } elseif ($auditorid > -1) {
            $condPatient .= " and b.auditorid = :auditorid ";
            $bind[':auditorid'] = $auditorid;
        }
        
        // 管理计划
        if (false == empty($configs['mgtgrouptpl']) && $configs['mgtgrouptpl'][0] > -2) {
            $mgtgrouptplid = $configs['mgtgrouptpl'][0] == -1 ? 0 : $configs['mgtgrouptpl'][0];
            $condPatient .= " and b.mgtgrouptplid = :mgtgrouptplid ";
            $bind[':mgtgrouptplid'] = $mgtgrouptplid;
        }
        
        // 医生组
        if (false == empty($configs['doctorgroup'])) {
            if (false == in_array(-2, $configs['doctorgroup'])) {
                // 把-1转化为0
                $list = OpTaskFilterService::FixnegativeToZero($configs['doctorgroup']);
                $doctorgroupidstr = implode(',', $list);
                if ($doctorgroupidstr) {
                    $condPatient .= " and b.doctorid in (
                        select id
                        from doctors
                        where doctorgroupid in ({$doctorgroupidstr})
                    ) ";
                }
            }
        }
        
        // 医生
        if (false == empty($configs['doctor']) && $configs['doctor'][0] > -1) {
            $doctorid = $configs['doctor'][0];
            $condOptask .= " and a.doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }
        
        // 疾病
        $is_use_disease = 0;
        if (false == empty($configs['disease'])) {
            if (false == in_array(-2, $configs['disease'])) {
                $list = OpTaskFilterService::FixnegativeToZero($configs['disease']);
                $diseaseidstr = implode(',', $list);
                if ($diseaseidstr) {
                    $condPatient .= " and b.diseaseid in ({$diseaseidstr}) ";
                    Debug::trace("===========" . "  and diseaseid in ({$diseaseidstr}) ");
                    
                    $is_use_disease = 1;
                }
            }
        }
        
        if ($is_use_disease == 0) {
            // 疾病组,如何指定了疾病，疾病组失效
            if ($this->myauditor->diseasegroupid > 0) {
                $configs['diseasegroup'][0] = $this->myauditor->diseasegroupid;
            }
            if (false == empty($configs['diseasegroup']) && $configs['diseasegroup'][0] > 0) {
                $diseasegroupid = $configs['diseasegroup'][0];
                $condOptask .= " and a.diseaseid in (
                    select id
                    from diseases
                    where diseasegroupid = :diseasegroupid
                ) ";
                $bind[':diseasegroupid'] = $diseasegroupid;
            }
        }
        
        // 患者组
        if (false == empty($configs['patientgroup'])) {
            if (false == in_array(-2, $configs['patientgroup'])) {
                $list = OpTaskFilterService::FixnegativeToZero($configs['patientgroup']);
                $patientgroupidstr = implode(',', $list);
                if ($patientgroupidstr) {
                    $condPatient .= " and b.patientgroupid in ({$patientgroupidstr}) ";
                }
            }
        }
        
        // 患者阶段
        if (false == empty($configs['patientstage'])) {
            if (false == in_array(-2, $configs['patientstage'])) {
                $list = OpTaskFilterService::FixnegativeToZero($configs['patientstage']);
                $patientstageidstr = implode(',', $list);
                if ($patientstageidstr) {
                    $condPatient .= " and b.patientstageid in ({$patientstageidstr}) ";
                }
            }
        }
        
        // 任务类型
        if (false == empty($configs['optasktpl'])) {
            $optasktplidstr = implode(',', $configs['optasktpl']);
            if ($optasktplidstr) {
                $condOptask .= " and a.optasktplid in ({$optasktplidstr}) ";
            }
        }
        
        // 任务节点
        if (false == empty($configs['opnode'])) {
            $opnodeidstr = implode(',', $configs['opnode']);
            if ($opnodeidstr) {
                $condOptask .= " and a.opnodeid in ({$opnodeidstr}) ";
            }
        }
        
        // 计划时间
        $is_use_plantime = 0;
        if (false == empty($configs['plantime'])) {
            $plantimes = $configs['plantime'];
            if (false == in_array(-2, $configs['plantime'])) {
                if ($plantimes[0] == 1) {
                    // 今日任务
                    $tomorrow = date('Y-m-d', time() + 3600 * 24);
                    $condOptask .= " and a.plantime < '{$tomorrow}' ";
                    
                    $is_use_plantime = 1;
                } elseif ($plantimes[0] == 2) {
                    // 未来任务
                    $today = date('Y-m-d');
                    $condOptask .= " and a.plantime > '{$today}' ";
                    
                    $is_use_plantime = 1;
                }
            }
        }
        
        // 任务状态
        $condStatus = "";
        if (false == empty($configs['status'])) {
            if (!in_array(-2, $configs['status'])) {
                $list = OpTaskFilterService::FixnegativeToZero($configs['status']);
                $statusstr = implode(',', $list);
                $condStatus = " and a.status in ({$statusstr}) ";
            }
        }
        
        if ($is_use_plantime == 1) {
            if ($condStatus) {
                $condOptask .= $condStatus;
            } else {
                $condOptask .= " and a.status in (0, 1, 2) ";
            }
        } else {
            if ($condStatus) {
                $condOptask .= $condStatus;
            }
        }
        
        // 优先级
        if (false == empty($configs['level'])) {
            if (false == in_array(-2, $configs['level'])) {
                $levelstr = implode(',', $configs['level']);
                if ($levelstr) {
                    $condOptask .= " and a.level in ({$levelstr}) ";
                }
            }
        }
        
        // 报到时间
        if (false == empty($configs['baodaotime'])) {
            $start_daycnt = $configs['baodaotime'][0];
            $end_daycnt = $configs['baodaotime'][1];
            
            if ($start_daycnt > -1) {
                $start_createtime = date('Y-m-d', strtotime("-{$start_daycnt} days") + 3600 * 24);
                $condPatient .= " and b.createtime < :start_createtime ";
                $bind[':start_createtime'] = $start_createtime;
            }
            
            if ($end_daycnt > -1) {
                $end_createtime = date('Y-m-d', strtotime("-{$end_daycnt} days"));
                $condPatient .= " and b.createtime >= :end_createtime ";
                $bind[':end_createtime'] = $end_createtime;
            }
        }
        
        // 如果什么条件都不加，会很慢，所以强行加上一个条件
        if ($condOptask == '' && $condPatient == '') {
            $condOptask .= " and a.status = 0 ";
        }
        
        $condPatient .= " and b.is_see = 1 ";
        
        $sql = "select a.*
                from optasks a
                inner join patients b on b.id = a.patientid
                where 1 = 1 {$condOptask} {$condPatient} 
                order by a.level desc, a.plantime asc, b.level desc ";
        
        $optasks = Dao::loadEntityList4Page("OpTask", $sql, $pagesize, $pagenum, $bind);
        
        // 任务总数和患者总数
        $countSql = "select count(a.id) as optask_cnt, count(distinct a.patientid) as patient_cnt
                from optasks a
                inner join patients b on b.id = a.patientid
                where 1 = 1 {$condOptask} {$condPatient} ";
        $row = Dao::queryRow($countSql, $bind);
        
        $cnt = $row['optask_cnt'];
        $url = "/optaskmgr/listnew?optaskfilterid={$optaskfilter->id}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        
        XContext::setValue("optaskfilter", $optaskfilter);
        XContext::setValue("optask_cnt", $row['optask_cnt']);
        XContext::setValue("patient_cnt", $row['patient_cnt']);
        XContext::setValue("pagelink", $pagelink);
        
        return $optasks;
    }
    
    public function doListOfPatient() {
        $patientid = XRequest::getValue('patientid', 0);
        
        $patient = Patient::getById($patientid);
        $optasks = OpTaskDao::getListByPatient($patient, " order by status asc, donetime desc, id desc ");
        
        XContext::setValue("patient", $patient);
        XContext::setValue("optasks", $optasks);
        return self::SUCCESS;
    }
    
    // 修补任务, 没有任务的患者都 补一条 common : default_optasktpl
    public function doFixNotOpTaskPatientJson() {
        $sql = "select distinct a.id
            from patients a
            left join optasks b on b.patientid = a.id
            where b.id is null limit 500 ";
        $ids = Dao::queryValues($sql);
        
        $cnt = count($ids);
        
        // 为了提升效率, 先查模板 OpTaskTpl
        $optasktpl = OpTaskTplDao::getOneByUnicode('common:default_optasktpl');
        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            
            // 生成任务: 公共任务
            $arr = [];
            $arr['status'] = 1; // 任务状态直接置为关闭
            OpTaskService::createOpTaskByOpTaskTpl(null, $patient, null, $optasktpl, $patient, $plantime = '', $auditorid = 10013, $arr);
        }
        
        echo $cnt;
        if ($cnt > 499) {
            echo '条, 还有更多';
        }
        
        return self::BLANK;
    }
    
    // 患者详情
    public function doOnePatientHtml() {
        $patientid = XRequest::getValue('patientid', 0);
        
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, __METHOD__ . '患者不存在');
        $drugsheet_nearly2 = DrugSheetDao::getListByPatientid($patientid, " order by id desc limit 2");
        
        $shopOrders = ShopOrderDao::getIsPayShopOrdersByPatientType($patient, ShopOrder::type_chufang);
        // 量表
        $papers = PaperDao::getListByPatientid($patient->id);
        $papertpls = PaperTplDao::getListByDiseaseid($patient->diseaseid, "GROUP BY papertplid");
    
        if ($patient->diseaseid != 1) {
            // 住院
            $cond = " AND patientid=:patientid";
            $bind = [
                ':patientid' => $patient->id];
            $bedtkts = Dao::getEntityListByCond('BedTkt', $cond, $bind);
            XContext::setValue("bedtkts", $bedtkts);
            // 复诊
            $revisittkts = Dao::getEntityListByCond('RevisitTkt', $cond, $bind);
            XContext::setValue("revisittkts", $revisittkts);
        }
    
        // 自动锁定
        if ($this->myauditor->is_auto_lock_patient == 1 && $patient->auditorid < 1) {
            $patient->auditorid = $this->myauditor->id;
            $patient->auditor_lock_time = date('Y-m-d H:i:s');
        }
    
        $optasktpls = OpTaskTplDao::getList();
    
        // sunshu #5842
        $patienttodaymarktpls = PatientTodayMarkTplDao::getListByDiseasegroupid($patient->disease->diseasegroupid);
        $data = [];
        foreach ($patienttodaymarktpls as $patienttodaymarktpl) {
            $data[] = [
                'id' => $patienttodaymarktpl->id,
                'title' => $patienttodaymarktpl->title,
            ];
        }
        $todaymarks = PatientTodayMarkDao::getListByPatientIdThedate($patientid, date('Y-m-d'));
        $patienttodaymarks = [];
        foreach ($todaymarks as $todaymark) {
            $patienttodaymarks[] = $todaymark->title;
        }
        XContext::setValue("patienttodaymarks", $patienttodaymarks);
        XContext::setValue("patienttodaymarktpls", json_encode($data, JSON_UNESCAPED_UNICODE));
        
        XContext::setValue("papers", $papers);
        XContext::setValue("papertpls", $papertpls);
        XContext::setValue("patient", $patient);
        XContext::setValue("mydisease", $this->mydisease);
        XContext::setValue("drugsheet_nearly2", $drugsheet_nearly2);
        XContext::setValue("shopOrders", $shopOrders);
        XContext::setValue("optasktpls", $optasktpls);
    
        return self::SUCCESS;
    }
    
    public function doOneHistoryPatientHtml() {
        $patientid = XRequest::getValue('patientid', 0);
        
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, __METHOD__ . '患者不存在');
        
        
        if ($this->mydisease instanceof Disease) {
            $optasktpls_notfollow = OpTaskTplDao::getListByDiseaseid($this->mydisease->id, " and status=1 and code!='follow' ");
            $optasktpls_follow = OpTaskTplDao::getListByDiseaseid($this->mydisease->id, " and status=1 and code='follow' ");
        } else {
            $optasktpls_notfollow = Dao::getEntityListByCond('OpTaskTpl', " and status=1 and code!='follow' ");
            $optasktpls_follow = Dao::getEntityListByCond('OpTaskTpl', " and status=1 and code='follow' ");
        }
        
        XContext::setValue("patient", $patient);
        XContext::setValue("optasktpls_notfollow", $optasktpls_notfollow);
        XContext::setValue("optasktpls_follow", $optasktpls_follow);
        return self::SUCCESS;
    }
    
    // 组合筛选列表, 另一个列表
    public function doListForShow() {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);
        
        $optasktplid = XRequest::getValue("optasktplid", 213540376);
        $auditorid_yunying = XRequest::getValue("auditorid_yunying", 0);
        $patient_name = XRequest::getValue("patient_name", "");
        
        $bind = [];
        
        // 过滤疾病
        $diseaseidstr = $this->getContextDiseaseidStr();
        
        $cond = " and diseaseid in ($diseaseidstr) ";
        
        // 过滤类型
        if ($optasktplid > 0) {
            $cond .= " and optasktplid = :optasktplid ";
            $bind[':optasktplid'] = $optasktplid;
        }
        
        // 过滤运营人员
        if ($auditorid_yunying > 0) {
            $cond .= " and auditorid = :auditorid ";
            $bind[':auditorid'] = $auditorid_yunying;
        }
        
        // 模糊搜索患者名
        if ($patient_name != "") {
            if (XPatientIndex::isEqual($patient_name)) {
                $condQuery = " and word = :word ";
                $bind[':word'] = "{$patient_name}";
            } else {
                $condQuery = " and word like :word ";
                $bind[':word'] = "%{$patient_name}%";
            }
            $cond .= " and patientid = (
                select patientid
                from xpatientindexs
                where {$condQuery} limit 1) ";
        }
        
        $cond .= " order by id ";
        
        $optasks = Dao::getEntityListByCond4Page("OpTask", $pagesize, $pagenum, $cond, $bind);
        
        $countSql = "select count(*) as cnt from optasks where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/optaskmgr/listforshow?optasktplid={$optasktplid}&auditorid_yunying={$auditorid_yunying}&patient_name={$patient_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        
        XContext::setValue("optasktplid", $optasktplid);
        XContext::setValue("auditorid_yunying", $auditorid_yunying);
        XContext::setValue("patient_name", $patient_name);
        
        XContext::setValue("optasks", $optasks);
        XContext::setValue("pagelink", $pagelink);
        
        return self::SUCCESS;
    }
    
    // [右下] 患者进行中的任务, (点击[列表>查看])
    public function doOneNewHtml() {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        
        $optasks = OpTaskDao::getListByPatient($patient, " and status in (0, 2) order by plantime asc, id asc ");
        
        $tomorrow = date('Y-m-d', time() + 86400);
        $optasks_progress = [];
        $optasks_future = [];
        foreach ($optasks as $optask) {
            if ($optask->plantime >= $tomorrow && $optask->opnode->code == 'root') {
                $optasks_future[] = $optask;
            } else {
                $optasks_progress[] = $optask;
            }
        }
        
        XContext::setValue("patient", $patient);
        XContext::setValue("optasks_progress", $optasks_progress);
        XContext::setValue("optasks_future", $optasks_future);
        
        return self::SUCCESS;
    }
    
    // 回复患者html
    public function doPaperTplHtml() {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        XContext::setValue("patient", $patient);
        
        $sql = "select distinct a.*
            from papertpls a
            inner join diseasepapertplrefs b on b.papertplid = a.id
            inner join pcards c on c.diseaseid = b.diseaseid
            where b.doctorid=0 and c.patientid = :patientid
            order by groupstr, title ";
        
        $bind = [];
        $bind[':patientid'] = $patient->id;
        
        $papertpls = Dao::loadEntityList('PaperTpl', $sql, $bind);
        
        XContext::setValue("papertpls", $papertpls);
        return self::SUCCESS;
    }
    
    // 批量关闭任务
    public function doCloseOpTasksByOpTaskTplJson() {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        
        $content = XRequest::getValue('content', "\n批量关闭任务 (by {$this->myauditor->name})");
        
        $cond = " and optasktplid = :optasktplid and status <> 1 limit 300 ";
        $bind = [];
        $bind[':optasktplid'] = $optasktplid;
        
        $optasks = Dao::getEntityListByCond('OpTask', $cond, $bind);
        $cnt = count($optasks);
        
        foreach ($optasks as $optask) {
            $content = $optask->content . $content;
            $optask->content = trim($content);
            
            OpTaskStatusService::changeStatus($optask, 1, $this->myauditor->id);
        }
        
        echo "已批量关闭 {$cnt} 个任务!";
        
        return self::BLANK;
    }
    
    // 挂起
    public function doHangupJson() {
        $optaskid = XRequest::getValue('optaskid', 0);
        $optask = OpTask::getById($optaskid);
        DBC::requireNotEmpty($optask, "optask is null");
        
        OpTaskStatusService::changeStatus($optask, 2, $this->myauditor->id);
        
        echo 'ok';
        
        return self::BLANK;
    }
    
    // 切换节点
    public function doOpNodeFlowJson() {
        $optaskid = XRequest::getValue('optaskid', 0);
        $optask = OpTask::getById($optaskid);
        DBC::requireNotEmpty($optask, "optask is null");
        
        $opnodeflowid = XRequest::getValue('opnodeflowid', 0);
        $opnodeflow = OpNodeFlow::getById($opnodeflowid);
        DBC::requireNotEmpty($opnodeflow, "opnodeflow is null");
        
        // 日期
        $next_plantime = XRequest::getValue('next_plantime', '');
        
        // 任务描述
        $audit_remark = XRequest::getValue('audit_remark', '');
        
        // 如果是约定跟进,修改plantime
        if ($opnodeflow->to_opnode->code == 'appoint_follow') {
            $optask->plantime = $next_plantime;
        }
        
        // 住院预约审核任务，修改应住院日期
        if ($optask->optasktpl->code == 'audit' && $optask->optasktpl->subcode == 'bedtkt' && $opnodeflow->to_opnode->code == 'audit_pass') {
            $optask->obj->plan_date = $next_plantime;
        }
        
        // 住院预约审核任务，修改应住院日期
        if ($optask->optasktpl->code == 'audit' && $optask->optasktpl->subcode == 'bedtkt' && $opnodeflow->to_opnode->code == 'doctor_apply') {
            $optask->obj->plan_date = $next_plantime;
            // 描述
            $optask->obj->auditor_remark = $audit_remark;
        }
        
        $exArr = $_POST;
        
        // 任务节点切换(流转)
        OpTaskEngine::flow($optask, $opnodeflow, $this->myauditor->id, $exArr);
        
        XContext::setValue('json', $this->result);
        return self::TEXTJSON;
    }
    
    // 历史任务列表
    public function doListHistory() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);
        $patientid = XRequest::getValue("patientid", '');
        $patient_name = XRequest::getValue("patient_name", '');
        $auditorid_yunying = XRequest::getValue("auditorid_yunying", 0);
        $level = XRequest::getValue("level", 0);
        $donedate_from = XRequest::getValue("donedate_from", date('Y-m-d', time() - 7 * 86400));
        $donedate_to = XRequest::getValue("donedate_to", date('Y-m-d', time()));
        $showOpenTask = XRequest::getValue('show_open_task', 0);
        $isRandom = XRequest::getValue('isRandom', 0);
        
        $bind = [];
        $cond = "";
        
        $donedate_from_time = date('Y-m-d H:i:s', strtotime($donedate_from));
        $donedate_to_time = date("Y-m-d H:i:s", strtotime($donedate_to) + 24 * 3600);
        
        $cond .= " and a.donetime >= '{$donedate_from_time}' and a.donetime < '{$donedate_to_time}' ";
        
        $diseaseidstr = $this->getContextDiseaseidStr();
        
        $cond .= " and a.diseaseid in ($diseaseidstr) ";
        
        if ($auditorid_yunying) {
            $cond .= " and a.auditorid = :auditorid ";
            $bind[":auditorid"] = $auditorid_yunying;
        }
        
        if ($level) {
            $cond .= " and a.level = :level ";
            $bind[":level"] = $level;
        }
        
        $sql = " select a.* from optasks a where 1=1 ";
        if (!$showOpenTask) {
            $cond .= " and a.status>0";
        }
        $cond .= " and a.patientid>0 group by a.patientid order by a.donetime desc, a.level desc";
        
        if ($patient_name) {
            $sql = "select a.*
                from optasks a
                inner join xpatientindexs b on a.patientid = b.patientid
                where 1=1 ";
            
            $bind = [];
            $cond = "";
            if (XPatientIndex::isEqual($patient_name)) {
                $cond .= " and b.word = :word ";
                $bind[':word'] = "{$patient_name}";
            } else {
                $cond .= " and b.word like :word ";
                $bind[':word'] = "%{$patient_name}%";
            }
            
            $cond .= " and a.diseaseid in ($diseaseidstr) ";
            if (!$showOpenTask) {
                $cond .= " and a.status>0";
            }
            
            $cond .= " and a.patientid>0 group by a.patientid order by a.donetime desc, a.level desc";
        }
        
        if ($patientid) {
            $sql = " select a.* from optasks a where 1=1 ";
            
            $bind = [];
            $cond = " and a.patientid = :patientid ";
            $bind[':patientid'] = $patientid;
            
            if (!$showOpenTask) {
                $cond .= " and a.status>0";
            }
            
            $cond .= " group by a.patientid ";
        }
        
        $sql .= $cond;
        $countBind = $bind;
        
        if ($isRandom) {
            $optaskidsSql = str_replace("a.*", "a.id", $sql);
            $optaskids = OpTaskService::tryGetRandOptaskIds($optaskidsSql, $bind, 10);
            
            $optasks = Dao::getEntityListByIds('OpTask', $optaskids);
        } else {
            $optasks = Dao::loadEntityList4Page("OpTask", $sql, $pagesize, $pagenum, $bind);
            
            $countSql = "select count(*) from (select a.* from optasks a inner join xpatientindexs b on a.patientid = b.patientid where 1=1 {$cond})tt";
            $cnt = Dao::queryValue($countSql, $countBind);
            $url = "/optaskmgr/listhistory?auditorid_yunying={$auditorid_yunying}&level={$level}&show_open_task={$showOpenTask}&donedate_from={$donedate_from}&donedate_to={$donedate_to}&patient_name={$patient_name}";
            $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
            XContext::setValue("pagelink", $pagelink);
        }
        XContext::setValue("optasks", $optasks);
        
        
        if ($this->mydisease instanceof Disease) {
            $optasktpls_notfollow = OpTaskTplDao::getListByDiseaseid($this->mydisease->id, " and status=1 and code!='follow' ");
            $optasktpls_follow = OpTaskTplDao::getListByDiseaseid($this->mydisease->id, " and status=1 and code='follow' ");
        } else {
            $optasktpls_notfollow = Dao::getEntityListByCond('OpTaskTpl', " and status=1 and code!='follow' ");
            $optasktpls_follow = Dao::getEntityListByCond('OpTaskTpl', " and status=1 and code='follow' ");
        }
        
        XContext::setValue("show_open_task", $showOpenTask);
        XContext::setValue("optasktpls_notfollow", $optasktpls_notfollow);
        XContext::setValue("optasktpls_follow", $optasktpls_follow);
        XContext::setValue("auditorid_yunying", $auditorid_yunying);
        XContext::setValue("level", $level);
        XContext::setValue("donedate_from", $donedate_from);
        XContext::setValue("donedate_to", $donedate_to);
        XContext::setValue("patient_name", $patient_name);
        XContext::setValue('isRandom', $isRandom);
        
        // 获取筛选分类
        $arr_filter = PipeTplService::getArrForFilter();
        XContext::setValue("arr_filter", $arr_filter);
        return self::SUCCESS;
    }
    
    // TODO 需要修改注释
    public function doOneHistoryHtml() {
        $page = XRequest::getValue('page', 1);
        $pagesize = XRequest::getValue("pagesize", 10);
        $patientid = XRequest::getValue("patientid", 0);
        $optasktplid = XRequest::getValue("optasktplid", 0);
        $optasktplcode = XRequest::getValue("optasktplcode", "");
        
        $bind = [];
        $cond = " AND patientid=:patientid";
        $bind[':patientid'] = $patientid;
        
        if ($optasktplid) {
            $cond .= " AND optasktplid=:optasktplid ";
            $bind[':optasktplid'] = $optasktplid;
        }
        
        if ($optasktplcode == "follow") {
            $cond .= " AND optasktplid in (select id from optasktpls where code = :code) ";
            $bind[':code'] = $optasktplcode;
        }
        
        $offset = ($page - 1) * $pagesize;
        
        $cond1 = $cond . " AND status=1 ORDER BY donetime DESC LIMIT {$offset}, {$pagesize}";
        $optasks_history = Dao::getEntityListByCond('OpTask', $cond1, $bind);
        if ($page == 1) {
            // 加载全部正在进行的任务
            $cond2 = $cond . " AND status=0";
            $optasks_open = Dao::getEntityListByCond('OpTask', $cond2, $bind);
            $optasks_history = array_merge($optasks_open, $optasks_history);
        }
        
        $sql = "SELECT COUNT(*) FROM optasks WHERE 1=1 $cond AND status=1 ";
        $cnt = Dao::queryValue($sql, $bind);
        $totalPage = ceil($cnt / $pagesize);
        
        XContext::setValue("totalPage", $totalPage);
        XContext::setValue("page", $page + 1);
        XContext::setValue("optasks_history", $optasks_history);
        return self::SUCCESS;
    }
    
    // 患者打tag
    public function doPatientTagJson() {
        $patientid = XRequest::getValue("patientid", 0);
        $tagid = XRequest::getValue("tagid", 0);
        $isdelete = XRequest::getValue("isdelete", 0);
        
        if ($isdelete) {
            $tagref = TagRefDao::getByObjtypeObjidTagid("Patient", $patientid, $tagid);
            if ($tagref instanceof TagRef) {
                $tagref->remove();
            }
        } else {
            $row = array();
            $row["objtype"] = "Patient";
            $row["objid"] = $patientid;
            $row["tagid"] = $tagid;
            TagRef::createByBiz($row);
        }
        
        echo "ok";
        return self::BLANK;
    }
    
    // Paper任务的回复消息, TODO by 为啥这里发消息, 为啥只有量表类型有消息
    public function doReplyMsgJson() {
        $optaskid = XRequest::getValue("optaskid", 0);
        $content = XRequest::getValue("content", "");
        $isnote = XRequest::getValue("isnote", 0);
        
        $optask = OpTask::getById($optaskid);
        if ($optask instanceof OpTask) {
            $myauditor = $this->myauditor;
            
            $pushmsg = null;
            
            $wxuser = $optask->wxuser;
            if ($wxuser instanceof WxUser) {
                $pushmsg = PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $myauditor, $content);
            } else {
                $patient = $optask->patient;
                if ($patient instanceof Patient) {
                    // 20170419 TODO by sjp : 考虑重构, 为何只发给最新扫码的pcard ?
                    DBC::requireNotEmpty($this->mydisease, "必须选疾病");
                    $pcard = $patient->getOnePcardByDiseaseid($this->mydisease->id);
                    $pushmsg = PushMsgService::sendTxtMsgToWxUsersOfPcardByAuditor($pcard, $myauditor, $content);
                }
            }
            
            if ($pushmsg instanceof PushMsg) {
                if ($isnote) {
                    $optask->content = $content;
                    
                    $content = "[添加批注] " . $content;
                    OpTaskService::addOptLog($optask, $content, $myauditor->id);
                }
            }
        }
        echo "ok";
        return self::BLANK;
    }
    
    // 手工创建跟进任务
    public function doAddJson() {
        $patientid = XRequest::getValue("patientid", 0);
        $content = XRequest::getValue("content", "");
        $plantime = XRequest::getValue("plantime", '0000-00-00 00:00:00');
        $optasktplid = XRequest::getValue("optasktplid", 0);
        $level = XRequest::getValue("level", 2);
        
        $optasktpl = OpTaskTpl::getById($optasktplid);
        
        $auditorid = $this->myauditor->id;
        
        $patient = Patient::getById($patientid);
        
        $resultStr = "error";
        
        if ($patient instanceof Patient) {
            
            // 生成任务: 手工创建跟进任务
            $arr = [];
            $arr['content'] = $content;
            $arr['level'] = $level;
            $optask = OpTaskService::createOpTaskByOpTaskTpl(null, $patient, null, $optasktpl, null, $plantime, $auditorid, $arr);
            
            if ($optask instanceof OpTask) {
                $resultStr = "ok";
            } else {
                $resultStr = "fail";
            }
        }
        echo $resultStr;
        return self::BLANK;
    }
    
    // 关闭任务
    public function doCloseoptaskjson() {
        $optaskid = XRequest::getValue("optaskid", 0);
        $optask = OpTask::getById($optaskid);
        DBC::requireNotEmpty($optask, 'optask is null');
        
        OpTaskStatusService::changeStatus($optask, 1, $this->myauditor->id);
        
        // 异步创建运营操作日志
        $content = "【直接关闭了 [{$optask->optasktpl->title}({$optask->id})({$optask->plantime})]任务】<br>";
        $row = [
            'auditorid' => $this->myauditor->id,
            'patientid' => $optask->patient->id,
            'code' => 'optask',
            'content' => $content
        ];
        AuditorOpLog::nsqPush($row);
        
        // 未发送的定时消息转为手动
        $plantxtmsgs = Plan_txtMsgDao::getUnsentListByObj($optask);
        foreach ($plantxtmsgs as $plantxtmsg) {
            $plantxtmsg->type = 2;
        }
        
        // 礼来首次电话任务关闭时，判断是否可以进六院管理计划
        if ("firstTel" == $optask->optasktpl->code) {
            MgtGroupService::tryJoin_pkuh6_when_closeSunflowerOptask($optask->patient, false);
        }
        
        echo "ok";
        
        return self::BLANK;
    }
    
    // 修改等级
    public function doChangeLevelJson() {
        $optaskid = XRequest::getValue("optaskid", 0);
        $level = XRequest::getValue("level", 0);
        DBC::requireNotEmpty($level, "level不能为0");
        
        $optask = OpTask::getById($optaskid);
        DBC::requireTrue($optask instanceof OpTask, 'optask is null');
        $optask->level = $level;
        
        echo "ok";
        return self::BLANK;
    }
    
    // 修改负责人
    public function doChangeAuditorJson() {
        $optaskid = XRequest::getValue("optaskid", 0);
        $auditorid = XRequest::getValue("auditorid", 0);
        $optask = OpTask::getById($optaskid);
        
        if ($optask instanceof OpTask) {
            $optask->set4lock("auditorid", $auditorid);
        }
        echo "ok";
        return self::BLANK;
    }
    
    // 修改审核备注
    public function doModifyAuditRemarkJson() {
        $optaskid = XRequest::getValue('optaskid', 0);
        $optask = OpTask::getById($optaskid);
        DBC::requireNotEmpty($optask);
        
        $audit_remark = XRequest::getValue('audit_remark', '');
        $optask->audit_remark = $audit_remark;
        
        echo "ok";
        return self::BLANK;
    }
    
    // doChartHtml
    public function doChartHtml() {
        $patientid = XRequest::getValue("patientid", 0);
        $writer = XRequest::getValue("writer", "all");
        
        $conners = PaperToEchartsService::getConnersChartData($patientid);
        $writers = PaperDao::getWritersByPatientid($patientid);
        
        XContext::setValue("patientid", $patientid);
        XContext::setValue("writer", $writer);
        XContext::setValue("writers", $writers);
        XContext::setValue("conners", $conners);
        return self::SUCCESS;
    }
    
    // doPatientBaseHtml
    public function doPatientBaseHtml() {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        
        XContext::setValue("patient", $patient);
        
        $disease = $patient->disease;
        
        $papertpls = $disease->getPaperTpls();
        XContext::setValue("papertpls", $papertpls);
        
        $patientpgrouprefs = PatientPgroupRefDao::getListByPatientid($patientid);
        
        // done pcard fix
        $pgroups = PgroupDao::getListByDiseaseid($disease->id);
        XContext::setValue("patientpgrouprefs", $patientpgrouprefs);
        XContext::setValue("pgroups", $pgroups);
        
        return self::SUCCESS;
    }
    
    // 任务统计概率,入口在主菜单上
    public function doReviewList() {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);
        $fromdate = XRequest::getValue("fromdate", '');
        $todate = XRequest::getValue("todate", '');
        $status = XRequest::getValue("status", -1);
        $auditorid_yunying = XRequest::getValue("auditorid_yunying", 0);
        
        XContext::setValue("fromdate", $fromdate);
        XContext::setValue("todate", $todate);
        XContext::setValue("auditorid_yunying", $auditorid_yunying);
        
        $cond = "";
        $bind = [];
        
        $diseaseidstr = $this->getContextDiseaseidStr();
        
        $cond .= " and diseaseid in ($diseaseidstr) ";
        
        if ($fromdate) {
            $cond .= ' and plantime >= :fromdate ';
            $bind[':fromdate'] = $fromdate;
        }
        
        if ($todate) {
            $cond .= ' and plantime <= :todate ';
            $bind[':todate'] = $todate;
        }
        
        if ($status >= 0) {
            $cond .= ' and status = :status ';
            $bind[':status'] = $status;
        }
        
        if ($auditorid_yunying > 0) {
            $cond .= " and auditorid = :auditorid ";
            $bind[':auditorid'] = $auditorid_yunying;
        }
        
        $cond .= " order by plantime desc, id desc ";
        $optasks = Dao::getEntityListByCond4Page('OpTask', $pagesize, $pagenum, $cond, $bind);
        XContext::setValue("optasks", $optasks);
        
        $countSql = "select count(*) as cnt from optasks where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/optaskmgr/reviewlist";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        
        XContext::setValue("pagelink", $pagelink);
        
        // 任务完成总量
        $finished_cnt_sql = "select count(*) as cnt
            from optasks
            where status=1";
        
        $finished_cnt = Dao::queryValue($finished_cnt_sql, []);
        XContext::setValue("finished_cnt", $finished_cnt);
        
        // 本周已完成
        $today = date('Y-m-d 19:00:00');
        $day7ago = date('Y-m-d 19:00:00', time() - 7 * 24 * 3600);
        $finished_week_cnt_sql = "select count(*) as cnt
            from optasks
            where status=1 and plantime >= :fromdate and plantime <= :todate";
        
        $bind = [];
        $bind[':fromdate'] = $day7ago;
        $bind[':todate'] = $today;
        
        $finished_week_cnt = Dao::queryValue($finished_week_cnt_sql, $bind);
        XContext::setValue("finished_week_cnt", $finished_week_cnt);
        
        // 今日待完成
        $yesterday = date('Y-m-d 19:00:00', time() - 1 * 24 * 3600);
        $today = date('Y-m-d 19:00:00');
        $today_todo_cnt_sql = "select count(*) as cnt
            from optasks
            where status=0 and plantime >= :fromdate and plantime <= :todate";
        
        $bind = [];
        $bind[':fromdate'] = $yesterday;
        $bind[':todate'] = $today;
        
        $today_todo_cnt = Dao::queryValue($today_todo_cnt_sql, $bind);
        XContext::setValue("today_todo_cnt", $today_todo_cnt);
        
        // 待做任务总数及人数
        $open_cnt_sql = "select count(*) as cnt
            from optasks
            where status=0 and diseaseid in ($diseaseidstr)";
        
        $open_cnt = Dao::queryValue($open_cnt_sql);
        XContext::setValue("open_cnt", $open_cnt);
        
        $open_patient_cnt_sql = "select count(distinct patientid) as cnt
            from optasks
            where status=0 and diseaseid in ($diseaseidstr)";
        
        $open_patient_cnt = Dao::queryValue($open_patient_cnt_sql);
        XContext::setValue("open_patient_cnt", $open_patient_cnt);
        
        // 未开始任务总数及人数
        $plan_cnt_sql = "select count(*) as cnt
            from optasks
            where status=0 and diseaseid in ($diseaseidstr) and plantime >= :fromdate";
        
        $bind = [];
        $bind[':fromdate'] = date('Y-m-d 00:00:00');
        
        $plan_cnt = Dao::queryValue($plan_cnt_sql, $bind);
        XContext::setValue("plan_cnt", $plan_cnt);
        
        $plan_patient_cnt_sql = "select count(distinct patientid) as cnt
            from optasks
            where status=0 and diseaseid in ($diseaseidstr) and plantime >= :fromdate";
        
        $bind = [];
        $bind[':fromdate'] = date('Y-m-d 00:00:00');
        
        $plan_patient_cnt = Dao::queryValue($plan_patient_cnt_sql, $bind);
        XContext::setValue("plan_patient_cnt", $plan_patient_cnt);
        
        // 1周内待做任务总数及人数
        $week_cnt_sql = "select count(*) as cnt
            from optasks
            where status=0 and diseaseid in ($diseaseidstr) and plantime <= :todate";
        
        $bind = [];
        $bind[':todate'] = date("Y-m-d 00:00:00", time() + 3600 * 24 * 8);
        
        $week_cnt = Dao::queryValue($week_cnt_sql, $bind);
        XContext::setValue("week_cnt", $week_cnt);
        
        $week_patient_cnt_sql = "select count(distinct patientid) as cnt
            from optasks
            where status=0 and diseaseid in ($diseaseidstr) and plantime <= :todate";
        
        $bind = [];
        $bind[':todate'] = date("Y-m-d 00:00:00", time() + 3600 * 24 * 8);
        
        $week_patient_cnt = Dao::queryValue($week_patient_cnt_sql, $bind);
        XContext::setValue("week_patient_cnt", $week_patient_cnt);
        
        // 无任务人数
        $none_patient_cnt_sql = "select count(distinct pc.patientid)
                    from patients p
                    inner join pcards pc on pc.patientid=p.id
                    inner join diseases di on di.id=pc.diseaseid
                    inner join users u on u.patientid=p.id
                    inner join wxusers wx on wx.userid=u.id
                    where pc.diseaseid in ($diseaseidstr)
                    and p.id not in (select p.id from patients p
                    inner join pcards pc on pc.patientid=p.id
                    inner join optasks op on op.patientid=p.id
                    where pc.diseaseid in ($diseaseidstr)
                    and op.status=0)
                    and u.id >100000
                    and p.name not like '%测试%'
                    and p.status = 1";
        
        $none_patient_cnt = Dao::queryValue($none_patient_cnt_sql);
        XContext::setValue("none_patient_cnt", $none_patient_cnt);
        
        $none_patient_name_list_sql = "select p.name
                    from patients p
                    inner join pcards pc on pc.patientid=p.id
                    inner join diseases di on di.id=pc.diseaseid
                    inner join users u on u.patientid=p.id
                    inner join wxusers wx on wx.userid=u.id
                    where pc.diseaseid in ($diseaseidstr)
                    and p.id not in (select p.id from patients p
                    inner join pcards pc on pc.patientid=p.id
                    inner join optasks op on op.patientid=p.id
                    where pc.diseaseid in ($diseaseidstr)
                    and op.status=0)
                    and u.id >100000
                    and p.name not like '%测试%'
                    and p.status = 1
                    group by pc.patientid";
        
        $none_patient_name_list = Dao::queryValues($none_patient_name_list_sql);
        XContext::setValue("none_patient_name_list", $none_patient_name_list);
        return self::SUCCESS;
    }
    
    // 定时消息
    public function doAjaxAddPlanTxtMsgPost() {
        $type = XRequest::getValue('type', 1);
        
        $plan_send_time = XRequest::getValue('plansendtime');
        if ($type == 1) {
            DBC::requireNotEmptyString($plan_send_time, '请选择发送时间');
        }
        
        $content = XRequest::getValue('content');
        DBC::requireNotEmptyString($content, '请填写发送内容');
        
        $remark = XRequest::getValue('remark');
        
        $plantxtmsgid = XRequest::getValue('plantxtmsgid');
        if (empty($plantxtmsgid)) { // 新增
            $objid = XRequest::getValue('objid');
            $objtype = XRequest::getValue('objtype');
            
            $optask = $objtype::getById($objid);
            DBC::requireNotEmpty($optask, '任务不存在');
            
            $patientid = XRequest::getValue('patientid');
            
            $row = array();
            $row["patientid"] = $patientid;
            $row["auditorid"] = $this->myauditor->id;
            $row["objtype"] = $objtype;
            $row["objid"] = $objid;
            $row["type"] = $type;
            if ($type == 1) { // 自动发送
                $row["plan_send_time"] = $plan_send_time;
            }
            $row["content"] = $content;
            $row["remark"] = $remark;
            $plantxtmsg = Plan_txtMsg::createByBiz($row);
        } else { // 修改
            $plantxtmsg = Plan_txtMsg::getById($plantxtmsgid);
            DBC::requireNotEmpty($plantxtmsg, '定时消息不存在');
            
            $plantxtmsg->type = $type;
            if ($type == 1) {
                $plantxtmsg->plan_send_time = $plan_send_time;
            } else {
                $plantxtmsg->plan_send_time = '0000-00-00 00:00:00';
            }
            $plantxtmsg->content = $content;
            $plantxtmsg->remark = $remark;
        }
        
        // 关联定时消息后，任务会被自动挂起。
        $optask = $plantxtmsg->obj;
        if ($optask instanceof OpTask) {
            OpTaskStatusService::changeStatus($optask, 2, $this->myauditor->id);
        }
        
        // 立即发送
        if ($plantxtmsg->type == 3) {
            $plantxtmsg->send($this->myauditor);
        }
        
        $this->result['errmsg'] = "保存成功";
        XContext::setValue("json", $this->result);
        return self::TEXTJSON;
    }
    
    public function doAjaxPlanTxtMsgSendPost() {
        $plantxtmsgid = XRequest::getValue('plantxtmsgid', 0);
        DBC::requireNotEmpty($plantxtmsgid, "定时消息不存在");
        $plantxtmsg = Plan_txtMsg::getById($plantxtmsgid);
        DBC::requireTrue($plantxtmsg instanceof Plan_txtMsg, "定时消息不存在");
        
        $plantxtmsg->send($this->myauditor);
        
        $this->result['errmsg'] = "发送成功";
        XContext::setValue("json", $this->result);
        return self::TEXTJSON;
    }
    
    public function doAjaxPlanTxtMsgDeletePost() {
        $plantxtmsgid = XRequest::getValue('plantxtmsgid', 0);
        DBC::requireNotEmpty($plantxtmsgid, "定时消息不存在");
        $plantxtmsg = Plan_txtMsg::getById($plantxtmsgid);
        DBC::requireTrue($plantxtmsg instanceof Plan_txtMsg, "定时消息不存在");
        
        $plantxtmsg->remove();
        
        $this->result['errmsg'] = "删除成功";
        XContext::setValue("json", $this->result);
        return self::TEXTJSON;
    }
    
    public function doGetPatientMsgFirstAndCntJson() {
        $myauditor = $this->myauditor;
        if (false == $myauditor->isYunying()) {
            $this->returnError('非运营人员！');
        }
        
        $data = [];
        if ($myauditor->isADHDDiseaseGroup()) {
            $data = $this->getDataForADHD(); //多动症组
        } elseif ($myauditor->isCancerDiseaseGroup()) {
            $data = $this->getDataForCancer(); //肿瘤组
        } elseif ($myauditor->isMulDiseaseGroup()) {
            $data = $this->getDataForMulDisease(); //多疾病组
        } else {
            $this->returnError('没有查询到运营人员的疾病组！');
        }
        
        $this->result["data"] = $data;
        return self::TEXTJSON;
    }
    
    private function getDataForADHD() {
        $myauditor = $this->myauditor;
        $data = [];
        //礼来项目
        if ($myauditor->isManageADHDSunflower()) {
            $data_sunflower = $this->getDataForADHDSunflower();
            $data = array_merge($data, $data_sunflower);
        }
        
        //六院管理项目
        if ($myauditor->isManageADHDLiuyuan()) {
            $data_liuyuan = $this->getDataForADHDLiuyuan();
            $data = array_merge($data, $data_liuyuan);
        }
        
        //常规患者 报到两个月内
        if ($myauditor->isManageADHDTwoMonth()) {
            $data_twomonth = $this->getDataForADHDTwoMonth();
            $data = array_merge($data, $data_twomonth);
        }
        
        //常规患者 报到两个月以上
        if ($myauditor->isManageADHDTwoMonthLater()) {
            $data_twomonthlater = $this->getDataForADHDTwoMonthLater();
            $data = array_merge($data, $data_twomonthlater);
        }
        
        //ADHD主管
        if ($myauditor->isManageADHDMaster()) {
            $data_master = $this->getDataForADHDMaster();
            $data = array_merge($data, $data_master);
        }
        return $data;
    }
    
    private function getDataForADHDSunflower() {
        $data = [];
        //礼来项目
        $data[] = $this->getDataByLevelAndDiseasegroupids(5, [2]);
        return $data;
    }
    
    private function getDataForADHDLiuyuan() {
        $data = [];
        //设置等级
        $level_arr = [4, 2];
        $mgtgrouptpl = MgtGroupTplDao::getByEname("pkuh6");
        
        $cond_patient = " and mgtgrouptplid = :mgtgrouptplid ";
        $bind = [];
        $bind[':mgtgrouptplid'] = $mgtgrouptpl->id;
        
        //六院管理项目
        foreach ($level_arr as $key => $level) {
            $data[] = $this->getDataByLevelAndDiseasegroupids($level, [2], $cond_patient, $bind);
        }
        return $data;
    }
    
    private function getDataForADHDTwoMonth() {
        $myauditor = $this->myauditor;
        //设置等级
        $level_arr = [4, 2];
        
        $data = [];
        foreach ($level_arr as $key => $level) {
            $cond_patient = " and mgtgrouptplid=0 ";
            $bind = [];
            $baodaoday = date('Y-m-d', strtotime("-55 days"));
            $cond_patient .= " and createtime > :baodaoday ";
            $bind[':baodaoday'] = $baodaoday;
            
            $data[] = $this->getDataByLevelAndDiseasegroupids($level, [2], $cond_patient, $bind);
        }
        return $data;
    }
    
    private function getDataForADHDTwoMonthLater() {
        $myauditor = $this->myauditor;
        //设置等级
        $level_arr = [4, 2];
        
        $data = [];
        foreach ($level_arr as $key => $level) {
            $cond_patient = " and mgtgrouptplid=0 ";
            $bind = [];
            $baodaoday = date('Y-m-d', strtotime("-55 days"));
            $cond_patient .= " and createtime <= :baodaoday ";
            $bind[':baodaoday'] = $baodaoday;
            
            $data[] = $this->getDataByLevelAndDiseasegroupids($level, [2], $cond_patient, $bind);
        }
        return $data;
    }
    
    private function getDataForADHDMaster() {
        $myauditor = $this->myauditor;
        //设置等级
        $level_arr = [4, 2];
        
        $data = [];
        foreach ($level_arr as $key => $level) {
            $data[] = $this->getDataByLevelAndDiseasegroupids($level, [2]);
        }
        return $data;
    }
    
    private function getDataForCancer() {
        $data = [];
        $level_arr = [3, 2];
        foreach ($level_arr as $key => $level) {
            $data[] = $this->getDataByLevelAndDiseasegroupids($level, [3]);
        }
        return $data;
    }
    
    private function getDataForMulDisease() {
        $myauditor = $this->myauditor;
        $data = [];
        //疾病组
        $diseasegroupid_arr = [];
        if ($myauditor->isMulDiseaseGroupOne()) {
            $diseasegroupid_arr = [4, 5];
        } elseif ($myauditor->isMulDiseaseGroupTwo()) {
            $diseasegroupid_arr = [6, 9];
        } else {
            $diseasegroupid_arr = [4, 5, 6, 9];
        }
        
        $data[] = $this->getDataByLevelAndDiseasegroupids(2, $diseasegroupid_arr);
        return $data;
    }
    
    private function getDataByLevelAndDiseasegroupids($level, $diseasegroupid_arr, $cond_patient = "", $bind = []) {
        $myauditor = $this->myauditor;
        $optasktpl = OpTaskTplDao::getOneByUnicode("PatientMsg:message");
        
        $cond = "";
        //有效患者
        $cond .= " and patientid in (select id from patients where ( status = 1 or (status = 0 and auditstatus = 0) ) {$cond_patient}) ";
        //疾病
        $diseasegroupidstr = implode(',', $diseasegroupid_arr);
        $cond .= " and diseaseid in (select id from diseases where diseasegroupid in ({$diseasegroupidstr})) ";
        //任务等级
        $cond .= " and level={$level} and status=0 ";
        
        $cond .= " and optasktplid = :optasktplid order by id asc ";
        $bind[':optasktplid'] = $optasktpl->id;
        
        $data = [];
        $optask = Dao::getEntityByCond('OpTask', $cond, $bind);
        
        $cnt_sql = "select count(id) as cnt
            from optasks
            where 1=1 {$cond}";
        $cnt = Dao::queryValue($cnt_sql, $bind);
        
        $data['time'] = $optask instanceof OpTask ? substr($optask->createtime, 11, 5) : '00:00';
        $data['date'] = $optask instanceof OpTask ? substr($optask->createtime, 5, 5) : '00-00';
        
        if ($myauditor->isADHDDiseaseGroup()) {
            $dealminutes = OpTask::getDealMinutesColorByLevel($level);
            $data['needdealtime'] = $optask instanceof OpTask ? date("H:i", strtotime("$optask->createtime +$dealminutes min")) : '00:00';
        }
        
        if ($myauditor->isADHDDiseaseGroup() || $myauditor->isCancerDiseaseGroup()) {
            $data['level'] = $level;
            $data['levelstr'] = OpTask::getLevelStrByLevel($level);
            $data['levelcolor'] = OpTask::getLevelWordColorByLevel($level);
        }
        $data['cnt'] = $cnt;
        return $data;
    }
    
    //重要!!
    //前端目前不走这里了，走的是nginx反向代理 location /commonservice，直接连接的go服务
    //故此运营后台不能有名为/commonservice的Action
    private function doAjaxSearchSuggest() {
        $keyword = XRequest::getValue("keyword", "");
        if (!$keyword) {
            $this->returnError();
        }
        $params = ['k' => $keyword];
        $host = Config::getConfig("suggest_patient_host");
        DBC::requireNotEmpty($host, __METHOD__ . "suggest_patient_host is empty");
        
        $ret = FUtil::curlGet($host, $params);
        if (!$ret) {
            Debug::warn(__METHOD__ . " ret is empty keyword is {$keyword}");
            return null;
        }
        
        $arr = json_decode($ret, true);
        $this->result['data'] = $arr;
        return self::TEXTJSON;
    }
}
