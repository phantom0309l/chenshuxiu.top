<?php
$pagetitle = "方寸运营后台管理系统";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址
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
                  <h4>复购患者名单</h4>
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
                        <label>首单</label>
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getShopOrderFirstCtrArray(),'first', $first, 'js-select2 form-control first')?>
                    </div>
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
            var startdate = $("#startdate").val();
            var enddate = $("#enddate").val();
            var first = $(".first option:selected").val();
            window.location.href = "/rptmgr/shoporderOutput?startdate=" + startdate + "&enddate=" + enddate + "&first=" + first;
        })
    })

    App.initHelper('select2');
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
