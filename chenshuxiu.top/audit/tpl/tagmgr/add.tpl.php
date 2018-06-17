<?php
$pagetitle = "标签新建";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/tagmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width='100'>标签类型</th>
                        <td><?= HtmlCtr::getRadioCtrImp(Tag::getTypeStrDefines(), 'typestr', $typestr,' '); ?></td>
                    </tr>
                    <tr>
                        <th>标签名称</th>
                        <td>
                            <input type="text" name="name" value="" />
                            <span class="gray">汉字</span>
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
