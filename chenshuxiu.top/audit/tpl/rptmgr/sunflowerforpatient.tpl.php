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
                <h4>入组患者维度统计</h4>
<p>

patientID<br/>
所属医生<br/>
所属礼来代表<br/>
报到日期<br/>
首次电话任务生成日期<br/>
首次电话任务关闭前，是否有用药记录<br/>
是否加入项目（是、否）<br/>
入项目日期<br/>
入组时的服药时长<br/>
加入项目前有几次双方接通的电话<br/>
当前状态<br/>
出组日期<br/>
是否有过入课程行为<br/>
是否提交过作业<br/>
AE 的数量<br/>
PC 的数量<br/>
未加入项目原因<br/>

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
                    "url" : '/rptmgr/sunflowerForPatientOutput',
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
