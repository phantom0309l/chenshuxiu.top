<?php
$pagetitle = "医生后台资源-修改";
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
            <form action="/doctorResourcemgr/modifypost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <input type="hidden" name="doctorresourceid" value="<?=$doctorResource->id?>" >
                    <tr>
                        <th width=140>名称</th>
                        <td>
                            <input type="text" name="name" value="<?=$doctorResource->name?>" placeholder="数据库-患者-添加" />
                        </td>
                    </tr>
                    <tr>
                        <th>描述</th>
                        <td>
                            <input type="text" name="content" value="<?=$doctorResource->content?>" style="width: 60%" />
                        </td>
                    </tr>
                    <tr>
                        <th>action</th>
                        <td>
                            <input type="text" name="_action" value="<?=$doctorResource->action?>" style="width: 60%" />
                        </td>
                    </tr>
                    <tr>
                        <th>method</th>
                        <td>
                            <input type="text" name="_method" value="<?=$doctorResource->method?>" style="width: 60%" />
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
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>