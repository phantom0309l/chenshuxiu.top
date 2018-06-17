<?php
$pagetitle = "订单/处方申请单";
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
            <form action="/shopordermgr/list" class="form-horizontal shopOrderForm">
                <input name="diseaseid" value="<?= $mydisease->id ?>" type="hidden"/>
                <div class="form-group">
                    <label class="col-md-2 control-label">疾病组:</label>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseGroupCtrArray(),'diseasegroupid',$diseasegroupid,'js-select2 form-control') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">类型 :</label>
                    <div class="col-md-6">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getShopOrderTypeCtrArray(),'type', $type, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getShopOrderHaveItemArray(),'haveitem', $haveitem, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">支付 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getShopOrderPayCtrArray(),'pay', $pay, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">状态 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getShopOrderStatusCtrArray(),'orderstatus', $orderstatus, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">发货 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getShopOrderSendoutCtrArray(),'sendout', $sendout, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">退款 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getShopOrderRefundCtrArray(),'refund', $refund, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">首单 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getShopOrderFirstCtrArray(),'first', $first, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">第几单:</label>
                    <div class="col-sm-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getShopOrderPosCtrArray(), "pos", $pos, 'js-select2 form-control') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">医生：</label>
                    <div class="col-sm-2">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                    <label class="col-sm-2 control-label">市场：</label>
                    <div class="col-sm-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getMarketAuditorCtrArray(),"auditorid",$auditorid, 'js-select2 form-control') ?>
                    </div>
                    <label class="col-sm-4 control-label" style="width:75px;">市场组：</label>
                    <div class="col-sm-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorGroupCtrArray(true, 'market'),"auditorgroupid",$auditorgroupid, 'js-select2 form-control') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">开始：</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="startdate" value="<?= $startdate ?>" placeholder="开始时间" />
                    </div>
                    <label class="control-label col-md-2">截止：</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="enddate" value="<?= $enddate ?>" placeholder="截至时间" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                    <?php if($myauditor->isHasRole(['yunyingmgr', 'tech', 'accountmgr'])){ ?>
                        <div class="col-md-2">
                            <span class="btn btn-primary btn-block outputBtn">市场数据</span>
                        </div>
                        <div class="col-md-2">
                            <span class="btn btn-primary btn-block outputShopOrderDetailBtn">订单明细</span>
                        </div>
                        <?php if($fuwu == 1){ ?>
                        <div class="col-md-2">
                            <span class="btn btn-primary btn-block outputService">服务1</span>
                        </div>
                        <div class="col-md-2">
                            <span class="btn btn-primary btn-block outputService2">服务2</span>
                        </div>
                        <?php } ?>
                    <?php } ?>
                    <?php if($myauditor->isHasRole(['account', 'accountmgr', 'techmgr'])){ ?>
                        <div class="col-md-2">
                            <a class="btn btn-primary btn-block" target="_blank" href="/shoppkgmgr/eorderlist">批量打印</a>
                        </div>
                    <?php } ?>
                </div>

                <div>
                    <div class="col-md-2">
                        实际销售额: <span class="red">¥<?= $left_amount_yuan_all ?></span>
                    </div>
                    <div class="col-md-2">
                        订单数: <span class="red"><?= $shop_order_cnt ?></span>
                    </div>
                </div>

                <div>
                    <?php if($patient instanceof Patient){ ?>
                        患者 :
                        <span class="red"><?=$patient->name?></span>
                        <a href="/shopordermgr/list">全部患者</a>
                    <?php } ?>
                </div>

            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td width="100"><?= $pay == "unpay" ? '创建时间' : '支付时间'?></td>
                    <td width="100">订单类型</td>
                    <td>第几单</td>
                    <td>患者</td>
                    <td>收货人</td>
                    <td>医生</td>
                    <td>市场</td>
                    <td width="150">商品详情</td>
                    <td>快递+挂号+商品=总金额(元)</td>
                    <td>支付</td>
                    <td>退款</td>
                    <td>状态</td>
                    <td style="min-width: 80px;">发货</td>
                    <td>留言</td>
                    <td width="50">快递单号</td>
                    <td width="50">处方编号</td>
                    <td width="100">操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($shopOrders as $i => $a) {
                    ?>
                <tr>
                    <td>
                        <?= $pay == "unpay" ? substr($a->createtime,5,11) : substr($a->time_pay,5,11) ?>
                    </td>
                    <td><?= $a->getTypeDesc() ?></td>
                    <td><?= $a->pos ?></td>
                    <td>
                        <a href="/shopordermgr/list?patientid=<?= $a->patientid ?>">
                            <?php if($a->patient instanceof Patient){ ?>
                            <?= $a->patient->getMaskName() ?>
                            <sup>[<?= ShopOrderDao::getShopOrderCntByPatientTime_paydate($a->patient, substr($a->time_pay,0,10)) ?>]</sup>
                            <?php } ?>
                        </a>
                    </td>
                    <td>
                        <?php if($a->shopaddress instanceof ShopAddress){ ?>
                            <span><?= $a->shopaddress->linkman_name ?></span>
                        <?php } ?>
                    </td>
                    <td><?= $a->thedoctor->name?></td>
                    <td><?= $a->thedoctor->marketauditor->name ?></td>
                    <td>
                        品类数：<?= $a->getShopOrderItemCnt() ?><br/>
                        总数量：<?= $a->getShopProductSumCnt() ?><br/>
                        <p style="font-size:12px;"><?= $a->getTitleOfShopProducts() ?></p>
                    </td>
                    <td align="right">
                        <?= $a->getExpress_price_yuan()?> + <?= $a->getGuahao_price_yuan() ?> + <?= $a->getItem_sum_price_yuan() ?> = <?= $a->getAmount_yuan()?>
                    </td>
                    <td align="center"><?= $a->getIs_payStr()?></td>
                    <td align="center"><?= $a->getRefundStr()?></td>
                    <td align="center"><?= $a->getStatusStr()?></td>
                    <td align="center">
                        <?php $shopPkgs = $a->getShopPkgs();
                            foreach ($shopPkgs as $shopPkg){ ?>
                                <?= $shopPkg->getIs_sendoutStr()?><br/>
                            <?php } ?>
                    </td>
                    <td align="center"><?= $a->remark ?></td>
                    <td align="center">
                        <?php foreach ($shopPkgs as $shopPkg){ ?>
                            <?= $shopPkg->express_no ?><br/>
                        <?php } ?>
                    </td>
                    <td align="center">
                        <?php
                            $prescription = PrescriptionDao::getPrescriptionByShopOrder($a);
                            if($prescription instanceof Prescription){
                                echo $prescription->chufang_cfbh;
                            }
                        ?>
                    </td>
                    <td align="center">
                        <a target="_blank" href="/shopordermgr/one?shoporderid=<?=$a->id ?>">详情</a>
                        <?php if($a->isValid()){ ?>
                            <?php if(1 == count($shopPkgs) && !$shopPkgs[0]->is_goodsout && !$shopPkgs[0]->is_sendout && $shopPkgs[0]->isFillExpress_no()){ ?>
                                <button class="btn btn-danger push-10-t sendoutBtn" data-shoppkgid="<?= $shopPkgs[0]->id ?>">出库并发货</button>
                            <?php }else{ ?>
                            <?php } ?>
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
<?php
$footerScript = <<<STYLE

    jQuery.prototype.serializeObject=function(){
        var obj=new Object();
        $.each(this.serializeArray(),function(index,param){
            if(!(param.name in obj)){
                obj[param.name]=param.value;
            }
        });
        return obj;
    };

    $(function(){

        var obj = {
            ".outputBtn" : "/shopordermgr/listOutput?",
            ".outputShopOrderDetailBtn" : "/shopordermgr/listOutputShopOrderDetail?",
            ".outputService" : "/shopordermgr/listOutputService?",
            ".outputService2" : "/shopordermgr/listOutputService2?"
        };

        function outputFunc(node, url){
            node.on("click", function(){
                var me = $(this);
                if(me.hasClass('process')){
                    return
                }
                me.addClass('process');
                me.text('正在导出，请稍等....');
                var data = $(".shopOrderForm").serializeObject();
    			$.ajax({
    				"type" : "post",
    				"data" : {
    					cnf : data
    				},
    				"dataType" : "json",
    				"url" : url,
    				"success" : function(d) {
                        if(d.errno == 0){
                            window.location.href = "/export_jobmgr/list";
                        }else{
                            alert("导出错误，请重新导出");
                            window.location.href = window.location.href;
                        }
    				}
    			});
            });
        }

        for(var nodeStr in obj){
            outputFunc($(nodeStr), obj[nodeStr]);
        }

        var can_click = true;
        $(document).on("click", ".sendoutBtn", function(){
            if(!can_click){
                return;
            }
            can_click = false;
            var me = $(this);
            var shoppkgid = me.data("shoppkgid");
			$.ajax({
				"type" : "post",
				"data" : {
					shoppkgid : shoppkgid
				},
				"dataType" : "json",
				"url" : "/shoppkgmgr/setIs_goodsoutAndIs_sendoutJson",
				"success" : function(d) {
                    if(d.errno == 0){
                        me.text("已出库发货").removeClass("sendoutBtn btn-danger").addClass("btn-default");
                    }else{
                        alert(d.errmsg);
                    }
                    can_click = true;
				}
			});
        });

    })

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
