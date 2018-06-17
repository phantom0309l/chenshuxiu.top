<?php
$pagetitle = "定时任务 CronProcess";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/static/js/avalon.js",
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a id="search_byid" class="btn btn-success" data-cronprocesstplid="<?= $cronprocesstpl->id?>">添加定时任务 of <?= $cronprocesstpl->title?></a>
                <a id="search_byid" class="btn btn-success" href="/cronprocessmgr/list">显示全部定时任务</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <th>定时任务id</th>
                    <th>定时任务类名</th>
                    <th>所应用疾病</th>
                    <th>标题</th>
                    <th>序列号</th>
                    <th>状态</th>
                    <th>说明</th>
                    <th>操作</th>
                </thead>
                <tbody>
            <?php foreach ($cronprocesses as $cronprocess) { ?>
                <tr>
                        <td><?= $cronprocess->id ?></td>
                        <td>
                            <a href="/cronprocessmgr/list?cronprocesstasktype=<?= $cronprocess->tasktype?>"> <?= $cronprocess->tasktype ?></a>
                        </td>
                <?php if (false == $cronprocess->disease){ ?>
                    <td>全部</td>
                <?php }else{ ?>
                    <td><?= $cronprocess->disease->name ?></td>
                <?php } ?>
                    <td><?= $cronprocess->title ?></td>
                        <td><?= $cronprocess->pos ?></td>
                        <td>
                    <?php
                if ($cronprocess->status) {
                    ?>
                            打开
                    <?php
                } else {
                    ?>
                            关闭
                    <?php
                }
                ?>
                    </td>
                        <td><?= $cronprocess->remark ?></td>
                        <td>
                            <a href="/cronprocessmgr/modify?cronprocessid=<?= $cronprocess->id ?>">修改</a>
                        </td>
                    </tr>
            <?php } ?>
            </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $("#search_byid").on("click",function(){
        var me = $(this);
        var cronprocesstplid = me.data("cronprocesstplid");
        if (cronprocesstplid) {
            var url = '/cronprocessmgr/add?cronprocesstplid=' + cronprocesstplid;
            window.location.href = url;
        }else{
            alert("请选择一类任务模板！");
        }

    });
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
