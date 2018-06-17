<?php

class JsonCheckupTpl
{

    // jsonArrayForDapi
    public static function jsonArrayForDapi (CheckupTpl $checkupTpl) {
        $arr = array();

        $arr["checkuptplid"] = $checkupTpl->id;
        $arr["title"] = $checkupTpl->title;

        return $arr;
    }

    // jsonArrayForIpad
    public static function jsonArrayForIpad (CheckupTpl $checkupTpl) {
        $arr = array();

        $arr["checkuptplid"] = $checkupTpl->id;
        $arr["title"] = $checkupTpl->title;

        // 这里是个坑: isselected != is_selected
        $arr["isselected"] = $checkupTpl->is_selected;

        return $arr;
    }

    // jsonArrayForDwx
    public static function jsonArrayForDwx (CheckupTpl $checkupTpl) {
        $arr = array();

        $arr["checkuptplid"] = $checkupTpl->id;
        $arr["title"] = $checkupTpl->title;
        $arr["is_selected"] = $checkupTpl->is_selected;

        return $arr;
    }

    // jsonArrayTkt
    public static function jsonArrayTkt (CheckupTpl $checkupTpl) {
        $arr = array();

        // 这里是个坑 : checkuptplid_tkt
        $arr["checkuptplid_tkt"] = $checkupTpl->id;
        $arr["title"] = $checkupTpl->title;
        $arr["content"] = $checkupTpl->content;
        $arr["is_selected"] = $checkupTpl->is_selected;

        return $arr;
    }

    // jsonArrayTktForList 修改时回显
    public static function jsonArrayTktForList (CheckupTpl $checkupTpl, $ids = array()) {
        $arr = array();

        // 这里是个坑 : checkuptplid_tkt
        $arr["checkuptplid_tkt"] = $checkupTpl->id;
        $arr["title"] = $checkupTpl->title;

        if (empty($ids)) {
            $arr["is_selected"] = $checkupTpl->is_selected;
        } else {
            if (in_array($checkupTpl->id, $ids)) {
                $arr["is_selected"] = 1;
            } else {
                $arr["is_selected"] = 0;
            }
        }

        return $arr;
    }
}
