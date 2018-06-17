<?php
$pagetitle = "User新建";
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
                    <form action="/usermgr/addpost" method="post">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                    <tr>
                        <td>关联患者:</td>
                        <td><?=$patient->name ?> <input type="hidden" name="patientid" value="<?=$patientid ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>姓名:</td>
                        <td>
                            <input type="text" name="name" value="" />
                            当前用户的姓名 (可以填患者姓名)
                        </td>
                    </tr>
                    <tr>
                        <td>关系:</td>
                        <td>
                            <input type="text" name="shipstr" value="" />
                            父亲 或 母亲 或 其他
                        </td>
                    </tr>
                    <tr>
                        <td>电话:</td>
                        <td>
                            <input type="text" name="mobile" value="" />
                            * 用于登录
                        </td>
                    </tr>
                    <tr>
                        <td>密码:</td>
                        <td>
                            <input type="text" name="password" value="" />
                            为空则自动取手机号后6位
                        </td>
                    </tr>
                    <tr>
                        <td>备注:</td>
                        <td>
                            <textarea name="auditremark" cols="40" rows="3"></textarea>
                            可以记录患者姓名,年龄等信息
                        </td>
                    </tr>
                    <tr>
                        <td></td>
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
