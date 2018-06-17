<?php
$pagetitle = "{$doctor->name}绑定药品";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <?php if ($mydisease instanceof Disease) { ?>
            <?php if (count($shopProductTypes) > 0) { ?>
                <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
                    <form action="/doctorshopproductrefmgr/binddoctor" class="form-horizontal">
                        <input type="hidden" id="doctorid" name="doctorid" value="<?= $doctor->id ?>"/>
                        <div class="form-group">
                            <label class="col-md-1 control-label">类型 :</label>
                            <div class="col-md-10">
                                <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::toShopProductTypeCtrArray($shopProductTypes), 'shopproducttypeid', $shopproducttypeid, 'css-radio-success') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-2">
                                <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                            </div>
                        </div>
                    </form>
                </div>
                <!---->
                <div class="block">
                    <div class="block-header">
                        <div class="block-options">
                            <a href="javascript:void(0);" class="btn btn-primary J_bindOnlineShopProducts">一键开启当前疾病的线上药品</a>
                        </div>
                        <div class="block-title">
                            <button name="status" class="btn btn-primary openbtn" data-status="1">批量开启</button>
                            <button name="status" class="btn btn-primary closebtn" data-status="0">批量关闭</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <!-- If you put a checkbox in thead section, it will automatically toggle all tbody section checkboxes -->
                        <table class="js-table-checkable table table-hover">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 70px;">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                        <input type="checkbox" id="check-all" name="check-all"><span></span>
                                    </label>
                                </th>
                                <th>药品名称</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($shopProducts as $shopProduct) {
                                if (in_array($shopProduct->id, array(299188646, 299326226))) {
                                    continue;
                                }
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <label class="css-input css-checkbox css-checkbox-primary">
                                            <input type="checkbox" name="shopProduct" value="<?= $shopProduct->id ?>"><span></span>
                                        </label>
                                    </td>
                                    <td>
                                        <?= $shopProduct->title ?>
                                    </td>
                                    <td>
                                            <span class="btn btn-default bindBtn <?= $doctor->hasBindShopProduct($shopProduct) ? "btn-primary" : "" ?>"
                                                  data-status="1" data-shopproductid="<?= $shopProduct->id ?>">开启</span>
                                        <span class="btn btn-default bindBtn <?= $doctor->hasBindShopProduct($shopProduct) ? "" : "btn-primary" ?>"
                                              data-status="0" data-shopproductid="<?= $shopProduct->id ?>">关闭</span>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="block-footer">
                        <div class="block-options">
                        </div>
                        <div class="block-title fl">
                            <button name="status" class="btn btn-primary openbtn" data-status="1">批量开启</button>
                            <button name="status" class="btn btn-primary closebtn" data-status="0">批量关闭</button>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <p class="mt10 red">
                    <span>当前疾病下没有商品类型</span>
                    <a href="/shopproducttypemgr/list" target="_blank">新建商品类型</a>
                </p>
            <?php } ?>
        <?php } else { ?>
            <p class="mt10 red">
                <span>请选择一个疾病</span>
            </p>
        <?php } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function () {
        var app = {
            canClick: true,
            init: function () {
                var self = this;
                self.handleBind();
            },
            handleBind: function () {
                var self = this;

                // 一键当前疾病的开启线上药品
                $('.J_bindOnlineShopProducts').on('click', function (e) {
                    if (!confirm('是否一键开启当前疾病的线上药品？')) {
                        return false;
                    }

                    window.location.href = "/doctorshopproductrefmgr/bindOnlineShopProducts?doctorid={$doctor->id}";
                });

                // 单个开启，单个关闭
                $(".bindBtn").on("click", function (e) {
                    e.preventDefault();
                    var me = $(this);
                    if (me.hasClass('btn-primary')) {
                        return false;
                    }
                    if (!self.canClick) {
                        return false;
                    }
                    self.canClick = false;
                    var doctorid = self.getDoctorid();
                    var shopproductid = me.data("shopproductid");
                    var status = me.data("status");
                    $.ajax({
                        url: '/doctorshopproductrefmgr/bindOrUnbindShopProductJson',
                        type: 'post',
                        dataType: 'text',
                        data: {doctorid: doctorid, shopproductid: shopproductid, status: status}
                    })
                        .done(function () {
                            me.parents("td").find(".btn-primary").removeClass('btn-primary');
                            me.addClass('btn-primary');
                        })
                        .fail(function () {
                            console.log("error");
                        })
                        .always(function () {
                            console.log("complete");
                            self.canClick = true;
                        });
                    return false;
                });

                // 批量开启，批量关闭
                $(".openbtn, .closebtn").on("click", function () {
                    self.canClick = false;
                    var checked_shopProducts = $('input[name="shopProduct"]:checked');
                    if (checked_shopProducts.length === 0) {
                        alert("请先选择药品");
                        return false;
                    }
                    var shopProductids = [];
                    var status = $(this).data('status');
                    checked_shopProducts.each(function (index, shopProduct) {
                        shopProductids.push($(shopProduct).val());
                    });
                    var doctorid = self.getDoctorid();
                    $.ajax({
                        url: '/doctorshopproductrefmgr/bindOrUnbindShopProductsJson',
                        type: 'post',
                        dataType: 'json',
                        data: {doctorid: doctorid, shopproductids: shopProductids, status: status}
                    }).done(function (response) {
                        if (response.errno === '0') {
                            window.location.reload();
                        } else {
                            alert(response.errmsg);
                        }
                    }).fail(function () {
                        alert("操作失败");
                    });
                    return false;
                })
            },
            getDoctorid: function () {
                return $("#doctorid").val();
            }
        };
        app.init();
    });
  
    $(function () {
        App.initHelpers('table-tools');
    });
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
