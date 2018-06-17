<?php

class JsonCheckupPicture
{
    // jsonArray
    public static function jsonArray (CheckupPicture $checkupPicture) {
        $arr = array();

        $arr["checkuppictureid"] = $checkupPicture->id;
        $arr["checkupid"] = $checkupPicture->checkupid;
        $checkupname = $checkupPicture->checkup->checkuptpl->title;
        if ($checkupname == "" || $checkupname == null) {
            $checkupname = '其他';
        }

        $arr["checkupname"] = $checkupname;
        $arr["check_date"] = $checkupPicture->check_date;
        $arr["title"] = $checkupPicture->title;
        $arr["picture"] = $checkupPicture->picture->toJsonArray();

        return $arr;
    }

    // jsonArray4Ipad
    public static function jsonArray4Ipad (CheckupPicture $checkupPicture) {
        $arr = array();

        $arr["checkuppictureid"] = $checkupPicture->id;
        $arr["checkupid"] = $checkupPicture->checkupid;
        $arr["check_date"] = $checkupPicture->check_date;
        $arr["title"] = $checkupPicture->title;

        $checkuptpl_title = $checkupPicture->checkup->checkuptpl->title;
        if ($checkuptpl_title == "" || $checkuptpl_title == null) {
            $checkuptpl_title = '其他';
        }

        $arr["checkuptpl_title"] = $checkuptpl_title;
        $arr["picture"] = JsonPicture::jsonArrayForIpad($checkupPicture->picture);

        return $arr;
    }

    // jsonArrayForIpad
    public static function jsonArrayForIpad (CheckupPicture $checkupPicture) {
        $arr = array();

        $arr["checkuppictureid"] = $checkupPicture->id;
        $arr["checkupid"] = $checkupPicture->checkupid;
        $arr["check_date"] = $checkupPicture->check_date;

        $checkup_name = $checkupPicture->checkup->checkuptpl->title;
        if ($checkup_name == "" || $checkup_name == null) {
            $checkup_name = '其他';
        }

        $arr["checkup_name"] = $checkup_name;
        $arr["title"] = $checkupPicture->title;
        $arr["picture"] = JsonPicture::jsonArrayForIpad($checkupPicture->picture);

        return $arr;
    }
}