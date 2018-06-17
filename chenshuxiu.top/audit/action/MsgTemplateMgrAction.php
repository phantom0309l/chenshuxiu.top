<?php
// MsgTemplateMgrAction
class MsgTemplateMgrAction extends AuditBaseAction
{

    // 消息模板列表
    public function doList() {
        $pagenum = XRequest::getValue('pagenum', 1);
        $pagesize = XRequest::getValue('pagesize', 100);

        $diseaseid = XRequest::getValue('diseaseid', 0);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $ename = XRequest::getValue('ename', '');

        $sql = "select diseaseid
                from msgtemplates
                group by diseaseid ";
        $diseaseids = Dao::queryValues($sql, []);

        $diseases = [];
        foreach ($diseaseids as $id) {
            if ($id) {
                $diseases[] = Disease::getById($id);
            }
        }

        $cond = "";
        $bind = [];

        if ($diseaseid) {
            $cond .= " and ( diseaseid = :diseaseid or diseaseid=0 ) ";
            $bind[':diseaseid'] = $diseaseid;
        }

        if ($doctorid) {
            $cond .= " and ( doctorid = :doctorid or doctorid=0) ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($ename) {
            $cond .= " and ename = :ename ";
            $bind[':ename'] = $ename;
        }

        $cond .= " order by doctorid desc,ename,diseaseid ";

        $msgtemplates = MsgTemplateDao::getEntityListByCond4Page('MsgTemplate', $pagesize, $pagenum, $cond, $bind);

        $cntsql = "select count(*) from msgtemplates where 1 = 1 " . $cond;
        $cnt = Dao::queryValue($cntsql, $bind);
        $url = "/msgtemplatemgr/list?diseaseid={$diseaseid}&doctorid={$doctorid}&ename={$ename}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("msgtemplates", $msgtemplates);
        XContext::setValue("diseaseid", $diseaseid);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("diseases", $diseases);

        return self::SUCCESS;
    }

    // 消息模板新建
    public function doAdd() {
        $diseaseid = XRequest::getValue('diseaseid', 0);

        $diseases = Dao::getEntityListByCond("Disease");

        XContext::setValue('diseaseid', $diseaseid);
        XContext::setValue('diseases', $diseases);

        return self::SUCCESS;
    }

    public function doAddPost() {
        $diseaseid = XRequest::getValue('diseaseid', 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");
        $doctorid = XRequest::getValue('doctorid', 0);
        $ename = XRequest::getValue('ename', '');
        $title = XRequest::getValue('title', '');
        $content = XRequest::getValue('content', '');

        $row = array();
        $row['diseaseid'] = $diseaseid;
        $row['doctorid'] = $doctorid;
        $row['ename'] = $ename;
        $row['title'] = $title;
        $row['content'] = $content;

        $msgtemplate = MsgTemplate::createByBiz($row);

        XContext::setJumpPath("/msgtemplatemgr/modify?msgtemplateid={$msgtemplate->id}");

        return self::BLANK;
    }

    // 消息模板修改
    public function doModify() {
        $msgtemplateid = XRequest::getValue('msgtemplateid', 0);
        $msgtemplate = MsgTemplate::getById($msgtemplateid);
        $myauditor = $this->myauditor;

        XContext::setValue('myauditor', $myauditor);
        XContext::setValue('msgtemplate', $msgtemplate);
        return self::SUCCESS;
    }

    public function doModifyPost() {
        $msgtemplateid = XRequest::getValue('msgtemplateid', 0);
        $title = XRequest::getValue('title', '');
        $content = XRequest::getValue('content', '');

        $msgtemplate = MsgTemplate::getById($msgtemplateid);

        $msgtemplate->title = $title;
        $msgtemplate->content = $content;

        XContext::setJumpPath("/msgtemplatemgr/modify?msgtemplateid={$msgtemplate->id}");

        return self::BLANK;
    }

    public function doDeletePost() {
        $msgtemplateid = XRequest::getValue('msgtemplateid', 0);

        $msgtemplate = MsgTemplate::getById($msgtemplateid);

        $msgtemplate->remove();

        XContext::setJumpPath("/msgtemplatemgr/list");

        return self::BLANK;
    }

    public function doTestJson() {
        $msgtemplateid = XRequest::getValue('msgtemplateid', 0);
        $content = XRequest::getValue('content', 0);
        $myauditor = $this->myauditor;

        $user = $myauditor->user;

        $wxuser = $user->getMasterWxUser();

        $msgtemplate = MsgTemplate::getById($msgtemplateid);
        $ename = $msgtemplate->ename;

        $enameArr = MsgTemplate::getEnameArr();

        $arr = $enameArr['ename_type'][$ename];
        if($wxuser instanceof WxUser){
            if($arr['send_by_custom']){
                PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $myauditor, $content);
            }

            if($arr['send_by_template']){
                foreach ($arr['wxtemplate_enames'] as $k => $v) {
                    $str = '测试';
                    $first = array(
                        "value" => "测试标题",
                        "color" => "");
                    $keywords = array(
                        array(
                            "value" => $str,
                            "color" => "#aaa"),
                        array(
                            "value" => $content,
                            "color" => "#ff6600"));
                    $content = WxTemplateService::createTemplateContent($first, $keywords);
                    PushMsgService::sendTplMsgToWxUserByAuditor($wxuser, $myauditor, $v, $content);
                }
            }
        }

        echo 'ok';
        return self::BLANK;
    }
}
