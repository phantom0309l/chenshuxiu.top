<?php
$pagetitle = "微信模板消息列表 WxTemplates";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a class="btn btn-success" href="/wxtemplatemgr/add">微信模板新建</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>wxshop</th>
                        <th>id</th>
                        <th>模板名称</th>
                        <th>微信模板id</th>
                        <th>ename</th>
                        <th>showkey</th>
                        <th>修改</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($wxtemplates as $a) {
                    ?>
                    <tr>
                        <td rowspan=2><?= $a->wxshop->name ?></td>
                        <td rowspan=2><?= $a->id ?></td>
                        <td rowspan=2><?= $a->title ?></td>
                        <td><?= $a->code ?></td>
                        <td><?= $a->ename ?></td>
                        <td><?= $a->showkey ?></td>
                        <td>
                            <a href="/wxtemplatemgr/modify?id=<?= $a->id ?>">修改</a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=5 style="color: #999"><?= $a->content ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
