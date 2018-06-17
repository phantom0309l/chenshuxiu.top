<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

XContext::setValue("dtpl", ROOT_TOP_PATH . "/domain/tpl");

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][fill_base_msg.php]=====");

function mergerImg ($imgs) {

    list ($max_width, $max_height) = getimagesize($imgs['dst']);
    $canvas = imagecreatetruecolor($max_width, $max_height);

    $dst_im = imagecreatefrompng($imgs['dst']);
    imagecopy($canvas, $dst_im, 0, 0, 0, 0, $max_width, $max_height);
    imagedestroy($dst_im);

    $srcs = $imgs['src'];
    foreach ($srcs as $src) {
        $url = $src['url'];
        $left = $src['left'];
        $top = $src['top'];
        $fw = $src['fw'];
        $fh = $src['fh'];
        $src_info = getimagesize($url);
        $fileType = $src_info[2];
        if ($fileType == 2) {
            // 原图是 jpg 类型
            $src_im = imagecreatefromjpeg($url);
        } else
            if ($fileType == 3) {
                // 原图是 png 类型
                $src_im = imagecreatefrompng($url);
            } else {
                // 无法识别的类型
                $src_im = imagecreatefrompng($url);
            }

        $tempcanvas = imagecreatetruecolor($fw, $fh);
        imagecopyresampled($tempcanvas, $src_im, 0, 0, 0, 0, $fw, $fh, $src_info['0'], $src_info['1']);

        imagecopy($canvas, $tempcanvas, $left, $top, 0, 0, $fw, $fh);
        imagedestroy($src_im);
        imagedestroy($tempcanvas);
    }

    imagejpeg($canvas, ROOT_TOP_PATH . "/wwwroot/img/static/img/qrcode/test14.jpg");
}

$imgs = array(
    'dst' => ROOT_TOP_PATH . "/wwwroot/img/static/img/qrcode/bg.png",
    'src' => array(
        array(
            "url" => ROOT_TOP_PATH . "/wwwroot/img/static/img/qrcode/test123.jpg",
            "fw" => 300,
            "fh" => 300,
            "left" => 170,
            "top" => 686),
        array(
            "url" => 'http://wx.qlogo.cn/mmopen/zylWicgVFULLoAnyJKhGiaeAoGav1Mhibco84eUz7zPlky52VqKITGGcFgWArcUaGJPF29VWkXHXNVIKPquG5q8wQSq8GAGbrsU/0',
            "fw" => 136,
            "fh" => 136,
            "left" => 454,
            "top" => 24)));

mergerImg($imgs);

Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
