<?php
if (! defined("_MYCRYPT_PHP")) {
    define("_MYCRYPT_PHP", true);

    class MyCrypt
    {

        var $cryptkey;

        public function MyCrypt () {
            $this->cryptkey = "heimacytd123232323";
        }

        public function encrypt ($txt) {
            $td = mcrypt_module_open('tripledes', '', 'ecb', '');
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            mcrypt_generic_init($td, $this->cryptkey, $iv);
            $encrypt = mcrypt_generic($td, $txt);
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
            return base64_encode($encrypt);
        }

        public function decrypt ($txt) {
            if ($txt == null || $txt == "") {
                return false;
            }

            $txt = base64_decode($txt);

            if ($txt == null || $txt == "") {
                return false;
            }
            $td = mcrypt_module_open('tripledes', '', 'ecb', '');
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            mcrypt_generic_init($td, $this->cryptkey, $iv);
            $decrypt = mdecrypt_generic($td, $txt);
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);

            return trim($decrypt);
        }

        public static function encode ($str) {
            $a = new MyCrypt();
            return $a->encrypt($str);
        }

        public static function decode ($str) {
            $a = new MyCrypt();
            return $a->decrypt($str);
        }
    }

}#_MYCRYPT_PHP

