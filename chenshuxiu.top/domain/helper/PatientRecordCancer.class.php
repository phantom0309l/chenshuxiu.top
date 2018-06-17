<?php

/**
 * 肿瘤运营备注体系
 * @author fhw
 *
 */
class PatientRecordCancer
{
    /**
     * 肿瘤模板
     *
    诊断
    分期
    手术
    化疗方案
    不良反应
    评估
    失访
    死亡
    其他
     */
    public static function getPatientRecordTpls () {
        $arr = [];

        $arr['诊断(肿瘤)'] = "cancer/_diagnose.php";
        $arr['分期(肿瘤)'] = "cancer/_staging.php";
        $arr['手术(肿瘤)'] = "cancer/_operation.php";
        $arr['化疗方案(肿瘤)'] = "cancer/_chemo.php";
        $arr['不良反应(肿瘤)'] = "cancer/_untoward_effect.php";
        $arr['评估(肿瘤)'] = "cancer/_evaluate.php";
        $arr['血常规治疗(肿瘤)'] = "cancer/_wbc_treat.php";
        $arr['血常规(肿瘤)'] = "cancer/_wbc_checkup.php";
        $arr['基因检测(肿瘤)'] = "cancer/_genetic.php";
        $arr['标志物(肿瘤)'] = "cancer/_markers.php";

        return $arr;
    }

    public static function getOptionByCode ($code){
        $fun = $code."_options";
        return self::$$fun;
    }

    public static function getTitleByCode ($code){
        $fun = $code."_titles";
        return self::$$fun;
    }

    public static function getChemoOptionByPatientid ($patientid){
        $patientrecords = PatientRecordDao::getParentListByPatientidCodeType($patientid, 'cancer' ,'chemo');

        $arr = [
            '0' => '未知',
        ];
        foreach ($patientrecords as $a) {
            $arr[$a->id] = self::getShortDesc($a);
        }
        return $arr;
    }

    public static function fixOptionStr ($data, $name) {
        $optionstr = $data["{$name}"] == '其他' ? "其他:" . $data["{$name}_other"] : $data["{$name}"];

        $optionstr = $optionstr == 'not' ? '空' : $optionstr;

        return  trim($optionstr);
    }

    public static function getCheckboxStr ($data, $code, $code_other) {
        $itemstr = $data["{$code}"];
        if (false !== strpos($itemstr, '其他')) {
            $itemstr .= $data["{$code_other}"];
        }

        return $itemstr;
    }

    public static function getMultInputStr ($data, $code) {
        $items = PatientRecordCancer::getTitleByCode('markers');

        $str = "";
        $list = [];
        foreach ($items as $item) {
            $value = $data["{$item}"];

            $list[] = "{$item}:{$value}";
        }

        $str = implode(',', $list);

        return $str;
    }

