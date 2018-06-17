<?php
$pagetitle = "定时任务列表 CronTab";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>时机</th>
                        <th>类型</th>
                        <th>脚本文件</th>
                        <th>脚本说明</th>
                        <th width='160'>末次执行</th>
                        <th width='240'>末次日志</th>
                        <th width='50'>日志</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                foreach ($crontabs as $crontab) {
                    $i ++;
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $crontab->when ?></td>
                        <td><?= $crontab->type; ?></td>
                        <td class=''><?= $crontab->process_name ?></td>
                        <td><?= $crontab->title; ?></td>
                        <td><?= $crontab->lastcrontime ?></td>
                        <td>
                            <?= $crontab->getLastCronLogTimeSpan()?>
                        </td>
                        <td>
                            <a href="/cronlogmgr/list?crontabid=<?= $crontab->id ?>" target='_blank'>日志</a>
                        </td>
                    </tr>
            <?php } ?>
            </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
