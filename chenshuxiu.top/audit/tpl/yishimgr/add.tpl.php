<?php
$pagetitle = "添加医师";
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
        <form action="/yishimgr/addpost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width='140'>角色类型</th>
                    <td>
                        <?= HtmlCtr::getRadioCtrImp([1=>"医师",2=>"审核药师",3=>"配药药师",9=>"管理员"],"type",1," ");?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width='140'>姓名</th>
                    <td width='280'>
                        <input type="text" name="name" />
                        *
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <th width='140'>手机号</th>
                    <td width='280'>
                        <input type="text" name="mobile"  />
                        *
                    </td>
                    <td>
                        用于登录
                    </td>
                </tr>
                <tr>
                    <th width='140'>密码</th>
                    <td width='280'>
                        <input type="text" name="password"  />
                        *
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <th width='140'>医院</th>
                    <td width='280'>
                        <input type="text" name="hospital_name"  />
                        *
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <th width='140'>科室</th>
                    <td width='280'>
                        <input type="text" name="department_name"  />
                        *
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" class="btn btn-success" value="提交" />
                    </td>
                    <td></td>
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