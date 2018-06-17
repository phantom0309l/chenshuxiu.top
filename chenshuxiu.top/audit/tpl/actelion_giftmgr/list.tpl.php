<?php
$pagetitle = "积分礼品";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<script text="javascript" src="<?= $img_uri ?>/jquery/jquery-file-upload.js"></script>
<script text="javascript">
    function uploadimg(obj) {
        if (obj.value.length > 0) {
            $.ajaxFileUpload({
                url: '/picture/uploadimagepost/?w=150&h=150&isCut=&type=LessonMaterial', //需要链接到服务器地址,w=缩略图宽,h=缩略图高
                secureuri: false,
                fileElementId: 'input-uploadimg', //文件选择框的id属性
                dataType: 'json', //服务器返回的格式，可以是json
                success: function (data, status) {            //相当于java中try语句块的用法
                    console.log(data);
                    var reg = /\d+_\d+\./;
                    var image_url = data.thumb.replace(reg, "");
                    var newimgDiv = "<div class=\"img-container fx-opt-zoom-out imgDiv\" style='width: 160px;height: 160px'>\n" +
                        "                <input type=\"hidden\" class=\"pictureid\" id='pictureid' name=\"pictureid\" value=\"" + data.pictureid + "\">\n" +
                        "                <img class=\"img-responsive\" src=\"" + data.thumb + "\" alt=\"\">\n" +
                        "                <div class=\"img-options\">\n" +
                        "                    <div class=\"img-options-content\" style=\"margin-top: 60px;\">\n" +
                        "                        <a target='_blank' class=\"btn btn-sm btn-default\" href=\"" + image_url + "\"><i class=\"fa fa-pencil\"></i>原图</a>\n" +
                        "                    </div>\n" +
                        "                </div>\n" +
                        "            </div>";
//                    alert(newimgDiv);
                    $("#showimg").append(newimgDiv);
                },
                error: function (data, status, e) {            //相当于java中catch语句块的用法
                    $('#upload_status').html('上传失败');
                    alert(data);
                    alert(e);
                }
            });
        }
    }
</script>

