<?php

class JsonCheckup
{

    // jsonArrayForAdmin
    public static function jsonArrayForAdmin (Checkup $checkup) {
        $arr = array();
        $arr = $checkup->toJsonArray();

        if ($checkup->checkuptpl instanceof CheckupTpl) {
            $arr['checkuptpl'] = $checkup->checkuptpl->toJsonArray();
            $checkuppictures = CheckupPictureDao::getListByCheckupid($checkup->id);
            foreach ($checkuppictures as $checkuppicture) {
                $arr['checkuppictures'][] = $checkuppicture->toJsonArray();
            }
        }

        $arr['xanswersheet'] = '';
        if ($checkup->xanswersheetid > 0) {
            $arr['xanswersheet'] = JsonXAnswerSheet::jsonArrayForAdmin($checkup->xanswersheet);
        }

        return $arr;
    }
}