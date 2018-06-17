<?php

class DoctorServiceOrderService
{
    public static function createDoctorServiceOrders(Doctor $doctor, $from_date, $end_date){
        //已经生成过，不再生成
        $doctorserviceorders = DoctorServiceOrderDao::getListByDoctorFrom_dateEnd_date($doctor, $from_date, $end_date);
        if(count($doctorserviceorders)){
            return;
        }

        $from_date_sub = substr($from_date, 0, 7);
        $end_date_sub = substr($end_date, 0, 7);
        //周没有跨月
        if($from_date_sub == $end_date_sub){
            $l_date = $from_date;
            $r_date = $end_date;
            self::createDoctorServiceOrdersImp($doctor, $l_date, $r_date, $from_date, $end_date, false);
        }else{
            $l_date = $from_date;
            $r_date = $end_date_sub . "-01";
            $r_date = date("Y-m-d", strtotime($r_date)-86400);
            self::createDoctorServiceOrdersImp($doctor, $l_date, $r_date, $from_date, $end_date, true);

            $l_date = $end_date_sub . "-01";
            $r_date = $end_date;
            self::createDoctorServiceOrdersImp($doctor, $l_date, $r_date, $from_date, $end_date, false);
        }
    }

    public static function createDoctorServiceOrdersImp(Doctor $doctor, $l_date, $r_date, $from_date, $end_date, $need_yuejie){

        $themonth = substr($l_date, 0, 7) . "-01";
        //已经生产足够的order了不再生成
        $amount_yuan = $doctor->getDoctorServiceOrdersAmount_yuan($themonth);
        if($amount_yuan >= 800){
            return;
        }

        $amount_yuanForGenerateOrder = self::getAmount_yuanForGenerateOrder($doctor, $l_date, $r_date, $need_yuejie);

        if($amount_yuanForGenerateOrder == 0){
            return;
        }

        if($amount_yuan + $amount_yuanForGenerateOrder > 800){
            $amount_yuanForGenerateOrder = 800 - $amount_yuan;
        }

        $preOrderArr = self::createPreOrderArr($doctor, $amount_yuanForGenerateOrder);
        foreach($preOrderArr as $a){
            if(empty($a)){
                continue;
            }
            $row = array();
            $row["doctorid"] = $doctor->id;

            $doctorServiceOrderTpl = self::getDoctorServiceOrderTpl($a);
            if(false == $doctorServiceOrderTpl instanceof DoctorServiceOrderTpl){
                continue;
            }
            $row["doctorserviceordertplid"] = $doctorServiceOrderTpl->id;
            $obj = self::getObj($a);
            $row["objtype"] = get_class($obj);
            $row["objid"] = $obj->id;

            $row["objcode"] = $doctorServiceOrderTpl->ename;
            $row["amount"] = $doctorServiceOrderTpl->price;
            $row["the_month"] = $themonth;
            $row["week_from_begin"] = XDateTime::getWFromFirstDate($from_date);
            $row["from_date"] = $from_date;
            $row["end_date"] = $end_date;

            DoctorServiceOrder::createByBiz($row);
        }
    }

    //获取要生成order的钱数
    private static function getAmount_yuanForGenerateOrder(Doctor $doctor, $l_date, $r_date, $need_yuejie=false){
        $amount_yuanForGenerateOrder = 0;
        if($need_yuejie){
            $themonth = substr($l_date, 0, 7) . "-01";
            //当月收益
            $shouyi = $doctor->getShouyiOfTheMonth($themonth);
            //当收益大于800时按照800算
            if($shouyi > 800){
                $shouyi = 800;
            }
            //已经生成order的钱数
            $amount_yuanAlreadyGenerated = $doctor->getDoctorServiceOrdersAmount_yuan($themonth);
            $amount_yuanForGenerateOrder = $shouyi - $amount_yuanAlreadyGenerated;
            if($amount_yuanForGenerateOrder < 0){
                Debug::warn("doctor[{$doctor->id}]themonth[{$themonth}]该月最终收益小于已生成的order钱数");
                $amount_yuanForGenerateOrder = 0;
            }

        }else{
            //周的收益，只算shoporder的
            $shouyi = $doctor->getShopOrderShouyi($l_date, $r_date);
            //不在月结的周里，最多结算200
            if($shouyi > 200){
                $shouyi = 200;
            }
            $amount_yuanForGenerateOrder = $shouyi;
        }
        //不足10直接返回
        if($amount_yuanForGenerateOrder < 10){
            return 0;
        }

        //修正，返回个位是0的整数，或者15;
        $amount_yuanForGenerateOrder = self::fixNum($amount_yuanForGenerateOrder);

        return $amount_yuanForGenerateOrder;
    }

