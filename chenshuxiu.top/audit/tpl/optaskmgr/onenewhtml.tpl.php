
<div class="optaskshell block block-bordered" style="margin-bottom: 10px;">
    <div class="block-header bg-gray-lighter">
        <?= "{$patient->name}进行中任务" ?>
        <?php
        $plantxtmsg_cnt = Plan_txtMsgDao::getCntByPatientid($patient->id);
        if ($plantxtmsg_cnt > 0) {
            $plantxtmsg_unsentcnt = Plan_txtMsgDao::getUnsentCntByPatientid($patient->id);
            echo "<span class='text-info'>(定时消息{$plantxtmsg_unsentcnt}/{$plantxtmsg_cnt}条)</span>";
        }
        ?>
        <ul class="block-options">
            <li>
                <a class="text-info" target="_blank" style="opacity: 1.0; color: #3169b1;" href="/optaskmgr/listhistory?patientid=<?= $patient->id ?>">
                    <i class="fa fa-history"></i>
                    历史任务
                </a>
            </li>
        </ul>
    </div>
    <div class="block-content remove-padding" style="padding-top: 10px;">
<?php
$is_yunyingmgr = $myauditor->isHasRole([
    'yunyingmgr']);
if (count($optasks_progress) > 0) {
    foreach ($optasks_progress as $i => $optask) {
        ?>
        <div class="optask">
    <?php
        $adhd = "";
        if ($isadhd) {
            $adhd = $myauditor->hasBindPgroup($optask->pgroupid) ? "" : "optask-tNo";
        }
        ?>
            <div class="optask-t <?php if ($i == 0) { ?>optask-t-active<?php } ?> <?= $adhd ?> " style="position: relative;">
                <div style="margin-right: 35px;">
                    <?php echo substr($optask->plantime, 0, 10); ?>
                    <?= $optask->optasktpl->title ?>
                    <span>[<?= $optask->user->shipstr ?>]</span>
                    <?= $myauditor->canHandleOptask($optask) ? "" : "<span>[无权限]</span>" ?>
                <?php if ($optask->opnode instanceof OpNode) { ?>
                    <?php if ($optask->status == 2) { ?>
                        <span style="color:<?= $optask->getColor() ?>">[<?= $optask->getStatusStr() ?>]</span>
                    <?php } ?>
                    <span style="color: green">[<?= $optask->opnode->title ?>]</span>
                    <span style="color: green">[创建人: <?= $optask->auditor->name ?>]</span>
                <?php } ?>
                <?php if($optask->level > 2){ ?>
                    <span class="red">[L<?= $optask->level ?>]</span>
                <?php } ?>
                    <span style="color: #999;"><?= $optask->level_remark ?></span>
                </div>
                <span class="pull-right" style="position: absolute; top: 0px; right: 20px;">
                    <i class="fa fa-angle-<?php if ($i == 0) { ?>down<?php } else { ?>right<?php } ?> angle"></i>
                </span>
            </div>
            <div class="optask-c <?= $i == 0 ? "" : "none" ?>" style="padding-left: 5px; padding-right: 5px; border-bottom: 1px solid #e9e9e9">
                <div class="optask-handlebox" data-optaskid="<?= $optask->id ?>" data-optasktplid="<?= $optask->optasktplid ?>" data-auditorid="<?= $myauditor->id ?>">
                    <div class="" style="margin-top: 5px;">
                    <?php if ($optask->pipeid) { ?>
                        <span class="btn btn-default trackPipe" data-pipeid="<?= $optask->pipeid ?>">查看流中内容</span>
                    <?php } ?>
                    <?php if ($myauditor->canHandleOptask($optask)) { ?>
                        <?php if ($optask->optasktpl->code == 'audit' && $optask->optasktpl->subcode == 'bedtkt') { ?>
                        <a class="btn btn-primary" target="_blank" href="/bedtktmgr/showhtml?bedtktid=<?= $optask->objid ?>">查看</a>
                        <?php } ?>
                        <span class="btn btn-default closeBtn" data-optaskid=<?= $optask->id ?> data-auditorid=<?= $myauditor->id ?>>关闭该任务</span>
                        <?php if ($optask->optasktpl->is_auto_send == 1) { ?>
                            <a class="btn btn-default" target="_blank" href="/optaskcronmgr/listofoptask?optaskid=<?= $optask->id ?>">查看自动化消息</a>
                        <?php } ?>
                        <div class="optaskLevelBox" style="display: block; width: 80px; float: right;" data-optaskid=<?= $optask->id ?>>
                            <?= HtmlCtr::getSelectCtrImp(CtrHelper::getOptaskLevelCtrArray(), "optask-level", $optask->level, 'form-control optask-level'); ?>
                            <span class="text-success level-change-notice"></span>
                        </div>
                    </div>
                    <div class="" style="margin-top: 5px;">
                    <?php if ($optask->optasktpl->code == 'doctor_reply') { ?>
                        <a target="_blank" class="btn btn-default push-10-l" data-optaskid=<?= $optask->id ?> href="/dwx_pipemgr/list?doctorid=<?= $optask->doctorid ?>" data-auditorid=<?= $myauditor->id ?>>回复医生</a>
                    <?php } ?>
            <?php
            $opnode = $optask->opnode;
            if ($opnode instanceof OpNode && $optask->status != 1) {
                if ($optask->status != 2 && $opnode->is_hang_up == 1) {
                    ?>
                        <span class="btn btn-default hangupBtn" data-optaskid=<?= $optask->id ?> data-optasktplid=<?= $optask->optasktplid ?> data-patientid=<?= $optask->patientid ?> data-status="1" data-auditorid=<?= $myauditor->id ?>>挂起</span>
            <?php
                }
                $arr = $optask->getTheOpnodeAllOpNodeFlow();
                $is_end_opnode = count($arr) < 2 ? 1 : 0;
                echo "[{$opnode->title}] => ";
                // 非根节点
                if ($is_end_opnode == 1) {
                    echo "[已是终节点]";
                } else {
                    echo HtmlCtr::getSelectCtrImp($arr, "", 0, "to_opnodeid");
                    ?>
                        <input type="hidden" id="opnodeflowid-<?= $optask->id ?>" value="">
                        <input type="hidden" id="opnodetitle-<?= $optask->id ?>" value="">
                        <input type="text" style="display: none" id="next_plantime-<?= $optask->id ?>" name="next_plantime" class="calendar" value="">
                    <?php
                    if ($opnode->code == 'doctor_apply' || $opnode->code == 'doctor_refuse') {
                        $display = "";
                        $readonly = "readonly";
                    } else {
                        $display = "none";
                        $readonly = "";
                    }
                    ?>
                        <span class="btn btn-sm btn-primary flowBtn" data-optaskid=<?= $optask->id ?> data-patientid="<?= $optask->patientid ?>" data-status="1" data-auditorid=<?= $myauditor->id ?>>确定切换</span>
                        <div id="audit_remark-<?= $optask->id ?>" style="display:<?= $display ?>">
                            <div class="form-group">
                                <label class="col-xs-12" for="example-textarea-input">描述</label>
                                <div class="col-xs-12">
                    <?php
                    $content = "";
                    if ($opnode->code == 'doctor_apply') {
                        $content = trim($optask->obj->auditor_remark);
                    } elseif ($opnode->code == 'doctor_refuse') {
                        $content = trim($optask->obj->doctor_remark);
                    }
                    ?>
                                    <textarea class="form-control" id="txt_audit_remark-<?= $optask->id ?>" name="example-textarea-input" rows="6" placeholder="Content.." <?= $readonly ?>><?= $content ?></textarea>
                                </div>
                            </div>
                        </div>
                <?php
                }
                ?>
            <?php } ?>
    <?php } ?>
    <?php if ($optask->optasktpl->code == 'baodao') { ?>
                        <a target="_blank" href="/patientmgr/list4bind?patientid=<?= $optask->patientid ?>" class="btn btn-success">报到审核</a>
    <?php } ?>
                    </div>
                </div>
                <div class="tab">
                    <ul class="tab-menu">
        <?php
        $action_show = "active";
        if ($optask->optasktpl->code != 'PatientMsg') {
            $action_show = "";
            ?>
                        <li class="active">内容</li>
    <?php } ?>
                        <li class="<?= $action_show ?>">备注</li>
    <?php if ($is_yunyingmgr) { ?>
                        <li>审核</li>
    <?php } ?>
                        <li>定时消息(<?= Plan_txtMsgDao::getCntByObj($optask) ?>)</li>
                    </ul>
                    <div class="tab-content">
        <?php
        $none_show = "";
        if ($optask->optasktpl->code != 'PatientMsg') {
            $none_show = "none";
            ?>
                        <div class="tab-content-item">
                            <div class="optask-innershell">
            <?php
            $typestr = $optask->optasktpl->getTypestr();
            if (file_exists(dirname(__FILE__) . "/_optask/_" . $typestr . ".php")) {
                $filename = $typestr;
            } else {
                $filename = "OpTaskBase";
            }
            include dirname(__FILE__) . "/_optask/_" . $filename . ".php";
            ?>

                                <div class="" style="border-top: 1px dashed #5c90d2; margin-bottom: 10px; margin-top: 10px;"></div>
                                <div class="pl10">
                                    <label class="fb">备注区</label>
                                    <textarea class="textarea_optlog_content textarea_two_rows"></textarea>
                                    <br />
                                    <span class="btn btn-sm btn-primary nextfollowBtn" data-optaskid="<?= $optask->id ?>">提交新备注</span>
                                </div>
            <?php
            $optlogs = $optask->getOptLogs();
            ?>
                                <div class="border-top-dashed mt10 p10">
                                    <div>备注列表</div>
                                    <div class="optlogTableShell mt10">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <td>时间(逆序)</td>
                                                        <td>内容</td>
                                                    </tr>
                                                </thead>
                                                <tbody class="tbody_optlog">
            <?php
            foreach ($optlogs as $optlog) {
                // $jsoncontent = json_decode($optlog->jsoncontent);
                ?>
                                                    <tr>
                                                        <td class="gray f12">
                                                            <?= $optlog->getCreateDayHi() ?>
                                                            <?= $optlog->auditor->name ?>
                                                        </td>
                                                        <td>
                                                            <?= $optlog->content ?>
                                                        </td>
                                                    </tr>
            <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    <?php } ?>
                        <div class="tab-content-item <?= $none_show ?>">
                            <div class="optask-innershell">
                                <div class="pl10">
                                    <textarea class="textarea_optlog_content textarea_two_rows"></textarea>
                                    <br />
                                    <span class="btn btn-sm btn-primary nextfollowBtn" data-optaskid="<?= $optask->id ?>">提交新备注</span>
                                </div>
    <?php
        $optlogs = $optask->getOptLogs();
        ?>
                                <div class="border-top-dashed mt10 p10">
                                    <div>备注列表</div>
                                    <div class="optlogTableShell mt10">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <td>时间(逆序)</td>
                                                        <td>内容</td>
                                                    </tr>
                                                </thead>
                                                <tbody class="tbody_optlog">
    <?php
        foreach ($optlogs as $optlog) {
            // $jsoncontent = json_decode($optlog->jsoncontent);
            ?>
                                                    <tr>
                                                        <td class="gray f12">
                                                            <?= $optlog->getCreateDayHi() ?>
                                                            <?= $optlog->auditor->name ?>
                                                        </td>
                                                        <td>
                                                            <?= $optlog->content ?>
                                                        </td>
                                                    </tr>
    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    <?php if ($is_yunyingmgr) { ?>
                        <div class="tab-content-item none">
                            <div class="optask-innershell">
                                <div class="replyBox">
                                    <div>
                                        <p>
                                            <textarea name="reply-msg" class="textarea reply-msg"><?= $optask->audit_remark ?></textarea>
                                        </p>
                                    </div>
                                    <p>
                                        <span class="btn btn-default modifyOptaskAudit_RemarkBtn" data-optaskid="<?= $optask->id ?>">修改</span>
                                    </p>
                                </div>
                            </div>
                        </div>
    <?php } ?>
                        <div class="tab-content-item none">
                            <div class="optask-innershell">
                                <?php include '_plan_txtmsg.tpl.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}
?>
    </div>
</div>
<script>
    function planTxtMsgFormSubmit(form) {
        $.ajax({
            "type": "post",
            "data": $(form).serialize(),
            "dataType": "json",
            "url": "/optaskmgr/ajaxAddPlanTxtMsgPost",
            "success": function (data) {
                alert(data.errmsg);
                if (data.errno == 0) {
                    current_showOptask.click();
                }
            }
        });
        return false;
    }
</script>

<?php if (count($optasks_future) > 0) { ?>
<div class="optaskshell block block-bordered">
    <div class="block-header bg-gray-lighter">
        <?= "{$patient->name}未来任务" ?>
    </div>
    <div class="block-content remove-padding" style="padding-top: 10px;">
        <?php
            $is_yunyingmgr = $myauditor->isHasRole(['yunyingmgr']);
            foreach ($optasks_future as $i => $optask) {
                ?>
                <div class="optask">
                    <?php
                    $adhd = "";
                    if ($isadhd) {
                        $adhd = $myauditor->hasBindPgroup($optask->pgroupid) ? "" : "optask-tNo";
                    }
                    ?>
                    <div class="optask-t <?= $adhd ?> " style="position: relative;">
                        <div style="margin-right: 35px;">
                            <?php echo substr($optask->plantime, 0, 10); ?>
                            <?= $optask->optasktpl->title ?>
                            <span>[<?= $optask->user->shipstr ?>]</span>
                            <?= $myauditor->canHandleOptask($optask) ? "" : "<span>[无权限]</span>" ?>
                            <?php if ($optask->opnode instanceof OpNode) { ?>
                                <?php if ($optask->status == 2) { ?>
                                    <span style="color:<?= $optask->getColor() ?>">[<?= $optask->getStatusStr() ?>]</span>
                                <?php } ?>
                                <span style="color: green">[<?= $optask->opnode->title ?>]</span>
                                <span style="color: green">[创建人: <?= $optask->auditor->name ?>]</span>
                            <?php } ?>
                            <?php if($optask->level > 2){ ?>
                                <span class="red">[L<?= $optask->level ?>]</span>
                            <?php } ?>
                            <span style="color: #999;"><?= $optask->level_remark ?></span>
                        </div>
                        <span class="pull-right" style="position: absolute; top: 0px; right: 20px;">
                    <i class="fa fa-angle-<?php if ($i == 0) { ?>down<?php } else { ?>right<?php } ?> angle"></i>
                </span>
                    </div>
                    <div class="optask-c none" style="padding-left: 5px; padding-right: 5px; border-bottom: 1px solid #e9e9e9">
                        <div class="optask-handlebox" data-optaskid="<?= $optask->id ?>" data-optasktplid="<?= $optask->optasktplid ?>" data-auditorid="<?= $myauditor->id ?>">
                            <div class="" style="margin-top: 5px;">
                                <?php if ($optask->pipeid) { ?>
                                    <span class="btn btn-default trackPipe" data-pipeid="<?= $optask->pipeid ?>">查看流中内容</span>
                                <?php } ?>
                                <?php if ($myauditor->canHandleOptask($optask)) { ?>
                                <?php if ($optask->optasktpl->code == 'audit' && $optask->optasktpl->subcode == 'bedtkt') { ?>
                                    <a class="btn btn-primary" target="_blank" href="/bedtktmgr/showhtml?bedtktid=<?= $optask->objid ?>">查看</a>
                                <?php } ?>
                                <span class="btn btn-default closeBtn" data-optaskid=<?= $optask->id ?> data-auditorid=<?= $myauditor->id ?>>关闭该任务</span>
                                <?php if ($optask->optasktpl->is_auto_send == 1) { ?>
                                    <a class="btn btn-default" target="_blank" href="/optaskcronmgr/listofoptask?optaskid=<?= $optask->id ?>">查看自动化消息</a>
                                <?php } ?>
                                <div class="optaskLevelBox" style="display: block; width: 80px; float: right;" data-optaskid=<?= $optask->id ?>>
                                    <?= HtmlCtr::getSelectCtrImp(CtrHelper::getOptaskLevelCtrArray(), "optask-level", $optask->level, 'form-control optask-level'); ?>
                                    <span class="text-success level-change-notice"></span>
                                </div>
                            </div>
                            <div class="" style="margin-top: 5px;">
                                <?php if ($optask->optasktpl->code == 'doctor_reply') { ?>
                                    <a target="_blank" class="btn btn-default push-10-l" data-optaskid=<?= $optask->id ?> href="/dwx_pipemgr/list?doctorid=<?= $optask->doctorid ?>" data-auditorid=<?= $myauditor->id ?>>回复医生</a>
                                <?php } ?>
                                <?php
                                $opnode = $optask->opnode;
                                if ($opnode instanceof OpNode && $optask->status != 1) {
                                    if ($optask->status != 2 && $opnode->is_hang_up == 1) {
                                        ?>
                                        <span class="btn btn-default hangupBtn" data-optaskid=<?= $optask->id ?> data-optasktplid=<?= $optask->optasktplid ?> data-patientid=<?= $optask->patientid ?> data-status="1" data-auditorid=<?= $myauditor->id ?>>挂起</span>
                                        <?php
                                    }
                                    $arr = $optask->getTheOpnodeAllOpNodeFlow();
                                    $is_end_opnode = count($arr) < 2 ? 1 : 0;
                                    echo "[{$opnode->title}] => ";
                                    // 非根节点
                                    if ($is_end_opnode == 1) {
                                        echo "[已是终节点]";
                                    } else {
                                        echo HtmlCtr::getSelectCtrImp($arr, "", 0, "to_opnodeid");
                                        ?>
                                        <input type="hidden" id="opnodeflowid-<?= $optask->id ?>" value="">
                                        <input type="hidden" id="opnodetitle-<?= $optask->id ?>" value="">
                                        <input type="text" style="display: none" id="next_plantime-<?= $optask->id ?>" name="next_plantime" class="calendar" value="">
                                        <?php
                                        if ($opnode->code == 'doctor_apply' || $opnode->code == 'doctor_refuse') {
                                            $display = "";
                                            $readonly = "readonly";
                                        } else {
                                            $display = "none";
                                            $readonly = "";
                                        }
                                        ?>
                                        <span class="btn btn-sm btn-primary flowBtn" data-optaskid=<?= $optask->id ?> data-patientid="<?= $optask->patientid ?>" data-status="1" data-auditorid=<?= $myauditor->id ?>>确定切换</span>
                                        <div id="audit_remark-<?= $optask->id ?>" style="display:<?= $display ?>">
                                            <div class="form-group">
                                                <label class="col-xs-12" for="example-textarea-input">描述</label>
                                                <div class="col-xs-12">
                                                    <?php
                                                    $content = "";
                                                    if ($opnode->code == 'doctor_apply') {
                                                        $content = trim($optask->obj->auditor_remark);
                                                    } elseif ($opnode->code == 'doctor_refuse') {
                                                        $content = trim($optask->obj->doctor_remark);
                                                    }
                                                    ?>
                                                    <textarea class="form-control" id="txt_audit_remark-<?= $optask->id ?>" name="example-textarea-input" rows="6" placeholder="Content.." <?= $readonly ?>><?= $content ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                <?php } ?>
                                <?php } ?>
                                <?php if ($optask->optasktpl->code == 'baodao') { ?>
                                    <a target="_blank" href="/patientmgr/list4bind?patientid=<?= $optask->patientid ?>" class="btn btn-success">报到审核</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tab">
                            <ul class="tab-menu">
                                <?php
                                $action_show = "active";
                                if ($optask->optasktpl->code != 'PatientMsg') {
                                    $action_show = "";
                                    ?>
                                    <li class="active">内容</li>
                                <?php } ?>
                                <li class="<?= $action_show ?>">备注</li>
                                <?php if ($is_yunyingmgr) { ?>
                                    <li>审核</li>
                                <?php } ?>
                                <li>定时消息(<?= Plan_txtMsgDao::getCntByObj($optask) ?>)</li>
                            </ul>
                            <div class="tab-content">
                                <?php
                                $none_show = "";
                                if ($optask->optasktpl->code != 'PatientMsg') {
                                    $none_show = "none";
                                    ?>
                                    <div class="tab-content-item">
                                        <div class="optask-innershell">
                                            <?php
                                            $typestr = $optask->optasktpl->getTypestr();
                                            if (file_exists(dirname(__FILE__) . "/_optask/_" . $typestr . ".php")) {
                                                $filename = $typestr;
                                            } else {
                                                $filename = "OpTaskBase";
                                            }
                                            include dirname(__FILE__) . "/_optask/_" . $filename . ".php";
                                            ?>

                                            <div class="" style="border-top: 1px dashed #5c90d2; margin-bottom: 10px; margin-top: 10px;"></div>
                                            <div class="pl10">
                                                <label class="fb">备注区</label>
                                                <textarea class="textarea_optlog_content textarea_two_rows"></textarea>
                                                <br />
                                                <span class="btn btn-sm btn-primary nextfollowBtn" data-optaskid="<?= $optask->id ?>">提交新备注</span>
                                            </div>
                                            <?php
                                            $optlogs = $optask->getOptLogs();
                                            ?>
                                            <div class="border-top-dashed mt10 p10">
                                                <div>备注列表</div>
                                                <div class="optlogTableShell mt10">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                            <tr>
                                                                <td>时间(逆序)</td>
                                                                <td>内容</td>
                                                            </tr>
                                                            </thead>
                                                            <tbody class="tbody_optlog">
                                                            <?php
                                                            foreach ($optlogs as $optlog) {
                                                                // $jsoncontent = json_decode($optlog->jsoncontent);
                                                                ?>
                                                                <tr>
                                                                    <td class="gray f12">
                                                                        <?= $optlog->getCreateDayHi() ?>
                                                                        <?= $optlog->auditor->name ?>
                                                                    </td>
                                                                    <td>
                                                                        <?= $optlog->content ?>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="tab-content-item <?= $none_show ?>">
                                    <div class="optask-innershell">
                                        <div class="pl10">
                                            <textarea class="textarea_optlog_content textarea_two_rows"></textarea>
                                            <br />
                                            <span class="btn btn-sm btn-primary nextfollowBtn" data-optaskid="<?= $optask->id ?>">提交新备注</span>
                                        </div>
                                        <?php
                                        $optlogs = $optask->getOptLogs();
                                        ?>
                                        <div class="border-top-dashed mt10 p10">
                                            <div>备注列表</div>
                                            <div class="optlogTableShell mt10">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <td>时间(逆序)</td>
                                                            <td>内容</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody class="tbody_optlog">
                                                        <?php
                                                        foreach ($optlogs as $optlog) {
                                                            // $jsoncontent = json_decode($optlog->jsoncontent);
                                                            ?>
                                                            <tr>
                                                                <td class="gray f12">
                                                                    <?= $optlog->getCreateDayHi() ?>
                                                                    <?= $optlog->auditor->name ?>
                                                                </td>
                                                                <td>
                                                                    <?= $optlog->content ?>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($is_yunyingmgr) { ?>
                                    <div class="tab-content-item none">
                                        <div class="optask-innershell">
                                            <div class="replyBox">
                                                <div>
                                                    <p>
                                                        <textarea name="reply-msg" class="textarea reply-msg"><?= $optask->audit_remark ?></textarea>
                                                    </p>
                                                </div>
                                                <p>
                                                    <span class="btn btn-default modifyOptaskAudit_RemarkBtn" data-optaskid="<?= $optask->id ?>">修改</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="tab-content-item none">
                                    <div class="optask-innershell">
                                        <?php include '_plan_txtmsg.tpl.php'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        ?>
    </div>
</div>
<?php } ?>