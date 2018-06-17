<?php
$pagetitle = "脚本执行详情 " . $cronlog->id;
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th width='120'>时机</th>
                        <td><?= $cronlog->crontab->when ?></td>
                    </tr>
                    <tr>
                        <th>类型</th>
                        <td><?= $cronlog->crontab->type ?></td>
                    </tr>
                    <tr>
                        <th>脚本</th>
                        <td><?= $cronlog->crontab->process_name ?></td>
                    </tr>
                    <tr>
                        <th>脚本</th>
                        <td><?= $cronlog->crontab->title ?></td>
                    </tr>
                    <tr>
                        <th>cronlogid</th>
                        <td><?= $cronlog->id ?></td>
                    </tr>
                    <tr>
                        <th>begintime</th>
                        <td><?= $cronlog->begintime ?></td>
                    </tr>
                    <tr>
                        <th>endtime</th>
                        <td><?= $cronlog->endtime; ?>
                    </td>
                    </tr>
                    <tr>
                        <th>brief</th>
                        <td><?= $cronlog->brief ?></td>
                    </tr>
                    <tr>
                        <th>content</th>
                        <td><?= $cronlog->content ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
