<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/1/29
 * Time: 17:53
 */

class Rpt_CancerMgrAction extends AuditBaseAction
{
    
    public function doList() {
        $date_range = XRequest::getValue('date_range', '');
        $doctorgroupid = XRequest::getValue('doctorgroupid', 0);
        
        $cache_key = md5($date_range . $doctorgroupid);
        
        $condEx = '';
        $condEx_wxuser = '';
        if (!empty($doctorgroupid)) {
            $doctorgroup = DoctorGroup::getById($doctorgroupid);
            if ($doctorgroup instanceof DoctorGroup) {
                $condEx .= " AND a.doctorid IN (SELECT id FROM doctors WHERE doctorgroupid = {$doctorgroupid})";
                $condEx_wxuser .= " AND a.doctorid IN (SELECT id FROM doctors WHERE doctorgroupid = {$doctorgroupid})";
            }
        }
        
        $optask_create_cond = '';
        $optask_plan_cond = '';
        if (!empty($date_range)) {
            $arr = explode('至', $date_range);
            $from_date = $arr[0] . ' 00:00:00';
            $to_date = $arr[1] . ' 23:59:59';
            
            $condEx .= " AND a.createtime BETWEEN '{$from_date}' AND '{$to_date}' ";
            $condEx_wxuser .= " AND a.createtime BETWEEN '{$from_date}' AND '{$to_date}' ";
            
            $optask_create_cond = " and createtime BETWEEN '{$from_date}' AND '{$to_date}' ";
            $optask_plan_cond = " and plantime BETWEEN '{$from_date}' AND '{$to_date}' ";
            
            $optask_first_plantime_cond = " and a.first_plantime BETWEEN '{$from_date}' AND '{$to_date}' ";
            
            $optaskopnodelog_createtime_cond = " and a.createtime BETWEEN '{$from_date}' AND '{$to_date}' ";
        }
        
        $diseaseids = Disease::getCancerDiseaseidsStr();
        
        $condEx .= " AND a.diseaseid IN ({$diseaseids}) ";
        $condEx_wxuser .= " AND c.diseaseid IN ({$diseaseids}) ";
        
        // 统计肿瘤运营任务完成情况
        $auditorids = [
            '10009' => '王宫瑜',
            '10019' => '谢艺',
            '10048' => '赖雪梅',
            '10057' => '未国霞',
            '10067' => '张绍政',
            '10068' => '高健亮',
            '10072' => '王福升',
            '10097' => '王琳琳',
            '10111' => '金丛建',
            '10112' => '侯锦'
        ];
        $auditoridstr = implode(',', array_keys($auditorids));
        $sql = "select a.auditorid as 'auditor', 
                count(if(a.status=0,true,null)) as 'status0', 
                count(if(a.status=2,true,null)) as 'status2', 
                count(if(a.status=1,true,null)) as 'status1'
                from optasks a 
                where a.auditorid in ($auditoridstr) {$optask_first_plantime_cond}
                group by a.auditorid
                order by null ";
        $status_optasks = XCache::getValue($cache_key . "status_optasks", 300, function () use ($sql) {
            return Dao::queryRows($sql);
        }, 'php');
        
        $sql = "select createauditorid as 'auditor', count(*) as 'create'
                from optasks
                where createauditorid in ($auditoridstr) {$optask_create_cond}
                group by createauditorid
                order by null ";
        $create_optasks = XCache::getValue($cache_key . "create_optasks", 300, function () use ($sql) {
            return Dao::queryRows($sql);
        }, 'php');
        
        foreach ($status_optasks as $i => $status_optask) {
            foreach ($create_optasks as $create_optask) {
                if ($status_optask['auditor'] == $create_optask['auditor']) {
                    $status_optasks[$i]['create'] = $create_optask['create'];
                    
                    break;
                }
            }
            
            $status_optasks[$i]['auditor'] = $auditorids[$status_optasks[$i]['auditor']];
        }
        
        // 运营任务完成统计
        $sql = "select a.optasktplid,
                count(a.id) as 'all_cnt',
                count(if(a.status=1,if(b.code='finish' or b.code='unfinish',true,null), null)) as 'finish_unfinish_cnt',
                count(if(a.status=0,true,null)) as 'status_0_cnt',
                count(if(a.status=2,true,null)) as 'status_2_cnt', 
                count(if(a.status=1,true,null)) as 'status_1_cnt',
                count(if(a.status=1,if(b.code='finish',true,null),null)) as 'status_1_finish_cnt',
                count(if(a.status=1,if(b.code='unfinish',true,null),null)) as 'status_1_unfinish_cnt',
                count(if(a.status=1,if(b.id is null,true,null),null)) as 'status_1_opnode_0_cnt',
                count(if(a.status=1,if((b.code!='finish' and b.code!='unfinish'),true,null),null)) as 'status_1_opnode_other_cnt'
                from optasks a
                left join opnodes b on b.id = a.opnodeid
                where 1 = 1 {$optask_first_plantime_cond}
                group by a.optasktplid ";
        
        $optask_tpl_list = XCache::getValue($cache_key . "optask_tpl_list", 300, function () use ($sql) {
            return Dao::queryRows($sql);
        }, 'php');
        
        // 6012 【定期随访】任务按照阶段统计完成率  之前的都不用修了，从上线这天开始（2018-04-17）
        $sql = "select 
                  count(a.id) as 'all',
                  count(if(a.patientstageid = 0, true, null)) as '无阶段',
                  count(if(a.patientstageid = 1, true, null)) as '其他',
                  count(if(a.patientstageid = 2, true, null)) as '化疗',
                  count(if(a.patientstageid = 3, true, null)) as '靶向',
                  count(if(a.patientstageid = 4, true, null)) as '手术'
                from optasks a
                inner join auditors b on b.id = a.auditorid
                inner join opnodes c on c.id = a.opnodeid
                where c.code = 'finish' and a.optasktplid = 270243896 and a.status = 1 and a.donetime >= '2018-04-18' {$optask_first_plantime_cond} ";
        $optask_regular_follow_list = Dao::queryRows($sql);
        
        // 6017 查出所有非终结节点的切换
        $sql = "select a.auditorid, c.name, count(a.id) as cnt
                from optaskopnodelogs a
                inner join opnodes b on b.id = a.opnodeid
                inner join auditors c on c.id = a.auditorid
                where b.code not in ('finish', 'unfinish', 'other_close', 'wbc_observe', 'wbc_treat') {$optaskopnodelog_createtime_cond}
                group by a.auditorid
                order by cnt desc ";
        $optask_effect_list = Dao::queryRows($sql);
        // 处理成kv形式，前端方便
        $optask_kv_effect_list = [];
        foreach ($optask_effect_list as $item) {
            $optask_kv_effect_list["{$item['name']}"] = $item['cnt'];
        }
        
        // 已关注患者数(患者总数，分母)
        $subscribe_patient_sql = "SELECT count(DISTINCT(a.id)) AS cnt, count(DISTINCT(if(a.status = 1, a.id, null))) AS online_cnt, count(DISTINCT(if(a.status = 0, a.id, null))) AS offline_cnt  
                                  FROM patients a
                                  INNER JOIN wxusers c ON c.patientid = a.id
                                  WHERE 1=1 {$condEx} ";
        
        // 未关注患者数
        $notsubscribe_sql = "SELECT count(DISTINCT(a.id)) AS cnt 
                             FROM patients a
                             LEFT JOIN wxusers c ON c.patientid = a.id
                             WHERE 1=1
                             AND a.createuserid = 0
                             AND c.id is NULL {$condEx} ";
        
        // 合并被丢弃数
        $merge_rubbish_sql = "SELECT count(DISTINCT(a.id)) AS cnt 
                             FROM patients a
                             LEFT JOIN wxusers c ON c.patientid = a.id
                             WHERE 1=1
                             AND a.createuserid > 0
                             AND c.id is NULL
                             {$condEx}";
        
        // 死亡患者数
        $dead_sql = "SELECT count(DISTINCT(a.id)) AS cnt 
                     FROM patients a
                     INNER JOIN wxusers c ON c.patientid = a.id
                     WHERE 1=1
                     AND a.is_live = 0 {$condEx} ";
        
        // 取消关注数
        $unsubscribe_sql = "SELECT count(DISTINCT(a.id)) AS cnt
                            FROM patients a
                            INNER JOIN wxusers c ON c.patientid = a.id
                            WHERE 1=1
                            AND a.subscribe_cnt = 0 {$condEx} ";
        
        // 失访数
        $lose_sql = "SELECT count(DISTINCT(a.id)) AS cnt 
                     FROM patients a
                     INNER JOIN wxusers c ON c.patientid = a.id
                     WHERE 1=1
                     AND a.is_lose = 1 {$condEx} ";
        
        // 未报到患者数，未报到患者关注数，未报到患者取消关注数
        $sql = "SELECT count(*) AS wxuser_cnt, sum(if(subscribe=1, 1, 0)) AS subscribe_1_cnt, sum(if(subscribe=0, 1, 0)) AS subscribe_0_cnt
                FROM (
                    SELECT distinct a.id, a.subscribe
                    FROM wxusers a
                    LEFT JOIN patients b ON b.id = a.patientid
                    INNER JOIN doctordiseaserefs c ON c.doctorid = a.doctorid
                    WHERE 1=1
                    AND b.id IS NULL
                    {$condEx_wxuser}
                ) tt;";
        $cnts = XCache::getValue($cache_key . "cnts", 300, function () use ($sql) {
            return Dao::queryRow($sql);
        }, 'php');
        
        $nopatient_cnt = $cnts['wxuser_cnt'];
        $nopatient_subscribe_cnt = $cnts['subscribe_1_cnt'];
        $nopatient_unsubscribe_cnt = $cnts['subscribe_0_cnt'];
        
        $row = XCache::getValue($cache_key . "row", 300, function () use ($subscribe_patient_sql) {
            return Dao::queryRow($subscribe_patient_sql);
        }, 'php');
        $subscribe_patient_cnt = $row['cnt'];
        $subscribe_patient_online_cnt = $row['online_cnt'];
        $subscribe_patient_offline_cnt = $row['offline_cnt'];
        
        $merge_rubbish_cnt = XCache::getValue($cache_key . "merge_rubbish_cnt", 300, function () use ($merge_rubbish_sql) {
            return Dao::queryValue($merge_rubbish_sql);
        }, 'php');
        
        $notsubscribe_cnt = XCache::getValue($cache_key . "notsubscribe_cnt", 300, function () use ($notsubscribe_sql) {
            return Dao::queryValue($notsubscribe_sql);
        }, 'php');
        
        $dead_cnt = XCache::getValue($cache_key . "dead_cnt", 300, function () use ($dead_sql) {
            return Dao::queryValue($dead_sql);
        }, 'php');
        
        $unsubscribe_cnt = XCache::getValue($cache_key . "unsubscribe_cnt", 300, function () use ($unsubscribe_sql) {
            return Dao::queryValue($unsubscribe_sql);
        }, 'php');
        
        $lose_cnt = XCache::getValue($cache_key . "lose_cnt", 300, function () use ($lose_sql) {
            return Dao::queryValue($lose_sql);
        }, 'php');
        
        $date_range_str = $date_range ? $date_range : '至今';
        
        XContext::setValue('date_range', $date_range);
        XContext::setValue('date_range_str', $date_range_str);
        XContext::setValue('doctorgroupid', $doctorgroupid);
        XContext::setValue('subscribe_patient_cnt', $subscribe_patient_cnt);
        XContext::setValue('subscribe_patient_online_cnt', $subscribe_patient_online_cnt);
        XContext::setValue('subscribe_patient_offline_cnt', $subscribe_patient_offline_cnt);
        XContext::setValue('merge_rubbish_cnt', $merge_rubbish_cnt);
        XContext::setValue('notsubscribe_cnt', $notsubscribe_cnt);
        XContext::setValue('dead_cnt', $dead_cnt);
        XContext::setValue('unsubscribe_cnt', $unsubscribe_cnt);
        XContext::setValue('lose_cnt', $lose_cnt);
        XContext::setValue('nopatient_cnt', $nopatient_cnt);
        XContext::setValue('nopatient_subscribe_cnt', $nopatient_subscribe_cnt);
        XContext::setValue('nopatient_unsubscribe_cnt', $nopatient_unsubscribe_cnt);
        
        XContext::setValue('optask_list', $status_optasks);
        XContext::setValue('optask_tpl_list', $optask_tpl_list);
        
        XContext::setValue('optask_regular_follow_list', $optask_regular_follow_list);
        XContext::setValue('optask_kv_effect_list', $optask_kv_effect_list);
        
        return self::SUCCESS;
    }
    
