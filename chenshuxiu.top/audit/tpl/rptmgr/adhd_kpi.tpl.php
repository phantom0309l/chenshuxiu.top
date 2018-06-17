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
                <h4>持续服药率固化统计需求</h4>
<p>
    1、需要监控的用药种类：<br/>
    令A=服用的药物中有以下任意一种，择思达，正丁，专注达，阿立哌唑，可乐定透皮贴，硫必利，静灵口服液，多动宁胶囊，智力糖浆，五维赖氨酸颗粒，地牡宁神口服液。<br/>

    2、统计的结果<br/>
    8周服药率<br/>
    12周服药率<br/>
    16周服药率<br/>
    20周服药率<br/>
    24周服药率<br/>

    团队KPI<br/>
    给出时间筛选范围，以报到时间进行筛选，左闭右闭，按照这个时间范围去追踪式的判断，有符合条件的就出数据。<br/>
    遵医嘱停药状态，取距今最近的停药日期，然后用这个日期减去该患者的报到日期。如果差值结果是[0,n-4]，n=8,12,16，20,24,则从该阶段的统计分母中刨除这个患者<br/>

    运营KPI<br/>
    统计方法追踪式，同团队KPI<br/>
    遵医嘱停药：A中所有药物的记录在指定的时间范围内（比如，8周服药率，就是分子中提及的[4,8周），均为遵医嘱停药<br/>
              <p><a href="http://doc.fangcunyisheng.cn/issues/<?= $redmine ?>">详情参见redmine</a></p>
              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                  <div class="title">
                      <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span>
                      <span>导出数据(按照患者报到时间筛选左闭右闭)</span>
                  </div>
              </div>
              <div class="panel-body">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>开始日期</label>
                      <input type="text" class="form-control calendar" id="startdate" value="<?= $startdate ?>">
                    </div>
                    <div class="form-group">
                      <label>结束日期</label>
                      <input type="text" class="form-control calendar" id="enddate" value="<?= $enddate ?>">
                    </div>
                    <button class="btn btn-primary btn-block outdata">导出</button>
                </div>
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
                var startdate = $("#startdate").val();
                var enddate = $("#enddate").val();
                $.ajax({
                    "type" : "post",
                    "data" : {
                        startdate : startdate,
                        enddate : enddate,
                    },
                    "dataType" : "json",
                    "url" : '/rptmgr/ADHD_KPIOutputJson',
                    "success" : function(d) {
                        if(d.errno == 0){
                            window.location.href = "/export_jobmgr/list";
                        }else{
                            alert("导出错误，请重新导出");
                            window.location.href = window.location.href;
                        }
                    }
                });
            })
        })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
