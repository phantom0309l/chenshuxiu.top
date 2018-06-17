<?php
$pagetitle = "订单/处方申请单";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/v5/lib/JsBarcode.all.min.js',
    $img_uri . "/v5/page/audit/shopordermgr/rendereorder.js?v=2"
]; // 填写完整地址
$px = $isXP ? 740 : 790;
$pageStyle = <<<STYLE
@media print {
    .fc-breadcrumb, .border1, .table-responsive, h5, form, #page-footer, #sidebar, #header-navbar{ display:none;}
    .header-navbar-fixed #main-container{ padding:0px;}
    .sectionBox{ padding:0px; }
    .printBox .topLogo{ visibility:hidden;}
    .printBox .print_paper td{ font-family: "Microsoft YaHei" }
    .printBox .print_paper td div{ font-family: "Microsoft YaHei" }
    .printBox .print_paper td span{ font-family: "Microsoft YaHei" }
    body{ height:{$px}px; overflow:hidden;}
    #page-container{ height:{$px}px; overflow:hidden;}
}
h5{
    height: 40px;
    padding: 5px;
    line-height: 20px;
//    border-top: 1px solid #eee;
}
STYLE;

$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12 sectionBox">
        <div>
            <div>
                <h5>
                    <i class="fa fa-angle-right"></i>
                    订单详情
                </h5>
            </div>
            <div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered col-md-10">
                            <thead>
                            <tr>
                                <td width="90">订单ID</td>
                                <td width="100">创建时间</td>
                                <td width="100">支付时间</td>
                                <td width="100">订单类型</td>
                                <td>患者</td>
                                <td>所属医生</td>
                                <td width="90">品类数</td>
                                <td width="90">总数目</td>
                                <td width="100">合计(元)</td>
                                <td width="100">快递费(元)</td>
                                <td width="100">挂号费(元)</td>
                                <td width="90">总金额(元)</td>
                                <td width="90" style="text-align: center">支付?</td>
                                <td style="text-align: center">已退款金额(元)</td>
                                <td style="text-align: center">操作</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?= $shopOrder->id ?></td>
                                <td class="gray"><?= $shopOrder->getCreatemdHi() ?></td>
                                <td><?= substr($shopOrder->time_pay, 5, 11); ?></td>
                                <td><?= $shopOrder->getTypeDesc() ?></td>
                                <td>
                                    <a target="_blank"
                                       href="/patientmgr/list?keyword=<?= $shopOrder->patientid ?>"><?= $shopOrder->patient->name ?></a>
                                </td>
                                <td><?= $shopOrder->patient->doctor->name ?></td>
                                <td align="center"><?= $shopOrder->getShopOrderItemCnt() ?></td>
                                <td align="center"><?= $shopOrder->getShopProductSumCnt() ?></td>
                                <td align="center"><?= $shopOrder->getItem_sum_price_yuan() ?></td>
                                <td align="center">
                                    <?php
                                    if ($shopOrder->is_pay || true) {
                                        echo $shopOrder->getExpress_price_yuan();
                                    } else {
                                        ?>
                                        <form action="/shopordermgr/modifyExpress_pricePost" method="post">
                                            <input type="hidden" name="shoporderid" value="<?= $shopOrder->id; ?>"/>
                                            <input style="width: 100px" type="text" name="express_price_yuan"
                                                   value="<?= $shopOrder->getExpress_price_yuan(); ?>"/>
                                            元
                                            <input type="submit" class="btn btn-success" value="修改"/>
                                        </form>
                                    <?php } ?>
                                </td>
                                <td align="center"><?= $shopOrder->getGuahao_price_yuan() ?></td>
                                <td align="center" class="red"><?= $shopOrder->getAmount_yuan() ?></td>
                                <td align="center"><?= $shopOrder->getIs_payStr() ?></td>
                                <td align="center" class="red">
                                    <a href="#refund"><?= $shopOrder->getRefund_amount_yuan(); ?></a>
                                </td>
                                <td align="center">
                                    <?php if (!$shopOrder->is_pay) { ?>
                                        <a href="/shopordermgr/balancepaypost?shoporderid=<?= $shopOrder->id ?>">余额支付</a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=15 align="right">
                                    订单/处方申请单 状态 : <?= $shopOrder->getStatusStr() ?>
                                    <?php
                                    $prescription = $shopOrder->getPrescription();
                                    if ($prescription instanceof Prescription) {
                                        ?>
                                        <a target="_blank" href="/prescriptionmgr/one?prescriptionid=<?= $prescription->id ?>">处方详情</a>
                                    <?php } ?>

                                    <a target="_blank" href="/shopordermgr/list?patientid=<?= $shopOrder->patientid ?>">历史订单</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="clear: both"></div>
                    <!--                    <div class="border1 p10 m10">-->
                    <!---->
                    <!--                    </div>-->
                    <!--                    <div style="clear: both"></div>-->
                </div>
            </div>


            <?php if ($shopOrder->isWeituo()) { ?>
                <h5>
                    <i class="fa fa-angle-right"></i>
                    委托单审核
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered col-md-10 mt10">
                        <thead>
                        <tr>
                            <td>孩子身份证号</td>
                            <td style="width: 650px;">家长身份证图片</td>
                            <td>状态</td>
                            <td>操作</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= $shopOrder->patient->prcrid ?></td>
                            <td>
                                <?php foreach ($shopOrderPictures as $shopOrderPicture) { ?>
                                    <p>
                                        <img src="<?= $shopOrderPicture->picture->getSrc() ?>" style="width: 100%"/>
                                    </p>
                                <?php } ?>
                            </td>
                            <td>
                                <span><?= $shopOrder->getOrderStatusStr() ?></span>
                            </td>
                            <td>
                                <a class="btn btn-primary" href="/shopordermgr/passPost?shoporderid=<?= $shopOrder->id ?>">通过</a>
                                <a class="btn btn-danger" href="/shopordermgr/refusePost?shoporderid=<?= $shopOrder->id ?>">拒绝</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            <?php } ?>

            <h5>
                <i class="fa fa-angle-right"></i>
                配送地址
            </h5>
            <div>
                <div class="table-responsive">
                    <table class="table table-bordered col-md-10">
                        <thead>
                        <tr>
                            <td>联系人</td>
                            <td>联系电话</td>
                            <td>省</td>
                            <td>市</td>
                            <td>区</td>
                            <td>地址</td>
                            <td>邮编</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= $shopOrder->shopaddress->linkman_name ?></td>
                            <td><?= $shopOrder->shopaddress->linkman_mobile ?></td>
                            <td><?= $shopOrder->shopaddress->xprovince->name ?></td>
                            <td><?= $shopOrder->shopaddress->xcity->name ?></td>
                            <td><?= $shopOrder->shopaddress->xcounty->name ?></td>
                            <td><?= $shopOrder->shopaddress->content ?></td>
                            <td><?= $shopOrder->shopaddress->postcode ?></td>
                        </tr>
                        </tbody>
                        <tbody>
                        <tr>
                            <td colspan=10>
                                <?= $shopOrder->shopaddress->linkman_name ?>
                                <?= $shopOrder->shopaddress->linkman_mobile ?>
                                <?= $shopOrder->shopaddress->xprovince->name ?>
                                <?= $shopOrder->shopaddress->xcity->name ?>
                                <?= $shopOrder->shopaddress->xcounty->name ?>
                                <?= $shopOrder->shopaddress->content ?>
                                <a target="_blank" class="btn btn-default"
                                   href="/shopaddressmgr/modify?shopaddressid=<?= $shopOrder->shopaddress->id ?>">修改地址</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div style="clear: both"></div>
            </div>
            <h5>
                <i class="fa fa-angle-right"></i>
                订单明细
                <?php if ($shopOrder->canPkg()) { ?>
                    <button class="btn btn-xs btn-primary pkg-btn" data-shoporderid="<?= $shopOrder->id ?>">剩余可配生成配送单</button>
                <?php } ?>
            </h5>
            <div>
                <div class="table-responsive">
                    <table class="table table-bordered col-md-10">
                        <thead>
                        <tr>
                            <td width="90">明细ID</td>
                            <td width="110">商品/药品ID</td>
                            <td width="120">图片</td>
                            <td>标题</td>
                            <td width="90">单价(元)</td>
                            <td width="90">数量</td>
                            <td width="90">剩余可配数量</td>
                            <td width="90">可退</td>
                            <td width="90">金额(元)</td>
                            <td width="100">操作</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($shopOrderItems as $a) {
                            $shopProduct = $a->shopproduct;
                            ?>
                            <tr>
                                <td><?= $a->id ?></td>
                                <td><?= $a->shopproductid ?></td>
                                <td>
                                    <img src="<?= $shopProduct->picture->getSrc(100, 100) ?>">
                                </td>
                                <td><?= $shopProduct->title ?></td>
                                <td align="right"><?= $a->getPrice_yuan() ?></td>
                                <td align="right"><?= $a->cnt ?></td>
                                <td align="right" class="<?= $a->getCanPkgCnt() ? 'red' : '' ?>"><?= $a->getCanPkgCnt() ?></td>
                                <td align="right" class="red"><?= $a->getMaxGoodsBackCnt() ?></td>
                                <td align="right"><?= $a->getAmount_yuan() ?></td>
                                <td>
                                    <a target="_blank" href="/shopproductmgr/one?shopproductid=<?= $a->shopproductid ?>">详情</a>
                                    <?php if ($shopOrder->isGoodsOutAll()) { ?>
                                        <p class="mt10"><a class="btn btn-default" target="_blank"
                                                           href="/shoporderitemmgr/refundShopProduct?shoporderitemid=<?= $a->id ?>&is_recycle=0">退货不入库存</a>
                                        </p>
                                        <p><a class="btn btn-default" target="_blank"
                                              href="/shoporderitemmgr/refundShopProduct?shoporderitemid=<?= $a->id ?>&is_recycle=1">退货入库存</a></p>
                                    <?php } else { ?>
                                        <p class="mt10">未出库商品不能退货</p>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td colspan=5 align="right">合计:</td>
                            <td align="right"><?= $shopOrder->getShopProductSumCnt() ?></td>
                            <td></td>
                            <td></td>
                            <td align="right"><?= $shopOrder->getItem_sum_price_yuan() ?></td>
                        </tr>
                        <tr>
                            <td colspan=8 align="right">配送费:</td>
                            <td align="right"><?= $shopOrder->getExpress_price_yuan() ?></td>
                        </tr>
                        <tr>
                            <td colspan=8 align="right">挂号费:</td>
                            <td align="right"><?= $shopOrder->getGuahao_price_yuan() ?></td>
                        </tr>
                        <tr>
                            <td colspan=8 align="right">总计:</td>
                            <td align="right" class="red"><?= $shopOrder->getAmount_yuan() ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div style="clear: both"></div>
            </div>
            <h5>
                <i class="fa fa-angle-right"></i>
                配送单
                <?php if (1 == count($shopPkgs)) { ?>
                    (方寸平台单号：<?= $shopPkgs[0]->fangcun_platform_no ?>)
                    <button class="btn btn-xs btn-primary pre-divide-btn" data-shoppkgid="<?= $shopPkgs[0]->id ?>">拆单</button>
                <?php } ?>
            </h5>

            <?php foreach ($shopPkgs as $i => $shopPkg) { ?>
                <div class="shoppkg-box">
                    <div class="mb10">
                        <?php if (count($shopPkgs) > 1) { ?>
                            配送单<?= $i + 1 ?>(方寸平台单号：<?= $shopPkg->fangcun_platform_no ?>)
                            <?php if ($shopPkg->canChange()) { ?>
                                <button class="btn btn-xs btn-primary pre-divide-btn" data-shoppkgid="<?= $shopPkg->id ?>">拆单</button>
                                <button class="btn btn-xs btn-danger delete-shoppkg-btn" data-shoppkgid="<?= $shopPkg->id ?>">删除</button>
                            <? } ?>
                        <? } ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered col-md-10">
                            <thead>
                            <tr>
                                <td width="90">明细ID</td>
                                <td width="110">商品/药品ID</td>
                                <td width="120">图片</td>
                                <td>标题</td>
                                <td width="90">单价(元)</td>
                                <td width="40">数量</td>
                                <td width="90">金额(元)</td>
                                <td width="90">操作</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($shopPkg->getShopPkgItems() as $a) {
                                $shopProduct = $a->shopproduct;
                                ?>
                                <tr>
                                    <td><?= $a->id ?></td>
                                    <td><?= $a->shopproductid ?></td>
                                    <td>
                                        <img src="<?= $shopProduct->picture->getSrc(100, 100) ?>">
                                    </td>
                                    <td><?= $shopProduct->title ?></td>
                                    <td align="right"><?= $a->getPrice_yuan() ?></td>
                                    <td align="right"><input <?= $shopPkg->canChange() ? '' : 'disabled' ?> class="shoppkgitem-cnt"
                                                                                                            style="width: 50px;"
                                                                                                            value="<?= $a->cnt ?>"></td>
                                    <td align="right"><?= $a->getAmount_yuan() ?></td>
                                    <td>
                                        <?php if ($shopPkg->canChange()) { ?>
                                            <button class="btn btn-primary save-shoppkgitem-cnt" data-shoppkgitemid="<?= $a->id ?>">保存</button>
                                            <button class="btn btn-danger delete-shoppkgitem-btn" data-shoppkgitemid="<?= $a->id ?>">删除</button>
                                        <? } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="8">
                                    <?php if (!$shopPkg->is_goodsout) { ?>
                                        <?php if ($shopPkg->isValid()) { ?>
                                            <?php if (true == $shopPkg->checkStockInTpl()) { ?>
                                                <a class="btn btn-success shownotgroup"
                                                   href="/shoppkgmgr/setIs_goodsoutPost?shoppkgid=<?= $shopPkg->id ?>&is_goodsout=1">设置为已出库</a>
                                            <?php } else { ?>
                                                <a class="btn btn-default" href="/shopproductmgr/list">去补库存</a>
                                            <?php } ?>
                                            <span class="red">选择快递：<?= $shopPkg->express_company ?></span>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php if ($shopPkg->isValid()) { ?>


                                            <?php if ($shopPkg->isFillExpress_no()) { ?>
                                                发货状态: <?= $shopPkg->getIs_sendoutStr(); ?>
                                                <?php
                                                if ($shopPkg->is_sendout) {
                                                    ?>
                                                    <span class="gray">( <?= $shopPkg->time_sendout; ?> )</span>
                                                    <a class="btn btn-success shownotgroup"
                                                       href="/shoppkgmgr/setIs_sendoutPost?shoppkgid=<?= $shopPkg->id ?>&is_sendout=0">设置为未发货</a>
                                                <?php } else { ?>
                                                    <a class="btn btn-success shownotgroup"
                                                       href="/shoppkgmgr/setIs_sendoutPost?shoppkgid=<?= $shopPkg->id ?>&is_sendout=1">设置为已发货</a>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>

                                    <?php if ($shopPkg->canPrintSFEOrder()) { ?>
                                        <span class="btn btn-success preprintBtn" data-shoppkgid=<?= $shopPkg->id ?>>打印预览</span>
                                        <span class="btn btn-danger printBtn none">打印</span>
                                    <?php } ?>

                                    <?php if ($shopPkg->canPushErp()) { ?>
                                        <button class="btn btn-default tradeAddBtn" data-shoppkgid="<?= $shopPkg->id ?>">推送订单到ERP</button>
                                    <?php } ?>

                                    <?php if ($shopPkg->is_push_erp) { ?>
                                        已推送ERP
                                    <?php } ?>

                                </td>
                            </tr>
                            <tr>
                                <td colspan="8">
                                    <form action="/shoppkgmgr/expressModifyPost" method="post" class="form-horizontal"
                                          style="margin-bottom: 10px;">
                                        <input type="hidden" name="shoppkgid" value="<?= $shopPkg->id; ?>"/>
                                        <div class="form-group" style="margin-bottom:0px;">
                                            <label class="col-sm-1 control-label">快递公司: </label>
                                            <span class="col-sm-2">
                    <?= HtmlCtr::getSelectCtrImp(CtrHelper::getExpress_companyCtrArrayForAudit(), 'express_company', $shopPkg->express_company, 'form-control') ?>
                </span>
                                            <label class="col-sm-1 control-label">快递单号: </label>
                                            <span class="col-sm-2">
                    <input type="text" name="express_no" value="<?= $shopPkg->express_no; ?>" class="form-control"/>
                </span>
                                            <label class="col-sm-1 control-label">实际运费: </label>
                                            <span class="col-sm-1">
                    <input type="text" name="express_price_real" value="<?= $shopPkg->getExpress_price_real_yuan(); ?>"
                           class="form-control"/>
                </span>
                                            <label class="col-sm-1 control-label">发票号: </label>
                                            <span class="col-sm-2">
                    <input type="text" name="invoice_no" value="<?= $shopOrder->invoice_no; ?>" class="form-control"/>
                </span>
                                            <input type="submit" class="btn btn-success" value="修改"/>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="printBox"></div>
                    <div style="clear: both"></div>

                </div>
            <?php } ?>

            <h5>
                <i class="fa fa-angle-right"></i>
                <a id="refund">支付和退款</a>
            </h5>
            <div>
                <div class="table-responsive">
                    <table class="table table-bordered col-md-10">
                        <thead>
                        <tr>
                            <td width="160">时间</td>
                            <td width="160">code</td>
                            <td width="160">金额(元)</td>
                            <td>备注</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($shopOrder->getAccountTransArray() as $a) { ?>
                            <tr>
                                <td><?= $a->createtime ?></td>
                                <td><?= $a->code ?></td>
                                <td><?= ($a->code == "pay") ? "-" : "+"; ?><?= $a->getAmount_yuan() ?></td>
                                <td><?= $a->remark ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td>合计退款</td>
                            <td class="red"><?= $shopOrder->getRefund_amount_yuan() ?></td>
                            <td>
                                用户[<?= $shopOrder->userid ?>]账户[user_rmb]余额: <span
                                        class="red"><?= $shopOrder->user->getAccount('user_rmb')->getBalance_yuan(); ?></span>
                                元
                                <a target="_blank" href="/accountitemmgr/list?accountid=<?= $userRmbAccount->id ?>">[明细]</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <?php if ($shopOrder->getLeft_amount() > 0) { ?>
                    <form action="/shopordermgr/RefundToAccountPost">
                        <input type="hidden" name="shoporderid" value="<?= $shopOrder->id; ?>"/>
                        <br/>
                        金额:
                        <input style="width: 100px" type="text" name="amount_yuan" value="0"/>
                        元 备注:
                        <input name="remark" type="text"/>
                        <input type="submit" class="btn btn-success" value="提交"/>
                    </form>
                <?php } ?>
                <div style="clear: both"></div>
            </div>

            <!--        配送单选择拆几单的模态框-->
            <div class="modal fade" id="modal-choice-box" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-body" style="font-size: 18px;">
                            您要将此配送单拆成
                            <select id="shopPkg-num-choice">
                                <option value="2" selected>2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                            单吗？
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                关闭
                            </button>

                            <button type="button" class="btn btn-primary choice-btn">
                                确定
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <!--        配送单拆分的模态框-->
            <div class="modal fade" id="modal-shopPkg-box" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>

                            <h4 class="modal-title" id="shopPkgBoxLabel">
                                配送单拆分
                            </h4>
                        </div>

                        <div class="modal-body">

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                关闭
                            </button>

                            <button type="button" class="btn btn-primary divide-btn">
                                提交
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
$(function(){
    $(".preprintBtn").on("click", function(){
        var me = $(this);
        me.hide();
        me.parents(".shoppkg-box").find(".printBtn").show();
        var shoppkgid = me.data("shoppkgid");
        var node = me.parents(".shoppkg-box").find(".printBox");
        renderEorder(shoppkgid, node);
    });

    $(".printBtn").on("click", function(){
        window.print();
    });

    var canClick = true;
    $(".tradeAddBtn").on("click", function(){
        var me = $(this);
        if(!canClick){
            return;
        }
        canClick = false;
        $.ajax({
            "type" : "post",
            "data" : {
                shoppkgid : me.data("shoppkgid")
            },
            "dataType" : "json",
            "url" : "/shoppkgmgr/tradeAddJson",
            "success" : function(d) {
                var errno = d.errno;
                if(errno == 0){
                    me.addClass("btn-success").text("已推送!");
                }else{
                    var errmsg = d.errmsg;
                    alert(errmsg);
                }
            }
        });
    });

    $(".pre-divide-btn").on("click", function(){
        var me = $(this);
        var shoppkgid = me.data("shoppkgid");
        $("#modal-choice-box").modal("show");
        $("#modal-choice-box").find(".choice-btn").data("shoppkgid", shoppkgid);
    });

    $(".choice-btn").on("click", function(){
        var me = $(this);
        $("#modal-choice-box").modal("hide");
        var shoppkgnum = me.parents("#modal-choice-box").find("#shopPkg-num-choice").val();
        var shoppkgid = me.data("shoppkgid");
        $.ajax({
            "type" : "get",
            "data" : {
                shoppkgid : shoppkgid,
                shoppkgnum : shoppkgnum,
            },
            "dataType" : "html",
            "url" : "/shoppkgmgr/dividehtml",
            "success" : function(data) {
                $("#modal-shopPkg-box .modal-body").html(data);
                $("#modal-shopPkg-box").modal("show");
            }
        });
    });

    function checkInput(nodes) {
        var flag = true;
        nodes.each(function () {
            var me = $(this);
            if ('' == me.val()) {
                flag = false;
                return flag;
            }
        });
        return flag;
    }

    $(".divide-btn").on("click", function(){
        var me = $(this);

        if(!confirm('请仔细审查并确认！')){
            return;
        }

        var nodes = $(".divide-input");
        if(false == checkInput(nodes)){
            alert("请补全数据，不允许为空，没有填0！");
            return;
        }

        var data = $('#divideForm').serialize();
        $.ajax({
            "type": "post",
            "url": "/shoppkgmgr/dividejson",
            dataType: "json",
            data: data,
            "success": function (res) {
                if (res.errno === '0') {
                    alert('拆分成功！');
                    window.location.reload();
                } else {
                    alert(res.errmsg);
                }
            },
            "error": function () {
                alert('拆分失败！');
                window.location.reload();
            }
        });
    });

    $(".save-shoppkgitem-cnt").on("click", function(){
        var me = $(this);

        var shoppkgitemid = me.data("shoppkgitemid");
        var cnt = me.parents("tr").find(".shoppkgitem-cnt").val();
        if(false == (/^[1-9]*[0-9]{1}$/.test(cnt))){
            alert("请输入数字！");
            window.location.reload();
            return;
        }

        $.ajax({
            "type": "post",
            "url": "/shoppkgitemmgr/modifycntjson",
            dataType: "json",
            data: {
                shoppkgitemid: shoppkgitemid,
                cnt: cnt,
            },
            "success": function (res) {
                if (res.errno === '0') {
                    alert('修改成功！');
                    window.location.reload();
                } else {
                    alert(res.errmsg);
                    window.location.reload();
                }
            },
            "error": function () {
                alert('修改失败！');
                window.location.reload();
            }
        });
    });

    $(".delete-shoppkg-btn").on("click", function(){
        var me = $(this);

        if(!confirm('确定要删除此配送单吗？')){
            return;
        }

        var shoppkgid = me.data("shoppkgid");

        $.ajax({
            "type": "post",
            "url": "/shoppkgmgr/deletejson",
            dataType: "json",
            data: {
                shoppkgid: shoppkgid,
            },
            "success": function (res) {
                if (res.errno === '0') {
                    alert('删除成功！');
                    window.location.reload();
                } else {
                    alert(res.errmsg);
                    window.location.reload();
                }
            },
            "error": function () {
                alert('删除失败！');
                window.location.reload();
            }
        });
    });

    $(".delete-shoppkgitem-btn").on("click", function(){
        var me = $(this);

        if(!confirm('确定要删除此配送单吗？')){
            return;
        }

        var shoppkgitemid = me.data("shoppkgitemid");

        $.ajax({
            "type": "post",
            "url": "/shoppkgitemmgr/deletejson",
            dataType: "json",
            data: {
                shoppkgitemid: shoppkgitemid,
            },
            "success": function (res) {
                if (res.errno === '0') {
                    alert('删除成功！');
                    window.location.reload();
                } else {
                    alert(res.errmsg);
                    window.location.reload();
                }
            },
            "error": function () {
                alert('删除失败！');
                window.location.reload();
            }
        });
    });

    $(".pkg-btn").on("click", function(){
        var me = $(this);

        if(!confirm('确定要生成配送单吗？')){
            return;
        }

        var shoporderid = me.data("shoporderid");

        $.ajax({
            "type": "post",
            "url": "/shopordermgr/pkgjson",
            dataType: "json",
            data: {
                shoporderid: shoporderid,
            },
            "success": function (res) {
                if (res.errno === '0') {
                    alert('生成成功！');
                    window.location.reload();
                } else {
                    alert(res.errmsg);
                    window.location.reload();
                }
            },
            "error": function () {
                alert('生成失败！');
                window.location.reload();
            }
        });
    });
})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
