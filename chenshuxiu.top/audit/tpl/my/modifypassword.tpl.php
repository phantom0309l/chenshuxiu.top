<?php
$pagetitle = '修改密码';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
                <form action="/my/modifypasswordpost" method="post">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <tr>
                            <th width=90>旧密码</th>
                            <td>
                                <input type="text" name="password" value=""/>
                            </td>
                        </tr>
                        <tr>
                            <th>新密码</th>
                            <td>
                                <input type="password" name="newpassword" value=""/>
                            </td>
                        </tr>
                        <tr>
                            <th>重复新密码</th>
                            <td>
                                <input type="password" name="newpasswordrepeat" value=""/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <input type="submit" value="提交"/>
                            </td>
                        </tr>
                    </table>
                    </div>
                </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
</body>
</html>
