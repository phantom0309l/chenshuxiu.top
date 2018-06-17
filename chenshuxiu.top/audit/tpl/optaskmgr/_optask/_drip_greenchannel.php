<div class="optaskOneShell">
    <?php
    $drip_greenchannel = $optask->obj;
    if ($drip_greenchannel instanceof Drip_greenChannel) { ?>
        <div class="optaskContent">
            <table class="green_channel mb15">
                <tbody>
                <tr>
                    <th>疾病</th>
                    <td><?= $drip_greenchannel->diseasestr ?></td>
                </tr>
                <tr>
                    <th>城市</th>
                    <td><?= $drip_greenchannel->xcity->name ?></td>
                </tr>
<!--                <tr>-->
<!--                    <th>医院</th>-->
<!--                    <td>--><?//= $drip_greenchannel->hospital->name ?><!--</td>-->
<!--                </tr>-->
                <tr>
                    <th>期望日期</th>
                    <td><?= $drip_greenchannel->expecteddate ?> 至 <?= $drip_greenchannel->bounddate ?></td>
                </tr>
                <?php if ($drip_greenchannel->status == 2) { ?>
                    <tr>
                        <th>实际日期</th>
                        <td><?= $drip_greenchannel->actualdate ?></td>
                    </tr>
                    <tr>
                        <th>发送内容</th>
                        <td><?= $drip_greenchannel->content ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php if ($drip_greenchannel->status == 1) { ?>
                <div class="pt15 border-t">
                    <form class="J_greenchannel_form">
                        <input type="hidden" name="patientid" value="<?= $optask->patientid ?>"/>
                        <input type="hidden" name="drip_greenchannelid" value="<?= $drip_greenchannel->id ?>"/>
                        <div class="form-group">
                            <label for="example-nf-email">实际日期</label>
                            <input class="form-control calendar" type="date" name="actualdate" placeholder="请选择实际就诊日期">
                        </div>
                        <div class="form-group">
                            <label for="example-nf-password">发送给患者的内容</label>
                            <textarea class="form-control" name="content" placeholder="请填写发送给患者的内容" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-primary J_greenchannel_btn"
                                    data-patientid="<?= $optask->patientid ?>"
                                    type="button">保存并发送
                            </button>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<script>
    $(function () {
        $('.J_greenchannel_btn').off('click').on('click', function () {
            var me = $(this);
            var patientid = me.data('patientid');

            me.prop('disabled', true);
            $.ajax({
                type: "post",
                data: $('.J_greenchannel_form').serialize(),
                url: "/drip_greenchannelmgr/confirmpostjson",
                dataType: 'json',
                complete: function () {
                    me.prop('disabled', false);
                },
                success: function (response) {
                    if (response.errno === '0') {
                        $(".patientid-" + patientid).click();
                        alert('发送成功');
                    } else {
                        alert(response.errmsg);
                    }
                },
                error: function () {
                    alert('发送失败');
                }
            });
        })
    })
</script>