<?php
// input
if (! isset($pictureInputName) || empty($pictureInputName)) {
    $pictureInputName = "pictureid";
}

// 缩略图宽高
if ($picWidth < 1) {
    $picWidth = 100;
}
if ($picHeight < 1) {
    $picHeight = 100;
}
if (! isset($isCut)) {
    $isCut = 1;
}

// $picture变量是预览用途
$initPictureUrl = '';
$initPictureId = 0;
if (isset($picture) && $picture instanceof Picture) {

    if ($iphone == true) {
        $initPictureUrl = $picture->getSrc();
        $initPictureId = $picture->id;
    } else {
        $initPictureUrl = $picture->getSrc($picWidth, $picHeight, $isCut);
        $initPictureId = $picture->id;
    }
}

if (! isset($divBlackBgOpen) || empty($divBlackBgOpen)) {
    $divBlackBgOpen = false;
}
$uploadimgid = 'input-uploadimg' . rand(1, 100000);
$uploadimgfunc = 'uploadimg' . rand(1, 100000);
?>
<script text="javascript">

    var jquery_file_upload_url = "<?php echo $img_uri; ?>/jquery/jquery-file-upload.js";
    var JS_jquery_file_uploadNode  = $("#JS_jquery_file_upload");
    if(JS_jquery_file_uploadNode.length == 0 ){
        var oScript= document.createElement("script");
        oScript.type = "text/javascript";
        oScript.src = jquery_file_upload_url;
        oScript.id = "JS_jquery_file_upload";
        var oHead = document.getElementsByTagName('head')[0];
        oHead.appendChild(oScript);
    }

    function <?=$uploadimgfunc?>(obj) {
        var container = $(obj).parents(".CTR_picture");
        if (obj.value.length > 0) {
            container.find(".uploadimg-loading").show();
            $.ajaxFileUpload({
                url: '/picture/uploadimagepost/?w=<?= $picWidth; ?>&h=<?= $picHeight; ?>&isCut=<?= $isCut; ?>&objtype=<?= $objtype?>&objid=<?= $objid?>&type=<?= $objsubtype?>', //需要链接到服务器地址,w=缩略图宽,h=缩略图高
                secureuri: false,
                fileElementId: '<?=$uploadimgid?>', //文件选择框的id属性
                dataType: 'json', //服务器返回的格式，可以是json
                success: function(data, status) {            //相当于java中try语句块的用法
                    container.find(".uploadPic-imgName").attr("title", data.name).html('图片', data.pictureid) ;
                    container.find(".uploadimg-delete-img-div").show();
                    container.find(".uploadPic-uploadedImg").show().find('img').attr("src", data.thumb);
                    container.find(".uploadimg-loading").hide();

                    var pictureInputNode = container.find(".CTR_picturInput");
                    pictureInputNode.val(data.pictureid);

                    container.find(".uploadPic-deleteImg").click(function() {
                        container.find(".CTR_picturInput").val(0);
                        container.find(".uploadPic-uploadedImg").hide();
                        container.find(".uploadimg-delete-img-div").hide();
                    });
                },
                error: function(data, status, e) {            //相当于java中catch语句块的用法
                    $('#upload_status').html('上传失败');
                    alert(data);
                    alert(e);
                }
            });
        }
    } //end of uploadImg
</script>
<div class="CTR_picture">
<input type="hidden" id="<?= $pictureInputName; ?>" class="CTR_picturInput <?= $pictureInputName; ?>" name="<?= $pictureInputName; ?>" value="<?= $initPictureId; ?>" />
<div style="width:<?= $picWidth + 10; ?>px;">
    <div <?= $iphone ? 'class="changebg cbg2"' : '' ?> style="<?= $divBlackBgOpen ? 'background-color:#000;' : '' ?>border:#ccc 1px solid;width:<?= $picWidth + 4; ?>px;height:<?= $picHeight + 8; ?>px;float:left;margin:0 2px 5px 0;padding:1px;text-align:center;line-height:<?= $picHeight; ?>px;">
        <p style="<?= $initPictureUrl ? '' : 'display:none;'; ?>margin:0" id="uploadimg-thumbimage"  class="uploadPic-uploadedImg">
            <?php if ($iphone == true) { ?>
                <img width="<?=$picWidth ?>" height="<?=$picHeight ?>" src="<?= $initPictureUrl; ?>">
            <?php } else { ?>
                <img src="<?= $initPictureUrl; ?>">
            <?php } ?>
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
    <input class="file-input" onchange="<?php echo $uploadimgfunc . '(this)';?>" type="file" id="<?=$uploadimgid?>" name="imgurl" />
    </div>
</div>
<div style="clear: both"></div>
</div>
<?php
$picWidth = 0;
$picHeight = 0;
$pictureInputName = "";
$initPictureUrl = "";
$initPictureId = 0;
$picture = null;
?>
