<?php

// DoctorConfigMgrAction
class DoctorConfigMgrAction extends AuditBaseAction
{

    public function doOverView () {
        $doctorid = XRequest::getValue("doctorid", 0);

        $doctor = Doctor::getById($doctorid);
        $diseases = $doctor->getDiseases();
        $hospitals = Dao::getEntityListByCond("Hospital");
        $comments = CommentDao::getArrayOfDoctor($doctorid);
        $cond = '';
        $cond .= ' and doctorid=:doctorid ';
        $bind[':doctorid'] = $doctorid;
        $doctorWxShopRefs = Dao::getEntityListByCond('DoctorWxShopRef', $cond, $bind);
        // trick里面有赋值操作，需要调用一下，否则模板取数据会出错
        foreach ($doctorWxShopRefs as $doctorWxShopRef) {
            $doctorWxShopRef->getQrUrl();
        }

        // 获取主管医生
        $doctorSuperiors = Doctor_SuperiorDao::getListByDoctorid($doctor->id);

        // 综合业务开通情况
        $bind = [
            ':doctorid' => $doctor->id];
        $diseasesBiz = [];
        foreach ($diseases as $disease) {
            $bind[':diseaseid'] = $disease->id;
            $sql = "SELECT id FROM revisittktconfigs WHERE doctorid=:doctorid AND diseaseid=:diseaseid AND status=1 LIMIT 1";

            $ret1 = Dao::queryValue($sql, $bind);

            $sql = "SELECT id FROM fitpages WHERE code IN ('patientbaseinfo', 'patientpcard', 'diseasehistory') AND doctorid=:doctorid AND diseaseid=:diseaseid LIMIT 1";
            $ret4 = Dao::queryValue($sql, $bind);

            unset($bind[':diseaseid']);

            $sql = "SELECT id FROM bedtktconfigs WHERE doctorid=:doctorid AND typestr='treat' AND is_allow_bedtkt=1 LIMIT 1";
            $ret2 = Dao::queryValue($sql, $bind);

            $sql = "SELECT id FROM bedtktconfigs WHERE doctorid=:doctorid AND typestr='checkup' AND is_allow_bedtkt=1 LIMIT 1";
            $ret3 = Dao::queryValue($sql, $bind);

            $diseasesBiz[$disease->name][] = $ret1 ? 1 : 0;
            $diseasesBiz[$disease->name][] = $ret2 ? 1 : 0;
            $diseasesBiz[$disease->name][] = $ret3 ? 1 : 0;
            $diseasesBiz[$disease->name][] = $ret4 ? 1 : 0;
        }

        // 门诊表
        $scheduletpls = $doctor->getScheduleTpls();
        $scheduletplTable = ScheduleTplService::getTableForDoctor($scheduletpls);

        XContext::setValue("diseasesBiz", $diseasesBiz);
        XContext::setValue("scheduletplTable", $scheduletplTable);
        XContext::setValue("doctorWxShopRefs", $doctorWxShopRefs);
        XContext::setValue("diseases", $diseases);
        XContext::setValue("doctor", $doctor);
        XContext::setValue("hospitals", $hospitals);
        XContext::setValue("doctorSuperiors", $doctorSuperiors);

        return self::SUCCESS;
    }

    public function doAjaxAutoConfig () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        $diseases = $doctor->getDiseases();
        DBC::requireNotEmpty($doctor, "医生为空，doctorid{$doctorid}");

        // 复诊
        $this->revisitTktConfig($doctor, $diseases);
        // 住院
        $this->bedtktConfig($doctor);
        // 量表
        $this->diseasepapertplrefConfig($doctor, $diseases);

