<?php
// 名称: FitPageService
// 备注: 可配置页面服务类
// 创建: 20170419 by sjp
class FitPageService
{
    // getFitPageByCodeDiseaseidDoctorid
    // 创建: 20170419 by sjp
    public static function getFitPageByCodeDiseaseidDoctorid($code, $diseaseid = 0, $doctorid = 0) {
        $fitpage = FitPageDao::getByCodeDiseaseidDoctorid($code, $diseaseid, $doctorid);

        // 指定疾病
        if (false == $fitpage instanceof FitPage) {
            $fitpage = FitPageDao::getByCodeDiseaseidDoctorid($code, $diseaseid, 0);
        }

        // 不指定医生和疾病
        if (false == $fitpage instanceof FitPage) {
            $fitpage = FitPageDao::getByCodeDiseaseidDoctorid($code, 0, 0);
        }

        return $fitpage;
    }
}
