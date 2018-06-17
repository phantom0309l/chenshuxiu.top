<div class="block replySection collapse" style="border: 1px solid #e9e9e9">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="javascript:">文本</a>
        </li>
        <li>
            <a href="javascript:">问诊量表</a>
        </li>
        <li>
            <a href="javascript:">图片</a>
        </li>
        <li>
            <a href="javascript:">文章</a>
        </li>
    </ul>
    <div class="block-content tab-content">
        <div class="tab-pane replyBox remove-margin-t active" id="btabs-alt-static-home">
            <div class="clearfix">
                <div class="col-md-6 remove-padding">
                    <?php
                    $diseaseGroup = $a->patient->getDiseaseGroup();
                    $dealwithTpls = [];
                    echo HtmlCtr::getSelectCtrImp(DealwithTplService::getCtrArrayForPatient($diseaseGroup->id), "dealwith_group", "noselect",
                        "dealwith_group form-control js-select2", 'width: 100%;');
                    ?>
                </div>
                <div class="col-md-6 remove-padding-r">
                    <select class="handleSelect dealwithTplSelect form-control js-select2 clear" style="width: 100%;">
                        <?php if (count($dealwithTpls) > 0) { ?>
                            <option value="">请选择....</option>
                        <?php } ?>

                        <?php foreach ($dealwithTpls as $c) { ?>
                            <option value="<?= $c->id ?>" data-msgcontent="<?= $c->msgcontent ?>"><?= $c->title ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="push-10-t">
                <textarea name="reply-msg" class="reply-msg speech-input" rows="8" lang="zh-cn" data-ready="开始..."></textarea>
            </div>
            <p>
                <a href="#" class="btn btn-default reply-btn" data-patientid="<?= $a->patientid ?>" data-type="TxtMsg">回复</a>
                <span class="text-danger reply-notice push-10-l"></span>
            </p>
        </div>
        <div class="tab-pane replyBox remove-margin-t" id="btabs-alt-static-profile">
            <div class="col-md-6 remove-padding">
                <?php
                $diseaseGroup = $a->patient->getDiseaseGroup();
                $dealwithTpls = [];
                echo HtmlCtr::getSelectCtrImp(DealwithTplService::getCtrArrayForPatient($diseaseGroup->id), "dealwith_group", "noselect",
                    "dealwith_group js-select2 form-control", 'width: 100%;');
                ?>
            </div>
            <div class="col-md-6 remove-padding-r">
                <select class="handleSelect dealwithTplSelect js-select2 form-control clear" style="width: 100%;">
                    <?php if (count($dealwithTpls) > 0) { ?>
                        <option value="">请选择....</option>
                    <?php } ?>

                    <?php foreach ($dealwithTpls as $c) { ?>
                        <option value="<?= $c->id ?>" data-msgcontent="<?= $c->msgcontent ?>"><?= $c->title ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-6 remove-padding push-10-t">
                <select class="wenzhen form-control">
                    <option value="">请选择....</option>
                    <?php
                    foreach ($papertpl_arr as $k => $v) {
                        ?>
                        <option value="<?= $v["url"] ?>"><?= $v["title"] ?></option>
                    <?php } ?>
                </select>
            </div>
            <textarea name="reply-msg" class="reply-msg push-10-t" cols="50" rows="8"></textarea>
            <p>
                <a href="#" class="btn btn-default reply-btn" data-patientid="<?= $a->patientid ?>" data-type="Wenzhen">回复</a>
                <span class="text-danger reply-notice push-10-l"></span>
            </p>
        </div>
        <div class="tab-pane replyBox remove-margin-t" id="btabs-alt-static-settings">
            <?php include("$dtpl/picture.ctr.php"); ?>
            <p style="margin-top: 10px;">
                <a href="#" class="btn btn-default reply-btn" data-patientid="<?= $a->patientid ?>" data-type="Pic">回复</a>
                <span class="text-danger reply-notice push-10-l"></span>
            </p>
        </div>
        <div class="tab-pane replyBox remove-margin-t" id="btabs-alt-static-settings">
            <div class="col-md-6 remove-padding">
                <select class="courseSelect form-control">
                    <option value="">请选择课程...</option>
                    <?php
                    foreach ($courses as $c) {
                        ?>
                        <option value="<?= $c->id ?>"><?= $c->title ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-6 remove-padding-r">
                <div class="lessonSelectShell"></div>
            </div>
            <textarea name="reply-msg" class="reply-msg push-10-t" cols="50" rows="8"></textarea>
            <p>
                <a href="#" class="btn btn-default reply-btn" data-patientid="<?= $a->patientid ?>" data-type="Article">回复</a>
                <span class="text-danger reply-notice push-10-l"></span>
            </p>
        </div>
    </div>
</div>
</div>
<div class="btnSection btn-group" style="width: 100%; position: absolute; bottom: 0">
    <?php $widthClass = "col-lg-6";
    $pipelevel = PipeLevelDao::getHasHandledByPipeid($a->id);
    if ($pipelevel instanceof PipeLevel) {
        $widthClass = "col-lg-4";
    }
    ?>
    <span class="btn btn-default reply-triggerBtn <?= $widthClass ?>" style="border-left: 0; border-bottom: 0;">
        <i class="si si-action-redo"></i>
        回复
    </span>
    <?php if ($a->canJoinLetter()) {
        $letter = LetterDao::getOneByObj($a->obj);
        ?>
        <?php if ($letter instanceof Letter) { ?>
            <button class="btn btn-success <?= $widthClass ?>" style="border-right: 0; border-bottom: 0;">
                <i class="si si-like"></i>
                已添加感谢留言
            </button>
        <?php } else { ?>
            <button class="btn btn-default thankQuick <?= $widthClass ?>" data-toggle="modal" data-target="#thankBox"
                    style="border-right: 0; border-bottom: 0;">
                <i class="si si-like"></i>
                添加到感谢留言
            </button>
        <?php } ?>
        <?php if ($pipelevel instanceof PipeLevel) {
            $is_urgent = $pipelevel->is_urgent; ?>
            <button class="btn <?= 2 == $is_urgent ? 'btn-warning' : 'btn-default' ?> pipelevelFixBtn <?= $widthClass ?>" data-toggle="modal"
                    data-target="#pipelevelFixBox" data-pipelevelid="<?= $pipelevel->id ?>" data-isurgent="<?= $is_urgent ?>"
                    style="border-right: 0; border-bottom: 0;">
                <i class="si si-wrench"></i>
                <?= 2 == $is_urgent ? '紧急' : '不紧急' ?>
            </button>
        <?php } ?>
    <?php } elseif ($a->canSendOcr()) { ?>
        <button class="btn btn-default ocr-btn <?= $widthClass ?>"
                data-url="<?= str_replace('fangcunhulian.cn', 'fangcunyisheng.com', $a->obj->getImgUrl()) ?>"
                data-pic-id="<?= $a->obj->patientpictureid ?>" data-toggle="modal" data-target="#picture-ocr"
                style="border-right: 0; border-bottom: 0;">
            图片识别
        </button>
    <?php } elseif ($a->objtype == 'CdrMeeting') { ?>
        <button class="btn btn-default cdr-btn <?= $widthClass ?>"
                data-cdr-id="<?= $a->obj->id ?>" data-toggle="modal"
                style="border-right: 0; border-bottom: 0;"
                <?php if(!$a->obj->isCallOk()) {echo 'disabled="disabled"';} ?> >
            语音识别
        </button>
    <?php } else { ?>
        <button class="btn btn-default <?= $widthClass ?>" disabled style="border-right: 0; border-bottom: 0;">nothing</button>
    <?php } ?>
</div>
<script>
    //重要！！这是一个全局都可以生效的tabclick
    $(document).on("click", ".nav-tabs>li", function () {
        var me = $(this);
        var index = me.index();
        var tab = me.parent().parent();
        var contents = tab.children(".tab-content").children(".tab-pane");
        me.addClass("active").siblings().removeClass("active");
        contents.eq(index).show().siblings().hide();
    });
</script>
