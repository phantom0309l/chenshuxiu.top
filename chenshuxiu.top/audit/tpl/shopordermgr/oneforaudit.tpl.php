<?php
$pagetitle = "订单绑定处方页";
$cssFiles = [
    $img_uri . "/static/css/bootstrap.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.css?v=20170829',
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.js?v=20170829',
    $img_uri . "/v5/common/setMedicineBreakDate.js?v=20171019",
]; //填写完整地址
$pageStyle = <<<STYLE
.infor-block{border : 1px solid #e9e9e9 }
.titleBar{margin-bottom : 10px;}
.oprationBox{margin-top : 10px; padding : 10px; border : 1px solid #e9e9e9;}
.recipeBox{margin : 20px;}
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
        <div style="margin : 5px 20px 10px 20px;">
            <span> <?= "患者：" . $patient->name ?> </span>
            <span> <?= "医生：" . $patient->doctor->name ?> </span>
            <ul class="block-options">
                <li> <a class="text-info" target="_blank" style="opacity: 1.0;color:#3169b1;" href="/patientmgr/list?keyword=<?=$shoporder->patientid?>">患者流</a> </li>
            </ul>
        </div>
        <div style="background:#fafafa;">
            <section class="col-md-6">
                <div class="infor-block">
                    <div class="block-header bg-gray-lighter titleBar">
                        <span> 开药门诊订单信息 </span>
                        <ul class="block-options">
                            <li> <a class="text-info" target="_blank" style="opacity: 1.0;color:#3169b1;" href="/shopordermgr/listforaudit?patientid=<?=$shoporder->patientid?>">查看全部物流</a> </li>
                        </ul>
                    </div>
                    <div style="text-align:center; margin:10px;" class="table-responsive">
                        <table class="table table-bordered col-md-10">
                            <thead>
                                <tr>
                                    <td style="text-align:center;">商品详情</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="text-align:left;">
                                        品类数：<?= $shoporder->getShopOrderItemCnt() ?> 总数量：<?= $shoporder->getShopProductSumCnt() ?><br/>
                                        <p style="font-size:12px;"><?= $shoporder->getTitleOfShopProducts() ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="">
                        <div class="">
                            <div class="">

                                <div style="margin : 10px;">
                                    <div class="">
                                        <div class="replyBox">
                                            <div>
                                                <p>
                                                    <textarea name="reply-msg" class="textarea reply-msg" cols="60" rows="6"><?= $shoporder->remark ?></textarea>
                                                </p>
                                            </div>
                                            <p>
                                                <span class="btn btn-default shoporderRemark" data-shoporderid="<?= $shoporder->id ?>">备注</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="oprationBox" style="margin-bottom : 10px; background : #6db8ed;">
                    <div class="">
                        <button class="btn btn-default shoporderbind <?= $shoporder->recipeid > 0 ? "btn-success" : "" ?>" id="button-search-patient" data-shoporderid="<?= $shoporder->id ?>" data-recipeid="<?= $recipe->id ?>" type="button"><?= $shoporder->recipeid > 0 ? "已绑定" : "绑定" ?></button>
                    </div>
                </div>
                <div class="oprationBox" style="margin-bottom : 10px; background : #f9fbff;">
                    <div class="">
                        <button class="btn btn-default shoporderPass <?= $shoporder->audit_status > 0 ? "btn-success" : "" ?>" id="button-search-patient" data-shoporderid="<?= $shoporder->id ?>" type="button"><?= $shoporder->audit_status > 0 ? "已关闭" : "关闭" ?></button>
                    </div>
                </div>
            </section>
            <section class="col-md-6">
                <div class="infor-block">
                    <div class="block-header bg-gray-lighter titleBar">
                        <span> 处方信息 </span>
                        <ul class="block-options">
                            <li> <a class="text-info" target="_blank" style="opacity: 1.0;color:#3169b1;" href="/recipemgr/list?keyword=<?=$shoporder->patientid?>">查看全部处方</a> </li>
                        </ul>
                    </div>
                    <div class="recipeBox">
                        <?php if($recipe instanceof Recipe){ ?>
                            <span style="font-size:18px;"> 处方时间：<?= $recipe->thedate ?> </span>
                            <span style="font-size:10px;"> 上传时间：<?= $recipe->createtime; ?> </span>
                            <div class="overflow:hidden" style="max-width:300px;">
                                <img class="img-responsive recipe-viewphoto viewer-toggle img-recipe"  data-url="<?= $recipe->picture->getSrc() ?>" src="<?= $recipe->picture->getSrc(400, 400, true)?>" alt="">
                            </div>
                        <?php }else { ?>
                            <span> 患者暂时没有上传处方！ </span>
                        <?php } ?>
                    </div>
                </div>
                <div class="block block-rounded" style="margin-top : 10px;">
                    <div class="block-header bg-gray-lighter titleBar">
                        <span> 当前服药信息 </span>
                    </div>
                    <div class="patientmedicineBox">
                        <?php include_once $tpl . '/_patient_medicine.php'; ?>
                        <?php if ($patient->diseaseid == 1) { include $tpl . "/_set_medicine_break_date.php"; } ?>
                    </div>
                </div>
            </section>
        </div>

        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<SCRIPT
$(function(){
    $('.img-recipe').viewer({
        inline: false,
        url: 'data-url',
        navbar: false,
        scalable: false,
        fullscreen: false,
        shown: function (e) {
        },
    });
    $(document).on("click", ".shoporderbind", function(){
        var me = $(this);
        if(me.hasClass('btn-success')){
            return;
        }

        var shoporderid = me.data('shoporderid');
        var recipeid = me.data('recipeid');

        if (0 == recipeid) {
            alert("没有可以绑定的处方！");
            return;
        }

        $.ajax({
            "type" : "post",
            "url" : "/shopordermgr/bindJson",
            dataType : "text",
            data : {
                shoporderid : shoporderid,
                recipeid : recipeid,
            },
            "success" : function(data) {
                if (data == 'success') {
                    me.text("已绑定");
                    me.addClass("btn-success");
                }
                if (data == 'false') {
                    alert("绑定失败");
                }
            }
        });
    });
    $(document).on("click", ".shoporderPass", function(){
        var me = $(this);
        if(me.hasClass('btn-success')){
            return;
        }

        var shoporderid = me.data('shoporderid');
        $.ajax({
            "type" : "post",
            "url" : "/shopordermgr/passJson",
            dataType : "text",
            data : {
                shoporderid : shoporderid,
            },
            "success" : function(data) {
                if (data == 'success') {
                    me.text("已关闭");
                    me.addClass("btn-success");
                }
            }
        });
    });
    $(document).on("click", ".shoporderRemark", function(){
        var me = $(this);
        if(me.hasClass('btn-success')){
            return;
        }

        var shoporderid = me.data('shoporderid');
        var remark = $('.reply-msg').val();
        $.ajax({
            "type" : "post",
            "url" : "/shopordermgr/remarkJson",
            dataType : "text",
            data : {
                shoporderid : shoporderid,
                audit_remark : remark,
            },
            "success" : function(data) {
                if (data == 'success') {
                    me.text("已添加");
                    me.addClass("btn-success");
                }
            }
        });
    });
});
SCRIPT;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
