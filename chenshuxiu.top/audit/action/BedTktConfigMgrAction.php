<?php

// RevisitTktConfigMgrAction
class BedTktConfigMgrAction extends AuditBaseAction
{

    public function doOne () {
        $doctorid = XRequest::getValue('doctorid');
        $doctor = Doctor::getById($doctorid);
        DBC::requireNotEmpty($doctor, "医生不存在");

        $typestr = XRequest::getValue('typestr', 'treat');
        $bedtktconfig = BedTktConfigDao::getByDoctoridType($doctor->id, $typestr);
        if ($bedtktconfig instanceof BedTktConfig) {
            XContext::setJumpPath("/bedtktconfigmgr/{$typestr}modify?bedtktconfigid={$bedtktconfig->id}");
        } else {
            XContext::setJumpPath("/bedtktconfigmgr/{$typestr}add?doctorid={$doctor->id}&typestr={$typestr}");
        }

        return self::SUCCESS;
    }

    public function doTreatAdd()
    {
        $typestr = XRequest::getValue('typestr');
        $doctorid = XRequest::getValue('doctorid');

        DBC::requireNotEmpty($typestr, "类型值不能为空");
        // typestr冗余
        DBC::requireTrue(($typestr == "treat"), "类型值非法");

        DBC::requireNotEmpty($doctorid, "医生id不能为空");
        $doctor = Doctor::getById($doctorid);
        DBC::requireNotEmpty($doctor, "医生不存在");

        XContext::setValue('typestr', $typestr);
        XContext::setValue('doctor', $doctor);
        return self::SUCCESS;
    }

    public function doTreatModify()
    {
        $bedtktconfigid = XRequest::getValue('bedtktconfigid');

        DBC::requireNotEmpty($bedtktconfigid, "预约住院id不能为空");
        $bedtktconfig = BedTktConfig::getById($bedtktconfigid);
        DBC::requireNotEmpty($bedtktconfig, "预约住院条目不存在");
        DBC::requireTrue(($bedtktconfig->typestr == "treat"), "预约住院条目关联类型出错");

        $content = json_decode($bedtktconfig->content, true);
        static $nameconfig = [
            'is_zhuyuan_photo_show' => '住院证照片',
            'is_feetype_show' => '医保类型',
            'is_xuechanggui_photo_show' => '血常规照片',
            'is_gangongneng_photo_show' => '肝功能照片',
            'is_plandate_show' => '入住日期',
            'is_idcard_show' => '身份证',
            'is_zhuyuanhao_show' => '住院号',
            'is_bingshi_show' => '病史',
            'is_linchuangbiaoxian_show' => '临床表现',
            'is_otherdisease_show' => '其他疾病',
            'is_shoushuriqi_show' => '手术日期',
            'is_xingongnengfenji_show' => '心功能分级',
            'is_xindiantu_show' => '心电图',
            'is_xueshuantanlitu_show' => '血栓弹力图',
            'is_fengshimianyijiancha_show' => '风湿免疫检查',
            'is_shuqianqitajiancha_show' => '术前其他检查',
            'is_zhuyuangoal_show' => '本次住院目的'
        ];
        XContext::setValue('nameconfig', $nameconfig);
        XContext::setValue('content', $content);
        XContext::setValue('bedtktconfig', $bedtktconfig);
        XContext::setValue('doctor', $bedtktconfig->doctor);
        XContext::setValue('typestr', $bedtktconfig->typestr);
        return self::SUCCESS;
    }

    public function doCheckupAdd()
    {
        $typestr = XRequest::getValue('typestr');
        $doctorid = XRequest::getValue('doctorid');

        DBC::requireNotNull($typestr, "typestr不能为空");
        // typestr冗余
        DBC::requireTrue(($typestr == "checkup"), "类型值非法");

        DBC::requireNotEmpty($doctorid, "医生id不能为空");
        $doctor = Doctor::getById($doctorid);
        DBC::requireNotEmpty($doctor, "医生不存在");
        XContext::setValue('typestr', $typestr);
        XContext::setValue('doctor', $doctor);
        return self::SUCCESS;
    }

    public function doCheckupModify()
    {
        $bedtktconfigid = XRequest::getValue('bedtktconfigid');

        DBC::requireNotEmpty($bedtktconfigid, "预约住院id不能为空");
        $bedtktconfig = BedTktConfig::getById($bedtktconfigid);
        DBC::requireNotEmpty($bedtktconfig, "预约住院条目不存在");
        DBC::requireTrue(($bedtktconfig->typestr == "checkup"), "预约住院条目关联类型出错");

        $content = json_decode($bedtktconfig->content, true);
        XContext::setValue('content', $content);
        XContext::setValue('bedtktconfig', $bedtktconfig);
        XContext::setValue('doctor', $bedtktconfig->doctor);
        XContext::setValue('typestr', $bedtktconfig->typestr);
        return self::SUCCESS;
    }

