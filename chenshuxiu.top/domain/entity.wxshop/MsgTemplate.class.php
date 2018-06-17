<?php
// 消息模板
// 和微信模板消息模板不是一回事
class MsgTemplate extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'ename',  // ename
            'diseaseid',  // diseaseid
            'doctorid',  // doctorid
            'title',  // 标题
            'content'); // 内容
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'diseaseid',
            'doctorid');
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
    }

    // $row = array();
    // $row["ename"] = $ename;
    // $row["diseaseid"] = $diseaseid;
    // $row["doctorid"] = $doctorid;
    // $row["title"] = $title;
    // $row["content"] = $content;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "MsgTemplate::createByBiz row cannot empty");

        $default = array();
        $default["ename"] = '';
        $default["diseaseid"] = 0;
        $default["doctorid"] = 0;
        $default["title"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getEnameArr() {
        return array(
            'common_str' => array(
                '#patient_name#' => '患者姓名',
                '#hospital_name#' => '医院名',
                '#doctor_name#' => '医生名',
                '#disease_name#' => '疾病名',
                ),
            'ename_type' => array(
                'register_pass' => array(
                    'ename' => 'register_pass',
                    'title' => '审核通过',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'register_refuse' => array(
                    'ename' => 'register_refuse',
                    'title' => '审核拒绝',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'wait_register' => array(
                    'ename' => 'wait_register',
                    'title' => '等待审核',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'submit_diary' => array(
                    'ename' => 'submit_diary',
                    'title' => '日记提交',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'submit_revisittkt' => array(
                    'ename' => 'submit_revisittkt',
                    'title' => '预约申请提交',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'success_revisittkt' => array(
                    'ename' => 'success_revisittkt',
                    'title' => '预约审核通过',
                    'send_by_custom' => true,
                    'send_by_template' => true,
                    'wxtemplate_enames' => array('doctornotice'),
                    'description' => '#revisittkt_time# => 预约时间'),
                'adhd_subscribe' => array(
                    'ename' => 'adhd_subscribe',
                    'title' => '方寸儿童管理服务平台关注',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '#baodao_url# => 报到链接'),
                'lilly_register_pass' => array(
                    'ename' => 'lilly_register_pass',
                    'title' => '(礼来)审核通过',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'lilly_wait_register' => array(
                    'ename' => 'lilly_wait_register',
                    'title' => '(礼来)等待审核',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'lilly_register_pass_byAuditor' => array(
                    'ename' => 'lilly_register_pass_byAuditor',
                    'title' => '(礼来)运营审核通过',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'cuibaodao_d1' => array(
                    'ename' => 'cuibaodao_d1',
                    'title' => '催昨晚18～今早6点',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'cuibaodao_d2' => array(
                    'ename' => 'cuibaodao_d2',
                    'title' => '催昨早6～今早6点',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '#baodao_url# => 报到链接'),
                'cuibaodao_d3' => array(
                    'ename' => 'cuibaodao_d3',
                    'title' => '催7周以上患者',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '#baodao_url# => 报到链接'),
                'cuibaodao_m1' => array(
                    'ename' => 'cuibaodao_m1',
                    'title' => '催当天[6,18)',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'cuibaodao_m2' => array(
                    'ename' => 'cuibaodao_m2',
                    'title' => '催[47小时30分,48小时)的扫码关注',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '#baodao_url# => 报到链接'),
                'lilly_cuibaodao_d1' => array(
                    'ename' => 'lilly_cuibaodao_d1',
                    'title' => '(礼来)催昨晚18～今早6点',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'lilly_cuibaodao_d2' => array(
                    'ename' => 'lilly_cuibaodao_d2',
                    'title' => '(礼来)催昨早6～今早6点',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '#baodao_url# => 报到链接'),
                'lilly_cuibaodao_d3' => array(
                    'ename' => 'lilly_cuibaodao_d3',
                    'title' => '(礼来)催7周以上患者',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '#baodao_url# => 报到链接'),
                'lilly_cuibaodao_m1' => array(
                    'ename' => 'lilly_cuibaodao_m1',
                    'title' => '(礼来)催当天[6,18)',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '无'),
                'lilly_cuibaodao_m2' => array(
                    'ename' => 'lilly_cuibaodao_m2',
                    'title' => '(礼来)催[47小时30分,48小时)的扫码关注',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '#baodao_url# => 报到链接'),
                'lilly_drugscalenotice_urge' => array(
                    'ename' => 'lilly_drugscalenotice_urge',
                    'title' => '(礼来)催用药及评估',
                    'send_by_custom' => false,
                    'send_by_template' => true,
                    'wxtemplate_enames' => array('adminNotice'),
                    'description' => '无'),
                'lilly_drugscalenotice_remind' => array(
                    'ename' => 'lilly_drugscalenotice_remind',
                    'title' => '(礼来)催用药及评估3天后提醒',
                    'send_by_custom' => false,
                    'send_by_template' => true,
                    'wxtemplate_enames' => array('adminNotice'),
                    'description' => '#str_fix# => 用药|评估|用药和评估'),
                'lilly_drugscalenotice_warnning' => array(
                    'ename' => 'lilly_drugscalenotice_warnning',
                    'title' => '(礼来)催用药及评估21天后警告',
                    'send_by_custom' => false,
                    'send_by_template' => true,
                    'wxtemplate_enames' => array('adminNotice'),
                    'description' => '#str_fix# => 用药|评估|用药和评估'),
                'lilly_autoout' => array(
                    'ename' => 'lilly_autoout',
                    'title' => '(礼来)自动出组',
                    'send_by_custom' => false,
                    'send_by_template' => true,
                    'wxtemplate_enames' => array('adminNotice'),
                    'description' => '无'),
                'lilly_notactiveout' => array(
                    'ename' => 'lilly_notactiveout',
                    'title' => '(礼来)不活跃出组',
                    'send_by_custom' => false,
                    'send_by_template' => true,
                    'wxtemplate_enames' => array('adminNotice'),
                    'description' => '无'),
                'add_pgroup' => array(
                    'ename' => 'add_pgroup',
                    'title' => '患者入组',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '#pgroup_name# => 组名'),
                'lilly_add_pgroup' => array(
                    'ename' => 'lilly_add_pgroup',
                    'title' => '(礼来)患者入组',
                    'send_by_custom' => true,
                    'send_by_template' => false,
                    'wxtemplate_enames' => array(),
                    'description' => '#pgroup_name# => 组名'),
                'pgroup_join_notice' => array(
                    'ename' => 'pgroup_join_notice',
                    'title' => '提醒患者入组',
                    'send_by_custom' => false,
                    'send_by_template' => true,
                    'wxtemplate_enames' => array('adminNotice'),
                    'description' => '#lesson_cnt# => 课文数, #detail_str# => 第几节第几天'),
                'lilly_pgroup_join_notice' => array(
                    'ename' => 'lilly_pgroup_join_notice',
                    'title' => '(礼来)提醒患者入组',
                    'send_by_custom' => false,
                    'send_by_template' => true,
                    'wxtemplate_enames' => array('adminNotice'),
                    'description' => '#lesson_cnt# => 课文数, #detail_str# => 第几节第几天'),
                'pgroup_hwk_notice' => array(
                    'ename' => 'pgroup_hwk_notice',
                    'title' => '提醒患者完成作业',
                    'send_by_custom' => false,
                    'send_by_template' => true,
                    'wxtemplate_enames' => array('adminNotice'),
                    'description' => '#day# => 天数, #hour# => 小时'),
                'lilly_pgroup_hwk_notice' => array(
                    'ename' => 'lilly_pgroup_hwk_notice',
                    'title' => '(礼来)提醒患者完成作业',
                    'send_by_custom' => false,
                    'send_by_template' => true,
                    'wxtemplate_enames' => array('adminNotice'),
                    'description' => '#day# => 天数, #hour# => 小时'),
                'lilly_patient_survey' => array(
                    'ename' => 'lilly_patient_survey',
                    'title' => '(礼来)合作患者满意度调查问卷',
                    'send_by_custom' => false,
                    'send_by_template' => true,
                    'wxtemplate_enames' => array('adminNotice'),
                    'description' => '无'),
                ),
        );
    }
}