        return self::TEXTJSON;
    }

    // 复诊
    private function revisitTktConfig ($doctor, $diseases) {
        foreach ($diseases as $disease) {
            $revisittktconfig = RevisitTktConfigDao::getByDoctorDisease($doctor, $disease);
            if (false == $revisittktconfig instanceof RevisitTktConfig) {
                $row = [];
                $row['doctorid'] = $doctor->id;
                $row['diseaseid'] = $disease->id;

                $revisittktconfig = RevisitTktConfig::createByBiz($row);
            }
            $revisittktconfig->status = 0;
            $revisittktconfig->remind_notice = '您预约了#doctor_name##thedate# 门诊复诊，提醒您提前安排好时间，按时复诊。';
            $revisittktconfig->confirm_notice = '您预约了#doctor_name#医生#thedate#门诊复诊，您是否可以按时复诊？请点击详情确认。';
            $revisittktconfig->confirm_content_yes = '#patient_name#您好，您已经预约了#doctor_name#医生#thedate#门诊，请您在#begin_hour#到达#address#候诊，请您按时到达。';
        }
    }

    // 住院
    private function bedtktConfig ($doctor) {
        $doctorid = $doctor->id;

        $typestrs = [
            'treat',
            'checkup'];

        $is_allow_bedtkt = 0;
        foreach ($typestrs as $typestr) {
            $bedtktconfig = BedTktConfigDao::getByDoctoridType($doctor->id, $typestr);
            if (false == $bedtktconfig instanceof BedTktConfig) {
                $row = array();
                $row['doctorid'] = $doctorid;
                $row['typestr'] = $typestr;
                $row['is_allow_bedtkt'] = $is_allow_bedtkt;
                $bedtktconfig = BedTktConfig::createByBiz($row);
            }
            // 维护doctors同步
            $doctor->is_allow_bedtkt = $is_allow_bedtkt;
            $bedtktconfig->is_allow_bedtkt = $is_allow_bedtkt;

            $content = json_decode($bedtktconfig->content, true) ?? [];

            $content["is_feetype_show"] = "0";
            $content["is_feetype_must"] = "0";
            $content["is_plandate_show"] = "0";
            $content["is_plandate_must"] = "0";
            $content["is_zhuyuan_photo_show"] = "0";
            $content["is_zhuyuan_photo_must"] = "0";
            $content["is_xuechanggui_photo_show"] = "0";
            $content["is_xuechanggui_photo_must"] = "0";
            $content["is_gangongneng_photo_show"] = "0";
            $content["is_gangongneng_photo_must"] = "0";
            $content["is_idcard_show"] = "0";
            $content["is_idcard_must"] = "0";
            $content["is_zhuyuanhao_show"] = "0";
            $content["is_zhuyuanhao_must"] = "0";
            $content["is_bingshi_show"] = "0";
            $content["is_bingshi_must"] = "0";
            $content["is_linchuangbiaoxian_show"] = "0";
            $content["is_linchuangbiaoxian_must"] = "0";
            $content["is_otherdisease_show"] = "0";
            $content["is_otherdisease_must"] = "0";
            $content["is_xingongnengfenji_show"] = "0";
            $content["is_xingongnengfenji_must"] = "0";
            $content["is_shoushuriqi_show"] = "0";
            $content["is_shoushuriqi_must"] = "0";
            $content["is_xindiantu_show"] = "0";
            $content["is_xindiantu_must"] = "0";
            $content["is_xueshuantanlitu_show"] = "0";
            $content["is_xueshuantanlitu_must"] = "0";
            $content["is_fengshimianyijiancha_show"] = "0";
            $content["is_fengshimianyijiancha_must"] = "0";
            $content["is_shuqianqitajiancha_show"] = "0";
            $content["is_shuqianqitajiancha_must"] = "0";
            $content['notice_content'] = '预约住院需要提交近3日血常规报告检查单照片和住院证正面';
            $content['yuyuenotice_content'] = '您的住院申请已提交，正在审核中。';
            $content['auditpass_notice_content'] = '您的住院申请已通过审核，请保持电话畅通会有医生与您联系。';
            $content['auditrefuse_notice_content'] = '您的住院申请未通过，如有问题请与我们联系。';

            $bedtktconfig->content = json_encode($content, JSON_UNESCAPED_UNICODE);
        }
    }

    // 量表
    private function diseasepapertplrefConfig ($doctor, $diseases) {
        foreach ($diseases as $disease) {
            if (Disease::isCancer($disease->id)) {
                // 肿瘤：《入组基本信息表》（ID:480664436）、《化疗不良反应评估表》（ID:234470506）、《靶向药不良反应评估表》（ID:234502226
                // ）
                /*
                 * #4752 补充：
                 * 放疗不良反应评估（ID:292499836）(运营可见、患者可见)
                 * NRS2002营养筛查表(ID：262357306)（运营可见）
                 * NRS2002评估表（ID:268541796）（运营可见）
                 * 疼痛分级表（ID：222413726）(运营可见、患者可见)
                 */
                $papertplids = [
                    480664436 => [
                        'show_in_audit' => 1,
                        'show_in_wx' => 1],
                    234470506 => [
                        'show_in_audit' => 1,
                        'show_in_wx' => 1],
                    234502226 => [
                        'show_in_audit' => 1,
                        'show_in_wx' => 1],

                    292499836 => [
                        'show_in_audit' => 1,
                        'show_in_wx' => 1],
                    262357306 => [
                        'show_in_audit' => 1,
                        'show_in_wx' => 0],
                    268541796 => [
                        'show_in_audit' => 1,
                        'show_in_wx' => 0],
                    222413726 => [
                        'show_in_audit' => 1,
                        'show_in_wx' => 1]];
                foreach ($papertplids as $papertplid => $configs) {
                    $papertpl = PaperTpl::getById($papertplid);
                    $diseasePaperTplRef = DiseasePaperTplRefDao::getByDoctorAndDiseaseAndPaperTpl($doctor, $disease, $papertpl);
                    if (false == $diseasePaperTplRef instanceof DiseasePaperTplRef) {
                        $row = [];
                        $row["doctorid"] = $doctor->id;
                        $row["diseaseid"] = $disease->id;
                        $row["papertplid"] = $papertplid;

                        $diseasePaperTplRef = DiseasePaperTplRef::createByBiz($row);
                    }
                    $diseasePaperTplRef->show_in_audit = $configs['show_in_audit'];
                    $diseasePaperTplRef->show_in_wx = $configs['show_in_wx'];
                }
            }
        }
    }

    public function doFitPage () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);

        $diseaseid = XRequest::getValue('diseaseid', 0);
        $code = XRequest::getValue('code', 'baodao');

        $diseases = $doctor->getDiseases();
        if (! $diseaseid) {
            $diseaseid = $diseases[0]->id;
        }

        $isdefault = false;

        // 判断当前医生的当前疾病是否有fitpage配置
        $sql = 'SELECT id FROM fitpages WHERE doctorid=:doctorid AND diseaseid=:diseaseid AND code=:code LIMIT 1';
        $bind = [
            ':doctorid' => $doctor->id,
            ':diseaseid' => $diseaseid,
            ':code' => $code];
        $fitpageid = Dao::queryValue($sql, $bind);
        if (! $fitpageid) {
            $isdefault = true;
            $defaultFitPage = Dao::getEntityByCond('FitPage', ' AND code=:code AND doctorid=0 AND diseaseid=0', [
                ':code' => $code]);
            DBC::requireNotEmpty($defaultFitPage, '默认配置获取失败');
            $fitpageid = $defaultFitPage->id;
        }

        $fitpagetpl = Dao::getEntityByCond('FitPageTpl', ' AND code=:code', [
            ':code' => $code]);
        DBC::requireNotEmpty($fitpagetpl, '模板id获取失败');

        $cond = " AND fitpagetplid = :fitpagetplid ORDER BY pos";
        $bind = [];
        $bind[':fitpagetplid'] = $fitpagetpl->id;
        // 元素库
        $fitpagetplitems = Dao::getEntityListByCond('FitPageTplItem', $cond, $bind);
        $ids = [];
        foreach ($fitpagetplitems as $a) {
            $ids[] = $a->id;
        }
        $fitpagetplitemidstr = implode('|', $ids);

        // 取实例元素
        $fitpage = FitPage::getById($fitpageid);

        $cond = " AND fitpageid = :fitpageid ORDER BY pos";
        $bind = [];
        $bind[':fitpageid'] = $fitpageid;

        $fitpageitems = Dao::getEntityListByCond('FitPageItem', $cond, $bind);

        $ids = [];
        foreach ($fitpageitems as $a) {
            $ids[] = $a->fitpagetplitemid;
        }
        $fitpageitemidstr = implode('|', $ids);

        $ismusts = [];
        foreach ($fitpageitems as $a) {
            $ismusts[] = $a->fitpagetplitemid . '-' . $a->ismust;
        }
        $ismuststr = implode('|', $ismusts);
        // 去除元素库中在实例元素中存在的元素
        // foreach ($fitpagetplitems as $i => $tplitem) {
        // foreach ($fitpageitems as $item) {
        // if ($tplitem->id == $item->fitpagetplitemid) {
        // unset($fitpagetplitems[$i]);
        // }
        // }
        // }

        // 处理序号
        $i = 0;
        foreach ($fitpagetplitems as $a) {
            $i ++;
            $a->pos = $i;
        }

        XContext::setValue('isdefault', $isdefault);
        XContext::setValue('diseaseid', $diseaseid);
        XContext::setValue('diseases', $diseases);
        XContext::setValue('fitpagetpl', $fitpagetpl);
        XContext::setValue('fitpage', $fitpage);
        XContext::setValue('fitpagetplitems', $fitpagetplitems);
        XContext::setValue('fitpageitems', $fitpageitems);
        XContext::setValue('fitpageitemidstr', $fitpageitemidstr);
        XContext::setValue('fitpagetplitemidstr', $fitpagetplitemidstr);
        XContext::setValue('ismuststr', $ismuststr);
        XContext::setValue("doctor", $doctor);
        return self::SUCCESS;
    }

    public function doAddFitPage () {
        $code = XRequest::getValue('code', '');
        $fitpagetpl = Dao::getEntityByCond('FitPageTpl', ' AND code=:code', [
            ':code' => $code]);

        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        $diseaseid = XRequest::getValue('diseaseid', 0);
        $disease = Dao::getEntityById('disease', $diseaseid);

        XContext::setValue('disease', $disease);
        XContext::setValue('fitpagetpl', $fitpagetpl);
        XContext::setValue("doctor", $doctor);

        return self::SUCCESS;
    }

    public function doAddFitPagePost () {
        $diseaseid = XRequest::getValue('diseaseid', 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");
        $doctorid = XRequest::getValue('doctorid', 0);
        $remark = XRequest::getValue('remark', '');

        $code = XRequest::getValue('code', '');
        $fitpagetpl = Dao::getEntityByCond('FitPageTpl', ' AND code=:code', [
            ':code' => $code]);

        $row = array();
        $row['fitpagetplid'] = $fitpagetpl->id;
        $row['code'] = $fitpagetpl->code;
        $row['diseaseid'] = $diseaseid;
        $row['doctorid'] = $doctorid;
        $row['remark'] = $remark;

        $fitpage = FitPage::createByBiz($row);

        XContext::setJumpPath("/doctorconfigmgr/fitpage?doctorid={$doctorid}&diseaseid=$diseaseid&code={$fitpagetpl->code}");
    }

    public function doConfigFitPagePost () {
        $fitpageid = XRequest::getValue('fitpageid', 0);
        $fitpagetplid = XRequest::getValue('fitpagetplid', 0);

        $poss = XRequest::getValue('pos', []);
        $tplids = XRequest::getValue('tplid', []);
        $ismusts = XRequest::getValue('ismust', []);

        $list = [
            'pos' => $poss,
            'tplids' => $tplids,
            'ismusts' => $ismusts];

        $fitpage = FitPage::getById($fitpageid);
        $fitpageitems = FitPageItemDao::getListByFitPage($fitpage);
        foreach ($fitpageitems as $a) {
            $a->remove();
        }

        foreach ($tplids as $fitpagetplitemid) {
            $row = [
                'fitpageid' => $fitpageid,
                'fitpagetplitemid' => $fitpagetplitemid,
                'ismust' => $ismusts["{$fitpagetplitemid}"] ?? 1,
                'pos' => $poss["{$fitpagetplitemid}"]];

            $fitpageitem = FitPageItem::createByBiz($row);
        }

        // 修改序号
        foreach ($poss as $fitpagetplitemid => $pos) {
            $fitpagetplitem = FitPageTplItem::getById($fitpagetplitemid);
            $fitpagetplitem->pos = $pos;
        }

        XContext::setJumpPath("/doctorconfigmgr/fitpage?doctorid={$fitpage->doctor->id}&diseaseid={$fitpage->disease->id}&code={$fitpage->fitpagetpl->code}");
        return self::BLANK;
    }
}
