<?php
$pagetitle = "医生配置模板  DoctorConfigTpls";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a class="btn btn-success" href="/doctorconfigtplmgr/add">医生配置模板新建</a>
                <a class="btn btn-success" href="/doctorconfigtplmgr/generateconfig">医生配置生成</a>
            </div>
            <form action="/doctorconfigtplmgr/posmodifypost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>id</td>
                            <td>序号</td>
                            <td>标题</td>
                            <td>code</td>
                            <td>分组</td>
                            <td>简介</td>
                            <td>操作</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($doctorconfigtpls as $a) {
                            ?>
                            <tr>
                                <td><?= $a->id ?></td>
                                <td>
                                    <input type="text" class="width50" name="pos[<?= $a->id ?>]" value="<?= $a->pos ?>" />
                                </td>
                                <td><?= $a->title?></td>
                                <td><?= $a->code ?></td>
                                <td><?= $a->groupstr ?></td>
                                <td><?= $a->brief ?></td>
                                <td>
                                    <a target="_blank" href="/doctorconfigtplmgr/modify?doctorconfigtplid=<?= $a->id ?>">修改</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td colspan=20>
                                <input class="btn btn-success" type="submit" value="保存序号修改">
    							<?php include $dtpl . "/pagelink.ctr.php"; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
