<?php
$pagetitle = "方寸运营后台管理系统";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .title{font-size:18px; color:#337ab7;}
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
                      <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                      <span>需求描述</span>
                  </div>
              </div>
              <div class="panel-body">
                <h4>报到30-180天患者缓解率</h4>
<p>
1、缓解率<br/>
调取报到时长[30-180]天有效患者的人数；<br/>
调取以上患者当中，SNAP-iv评估平均分小于等于一分或者总分最高分比最低分高20%（含）以上的患者人数<br/>

2、SNAP-IV评估填写量<br/>
截止到17年x月x日，系统内SNAP-IV评估填写总数；<br/>
</p>
              <p><a href="http://doc.fangcunyisheng.cn/issues/<?= $redmine ?>">详情参见redmine</a></p>
              </div>
            </div>

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
                      <label>报到时长</label>
                      <input type="number" class="form-control" id="baodaocnt_left" value="30" style="width:70px; display:inline-block;">
                      ---
                      <input type="text" class="form-control" readonly value="180" style="width:70px; display:inline-block;">
                    </div>
                    <div class="form-group">
                      <label>缓解率截止日期</label>
                      <input type="text" class="form-control calendar" id="thedate_huanjie" value="<?= $thedate_huanjie ?>">
                    </div>
                    <div class="form-group">
                      <label>SNAP-IV评估截止日期</label>
                      <input type="text" class="form-control calendar" id="thedate_scale" value="<?= $thedate_scale ?>">
                    </div>
                    <button class="btn btn-primary btn-block outdata">导出</button>
                </div>
                <p class="datashow"></p>
              </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
        $(function(){
            $(".outdata").on("click", function(){
                var me = $(this);
                if(me.hasClass('process')){
                    return
                }
                me.addClass('process');
                me.text('正在导出，请稍等....');
                var thedate_huanjie = $("#thedate_huanjie").val();
                var thedate_scale = $("#thedate_scale").val();
                var baodaocnt_left = $("#baodaocnt_left").val();
                $.ajax({
                    url: '/rptmgr/huanjieRatioJson',
                    timeout: 200000,
                    type: 'get',
                    dataType: 'json',
                    data: {thedate_huanjie: thedate_huanjie, thedate_scale: thedate_scale, baodaocnt_left: baodaocnt_left}
                })
                .done(function(d) {
                    var str = "";
                    var str1 = "基于上面截止时间，报到时长[30-180]天有效患者的人数 : " + d.total + "<br/>";
                    var str2 = "以上患者当中，SNAP-iv评估平均分小于等于一分的患者人数 : " + d.huanjiecnt1 + "<br/>";
                    var str3 = "以上患者当中，SNAP-iv评估总分最高分比最低分高20%（含）以上的患者人数 : " + d.huanjiecnt2 + "<br/>";
                    var str4 = "以上患者当中，缓解患者总数 : " + d.huanjiecnt + "; 缓解率为:" + ((d.huanjiecnt/d.total)*100).toFixed(2) + "%<br/>";
                    var str5 = "基于上面截止时间，SNAP-IV评估填写总数 : " + d.scalecnt + "<br/>";
                    str = str1 + str2 + str3 + str4 + str5;
                    $(".datashow").html(str);
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    me.removeClass('process');
                    me.text('导出');
                });
            })
        })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