    public function doGetWorkloadList() {
        $thedate = XRequest::getValue('thedate', date('Y-m-d'));
        $auditorid = XRequest::getValue('auditorid', 0);
        
        $start_time = $thedate . ' 00:00:00';
        $end_time = $thedate . ' 23:59:59';
        
        if (empty($auditorid)) {
            $auditorids = "10057, 10048, 10067, 10097, 10112";
        } else {
            $auditorids = $auditorid;
        }
        
        $sql = "SELECT RIGHT(LEFT(a.createtime, 13), 2) AS hour, COUNT(*) AS cnt
                FROM optasks a
                WHERE a.status = 1
                AND a.auditorid IN ({$auditorids})
                AND a.first_plantime BETWEEN '{$start_time}' AND '{$end_time}'
                GROUP BY RIGHT(LEFT(a.createtime, 13), 2)
                ";
        $data1 = Dao::queryRows($sql);
        
        $sql = "SELECT RIGHT(LEFT(a.createtime, 13), 2) AS hour, COUNT(*) AS cnt
                FROM optaskopnodelogs a
                LEFT JOIN optasks b ON b.id = a.optaskid
                LEFT JOIN opnodes c ON c.id = b.opnodeid
                WHERE a.auditorid IN ({$auditorids})
                AND c.code not in ('finish', 'unfinish', 'other_close', 'wbc_observe', 'wbc_treat')
                AND (a.createtime BETWEEN :start_time AND :end_time)
                GROUP BY RIGHT(LEFT(a.createtime, 13), 2)";
        
        $bind = [];
        $bind[":start_time"] = $start_time;
        $bind[":end_time"] = $end_time;
        
        $data2 = Dao::queryRows($sql, $bind);
        
        $data = [];
        foreach ($data1 as $item1) {
            $hour1 = $item1['hour'];
            $cnt1 = $item1['cnt'];
    
            $cnt = $cnt1;
    
            foreach ($data2 as $item2) {
                $hour2 = $item2['hour'];
                $cnt2 = $item2['cnt'];
                if ($hour1 == $hour2) {
                    $cnt += $cnt2;
                    break;
                }
            }
            $data[$hour1] = $cnt;
        }
        
        foreach ($data2 as $item1) {
            $hour1 = $item1['hour'];
            $cnt1 = $item1['cnt'];
            if (in_array($hour1, $data)) {
                Debug::trace($hour1. "已存在");
                continue;
            }

            $cnt = $cnt1;

            foreach ($data1 as $item2) {
                $hour2 = $item2['hour'];
                $cnt2 = $item2['cnt'];
                if ($hour1 == $hour2) {
                    $cnt += $cnt2;
                    break;
                }
            }
            $data[$hour1] = $cnt;
        }
        $json_data = json_encode($data);
        
        XContext::setValue('json', $json_data);
        XContext::setValue('thedate', $thedate);
        XContext::setValue('auditorid', $auditorid);
        return self::SUCCESS;
        
    }
    
    
    public function doLoseList() {
        $diseaseids = Disease::getCancerDiseaseidsStr();
        
        // 已关注患者数(患者总数，分母)
        $subscribe_patient_sql = "SELECT count(DISTINCT(a.id)) AS cnt  
                                  FROM patients a
                                  INNER JOIN wxusers c ON c.patientid = a.id
                                  WHERE 1=1 
                                  AND a.diseaseid IN ({$diseaseids})";
        $subscribe_patient_cnt = Dao::queryValue($subscribe_patient_sql);
        
        $lose_sql = "SELECT a.*, count(d.id) as patientreocrd_cnt, count(e.id) as optask_cnt
                     FROM patients a
                     INNER JOIN wxusers c ON c.patientid = a.id
                     LEFT JOIN patientrecords d ON (d.patientid = a.id AND d.code = 'common' AND d.type = 'lose')
                     LEFT JOIN optasks e ON (e.patientid = a.id AND e.status IN (0, 2))
                     WHERE 1=1
                     AND a.is_lose = 1 
                     AND a.diseaseid IN ({$diseaseids})
                     GROUP BY a.id 
                     ORDER BY patientreocrd_cnt DESC, optask_cnt DESC";
        
        $pagesize = 500;
        $pagenum = XRequest::getValue('pagenum', 1);
        $patients = Dao::loadEntityList4Page('Patient', $lose_sql, $pagesize, $pagenum);
        
        $countSql = "SELECT count(DISTINCT(a.id)) AS cnt 
                     FROM patients a
                     INNER JOIN wxusers c ON c.patientid = a.id
                     WHERE 1=1
                     AND a.is_lose = 1 
                     AND a.diseaseid IN ({$diseaseids}) ";
        
        //获得分页
        $cnt = Dao::queryValue($countSql);
        $url = "/rpt_cancermgr/loselist";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        
        XContext::setValue('lose_cnt', $cnt);
        XContext::setValue('subscribe_patient_cnt', $subscribe_patient_cnt);
        XContext::setValue('patients', $patients);
        return self::SUCCESS;
    }
    