    public static function getShortDesc(PatientRecord $patientrecord){
        $desc = "[{$patientrecord->thedate}] (肿瘤) ";

        $data = $patientrecord->loadJsonContent();

        switch ($patientrecord->type) {
            case 'markers':
                $str = self::getMultInputStr($data, 'markers');

                $desc .= "[内容 {$str}]";
                break;
            case 'genetic':
                $itemstr = self::getCheckboxStr($data, 'items', 'item_other');

                $desc .= "[检测日期 {$patientrecord->thedate}]";
                $desc .= "[内容 {$itemstr}]";
                break;

            case 'operation':
                $position_str = self::fixOptionStr($data, 'type');

                $desc .= "[手术日期 {$patientrecord->thedate}]";
                $desc .= "[性质 {$position_str}]";
                break;

            case 'staging':
                $desc .= "[分期日期 {$data['thedate']}] ";
                $desc .= "[{$data['type']}] ";
                $desc .= "[{$data['T']}] ";
                $desc .= "[{$data['N']}] ";
                $desc .= "[{$data['M']}] ";
                $desc .= "[{$data['stage']}期] ";
                break;

            case 'diagnose':
                $position_str = self::fixOptionStr($data, 'position');
                $diagnose_start_str = self::fixOptionStr($data, 'diagnose_start');
                $diagnose_start_str = $diagnose_start_str == '空' ? $diagnose_start_str: $diagnose_start_str  . '癌' ;
                $diagnose_special_str = self::fixOptionStr($data, 'special');
                $diagnose_shift_position_str = self::fixOptionStr($data, 'shift_position');

                $desc .= "[诊断日期 {$data['thedate']}] ";
                $desc .= "[{$position_str}] ";
                $desc .= "[{$diagnose_start_str}] ";
                $desc .= "[{$diagnose_special_str}] ";
                $desc .= "[转移日期 {$data['shift_thedate']}] ";
                $desc .= "[转移位置 {$diagnose_shift_position_str}] ";
                break;

            case 'chemo':
                $desc .= $data['protocol'];
                $desc .= $data['cycle'];
                $desc .= $data['property'];
                $desc .= $data['period'];
                break;

            case 'untoward_effect':
                $desc .= $data['name'];
                $desc .= $data['degree'];
                $desc .= '级';
                break;

            case 'evaluate':
                $desc .= '评估';
                $desc .= $data['assess'];
                break;

            case 'dead':
                $desc .= '死亡';
                break;

            case 'wbc_treat':
                $desc .= '血常规治疗';
                $desc .= $data['name'];
                break;

            case 'wbc_checkup':
                $desc .= '血常规';
                $baixibao = $data['baixibao'];
                $xuehongdanbai = $data['xuehongdanbai'];
                //                echo "xuehongdanbai";
                //                print_r($data['xuehongdanbai']);
                $xuexiaoban = $data['xuexiaoban'];
                $zhongxingli = $data['zhongxingli'];

                if($baixibao == ''){
                    $baixibao_desc = "未录入 ";
                }elseif ( $baixibao >= 4 ){
                    $baixibao_desc = " 0级";
                }elseif( $baixibao >= 3 ){
                    $baixibao_desc = " 1级";
                }elseif( $baixibao >= 2 ){
                    $baixibao_desc = " 2级";
                }elseif( $baixibao >= 1 ){
                    $baixibao_desc = " 3级";
                }elseif( $baixibao >= 0 && $baixibao !== '' ){
                    $baixibao_desc = " 4级";
                }else{
                    // 20170616 冯老师的诡异需求，白细胞不填，”白细胞”这三个字都不展示
                }
                $desc .= " 白细胞".$baixibao_desc;

                if($xuehongdanbai == ''){
                    $xuehongdanbai_desc = "未录入 ";
                }elseif ( $xuehongdanbai >= 120 ){
                    $xuehongdanbai_desc = " 0级";
                }elseif( $xuehongdanbai >= 100 ){
                    $xuehongdanbai_desc = " 1级";
                }elseif( $xuehongdanbai >= 80 ){
                    $xuehongdanbai_desc = " 2级";
                }elseif( $xuehongdanbai >= 65 ){
                    $xuehongdanbai_desc = " 3级";
                }elseif( $xuehongdanbai >= 0 ){
                    $xuehongdanbai_desc = " 4级";
                }else{
                    $xuehongdanbai_desc = " 未知级（原数为{$xuehongdanbai}）";
                }
                $desc .= " 血红蛋白".$xuehongdanbai_desc;

                if($xuexiaoban == ''){
                    $xuexiaoban_desc = "未录入 ";
                }elseif ( $xuexiaoban >= 100 ){
                    $xuexiaoban_desc = " 0级";
                }elseif( $xuexiaoban >= 75 ){
                    $xuexiaoban_desc = " 1级";
                }elseif( $xuexiaoban >= 50 ){
                    $xuexiaoban_desc = " 2级";
                }elseif( $xuexiaoban >= 25 ){
                    $xuexiaoban_desc = " 3级";
                }elseif( $xuexiaoban >= 0 ){
                    $xuexiaoban_desc = " 4级";
                }else{
                    $xuexiaoban_desc = " 未知级（原数为{$xuexiaoban}）";
                }
                $desc .= " 血小板". $xuexiaoban_desc;

                if($zhongxingli == ''){
                    $zhongxingli_desc = "未录入 ";
                }elseif ( $zhongxingli >= 2 ){
                    $zhongxingli_desc = " 0级";
                }elseif( $zhongxingli >= 1.5 ){
                    $zhongxingli_desc = " 1级";
                }elseif( $zhongxingli >= 1 ){
                    $zhongxingli_desc = " 2级";
                }elseif( $zhongxingli >= 0.5 ){
                    $zhongxingli_desc = " 3级";
                }elseif( $zhongxingli >= 0 ){
                    $zhongxingli_desc = " 4级";
                }else{
                    $zhongxingli_desc = " 未知级（原数为{$zhongxingli}）";
                }
                $desc .= " 中性粒细胞".$zhongxingli_desc;

                break;

            case 'lkf_checkup':
                $desc .= '肝肾功';
                $lkf_alt = $data['lkf_alt'];
                $lkf_alp = $data['lkf_alp'];
                $lkf_tbil = $data['lkf_tbil'];
                $lkf_cr = $data['lkf_cr'];

                if($lkf_alt == ''){
                    $lkf_alt_desc = "未录入 ";
                }elseif ( $lkf_alt >= 1000 ){
                    $lkf_alt_desc = "4级";
                }elseif( $lkf_alt >= 250 ){
                    $lkf_alt_desc = "3级";
                }elseif( $lkf_alt >= 125 ){
                    $lkf_alt_desc = "2级";
                }elseif( $lkf_alt >= 50 ){
                    $lkf_alt_desc = "1级";
                }elseif( $lkf_alt >= 0 ){
                    $lkf_alt_desc = "0级";
                }else{
                    $lkf_alt_desc = "未知级（原数为{$lkf_alt}）";
                }
                $desc .= " ALT ".$lkf_alt_desc;

                if($lkf_alp == ''){
                    $lkf_alp_desc = "未录入 ";
                }elseif ( $lkf_alp >= 2700 ){
                    $lkf_alp_desc = "4级";
                }elseif( $lkf_alp >= 675 ){
                    $lkf_alp_desc = "3级";
                }elseif( $lkf_alp >= 337.5 ){
                    $lkf_alp_desc = "2级";
                }elseif( $lkf_alp >= 135 ){
                    $lkf_alp_desc = "1级";
                }elseif( $lkf_alp >= 0 ){
                    $lkf_alp_desc = "0级";
                }else{
                    $lkf_alp_desc = "未知级（原数为{$lkf_alp}）";
                }
                $desc .= " ALP ".$lkf_alp_desc;

                if($lkf_tbil == ''){
                    $lkf_tbil_desc = "未录入 ";
                }elseif ( $lkf_tbil >= 222 ){
                    $lkf_tbil_desc = "4级";
                }elseif( $lkf_tbil >= 66.6 ){
                    $lkf_tbil_desc = "3级";
                }elseif( $lkf_tbil >= 33.3 ){
                    $lkf_tbil_desc = "2级";
                }elseif( $lkf_tbil >= 22.2 ){
                    $lkf_tbil_desc = "1级";
                }elseif( $lkf_tbil >= 0 ){
                    $lkf_tbil_desc = "0级";
                }else{
                    $lkf_tbil_desc = "未知级（原数为{$lkf_tbil}）";
                }
                $desc .= " TBIL ".$lkf_tbil_desc;

                if($lkf_cr == ''){
                    $lkf_cr_desc = "未录入 ";
                }elseif ( $lkf_cr >= 1040 ){
                    $lkf_cr_desc = "4级";
                }elseif( $lkf_cr >= 312 ){
                    $lkf_cr_desc = "3级";
                }elseif( $lkf_cr >= 156 ){
                    $lkf_cr_desc = "2级";
                }elseif( $lkf_cr >= 104 ){
                    $lkf_cr_desc = "1级";
                }elseif( $lkf_cr >= 0 ){
                    $lkf_cr_desc = "0级";
                }else{
                    $lkf_cr_desc = "未知级（原数为{$lkf_cr}）";
                }
                $desc .= " Cr ".$lkf_cr_desc;

                break;
            default:
                break;
        }
        $desc .= $patientrecord->content;

        return $desc;
    }

