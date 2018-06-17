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
?>
<script text="javascript" src="<?php echo $img_uri; ?>/jquery/jquery-file-upload.js"></script>
<script text="javascript">
    function uploadImg(obj) {
        if (obj.value.length > 0) {
            $("#loading").show();
            $.ajaxFileUpload({
                url: '/picture/uploadimagepost/?w=<?= $picWidth; ?>&h=<?= $picHeight; ?>&isCut=<?= $isCut; ?>&objtype=<?= $objtype?>&objid=<?= $objid?>&type=<?= $objsubtype?>', //需要链接到服务器地址,w=缩略图宽,h=缩略图高
                secureuri: false,
                fileElementId: 'uploadimg', //文件选择框的id属性
                dataType: 'json', //服务器返回的格式，可以是json
                success: function(data, status) {            //相当于java中try语句块的用法
                    $(".uploadPic-imgName").attr("title", data.name);
                    $(".uploadPic-imgName").html('图片' + data.pictureid);
                    $("#delete-img-div").show();
                    $(".uploadPic-uploadedImg img").attr("src", data.thumb);
                    $("#loading").hide();

                    $("#thumbimage").show();
                    document.getElementById("<?= $pictureInputName ?>").value = data.pictureid;

                    $(".uploadPic-deleteImg").click(function() {
                        $("#<?= $pictureInputName; ?>").val(0);
                        $("#thumbimage").hide();
                        $("#delete-img-div").hide();
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
<input type="hidden" id="<?= $pictureInputName; ?>" name="<?= $pictureInputName; ?>" value="<?= $initPictureId; ?>" />
<div style="width:<?= $picWidth + 10; ?>px;border: none;float: left">
    <div <?= $iphone ? 'class="changebg cbg2"' : '' ?> style="<?= $divBlackBgOpen ? 'background-color:#000;' : '' ?>width:<?= $picWidth + 4; ?>px;height:<?= $picHeight + 8; ?>px;float:left;margin:0 2px 5px 0;padding:1px;text-align:center;line-height:<?= $picHeight; ?>px;">
        <p style="<?= $initPictureUrl ? '' : 'display:none;'; ?>margin:0" id="thumbimage"  class="uploadPic-uploadedImg">
            <?php if ($iphone == true) { ?>
                <img width="<?=$picWidth ?>" height="<?=$picHeight ?>" src="<?= $initPictureUrl; ?>" style="border: none">
            <?php } else { ?>
                <img src="<?= $initPictureUrl; ?>" style="border: none">
            <?php } ?>
        </p>
    </div>
    <div style="clear: both;"></div>
</div>
<div style="width: 100px; float: right; background: #f66; position: relative; height: 33px; display: inline-block; color: white; line-height: 33px; text-align: center; border-radius: 21px;">
    选择文件
    <input class="file-input" onchange="uploadImg(this);" type="file" id="uploadimg" name="imgurl" style="opacity: 0; width: 100px; position: absolute; top: 0px; right: 0px;" />
</div>
<div style="float: right; color: #f66; display: none;" id="loading">正在上传</div>
<div style="clear: both"></div>
<?php
$picWidth = 0;
$picHeight = 0;
$pictureInputName = "";
$initPictureUrl = "";
$initPictureId = 0;
$picture = null;
?>
