<?php
$pagetitle = "服务号新建";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
            <form action="/wxshopmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>名称</th>
                        <td>
                            <input type="text" name="name" value="" />
                            服务号的中文名
                        </td>
                    </tr>
                    <tr>
                        <th>短名称</th>
                        <td>
                            <input type="text" name="shortname" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>类型</th>
                        <td><?= HtmlCtr::getRadioCtrImp(WxShop::typeDescs(), 'type', 0,' ') ?></td>
                    </tr>
                    <tr>
                        <th>gh</th>
                        <td>
                            <input type="text" name="gh" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>token</th>
                        <td>
                            <input type="text" name="token" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>appid</th>
                        <td>
                            <input type="text" name="appid" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>secret</th>
                        <td>
                            <input type="text" style='width: 400px' name="secret" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>encodingaeskey</th>
                        <td>
                            <input type="text" style='width: 400px' name="encodingaeskey" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-success" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
