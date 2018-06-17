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
                <h4>患教文章推送的统计</h4>
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
                                <td class="red" colspan="6" style="text-align: center;"><?= $types[$type] ?></td>
                            </tr>
                            <tr>
                                <td>ID</td>
                                <td>课文</td>
                                <td>推送的WxUser总数量</td>
                                <td>打开文章的WxUser总数量</td>
                                <td>看过2次以上的WxUser总数量</td>
                                <td>近三周推送的WxUser数量</td>
                                <td>文章被打开的总次数</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($PatientEduCount as $k => $v) { ?>
                                <tr>
                                    <td><?= $v["lessonid"] ?></td>
                                    <td><a href="/lessonmgr/modify?lessonid=<?= $v['lessonid']?>"><?= $v["lessonTitle"] ?></a></td>
                                    <td><?= $v["WxUserCnt"] ?></td>
                                    <td><?= $v["WxUserCntByRead"] ?></td>
                                    <td><?= $v["WxUserCntByReadTimes"] ?></td>
                                    <td><?= $v["WxUserCntByReadAndWeek"] ?></td>
                                    <td><?= $v["readTimes"] ?></td>
                                <tr>
                            <?php } ?>
                        <tbody>
                    </table>
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
