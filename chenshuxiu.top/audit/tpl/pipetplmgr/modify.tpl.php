<?php
$pagetitle = "快捷回复修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/pipetplmgr/modifypost" method="post">
                <input type="hidden" name="pipetplid" value="<?= $pipetpl->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>id</th>
                        <td><?= $pipetpl->id?></td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?= $pipetpl->createtime?></td>
                    </tr>
                    <tr>
                        <th>修改时间</th>
                        <td><?= $pipetpl->updatetime?></td>
                    </tr>
                    <tr>
                        <th>标题</th>
                        <td>
                            <input id="title" type="text" name="title" style="width: 80%;" value="<?= $pipetpl->title?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>是否在医生端显示</th>
                        <td>
                            <?= HtmlCtr::getRadioCtrImp(array("0"=>"不显示","1"=>"显示"), 'show_in_doctor', $pipetpl->show_in_doctor, '', 'show_in_doctor')?>
                        </td>
                    </tr>
                    <tr>
                        <th>objtype</th>
                        <td>
                            <?= $pipetpl->objtype ?>
                        </td>
                    </tr>
                    <tr>
                        <th>objcode</th>
                        <td>
                            <?= $pipetpl->objcode ?>
                        </td>
                    </tr>
                    <tr>
                        <th>内容</th>
                        <td>
                            <textarea id="content" name="content" rows="6" style="width: 80%;"><?= $pipetpl->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="修改" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
