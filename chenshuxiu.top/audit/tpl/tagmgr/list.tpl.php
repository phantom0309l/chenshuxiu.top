<?php
$pagetitle = "标签列表 Tags";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <label for="">按类型筛选：</label>
                <select id="selectTypestr" style="width: 80px; border: 1px solid #ddd; height: 30px;">
                    <?php

                    foreach (Tag::getTypeStrDefines(true) as $k => $v) {
                        if ($k == $typestr) {
                            ?>
                                <option value="<?= $k ?>" selected><?= $v ?></option>
                        <?php   } else { ?>

                                <option value="<?= $k ?>">  <?= $v ?></option>

                            <?php
                        }
                    }
                    ?>
                </select>
                <a class="btn btn-success" href="/tagmgr/add?typestr=<?=$typestr ?>">标签新建</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <td>tagid</td>
                        <td>类型</td>
                        <td>名称/name</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
<?php
foreach ($tags as $a) {
    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td>
                            <a href="/tagmgr/list?typestr=<?=$a->typestr ?>"><?= $a->typestr ?> <?= $a->getTypeStrDesc() ?></a>
                        </td>
                        <td><?= $a->name ?></td>
                        <td>
                            <a target="_blank" href="/tagmgr/modify?tagid=<?= $a->id ?>">修改</a>
                        </td>
                    </tr>
<?php } ?>
                    <tr>
                        <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
        $(document).on(
            "change",
            "#selectTypestr",
            function(){
                var val =  $(this).val() ;
                var url = val==0 ? location.pathname : location.pathname + '?typestr=' + val ;
                window.location.href = url ;
            });
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
