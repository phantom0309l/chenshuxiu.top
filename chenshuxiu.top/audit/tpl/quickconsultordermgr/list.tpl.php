<?php
$pagetitle = "快速咨询列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .form-group label {
    font-weight: 500;
    width: 9%;
    text-align: left;
}
.padding-left0{
    padding-left: 0px;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="content bg-white border-b">
            <div class="row items-push text-uppercase">
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">支付总金额</div>
                    <div class="text-muted animated fadeIn"><small><i class="si si-calendar"></i> All Time</small></div>
                    <a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalAmount / 100) ?></a>
                </div>
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">退款总金额</div>
                    <div class="text-muted animated fadeIn"><small><i class="si si-calendar"></i> All Time</small></div>
                    <a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalRefundAmount / 100) ?></a>
                </div>
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">本月支付总金额</div>
                    <div class="text-muted animated fadeIn"><small><i class="si si-calendar"></i> All Time</small></div>
                    <a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalAmount_month / 100) ?></a>
                </div>
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">今天支付总金额</div>
                    <div class="text-muted animated fadeIn"><small><i class="si si-calendar"></i> All Time</small></div>
                    <a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalAmount_today / 100) ?></a>
                </div>
            </div>
        </div>

        <div class="searchBar">
            <form class="form-horizontal" action="/quickconsultordermgr/list" method="get">
                <div class="form-group mt10">
                    <label class="control-label col-md-2" style="text-align:left">状态</label>
                    <div class="col-md-3">
                        <select name="status" autocomplete="off" class="form-control">
                            <?php
                            $allstatus = QuickConsultOrder::getAllStatus(true);
                            foreach ($allstatus as $key => $value) { ?>
                                <option <?= $key == $status ? 'selected' : '' ?>
                                        value="<?= $key ?>"><?= $value ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="mt10">
                    <input type="submit" class="btn btn-success" value="组合筛选"/>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
                <thead>
                <tr>
                    <td>浏览时间</td>
                    <td>患者</td>
                    <td>医生</td>
                    <td>疾病</td>
                    <td>支付时间</td>
                    <td>状态</td>
                    <td>操作</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($quickconsultorders as $quickconsultorder) {
                    $patient = $quickconsultorder->patient;
                    ?>
                    <tr>
                        <td><?= $quickconsultorder->createtime ?></td>
                        <td><?= $patient->name ?></td>
                        <td><?= $patient->doctor->name ?></td>
                        <td><?= $patient->disease->name ?></td>
                        <td><?= $quickconsultorder->time_pay ?>
                            <?php
                            if ($quickconsultorder->status == 3) {
                                $timeRemaining = $quickconsultorder->getTimeRemaining();
                                if ($timeRemaining == 0) {
                                    echo '<span class="label label-danger">已超时</span>';
                                } else {
                                    echo "<span class='label label-info J_timeRemaining' data-timeremaining='{$timeRemaining}'></span>";
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $class = '';
                            switch ($quickconsultorder->status) {
                                case 3:
                                    $class = 'text-primary';
                                    break;
                                case 4:
                                    $class = 'text-primary';
                                    break;
                                case 5:
                                    $class = 'text-success';
                                    break;
                                default:
                                    $class = 'text-muted';
                                    break;
                            }
                            ?>
                            <span class="<?= $class ?>"><?= $quickconsultorder->getStatusStr() ?></span></td>
                        <td>
                            <?php if ($quickconsultorder->status == 3) { ?>
                                <a target="_blank" class="btn btn-sm btn-primary"
                                   href="/optaskmgr/listnew?patient_name=<?= $patient->name ?>&diseaseid=<?= $patient->diseaseid ?>&status_str=all">
                                    处理
                                </a>
                            <?php } else { ?>
                                <a target="_blank" class="btn btn-sm btn-default"
                                   href="/optaskmgr/listnew?patient_name=<?= $patient->name ?>&diseaseid=<?= $patient->diseaseid ?>&status_str=all">
                                    查看
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan=100 class="pagelink">
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
$(function() {
    $('.J_timeRemaining').each(function() {
        var time = $(this).data('timeremaining');
        resetTime(time, this);
    })

    //单纯分钟和秒倒计时
    function resetTime(time, el) {
        var timer = null;
        var t = time;
        var m = 0;
        var s = 0;
        m = Math.floor(t / 60 % 60);
        m < 10 && (m = '0' + m);
        s = Math.floor(t % 60);

        function countDown() {
            s--;
            s < 10 && (s = '0' + s);
            if (s.length >= 3) {
                s = 59;
                m = "0" + (Number(m) - 1);
            }
            if (m.length >= 3) {
                m = '00';
                s = '00';
                clearInterval(timer);
                $(el).removeClass('label-info');
                $(el).addClass('label-danger');
                $(el).html('已超时');
                return false;
            }
            $(el).html('<i class="glyphicon glyphicon-time"></i> ' + m + "分" + s + "秒");
        }

        timer = setInterval(countDown, 1000);
    }
})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
