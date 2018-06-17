<?php
$pagetitle = "医生收益统计";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <section class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
              <div class="title">
                  <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span>
                  <span>导出数据</span>
              </div>
          </div>
          <div class="panel-body">
              <div class="col-md-4">
                <div class="form-group">
                  <label>导出月份（例：2017-05-01表示2017年5月）</label>
                  <input type="text" class="form-control calendar" id="date" value="<?= $date ?>">
                </div>
                <button class="btn btn-primary btn-block outdata">导出</button>
            </div>
          </div>
        </div>
        <div class='mt10'>
            颜色说明:
            <span class="green">绿色:总人数</span>
            <span class="orange">橙色:当月报到</span>
            <span class="blue">蓝色:2-6个月持续管理</span>
        </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <td width="60">序号</td>
                        <td width="60">姓名</td>
                        <td>医院</td>
                        <td>疾病</td>
                        <td>市场责任人</td>
                <?php

                foreach ($months as $i => $v) {
                    if ($i != 3) {
                        ?>
                    <td><?=$months[$i] ?></td>
                <?php }else{?>
                    <td>近３个月总计</td>
                    <?php
                    }
                }
                ?>
            </tr>
                </thead>
                <tbody>
            <?php
            $indexnum = 0;
            $amount_doctor = array();
            foreach ($doctors as $a) {
                $indexnum++;
                ?>
                <tr>
                        <td><?= $indexnum ?></td>
                        <td><?= $a->name ?></td>
                        <td><?= $a->hospital->shortname ?></td>
                        <td><?= $a->getDiseaseNamesStr() ?></td>
                        <td><?= $a->marketauditor->name ?> </td>

                    <?php
                $cnt_threemonth = 0;
                for ($i = 0; $i < count($months); $i ++) {
                    $m = $months[$i];

                    if ($m == 'beforecnt') {
                        $fromdate = "";
                        $todate = $months[$i - 1] . "-01";
                    } elseif ($m == 'allcnt') {
                        $fromdate = "";
                        $todate = "";
                    } else {
                        list ($year, $month) = explode("-", $m);

                        if ($month == 12) {
                            $monthnext = 1;
                            $yearnext = $year + 1;
                        } else {
                            $monthnext = $month + 1;
                            $yearnext = $year;
                        }

                        if ($monthnext < 10) {
                            $monthnext = "0" . $monthnext;
                        }

                        $fromdate = $m . "-01";
                        $todate = $yearnext . "-" . $monthnext . "-01";
                    }
                    $url_date = '&fromdate=' . $fromdate . '&todate=' . $todate;

                    $cnt1 = isset($patientRptGroupbyDoctorMonth_all[$a->id][$m])?$patientRptGroupbyDoctorMonth_all[$a->id][$m]:0;
                    $cnt_baodao = isset($patientRptGroupbyDoctorMonth_baodao[$a->id][$m])?$patientRptGroupbyDoctorMonth_baodao[$a->id][$m]:0;
                    $cnt_manage = isset($patientRptGroupbyDoctorMonth_manage[$a->id][$m])?$patientRptGroupbyDoctorMonth_manage[$a->id][$m]:0;

                    $amount_doctor[$i] += $cnt1;
                    if (empty($cnt1)) {
                        $cnt1 = 0;
                    }
                    if ($i < 3) {
                        $cnt_threemonth += $cnt1;
                        ?>
                            <td>
                                <?php
                                if ($myauditor->isOnlyOneRole('market')) {?>
                                    <!-- <?=$cnt1."/".$cnt_baodao."/".$cnt_manage?> -->
                                    <span class="green"><?=$cnt1?></span>
                                    <span class="orange"><?="/".$cnt_baodao?></span>
                                    <span class="blue"><?="/".$cnt_manage?></span>
                                <?php }else{?>
                                    <a href="/doctormgr/listofdoctor?doctorid=<?=$a->id ?>&themonth=<?=$m?>" class="blue">
                                      <!-- <?=$cnt1."/".$cnt_baodao."/".$cnt_manage?> -->
                                      <span class="green"><?=$cnt1?></span>
                                      <span class="orange"><?="/".$cnt_baodao?></span>
                                      <span class="blue"><?="/".$cnt_manage?></span>
                                    </a>
                                <?php }?>
                        </td>
                            <?php
                    } else {
                        ?>
                        <td style="color: red">
                            <?=$i == 3 ?$cnt_threemonth:$cnt1?>
                        </td>
                        <?php
                    }
                }
                ?>
                </tr>
                <?php
            }
            ?>
            <tr style="background-color: #CCFFFF">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>总计</td>
                <?php
                for ($i = 0; $i < count($months); $i ++) {
                    ?>
                    <td>
                        <?= $amount_doctor[$i]?>
                    </td>
                    <?php
                }
                ?>
            </tr>
                    <tr>
                        <td colspan=100 class='pagelink'>
                    <?php include $dtpl . "/pagelink.ctr.php"; ?>
                </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $("#isshowall_true").on("click", function () {
        var url = location.pathname + '?isshowall=1';
        window.location.href = url;
    });
    $("#isshowall_false").on("click", function(){
        var url = location.pathname + '?isshowall=0';
        window.location.href = url;
    });
    $(function(){
        $(".outdata").on("click", function(){
            var me = $(this);
            if(me.hasClass('process')){
                return
            }
            me.addClass('process');
            me.text('正在导出，请稍等....');
            var date = $("#date").val();
            window.location.href = "/doctormgr/settleListMonthOutput?date=" + date;
        })
    })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>