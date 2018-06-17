<?php

// 创建: 20180117 by lijie
class DrugItemService
{
    public static function getDetail ($patient, $type) {
        $begin_date = self::getBeginDate($type);
        $patientmedicinerefs = $patient->getAllPatientMedicineRefs();
        $result = array();
        foreach ($patientmedicinerefs as $a) {
            $medicine = $a->medicine;
            if( false == $medicine instanceof Medicine || $medicine->name == "其他" || $medicine->groupstr == "其他" ){
                continue;
            }
            $arr = self::getDrugItemArr($a, $begin_date);
            if( count($arr["value"]) > 0 ){
                $result[] = $arr;
            }
        }

        return $result;
    }

    private static function getDrugItemArr( PatientMedicineRef $patientmedicineref, $begin_date = null ){
        $arr = array();
        $left_arr = array();
        $right_arr = array();
        $medicine = $patientmedicineref->medicine;
        if( false == $medicine instanceof Medicine ){
            return $arr;
        }
        $arr["name"] = $medicine->name;
        $arr["unit"] = $medicine->unit;
        $arr["value"] = array();
        $arr["date"] = array();
        //根据record_date，id逆序取出
        $drugitems = $patientmedicineref->getDrugItems();
        $len = count($drugitems);
        foreach ($drugitems as $i => $a) {
            $date = substr($a->record_date, 0, 10);
            //record_date时间线上最后一条展示
            if( $i == 0 ){
                $left_arr["date"] = date("Y-m-d", time());
                $left_arr["value"] = $a->value;
            }

            //开始用药日期 大于begin_date
            if( $begin_date && ($len == $i+1) && strtotime($date) > strtotime($begin_date) ){
                $right_arr["date"] = $date;
                $right_arr["value"] = 0;
            }

            if( $begin_date && strtotime($date) < strtotime($begin_date) ){
                $right_arr["date"] = $date;
                $right_arr["value"] = $a->value;
                break;
            }
            if( false == in_array($date, $arr["date"]) ){
                $arr["value"][] = $a->value;
                $arr["date"][] = $date;
            }

        }

        //用于图表展示给DrugItemArr头尾各补一个值
        //因为是倒序
        //数组左边直接补当前最后的值
        //数组右边要做些判断，判断当前头部是不是患者填写的第一条记录
        if($begin_date){
            if( count($right_arr) > 0 ){
                $v1 = $right_arr["value"];
                $v2 = end($arr["value"]);
                //两个值不相等时做操作
                if( $v1 != $v2 ){
                    $arr["value"][] = $right_arr["value"];
                    $arr["date"][] = $right_arr["date"];
                }
            }

            array_unshift($arr["value"], $left_arr["value"]);
            array_unshift($arr["date"], $left_arr["date"]);
        }
        $arr["value"] = array_reverse( $arr["value"] );
        $arr["date"] = array_reverse( $arr["date"] );
        return $arr;
    }

    private static function getBeginDate($type){
        $map = array(
            1 => 90,
            2 => 180,
            3 => 360
         );
         $t = time() - 86400*$map[$type];
         return Date("Y-m-d", $t);
    }
}
