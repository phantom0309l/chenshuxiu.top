<?php

class OpTaskCronMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doListOfOpTask() {
        $optaskid = XRequest::getValue("optaskid", 0);
        $optask = OpTask::getById($optaskid);

        $cond = " and optaskid = :optaskid order by plan_exe_time asc ";
        $bind = [
            ':optaskid' => $optaskid
        ];

        $optaskcrons = Dao::getEntityListByCond("OpTaskCron", $cond, $bind);

        XContext::setValue("optaskcrons", $optaskcrons);
        XContext::setValue("optask", $optask);

        return self::SUCCESS;
    }
    
    public function doBreak () {
        $optaskid = XRequest::getValue("optaskid", 0);

        $optaskcrons = OpTaskCronDao::getListByOptaskidStatus($optaskid, 0);
        foreach ($optaskcrons as $optaskcron) {
            $optaskcron->status = 2;

            $optaskcron->remark = "[中断] {$this->myauditor->name}手动中断";
        }

        XContext::setJumpPath("/optaskcronmgr/listofoptask?optaskid={$optaskid}");

        return self::BLANK;
    }

    public function doModifyContentJson () {
        $optaskcronid = XRequest::getValue('optaskcronid', 0);
        $optaskcron = OpTaskCron::getById($optaskcronid);

        $content = XRequest::getValue('content', '');

        if ($optaskcron instanceof OpTaskCron) {
            $optaskcron->content = $content;
            $this->result['errno'] = 0;
            $this->result['errmsg'] = "修改成功";
        } else {
            $this->returnError('修改失败');
        }

        return self::TEXTJSON;
    }
}
        