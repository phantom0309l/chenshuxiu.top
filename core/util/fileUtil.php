<?php

class FileUtil
{
    // mk dir
    public static function createDir ($filePath) {
        $arr_filePaths = explode("/", $filePath);
        $filePath_root = $arr_filePaths[0] . "/";

        if (! is_dir($filePath_root)) {
            return false;
        }
        for ($i = 1; $i < count($arr_filePaths); $i ++) {
            $filePath_root = $filePath_root . $arr_filePaths[$i] . "/";
            if (! is_dir($filePath_root)) {
                mkdir($filePath_root, 0777);
            }
        }
        return true;
    }

    // create file
    public static function createFile ($fileName, $str) {
        if (file_exists($fileName)) {
            copy($fileName, $fileName . "_" . date("YmdHis"));
        }
        if ($fp = fopen($fileName, "w")) {
            fwrite($fp, $str);
            fclose($fp);
            return true;
        }
        return false;
    }

    public static function down2file ($data, $file_name) {
        $file_size = strlen($data);
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: $file_size");
        header("Content-Disposition: attachment; filename=" . $file_name);
        echo $data;
        return false;
    }
    // download -- file_dir,file_name
    public static function download ($file_dir, $file_name) {
        $file_dir = chop($file_dir);
        if ($file_dir != '') {
            $file_path = $file_dir;
            if (substr($file_dir, strlen($file_dir) - 1, strlen($file_dir)) != '/') {
                $file_path .= '/';
            }
            $file_path .= $file_name;
        } else {
            $file_path = $file_name;
        }

        if (! file_exists($file_path))
            return false;

        $file_size = filesize($file_path);
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: $file_size");
        header("Content-Disposition: attachment; filename=" . $file_name);

        $fp = fopen($file_path, "r");
        $buffer_size = 1024;
        $cur_pos = 0;

        while (! feof($fp) && $file_size - $cur_pos > $buffer_size) {
            $buffer .= fread($fp, $buffer_size);
            $cur_pos += $buffer_size;
        }

        $buffer .= fread($fp, $file_size - $cur_pos);
        echo $buffer;
        fclose($fp);
        return true;
    }
}
?>