    public function doOpTaskDataJson() {
        /*
        肿瘤-基本信息填写
        肿瘤-血常规收集
        肿瘤-血常规收集(治疗)
        肿瘤-血常规收集(观察)
        肿瘤-化疗方案收集
        肿瘤-用药核对
        肿瘤-定期复查
        肿瘤-定期随访

        count(if(a.status=0,true,null)) / count(a.id) * 100 as 'status_0_cnt',
        count(if(a.status=2,true,null)) / count(a.id) * 100 as 'status_2_cnt',
        count(if(a.status=1,true,null)) / count(a.id) * 100 as 'status_1_cnt'
         */
        $date_range = XRequest::getValue('date_range', '');
        
        if (!empty($date_range)) {
            $arr = explode('至', $date_range);
            $from_date = $arr[0];
            $to_date = $arr[1];
        } else {
            // 如果没选日期，默认给一个月
            $from_date = date("Y-m-d", strtotime("-1 month"));
            $to_date = date('Y-m-d');
        }
        
        $optask_first_plantime_cond = " and a.first_plantime >= '{$from_date}' AND a.first_plantime < '{$to_date}' ";
        
        $optasktplids = [493488746, 440624196, 445430496, 458705906, 334289746, 445440206, 577224776, 270243896];
        
        $xAxis = [];
        $date = $from_date;
        while (strtotime($date) < strtotime($to_date)) {
            $xAxis[] = $date;
            $date = date('Y-m-d', strtotime($date) + 3600 * 24);
        }
        
        foreach ($optasktplids as $i => $optasktplid) {
            $sql = "select left(a.first_plantime, 10) as 'first_plantime',
                count(if(b.code='finish',true,null)) / count(if(b.code='finish',true,if(b.code='unfinish',true,null))) * 100 as 'status_1_cnt'
                from optasks a
                left join opnodes b on b.id = a.opnodeid
                where 1 = 1 {$optask_first_plantime_cond} and a.optasktplid = {$optasktplid} and a.status = 1
                group by left(a.first_plantime, 10) ";
            $list = Dao::queryRows($sql);
            
            $optasktpltitle = Dao::queryValue("select title from optasktpls where id = {$optasktplid} ");
            
            $serieVs = [];
            foreach ($xAxis as $xAxi) {
                $flag = 0;
                foreach ($list as $item) {
                    if ($item['first_plantime'] == $xAxi) {
                        $flag = 1;
                        break;
                    }
                }
                if ($flag == 1) {
                    $serieVs[] = $item['status_1_cnt'];
                } else {
                    $serieVs[] = -10;
                }
            }
            
            $series[] = [
                'name' => $optasktpltitle,
                'type' => 'line',
                'stack' => $optasktpltitle,
                'data' => $serieVs
            ];
        }
        
        $legend = [
            '肿瘤-基本信息填写',
            '肿瘤-血常规收集',
            '肿瘤-血常规收集(治疗)',
            '肿瘤-血常规收集(观察)',
            '肿瘤-化疗方案收集',
            '肿瘤-用药核对',
            '肿瘤-定期复查',
            '肿瘤-定期随访'
        ];
        
        $data = [
            'legend' => $legend,
            'xAxis' => $xAxis,
            'series' => $series
        ];
        
        XContext::setValue('json', $data);
        
        return self::TEXTJSON;
    }
}