    // 标志物input的titles
    public static $markers_titles = [
        'CEA', 'AFP', 'CA125', 'CA153', 'CA199', 'CA242', 'CA724', 'NSE', 'proGRP', 'cyfra211', 'TPS', 'SccAg'
    ];

    // 基因检测
    private static $genetic_options = [
        'EGFR19缺失' => 'EGFR19缺失',
        'EGFRT790M' => 'EGFRT790M',
        'EGFRL858R' => 'EGFRL858R',
        'EML4-ALK融合突变' => 'EML4-ALK融合突变',
        'KRAS突变' => 'KRAS突变',
        'BRAF V600E突变' => 'BRAF V600E突变',
        'c-MET突变' => 'c-MET突变',
        'HER2突变' => 'HER2突变',
        'TP53突变' => 'TP53突变',
        'ROS1突变' => 'ROS1突变',
        'RET' => 'RET',
        'PIK3CA' => 'PIK3CA',
        'PTEN' => 'PTEN',
        'MSH' => 'MSH',
        'MLH' => 'MLH',
        '其他' => '其他'
    ];

    // 手术
    private static $operation_options = [
        'not' => '',
        '根治性手术' => '根治性手术',
        '姑息性手术' => '姑息性手术'
    ];

    private static $chemo_cycle_options = [
        '两周方案' => '两周方案',
        '三周方案' => '三周方案',
        '四周方案' => '四周方案',
        '六周方案' => '六周方案',
        '未知周期' => '未知周期'
    ];

