<?php
$pagetitle = "微信模板消息修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/wxtemplatemgr/modifypost" method="post">
                <input type="hidden" name="wxtemplateid" value="<?= $wxtemplate->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>id</th>
                        <td><?= $wxtemplate->id ?></td>
                    </tr>
                    <tr>
                        <th>wxshop</th>
                        <td><?= $wxtemplate->wxshop->shortname ?></td>
                    </tr>
                    <tr>
                        <th>微信模板id</th>
                        <td>
                            <input type="text" name="code" value="<?= $wxtemplate->code ?>" style="width: 600px" />
                        </td>
                    </tr>
                    <tr>
                        <th>模板名称</th>
                        <td>
                            <input type="text" name="title" value="<?= $wxtemplate->title ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>ename</th>
                        <td>
                            <input type="text" name="ename" value="<?= $wxtemplate->ename ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>用于展示的模板key</th>
                        <td>
                            <input type="text" name="showkey" value="<?= $wxtemplate->showkey ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>模板显示格式</th>
                        <td>
                            <textarea name="content" cols="80" rows="10"><?= $wxtemplate->content ?></textarea>
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
