<?php
$pagetitle = "修改";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <input type="hidden" value="<?= $shopPkg->id ?>" name="shoppkgid"/>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <td>Id</td>
                    <td>
                        <input type="text" name="wxuserid" value="<?= $shopPkg->id ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>创建时间</td>
                    <td>
                        <input type="text" name="createtime" value="<?= $shopPkg->createtime ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>患者</td>
                    <td>
                        <input type="text" name="patient-name" value="<?= $shopPkg->patient->name ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>所属医生</td>
                    <td>
                        <input type="text" name="doctor-name" value="<?= $shopPkg->patient->doctor->name ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>订单号</td>
                    <td>
                        <input type="text" name="shoporderid" value="<?= $shopPkg->shoporderid ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>配送单号</td>
                    <td>
                        <input type="text" name="fangcun_platform_no" value="<?= $shopPkg->fangcun_platform_no ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>实际运费</td>
                    <td>
                        <input type="text" name="express_price_real" value="<?= $shopPkg->getExpress_price_real_yuan() ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>是否出库</td>
                    <td>
                        <input type="text" name="is_goodsout" value="<?= $shopPkg->getIs_goodsoutStr() ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>是否发货</td>
                    <td>
                        <input type="text" name="is_sendout" value="<?= $shopPkg->getIs_sendoutStr() ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>快递公司</td>
                    <td>
                        <input type="text" name="express_company" value="<?= $shopPkg->express_company ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>快递号</td>
                    <td>
                        <input type="text" name="express_no" value="<?= $shopPkg->getExpress_noStr() ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>出库时间</td>
                    <td>
                        <input type="text" name="time_goodsout" value="<?= $shopPkg->time_goodsout ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>发货时间</td>
                    <td>
                        <input type="text" name="time_sendout" value="<?= $shopPkg->time_sendout ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>电子运单接口成功后返回信息</td>
                    <td>
                        <textarea rows="10" cols="120" name="eorder_content"><?= $shopPkg->eorder_content ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>是否需要推送到erp</td>
                    <td>
                        <input type="text" name="need_push_erp" value="<?= $shopPkg->getNeed_push_erpStr() ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>是否已推送到erp</td>
                    <td>
                        <input type="text" name="is_push_erp" value="<?= $shopPkg->getIs_push_erpStr() ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>推送到erp的时间</td>
                    <td>
                        <input type="text" name="time_push_erp" value="<?= $shopPkg->time_push_erp ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>推送remark</td>
                    <td>
                        <textarea rows="10" cols="120" name="remark_push_erp"><?= $shopPkg->remark_push_erp ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>状态</td>
                    <td>
                        <input type="text" name="status" value="<?= $shopPkg->getStatusStr() ?>"/>
                    </td>
                </tr>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
