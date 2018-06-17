<?php
$pagetitle = "新增服务商品";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form class="J_form">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>类型</th>
                        <td>
                            <div class="col-md-4">
                                <select autocomplete="off" name="type" id="type" class="js-select2 form-control">
                                    <option value="quickpass" selected="selected"> 快速通行证</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>图片</th>
                        <td>
                            <div class="col-md-4">
                                <?php
                                $picWidth = 150;
                                $picHeight = 150;
                                $pictureInputName = "pictureid";
                                $isCut = false;
                                $objtype = "ServiceProduct";
                                $objid = 0;
                                $objsubtype = "";
                                require_once("$dtpl/picture.ctr.php");
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>标题</th>
                        <td>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="title" placeholder="请填写服务商品标题" value="">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>短标题</th>
                        <td>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="short_title" placeholder="请填写服务商品短标题" value="">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>总价（元）</th>
                        <td>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="price" placeholder="请填写服务商品总价" value="">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>服务项数量</th>
                        <td>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="item_cnt" placeholder="请填写服务项数量，单位按类型来决定" value="">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>介绍</th>
                        <td>
                            <div class="col-md-4">
                                <textarea class="form-control" name="content" rows="4" cols="40" placeholder="请填写服务商品介绍"></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>状态</th>
                        <td>
                            <div class="col-md-4">
                                <label class="css-input css-radio css-radio-primary push-10-r">
                                    <input type="radio" name="status" value="1" checked=""><span></span> 上线
                                </label>
                                <label class="css-input css-radio css-radio-primary">
                                    <input type="radio" name="status" value="0"><span></span> 下线
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <div class="col-md-4">
                                <input class="btn btn-success J_submit" type="button" value="提交"/>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<script>
    $(function () {
        App.initHelper('select2');

        $(function () {
            $(".J_submit").on("click", function () {
                var me = $(this);
                if (me.data('confirm') == true) {
                    if (!confirm('是否提交？')) {
                        return false;
                    }
                }
                var data = $('.J_form').serialize();
                $.ajax({
                    "type": "post",
                    "url": "/serviceproductmgr/addjson",
                    dataType: "json",
                    data: data,
                    "success": function (res) {
                        if (res.errno === '0') {
                            alert('保存成功');
                            var type = $('#type').val();
                            window.location.href = '/serviceproductmgr/list?type=' + type;
                        } else {
                            alert(res.errmsg);
                        }
                    },
                    "error": function () {
                        alert('保存失败');
                    }
                });
            })
        });
    })
</script>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
