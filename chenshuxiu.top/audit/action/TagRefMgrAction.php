<?php

class TagRefMgrAction extends AuditBaseAction
{

    // list
    public function doList () {
        $typestr = XRequest::getValue('typestr', 'all');
        $name = XRequest::getValue('name', ''); // TODO 是否应该是 tagname ?
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);

        $sql = "select a.*
                from tagrefs a
                inner join tags b on b.id = a.tagid
                where 1 = 1 ";

        $cond = '';

        $bind = [];

        // tag->typestr 过滤
        if ($typestr != 'all') {
            $cond .= " and b.typestr = :typestr ";
            $bind[':typestr'] = $typestr;
        }

        // tag->name 过滤
        if ($name) {
            $cond .= " and b.name = :name ";
            $bind[':name'] = $name;
        }

        // obj 过滤
        if ($objtype && $objid > 0) {
            $cond .= " and a.objtype = :objtype and a.objid = :objid ";
            $bind[':objtype'] = $objtype;
            $bind[':objid'] = $objid;
        }

        $sql .= $cond;
        $sql .= " order by a.id ";

        $tagrefs = Dao::loadEntityList("TagRef", $sql, $bind);

        XContext::setValue('typestr', $typestr);
        XContext::setValue('name', $name);
        XContext::setValue('objtype', $objtype);
        XContext::setValue('objid', $objid);

        XContext::setValue('tagrefs', $tagrefs);

        return self::SUCCESS;
    }

    // add
    public function doAdd () {
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);
        $typestr = XRequest::getValue('typestr', '');

        $tagidnameArr = CtrHelper::getTagCtrArrayWithAll($typestr);

        XContext::setValue('objtype', $objtype);
        XContext::setValue('objid', $objid);
        XContext::setValue('typestr', $typestr);

        XContext::setValue('tagidnameArr', $tagidnameArr);

        return self::SUCCESS;
    }

    // addPost
    public function doAddPost () {
        $typestr = XRequest::getValue('typestr', '');

        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);
        $tagid = XRequest::getValue('tagid', 0);

        $row = array();
        $row['objtype'] = $objtype;
        $row['objid'] = $objid;
        $row['tagid'] = $tagid;

        $tagref = TagRef::createByBiz($row);

        // #4391 治疗阶段标记为【新辅助化疗中】【辅助化疗中】【晚期化疗中】【化疗】中任意一个的患者。立即生成[化疗方案收集]任务
        $needs = [
            '新辅助化疗中',
            '辅助化疗中',
            '晚期化疗中',
            '化疗'];
        if (in_array($tagref->tag->name, $needs) && $tagref->obj instanceof Patient) {
            // 生成任务: 化疗方案收集任务 (患者唯一)
            OpTaskService::tryCreateOpTaskByPatient($tagref->obj, 'chemo:collection', null, $plantime='', $this->myauditor->id);
        }

        // 提交工作单元
        $unitofwork = BeanFinder::get("UnitOfWork");
        $unitofwork->commitAndInit();

        /*
         * 定期随访
         * 类型：1 生存状态 2 准备手术 3 准备化疗 4 无瘤期(暂时不做)
         * 创建条件：
         * 1不处于【新辅助化疗中】、【辅助化疗中】、【晚期化疗中】、【化疗】的患者。修改治疗阶段标签时符合条件生成。
         * 2 定期随访自生成（详见后）
         * 四类型条件：
         * 1 生存状态（28天）：【晚期化疗后】【放弃治疗】【中药治疗】【免疫治疗】
         * 2 准备手术（7天）：【准备手术】
         * 3 准备化疗（7天）：【新辅助化疗前】【辅助化疗前】【晚期化疗前】
         * 4 无瘤期（无瘤期（0-42）12 （42-146）24（157-∞）52）：【无瘤期】
         * 其中4个任务的优先级为 准备化疗 > 准备手术 >无瘤期 >生存状态
         *
         * 'shengcunstatus' => '生存状态',
         * 'zhunbeishoushu' => '准备手术',
         * 'zhunbeihualiao' => '准备化疗',
         * 'wuliaoqi' => '无瘤期'
         */
        $patient = $tagref->obj;
        $cancer_diseaseids = Disease::getCancerDiseaseidArray();
        if ($patient instanceof Patient && in_array($patient->diseaseid, $cancer_diseaseids)) {

            // 生成任务: 肿瘤定期随访任务 (患者唯一)
            OpTaskService::tryCreateOpTask_Regular_follow($patient, $this->myauditor->id);
        }

        XContext::setJumpPath("/tagrefmgr/list?objtype={$objtype}&objid={$objid}&typestr={$typestr}");

        return self::SUCCESS;
    }

    // delete
    public function doDeletePost () {
        $tagrefid = XRequest::getValue('tagrefid', 0);
        $objid = XRequest::getValue('objid', 0);
        $objtype = XRequest::getValue('objtype', '');
        $typestr = XRequest::getValue('typestr', '');

        $tagref = TagRef::getById($tagrefid);
        $tagref->remove();

        XContext::setJumpPath("/tagrefmgr/list?objtype={$objtype}&objid={$objid}&typestr={$typestr}");

        return self::BLANK;
    }

    // 根据患者诊断类型进行 增/删 数据
    public function doAddOrDelByPatientDiagnosis () {
        $patientDiagnosisids = XRequest::getValue('patientDiagnosisids',[]);
        $patientid = XRequest::getValue('patientid',0);

        $patient = Patient::getById($patientid);
        DBC::requireTrue($patient instanceof Patient, '患者不存在');

        $checkedTagids = TagService::getTagidsByObj($patient,'patientDiagnosis');

        // 删除的逻辑
        // 先将该患者已经选中的指定tagref记录在内存中置为 remove 状态，
        // 再通过页面传来的 tagid 去将标为remove 状态的tagref 置为 unremove
        foreach ($checkedTagids as $tagid) {
            $tagRefChecked = TagRefDao::getByObjtypeObjidTagid('Patient',$patientid,$tagid);
            $tagRefChecked->remove();
        }

        if (!empty($patientDiagnosisids)) {
            foreach ($patientDiagnosisids as $patientDiagnosisid) {
                $this->addOrUnRemoveTagRef($patient,$patientDiagnosisid);
            }
        }

        // 提交工作单元
        $unitofwork = BeanFinder::get("UnitOfWork");
        $unitofwork->commitAndInit();

        // 获取
        $patientDiagnosisStr = TagRefDao::getTagNamesStr($patient,'patientDiagnosis');
        $patientDiagnosisStr = $patientDiagnosisStr == ' - ' ? "未知" : $patientDiagnosisStr;

        $json = json_encode(array('tagnameStr'=>$patientDiagnosisStr));

        XContext::setValue("outdatas",$json);
        return self::JSON;
    }

    private function addOrUnRemoveTagRef (Entity $obj,$tagid) {
        $row = array();
        $row["objtype"] = get_class($obj);
        $row["objid"] = $obj->id;
        $row['tagid'] = $tagid;

        $tagRef = TagRef::createByBiz($row);
        $tagRef->unRemove();

        return $tagRef;
    }

}