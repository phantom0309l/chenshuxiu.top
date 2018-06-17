<?php
/*
 * 常量类
 */
class XConst
{

    public static function maskMobile ($mobile) {
        $str1 = substr($mobile, 0, 3);
        $str2 = substr($mobile, - 4);

        return $str1 . "****" . $str2;
    }

    const Init = 0;

    const Online = 1;

    const Status_offline = 0;

    const Status_online = 1;

    const Status_unsubscribe = 2;

    public static $Statuss = array(
        self::Status_offline => '下线',
        self::Status_online => "上线",
        self::Status_unsubscribe=>"取消关注"
    );

    public static $status_withcolor = array(
        self::Status_offline => "<font color='red'>下线</font>",
        self::Status_online => "<font color='green'>上线</font>",
        self::Status_unsubscribe => "<font color='red'>取消关注<br/>重新报到</font>"
    );

    public static function status ($value) {
        return self::$Statuss[$value];
    }

    public static function status_withcolor ($value) {
        return self::$status_withcolor[$value];
    }

    // 审核状态
    const AuditStatus_init = 0;

    const AuditStatus_pass = 1;

    const AuditStatus_refuse = 2;

    public static $AuditStatuss = array(
        self::AuditStatus_init => '待审核',
        self::AuditStatus_pass => "审核通过",
        self::AuditStatus_refuse => '审核拒绝');

    public static $AuditStatus_withcolor = array(
        self::AuditStatus_init => '待审核',
        self::AuditStatus_pass => "通过",
        self::AuditStatus_refuse => '拒绝');

    public static function auditStatus ($value) {
        return self::$AuditStatuss[$value];
    }

    public static function auditStatus_withcolor ($value) {
        return self::$AuditStatus_withcolor[$value];
    }

    // 布尔,是否
    const bool_null = - 1;

    const bool_no = 0;

    const bool_yes = 1;

    public static $Bools = array(
        self::bool_null => '未知',
        self::bool_no => "否",
        self::bool_yes => "是");

    public static function bool ($value) {
        return self::$Bools[$value];
    }

    // 性别
    const Sex_all = - 1;

    const Sex_null = 0;

    const Sex_man = 1;

    const Sex_woman = 2;

    public static $Sexs = array(
        self::Sex_null => '空',
        self::Sex_woman => "女",
        self::Sex_man => "男");

    public static $SexsAll = array(
        self::Sex_all => '全部',
        self::Sex_null => '空',
        self::Sex_woman => "女",
        self::Sex_man => "男");

    public static function Sex ($value) {
        return self::$Sexs[$value];
    }

    public static function isSelectSex ($value) {
        return $value > self::Sex_null;
    }

    // 手机设备类型
    const DeviceType_ios = 1;

    const DeviceType_android = 2;

    public static $DeviceTypes = array(
        self::DeviceType_ios => '苹果',
        self::DeviceType_android => "安卓");

    public static function devicetype ($value) {
        return self::$DeviceTypes[$value];
    }

    // 关系
    const Ship_self = 0;

    const Ship_father = 1;

    const Ship_mother = 2;

    const Ship_teacher = 3;

    const Ship_other = 9;

    public static $Ships = array(
        self::Ship_self => '本人',
        self::Ship_father => "父亲",
        self::Ship_mother => "母亲",
        self::Ship_teacher => "老师",
        self::Ship_other => "其他");

    public static function ship ($value) {
        return self::$Ships[$value];
    }

}