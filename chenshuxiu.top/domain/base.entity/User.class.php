<?php

/*
 * User 登录用户 患者，医生，运营人员都需要User来登录，一个患者可以有多个user
 */
class User extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'createwxuserid',  // createwxuserid
            'patientid',  // patientid
            'xcode',  // 方寸号
            'unionid',  // 微信unionid
            'username',  // 用户名，可空
            'mobile',  // 手机号
            'password',  // 密码
            'sasdrowp',  // 密码
            'name',  // 姓名
            'shipstr',  // 关系
            'token',  // 每次登录生成一个新的token，有效期 1年
            'token_expires_in',  // token有效期
            'ref_userid',  // 邀请人userid
            'auditremark',  // 备注
            'login_fail_cnt',  // 失败登陆次数
            'last_login_time',  // 最后一次登陆时间
            'last_modifypassword_time'); // 最后一次修改密码时间
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid',
            'xcode');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["createwxuser"] = array(
            "type" => "WxUser",
            "key" => "createwxuserid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
    }

    // ///////////////////////
    // $row = array();
    // $row["unionid"] = $unionid;
    // $row["username"] = $username;
    // $row["mobile"] = $mobile;
    // $row["password"] = $password;
    // $row["patientid"] = $patientid;
    // $row["name"] = $name;
    // $row["shipstr"] = $shipstr;
    // 'login_fail_cnt', //失败登陆次数
    // 'last_login_time', //最后一次登陆时间
    // 'last_modifypassword_time', //最后一次修改密码时间
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "User::createByBiz row cannot empty");

        $default = array();
        $default["createwxuserid"] = 0;
        $default["patientid"] = 0;
        $default["unionid"] = '';
        $default["xcode"] = XCode::getNextCode('userxcode');
        $default["username"] = '';
        $default["mobile"] = '';
        $default["password"] = '';
        $default["sasdrowp"] = '';
        $default["name"] = '';
        $default["shipstr"] = '';
        $default["ref_userid"] = 0;
        $default["token"] = '';
        $default["token_expires_in"] = 0;
        $default["auditremark"] = '';
        $default["login_fail_cnt"] = 0;
        $default["last_login_time"] = '0000-00-00 00:00:00';
        $default["last_modifypassword_time"] = '0000-00-00 00:00:00';

        $row += $default;
        if (isset($row['password']) && ! empty($row['password'])) {
            $row['sasdrowp'] = $row['password'];
            $row['password'] = self::encryptPassword($row['password']);
        }
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getUserTypeStr () {
        $str = '';
        if ($this->isDoctor()) {
            $str .= '医生 ';
        }

        if ($this->isAuditor()) {
            $str .= '运营 ';
        }

        if ($this->patientid > 0) {
            $str .= '患者';
        }

        return trim($str);
    }

    // 是测试用户
    public function isTest () {
        if ($this->isAuditor()) {
            return true;
        }

        if ($this->patient instanceof Patient) {
            return $this->patient->isTest();
        }

        return false;
    }

    // 是医生或医生助理
    public function isDoctorOrAssistant () {
        return $this->isDoctor() || $this->isAssistant();
    }

    // 是医生
    public function isDoctor () {
        $doctor = $this->getDoctor();
        return ($doctor instanceof Doctor);
    }

    // 是医生助理
    public function isAssistant () {
        $assistant = $this->getAssistant();
        return ($assistant instanceof Assistant);
    }

    // 是运营人员
    public function isAuditor () {
        $auditor = $this->getAuditor();
        if ($auditor instanceof Auditor && ! $auditor->isLeave()) {
            return true;
        } else {
            return false;
        }
    }

    // 获取医生对象
    public function getDoctor () {
        return DoctorDao::getByUserid($this->id);
    }

    // 获取医助对象
    public function getAssistant () {
        return AssistantDao::getByUserid($this->id);
    }

    // 获取运营人员对象
    public function getAuditor () {
        return AuditorDao::getByUserid($this->id);
    }

    // 获取WxUser对象
    public function getWxUsers () {
        return WxUserDao::getListByUserId($this->id);
    }

    // 获取 MasterWxUser
    public function getMasterWxUser ($wxshopid = 0) {
        return WxUserDao::getMasterWxUserByUserId($this->id, $wxshopid);
    }

    // 获取 MasterWxUserId
    public function getMasterWxUserId ($wxshopid = 0) {
        $wxuser = WxUserDao::getMasterWxUserByUserId($this->id, $wxshopid);
        return ($wxuser instanceof WxUser) ? $wxuser->id : 0;
    }

    // 获取唯一的WxUserId
    public function getWxUserIdIfOnlyOne () {
        $wxusers = $this->getWxUsers();
        if (count($wxusers) == 1) {
            $wxuser = array_shift($wxusers);
            return $wxuser->id;
        }
        return 0;
    }

    // 修正 user->patientid
    public function fixPatientId ($patientid) {
        if (empty($patientid)) {
            $patientid = 0;
        }

        // 级联修正 wxuser->patientid
        foreach ($this->getWxUsers() as $w) {
            $w->fixPatientId($patientid);
        }

        $this->set4lock('patientid', $patientid);
    }

    // 获取账户对象
    public function getAccount ($code = 'user_rmb', $unit = Unit::rmb) {
        return Account::getByUserAndCode($this, $code, $unit);
    }

    public function getMaskMobile () {
        return substr($this->mobile, 0, 3) . "****" . substr($this->mobile, - 4);
    }

    public function getShowName () {
        if ($this->name) {
            return $this->name;
        }

        if ($this->username) {
            return $this->username;
        }
        return $this->getMaskMobile();
    }

    // 获取患者名称 或 用户姓名
    public function getPatientNameOrShowName () {
        $str = '';
        if ($this->patient instanceof Patient && $this->patient->name) {
            $str = $this->patient->name;
        }

        if (empty($str)) {
            return $this->getShowName();
        }

        return $str;
    }

    // 有报到患者
    public function hasBaodaoPatient () {
        return ($this->patient instanceof Patient && $this->patient->isBaodaoed());
    }

    // 没有报到患者
    public function noBaodaoPatient () {
        return false == $this->hasBaodaoPatient();
    }

    // 验证密码
    public function validatePassword ($password) {
        $password = trim($password);
        if (empty($password)) {
            return false;
        }

        if ($this->password == $password) {
            return true;
        }

        $password = self::encryptPassword($password);

        if ($this->password == $password) {
            return true;
        }

        return false;
    }

    // 修改密码
    public function modifyPassword ($password) {
        if (strlen($password) > 20) {
            return;
        }

        $this->password = self::encryptPassword($password);
        $this->sasdrowp = $password;

        $this->last_modifypassword_time = date('Y-m-d H:i:s');
    }

    public function sendsms ($content, $appendarr = array()) {
        if (! $this->mobile)
            return false;
        return ShortMsg::sendmsg_asyn($this->id, $this->patientid, $content, $appendarr);
    }

    public function isCourseOver ($courseid) {
        $flag = false;
        $courseuserref = CourseUserRefDao::getByUseridCourseid($this->id, $courseid);
        if ($courseuserref instanceof CourseUserRef) {
            $pos = $courseuserref->pos;
            $course = Course::getById($courseid);
            $maxpos = CourseLessonRefDao::getMaxPosOfCourse($course);
            if ($pos == $maxpos) {
                $flag = true;
            }
        }
        return $flag;
    }

    public function isLoginTimeout () {
        $last_login_time = $this->last_login_time;
        if ($last_login_time == "0000-00-00 00:00:00") {
            return true;
        }
        $now = date("Y-m-d H:i:s", time());
        $diff = XDateTime::getDateDiff($now, $last_login_time);
        if ($diff > 3) {
            return true;
        } else {
            return false;
        }
    }

    // 所学的课程列表
    // 备注: 没有调用点 by 20170302
    public function getCourses () {
        $refs = $this->getCourseUserRefs();
        return CourseUserRef::toCourseArray($refs);
    }

    // 获取课程关系列表
    public function getCourseUserRefs ($groupstr = false) {
        return CourseUserRefDao::getListByUser($this, $groupstr);
    }

    // ///////////////////////
    // token begin
    // 生成token
    public function setNewToken () {
        $doctor = $this->getDoctor();

        // 测试医院测试出来
        if ($doctor instanceof Doctor && $this->token && $doctor->isTest()) {
            return $this->token;
        }

        $md5 = md5('fcqx:' . $this->id . ':' . date('Y-m-d H:i:s', time()));
        $token = 'fcqx' . $md5;
        $this->token = $token;

        $this->refreshTokenExpiresIn();
        return $this->token;
    }

    // 刷新token的过期时间
    public function refreshTokenExpiresIn () {
        $this->token_expires_in = time() + (365 * 24 * 60 * 60);
    }

    // 清空token
    public function clearToken () {
        $this->token = "";
        $this->token_expires_in = 0;
    }

    // 检查token是否有效
    public function tokenCheck () {
        if ($this->token == '' || $this->token_expires_in < time()) {
            return false;
        }

        return true;
    }

    // 检查token是否有效 tokenCheck 的别名
    public function checkToken () {
        return $this->tokenCheck();
    }

    // token end
    // ///////////////////////

    // 测试省市名
    public function testCityStr () {
        return '等待修改';
    }

    // 只有一个cronbak在调用
    public function getMaxPosInCourse (Course $course) {
        $sql = "SELECT max(clr.pos)
            FROM lessonuserrefs lr
            INNER JOIN courselessonrefs clr ON clr.courseid = :courseid AND clr.lessonid = lr.lessonid
            WHERE lr.userid = :userid AND lr.courseid = :courseid ";

        $bind = [];
        $bind[':courseid'] = $course->id;
        $bind[':userid'] = $this->id;

        $pos = Dao::queryValue($sql, $bind);
        return (int) $pos;
    }

    public function canPassFbtWelcome () {
        $pipe = PipeDao::getByEntity($this, "FbtPass_Can");
        return $pipe instanceof Pipe;
    }

    public function isEverFbtWelcome () {
        $pipe = PipeDao::getByEntity($this, "FbtPass_Want");
        return $pipe instanceof Pipe;
    }

    public function getRankOfHardWorkInOneCourse (Course $course) {
        return CourseUserRefDao::getUserRankOfHardWorkInOneCourse($this, $course);
    }

    public function getHardWorkInOneCourse (Course $course) {
        return CourseUserRefDao::getUserHardWorkInOneCourse($this, $course);
    }

    public function getRankOfPosInOneCourse (Course $course) {
        return CourseUserRefDao::getUserRankOfPosInOneCourse($this, $course);
    }

    // 获取医生后台权限
    public function getAdminPrivileges () {
        if ($this->isAssistant()) {
            $assistant = $this->getAssistant();
            $doctorResources = $assistant->getDoctorResources();
        } else
            if ($this->isDoctor()) {
                $doctorResources = DoctorResourceDao::getEntityListByCond('DoctorResource');
            }

        $privileges = array();
        foreach ($doctorResources as $one) {
            $privileges[] = $one->name;
        }

        return $privileges;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function encryptPassword ($password) {
        if (! empty($password)) {
            $password = md5("fcqx_" . $password);
        }
        return $password;
    }
}