    // 转移-转移位置 肝、肺、骨、脑
    private static $diagnose_shift_position_options = [
        '肝' => '肝',
        '肺' => '肺',
        '骨' => '骨',
        '脑' => '脑',
        '其他' => '其他'
    ];

    // 分期-分期 I IA IB IC II IIA IIB IIC III IIIA IIIB IIIC IV
    private static $staging_stage_options = [
        'I' => 'I',
        'IA' => 'IA',
        'IB' => 'IB',
        'IC' => 'IC',
        'II' => 'II',
        'IIA' => 'IIA',
        'IIB' => 'IIB',
        'IIC' => 'IIC',
        'III' => 'III',
        'IIIA' => 'IIIA',
        'IIIB' => 'IIIB',
        'IIIC' => 'IIIC',
        'IV' => 'IV',
        '非IV' => '非IV',
        '未知' => '未知'
    ];

    // 分期-M
    private static $staging_M_options = [
        'Mx' => 'Mx',
        'M0' => 'M0',
        'M1' => 'M1',
        'M1a' => 'M1a',
        'M1b' => 'M1b'
    ];

    // 分期-N
    private static $staging_N_options = [
        'Nx' => 'Nx',
        'N0' => 'N0',
        'N1' => 'N1',
        'N1a' => 'N1a',
        'N1b' => 'N1b',
        'N1c' => 'N1c',
        'N2' => 'N2',
        'N2a' => 'N2a',
        'N2b' => 'N2b',
        'N2c' => 'N2c',
        'N3' => 'N3',
        'N3a' => 'N3a',
        'N3b' => 'N3b',
        'N3c' => 'N3c'
    ];

    // 分期-T
    private static $staging_T_options = [
        'TX' => 'TX',
        'T0' => 'T0',
        'Tis' => 'Tis',
        'T1' => 'T1',
        'T1a' => 'T1a',
        'T1b' => 'T1b',
        'T1c' => 'T1c',
        'T2' => 'T2',
        'T2a' => 'T2a',
        'T2b' => 'T2b',
        'T2c' => 'T2c',
        'T3' => 'T3',
        'T3a' => 'T3a',
        'T3b' => 'T3b',
        'T3c' => 'T3c',
        'T4' => 'T4',
        'T4a' => 'T4a',
        'T4b' => 'T4b',
        'T4c' => 'T4c'
    ];

