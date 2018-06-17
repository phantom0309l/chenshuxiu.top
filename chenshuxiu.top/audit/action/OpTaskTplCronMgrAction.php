<?php

class OpTaskTplCronMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    
    // 列表
    public function doListofoptasktpl() {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);
        DBC::requireNotEmpty($optasktpl, "optasktpl is null");

        $cond = "";
        $bind = [];

        $cond = " and optasktplid = :optasktplid order by step ";
        $bind[':optasktplid'] = $optasktplid;

        $optasktplcrons = Dao::getEntityListByCond('OpTaskTplCron', $cond, $bind);

        XContext::setValue("optasktplcrons", $optasktplcrons);
        XContext::setValue("optasktpl", $optasktpl);

        return self::SUCCESS;
    }
    
    // 详情页
    public function doOne () {
        $optasktplcronid = XRequest::getValue("optasktplcronid", 0);

        $opTaskTplCron = OpTaskTplCron::getById($optasktplcronid);

        XContext::setValue("opTaskTplCron", $opTaskTplCron);
        return self::SUCCESS;
    }
    
    public function doAdd () {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);
        DBC::requireNotEmpty($optasktpl, "optasktpl is null");

        XContext::setValue("optasktpl", $optasktpl);

        return self::SUCCESS;
    }
    
    public function doAddPost () {
        $optasktplid = XRequest::getValue("optasktplid", 0);
        $step = XRequest::getValue("step", 1);
        $send_content = XRequest::getValue("send_content", '');
        $dealwith_type = XRequest::getValue("dealwith_type", 'hang_up');
        $follow_daycnt = XRequest::getValue("follow_daycnt", 7);
        $remark = XRequest::getValue("remark", '');

        $row = [];
        $row["optasktplid"] = $optasktplid;
        $row["step"] = $step;
        $row["send_content"] = $send_content;
        $row["dealwith_type"] = $dealwith_type;
        if ($dealwith_type != 'appoint_follow') {
            $follow_daycnt = 0;
        }
        $row["follow_daycnt"] = $follow_daycnt;
        $row["remark"] = $remark;

        OpTaskTplCron::createByBiz($row);

        XContext::setJumpPath("/optasktplcronmgr/listofoptasktpl?optasktplid={$optasktplid}");
        return self::SUCCESS;
    }
    
    public function doModify () {
        $optasktplcronid = XRequest::getValue("optasktplcronid", 0);

        $optasktplcron = OpTaskTplCron::getById($optasktplcronid);
        DBC::requireTrue($optasktplcron instanceof $optasktplcron, "opTaskTplCron不存在:{$optasktplcronid}");

        XContext::setValue("optasktplcron", $optasktplcron);

        return self::SUCCESS;
    }
    
    // 修改提交
    public function doModifyPost () {
        $optasktplcronid = XRequest::getValue("optasktplcronid", 0);
        $optasktplid = XRequest::getValue("optasktplid", 0);
        $step = XRequest::getValue("step", 1);
        $send_content = XRequest::getValue("send_content", '');
        $dealwith_type = XRequest::getValue("dealwith_type", 'hang_up');
        $follow_daycnt = XRequest::getValue("follow_daycnt", 0);
        $remark = XRequest::getValue("remark", '');

        $optasktplcron = OpTaskTplCron::getById($optasktplcronid);
        DBC::requireTrue($optasktplcron instanceof OpTaskTplCron, "opTaskTplCron不存在:{$optasktplcronid}");

        $optasktplcron->step = $step;
        $optasktplcron->send_content = $send_content;
        $optasktplcron->dealwith_type = $dealwith_type;
        if ($dealwith_type == 'appoint_follow') {
            if ($follow_daycnt > 0) {
                $optasktplcron->follow_daycnt = $follow_daycnt;
            }
        } else {
            $optasktplcron->follow_daycnt = 0;
        }
        $optasktplcron->remark = $remark;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/optasktplcronmgr/modify?optasktplcronid=" . $optasktplcronid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doDeleteJson () {
        $optasktplcronid = XRequest::getValue("optasktplcronid", 0);
        $optasktplcron = OpTaskTplCron::getById($optasktplcronid);

        if ($optasktplcron instanceof OpTaskTplCron) {
            $sql = " select count(*) from optaskcrons where optasktplcronid = :optasktplcronid and status = 0 ";
            $bind = [
                ':optasktplcronid' => $optasktplcronid
            ];
            $cnt = Dao::queryValue($sql, $bind);

            if ($cnt <= 0) {
                $optasktplcron->remove();

                echo 'success';
            } else {
                echo 'fail';
            }
        } else {
            echo 'fail';
        }

        return self::BLANK;
    }
}
        