<?php

class UploadFile
{

    public function __construct () {

    }

    public static function upload ($filename, $tmp, $filesize, $dir) {
        if (empty($filename)) {
            return false;
        }

        $maxsize = 1024 * 1024 * 30;
        // $maxsize = 1024;

        if (self::checkFiletype($filename) == false) {
            return false;
        }

        if (self::checkFilesize($filesize, $maxsize) == false) {
            return false;
        }
        return self::save($filename, $tmp, $dir);
    }

    public static function getFiles ($dir) {
        $files = array();
        if (! is_dir($dir)) {
            return false;
        }
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file == "." || $file == "..") {
                    continue;
                }
                $files[] = $file;
            }
            closedir($handle);
        }
        return $files;

    }

    public static function checkFiletype ($filename) {
        $extention = preg_replace('/.*\.(.*[^\.].*)*/iU', '\\1', $filename); // 取得文件扩展名;
        $extentions = array(
            "doc",
            "xls",
            "ppt",
            "avi",
            "txt",
            "gif",
            "jpg",
            "jpeg",
            "bmp",
            "png",
            "mp3",
            "swf");
        foreach ($extentions as $a) {
            if ($a == strtolower($extention)) {
                return true;
            }
        }
        return false;

    }

    public static function checkFilesize ($filesize, $maxsize) {
        if (intval($filesize) > $maxsize) {
            return false;
        }
        return true;
    }

    public static function save ($filename, $tmp, $dir) {
        if (! file_exists($dir))         // 检测子目录是否存在;
        {
            mkdir($dir, 0777, true); // 不存在则创建;
        }

        if (copy($tmp, "{$dir}/{$filename}")) {
            return true;
        }
        return false;
    }

}
