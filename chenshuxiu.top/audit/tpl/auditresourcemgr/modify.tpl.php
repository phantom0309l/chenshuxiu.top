<?php
$pagetitle = "修改资源 AuditResource";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/auditresourcemgr/modifypost" method="post">
                <input type="hidden" name="auditresourceid" value="<?= $auditresource->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=90>类型</th>
                        <td>
                            <?php echo HtmlCtr::getRadioCtrImp(AuditResource::getTypeArr(),'type',$auditresource->type,''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>页面名称</th>
                        <td>
                            <input style="width: 50%" type="text" name="title" value="<?=$auditresource->title?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>action</th>
                        <td>
                            <input style="width: 50%" type="text" name="action_add" value="<?=$auditresource->action?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>method</th>
                        <td>
                            <input style="width: 50%" type="text" name="method_add" value="<?=$auditresource->method?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>auditmenuid</th>
                        <td>
                            <input style="width: 50%" type="text" name="auditmenuid" value="<?=$auditresource->auditmenuid?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>谁拥有权限</th>
                        <td>
                            <?php echo HtmlCtr::getCheckboxCtrImp(AuditRole::getDescArr(),'auditroleids[]',$auditresource->getAuditRoleIdArr(),''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>疾病分组</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseGroupCtrArray(),'diseasegroupid',$auditresource->diseasegroupid,''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>技术责任人</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getTechAuditorCtrArray(),'owner_auditorid',$auditresource->owner_auditorid,''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>手册</th>
                        <td>
                            <textarea cols='80' rows='8' name="content"><?=$auditresource->content?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>技术备注</th>
                        <td>
                            <textarea cols='80' rows='3' name="remark"><?=$auditresource->remark?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
