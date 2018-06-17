<?php
$pagetitle = "任务统计概览 Optasks";
$cssFiles = [
    "{$img_uri}/v5/page/audit/optaskmgr/reviewlist/list.css?v=20161219",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div>
                <div class="diamondDiv">
                    任务完成总量<br/><?= $finished_cnt ?>
                </div>
                <div class="diamondDiv">
                    本周已完成<br/><?= $finished_week_cnt ?>
                </div>
                <div class="diamondDiv">
                    今日待完成<br/><?= $today_todo_cnt ?>
                </div>
                <br/>
                <a href="/optaskmgr/reviewlist?status=0">
                    <div class="diamondDiv">
                        待做任务总数及人数<br/><?= $open_cnt ?> / <?= $open_patient_cnt ?>
                    </div>
                </a>
                <a href="/optaskmgr/reviewlist?fromdate=<?= date("Y-m-d 00:00:00") ?>&status=0">
                    <div class="diamondDiv">
                        未开始任务总数及人数<br/><?= $plan_cnt ?> / <?= $plan_patient_cnt ?>
                    </div>
                </a>
                <a href="/optaskmgr/reviewlist?todate=<?= date("Y-m-d 00:00:00", time()+3600*24*8 ) ?>&status=0">
                    <div class="diamondDiv">
                        1周内待做任务总数及人数<br/><?= $week_cnt ?> / <?= $week_patient_cnt ?>
                    </div>
                </a>
                <div class="diamondDiv" data-toggle="tooltip" data-placement="right" title="<?php echo implode(" ",$none_patient_name_list) ?>">
                    无任务人数<br/><?= $none_patient_cnt ?>
                </div>
            </div>
            <div class="searchBar">
                <form action="/optaskmgr/reviewlist" method="get" class="pr">
                    <div class="mt10" >
                        <span>筛选时间</span>
                        从
                        <input type="text" value="<?= $fromdate ?>" readonly name="fromdate" class="calendar" />
                        到
                        <input type="text" value="<?= $todate ?>" readonly name="todate" class="calendar" />
                        <div class="mt10">
                            <label>按关闭任务人员筛选：</label>
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getYunyingAuditorCtrArray(),"auditorid_yunying",$auditorid_yunying,'f18');?>
                        </div>
                    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value="组合筛选" />
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>创建日期</td>
                        <td>开始时间</td>
                        <td>关闭时间</td>
                        <td>患者</td>
                        <td>标题</td>
                        <td>状态</td>
                        <td>描述</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach ($optasks as $a) {
                ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->plantime ?></td>
                        <td><?= $a->donetime ?></td>
                        <td><?= $a->patient->name ?></td>
                        <td><?= $a->optasktpl->title ?></td>
                        <td><?= $a->getStatusStr() ?></td>
                        <td><?= $a->content ?></td>
                        <td>
                            <a target="_blank" href="/optaskmgr/listnew?patient_name=<?= $a->patient->name ?>">查看</a>
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
<?php
$footerScript = <<<STYLE
    $(function () { $("[data-toggle='tooltip']").tooltip(); });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