    public function doAddPost()
    {
        $doctorid = XRequest::getValue("doctorid");
        $typestr = XRequest::getValue("typestr");
        $isallowbedtkt = XRequest::getValue("is_allow_bedtkt");
        $content = XRequest::getValue("content");

        DBC::requireNotEmpty($doctorid, "医生id不能为空");

        $doctor = Doctor::getById($doctorid);
        DBC::requireNotEmpty($doctor, "医生不存在");

        DBC::requireNotNull($typestr, "类型不能为空");
        DBC::requireTrue($this->checkTypeStr($typestr), "类型值非法");

        DBC::requireNotNull($isallowbedtkt, "是否开通住院值不能为空");
        DBC::requireTrue(($isallowbedtkt == 0 || $isallowbedtkt == 1), "是否开通住院值非法");

        //数据约束
        $cond = "";
        $bind = [];
        $cond .= " AND doctorid = :doctorid AND typestr = :typestr LIMIT 1";
        $bind [':doctorid'] = $doctor->id;
        $bind [':typestr'] = $typestr;
        $temp = DAO::getEntityByCond("BedTktConfig", $cond, $bind);
        DBC::requireTrue(false == ($temp instanceof BedTktConfig), "条目约束冲突");

        //维护doctors同步
        if ($isallowbedtkt == 1) {
            $doctor->is_allow_bedtkt = 1;
        } else if ($isallowbedtkt == 0) {
            $cond = '';
            $bind = [];
            $cond .= "AND doctorid = :doctorid AND is_allow_bedtkt = 1 LIMIT 1";
            $bind [':doctorid'] = $doctor->id;
            $temp = DAO::getEntityByCond("BedTktConfig", $cond, $bind);
            if ($temp instanceof BedTktConfig) {
                $doctor->is_allow_bedtkt = 1;
            } else {
                $doctor->is_allow_bedtkt = 0;
            }
        }

        $row = array();
        $row['doctorid'] = $doctorid;
        $row['typestr'] = $typestr;
        $row['is_allow_bedtkt'] = $isallowbedtkt;
        if ($isallowbedtkt == 1) {
            DBC::requireNotNull($content, "内容不能为空");
            DBC::requireTrue(is_array($content), "内容格式错误");
            $row['content'] = json_encode($content, JSON_UNESCAPED_UNICODE);
        }
        $bedtktconfig = BedTktConfig::createByBiz($row);

        XContext::setJumpPath("/bedtktconfigmgr/{$typestr}modify?bedtktconfigid={$bedtktconfig->id}");
        return self::SUCCESS;
    }

    public function doModifyPost()
    {
        $bedtktconfigid = XRequest::getValue("bedtktconfigid");
        $isallowbedtkt = XRequest::getValue("is_allow_bedtkt");
        $content = XRequest::getValue("content");

        DBC::requireNotEmpty($bedtktconfigid, "预约住院id不能为空");
        $bedtktconfig = BedTktConfig::getById($bedtktconfigid);
        DBC::requireNotEmpty($bedtktconfig, "预约住院条目不存在");
        $doctor = $bedtktconfig->doctor;

        DBC::requireNotNull($isallowbedtkt, "是否开通住院值不能为空");
        DBC::requireTrue(($isallowbedtkt == 0 || $isallowbedtkt == 1), "是否开通住院值非法");

        //维护doctors同步
        if ($isallowbedtkt == 1) {
            $doctor->is_allow_bedtkt = 1;
        } else if ($isallowbedtkt == 0) {
            $cond = '';
            $bind = [];
            $cond .= " AND id != :bedtktconfigid AND doctorid = :doctorid AND is_allow_bedtkt = 1 LIMIT 1";
            $bind [':bedtktconfigid'] = $bedtktconfig->id;
            $bind [':doctorid'] = $doctor->id;
            $temp = DAO::getEntityByCond("BedTktConfig", $cond, $bind);
            if ($temp instanceof BedTktConfig) {
                $doctor->is_allow_bedtkt = 1;
            } else {
                $doctor->is_allow_bedtkt = 0;
            }
        }

        $bedtktconfig->is_allow_bedtkt = $isallowbedtkt;
        if ($isallowbedtkt == 1) {
            DBC::requireNotNull($content, "内容不能为空");
            DBC::requireTrue(is_array($content), "内容格式错误");
            $bedtktconfig->content = json_encode($content, JSON_UNESCAPED_UNICODE);
        }

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/bedtktconfigmgr/{$bedtktconfig->typestr}modify?bedtktconfigid={$bedtktconfig->id}" . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    private function checkTypeStr($typestr)
    {
        //类型 枚举类型
        $arr = ['treat', 'checkup'];
        if (in_array($typestr, $arr)) {
            return true;
        } else {
            return false;
        }
    }
}
