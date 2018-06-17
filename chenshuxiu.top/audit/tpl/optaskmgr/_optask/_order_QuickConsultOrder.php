<style>
    .tab-content .form-group label {
        width: auto;
    }
</style>

<div class="optaskOneShell">
    <?php
    $quickconsultorder = $optask->obj;
    if ($quickconsultorder instanceof QuickConsultOrder) { ?>
        <div class="optaskContent">
            <h5>
                快速咨询
            </h5>
            <?php
            if ($quickconsultorder->status == 4) { ?>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">完成前请选择咨询方式</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="interactive_mode" name="interactive_mode">
                                <option value="">请选择</option>
                                <?php
                                foreach ($quickconsultorder->getInteractiveModes() as $key => $value) { ?>
                                    <option <?= $quickconsultorder->interactive_mode == $key ? 'selected' : '' ?>
                                            value="<?= $key ?>">
                                        <?= $value ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <button class="btn btn-sm btn-success col-sm-1" id="interactive_mode_btn" style="width: 46px"
                                data-quickconsultorderid="<?= $quickconsultorder->id ?>">保存
                        </button>
                    </div>
                </div>
            <?php }
            ?>
            <div class="push-10-t pb10 border-b">
                <?= '<span class="label label-primary">' . $quickconsultorder->getStatusStr() . '</span>&nbsp;' ?>
                <?php
                $is_timeout = $quickconsultorder->isTimeout() ? '已超时' : '';
                echo "<span class='label label-danger'>{$is_timeout}</span> &nbsp;";

                $is_refund = $quickconsultorder->is_refund == 1 ? '已退款' : '';
                echo "<span class='label label-warning'>{$is_refund}</span> &nbsp;";
                ?>
            </div>
            <p class="push-10-t">
                <?= $quickconsultorder->content; ?>
            </p>
            <ul class="quickconsultorder_picbox remove-padding push-10-t" style="list-style: none;">
                <?php
                $basicpictures = $quickconsultorder->getBasicPictures();
                foreach ($basicpictures as $basicpicture) { ?>
                    <li class="push-10-r push-10 fl">
                        <img style="width: 150px; height: 150px;" class="img-responsive viewer-toggle"
                             data-url="<?= $basicpicture->picture->getSrc(); ?>"
                             src="<?= $basicpicture->picture->getSrc(150, 150, true) ?>"/>
                    </li>
                <?php } ?>
                <div class="clear"></div>
            </ul>
        </div>
    <?php } ?>
</div>
<script>
    $(function () {
        $('.quickconsultorder_picbox').viewer({
            inline: false,
            url: 'data-url',
            class: 'viewer-toggle',
            navbar: false,
            scalable: false,
            fullscreen: false,
            shown: function (e) {
            }
        })
        $('.quickconsultorder_picbox').viewer('update');

        $('#interactive_mode_btn').on('click', function () {
            var quickconsultorderid = $(this).data('quickconsultorderid');
            var interactive_mode = $('#interactive_mode').val();
            if (interactive_mode === '' || interactive_mode === null || interactive_mode === undefined) {
                alert('请选择交流方式');
                return false;
            }
            $.ajax({
                type: "post",
                url: "/quickconsultordermgr/ajaxModifyInteractiveMode",
                data: {
                    'interactive_mode': interactive_mode,
                    'quickconsultorderid': quickconsultorderid,
                },
                dataType: "json",
                success: function (response) {
                    if (response.errno == "0") {
                        alert('保存成功');
                    } else {
                        alert('保存失败');
                    }
                }
            });
        });
    })
</script>