    // 分期-分期类型
    private static $staging_type_options = [
        'p' => 'p',
        'c' => 'c'
    ];

    // 诊断-特殊
    private static $diagnose_special_options = [
        'not' => '',
        'T细胞淋巴瘤' => 'T细胞淋巴瘤',
        'B细胞淋巴瘤' => 'B细胞淋巴瘤',
        '黑色素瘤' => '黑色素瘤',
        '其他' => '其他'
    ];

    // 诊断-组织起源
    private static $diagnose_start_options = [
        'not' => '',
        '腺' => '腺',
        '鳞' => '鳞',
        '小细胞' => '小细胞',
        '神经内分泌' => '神经内分泌',
        '印戒细胞' => '印戒细胞',
        '大细胞' => '大细胞',
        '腺鳞' => '腺鳞',
        '粘液腺' => '粘液腺',
        '浆液腺' => '浆液腺',
        '透明细胞' => '透明细胞',
        '其他' => '其他',
        '待确诊'=>'待确诊',
        '未知'=>'未知',
    ];

    // 诊断-部位
    private static $diagnose_position_options = [
        'not' => '',
        '鼻咽' => '鼻咽',
        '喉' => '喉',
        '食管' => '食管',
        '胰腺' => '胰腺',
        '胃' => '胃',
        '结肠' => '结肠',
        '直肠' => '直肠',
        '胆囊' => '胆囊',
        '胆管' => '胆管',
        '肺' => '肺',
        '乳腺' => '乳腺',
        '膀胱' => '膀胱',
        '肾' => '肾',
        '其他' => '其他',
        '待确诊'=>'待确诊',
        '未知'=>'未知',
    ];

    // 化疗方案
    private static $chemo_protocol_options = [
        '未知' => '未知',
        'TP方案' => 'TP方案',
        '吉西他滨' => '吉西他滨',
        'PE' => 'PE',
        'EP' => 'EP',
        'DP' => 'DP',
        'IP' => 'IP',
        '长春瑞滨+顺（卡）铂' => '长春瑞滨+顺（卡）铂',
        '培美曲塞+顺（卡）铂' => '培美曲塞+顺（卡）铂',
        '培美曲塞' => '培美曲塞',
        '多西紫杉醇+顺（卡）铂' => '多西紫杉醇+顺（卡）铂',
        '吉西他滨+顺（卡）铂' => '吉西他滨+顺（卡）铂',
        '阿帕替尼+培美曲塞+卡铂' => '阿帕替尼+培美曲塞+卡铂',
        '紫杉醇+奈达铂' => '紫杉醇+奈达铂',
        '依托泊苷+卡铂' => '依托泊苷+卡铂',
        'R-CHO' => 'R-CHO',
        'RCHOP-E' => 'RCHOP-E',
        'ABVD' => 'ABVD',
        '长春地辛+依托泊苷' => '长春地辛+依托泊苷',
        'FCR方案' => 'FCR方案',
        'TOX' => 'TOX',
        'TSOX' => 'TSOX',
        'FOLFOX6' => 'FOLFOX6',
        'SOX' => 'SOX',
        'XELOX' => 'XELOX',
        'TS' => 'TS',
        'TX' => 'TX',
        'DCF' => 'DCF',
        'D+FOLFOX6' => 'D+FOLFOX6',
        'IT' => 'IT',
        'CAPIRI' => 'CAPIRI',
        'FOLFOX' => 'FOLFOX',
        'Folfiri' => 'Folfiri',
        'AP' => 'AP',
        'NP' => 'NP',
        '健择' => '健择',
        'C225' => 'C225',
        '安维汀+AP' => '安维汀+AP',
        '多西他赛+阿帕替尼' => '多西他赛+阿帕替尼',
        '安维汀+FOLFOX' => '安维汀+FOLFOX',
        '安维汀+FOLFIRI' => '安维汀+FOLFIRI',
        '爱必妥+FOLFIRI' => '爱必妥+FOLFIRI',
        '爱必妥+FOLFOX' => '爱必妥+FOLFOX',
        '安维汀+XELOX' => '安维汀+XELOX',
        '希罗达' => '希罗达',
        '替吉奥' => '替吉奥',
        'GP' => 'GP',
        'DX' => 'DX',
        'PX' => 'PX',
        'GS' => 'GS',
        'CAP' => 'CAP',
        'AC' => 'AC',
        'PEB' => 'PEB',
        'GX' => 'GX',
        'TF' => 'TF',
        'TIEL' => 'TIEL',
        'BCD' => 'BCD',
        '其他' => '其他'
    ];

