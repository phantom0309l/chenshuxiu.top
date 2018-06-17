<?php
$pagetitle = "用户省市测试列表 Users";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>userid</th>
                        <th>创建时间</th>
                        <th>手机</th>
                        <th>cityStr</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($users as $a) {
                            if ($a instanceof User) {
                                ?>
                                    <tr>
                                        <td><?= $a->id ?></td>
                                        <td><?= $a->createtime ?></td>
                                        <td><?= $a->getMaskMobile() ?></td>
                                        <td><?= $a->testCityStr(); ?></td>
                                    </tr>
                                <?php
                            }
                        }
                    ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
