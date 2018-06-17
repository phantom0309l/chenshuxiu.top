<?php
$pagetitle = "新建菜单 AuditMenu";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/auditmenumgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=90>父级菜单</th>
                        <td>
                            <?php echo HtmlCtr::getRadioCtrImp(AuditMenu::getParentArr(),'parentmenuid',0,''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>页面名称</th>
                        <td>
                            <input style="width:50%" type="text" name="title" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>url</th>
                        <td>
                            <input style="width:50%" type="text" name="url" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>auditresourceid</th>
                        <td>
                            <input style="width:50%" type="text" name="auditresourceid" value="" />
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
