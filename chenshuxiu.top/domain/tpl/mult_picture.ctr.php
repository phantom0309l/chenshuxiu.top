<?php
// input
if (!isset($pictureInputNames) || empty($pictureInputNames)) {
    $pictureInputNames = "pictureids";
}

// 缩略图宽高
if ($picWidth < 1) {
    $picWidth = 100;
}
if ($picHeight < 1) {
    $picHeight = 100;
}
if (!isset($isCut)) {
    $isCut = 1;
}
if (!isset($maxImgLen)) {
    $maxImgLen = 0;
}
if (!isset($uploaderid)) {
    $uploaderid = 'addLeftImg';
}
$uploadifyid = "J_" . $uploaderid;

?>
<link rel="stylesheet" href="<?= $img_uri; ?>/m/css/uploadify.css">
<script src="<?= $img_uri; ?>/m/js/jquery.uploadify.min.js"></script>
<script text="javascript">
    var maxImgLen = <?= $maxImgLen ?>;
    <?php if(!$multi_upload_namespace){
        $multi_upload_namespace = "mult_upload";
    }?>
    var <?php echo $multi_upload_namespace;?> = <?php echo $multi_upload_namespace;?> || {};
    $(function () {
    <?=$multi_upload_namespace?>.initSWFJs();
    });
    <?=$multi_upload_namespace?>.initSWFJs = function() {
//    var fls= flashChecker();
//    if(!fls.f){
//        if(confirm("您的浏览器不支持Flash,无法进行图片上传,是否安装?"))
//        {
//            window.open("http://get2.adobe.com/cn/flashplayer/");
//        }
//        haveFlash = false;
//        return;
//    }
//    if(fls.v < 9){
//        alert("您的浏览器Flash Player版本太低,无法进行图片上传操作,请更新Adobe Flash Player.");
//        haveFlash = false;
//        return;
//    }

        var queueNum = 0;
        //$('.J_uploadify').uploadify({
        $('#<?=$uploadifyid?>').uploadify({
            'fileObjName': 'imgurl',
            'auto': true,
            'multi': true,
            'removeCompleted': true,
            'uploadLimit': maxImgLen,
            'buttonClass': 'uploadifyButtonImg',
            'fileSizeLimit': '5000KB',
            'fileTypeExts': '*.jpg;*.jpeg;*.gif;*.png',
            'buttonImage': '<?= $img_uri ?>/m/img/add.jpg',
//            'buttonText': '开始上传',
            'width': 66,
            'height': 44,
            'swf': '/uploadify/uploadify.swf',
            'uploader': '/picture/uploadimagepost/?objid=<?= $objid ?>&objtype=<?= $objtype ?>&w=<?= $picWidth; ?>&h=<?= $picHeight; ?>&isCut=<?= $isCut; ?>',
            'overrideEvents': ['onSelectError', 'onDialogClose'],
            'onSelectError': function (file, errorCode, errorMsg) {
                switch (errorCode) {
                    case -100:
                        alert("最多上传 " + maxImgLen + " 图片");
                        break;
                    case -110:
                        alert("图片 [" + file.name + "] 大小超出系统限制的" + $('#file_upload').uploadify('settings', 'fileSizeLimit') + "大小！");
                        break;
                }
                return false;
            },
            'onDialogClose': function (queueData) {
            },
            'onUploadStart': function (file) {
            },
            'onUploadSuccess': function (file, data, response) {
                <?=$multi_upload_namespace?>.addImage(data);
            },
            'onClearQueue': function (queueItemCount) {
            }
        });
    }
<?=$multi_upload_namespace?>.checkCanAddImage = function() {
            var imgLen = $('.multipicture ul li .setting_thumbimg').length;
            if (imgLen == maxImgLen && maxImgLen != 0) {
                $('#<?= $uploaderid ?>').hide();
            } else {
                $('em', '#<?= $uploaderid ?>').html(maxImgLen - imgLen);
                if ($('#<?= $uploaderid ?>').is(":hidden")) {
                    $('#<?= $uploaderid ?>').show();
                }
            }
        }
<?=$multi_upload_namespace?>.addImage = function(data) {
        data = eval('(' + data + ')');
        var imgSrc = data.thumb;
        var imgId = data.pictureid;
        var addStr = '<li class="li_pic" id="del_' + imgId + '"><input type="hidden" name="<?=$pictureInputNames ?>[]" value="' + imgId + '"><p class="setting_thumbimg" style="margin-bottom: 0;"><img path="' + imgId + '" src="' + imgSrc + '" /> </p> <p class="setting_title" style="margin-bottom: 0;"><span>' + data.name + '</span> <input type="text" name="multiImageTitle[]" ></p><a onclick="<?=$multi_upload_namespace?>.removeImage(\'del_' + imgId + '\');" ><img src="<?= $img_uri ?>/m/img/close.jpg" width="18" height="18" /></a></li>';
        $('#<?= $uploaderid ?>').before(addStr);
        $('#file_upload-queue').empty();
        <?=$multi_upload_namespace?>.checkCanAddImage();
    }

        <?=$multi_upload_namespace?>.removeImage = function(id) {
        $('#' + id).remove();
        <?=$multi_upload_namespace?>.checkCanAddImage();
    }
</script>
<div class="multipicture" style="width: auto;">
    <ul>
        <li class="li_last" id="<?=$uploaderid?>">
            <div class="last_img uploadifyImg">
                <div id="<?=$uploadifyid?>" class="uploadify J_uploadify"></div>
                <div class="addImg">点击添加图片</div>
            </div>
        </li>
        <div class="clear"></div>
    </ul>
</div>
<div style="clear: both"></div>
<?php
$picWidth = 0;
$picHeight = 0;
$pictureInputNames = "";
$initPictureUrl = "";
$initPictureId = 0;
$picture = null;
$objid = 0;
$objtype = "";
$maxImgLen = 0;
$uploaderid = "";
?>
