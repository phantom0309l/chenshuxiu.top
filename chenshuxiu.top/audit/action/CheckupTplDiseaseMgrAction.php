<?php
// CheckupTplMgrAction
class CheckupTplDiseaseMgrAction extends AuditBaseAction
{

    // 检查报告模板(疾病)列表
    public function doList () {
        $title = XRequest::getValue('title','');

        $cond = "";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond= " and diseaseid in ($diseaseidstr) ";

        if($title != ''){
            $cond .= " and title like :title ";
            $bind[':title'] = "%{$title}%";
        }

        $checkuptpls = Dao::getEntityListByCond('CheckupTpl', $cond, $bind);

        $i = 0;
        foreach ($checkuptpls as $a) {
             $i ++;
             $a->pos = $i;
        }

        XContext::setValue('title',$title);
        XContext::setValue('checkuptpls', $checkuptpls);

        return self::SUCCESS;
    }

    // 检查报告模板(疾病)新建
    public function doAdd () {
        $doctors = Dao::getEntityListByCond('Doctor');
        XContext::setValue('doctors', $doctors);

        DBC::requireNotEmpty($this->mydisease, "必须选疾病");

        // 获取当前疾病之外的所有疾病的模板
        $cond = " and diseaseid != :diseaseid";
        $bind = [
            ':diseaseid' => $this->mydisease->id
        ];
        $checkuptpls = Dao::getEntityListByCond('CheckupTpl', $cond, $bind);

        $checkuptpls_arr = array( 0 => '不选' );
        foreach( $checkuptpls as $checkuptpl ){
            $tmpstr = '';
            if( $checkuptpl->disease instanceof Disease ){
                $tmpstr .= "[{$checkuptpl->disease->name}]";
            }else{
                $tmpstr .= "[疾病通用]";
            }

            if( $checkuptpl->doctor instanceof Doctor ){
                $tmpstr .= " [{$checkuptpl->doctor->name}]";
            }else{
                $tmpstr .= " [医生通用]";
            }

            $tmpstr .= " - {$checkuptpl->title}";
            $checkuptpls_arr[$checkuptpl->id] = $tmpstr;
        }

        XContext::setValue('checkuptpls_arr', $checkuptpls_arr);
        return self::SUCCESS;
    }

    public function doAddPost () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $checkuptplid = XRequest::getValue('checkuptplid', 0);
        $groupstr = XRequest::getValue('groupstr', '');
        $title = XRequest::getValue('title', '');
        $brief = XRequest::getValue('brief', '');
        $content = XRequest::getValue('content', '');
        $ename = XRequest::getValue('ename', '0');

        DBC::requireTrue($ename != "zhiliao", '不能使用"zhiliao"为ename，请修改');

        DBC::requireNotEmpty($this->mydisease, "必须选疾病");

        $row = [];
        $row['diseaseid'] = $this->mydisease->id;
        $row['doctorid'] = $doctorid;
        $row['groupstr'] = $groupstr;
        $row['title'] = $title;
        $row['brief'] = $brief;
        $row['content'] = $content;
        $row['ename'] = $ename;

        $checkuptplnew = CheckupTpl::createByBiz($row);

        if( $checkuptplid > 0 ){
            $checkuptpl = CheckupTpl::getById($checkuptplid);
            $checkuptpl->copyXQuestionSheetTo($checkuptplnew);
        }

        XContext::setJumpPath("/checkuptpldiseasemgr/list");

        return self::SUCCESS;
    }

    public function doModify () {
        $checkuptplid = XRequest::getValue('checkuptplid', 0);

        $checkuptpl = CheckupTpl::getById($checkuptplid);

        XContext::setValue('checkuptpl', $checkuptpl);
        return self::SUCCESS;
    }

    // 检查报告模板(疾病)修改
    public function doModifyPost () {
        $checkuptplid = XRequest::getValue('checkuptplid', 0);
        $doctorid = XRequest::getValue('doctorid', 0);
        $groupstr = XRequest::getValue('groupstr', '');
        $title = XRequest::getValue('title', '');
        $brief = XRequest::getValue('brief', '');
        $content = XRequest::getValue('content', '');
        $ename = XRequest::getValue('ename', '');

        $checkuptpl = CheckupTpl::getById($checkuptplid);
        $checkuptpl->doctorid = $doctorid;
        $checkuptpl->groupstr = $groupstr;
        $checkuptpl->title = $title;
        $checkuptpl->brief = $brief;
        $checkuptpl->content = $content;
        $checkuptpl->ename = $ename;

        $preMsg = "修改已提交 " . XDateTime::now();
        XContext::setJumpPath("/checkuptpldiseasemgr/modify?checkuptplid={$checkuptplid}&preMsg=" . urlencode($preMsg));

        return self::SUCCESS;
    }

    // 修改排序
    public function doPosModifyPost () {
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $id => $pos) {
            $checkuptpl = CheckupTpl::getById($id);
            $checkuptpl->pos = $pos;
        }

        $preMsg = "已保存顺序调整,并修正序号 " . XDateTime::now();
        XContext::setJumpPath("/checkuptpldiseasemgr/list?preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doDeleteJson () {
        $checkuptplid = XRequest::getValue('checkuptplid', 0);

        $checkuptpl = CheckupTpl::getById($checkuptplid);
        $checkuptpl->remove();

        echo "success";

        return self::BLANK;
    }
}
