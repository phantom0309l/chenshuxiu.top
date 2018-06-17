<?php
$pagetitle = "医生运营交互 Dwx_pipes";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/v3/js/amr/amrnb.js",
    $img_uri . "/v3/js/amr/amritem.js",
    $img_uri . "/v5/page/audit/dwx_pipemgr/list.js?v=2018042601",
]; //填写完整地址
$pageStyle = <<<STYLE
    .trOnSeleted {
        background-color: #e6e6fa !important;
    }

    .trOnMouseOver {
        background-color: #e6e6fa !important;
    }
    
    #main-container {
     background: #f5f5f5 !important;
    }
    
    .imgDiv {
        border:#ccc 1px solid;
        width:154px;
        height:158px;
        float:left;
        margin:0 2px 5px 0;
        padding:1px;
        text-align:center;
        line-height:150px;
    }
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<script text="javascript" src="<?= $img_uri ?>/jquery/jquery-file-upload.js"></script>
<script text="javascript">
    function uploadimg(obj) {
        if (obj.value.length > 0) {
            var pic_type = $(obj).data('pic_type');
            $.ajaxFileUpload({
                url: '/picture/uploadimagepost/?w=150&h=150&isCut=&type=LessonMaterial', //需要链接到服务器地址,w=缩略图宽,h=缩略图高
                secureuri: false,
                fileElementId: 'input-uploadimg_' + pic_type, //文件选择框的id属性
                dataType: 'json', //服务器返回的格式，可以是json
                success: function (data, status) {            //相当于java中try语句块的用法
                    console.log(data);
                    var reg = /\d+_\d+\./;
                    var image_url = data.thumb.replace(reg, "");
                    var newimgDiv = "<div class=\"img-container fx-opt-zoom-out imgDiv\">\n" +
                        "                <input type=\"hidden\" class=\"pictureid\" name=\"" + pic_type + "[]\" value=\"" + data.pictureid + "\">\n" +
                        "                <img class=\"img-responsive\" src=\"" + data.thumb + "\" alt=\"\">\n" +
                        "                <div class=\"img-options\">\n" +
                        "                    <div class=\"img-options-content\" style=\"margin-top: 60px;\">\n" +
                        "                        <a target='_blank' class=\"btn btn-sm btn-default\" href=\"" + image_url + "\"><i class=\"fa fa-pencil\"></i>原图</a>\n" +
                        "                        <a class=\"btn btn-sm btn-default delete-pic\" href=\"javascript:void(0)\"><i class=\"fa fa-times\"></i>删除</a>\n" +
                        "                    </div>\n" +
                        "                </div>\n" +
                        "            </div>";
                    $("#showimg_" + pic_type).append(newimgDiv);
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

<div class="col-md-12 contentShell">
    <section class="col-md-6 content-left">
        <div class="p20 bg-white">
            <div class="col-md-12">
                <div class="col-sm-6 col-xs-12 pull-right">
                    <form class="form-horizontal" action="/dwx_pipemgr/list" method="get">
                        <div class="form-group">
                            <div class="input-group">
                                <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                                <span class="input-group-btn" style="width: 1%; line-height: 35px;">
                                    <button type="submit" class="btn btn-primary">
                                        <span aria-hidden="true" class="glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div>
                <table class="table remove-margin">
                    <thead>
                    <tr>
                        <th>医生</th>
                        <th>新消息数量</th>
                        <th>最新消息时间</th>
                        <th>运营负责人</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($doctors as $a) {
                        $color_select = "";
                        if ($a->id == $click_doctorid) {
                            $color_select = "trOnSeleted";
                        }
                        ?>
                        <tr onmouseover="over(this)" onmouseout="out(this)" class="<?= $color_select ?>">
                            <td data-toggle="popover" data-placement="right" data-content="<?= $a->service_remark ?>"
                                data-original-title="<?= $a->hospital->name ?> <?= $a->department ?>"><?= $a->name ?></td>
                            <td>
                                <?= $a->getNewDoctorMsgCnt() ?>
                                <span id="shownew-<?= $a->id ?>" style="color: red">
                        		<?php if ($a->is_new_pipe == 1) { ?>
                                    new
                                <?php } ?>
                            	</span>
                            </td>
                            <td><?= $a->getLastTimeDwxPipe() ?></td>
                            <td><?= $a->yunyingauditor->name ?></td>
                            <td><a href="javascript:" data-doctorid="<?= $a->id ?>" data-doctorname="<?= $a->name ?>" id="doctorid-<?= $a->id ?>"
                                   class="showDwxPipeHtml">查看</a></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan=10>
                            <?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <?php $doctor = $doctors[0]; ?>
    <section class="col-md-6 content-right">

        <div class="block">
            <ul class="nav nav-tabs nav-tabs-alt" data-toggle="tabs">
                <li class="active">
                    <a href="#btabs-alt-static-home">文本</a>
                </li>
                <li class="">
                    <a href="#btabs-alt-static-profile">图片</a>
                </li>
            </ul>
            <div class="block-content tab-content">
                <div class="tab-pane active" id="btabs-alt-static-home">
                    <div class="bg-white" id="dwx_pipereply">
                        <div class="block">
                            <div class="block-content pt10" style="padding: 0px">
                                <form class="form-horizontal">
                                    <input type="hidden" id="select_doctorid" value="<?= $doctor->id ?>">
                                    <div class="form-group push-10">
                                        <div class="col-xs-8 mb10">
                                            <label class="push-5-r" for="content_repty"><?= $myauditor->name ?> 医助</label>
                                            <a class="btn btn-default btn-sm deleteNew" href="javascript:void(0);"
                                               data-doctorid="<?= $doctor->id ?>">去new</a>
                                        </div>
                                        <div class="col-xs-12 push-10">
                                            <textarea class="form-control" id="content_repty" name="content_repty" rows="5" placeholder="请输入内容"></textarea>
                                        </div>
                                        <div class="col-xs-12">回复给:<span style="color:red" id="select_doctor_name" class="J_doctor_name"><?= $doctor->name ?></span></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <a href="#" class="btn btn-sm btn-info" id="auditor_reply_to_doctor"><i class="fa fa-send push-5-r"></i>回复</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="btabs-alt-static-profile" style="margin-bottom: 20px;">
                    <div id="showimg_dwxpicmsg">
                    </div>
                    <div style="clear: both;"></div>
                    <div>
                        <input class="file-input" onchange="uploadimg(this)" data-pic_type="dwxpicmsg" type="file" id="input-uploadimg_dwxpicmsg" name="imgurl"/>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <div class="col-xs-12" style="padding-left: 0px;margin-bottom: 15px;">
                            <a href="#" class="btn btn-sm btn-info" id="JS_reply_pic"><i class="fa fa-send push-5-r"></i><span>回复</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white" id="pipeDwxPipeListHtml">
            <div class="block">
                <div class="block-header">
                    <h3 class="block-title">医生运营流</h3>
                </div>
                <div class="block-content" style="padding-left: 0px;">
                    <ul class="list list-timeline pull-t">
                    </ul>
                </div>
            </div>
            <div class="text-center" id="dwx_pipeshowMore" style="display: none">
                <a class="btn btn-default push-20 showDwxPipeMore">查看更多</a>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
    <div class="slides"></div>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>
<div id='answersheet' class="col-md-4 pull-right none">
    <div class="panel panel-primary">
        <div class="panel-heading" id="answersheet-title"></div>
        <div id='details' class="panel-body"></div>
    </div>
    <span id="answersheet-close">x</span>
</div>
<div id="goTop" class="none">Top</div>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<?php
$footerScript = <<<XXX
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
