<?php
$pagetitle = "新建资源 AuditResource";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/auditresourcemgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=90>类型</th>
                        <td>
                            <?php echo HtmlCtr::getRadioCtrImp(AuditResource::getTypeArr(),'type','',''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>页面名称</th>
                        <td>
                            <input style="width: 50%" type="text" name="title" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>action</th>
                        <td>
                            <input style="width: 50%" type="text" name="action_add" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>method</th>
                        <td>
                            <input style="width: 50%" type="text" name="method_add" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>auditmenuid</th>
                        <td>
                            <input style="width: 50%" type="text" name="auditmenuid" value="0" />
                        </td>
                    </tr>
                    <tr>
                        <th>谁拥有权限</th>
                        <td>
                            <?php echo HtmlCtr::getCheckboxCtrImp(AuditRole::getDescArr(),'auditroleids[]',array(),''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>技术责任人</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getTechAuditorCtrArray(),'owner_auditorid',0,''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>手册</th>
                        <td>
                            <textarea cols='80' rows='8' name="content"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>技术备注</th>
                        <td>
                            <textarea cols='80' rows='3' name="remark"></textarea>
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
