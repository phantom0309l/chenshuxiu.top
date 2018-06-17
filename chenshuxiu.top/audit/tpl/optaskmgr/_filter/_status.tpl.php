<?php
$status = empty($configs['status']) ? [-1] : $configs['status'];
$statusstr = $configs['showstr']['statusstr'] ?? ['进行中'];

$status_input = implode(',', $status);
$statusstr_input = implode(',', $statusstr);
?>
<div class="form-group optaskfilter-list" data-key="status">
    <input type="hidden" id="status" name="status" value="<?=$status_input?>">
    <input type="hidden" id="statusstr" name="statusstr" value="<?=$statusstr_input?>">
    <label class="col-xs-3 control-label" for="statuss">任务状态</label>
    <div class="col-xs-9">
        <?php
         echo HtmlCtr::getCheckboxCtrImp4OneUi(CtrHelper::toOptaskStatuCtrArrayForFilter(), 'statuss', $status, '');
        ?>
    </div>
    <script>
        $(function () {
            $('input[name="statuss"]').on('click', function () {
                var me = $(this);
                var click_val = me.val();

                var statuss = [];
                var statusstr = [];
                if (click_val == -2) {
                    var checked_cnt = 0;
                    $('input[name="statuss"]').each(function () {
                        var m = $(this);
                        if (m.val() != -2) {
                            statuss.push(m.val());
                            statusstr.push(m.data('value'));
                            m.prop('checked', false);
                        }

                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                        }
                    });
                    if (checked_cnt == 0) {
                        $('input[name="statuss"]').each(function () {
                            var m = $(this);
                            if (m.val() != -2) {
                                m.prop('checked', false);
                            } else {
                                m.prop('checked', true);
                            }
                        });
                    }
                } else {
                    var checkbox_cnt = 0;
                    var checked_cnt = 0;
                    $('input[name="statuss"]').each(function () {
                        var m = $(this);
                        checkbox_cnt ++;
                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                            if (m.val() != -2) {
                                statuss.push(m.val());
                                statusstr.push(m.data('value'));
                            }
                        }

                        if (m.val() == -2) {
                            if (m.prop('checked') == true) {
                                m.prop('checked', false);
                                checked_cnt --;
                            }
                        }
                    });
                    if (checkbox_cnt - checked_cnt == 1 || checked_cnt == 0) {
                        $('input[name="statuss"]').each(function () {
                            var m = $(this);
                            if (m.val() != -2) {
                                m.prop('checked', false);
                            } else {
                                m.prop('checked', true);
                            }
                        });
                    }
                }

                var status = statuss.join(',');
                var statusstr = statusstr.join(',');

                $('input[name="statuss"]').each(function () {
                    var m = $(this);
                    if (m.val() == -2 && m.prop('checked') == true) {
                        status = '-2';
                        statusstr = '全部';
                    }
                });

                $('#status').val(status);
                $('#statusstr').val(statusstr);
            });
        });
    </script>
</div>