    private static function getDoctorServiceOrderTpl($a){
        $enameArr = array(
            "Patient" => "pipe_audit",
            "Faq" => "faq_audit",
            "Paper" => "scale_audit",
            "StudyPlan" => "train_audit"
        );
        $ename = $enameArr[get_class($a)] ? $enameArr[get_class($a)] : "";
        return DoctorServiceOrderTplDao::getOneByEname($ename);
    }

    private static function getObj($a){
        if($a instanceof Paper || $a instanceof StudyPlan){
            $a = $a->patient;
        }
        return $a;
    }

    //基于收益生成order
    private static function createPreOrderArr(Doctor $doctor, $amount_yuan){
        $result = array();
        $doctorid = $doctor->id;

        if($amount_yuan < 100){
            $result = self::getPlanArrForLtHundred($amount_yuan, $doctorid);
        }else{
            $hundredCnt = floor($amount_yuan/100);
            $leftNum =  $amount_yuan % 100;

            $result1 = self::getPlanArrByHundredCnt($hundredCnt, $doctorid);
            $result2 = array();
            if($leftNum > 0){
                $result2 = self::getPlanArrForLtHundred($leftNum, $doctorid);
            }
            $result = array_merge($result1, $result2);
        }
        return $result;
    }

    private static function getPlanArrForLtHundred($num, $doctorid){
        $result = array();
        $pipePatients = self::getPipePatients($doctorid);
        $pipePatientCnt = count($pipePatients);
        $plan = self::getPlanByNum($num, $pipePatientCnt);
        if($plan == null){
            return $result;
        }
        foreach($plan as $type => $n){
            $temp = array();
            if($type == 10){
                $temp = self::getPipePatients($doctorid, $n);
            }else{
                $temp = self::getFaqs($n);
            }
            $result = array_merge($result, $temp);
        }
        return $result;
    }

    private static function getPlanByNum($num, $pipePatientCnt){
        $plan = null;
        $planArr = self::planArr();
        $arr = $planArr[$num];
        $cntArr = $arr[0];
        $randArr = self::getRandArrForSelectPlan($cntArr, $pipePatientCnt);
        $randArrCnt = count($randArr);
        if($randArrCnt){
            $n = rand(0,$randArrCnt-1);
            $planIndex = $randArr[$n];
            $plan = $arr[$planIndex];
        }
        return $plan;
    }

    private static function getRandArrForSelectPlan($cntArr, $pipePatientCnt){
        $temp = array();
        foreach($cntArr as $i => $num){
            if($pipePatientCnt >= $num){
                $temp[] = ($i+1);
            }
        }
        return $temp;
    }

    private static function getPlanArrByHundredCnt($hundredCnt, $doctorid){
        $result = array();
        $papers = self::getPapers($doctorid);
        $paperCnt = count($papers);
        $studyplans = self::getStudyPlans($doctorid);
        $studyplanCnt = count($studyplans);

        $cnt = $paperCnt + $studyplanCnt;
        $diff = $hundredCnt - $cnt;
        //paper 和 studyplan足够生成
        if($diff <= 0){
            $result = self::getRandPaperStudyplanArr($papers, $studyplans, $hundredCnt);
        }else{
            //paper 和 studyplan不足够生成，需要faq和pipepatient补
            $result1 = self::getRandPaperStudyplanArr($papers, $studyplans, $cnt);
            $result2 = self::getPipepatientFaqArrByHundredCnt($diff, $doctorid);
            $result = array_merge($result1, $result2);
        }
        return $result;
    }

