<?php
$pagetitle = "员工列表 Auditor";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
//    $img_uri . "/v5/plugin/echarts/echarts.js",
]; //填写完整地址
?>

<style>
    .searchBar .form-group label {
        font-weight: 500;
        width: 9%;
        text-align: left;
    }
</style>

<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form class="form-horizontal" action="/auditormgr/cancerlist" method="get">
                <input type="hidden" id="tab_select" name="tab_select" value="<?=$tab_select?>">
                <div class="form-group mt10">
                    <label class="control-label col-md-2">市场人员</label>
                    <div class="col-md-3">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(), "auditorid", $auditorid, 'js-select2 form-control auditorid'); ?>
                    </div>
                    <label class="control-label col-md-2">日期范围</label>
                    <div class="col-md-3">
                        <input class="form-control" type="text" id="date_range" name="date_range" value="<?= $date_range ?>" placeholder="日期范围">
                    </div>
                </div>
                <div class="form-group mt10">
                    <label class="control-label col-md-1">首单-运营(%)</label>
                    <div class="col-md-3">
                        <input class="form-control" type="text" id="first_auditor" name="first_auditor" value="<?= $first_auditor ?>" placeholder="首单-运营">
                    </div>

                    <label class="control-label col-md-1">首单-非运营(%)</label>
                    <div class="col-md-3">
                        <input class="form-control" type="text" id="not_first_auditor" name="not_first_auditor" value="<?= $not_first_auditor ?>" placeholder="首单-非运营">
                    </div>

                    <label class="control-label col-md-1" style="width: 90px;">非首单(%)</label>
                    <div class="col-md-2">
                        <input class="form-control" type="text" id="not_auditor" name="not_auditor" value="<?= $not_auditor ?>" placeholder="非首单">
                    </div>
                </div>
                <div class="mt10">
                    <input type="submit" class="btn btn-success" value="组合筛选"/>
                </div>
            </form>
        </div>

        <div class="block">
            <ul class="nav nav-tabs nav-tabs-alt js_tab_select" data-toggle="tabs">
                <?php
                    if ($tab_select == '')
                ?>
                <li class="<?=$tab_select == '#patient' ? 'active' : $tab_select == '' ? 'active' : '';?>">
                    <a href="#patient">新增患者</a>
                </li>
                <li class="<?=$tab_select == '#doctor' ? 'active' : '';?>">
                    <a href="#doctor">新增医生</a>
                </li>
                <li class="<?=$tab_select == '#shoporder' ? 'active' : '';?>">
                    <a href="#shoporder">订单</a>
                </li>
            </ul>
            <div class="block-content tab-content">
                <div class="tab-pane <?=$tab_select == '#patient' ? 'active' : $tab_select == '' ? 'active' : '';?>" id="patient">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <?php
                                if (false == empty($patientcnts)) {
                                    foreach ($patientcnts[0] as $k => $v) {
                                        ?>
                                            <th><?=$k?></th>
                                        <?php
                                    }
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $cnt = 0;
                                if (false == empty($patientcnts)) {
                                    foreach ($patientcnts as $i => $a) {
                                        $cnt += $a['cnt'];
                                        ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <?php
                                            foreach ($a as $v) {
                                                ?>
                                                <td><?= $v ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                }
                            ?>

                            <tr>
                                <?php if ($auditorid) { ?>
                                    <td colspan="5">总计</td>
                                    <td><?=$cnt?></td>
                                <?php } else { ?>
                                    <td colspan="2">总计</td>
                                    <td><?=$cnt?></td>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane <?=$tab_select == '#doctor' ? 'active' : '';?>" id="doctor">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <?php
                            if (false == empty($doctorcnts)) {
                                foreach ($doctorcnts[0] as $k => $v) {
                                    ?>
                                    <th><?= $k ?></th>
                                    <?php
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                                $cnt = 0;
                                if (false == empty($doctorcnts)) {
                                    foreach ($doctorcnts as $i => $a) {
                                        if (!$auditorid) {
                                            $cnt += $a['cnt'];
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <?php
                                            foreach ($a as $v) {
                                                ?>
                                                <td><?= $v ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                }
                            ?>
                            <tr>
                                <?php if ($auditorid) { ?>
                                    <td colspan="4">总计</td>
                                    <td><?=count($doctorcnts)?></td>
                                <?php } else { ?>
                                    <td colspan="2">总计</td>
                                    <td><?=$cnt?></td>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane <?=$tab_select == '#shoporder' ? 'active' : '';?>" id="shoporder">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <?php
                            if (false == empty($shopordercnts)) {
                                foreach ($shopordercnts[0] as $k => $v) {
                                    ?>
                                    <th><?= $k ?></th>
                                    <?php
                                }
                            }
                            ?>
                            <?php if($auditorid) { ?>
                                <th>绩效</th>
                            <?php }?>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                                $shiji_amount_sum = 0.0;
                                $amount_sum = 0.0;
                                $jixiao_sum = 0.0;
                                $cnt = 0.0;
                                if (false == empty($shopordercnts)) {
                                    foreach ($shopordercnts as $i => $a) {
                                        $cnt += $a['cnt'];
                                        ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <?php
                                            foreach ($a as $k => $v) {
                                                if ($k == '运营转化') {
                                                    list($id, $v) = explode('|', $v);
                                                    $arr = [
                                                        '0' => '未选择',
                                                        '1' => '否',
                                                        '2' => '是'
                                                    ];
                                                    ?>
                                                    <td id="<?= $id ?>">
                                                        <?php echo HtmlCtr::getSelectCtrImp($arr, 'is_lead_by_auditor', $v, 'form-control'); ?>
                                                    </td>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <td><?= $v ?></td> <?php
                                                }
                                            }
                                            ?>
                                            <?php
                                            // 计算绩效
                                            if ($auditorid) {
                                                ?>
                                                <td><?php
                                                list($id, $is_lead_by_auditor) = explode('|', $a['运营转化']);
                                                $amount = str_replace('￥', '', $a['订单金额(不含运费)']);
                                                $shiji_amount = str_replace('￥', '', $a['实付金额(含运费)']);
                                                $jixiao = 0.0;
                                                if ($a['是否首单'] == '是' && $is_lead_by_auditor == 2) {
                                                    $jixiao = $amount * $first_auditor * 0.01;
                                                    echo "{$amount} * {$first_auditor}% = {$jixiao}";
                                                } elseif ($a['是否首单'] == '是' && $is_lead_by_auditor == 1) {
                                                    $jixiao = $amount * $not_first_auditor * 0.01;
                                                    echo "{$amount} * {$not_first_auditor}% = {$jixiao}";
                                                } elseif ($a['是否首单'] == '否') {
                                                    $jixiao = $amount * $not_auditor * 0.01;
                                                    echo "{$amount} * {$not_auditor}% = {$jixiao}";
                                                }

                                                $shiji_amount_sum += $shiji_amount;
                                                $amount_sum += $amount;
                                                $jixiao_sum += $jixiao;
                                                ?></td><?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                }
                            ?>
                            <tr>
                                <?php if ($auditorid) { ?>
                                    <td colspan="9">总计</td>
                                    <td>￥<?=$shiji_amount_sum?></td>
                                    <td>￥<?=$amount_sum?></td>
                                    <td>￥<?=$jixiao_sum?></td>
                                <?php } else { ?>
                                    <td colspan="2">总计</td>
                                    <td><?=$cnt?></td>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<script>
    $(function () {
        $('select[name="is_lead_by_auditor"]').on('change', function () {
            var is_lead_by_auditor = $(this).val();
            console.log(is_lead_by_auditor);

            var shoporderid = $(this).parent('td').attr('id');
            console.log(shoporderid);

            $.ajax({
                "type" : "get",
                "data" : {
                    shoporderid : shoporderid,
                    is_lead_by_auditor : is_lead_by_auditor
                },
                "dataType" : "text",
                "url" : "/shopordermgr/changeleadauditorjson",
                "success" : function (data) {
                    if (data == 'success') {
                        alert("修改成功!");
                    } else {
                        alert("修改失败!");
                    }
                }
            });
        });
    });
</script>
<?php
$footerScript = <<<XXX
$(function () {
    App.initHelper('select2');

    //日期范围选择
    laydate.render({ 
        elem: '#date_range',
        range: '至' //或 range: '~' 来自定义分割字符
    });
    
    $(".js_tab_select > li > a").on('click', function(){
        var tab_select = $(this).attr('href');
        $("#tab_select").val(tab_select);
        console.log(tab_select);
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
