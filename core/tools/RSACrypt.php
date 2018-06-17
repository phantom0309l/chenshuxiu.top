<?php
/**
 * RSACrypt class
 *
 * 提供基于RSA算法的加解密接口.
 */
 class RSACrypt {

     private $pubKey = null;
     private $priKey = null;

    /**
    * 构造函数
    *
    * @param string 公钥文件（验签和加密时传入）
    * @param string 私钥文件（签名和解密时传入）
    */
    public function __construct($public_key = '', $private_key = '')
    {
        if ($public_key) {
            $this->_getPublicKey($public_key);
        }
        if ($private_key) {
            $this->_getPrivateKey($private_key);
        }
    }

    private function _encode($data, $code)
    {
        switch (strtolower($code)) {
            case 'base64':
                $data = base64_encode('' . $data);
                break;
            case 'hex':
                $data = bin2hex($data);
                break;
            case 'bin':
            default:
        }
        return $data;
    }

    private function _decode($data, $code)
    {
        switch (strtolower($code)) {
            case 'base64':
                $data = base64_decode($data);
                break;
            case 'hex':
                $data = $this->_hex2bin($data);
                break;
            case 'bin':
            default:
        }
        return $data;
    }

    private function _getPublicKey($key_content)
    {
        if ($key_content) {
            $this->pubKey = openssl_get_publickey($key_content);
        }
    }

    private function _getPrivateKey($key_content)
    {
        if ($key_content) {
            $this->priKey = openssl_get_privatekey($key_content);
        }
    }

    private function _hex2bin($hex = false)
    {
        $ret = $hex !== false && preg_match('/^[0-9a-fA-F]+$/i', $hex) ? pack("H*", $hex) : false;
        return $ret;
    }

    /**
    * 生成签名
    *
    * @param string 签名材料
    * @param string 签名编码（base64/hex/bin）
    * @return 签名值
    */
    public function sign($data, $code = 'base64')
    {
        $ret = false;
        if (openssl_sign($data, $ret, $this->priKey)) {
            $ret = $this->_encode($ret, $code);
        }
        return $ret;
    }

    /**
    * 验证签名
    *
    * @param string 签名材料
    * @param string 签名值
    * @param string 签名编码（base64/hex/bin）
    * @return bool
    */
    public function verify($data, $sign, $code = 'base64')
    {
        $ret = false;
        $sign = $this->_decode($sign, $code);
        if ($sign !== false) {
            switch (openssl_verify($data, $sign, $this->pubKey)) {
                case 1:
                    $ret = true;
                    break;
                case 0:
                case -1:
                default:
                    $ret = false;
            }
        }
        return $ret;
    }

    /**
    * 加密
    *
    * @param string 明文
    * @param string 密文编码（base64/hex/bin）
    * @param int 填充方式（貌似php有bug，所以目前仅支持OPENSSL_PKCS1_PADDING）
    * @return string 密文
    */
    public function encrypt($data, $code = 'base64', $padding = OPENSSL_PKCS1_PADDING)
    {
        $ret = false;
        if (openssl_public_encrypt($data, $result, $this->pubKey, $padding)) {
            $ret = $this->_encode($result, $code);
        }
        return $ret;
    }

    /**
    * 解密
    *
    * @param string 密文
    * @param string 密文编码（base64/hex/bin）
    * @param int 填充方式（OPENSSL_PKCS1_PADDING / OPENSSL_NO_PADDING）
    * @param bool 是否翻转明文（When passing Microsoft CryptoAPI-generated RSA cyphertext, revert the bytes in the block）
    * @return string 明文
    */
    public function decrypt($data, $code = 'base64', $padding = OPENSSL_PKCS1_PADDING, $rev = false)
    {
        $ret = false;
        $data = $this->_decode($data, $code);
        if ($data !== false) {
            if (openssl_private_decrypt($data, $result, $this->priKey, $padding)) {
                $ret = $rev ? rtrim(strrev($result), "\0") : '' . $result;
            }
        }
        return $ret;
    }
}
