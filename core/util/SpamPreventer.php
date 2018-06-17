<?php

class SpamPreventer
{

    private static $privateKey = 'f28　哈9f2)&*(Fh';

    static public function getFormFingerPrint ($formName) {
        $time = gmmktime();
        $ip = Ip::get();
        $publicKey = md5(self::$privateKey . $formName . $time . $ip);

        echo '<input type="hidden" name="gffp_t" value="' . $time . '"/>';
        echo '<input type="hidden" name="gffp_k" value="' . $publicKey . '"/>';
        echo '<input type="hidden" name="gffp_r" value="' . rand(32789, 984372) . '"/>';
    }

    static public function checkFromFingerPrint ($formName) {
        $time = XRequest::getValue('gffp_t', '');
        $ip = Ip::get();
        $publicKey = XRequest::getValue('gffp_k', '');

        $localKey = md5(self::$privateKey . $formName . $time . $ip);
        if ($localKey != $publicKey)
            return false;
            // 只有三十分钟有效期
        if ($time < gmmktime() - 60 * 30)
            return false;
        return true;
    }
}