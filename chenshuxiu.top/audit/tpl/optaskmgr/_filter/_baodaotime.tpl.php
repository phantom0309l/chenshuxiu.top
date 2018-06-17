<?php
$badaotimes = empty($configs['baodaotime']) ? [-1,-1] : $configs['baodaotime'];;
$badaotimestr = $configs['showstr']['baodaotimestr'][0] ?? '';

$badaotime = implode(',', $badaotimes);
list($start_baodao_daycnt, $end_baodao_daycnt) = explode(',', $badaotime);
?>
<div class="form-group optaskfilter-list" data-key="baodaotime">
    <input type="hidden" id="baodaotime" name="baodaotime" value="<?=$badaotime?>">
    <input type="hidden" id="baodaotimestr" name="baodaotimestr" value="<?=$badaotimestr?>">
    <label class="col-xs-3 control-label" for="baodaotime">已报到天数</label>
    <div class="col-xs-9">
        <div class="col-xs-6" style="padding-left: 0px; padding-right: 5px;">
            <?php
                $days = [];
                $days["-1"] = '未选择';
                for ($i = 0; $i <= 200; $i++) {
                    $days["{$i}"] = $i;
                }

                echo HtmlCtr::getSelectCtrImp($days, 'start_baodao_daycnt', $start_baodao_daycnt, 'form-control');
            ?>
        </div>
        <div class="col-xs-6" style="padding-left: 5px; padding-right: 0px;">
            <?php
                echo HtmlCtr::getSelectCtrImp($days, 'end_baodao_daycnt', $end_baodao_daycnt, 'form-control');
            ?>
        </div>
    </div>
</div>
<script>
    $(function () {
        function baodaotime (start_baodao_daycnt, end_baodao_daycnt) {
            var baodaotime = start_baodao_daycnt + "," + end_baodao_daycnt;

            if (start_baodao_daycnt == -1 && end_baodao_daycnt > -1) {
                var baodaotimestr = "距今" + end_baodao_daycnt + "天以内报到";
            }
            if (start_baodao_daycnt > -1 && end_baodao_daycnt == -1) {
                var baodaotimestr = "距今" + start_baodao_daycnt + "天以外报到";
            }
            if (start_baodao_daycnt > -1 && end_baodao_daycnt > -1) {
                var baodaotimestr = "报到距今" + start_baodao_daycnt + "天—" + end_baodao_daycnt + "天之间";
            }


            $('#baodaotime').val(baodaotime);

            if (start_baodao_daycnt > -1 || end_baodao_daycnt > -1) {
                $('#baodaotimestr').val(baodaotimestr);
            } else {
                $('#baodaotimestr').val('');
            }
        }

        $('#start_baodao_daycnt').on('change', function () {
            var me = $(this);
            var start_baodao_daycnt = me.val();
            var end_baodao_daycnt = $('#end_baodao_daycnt').val();

            baodaotime(start_baodao_daycnt, end_baodao_daycnt);
        });

        $('#end_baodao_daycnt').on('change', function () {
            var me = $(this);
            var end_baodao_daycnt = me.val();
            var start_baodao_daycnt = $('#start_baodao_daycnt').val();

            baodaotime(start_baodao_daycnt, end_baodao_daycnt);
        });
    });
</script>