<?php
$pagetitle = "方寸运营后台管理系统";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
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
                        <span>AE 上报率明细（第19页）</span>
                    </div>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="/lillyreportmgr/page19detail" method="get" class="pr">
                        <div class="">
                            <div class="form-group">
                                <label class="control-label">统计截止日期</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control calendar col-md-4" id="thedate" name="thedate" value='<?= $thedate ?>'>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-block" style="width : 120px; margin-left : 20px;">查询</button>
                        </div>
                    </from>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <thead>
                    </thead>
                    <tbody>
                <?php
                foreach ($data as $k => $v) {
                    ?>
                        <tr>
                            <?php
                            foreach ($v as $i => $j) {
                                ?>
                                <td><?= $j ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
