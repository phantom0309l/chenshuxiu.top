<div class="block replySection push-10-t collapse" style="border:1px solid #e9e9e9">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="javascript:">文本</a>
        </li>
        <li>
            <a href="javascript:">图片</a>
        </li>
    </ul>
    <div class="block-content tab-content" style="padding-top: 12px;">
        <div class="tab-pane replyBox remove-margin-t active">
            <div class="col-md-6 remove-padding">
                <?php
                $diseaseGroup = null;

                if ($a->patient instanceof Patient) {
                    $diseaseGroup = $a->patient->getDiseaseGroup();
                } else {
                    $diseaseGroup = $a->wxuser->wxshop->disease->diseasegroup;
                }

                $dealwithTpls = [];
                echo HtmlCtr::getSelectCtrImp(DealwithTplService::getCtrArrayForPatient($diseaseGroup->id), "dealwith_group", "noselect",
                    "dealwith_group form-control js-select2", 'width: 100%;');
                ?>
            </div>
            <div class="col-md-6 remove-padding-r">
                <select class="handleSelect dealwithTplSelect clear form-control js-select2" style="width: 100%">
                    <?php if (count($dealwithTpls) > 0) { ?>
                        <option value="">请选择....</option>
                    <?php } ?>

                    <?php foreach ($dealwithTpls as $c) { ?>
                        <option value="<?= $c->id ?>" data-msgcontent="<?= $c->msgcontent ?>"><?= $c->title ?></option>
                    <?php } ?>
                </select>
            </div>
            <div style="clear: both"></div>
            <textarea name="reply-msg" class="reply-msg push-10-t" rows="8"></textarea>
            <p>
                <a href="#" class="btn btn-default reply-btn" data-wxuserid="<?= $a->wxuserid ?>" data-type="TxtMsg">回复</a>
            </p>
        </div>
        <div class="tab-content-item none">
            <?php include("$dtpl/picture.ctr.php"); ?>
            <p style="margin-top: 10px;">
                <a href="#" class="btn btn-default reply-btn" data-wxuserid="<?= $a->wxuserid ?>" data-type="Pic">回复</a>
            </p>
        </div>
    </div>
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
