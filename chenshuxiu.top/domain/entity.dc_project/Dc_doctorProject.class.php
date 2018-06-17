<?php

/*
 * Dc_doctorProject
 */
class Dc_doctorProject extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'title',  // 项目名称(冗余dc_project,如果没有填写的话)
            'dc_projectid',  // dc_projectid
            'doctorid',  // doctorid
            'begin_date',  // 项目开始日期
            'end_date',  // 项目结束日期
            'frequency',  // 频次(例如:3 单位：天/次)
            'period',  // 周期(例如:20 单位：天)
            'papertplids',  // 量表模板ids
            'is_auto_open_next',  // 自动开启下一次开关(0：不自动开启， 1：自动开启)
            'content',  // 备注
            'send_content_tpl',  // 发送消息模板
            'bulletin',  // 公告
            'dc_doctorproject_status',  // 项目状态（0：进行中，1：已完成，2：已停止）
            'create_auditorid' // 创建人
);
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'dc_projectid',
            'doctorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["dc_project"] = array(
            "type" => "Dc_project",
            "key" => "dc_projectid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["create_auditor"] = array(
            "type" => "Auditor",
            "key" => "create_auditorid");
    }

    // $row = array();
    // $row["title"] = $title;
    // $row["dc_projectid"] = $dc_projectid;
    // $row["doctorid"] = $doctorid;
    // $row["begin_date"] = $begin_date;
    // $row["end_date"] = $end_date;
    // $row["frequency"] = $frequency;
    // $row["period"] = $period;
    // $row["papertplids"] = $papertplids;
    // $row["is_auto_open_next"] = $is_auto_open_next;
    // $row["content"] = $content;
    // $row["send_content_tpl"] = $send_content_tpl;
    // $row["bulletin"] = $bulletin;
    // $row["dc_doctorproject_status"] = $dc_doctorproject_status;
    // $row["create_auditorid"] = $create_auditorid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Dc_doctorProject::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["dc_projectid"] = 0;
        $default["doctorid"] = 0;
        $default["begin_date"] = '';
        $default["end_date"] = '';
        $default["frequency"] = 0;
        $default["period"] = 0;
        $default["papertplids"] = '';
        $default["is_auto_open_next"] = 0;
        $default["content"] = '';
        $default["send_content_tpl"] = '';
        $default["bulletin"] = '';
        $default["dc_doctorproject_status"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getStatusStr () {
        $row = [
            '0' => '进行中',
            '1' => '已完成',
            '2' => '已停止'];

        return $row[$this->dc_doctorproject_status];
    }

    public function getPaperTplTitleStr () {
        if (! $this->papertplids) {
            return '';
        }
        $papertplids = explode(',', $this->papertplids);

        $titlestr = '';
        foreach ($papertplids as $i => $id) {
            $papertpl = PaperTpl::getById($id);

            if ($papertpl instanceof PaperTpl) {
                $i ++;
                $titlestr .= "{$i}:<a target=\"_blank\" href=\"/xquestionsheetmgr/one?xquestionsheetid={$papertpl->xquestionsheetid}\">{$papertpl->title}</a><br>";
            }
        }

        return $titlestr;
    }

    // 获取dc_patientplan数量
    public function getDc_patientplanCnt () {
        $dc_patientplans = Dc_patientPlanDao::getListByDc_doctorproject($this);

        return count($dc_patientplans);
    }
}
