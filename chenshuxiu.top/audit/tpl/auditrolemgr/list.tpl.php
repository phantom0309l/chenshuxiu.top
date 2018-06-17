<?php
$pagetitle = "员工角色列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>角色id</td>
                        <td>code</td>
                        <td>名称</td>
                    </tr>
                </thead>
                <tbody>
            <?php foreach($auditRoles as $a) { ?>
                    <tr>
                        <td><?= $a->id; ?></td>
                        <td><?= $a->code; ?></td>
                        <td><?= $a->name; ?></td>
                    </tr>
            <?php }  ?>
                </tbody>
            </table>
            </div>
            <h4>新增角色</h4>
            <form action="/auditrolemgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=90>code</th>
                        <td>
                            <input type="text" name="code" value="" />
                            用于代码编写
                        </td>
                    </tr>
                    <tr>
                        <th>名称</th>
                        <td>
                            <input type="text" name="name" value="" />
                            用于显示
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
