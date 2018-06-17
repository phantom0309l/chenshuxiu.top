<?php
/**
 * AESHelper class
 *
 * 提供基于AES算法的加解密接口.
 */
 class AESCrypt {

     private $key;
     private $key_size;
     private $cipher;
     private $mode;

     // 构造函数
     // $key 密钥 字符串
     // $key_size 密钥长度（128，256）单位为bit
     // $cipher MCRYPT_ciphername 常量中的一个，或者是字符串值的算法名称。（其中包括了加密模式） 加密算法分组大小
     // $mode MCRYPT_MODE_modename 常量中的一个，或以下字符串中的一个："ecb"，"cbc"，"cfb"，"ofb"，"nofb" 和 "stream"。
     public function __construct ($key, $key_size = 256, $cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_ECB) {
         $this->key = $key;
         $this->key_size = $key_size / 8; // 输入值单位为bit，这里转换为byte
         $this->cipher = $cipher;
         $this->mode = $mode;
     }

     public function __set($key, $value){
         $this->$key = $value;
     }

     public function __get($key) {
         return $this->$key;
     }

     public function encrypt($input) {
         $size = mcrypt_get_block_size($this->cipher, $this->mode);
         $input = $this->pkcs7_pad($input, $size);
         $td = mcrypt_module_open($this->cipher, '', $this->mode, '');
         $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
         mcrypt_generic_init($td, str_pad($this->key, $this->key_size), $iv);
         $data = mcrypt_generic($td, $input);
         mcrypt_generic_deinit($td);
         mcrypt_module_close($td);
         $data = base64_encode($data);
         return $data;
     }

     private function pkcs7_pad ($text, $blocksize) {
         $pad = $blocksize - (strlen($text) % $blocksize);
         return $text . str_repeat(chr($pad), $pad);
     }

     public function decrypt($sStr) {
         $decrypted= mcrypt_decrypt($this->cipher, str_pad($this->key, $this->key_size), base64_decode($sStr), $this->mode);
         $dec_s = strlen($decrypted);
         $padding = ord($decrypted[$dec_s-1]);
         $decrypted = substr($decrypted, 0, -$padding);
         return $decrypted;
     }
}
