<?php

class IDCard {

    public static function isIDCardNo($vStr){
        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)){
            return false;
        }
        $vLength = strlen($vStr);
        if ($vLength == 18) {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }
        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday){
            return false;
        }
        if ($vLength == 18) {
            // 取出本体码
            $idcard_base = substr($vStr, 0, 17);

            // 取出校验码
            $verify_code = strtoupper( substr($vStr, 17, 1) );

            // 加权因子
            $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

            // 校验码对应值
            $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

            // 根据前17位计算校验码
            $total = 0;
            for($i=0; $i<17; $i++){
                $total += substr($idcard_base, $i, 1)*$factor[$i];
            }

            // 取模
            $mod = $total % 11;

            // 比较校验码
            if($verify_code == $verify_code_list[$mod]){
                return true;
            }else{
                return false;
            }
        }
        return true;
    }

    public static function getSex($no = ""){
        //1男，2女, 0未知
        if($no == ""){
            return 0;
        }
        $len = strlen($no);
        if($len == 18){
            $sex_str = substr($no, 16, 1);
        }else{
            $sex_str = substr($no, 14, 1);
        }

        return ($sex_str % 2 == 0) ? 2 : 1;
    }

    public static function getBirthday($no = "", $split = "-"){
        if($no == ""){
            return "";
        }
        $len = strlen($no);
        if($len == 18){
            $str = substr($no, 6, 8);
            $str1 = substr($str,0,4);
            $str2 = substr($str,4,2);
            $str3 = substr($str,6,2);
        }else{
            $str = substr($no, 6, 6);
            $str1 = "19" . substr($str,0,2);
            $str2 = substr($str,2,2);
            $str3 = substr($str,4,2);
        }
        $arr = array($str1, $str2, $str3);
        return implode($split, $arr);
    }

}
