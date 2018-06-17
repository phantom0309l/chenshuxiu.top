<?php
ini_set('display_errors', 0);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class export_doctor_checkup
{
    private $latestZhenduans = [];
    private static $maps =  [
        'id' => '方寸患者ID',
        'name' => '姓名',
        'sex' => '性别',
        'birthday' => '生日',
        'prcrid' => '身份证',
        'nation' => '民族',
        'blood_type' => '血型',
        'marry_status' => '婚姻',
        'children' => '子女',
        'career' => '职业',
        'education' => '教育程度',
        'mobile' => '主联系人手机',
        'email' => '电子邮箱',
        'birth_place' => '出生地',
        'native_place' => '籍贯',
        'now_place' => '现居住地',
        'long_live_place' => '长期居住地',
        'once_place' => '曾居住地',
        'communicate_place' => '通讯地址',
        'other_contacts' => '备用联系人',
        'out_case_no' => '病历号',
        'patientcardno' => '就诊卡号',
        'patientcard_id' => '患者ID',
//         'bingan_no' => '病案号',
        'fee_type' => '医保类型',
        'create_doc_date' => '建档日期',
        'hospital' => '建档医院',
        'masterdoctor' => '主治医生',
        'scientific_no' => '科研编号',
        'general_history' => '普通病史',
        'family_history' => '家族病史',
        'menstruation_history' => '月经史',
        'childbearing_history' => '生育史',
        'smoke_history' => '吸烟史',
        'drink_history' => '饮酒史',
        'trauma_history' => '外伤史',
        'infect_history' => '传染病史',
        'special_contact_history' => '特殊接触史',
        'allergy_history' => '过敏史',
        'zhenduan' => '最新诊断',
    ];
    // public function run () {
    // $id = 213626476;
    public function run ($id) {
        $j = 0;
        while ($j < 2) {
            try {
                $unitofwork = BeanFinder::get("UnitOfWork");

                $i = 0;
                while ($i < 50) {
                    $exportJob = Dao::getEntityById('Export_Job', $id);
                    if (! $exportJob) {
                        $i ++;
                        usleep(200000);
                        continue;
                    }
                    break;
                }

                // 没找到任务
                if (! $exportJob) {
                    Debug::warn(__METHOD__ . ' export job is null id [' . $id . ']');
                    Debug::flushXworklog();
                    return false;
                }

                // 不是初始状态
                if (! $exportJob->isNew()) {
                    Debug::trace(__METHOD__ . 'export job [' . $id . '] status is not new');
                    Debug::flushXworklog();
                    return true;
                }

                // 开始运行导出任务
                // 给任务置一个状态
                $exportJob->status = Export_Job::STATUS_RUNNING;
                $unitofwork->commitAndInit();
                BeanFinder::clearBean("UnitOfWork");
                BeanFinder::clearBean("DbExecuter");
                $dbExecuter = BeanFinder::get('DbExecuter');
                $unitofwork = BeanFinder::get("UnitOfWork");
                // 重新获取一次，防止出问题
                $exportJob = Dao::getEntityById('Export_Job', $id);
                $doctorid = $exportJob->doctorid;
                // todo biz work
                if ($exportJob->patienttagtplid == 0) {
                    $sql = "SELECT a.id, a.name, b.out_case_no,
                        CASE WHEN a.sex=1 THEN '男'
                        WHEN a.sex=2 THEN '女'
                        WHEN a.sex=0 THEN '未知'
                        END AS gender, a.birthday, a.nation, b.create_doc_date, c.name AS doctor, d.name AS hospital
                        FROM patients a INNER JOIN pcards b ON a.id = b.patientid
                        INNER JOIN doctors c ON b.doctorid = c.id
                        INNER JOIN hospitals d ON c.hospitalid = d.id
                        WHERE b.doctorid = '$doctorid'";
                } else {
                    $sql = "SELECT a.id, a.name, b.out_case_no,
                        CASE WHEN a.sex=1 THEN '男'
                        WHEN a.sex=2 THEN '女'
                        WHEN a.sex=0 THEN '未知'
                        END AS gender, a.birthday, a.nation, b.create_doc_date, d.name AS doctor, e.name AS hospital
                        FROM patients a INNER JOIN pcards b ON a.id = b.patientid
                        INNER JOIN patienttags c ON a.id = c.patientid
                        INNER JOIN doctors d ON b.doctorid = d.id
                        INNER JOIN hospitals e ON d.hospitalid = e.id
                        WHERE b.doctorid = '$doctorid'
                        AND c.patienttagtplid='{$exportJob->patienttagtplid}'
                        ";
                }
                $patientInfos = Dao::queryRows($sql);
                $len = count($patientInfos);
                $m = 0;
                $data0 = [];
                $i = 0;
                foreach ($patientInfos as $patientInfo) {
                    //echo ++$i, "\t", $patientInfo['name'], "\n";
                    $tmp = $this->exportCheckup($exportJob, $patientInfo);
                    $data0 = array_merge($data0, $tmp);
                    $m ++;
                    if ($m > 0 && $m % 100 == 0) {
                        $exportJob->progress = round(($m / $len) * 100, 1);
                        // echo "progress ", $exportJob->progress, "\n";
                        $unitofwork->commitAndInit();
                        BeanFinder::clearBean("UnitOfWork");
                        BeanFinder::clearBean("DbExecuter");
                        $dbExecuter = BeanFinder::get('DbExecuter');
                        $unitofwork = BeanFinder::get("UnitOfWork");
                        $exportJob = Dao::getEntityById('Export_Job', $id);
                        // sleep(1);
                    }
                }
                //患者基本数据
                $patientInfoList = PatientService::getPatientListByDoctorId($doctorid, $exportJob->patienttagtplid);
                $patientInfoList = $this->formatPatientInfoData($patientInfoList, $doctorid);
                //主诉数据
                $zhusuData = $this->getZhusuData($exportJob, $doctorid);
                //治疗数据，如果有的话
                $chemoData = $this->getChemoData($exportJob, $doctorid);
                // 按照量表维度导出
                $this->exportData4Checkup($data0, $patientInfoList, $zhusuData, $chemoData, $id, $exportJob->doctor->user->username);
                // //按患者维度导出
                // $this->exportData4Patient($data0);
                $exportJob->progress = 100;
                $exportJob->status = Export_Job::STATUS_COMPLETE;
                $unitofwork->commitAndInit();
                break; // 跳出外层循环
            } catch (Exception $e) {
                print_r($e->getMessage());
                $j ++;
                BeanFinder::clearBean("UnitOfWork");
                BeanFinder::clearBean("DbExecuter");
                $dbExecuter = BeanFinder::get('DbExecuter');
                $dbExecuter->reConnection();
                Debug::warn('export job fail ' . $j . ' jobid:' . $id);
            }
        }
        Debug::trace('export job has done success jobid:' . $id);
        Debug::flushXworklog();

        return true;
    }

    private function exportCheckup ($exportJob, $patientInfo) {
        $patientid = $patientInfo['id'];
        $patientName = $patientInfo['name'];
        $data = json_decode($exportJob->data, true);
        $doctorid = $exportJob->doctorid;
        $ret = [];
        if ($doctorid == '33') {
            $zhenduan = $this->getLatestZhenDuan($patientid, $doctorid);
        }
        foreach ($data as $ename => $questionids) {
            $cond = ' AND doctorid=:doctorid AND ename=:ename AND diseaseid=:diseaseid';
            $bind = [
                ':doctorid' => $doctorid,
                ':ename' => $ename,
                ':diseaseid' => $exportJob->diseaseid,
            ];
            
            $checkuptpl = Dao::getEntityByCond('CheckupTpl', $cond, $bind);

            //取checkup，考虑到徐雁的特殊情况，只导出所有诊断最新的一条
            if ($ename == 'zhenduan' && $doctorid == '33') {
                $sql = "SELECT * FROM checkups 
                    WHERE checkuptplid=:checkuptplid AND patientid=:patientid
                    ORDER BY id DESC LIMIT 1";
            } else {
                $sql = "SELECT * FROM checkups
                    WHERE checkuptplid=:checkuptplid AND patientid=:patientid";
            }
            $bind = [
                ':checkuptplid' => $checkuptpl->id,
                ':patientid' => $patientid];
            $checkups = Dao::loadEntityList('Checkup', $sql, $bind);
            if (empty($checkups)) {
                continue; // 该患者该检查报告模板，没有检查报告
            }
            /*
             * 每一个checkuptpl
             * [
             * ename:ename,
             * name:name,
             * patientid:patientid,
             * patientname:patientname,
             * qa:[
             * {
             * createtime:createtime,
             * question:answer,
             * question:answer
             * }
             * ]
             * ]
             * ]
             */
            $questions = Dao::getEntityListByIds('XQuestion', $questionids);
            $ret0 = [];
            $ret0['ename'] = $ename;
            $ret0['name'] = $checkuptpl->title;
            $ret0['patientid'] = $patientid;
            $ret0['patientname'] = $patientName;
            $ret0['qa'] = [];
            $i = 0;
            foreach ($checkups as $checkup) {
                $answers = $checkup->xanswersheet->getAnswers();
                $start = microtime(true);
                $cost = round(microtime(true) - $start, 2);
                //echo "\t", $checkuptpl->title, "\t", $cost, "\n";
                $tmp = [
                    '患者' => $patientName,
                    '方寸患者ID' => $patientid . "\t",
                    '创建时间' => $checkup->createtime,
                    '性别' => $patientInfo['gender'],
                    '生日' => $patientInfo['birthday'],
                ];
                if ($doctorid == '33' && $ename != 'zhenduan') {
                    $tmp['最新诊断'] = $zhenduan;
                }
                foreach ($questions as $question) {
                    if ($question->type == 'Caption') {
                        continue;
                    }
                    $questionName = $question->content;
                    $answer = $this->getAnswerByQuestion($answers, $question);
                    if ($answer) {
                        $answerContent = $this->getAnswerContent($answer);
                    } else {
                        $answerContent = '';
                    }
                    $tmp[$questionName] = $answerContent;
                }
                $ret0['qa'][] = $tmp;
            }
            $ret[] = $ret0;

            // $questions = Dao::getEntityListByIds('XQuestion', $questionids);
            // $questionArr = [];
            // foreach ($questions as $question) {
            // if ($question->type == 'Caption') {
            // continue;
            // }
            // $tmp = [];
            // $tmp['xquestionid'] = $question->id;
            // $tmp['content'] = $question->content;
            // $tmp['issimple'] = $question->issimple;
            // $tmp['type'] = $question->type;
            // $questionArr[] = $tmp;
            // }
        }
        return $ret;
    }

    private function getAnswerByQuestion($answers, $question) {
        foreach ($answers as $answer) {
            if ($answer->xquestionid == $question->id) {
                return $answer;
            }
        }
        return '';
    }

    private function getZhusuData($exportJob, $doctorid) {
        if ($exportJob->patienttagtplid == 0) {
            if ($doctorid == '33') {
                $sql = "SELECT b.name AS 患者,b.id AS 方寸患者ID,
                    CASE WHEN b.sex=1 THEN '男'
                    WHEN b.sex=2 THEN '女'
                    WHEN b.sex=0 THEN '未知'
                    END AS 性别, b.birthday AS 生日, '' AS 最新诊断,
                    a.`thedate` AS 就诊日期, a.`content` AS 主诉
                    FROM `revisitrecords` a
                    INNER JOIN patients b ON a.`patientid` = b.`id`
                    WHERE a.doctorid=$doctorid
                    ORDER BY b.name ASC, thedate ASC";
            } else {
                $sql = "SELECT b.name AS 患者,b.id AS 方寸患者ID,
                    CASE WHEN b.sex=1 THEN '男'
                    WHEN b.sex=2 THEN '女'
                    WHEN b.sex=0 THEN '未知'
                    END AS 性别, b.birthday AS 生日,
                    a.`thedate` AS 就诊日期, a.`content` AS 主诉
                    FROM `revisitrecords` a
                    INNER JOIN patients b ON a.`patientid` = b.`id`
                    WHERE a.doctorid=$doctorid
                    ORDER BY b.name ASC, thedate ASC";
            }
        } else {
            if ($doctorid == '33') {
                $sql = "SELECT b.name AS 患者,b.id AS 方寸患者ID,
                    CASE WHEN b.sex=1 THEN '男'
                    WHEN b.sex=2 THEN '女'
                    WHEN b.sex=0 THEN '未知'
                    END AS 性别, b.birthday AS 生日, '' AS 最新诊断,
                    a.`thedate` AS 就诊日期, a.`content` AS 主诉
                    FROM `revisitrecords` a
                    INNER JOIN patients b ON a.`patientid` = b.`id`
                    INNER JOIN patienttags c ON a.patientid = c.patientid
                    WHERE a.doctorid=$doctorid
                    AND c.patienttagtplid={$exportJob->patienttagtplid}
                    ORDER BY b.name ASC, thedate ASC";
            } else {
                $sql = "SELECT b.name AS 患者,b.id AS 方寸患者ID,
                    CASE WHEN b.sex=1 THEN '男'
                    WHEN b.sex=2 THEN '女'
                    WHEN b.sex=0 THEN '未知'
                    END AS 性别, b.birthday AS 生日,
                    a.`thedate` AS 就诊日期, a.`content` AS 主诉
                    FROM `revisitrecords` a
                    INNER JOIN patients b ON a.`patientid` = b.`id`
                    INNER JOIN patienttags c ON a.patientid = c.patientid
                    WHERE a.doctorid=$doctorid
                    AND c.patienttagtplid={$exportJob->patienttagtplid}
                    ORDER BY b.name ASC, thedate ASC";
            }
        }
        $data = Dao::queryRows($sql);
        $arr = [];
        foreach ($data as $k => $v) {
            
            foreach ($v as $k1 => $v1) {
                if ($k1 == "方寸患者id") {
                    $v1 = $v1 . "\t";
                }
                //获取最新诊断信息
                if ($doctorid == 33 && $k1 == "最新诊断") {
                    $v1 = $this->getLatestZhenDuan($v["方寸患者id"], $doctorid);
                }
                $arr[$k][strtoupper($k1)] = $v1;
            }
        }
        $ret = [];
        $ret['主诉']['name'] = '主诉';
        $ret['主诉']['ename'] = 'zhusu';
        $ret['主诉']['list'] = $arr;

        return $ret;
    }

    private function getChemoData($exportJob, $doctorid) {
        if ($exportJob->patienttagtplid == 0) {
            $sql = "SELECT a.patientid, b.name as patientname,
                CASE WHEN b.sex=1 THEN '男'
                WHEN b.sex=2 THEN '女'
                WHEN b.sex=0 THEN '未知'
                END AS gender, b.birthday,
                a.hospital, a.startdate, a.pkg_name, a.type, a.stage, a.progress_reason, a.pkg_items, a.effect_name, a.effect_content, a.sideeffect_items, a.x_yes, a.x_startdate, a.x_part, a.x_type, a.x_dose, a.x_timespan
                FROM chemos a 
                INNER JOIN patients b ON a.patientid = b.id WHERE a.doctorid=$doctorid";
        } else {
            $sql = "SELECT a.patientid, c.name AS patientname, 
                CASE WHEN c.sex=1 THEN '男'
                WHEN c.sex=2 THEN '女'
                WHEN c.sex=0 THEN '未知'
                END AS gender, c.birthday,
                a.hospital, startdate, pkg_name, a.type, stage, progress_reason, pkg_items, effect_name, effect_content, sideeffect_items, x_yes, x_startdate, x_part, x_type, x_dose, x_timespan
                FROM chemos a 
                INNER JOIN patienttags b ON a.patientid=b.patientid
                INNER JOIN patients c ON a.patientid = c.id
                WHERE a.doctorid=$doctorid
                AND b.patienttagtplid={$exportJob->patienttagtplid}";
        }
        $data = Dao::queryRows($sql);
        $arr = [];
        static $dic = [
            'patientid' => '方寸患者ID',
            'patientname' => '患者姓名',
            'gender' => '性别',
            'birthday' => '生日',
            'hospital' => '化疗医院',
            'startdate' => '化疗开始时间',
            'pkg_name' => '化疗方案',
            'type' => '化疗性质',
            'stage' => '化疗疗程',
            'progress_reason' => '进展原因',
            'pkg_items' => '具体用药',
            'effect_name' => '疗效',
            'effect_content' => '评价依据',
            'sideeffect_items' => '不良反应',
            'x_yes' => '是否同步放疗',
            'x_startdate' => '放疗开始日期',
            'x_part' => '放疗部位',
            'x_type' => '放疗模式',
            'x_dose' => '放疗剂量',
            'x_timespan' => '放疗持续时间',
        ];
        if (empty($data)) {
            return [];
        }
        foreach ($data as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $name = $dic[$k1] ?? '';
                if (!$name) {
                    continue;
                }
                if ($k1 == 'pkg_items') {
                    $tmp = json_decode($v1, true);
                    $str = '';
                    foreach ($tmp as $a) {
                        $str .= $a['name'] . ' '. $a['method3'] . ' '. $a['pickedmethod4'] . ' '. $a['pickedtime'] . ' ' . $a['remark'] . "\r\n";
                    }
                    $str = rtrim($str);
                } else if ($k1 == 'sideeffect_items') {
                    $tmp = json_decode($v1, true);
                    $str = '';
                    foreach ($tmp as $a) {
                        $str .= implode(' ', $a) . "\r\n";
                    }
                    $str = rtrim($str);
                } else if ($k1 == 'x_yes') {
                    $str = $v1 == 1 ? '是' : '否';
                } else {
                    $str = $v1;
                }
                $arr[$k][$name] = $str;
            }
        }
        $ret = [];
        $ret['治疗']['name'] = '治疗';
        $ret['治疗']['ename'] = 'chemo';
        $ret['治疗']['list'] = $arr;

        return $ret;
    }

    //获取一个患者最新的诊断，徐雁专用
    private function getLatestZhenDuan($patientid, $doctorid) {
        if ($doctorid != '33') {
            return '';
        }
        if (isset($this->latestZhenduans[$patientid.'_'.$doctorid])) {
            //echo "从缓存读取", $this->latestZhenduans[$patientid.'_'.$doctorid], "\n";
            return $this->latestZhenduans[$patientid.'_'.$doctorid];
        }
        $checkuptpl = Dao::getEntityByCond('CheckupTpl', ' AND ename=:ename AND doctorid=:doctorid', [':ename'=>'zhenduan', ':doctorid'=>$doctorid]);
        $sql = "SELECT * FROM checkups
            WHERE checkuptplid=:checkuptplid AND patientid=:patientid
            ORDER BY id DESC LIMIT 1";
        $bind = [
            ':checkuptplid' => $checkuptpl->id,
            ':patientid' => $patientid
        ];
        $checkup = Dao::loadEntity('Checkup', $sql, $bind);
        if (!$checkup) {
            return '';
        }
        $answers = $checkup->xanswersheet->getAnswers();
        $answerContent = '';
        foreach ($answers as $answer) {
            $answerContent .= $this->getAnswerContent($answer) . ' ';
        }
        $this->latestZhenduans[$patientid.'_'.$doctorid] = trim($answerContent);
        return $this->latestZhenduans[$patientid.'_'.$doctorid];
    }

    private function getAnswerContent (XAnswer $answer) {
        $optionstr = '';
        $options = $answer->getTheXOptions();
        foreach ($options as $option) {
            $optionstr .= $option->content;
        }
        $content = $answer->content;
        $qualitative = $answer->qualitative != '请选择' ? $answer->qualitative : '';
        if ($qualitative == '其他') {
            $qualitative .= ' ' . $content;
            $content = '';
        }
        return $optionstr . ' ' . $content . ' ' . $qualitative;
    }

    private function formatData ($data) {
        $ret = [];
        foreach ($data as $one) {
            if (! trim($one['ename'])) {
                continue;
            }
            if (! isset($ret[$one['ename']])) {
                $ret[$one['ename']] = [];
            }
            $ret[$one['ename']]['ename'] = $one['ename'];
            $ret[$one['ename']]['name'] = $one['name'];
            if (! isset($ret[$one['ename']]['list'])) {
                $ret[$one['ename']]['list'] = [];
            }
            // $tmp['患者'] = $one['patientname'];
            $ret[$one['ename']]['list'] = array_merge($ret[$one['ename']]['list'], $one['qa']);
        }
        return $ret;
    }

    private function formatPatientInfoData($patientInfoData, $doctorid) {
        $sets = [];
        foreach ($patientInfoData as $disease => $list) {//不同疾病
            foreach ($list as $i => $one) {
                $one['id'] .= "\t";
                $one['prcrid'] .= "\t";
                $one['mobile'] .= "\t";
                $one['out_case_no'] .= "\t";
                $one['patientcardno'] .= "\t";
                $one['patientcard_id'] .= "\t";
//                 $one['bingan_no'] .= "\t";
                $set = [];
                $set[self::$maps['name']] = $one['name'];
                foreach ($one as $key => $a) {
                    $key0 = self::$maps[$key] ?? $key;
                    if ($key == 'name') {//处理name的问题
                        continue;
                    }
                    $set[$key0] = $a;
                }
                if ($doctorid == '33') {
                    $set[self::$maps['zhenduan']] = $this->getLatestZhenDuan(trim($one['id']), $doctorid);
                }
                $_key = $disease . "患者";
                if (!isset($sets[$_key])) {
                    $sets[$_key] = [];
                    $sets[$_key]['ename'] = 'patient';
                    $sets[$_key]['name'] = $_key;
                    $sets[$_key]['list'] = [];
                }
                $sets[$_key]['list'][] = $set;
            }
        }
        unset($patientInfoData);
        return $sets;
    }

    // 按量表维度导出
    private function exportData4Checkup ($data0, $patientData, $zhusuData, $chemoData, $jobid, $username) {
        // 为了让代码更容易看，在此多循环一次
        $data = array_merge($patientData, $zhusuData);
        if ($chemoData) {
            $data = array_merge($data, $chemoData);
        }
        $data = array_merge($data, $this->formatData($data0));

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("fangcun");
        $objPHPExcel->getProperties()->setTitle("fangcun doctor database");
        // 将数据写入到文件
        $i = 0;
        foreach ($data as $one) {
            if ($i > 0) {
                $workSheet = new PHPExcel_Worksheet($objPHPExcel, trim($one['name'])); // 创建一个工作表
                $objPHPExcel->addSheet($workSheet); // 插入工作表
            }
            $objPHPExcel->setActiveSheetIndex($i);
            $objPHPExcel->getActiveSheet()->setTitle(trim($one['name']));
            $lines = [];
            foreach ($one['list'] as $key => $a) {
                if ($key == 0) {
                    $titles = array_keys($a);
                    $lines[] = $titles;
                }
                $lines[] = array_values($a);
            }
            $objPHPExcel->getActiveSheet()->fromArray($lines, // 赋值的数组
NULL, // 忽略的值,不会在excel中显示
'A1'); // 赋值的起始位置

            $i ++;
        }
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $distDir = "/home/xdata/download/doctordb/{$username}";
        if (! is_dir($distDir)) {
            mkdir($distDir, 0755, true);
        }
        $fileName = md5($jobid);
        $objWriter->save($distDir . '/' . $fileName . '.xls');
    }

    // 按患者维度导出
    private function exportData4Patient ($data0) {
        // todo
    }
}

if ($argc < 2) {
    echo "usage: php " . basename(__FILE__) . " jobid\n";
    exit(1);
}

$id = $argv[1];
if (!$id) {
    echo "jobid ($id) is empty\n";
    exit(2);
}

$obj = new export_doctor_checkup();
$obj->run($id);

Debug::flushXworklog();

