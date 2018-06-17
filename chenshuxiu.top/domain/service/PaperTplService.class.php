<?php

// 创建: 20170626 by txj
//方寸儿童管理服务平台当前基本配置，其他疾病太多不显示了，当个用例。
/*
+------------+----------+---------------+-----------------------------+----------+-----------+
| papertplid | groupstr | ename         | title                       | doctorid | diseaseid |
+------------+----------+---------------+-----------------------------+----------+-----------+
|  100996299 | scale    | adhd_iv       | SNAP-IV评估                    |        0 |         1 |
|  179607836 | scale    | QCD           | 儿童困难问卷（QCD）         |        0 |         1 |
|  122759865 | scale    | sideeffectnew | 副反应评估                  |        0 |         1 |
+------------+----------+---------------+-----------------------------+----------+-----------+
*/
class PaperTplService
{
    //在患者微信端要展示的量表
    public static function getArrShowInWXByPatient(Patient $patient){
        $diseaseid = $patient->diseaseid;
        $doctorid = $patient->doctorid;

        //取到疾病通用量表
        $diseasepapertplrefs_1 = DiseasePaperTplRefDao::getListByDiseaseidDoctorid($diseaseid, 0, null, 1);
        //取到该疾病下医生的专属量表
        $diseasepapertplrefs_2 = DiseasePaperTplRefDao::getListByDiseaseidDoctorid($diseaseid, $doctorid, null, 1);
        $diseasepapertplrefs = array_merge($diseasepapertplrefs_1, $diseasepapertplrefs_2);

        $cnt = count($diseasepapertplrefs);
        //如果没有量表返回默认量表
        if($cnt == 0){
            return self::getDefaultArr();
        }

        //根据业务逻辑，找到不需要展示的量表
        $ignore_papertplid_arr = self::getIgnorePapertplidArr($patient);

        //清洗，过滤得到最后的数据
        $papertpls = self::filterArr($diseasepapertplrefs, $ignore_papertplid_arr);
        return $papertpls;
    }

    private static function getDefaultArr(){
        //SF-36 (健康状况调查表)
        $a = PaperTpl::getById(101457325);
        //医院焦虑和抑郁量表
        $b = PaperTpl::getById(103226070);
        return array($a,$b);
    }

    private static function filterArr($arr, $ignore_papertplid_arr){
        $temp = array();
        $result = array();
        foreach($arr as $a){
            $papertplid = $a->papertplid;
            $doctorid = $a->doctorid;
            if($doctorid == 0 && in_array($papertplid, $ignore_papertplid_arr)){
                continue;
            }
            if(false == in_array($papertplid, $temp)){
                $temp[] = $papertplid;
                $result[] = $a->papertpl;
            }
        }
        return $result;
    }

    private static function getIgnorePapertplidArr(Patient $patient){
        $ignore_papertplid_arr = [];

        $doctorid = $patient->doctorid;

        //李斐的患者过滤掉SNAP-IV评估
        if(in_array($doctorid, array(31))){
            $adhd_iv = PaperTplDao::getByEname("adhd_iv");
            $ignore_papertplid_arr[] = $adhd_iv->id;
        }

        //不在礼来合作的要过滤掉QCD
        $is_in_hezuo = $patient->isInHezuo("Lilly");
        if(false == $is_in_hezuo){
            $QCD = PaperTplDao::getByEname("QCD");
            $ignore_papertplid_arr[] = $QCD->id;
        }

        //过滤掉不适合填写副反应的
        $need_fill_sideeffect = self::needFillSideeffect($patient);
        if(false == $need_fill_sideeffect){
            $sideeffect = PaperTplDao::getByEname("sideeffectnew2");
            $ignore_papertplid_arr[] = $sideeffect->id;
        }else{
            //即使适合填写副反应，如果在礼来合作中，
            if($is_in_hezuo){
                $sideeffect = PaperTplDao::getByEname("sideeffectnew2");
                $ignore_papertplid_arr[] = $sideeffect->id;
            }
        }
        return $ignore_papertplid_arr;
    }

    private static function needFillSideeffect(Patient $patient){
        $need_fill_sideeffect = false;
        $drugsheet = DrugSheetDao::getOneByPatientid($patient->id, " order by thedate desc");
        if ($drugsheet instanceof DrugSheet) {
            $drugitems = DrugItemDao::getListByDrugsheetid($drugsheet->id, " and medicineid in (2,3) and value > 0");
            if (count($drugitems) > 0) {
                $need_fill_sideeffect = true;
            }
        }
        return $need_fill_sideeffect;
    }
}