    private static $chemo_property_options = [
        '未知' => '未知',
        '新辅助' => '新辅助',
        '辅助' => '辅助',
        '晚期' => '晚期',
        '晚期一线' => '晚期一线',
        '晚期二线' => '晚期二线',
        '晚期三线' => '晚期三线',
        '晚期四线' => '晚期四线',
        '晚期五线' => '晚期五线',
        '靶向' => '靶向',
        '其他' => '其他',
    ];

    private static $chemo_period_options = [
        '未知' => '未知',
        '第一程' => '第一程',
        '第二程' => '第二程',
        '第三程' => '第三程',
        '第四程' => '第四程',
        '第五程' => '第五程',
        '第六程' => '第六程',
        '第七程' => '第七程',
        '第八程' => '第八程',
        '第九程' => '第九程',
        '第十程' => '第十程',
        '第十一程' => '第十一程',
        '第十二程' => '第十二程',
        '第十三程' => '第十三程',
        '第十四程' => '第十四程',
        '第十五程' => '第十五程',
        '第十六程' => '第十六程',
        '第十七程' => '第十七程',
        '第十八程' => '第十八程',
        '第十九程' => '第十九程',
        '第二十程' => '第二十程',
        '其他' => '其他',
    ];

    private static $untoward_effect_name_options = [
        'WBC' => 'WBC',
        'PLT' => 'PLT',
        'HGB' => 'HGB',
        '粒细胞' => '粒细胞',
        '粒缺发热' => '粒缺发热',
        '出血' => '出血',
        '转氨酶ALT' => '转氨酶ALT',
        'ALP' => 'ALP',
        '总胆红素' => '总胆红素',
        '肌酐Cr' => '肌酐Cr',
        '蛋白尿' => '蛋白尿',
        '血尿' => '血尿',
        '恶心' => '恶心',
        '呕吐' => '呕吐',
        '腹泻' => '腹泻',
        '便秘' => '便秘',
        '黏膜炎' => '黏膜炎',
        '脱发' => '脱发',
        '肺' => '肺',
        '周围神经' => '周围神经',
        '头痛' => '头痛',
        '皮肤' => '皮肤',
        '手足' => '手足',
        '过敏' => '过敏',
        '体重下降' => '体重下降',
        '心率异常' => '心率异常',
        '心功能' => '心功能',
        '心肌缺血' => '心肌缺血',
        '高血压' => '高血压',
        '甲沟炎' => '甲沟炎',
        '结膜炎' => '结膜炎',
        '其他' => '其他',
    ];

    private static $untoward_effect_degree_options = [
        '-1' => '未知',
        '0' => '0',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
    ];

    private static $evaluate_class_options = [
        '新辅助' => '新辅助',
        '辅助' => '辅助',
        'RECIST' => 'RECIST',
    ];

    private static $evaluate_assess_1_options = [ // 对应  新辅助
        '完全退缩' => '完全退缩',
        '部分退缩' => '部分退缩',
        '稳定' => '稳定',
        '进展' => '进展',
    ];

    private static $evaluate_assess_2_options = [ // 对应  辅助
        'DFS' => 'DFS',
        'PD' => 'PD',
    ];

    private static $evaluate_assess_3_options = [ // 对应  RECIST
        'CR' => 'CR',
        'PR' => 'PR',
        'SD' => 'SD',
        'PD' => 'PD',
    ];
}