<div class="col-md-12">
    <section class="col-md-12">
        <input type="hidden" id="patientid" value="<?=$patient->id?>">
        <div class="col-md-12">
            <div class="col-sm-3 col-xs-2 success" style="float: left; padding: 0px; line-height: 2.5;">
                <button class="btn btn-sm btn-primary" data-type="add" data-toggle="modal" data-target="#gift-edit" type="button">
                    <i class="fa fa-plus push-5-r"></i> 新建
                </button>
            </div>
            <div class="clear"></div>
        </div>
        <div class="col-md-12" style="overflow-x: auto">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="width: 100px">id</th>
                    <th style="width: 190px">创建时间</th>
                    <th>名称</th>
                    <th>图片</th>
                    <th>价格</th>
                    <th>剩余库存</th>
                    <th>初始化库存</th>
                    <th>备注</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($actelion_gifts as $a) { ?>
                    <tr>
                        <td><?=$a->id?></td>
                        <td><?=$a->createtime?></td>
                        <td><?=$a->title?></td>
                        <td>
                            <div class="col-lg-6 animated fadeIn">
                                <div class="img-container">
                                    <img class="img-responsive" src="<?=$a->picture->getSrc(200, 200)?>" alt="">
                                    <div class="img-options">
                                        <div class="img-options-content">
                                            <a target='_blank' class="btn btn-sm btn-default" href="<?=$a->picture->getSrc()?>"><i class="fa fa-pencil"></i>原图</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><?=$a->jifen_price?></td>
                        <td><?=$a->left_cnt?></td>
                        <td><?=$a->init_cnt?></td>
                        <td><?=$a->remark?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button class="btn btn-xs btn-default" type="button" data-id="<?=$a->id?>" data-remark="<?=$a->remark?>" data-title="<?=$a->title?>" data-jifen_price="<?=$a->jifen_price?>" data-init_cnt="<?=$a->init_cnt?>" data-type="modify" data-toggle="modal" data-target="#gift-edit" title="" data-original-title="Edit Client"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-xs btn-default" type="button" data-toggle="tooltip" title="" data-original-title="Remove Client"><i class="fa fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- 模态框 -->
<div class="modal" id="gift-edit" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button">
                                <i class="si si-close"></i>
                            </button>
                        </li>
                    </ul>
                    <h3 class="block-title" id="type-title">新建</h3>
                </div>
                <div class="block-content">
                    <input type="hidden" id="actelion_giftid" name="actelion_giftid" value="">
                    <div class="form-group">
                        <label class="" for="title">礼品名称</label>
                        <div class="">
                            <input class="form-control" type="text" id="title" name="title" placeholder="请输入礼品名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="jifen_price">礼品价格</label>
                        <div class="">
                            <input class="form-control" type="text" id="jifen_price" name="jifen_price" placeholder="请输入礼品价格">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="">礼品图片</label>
                        <div class="">
                            <div id="showimg">
                            </div>
                            <div style="clear: both;"></div>
                            <div>
                                <input class="file-input" onchange="uploadimg(this)" id="input-uploadimg" type="file" name="imgurl"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="code">初始化库存</label>
                        <div class="">
                            <input class="form-control" type="text" id="init_cnt" name="init_cnt" placeholder="请输入初始化库存">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="code">备注</label>
                        <div class="">
                            <textarea class="form-control" id="remark" name="remark" rows="4" placeholder="备注"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary" type="button" id="submit-edit" data-dismiss="modal">
                    <i class="fa fa-check"></i>提交
                </button>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function(){
    $('#gift-edit').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        var type = button.data('type');
        if (type == 'modify') {
            $("#type-title").text('修改');

            var actelion_giftid = button.data('id');
            var title = button.data('title');
            var jifen_price = button.data('jifen_price');
            var init_cnt = button.data('init_cnt');
            var remark = button.data('remark');

            modal.find('#actelion_giftid').val(actelion_giftid);
            modal.find('#title').val(title);
            modal.find('#jifen_price').val(jifen_price);
            modal.find('#init_cnt').val(init_cnt);
            modal.find('#remark').val(remark);
        } else {
            $("#type-title").text('新建');
        }
    });

    $('#submit-edit').on('click', function () {
        var actelion_giftid = $('#actelion_giftid').val();
        var title = $('#title').val();
        var jifen_price = $('#jifen_price').val();
        var pictureid = $('#pictureid').val();
        var init_cnt = $('#init_cnt').val();
        var remark = $('#remark').val();

//        alert(title + " " + jifen_price + " " + init_cnt);
//        alert(pictureid);return false;

        if (title == '') {
            alert("礼品名称不能为空!");
            return false;
        }

        if (jifen_price == '') {
            alert("礼品价格不能为空!");
            return false;
        }

        if (actelion_giftid == '' && (pictureid == 0 || pictureid == '' || pictureid == undefined)) {
            alert("图片不能为空!");
            return false;
        }

        if (init_cnt == '' || init_cnt == 0) {
            alert("初始化库存不能为空!");
            return false;
        }

        var flag = 0;

        $.ajax({
            url: '/actelion_giftmgr/addormodifyjson',
            type: 'get',
            dataType: 'text',
            async: false,
            data: {
                actelion_giftid: actelion_giftid,
                title: title,
                jifen_price: jifen_price,
                pictureid: pictureid,
                init_cnt : init_cnt,
                remark : remark
            },
            "success": function (data) {
                if (data == 'add-success') {
                    alert("添加成功");
                    window.location.href = window.location.href;
                } else if (data == 'modify-success'){
                    alert("修改成功");
                    window.location.href = window.location.href;
                } else {
                    alert("未知错误");
                    flag = 1;
                }
            }
        });

        if (flag == 1) {
            return false;
        }
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
