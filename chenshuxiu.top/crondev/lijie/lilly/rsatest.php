<?php
/**
 *
 */
 ini_set("arg_seperator.output", "&amp;");
 ini_set("magic_quotes_gpc", 0);
 ini_set("magic_quotes_sybase", 0);
 ini_set("magic_quotes_runtime", 0);
 ini_set('display_errors', 1);
 ini_set("memory_limit", "2048M");
 include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
 include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
 mb_internal_encoding("UTF-8");

 TheSystem::init(__FILE__);

include_once (ROOT_TOP_PATH . "/../core/tools/RSACrypt.php");

$private_key = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQC3//sR2tXw0wrC2DySx8vNGlqt3Y7ldU9+LBLI6e1KS5lfc5jl
TGF7KBTSkCHBM3ouEHWqp1ZJ85iJe59aF5gIB2klBd6h4wrbbHA2XE1sq21ykja/
Gqx7/IRia3zQfxGv/qEkyGOx+XALVoOlZqDwh76o2n1vP1D+tD3amHsK7QIDAQAB
AoGBAKH14bMitESqD4PYwODWmy7rrrvyFPEnJJTECLjvKB7IkrVxVDkp1XiJnGKH
2h5syHQ5qslPSGYJ1M/XkDnGINwaLVHVD3BoKKgKg1bZn7ao5pXT+herqxaVwWs6
ga63yVSIC8jcODxiuvxJnUMQRLaqoF6aUb/2VWc2T5MDmxLhAkEA3pwGpvXgLiWL
3h7QLYZLrLrbFRuRN4CYl4UYaAKokkAvZly04Glle8ycgOc2DzL4eiL4l/+x/gaq
deJU/cHLRQJBANOZY0mEoVkwhU4bScSdnfM6usQowYBEwHYYh/OTv1a3SqcCE1f+
qbAclCqeNiHajCcDmgYJ53LfIgyv0wCS54kCQAXaPkaHclRkQlAdqUV5IWYyJ25f
oiq+Y8SgCCs73qixrU1YpJy9yKA/meG9smsl4Oh9IOIGI+zUygh9YdSmEq0CQQC2
4G3IP2G3lNDRdZIm5NZ7PfnmyRabxk/UgVUWdk47IwTZHFkdhxKfC8QepUhBsAHL
QjifGXY4eJKUBm3FpDGJAkAFwUxYssiJjvrHwnHFbg0rFkvvY63OSmnRxiL4X6EY
yI9lblCsyfpl25l7l5zmJrAHn45zAiOoBrWqpM5edu7c
-----END RSA PRIVATE KEY-----';

$public_key = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC3//sR2tXw0wrC2DySx8vNGlqt
3Y7ldU9+LBLI6e1KS5lfc5jlTGF7KBTSkCHBM3ouEHWqp1ZJ85iJe59aF5gIB2kl
Bd6h4wrbbHA2XE1sq21ykja/Gqx7/IRia3zQfxGv/qEkyGOx+XALVoOlZqDwh76o
2n1vP1D+tD3amHsK7QIDAQAB
-----END PUBLIC KEY-----';


$rsa = new RSACrypt($public_key, $private_key);

$data = $rsa->encrypt("ZzvRn9PLlWBqE2Z0lJ8ylv3GtYFkhLHb");
echo "加密：{$data}\n";
//
// $data = $rsa->decrypt($data);
// echo "解密：{$data}\n";


$aeskey = "awyb5ebXjOZ6UrX8kbUSIDgdNAS2BWcUUgKGRkDtUWYeCuaMBcYHSnEwKkXtlBX5STUx/u0FMofGBCKknPMKrMGaLuoPigP20gQ8nrJIZ5YuSBDeAVnbJaMa/vOmppSNNCnaDtpQ2K6uplfBEFXJrvdl5CKPbdYbjepb26g8TPk=";

$aeskey = $rsa->decrypt($aeskey);
echo "解密：{$aeskey}\n";
