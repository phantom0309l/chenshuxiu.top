<?php
$levels = empty($configs['level']) ? [-2] : $configs['level'];
$levelstr = $configs['showstr']['levelstr'] ?? [];

$levels_input = implode(',', $levels);
$levelstr_input = implode(',', $levelstr);
?>
<div class="form-group optaskfilter-list" data-key="level">
    <input type="hidden" id="level" name="level" value="<?=$levels_input?>">
    <input type="hidden" id="levelstr" name="levelstr" value="<?=$levelstr_input?>">
    <label class="col-xs-3 control-label" for="levels">优先级</label>
    <div class="col-xs-9">
        <?php
        echo HtmlCtr::getCheckboxCtrImp4OneUi(CtrHelper::getOptaskLevelCtrArrayForFilter(), 'levels', $levels, '');
        ?>
    </div>
    <script>
        $(function () {
            $('input[name="levels"]').on('click', function () {
                var me = $(this);
                var click_val = me.val();

                var statuss = [];
                var statusstr = [];
                if (click_val == -2) {
                    var checked_cnt = 0;
                    $('input[name="levels"]').each(function () {
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
                        $('input[name="levels"]').each(function () {
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
                    $('input[name="levels"]').each(function () {
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
                        $('input[name="levels"]').each(function () {
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

                $('input[name="levels"]').each(function () {
                    var m = $(this);
                    if (m.val() == -2 && m.prop('checked') == true) {
                        status = '-2';
                        statusstr = '全部';
                    }
                });

                $('#level').val(status);
                $('#levelstr').val(statusstr);
            });
        });
    </script>
</div>
