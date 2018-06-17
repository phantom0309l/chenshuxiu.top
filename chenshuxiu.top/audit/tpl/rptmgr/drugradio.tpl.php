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
                <h4><?=$week?>周遵医嘱服药率</h4>
<p>

patientID<br/>
所属医生<br/>
所属礼来代表<br/>
报到日期<br/>
报到时长（距今）<br/>
入项目日期<br/>
第一条择思达用药记录日期<br/>
最后一条择思达用药记录日期<br/>
第一条AE或PC或AEPC创建日期<br/>
最后一条AE或PC或AEPC创建日期<br/>
截止目前AE/AEPC/PC创建总个数<br/>
生成4周基础用药提醒日期<br/>
生成任务+10天内是否有择思达用药记录<br/>
生成任务日期+10天内有记录的患者，在任务生成后到更新期间是否有运营发送的消息/量表/电话<br/>
择思达记录是否为0（取该时间区间内距今最近一次用药情况）<br/>
停药 原因（遵医嘱停药/自行停药）<br/>
停药备注<br/>
当前是否在项目中<br/>
出组原因（顺利出组、不活跃退出、停换药退出、主动退出、扫非合作医生退出、取关）<br/>
出组日期<br/>

              <p><a href="http://doc.fangcunyisheng.cn/issues/<?= $redmine ?>">详情参见redmine</a></p>
              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                  <div class="title">
                      <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span>
                      <span>导出数据(按照患者入组筛选左闭右闭)</span>
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
                window.location.href = "/rptmgr/drugRadioOutput?startdate=" + startdate + "&enddate=" + enddate + "&week=$week";
            })
        })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
