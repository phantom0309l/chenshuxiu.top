<?php

class JsonPicture
{
    // jsonArray
    public static function jsonArray (Picture $picture, $box_width = 0, $box_height = 0, $iscut = true, $isfill = false) {
        $thumbArray = $picture->toJsonArrayThumb($box_width, $box_height, $iscut, $isfill);
        return array(
            "pictureid" => $picture->id,
            "url" => $picture->getSrc(),
            "width" => $picture->width,
            "height" => $picture->height,
            "size" => $picture->size,
            "box_width" => $box_width,
            "box_height" => $box_height,
            "iscut" => $iscut ? 1 : 0,
            "isfill" => $isfill ? 1 : 0,
            "thumb_url" => $thumbArray['thumb_url'],
            "thumb_width" => $thumbArray['thumb_width'],
            "thumb_height" => $thumbArray['thumb_height']);
    }

    // jsonArrayForIpad
    public static function jsonArrayForIpad (Picture $picture) {
        $thumbArray = $picture->toJsonArrayThumb(300, 300, 0, 0);

        return array(
            "pictureid" => $picture->id,
            "url" => $picture->getSrc(),
            "width" => $picture->width,
            "height" => $picture->height,
            "thumb_url" => $thumbArray['thumb_url'],
            "thumb_width" => $thumbArray['thumb_width'],
            "thumb_height" => $thumbArray['thumb_height']);
    }
}