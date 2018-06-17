<?php
/*
 * Pgroup
 */
class Pgroup extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'name',  // 分组名称
            'ename',  // 分组名称英文名称
            'typestr',
            'subtypestr',
            'diseaseid',  // 疾病id
            'doctorid',
            'courseid',  // 课程id
            'inpapertplid',  // 入组量表模板id
            'outpapertplid',  // 出组模板id
            'daycnt',  // 在组天数
            'showinaudit',  // 是否在后台显示
            'showinwx',  // 是否在微信端显示
            'refer_pgroupids', //本组相关推荐组，逗号分开
            'level', // 组的优先级
            'summary'); // 出组作业点评

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["course"] = array(
            "type" => "Course",
            "key" => "courseid");
        $this->_belongtos["inpapertpl"] = array(
            "type" => "PaperTpl",
            "key" => "inpapertplid");
        $this->_belongtos["outpapertpl"] = array(
            "type" => "PaperTpl",
            "key" => "outpapertplid");
    }

    // $row = array();
    // $row["name"] = $name;
    // $row["ename"] = $ename;
    // $row["daycnt"] = $daycnt;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Pgroup::createByBiz row cannot empty");

        $default = array();
        $default["name"] = '';
        $default["ename"] = '';
        $default["typestr"] = '';
        $default["subtypestr"] = '';
        $default["diseaseid"] = 0;
        $default["doctorid"] = 0;
        $default["courseid"] = 0;
        $default["inpapertplid"] = 0;
        $default["outpapertplid"] = 0;
        $default["daycnt"] = 0;
        $default["showinaudit"] = 1;
        $default["showinwx"] = 1;
        $default["refer_pgroupids"] = '';
        $default["level"] = 0;
        $default["summary"] = "";

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 返回值为数值型　例：有效率为89%，返回为89
    public function effective ($fromdate = "", $todate = "") {
        $cond = "";
        if ($fromdate) {
            $cond .= " and createtime >= '{$fromdate}' ";
        }

        if ($todate) {
            $todate_addone = date('Y-m-d', strtotime('+1 day', strtotime($todate)));
            $cond .= " and createtime <= '{$todate_addone}' ";
        }

        $effectcnt = $this->isEffectCnt($cond);
        $donecnt = $this->isDoneCnt($cond);

        if ($donecnt) {
            return round(($effectcnt / $donecnt) * 100);
        } else {
            return 0;
        }
    }

    public function isEffectCnt( $condEx="" ){
        return PatientPgroupRefDao::getCntByPgroupid($this->id, " and iseffect = 1 {$condEx} ");
    }

    public function isDoneCnt( $condEx="" ){
        return PatientPgroupRefDao::getCntByPgroupid($this->id, " and iseffect > 0 {$condEx} ");
    }

    public function getFixNameForDoctor($doctor){
        $name = $this->name;
        $is_in_hezuo = $doctor->isHezuo("Lilly");
        if($is_in_hezuo){
            return $this->getFixName();
        }else{
            return $this->name;
        }
    }

    private function getFixName(){
        $arr = array(
            111409079 => "多一点“正关注”", // 这样关注，孩子会更好
            111621505 => "提升注意力", // 孩子写作业拖拉？先提升注意力
            120801805 => "“万能”的小积分", // 小积分培养好行为
            120804245 => "学沟通、明规则", // 学沟通、明规则、定方案
            120812195 => "“冷处理”坏情绪", // 改善孩子情绪问题之频繁哭闹/发脾气
            141695576 => "西药这样吃", // 西药这样吃
        );

        return $arr[$this->id] ? $arr[$this->id] : '';
    }

    // 满足微信端显示课程的条件
    public function canShowCourse () {
        if ($this->course instanceof Course) {
            if ($this->course->picture instanceof Picture) {
                return true;
            }
        }

        return false;
    }

    public function getTypestrDesc () {
        $arr = self::getTypestrDescArr();
        $typestr = $this->typestr;
        return isset($arr[$typestr]) ? $arr[$typestr] : $typestr;
    }

    public function getSubTypestrDesc () {
        $arr = self::getSubTypestrDescArr();
        $subtypestr = $this->subtypestr;
        return isset($arr[$subtypestr]) ? $arr[$subtypestr] : "";
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    public static function getTypestrDescArr () {
        $arr = array();
        $arr['manage'] = '管理组';
        $arr['lab'] = '实验组';
        return $arr;
    }

    public static function getSubTypestrDescArr () {
        $arr = array();
        $arr['SchoolTraining'] = '学校培训';
        $arr['FamilyTraining'] = '家庭培训';
        $arr['BehaviorTraining'] = '行为训练';
        $arr['ABCTraining'] = '入门练习';
        $arr['AdvancedTraining'] = '进阶练习';
        $arr['PracticalTraining'] = '实战应对';
        return $arr;
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

}
