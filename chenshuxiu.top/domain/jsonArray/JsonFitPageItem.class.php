<?php

class JsonFitPageItem
{
    // jsonArray
    public static function jsonArray (FitPageItem $fitPageItem) {
        return array(
            'code' => $fitPageItem->fitpagetplitem->code,
            'name' => $fitPageItem->fitpagetplitem->name,
            'must' => 0,
            'default' => '',
            'options' => $fitPageItem->content ? explode('|', $fitPageItem->content) : array());
    }

    // jsonArrayArrayOfFitPageForAdmin
    public static function jsonArrayArrayOfFitPageForAdmin (FitPage $fitpage) {
        $fitpageitems = $fitpage->getFitPageItems();

        $arr = array();

        foreach ($fitpageitems as $a) {
            $arr[] = JsonFitPageItem::jsonArray($a);
        }

        return $arr;
    }

    public static function jsonArrayArrayOfFitPageForNewAdmin (FitPage $fitpage) {
        $fitpageitems = $fitpage->getFitPageItems();

        $list = [];
        foreach ($fitpageitems as $a) {
            $arr = [
                'code' => $a->fitpagetplitem->code,
                'title' => $a->fitpagetplitem->name,
                'ismust' => $a->ismust,
                'value' => ""
            ];

            $list[] = $arr;
        }

        return $list;
    }
}
