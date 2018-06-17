<?php
$pagetitle = "定时任务日志列表 CronLogs";
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
                        <th>脚本</th>
                        <th>执行时间</th>
                        <th>结束时间</th>
                        <th>执行摘要</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = $pagelink->getFirstNoOfThePage() - 1;
                foreach ($cronlogs as $cronlog) {
                    $i ++;
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td class=''>
                            <a href="/cronlogmgr/list?crontabid=<?= $cronlog->crontabid ?>"><?= $cronlog->crontab->process_name ?></a>
                        </td>
                        <td><?= $cronlog->begintime ?></td>
                        <td><?= $cronlog->endtime ?></td>
                        <td><?= $cronlog->brief ?></td>
                        <td>
                            <a href="/cronlogmgr/one?cronlogid=<?= $cronlog->id ?>" target='_blank'>详情</a>
                        </td>
                    </tr>
            <?php } ?>
                    <tr>
                        <td colspan=10>
<?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
