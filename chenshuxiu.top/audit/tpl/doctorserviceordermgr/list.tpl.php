<?php
$pagetitle = "列表";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
.searchBar .control-label{ width:65px; text-align:left; padding-right:0px;}
.searchBar{ border-radius:3px;}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/doctorserviceordermgr/list" class="form-horizontal shopOrderForm">
            <div class="form-group">
                <label class="col-sm-2 control-label">医生：</label>
                <div class="col-sm-2">
                    <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                </div>
                <label class="control-label col-md-2">月份：</label>
                <div class="col-md-2">
                    <input type="text" class="calendar form-control" name="the_month" value="<?= $the_month ?>" placeholder="开始时间" />
                </div>
                <div class="col-md-2">
                    <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                </div>
            </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>id</td>
                    <td>月份</td>
                    <td>起始时间</td>
                    <td>截止时间</td>
                    <td>医生</td>
                    <td>类型</td>
                    <td>单位</td>
                    <td>医生审核时间</td>
                    <td>医生备注</td>
                    <td>是否结算</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($doctorServiceOrders as $i => $a) {
                    ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= substr($a->the_month, 0, 7) ?></td>
                    <td><?= $a->from_date ?></td>
                    <td><?= $a->end_date ?></td>
                    <td><?= $a->doctor->name ?></td>
                    <td><?= $a->doctorserviceordertpl->ename ?></td>
                    <td><?= $a->getAmount_yuan() ?></td>
                    <td><?= $a->doctor_time_confirmed ?></td>
                    <td><?= $a->content ?></td>
                    <td>
                        <?= $a->isRecharged() ? "<span class='green'>是</span>" : "<span class='red'>否</span>" ?>
                    </td>
                </tr>
                <?php } ?>
                <?php if(DoctorServiceOrderDao::getAmountSumOfNeedRechargeByDoctorThe_month($doctor, $the_month) > 0){ ?>
                <tr class="tr">
                    <td colspan="10">
                        <form action="/doctorServiceOrderMgr/genDoctorWithdrawOrderPost">
                            <input type="hidden" name="doctorid" value="<?= $doctor->id ?>" />
                            <input type="hidden" name="the_month" value="<?= $the_month ?>" />
                            <span>医事服务费总计：<?= $doctor->getDoctorServiceOrdersAmount_yuan($the_month) ?></span>
                            <span class="ml10">医生：<span><?= $doctor->name ?></span></span>
                            <span class="ml10">余额：<span class="red"><?= $doctor->getAccountRmbBalance_yuan() ?></span></span>
                            <input class="btn btn-primary ml10" type="submit" value="生成提现单"/>
                        </form>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>


<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
