<?php
$pagetitle = "Tag修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/tagmgr/modifypost" method="post">
                <input type="hidden" name="tagid" value="<?= $tag->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width='100'>tagid</th>
                        <td><?= $tag->id ?></td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?= $tag->createtime ?></td>
                    </tr>
                    <tr>
                        <th>标签类型</th>
                        <td><?= HtmlCtr::getRadioCtrImp(Tag::getTypeStrDefines(), 'typestr',$tag->typestr ,' '); ?></td>
                    </tr>
                    <tr>
                        <th>name</th>
                        <td>
                            <input type="text" name="name" value="<?= $tag->name ?>" />
                            请慎重修改
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
