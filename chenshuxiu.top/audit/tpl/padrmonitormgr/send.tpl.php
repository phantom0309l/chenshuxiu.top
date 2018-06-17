<?php
$pagetitle = "不良反应监测";
$cssFiles = [
    $img_uri . "/static/eventCalendar/eventCalendar.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/static/eventCalendar/eventCalendar.js",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址
$pageStyle = <<<STYLE
    .searchBar {
        background-color: #f2f2f2;
        padding: 8px 5px 8px 10px;
        border: 1px solid #e9e9e9;
        margin: 10px 15px 10px;
    }

    .ar-table tr td {
        border-top: 1px dashed #ddd !important;
    }

    .ar-table tr:first-child td {
        border-top: 0 !important;
    }

    .ar-item-title {
        font-weight: bold;
        padding: 0;
        width: 100px;
        vertical-align: middle !important;
    }

    .ar-blue {
        color: #199EDB;
        font-size: 1.6rem;
    }

    .dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        margin: 0 5px;
    }

    .dot-xuechanggui {
        background-color: red;
    }

    .dot-yandihuangban {
        background-color: orange;
    }

    .dot-ganshengong {
        background-color: brown;
    }

    .doctor-select {
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #646464;
        background-color: #fff;
        background-image: none;
        border: 1px solid #e6e6e6;
        border-radius: 3px;
        -webkit-box-shadow: none;
        box-shadow: none;
        -webkit-transition: all 0.15s ease-out;
        transition: all 0.15s ease-out;
    }

    .eventCalendar {
        width: 300px;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

$week = $padrmonitor->getWeek();
$rules = $padrmonitor->getRules();
$valid_rules = [];
foreach ($rules as $a) {
    $range = $a['range'];
    if ($range['start'] <= $week && ($week <= $range['end'] || '∞' == $range['end'])) {
        $valid_rules[] = $a;
    }
}
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="col-md-6 col-sm-12">
                <div class="block block-bordered">
                    <div class="block-header bg-gray-lighter">
                        <?php $medicine = $padrmonitor->medicine;
                        if ($medicine instanceof Medicine) {
                            echo "{$medicine->scientificname}（{$medicine->name}）";
                        } ?>
                    </div>
                    <div class="block-content">
                        <div class="table-responsive">
                            <table class="table remove-margin ar-table">
                                <tbody>
                                <tr>
                                    <td class="ar-item-title">当前处于</td>
                                    <td colspan="2">
                                        第<span class="ar-blue"> <?= $week; ?> </span>周
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="<?= count($valid_rules) + 1 ?>"
                                        class="ar-item-title"
                                        style="vertical-align: middle">监测检查
                                    </td>
                                </tr>
                                <?php
                                foreach ($valid_rules as $a) {
                                    $range = $a['range'];
                                    ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <ul class="remove-padding remove-margin">
                                                    <?php
                                                    $items = $a['items'];
                                                    foreach ($items as $key => $item) { ?>
                                                        <p class="<?= $key < count($items) - 1 ? '' : 'remove-margin' ?>"><?= $item['name'] ?></p>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </td>
                                        <td style="vertical-align: middle">
                                            [<?= $range['start'] ?>, <?= $range['end'] ?>] ，
                                            每<span class="ar-blue"> <?= $a['week_interval'] ?> </span>周一次
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr class="J_day_tr">
                                    <td class="ar-item-title">监测日</td>
                                    <td colspan="2">
                                        星期<span class="ar-blue"> <?= $padrmonitor->weekday ?> </span>
                                        <button class="btn btn-primary btn-sm ml10" onclick="showModifyDay()">
                                            修改
                                        </button>
                                    </td>
                                </tr>
                                <tr class="J_modifyDay_tr" style="display: none;">
                                    <td class="ar-item-title">监测日</td>
                                    <td colspan="2">
                                        <div class="col-md-4 remove-padding">
                                            <select class="form-control J_day_select">
                                                <?php for ($i = 1; $i <= 7; $i++) { ?>
                                                    <option value="<?= $i ?>" <?= $i == $padrmonitor->weekday ? 'selected' : '' ?>>
                                                        星期 <?= $i ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-8 remove-padding">
                                            <button class="btn btn-success btn-sm ml10"
                                                    onclick="submitModifyDay(this)">保存
                                            </button>
                                            <button class="btn btn-default btn-sm ml10"
                                                    onclick="hideModifyDay()">取消
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="block-footer tc">
                        <button class="btn btn-success J_send_btn" onclick="send(this)">将监测日历发送给患者</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="block block-bordered">
                    <div class="block-header bg-gray-lighter">
                        监测日历
                    </div>
                    <div class="block-content remove-padding">
                        <ul class="nav nav-tabs onepatient-tab">
                            <?php
                            $rulesItems = $padrmonitor->getRulesItems();
                            foreach ($rulesItems as $key => $value) { ?>
                                <li><a><i class="dot dot-<?= $key ?>"></i><?= $value ?></a></li>
                            <?php } ?>
                        </ul>
                        <div id="calendar" class="eventCalendar" style="margin: 0 auto;"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php
$day = $padrmonitor->weekday == 7 ? 0 : $padrmonitor->weekday;
$rulesJson = json_encode($rules, JSON_UNESCAPED_UNICODE);
$firstDrugDate = PADRMonitor_AutoService::getFirstDrugDate($padrmonitor->patientid, $padrmonitor->diseaseid, $medicine->id);
$footerScript = <<<STYLE

    var padrmonitorid = "{$padrmonitor->id}";

    $(function () {
        $('#calendar').calendar({
            ifSwitch: true, // 是否切换月份
            hoverDate: false, // hover是否显示当天信息
            backToday: true, // 是否返回当天
            startDateStr: "{$firstDrugDate}",
            day: {$day},
            rules: {$rulesJson},
        });
    });

    function showModifyDay() {
        $('.J_day_tr').hide();
        $('.J_modifyDay_tr').show();
        $('.J_send_btn').prop('disabled', true);
    }

    function hideModifyDay() {
        $('.J_day_tr').show();
        $('.J_modifyDay_tr').hide();
        $('.J_day_select').val(0);
        $('.J_send_btn').prop('disabled', false);
    }

    function submitModifyDay(btn) {
        var btnText = $(btn).text();
        $(btn).text('正在保存');
        $(btn).prop('disabled', true);
        var day = $('.J_day_select').val();
        $.ajax({
            type: "post",
            url: "/padrmonitormgr/ajaxmodifyday",
            data: {
                padrmonitorid: padrmonitorid,
                day: day,
            },
            dataType: "json",
            success: function(d) {
                if (d.errno == 0) {
                    window.location.reload();
                } else {
                    alert(d.errmsg);
                    $(btn).text(btnText);
                    $(btn).prop('disabled', false);
                }
            },
            error: function() {
                alert('保存失败');
                $(btn).text(btnText);
                $(btn).prop('disabled', false);
            }
        });
    }

    function send(btn) {
        var btnText = $(btn).text();
        $(btn).text('正在发送');
        $(btn).prop('disabled', true);
        $.ajax({
            type: "post",
            url: "/padrmonitormgr/ajaxsend",
            data: {
                padrmonitorid: padrmonitorid,
            },
            dataType: "json",
            success: function(d) {
                if (d.errno == 0) {
                    alert('发送成功');
                } else {
                    alert(d.errmsg);
                }
            },
            error: function() {
                alert('发送失败');
            },
            complete: function() {
                $(btn).text(btnText);
                $(btn).prop('disabled', false);
            }
        });
    }
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>