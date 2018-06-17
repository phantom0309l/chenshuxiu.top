<?php
$pagetitle = "上传图片";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="margin: 10px auto 20px auto;">
            <span>上传单张图片</span>
            <form>
                <script text="javascript" src="<?= $img_uri ?>/jquery/jquery-file-upload.js"></script>
                <script text="javascript">
                    function uploadimg(obj) {
                        var container = $(obj).parent().parent();
                        if (obj.value.length > 0) {
                            container.find(".uploadimg-loading").show();
                            $.ajaxFileUpload({
                                url: '/picture/uploadimagepost/?w=150&h=150&isCut=&type=LessonMaterial', //需要链接到服务器地址,w=缩略图宽,h=缩略图高
                                secureuri: false,
                                fileElementId: 'input-uploadimg', //文件选择框的id属性
                                dataType: 'json', //服务器返回的格式，可以是json
                                success: function (data, status) {            //相当于java中try语句块的用法
                                    container.find(".uploadPic-imgName").attr("title", data.name).html('图片', data.pictureid);
                                    container.find(".uploadimg-delete-img-div").show();
                                    container.find(".uploadPic-uploadedImg").show().find('img').attr("src", data.thumb);
                                    container.find(".uploadimg-loading").hide();
                                    var picname = 'pictureid';
                                    container.siblings("." + picname).val(data.pictureid);

                                    container.find(".uploadPic-deleteImg").click(function () {
                                        container.siblings("." + picname).val(0);
                                        container.find(".uploadPic-uploadedImg").hide();
                                        container.find(".uploadimg-delete-img-div").hide();
                                    });
                                    var reg = /\d+_\d+\./;
                                    var image_url = data.thumb.replace(reg, "");
                                    var str = '<img src="' + image_url + '" style="width:100%"/>';
                                    $("#urlBox").text(str);
                                },
                                error: function (data, status, e) {            //相当于java中catch语句块的用法
                                    $('#upload_status').html('上传失败');
                                    alert(data);
                                    alert(e);
                                }
                            });
                        }
                    } //end of uploadImg
                </script>
                <input type="hidden" id="pictureid" class="pictureid" name="pictureid" value="0"/>
                <div style="width:160px;">
                    <div style="border:#ccc 1px solid;width:154px;height:158px;float:left;margin:0 2px 5px 0;padding:1px;text-align:center;line-height:150px;">
                        <p style="display:none;margin:0" id="uploadimg-thumbimage" class="uploadPic-uploadedImg">
                            <img src="">
                        </p>
                    </div>
                    <div style="float: left; display: none;" class="uploadimg-delete-img-div">
                        <span title="" class="uploadPic-imgName"></span>
                        &nbsp;&nbsp;
                        <a title="删除这张图片" href="javascript:" class="js-link uploadPic-deleteImg delete-trigger">删除</a>
                    </div>
                    <div style="float: left; display: none;" class="uploadimg-loading">正在上传</div>
                    <div style="clear: both;"></div>
                    <div>
                        <input class="file-input" onchange="uploadimg(this)" type="file" id="input-uploadimg"
                               name="imgurl"/>
                    </div>
                </div>
                <div style="clear: both"></div>
                <p id="urlBox"></p>
            </form>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
