<?php
/*
 * 错误码
 */
class ErrCode
{

    const ok = "ok";

    const error = "error";

    const api_abandon = 901;

    const token_auth_fail = 1001;

    const op_auth_fail = 1002;

    const error_username = 1003;

    const error_password = 1004;

    const not_doctor = 1005;

    const invaild_params = 1006;

    const invaild_datas = 1007;

    const no_img_data = 2001;

    const error_img_type = 2002;

    const upload_img_fail = 2003;

    const no_voice_data = 3001;

    const no_video_data = 3002;

    public static $Desc = array(
        0 => "成功",  //

        self::api_abandon => "本接口已废弃",  //

        self::token_auth_fail => "请先登录",  //
        self::op_auth_fail => "操作权限验证失败",  //
        self::error_username => "用户不存在",  //
        self::error_password => "密码错误",  //
        self::invaild_params => "无效参数",  //
        self::invaild_datas => "无效数据(包括重复)",  //

        self::no_img_data => "没有获取到图片数据",  //
        self::error_img_type => "错误的图片格式",  //
        self::upload_img_fail => "上传图片出现问题",  //

        self::no_voice_data => "没有获取到音频数据",  //
        self::no_video_data => "没有获取到视频数据",  //

        self::not_doctor => "未登录或非医生权限"); //

    public static function desc ($value) {
        return self::$Desc[$value];
    }
}