    private static function getRandPaperStudyplanArr($papers, $studyplans, $cnt){
        $result = array();
        if($cnt <= 0){
            return $result;
        }
        $result = array_merge($papers, $studyplans);
        shuffle($result);
        $result = array_slice($result,0,$cnt);
        return $result;
    }

    private static function getPipepatientFaqArrByHundredCnt($hundredCnt, $doctorid){
        $result = array();
        $pipePatients = self::getPipePatients($doctorid);
        $pipePatientCnt = count($pipePatients);

        if($pipePatientCnt == 0){
            return $result;
        }

        if($pipePatientCnt > $hundredCnt){
            $pipePatientCnt = $hundredCnt;
        }

        for($i = 0; $i < $pipePatientCnt; $i++){
            $faqs = self::getFaqs(6);
            $patients = self::getPipePatients($doctorid, 1);
            $temp = array_merge($faqs, $patients);
            $result = array_merge($result, $temp);
        }
        return $result;
    }

    //获取最近流活跃的患者，用于生成『患者交流审查』
    private static function getPipePatients($doctorid, $cnt = 10){
        $cond = " and doctorid = :doctorid and status = 1 order by lastpipe_createtime desc limit {$cnt}";
        $bind = [];
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityListByCond('Patient', $cond, $bind);
    }

    //获取需要的faq条数，用于FAQ审查
    private static function getFaqs($num){
        $arr = range(1,54);
        shuffle($arr);
        $arr = array_slice($arr,0,$num);
        $id_str = implode(",", $arr);
        $cond = " and id in ({$id_str})";
        return Dao::getEntityListByCond('Faq', $cond);
    }

    //获取最近一次填写量表的患者
    private static function getPapers($doctorid, $cnt = 8){
        $patient = null;
        $cond = " and ename = 'adhd_iv' and doctorid = :doctorid order by id desc limit {$cnt}";
        $bind = [];
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityListByCond("Paper", $cond, $bind);
    }

    //获取最近一次做行为训练课程的患者
    private static function getStudyPlans($doctorid, $cnt = 8){
        $patient = null;
        $cond = " and doctorid = :doctorid and objcode = 'test' and done_cnt > 0 order by id desc limit {$cnt}";
        $bind = [];
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityListByCond('StudyPlan', $cond, $bind);
    }

    //获取拆分方案
    public static function planArr(){
        $arr = array(
            10 => array(
                array(1),
                array(
                    10 => 1
                )
            ),
            15 => array(
                array(0),
                array(
                    15 => 1
                )
            ),
            20 => array(
                array(2),
                array(
                    10 => 2
                )
            ),
            30 => array(
                array(3,0),
                array(
                    10 => 3
                ),
                array(
                    15 => 2
                )
            ),
            40 => array(
                array(4,1),
                array(
                    10 => 4
                ),
                array(
                    10 => 1,
                    15 => 2
                )
            ),
            50 => array(
                array(5,2),
                array(
                    10 => 5
                ),
                array(
                    10 => 2,
                    15 => 2
                )
            ),
            60 => array(
                array(6,3,0),
                array(
                    10 => 6
                ),
                array(
                    10 => 3,
                    15 => 2
                ),
                array(
                    15 => 4
                )
            ),
            70 => array(
                array(7,4,1),
                array(
                    10 => 7
                ),
                array(
                    10 => 4,
                    15 => 2
                ),
                array(
                    10 => 1,
                    15 => 4
                )
            ),
            80 => array(
                array(8,5,2),
                array(
                    10 => 8
                ),
                array(
                    10 => 5,
                    15 => 2
                ),
                array(
                    10 => 2,
                    15 => 4
                )
            ),
            90 => array(
                array(9,6,3,0),
                array(
                    10 => 9
                ),
                array(
                    10 => 6,
                    15 => 2
                ),
                array(
                    10 => 3,
                    15 => 4
                ),
                array(
                    15 => 6
                )
            )
        );
        return $arr;
    }

    //修正数字
    private static function fixNum($num){
        if($num == 15){
            return $num;
        }
        $num = floor($num);
        $arr = str_split($num);
        $arr[count($arr)-1] = 0;
        return join("", $arr);
    }

}
