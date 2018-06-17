<?php
$pagetitle = "就诊须知配置";
$sideBarMini = true;
$pageStyle = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php";?>
    <div class="content-div">
        <section class="col-md-12">
            <div class="js-validation-bootstrap form-horizontal">
                <input type="hidden" name="doctorid" value="<?= $doctor->id ?>" />
                <div class="col-md-12 col-xs-12 remove-padding">

                <!--就诊须知-->
                <div class="block block block-bordered" id="other">
                    <div class="block-header bg-gray-lighter">
                        <h3 class="block-title">就诊须知</h3>
                    </div>
                    <div class="block-content">
                        <div class="col-md-12 col-xs-12 clearfloat">
                            <div class="is-treatment-notice-box">
                                <label class="col-xs-12 font-w400" for="val-status">
                                    报到患者发送报到须知
                                    <span class="text-white">*</span>
                                </label>
                                <div class="col-md-9">
                                    <label class="css-input css-radio css-radio-warning push-10-r">
                                        <input type="radio" name="is_treatment_notice" value="1" <?php if ($doctor->is_treatment_notice == 1) {?> checked="checked" <?php }?>>
                                        <span></span>
                                        是
                                    </label>
                                    <label class="css-input css-radio css-radio-warning">
                                        <input type="radio" name="is_treatment_notice" value="0" <?php if ($doctor->is_treatment_notice==0){?> checked="checked" <?php }?>>
                                        <span></span>
                                        否
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <div class="btn btn-sm btn-primary btn-minw save" data-doctorid=<?=$doctor->id?>>保存</div>
                                </div>
                            </div>
                        </div><!--end of clearfloat -->
                    </div>
                    <!--/end of block-content-->
                </div>
                <!--/end of block-->
            </div>
            </div>

        </section>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<SCRIPT
$(function(){
    $(".save").on("click",function(){
        var me = $(this);
        var doctorid = me.data('doctorid');
        var is_treatment_notice = $(".is-treatment-notice-box").find("input:checked").val();

        $.ajax({
            "type" : "get",
            "data" : {
                doctorid : doctorid,
                is_treatment_notice : is_treatment_notice,
            },
            "dataType" : "text",
            "url" : "/doctormgr/changgeIsTreatmentNoticeJson",
            "success" : function(data){
                if(data == "ok"){
                    alert("操作成功！")
                    var lessonid = "{$doctor->getTreatmentLesson()->id}";
                    var lesson_title = "{$doctor->getTreatmentLesson()->title}";
                    me.after('<p style="margin-top:10px">该医生绑定的就诊须知是：《<a href="/lessonmgr/modify?lessonid=' + lessonid + '">' + lesson_title + '</a>》</p>');
                }else if(data == "needlesson"){
                    if(true == confirm("需要去创建就诊须知课文！")){
                        window.location.href = "/lessonmgr/add?doctorid=" + doctorid + "&courseid=480802556";
                    }else {
                        window.location.href = window.location.href;
                    }
                }
            }
        });
    });
});
SCRIPT;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
