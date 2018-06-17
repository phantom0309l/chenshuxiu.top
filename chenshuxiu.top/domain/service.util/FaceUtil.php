<?php

class FaceUtil
{

    public static function init () {
        $appid = '1006723';
        $secretId = 'AKIDhe0t9CVT9rLUV5N537PREVWuldS35ZBX';
        $secretKey = '9csaOiQtpUbRhe3lmdZMRRrdpir4nDVU';
        $userid = '747969377';
        // 优图开放平台初始化
        Conf::setAppInfo($appid, $secretId, $secretKey, $userid, conf::API_YOUTU_END_POINT);
    }

}
