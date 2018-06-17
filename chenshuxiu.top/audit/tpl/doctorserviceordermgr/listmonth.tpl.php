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
$(function(){
    $('.js-select2').select2();
})
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/doctorserviceordermgr/listmonth" class="form-horizontal shopOrderForm">
                <div class="form-group">
                    <label class="col-sm-2 control-label">医生：</label>
                    <div class="col-sm-2">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                    <label class="control-label col-md-2">月份：</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="the_month" value="<?= $the_month ?>" placeholder="开始时间" />
                    </div>
                    <label class="col-sm-2 control-label">门诊：</label>
                    <div class="col-sm-2">
                        <select class="js-select2 form-control" name="is_menzhen">
                            <option value="-1" <?= $is_menzhen == -1 ? 'selected' : ''?>>全部</option>
                            <option value="0" <?= $is_menzhen == 0 ? 'selected' : ''?>>未开启</option>
                            <option value="1" <?= $is_menzhen == 1 ? 'selected' : ''?>>已开启</option>
                        </select>
                    </div>
                    <label class="col-sm-2 control-label">签约：</label>
                    <div class="col-sm-2">
                        <select class="js-select2 form-control" name="is_sign">
                            <option value="-1" <?= $is_sign == -1 ? 'selected' : ''?>>全部</option>
                            <option value="0" <?= $is_sign == 0 ? 'selected' : ''?>>未签约</option>
                            <option value="1" <?= $is_sign == 1 ? 'selected' : ''?>>已签约</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                    <div class="col-md-2">
                        <a href="/doctorwithdrawordermgr/list" class="btn btn-default btn-block">汇款单列表</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>pos</td>
                    <td>id</td>
                    <td>月份</td>
                    <td>医生</td>
                    <td>医生order</td>
                    <td>活动</td>
                    <td class="none">医事服务1 + 医事服务2</td>
                    <td>是否全部结算</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($doctorServiceOrders as $i => $a) {
                    ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= $a->id ?></td>
                    <td><?= substr($a->the_month, 0, 7) ?></td>
                    <td><?= $a->doctor->name ?></td>
                    <td><?= $a->doctor->getDoctorServiceOrdersAmount_yuan($a->the_month) ?></td>
                    <td><?= $a->doctor->getActivePatientShouyiByThemonth($a->the_month) ?></td>
                    <td class="none">
                        <?= $a->doctor->getActivePatientShouyiByThemonth($a->the_month) ?>
                        +
                        <?= $a->doctor->getShopOrderShouyiByThemonth($a->the_month) ?>
                        =
                        <?= $a->doctor->getShouyiOfTheMonth($a->the_month) ?>
                    </td>
                    <td>
                        <?= DoctorServiceOrderDao::getAmountSumOfNeedRechargeByDoctorThe_month($a->doctor, $a->the_month) > 0 ? "<span class='red'>否</span>" : "<span class='green'>是</span>" ?>
                    </td>
                    <td align="center">
                        <a target="_blank" href="/doctorserviceordermgr/list?doctorid=<?=$a->doctorid ?>&the_month=<?= $a->the_month?>">详情</a>
                        <?php
                            $account = Account::getByUserAndCodeImp($a->doctor->user, 'doctor_rmb');
                            if($account instanceof Account){
                        ?>
                            <a target="_blank" href="/accountItemMgr/list?accountid=<?= $account->id?>">账户明细</a>
                        <?php } ?>
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

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
