<?php
$pagetitle = "电子运单批量打印";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/v5/lib/JsBarcode.all.min.js',
    $img_uri . "/v5/page/audit/shopordermgr/rendereorder.js?v=2"
]; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .control-label{ width:65px; text-align:left; padding-right:0px;}
.searchBar{ border-radius:3px;}
@media print {
    .fc-breadcrumb, .border1, .table-responsive, h5, form, #page-footer, #sidebar, #header-navbar{ display:none;}
    .header-navbar-fixed #main-container{ padding:0px;}
    .sectionBox{ padding:0px; }
    .printBox .topLogo{ visibility:hidden;}
    .printBox .print_paper td{ font-family: "Microsoft YaHei" }
    .printBox .print_paper td div{ font-family: "Microsoft YaHei" }
    .printBox .print_paper td span{ font-family: "Microsoft YaHei" }
    .baseMsg{ display:none;}
    .pages{ display:none;}
    .printBtnShell{ display:none;}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12 sectionBox">
        <div>
            <?php
                foreach ($shopPkgs as $i => $a) {
            ?>
                <p class="baseMsg">
                    <span>收货人：<?= $a->shoporder->shopaddress->linkman_name ?></span>
                    <span>患者：<?= $a->patient->getMaskName() ?></span>
                    <span>支付时间：<?= substr($a->shoporder->time_pay,5,11) ?></span>
                    <span class="btn btn-primary hideBtn">隐藏本单</span>
                </p>
                <div class="printBox" data-shoppkgid="<?= $a->id ?>"></div>
            <?php } ?>
        </div>
        <div class="printBtnShell">
            <span class="btn btn-danger printBtn">打印</span>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
$(function(){
    $(".printBox").each(function(){
        var node = $(this);
        var shoppkgid = node.data("shoppkgid");
        renderEorder(shoppkgid, node);
    })

    $(".printBtn").on("click", function(){
        window.print();
    })

    $(".hideBtn").on("click", function(){
        var me = $(this);
        var printBoxNode = me.parents(".baseMsg").next();
        if(printBoxNode.is(":visible")){
            printBoxNode.hide();
            me.text("显示本单");
        }else{
            printBoxNode.show();
            me.text("隐藏本单");
        }
    })
})

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
