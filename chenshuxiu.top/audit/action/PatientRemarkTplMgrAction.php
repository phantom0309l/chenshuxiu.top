<?php
// PatientRemarkTplMgrAction
class PatientRemarkTplMgrAction extends AuditBaseAction
{

    public function doList () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        XContext::setValue('doctor', $doctor);

        $patientremarktpls = PatientRemarkTplDao::getListByDoctorid($doctorid);
        $i = 0;
        foreach ($patientremarktpls as $a) {
            $i ++;
            $a->pos = $i;
        }

        XContext::setValue('patientremarktpls', $patientremarktpls);

        return self::SUCCESS;
    }

    public function doOne () {
        return self::SUCCESS;
    }

    public function doAdd () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        XContext::setValue('doctor', $doctor);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $name = XRequest::getValue("name", '');
        $typestr = XRequest::getValue("typestr", 'symptom');
        $pos = XRequest::getValue("pos", 0);

        $doctor = Doctor::getById($doctorid);

        $row = array();
        $row['doctorid'] = $doctorid;
        $row['diseaseid'] = $doctor->getMasterDisease()->id;
        $row['typestr'] = $typestr;
        $row['name'] = $name;
        $row['pos'] = $pos;

        $patientremarktpl = PatientRemarkTpl::createByBiz($row);

        $preMsg = "已添加完毕 " . XDateTime::now();
        XContext::setJumpPath("/patientremarktplmgr/modify?patientremarktplid={$patientremarktpl->id}&preMsg=" . urlencode($preMsg));
        return self::BLANK;
    }

    public function doModify () {
        $patientremarktplid = XRequest::getValue("patientremarktplid", 0);
        $patientremarktpl = PatientRemarkTpl::getById($patientremarktplid);

        XContext::setValue('patientremarktpl', $patientremarktpl);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $patientremarktplid = XRequest::getValue("patientremarktplid", 0);
        $name = XRequest::getValue("name", '');
        $typestr = XRequest::getValue("typestr", 'symptom');
        $pos = XRequest::getValue("pos", 0);

        $patientremarktpl = PatientRemarkTpl::getById($patientremarktplid);
        $patientremarktpl->name = $name;
        $patientremarktpl->typestr = $typestr;
        $patientremarktpl->pos = $pos;

        $preMsg = "已修改完毕 " . XDateTime::now();
        XContext::setJumpPath("/patientremarktplmgr/modify?patientremarktplid={$patientremarktpl->id}&preMsg=" . urlencode($preMsg));
        return self::BLANK;
    }

    // 修改排序
    public function doPosModifyPost () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $id => $pos) {
            $patientremarktpl = PatientRemarkTpl::getById($id);
            $patientremarktpl->pos = $pos;
        }

        $preMsg = "已保存顺序调整,并修正序号 " . XDateTime::now();
        XContext::setJumpPath("/patientremarktplmgr/list?doctorid={$doctorid}&preMsg=" . urlencode($preMsg));
        return self::BLANK;
    }

    // 删除
    public function doDeletePost () {
        $patientremarktplid = XRequest::getValue("patientremarktplid", 0);

        $patientremarktpl = PatientRemarkTpl::getById($patientremarktplid);

        $commonwords = CommonWordDao::getListByOwnertypeOwneridTypestr("PatientRemarkTpl", $patientremarktplid, "symptom");
        foreach( $commonwords as $commonword ){
            $commonword->remove();
        }

        $patientremarktpl->remove();
        echo "ok";

        return self::BLANK;
    }

    public function doCopyPost () {
        $fromdoctorid = XRequest::getValue("fromdoctorid", 0);
        $todoctorid = XRequest::getValue("todoctorid", 0);

        $fromdoctor = Doctor::getById($fromdoctorid);
        $todoctor = Doctor::getById($todoctorid);

        if( false == $fromdoctor instanceof Doctor ){
            $preMsg = "填写的医生id{$fromdoctorid} 不正确" . XDateTime::now();
            XContext::setJumpPath("/patientremarktplmgr/list?doctorid={$todoctorid}&preMsg=" . urlencode($preMsg));
            return self::BLANK;
        }

        if( false == $todoctor instanceof Doctor ){
            $preMsg = "请从医生列表进入" . XDateTime::now();
            XContext::setJumpPath("/patientremarktplmgr/list?doctorid={$todoctorid}&preMsg=" . urlencode($preMsg));
            return self::BLANK;
        }
        $patientremarktpls_from = PatientRemarkTplDao::getListByDoctorid($fromdoctorid);

        foreach( $patientremarktpls_from as $a ){
            $commonwords = CommonWordDao::getListByOwnertypeOwneridTypestr("PatientRemarkTpl", $a->id, $a->typestr);
            $row = array();
            $row['doctorid'] = $todoctorid;
            $row['typestr'] = $a->typestr;
            $row['name'] = $a->name;
            $row['pos'] = $a->pos;

            $new_patientremarktpl = PatientRemarkTpl::createByBiz($row);

            foreach ($commonwords as $b) {
                $row = array();
                $row['ownertype'] = $b->ownertype;
                $row['ownerid'] = $new_patientremarktpl->id;
                $row['typestr'] = $b->typestr;
                $row['groupstr'] = $b->groupstr;
                $row['content'] = $b->content;
                $row['weight'] = $b->weight;

                CommonWord::createByBiz($row);
            }
        }

        $preMsg = "已添加完毕 " . XDateTime::now();
        XContext::setJumpPath("/patientremarktplmgr/list?doctorid={$todoctorid}&preMsg=" . urlencode($preMsg));
        return self::BLANK;
    }
}
