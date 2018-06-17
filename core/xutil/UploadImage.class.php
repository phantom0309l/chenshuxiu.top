<?php

class UploadImage
{

    public function __construct () {

    }

    public function info ($imagepath, $filesize = true) {
        $info = getimagesize($imagepath);
        if ($filesize == true) {
            $size = filesize($imagepath);
        }
        $type = $info[mime];
        if ($type == "image/gif") {
            $end = "gif";
        } else
            if ($type == "image/jpeg") {
                $end = "jpg";
            } elseif ($type == "image/bmp") {
                $end = "bmp";
            } elseif ($type == "application/x-shockwave-flash") {
                $end = "swf";
            } else {
                $end = 'undefine'; // 格式不正确
            }
        $imageinfo['width'] = $info[0];
        $imageinfo['height'] = $info[1];
        $imageinfo['type'] = $end;
        $imageinfo['size'] = $size;
        $imageinfo['tmp_name'] = $imagepath;
        return $imageinfo;
    }

    public function isEmpty ($name) {
        $code = empty($_FILES[$name]['tmp_name']);
        return $code;
    }

    public function insert ($name, $path, $multi = false) {
        if ($multi == true) {
            $num = count($_FILES[$name]['name']);
            $fname = $_FILES[$name]['name'];
            $tname = $_FILES[$name]['tmp_name'];
            for ($i = 0; $i < $num; $i ++) {
                $_imageinfo = $this->info($tname[$i]);
                if ($_imageinfo['type'] == 'undefine') {
                    $_imageinfo['name'] = $fname[$i];
                    $_imageinfo['code'] = '1';
                    $imageinfo[0] = $_imageinfo;
                    return $imageinfo;
                }
                if ($_imageinfo['size'] >= 1024000) {
                    $_imageinfo['name'] = $fname[$i];
                    $_imageinfo['code'] = '2';
                    $imageinfo[0] = $_imageinfo;
                    return $imageinfo;
                }
            }
            for ($i = 0; $i < $num; $i ++) {

                $_imageinfo = $this->info($tname[$i]);
                $nname = time() . '.' . $_imageinfo['type'];
                if (move_uploaded_file($tname[$i], $path . $nname)) {
                    $_imageinfo['name'] = $fname[$i];
                    $_imageinfo['nname'] = $nname;
                    $_imageinfo['code'] = '0';
                    $imageinfo[$i] = $_imageinfo;
                } else {
                    $_imageinfo['name'] = $fname[$i];
                    $_imageinfo['nname'] = $nname;
                    $_imageinfo['code'] = '3';
                    $imageinfo[0] = $_imageinfo;
                    return $imageinfo;
                }
            }
            return $imageinfo;
        } else {
            $fname = $_FILES[$name]['name'];
            $tname = $_FILES[$name]['tmp_name'];
            $_imageinfo = $this->info($tname);
            if ($_imageinfo['type'] == 'undefine') {
                $_imageinfo['name'] = $fname;
                $_imageinfo['code'] = '1';
                $imageinfo[0] = $_imageinfo;
                return $imageinfo;
            }
            if ($_imageinfo['size'] >= 1024000) {
                $_imageinfo['name'] = $fname;
                $_imageinfo['code'] = '2';
                $imageinfo[0] = $_imageinfo;
                return $imageinfo;
            }
            $nname = $_imageinfo['width'] . 'X' . $_imageinfo['height'] . '_' . $_imageinfo['size'] . '.' . $_imageinfo['type'];
            if (move_uploaded_file($tname, $path . $nname)) {
                $_imageinfo['name'] = $fname;
                $_imageinfo['nname'] = $nname;
                $_imageinfo['code'] = '0';
                $imageinfo[0] = $_imageinfo;
            } else {
                $_imageinfo['name'] = $fname;
                $_imageinfo['nname'] = $nname;
                $_imageinfo['code'] = '3';
                $imageinfo[0] = $_imageinfo;
            }
            return $imageinfo;
        }

    }

    public function getImgInfo ($name) {
        $tname = $_FILES[$name]['tmp_name'];
        return $this->info($tname);
    }

    public function update () {

    }

    public function delete () {

    }

    public function upload () {

    }
}
