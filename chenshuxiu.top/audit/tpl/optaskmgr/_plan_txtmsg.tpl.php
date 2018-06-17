<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/12/4
 * Time: 13:02
 */
?>
<style>
    .plantxtmsgbox th, .plantxtmsgbox td {
        text-align: center;
    }

    .plantxtmsgbox td {
        vertical-align: middle !important;
    }
</style>
<?php $plantxtmsgs = $optask->getPlanTxtMsgs(); ?>
<div class="J_planTxtMsgBox<?= $i ?>">
    <div class="p15 plantxtmsgbox">
        <table class="table table-bordered <?= !empty($plantxtmsgs) ? '' : 'hide' ?>">
            <thead class="bg-gray-lighter">
            <tr>
                <th style="width: 55px;">类型</th>
                <th style="width: 95px;">发送时间</th>
                <th>内容</th>
                <th>备注</th>
                <th style="width: 73px;">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($plantxtmsgs as $plantxtmsg) { ?>
                <tr>
                    <td><span class="label label-info"><?= $plantxtmsg->getTypesDescOfShort() ?></span></td>
                    <td>
                        <?php
                        if ($plantxtmsg->type == 1) {
                            echo $plantxtmsg->plan_send_time;
                        } else {
                            echo $plantxtmsg->pushmsg instanceof PushMsg ? $plantxtmsg->pushmsg->createtime : '未发送';
                        }
                        ?>
                    </td>
                    <td><?= $plantxtmsg->content ?></td>
                    <td><?= $plantxtmsg->remark ?></td>
                    <td>
                        <?php if ($plantxtmsg->pushmsgid != 0) {
                            echo "<a  class='text-success'>已发送</a>";
                        } else {
                            // 不是定时发送
                            if ($plantxtmsg->type != 1) { ?>
                                <a class="btn btn-primary btn-xs J_send" data-plantxtmsgid="<?= $plantxtmsg->id ?>">
                                    <i class="fa fa-send push-5-r"></i>发送</a>
                                <br>
                            <?php } ?>
                            <a class="btn btn-danger btn-xs J_delete" data-plantxtmsgid="<?= $plantxtmsg->id ?>">
                                <i class="fa fa-trash push-5-r"></i>删除</a>
                            <br>
                            <a class="btn btn-default btn-xs push-5-t J_edit"
                               data-objtype="<?= $plantxtmsg->objtype ?>"
                               data-objid="<?= $plantxtmsg->objid ?>"
                               data-patientid="<?= $plantxtmsg->patientid ?>"
                               data-plantxtmsgid="<?= $plantxtmsg->id ?>"
                               data-type="<?= $plantxtmsg->type ?>"
                               data-plansendtime="<?= $plantxtmsg->plan_send_time ?>"
                               data-content="<?= $plantxtmsg->content ?>"
                               data-remark="<?= $plantxtmsg->remark ?>"
                            ><i class="fa fa-edit push-5-r"></i>修改</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <div class="block block-bordered remove-margin">
            <div class="block-header bg-gray-lighter">
                <ul class="block-options">
                    <li>
                        <button type="button" class="J_add text-primary" style="color: #5c90d2; opacity: 1;">
                            <i class="fa fa-plus"></i> 新建定时消息
                        </button>
                    </li>
                </ul>
                <h3 class="block-title">定时消息</h3>
            </div>
            <div class="block-content">
                <div class="tc">
                    <p class="J_tip text-info push-20 <?= empty($plantxtmsgs) ? 'hide' : '' ?>">点击"新建定时消息"或点击"修改"</p>
                </div>
                <form class="J_plantxtmsg_form <?= empty($plantxtmsgs) ? '' : 'hide' ?>"
                      onsubmit="return planTxtMsgFormSubmit(this)">
                    <input type="hidden" name="objtype" value="OpTask">
                    <input type="hidden" name="objid" value="<?= $optask->id ?>">
                    <input type="hidden" name="patientid" value="<?= $optask->patientid ?>">
                    <input type="hidden" name="plantxtmsgid" value="">
                    <div class="form-group">
                        <label>发送类型</label>
                        <div class="col-xs-12 remove-padding">
                            <?php
                            $type = 1;
                            foreach (Plan_txtMsg::getTypes() as $key => $value) { ?>
                                <label class="css-input css-radio css-radio-info">
                                    <input type="radio" name="type"
                                           value="<?= $key ?>" <?= $key == $type ? 'checked' : '' ?>><span></span>
                                    <?= $value ?>
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>发送时间</label>
                        <input type="text" name="plansendtime"
                               class="form-control J_plan_send_time" <?= $type == 1 ? '' : 'disabled' ?>>
                    </div>
                    <div class="form-group">
                        <label>发送内容</label>
                        <textarea name="content" rows="5"
                                  class="form-control"
                                  placeholder="填写发送内容"></textarea>
                    </div>
                    <div class="form-group">
                        <label>运营备注(选填)</label>
                        <textarea name="remark" rows="5"
                                  class="form-control"
                                  placeholder="填写运营备注"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary fr" type="submit"><i class="fa fa-check push-5-r"></i>保存
                        </button>
                        <div class="clear"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        var dd = new Date();
        dd.setDate(dd.getDate() + 1);
        var tomorrow_time = dd.Format('YYYY-MM-DD') + ' 14:00:00';

        var el = $('.J_planTxtMsgBox<?= $i ?>');
        el.on('change', 'input:radio[name="type"]', function () {
            var value = $(this).val();
            var input = el.find('.J_plan_send_time');
            if (value == 1) {
                input.attr("disabled", false);
            } else {
                input.attr("disabled", true);
            }
        });

        var names = [
            'objtype',
            'objid',
            'patientid',
            'plantxtmsgid',
            'type',
            'plansendtime',
            'content',
            'remark',
        ];

        el.on('click', '.J_edit', function () {
            var me = $(this);
            var form = el.find('.J_plantxtmsg_form');
            form.removeClass('hide');
            el.find('.J_tip').hide();

            names.forEach(function (name) {
                var value = me.data(name);
                if (name == 'type') {
                    var radios = form.find("input[name='" + name + "']");
                    radios.each(function () {
                        if ($(this).val() == value) {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    })
                    if (value == 1) {
                        form.find("input[name='plansendtime']").attr("disabled", false);
                    } else {
                        form.find("input[name='plansendtime']").attr("disabled", true);
                    }
                } else {
                    form.find("input[name='" + name + "'], textarea[name='" + name + "']").val(value);
                }
            })
        });

        el.on('click', '.J_add', function () {
            var me = $(this);
            var form = el.find('.J_plantxtmsg_form');
            form.removeClass('hide');
            el.find('.J_tip').hide();

            form.find("input[name='plantxtmsgid']").val('');
            var radios = form.find("input[name='type']");
            radios.each(function () {
                if ($(this).val() == 1) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            })
            form.find("input[name='plansendtime']").attr("disabled", false);
            form.find("input[name='plansendtime']").val(tomorrow_time);
            form.find("textarea[name='content']").val('');
            form.find("textarea[name='remark']").val('');
        });

        el.on('click', '.J_delete', function () {
            if (!confirm('确定删除吗？')) {
                return false;
            }
            var me = $(this);
            var plantxtmsgid = $(this).data('plantxtmsgid');

            $.ajax({
                "type": "post",
                "data": {plantxtmsgid: plantxtmsgid},
                "dataType": "json",
                "url": "/optaskmgr/ajaxPlanTxtMsgDeletePost",
                "success": function (data) {
                    alert(data.errmsg);
                    if (data.errno == 0) {
                        current_showOptask.click();
                    }
                }
            });
        });

        el.on('click', '.J_send', function () {
            var plantxtmsgid = $(this).data('plantxtmsgid');

            $.ajax({
                "type": "post",
                "data": {plantxtmsgid: plantxtmsgid},
                "dataType": "json",
                "url": "/optaskmgr/ajaxPlanTxtMsgSendPost",
                "success": function (data) {
                    alert(data.errmsg);
                    if (data.errno == 0) {
                        current_showOptask.click();
                    }
                }
            });
        });

        <?php if ($plantxtmsg instanceof Plan_txtMsg && $plantxtmsg->plan_send_time != '0000-00-00 00:00:00') { ?>
        var datetime = "<?= $plantxtmsg->plan_send_time ?>";
        <?php } else { ?>
        var datetime = tomorrow_time;
        <?php } ?>
        el.find('.J_plan_send_time').val(datetime);
        laydate.render({
            elem: '.J_planTxtMsgBox<?= $i ?> .J_plan_send_time',
            type: 'datetime'
        });
    })
</script>