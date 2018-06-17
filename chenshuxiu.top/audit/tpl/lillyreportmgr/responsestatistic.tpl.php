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
                <h4>响应速度和响应率</h4>
              <p><a href="http://doc.fangcunyisheng.cn/issues/<?= $redmine ?>">详情参见redmine</a></p>
              </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="title">
                        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                        <span>数据</span>
                    </div>
                </div>
                <div class="panel-body">
                <div class="col-md-12 datashow">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>响应速度</th>
                                <th>响应率</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>分子</td>
                                <td>
                                    <a href="/lillyreportmgr/responseStatisticDetail?type=haveService&fromdate=<?=$fromdate?>&todate=<?=$todate?>">
                                    <span class='red' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                        <?= $data["haveServiceCnt"] ?>
                                    </span>
                                    </a>
                                </td>
                                <td>
                                    <a href="/lillyreportmgr/responseStatisticDetail?type=haveAnswer&fromdate=<?=$fromdate?>&todate=<?=$todate?>">
                                    <span class='red' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                        <?= $data["haveAnswerCnt"] ?>
                                    </span>
                                    </a>
                                </td>
                            <tr>
                            <tr>
                                <td>分母</td>
                                <td>
                                    <a href="/lillyreportmgr/responseStatisticDetail?type=needService&fromdate=<?=$fromdate?>&todate=<?=$todate?>">
                                    <span class='red' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                        <?= $data["needServiceCnt"] ?>
                                    </span>
                                    </a>
                                </td>
                                <td>
                                    <a href="/lillyreportmgr/responseStatisticDetail?type=needAnswer&fromdate=<?=$fromdate?>&todate=<?=$todate?>">
                                    <span class='red' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                        <?= $data["needAnswerCnt"] ?>
                                    </span>
                                    </a>
                                </td>
                            <tr>
                        <tbody>
                    </table>
                </div>
                </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                  <div class="title">
                      <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span>
                      <span>查询数据(左闭右闭)</span>
                  </div>
              </div>
              <div class="panel-body">
                <div class="col-md-4">
                    <form action="/lillyreportmgr/responsestatistic" method="get">
                        <div class="form-group">
                            <label>开始日期</label>
                            <input type="text" class="form-control calendar" name="fromdate" id="fromdate" value="<?= $fromdate ?>">
                        </div>
                        <div class="form-group">
                            <label>截止日期</label>
                            <input type="text" class="form-control calendar" name="todate" id="todate" value="<?= $todate ?>">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block searchdata">查询</button>
                    </form>
                </div>
              </div>
            </div>
        </section>
    </div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function() {
    App.initHelper('select2');
});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
