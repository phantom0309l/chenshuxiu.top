<?php
// AssistantMgrAction
class AssistantMgrAction extends AuditBaseAction
{

    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $cond = "";
        $bind = [];

        if ($doctorid) {
            $cond .= " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        $cond .= " order by id desc ";

        $assistants = Dao::getEntityListByCond4Page("Assistant", $pagesize, $pagenum, $cond, $bind);

        // 翻页begin
        $countSql = "select count(*) as cnt from assistants where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/assistantmgr/list?doctorid={$doctorid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('assistants', $assistants);

        return self::SUCCESS;
    }

    //public function doModify() {
        //$assistantid = XRequest::getValue('assistantid', '');
        //$assistant = Dao::getEntityById('Assistant', $assistantid);

        //$doctorResources = Dao::getEntityListByCond('DoctorResource', '');

        //XContext::setValue('assistant', $assistant);
        //XContext::setValue('doctorResources', $doctorResources);

        //return self::SUCCESS;
    //}

    //public function doModifyPost() {
        //$assistantid = XRequest::getValue('assistantid', '');
        //$assistant = Dao::getEntityById('Assistant', $assistantid);
        //$name = XRequest::getValue('name', '');

        //$resoureidArr = XRequest::getValue('resourceids', []);
        //$resourceids = implode(',', $resoureidArr);

        //$assistant->name = $name;
        //$assistant->resourceids = $resourceids;

        //$preMsg = '修改成功';
        //XContext::setJumpPath("/my/modifypassword?preMsg=" . urlencode($preMsg));
        //return self::SUCCESS;
    //}

    //public function doAdd() {
        //return self::SUCCESS;
    //}

    //public function doAddPost() {
        //$name = XRequest::getValue('name', '');
    //}
}
