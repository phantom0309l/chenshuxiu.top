<?php

// 如果图片存在则直接返回
$REQUEST_URI = $_SERVER['REQUEST_URI'];
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$Photo_ROOT = $DOCUMENT_ROOT . '/xphoto';

$filename = $Photo_ROOT . $REQUEST_URI;
if (file_exists($filename) && strpos($filename, "..") == false) {
    header('Content-type: image/jpeg');
    echo file_get_contents($filename);
    exit();
}
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 0);

// load Config and Assembly
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/www/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__, true);

Config::setConfig("needUrlRewrite", true);

$pattern_cut = "/^(.*?)\.(\d*?)\.(.*?)$/i";
$pattern_resize = "/^(.*?)\.(\d*?)_(\d*?)\.(.*?)$/i";
$pattern_cut2 = "/^(.*?)\.(\d*?)-(\d*?)\.(.*?)$/i";
$matches = array();

// 裁剪成方形,并缩略
if (preg_match($pattern_cut, $REQUEST_URI, $matches)) {
    list ($all, $pre, $width, $ext) = $matches;
    $srcFile = $Photo_ROOT . $pre . '.' . $ext;

    if (! file_exists($srcFile)) {
        // TODO 原图不存在
        echo "404-src.jpg";
        exit();
    }

    $t = new ThumbHandler();
    $t->setSrcImg($srcFile);

    $t->setDstImg($filename);
    $t->createCutImg($width);

    if (file_exists($filename)) {
        header('Content-type: image/jpeg');
        echo file_get_contents($filename);
        exit();
    } else {
        // TODO 缩略失败
        echo "404-thumb.jpg";
    }
} // 等比缩略
elseif (preg_match($pattern_resize, $REQUEST_URI, $matches)) {
    list ($all, $pre, $width, $height, $ext) = $matches;
    $srcFile = $Photo_ROOT . $pre . '.' . $ext;

    if (! file_exists($srcFile)) {
        // TODO 原图不存在
        echo "404-src.jpg";
        exit();
    }

    $t = new ThumbHandler();
    $t->setSrcImg($srcFile);

    $t->setDstImg($filename);
    $t->createImg($width, $height);

    if (file_exists($filename)) {
        header('Content-type: image/jpeg');
        echo file_get_contents($filename);
        exit();
    } else {
        // TODO 缩略失败
        echo "404-thumb.jpg";
    }
} elseif (preg_match($pattern_cut2, $REQUEST_URI, $matches)) {
    list ($all, $pre, $width, $height, $ext) = $matches;
    $srcFile = $Photo_ROOT . $pre . '.' . $ext;

    if (! file_exists($srcFile)) {
        // TODO 原图不存在
        echo "404-src.jpg";
        exit();
    }

    $t = new ThumbHandler();
    $t->setSrcImg($srcFile);

    $t->setDstImg($filename);
    $t->createCutImg2($width, $height);

    if (file_exists($filename)) {
        header('Content-type: image/jpeg');
        echo file_get_contents($filename);
        exit();
    } else {
        // TODO 缩略失败
        echo "404-thumb.jpg";
    }
} else {
    // TODO 错误的url
    echo "404-url.jpg";
}