<?php
// input
if (! isset($voiceInputName) || empty($voiceInputName)) {
    $voiceInputName = "voiceid";
}

$initVoiceId = 0;

?>
<script text="javascript" src="<?php echo $img_uri; ?>/jquery/jquery-file-upload.js"></script>
<script text="javascript">
    function uploadVoice(obj) {
        if (obj.value.length > 0) {
            $("#voiceloading").show();
            $.ajaxFileUpload({
                url: '/voice/upload', //需要链接到服务器地址,w=缩略图宽,h=缩略图高
                secureuri: false,
                fileElementId: 'voicefile', //文件选择框的id属性
                dataType: 'json', //服务器返回的格式，可以是json
                success: function(data, status) {            //相当于java中try语句块的用法
                    $("#voiceloading").text(data.msg);

                    document.getElementById("<?= $voiceInputName ?>").value = data.voiceid;

                },
                error: function(data, status, e) {            //相当于java中catch语句块的用法
                    alert(data);
                    alert(e);
                }
            });
        }
    } //end of uploadImg
</script>
<input type="hidden" id="<?= $voiceInputName; ?>" name="<?= $voiceInputName; ?>" value="<?= $initVoiceId; ?>" />
<div>
    <div style="float: left; display: none;" id="voiceloading">正在上传</div>
    <div style="clear: both;"></div>
    <div>
        <input class="file-input" onchange="uploadVoice(this);" type="file" id="voicefile" name="voicefile" formenctype="multipart/form-data"/>
    </div>
</div>
<div style="clear: both"></div>
<?php
$voiceInputName = "";
$initVoiceId = 0;
?>
