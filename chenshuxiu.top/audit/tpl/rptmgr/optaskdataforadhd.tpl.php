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
                <h4>限时回复数据监控</h4>
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
                    <?php if("sunflower" != $type){ ?>
                        <span class="red">紧急：红色</span>   <span class="green">不紧急：绿色</span></br>
                    <?php } ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td class="red" colspan="6" style="text-align: center;"><?= $types[$type] ?></td>
                            </tr>
                            <tr>
                                <td>时间段 \ 类别</td>
                                <td>平均回复时间(小时)</td>
                                <td>最长响应时间(小时)</td>
                                <td>消息数量(条)</td>
                                <td>关闭前未回复消息数量(条)</td>
                                <?php if("sunflower" != $type){ ?>
                                    <td>回复率（紧急10min内/非紧急1h内）</td>
                                <?php }else { ?>
                                    <td>回复率（30min内）</td>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $k => $v) { ?>
                                <tr>
                                    <td><?= $v["name"] ?></td>
                                    <td><?= $v["values"]["average"] ?></td>
                                    <td><?= $v["values"]["max_apply_times"] ?></td>
                                    <td><?= $v["values"]["cnt"] ?></td>
                                    <td><?= $v["values"]["not_apply_cnt"] ?></td>
                                    <td><?= $v["values"]["apply_rate"] ?></td>
                                <tr>
                            <?php } ?>
                        <tbody>
                    </table>
                </div>
                </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                  <div class="title">
                      <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span>
                      <span>查询数据(按照消息任务生成时间筛选左闭右闭)</span>
                  </div>
              </div>
              <div class="panel-body">
                <div class="col-md-4">
                    <form action="/rptmgr/optaskdataforadhd" method="get">
                        <div class="form-group">
                            <label>查询类型</label>
                            <?php
                                echo HtmlCtr::getSelectCtrImp($types, 'type', $type, 'js-select2 form-control', 'width: 100%');
                            ?>
                        </div>
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
