<?php
$pagetitle = "定时任务日志 CronLog";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
        "{$img_uri}/static/js/avalon.js",
]; //填写完整地址
$pageStyle = <<<STYLE
    .main {
        width: 30%;
        position: relative;
        margin-left: 20%;
        margin-right: auto;
    }
STYLE;
$pageScript = <<<SCRIPT
    var pageredirect = avalon.define("pageredirect", function(vm){
        vm.pagecheck = function (pagenum) {
            url = window.location.href;
            window.location = url.match(/pagenum/)? url.replace(/(pagenum=)(\d*)/,"$1"+pagenum) : newurl = url + "&pagenum="+pagenum
        };
    });
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <th>记录ID(特殊情景使用)</th>
                    <th>脚本类型</th>
                    <th>执行标题</th>
                    <th>执行内容</th>
                    <th>执行时间</th>
                    <th>结束时间</th>
                    <th>操作</th>
                </thead>
                <tbody>
            <?php foreach ($cronlogs as $cronlog) { ?>
                <tr>
                        <td><?= $cronlog->id ?></td>
                        <td class=''><?= $cronlog->getTypeStrDesc() ?></td>
                        <td><?= $cronlog->title? $cronlog->title : "未启用" ?></td>
                        <td><?= $cronlog->content? $cronlog->content : "未启用" ?></td>
                        <td><?= $cronlog->begintime ?></td>
                        <td><?= $cronlog->endtime ?></td>
                        <td>
                            <a href="/cronlogmgr/one?cronlogid=<?= $cronlog->id ?>" target='_blank'>查看详情</a>
                        </td>
                    </tr>
            <?php } ?>
            </tbody>
            </table>
            </div>
        </section>
    </div>
    <p class='main' ms-controller='pageredirect'>
<?php if ($pagebar[0] > 1) { ?>
            <a style='cursor: pointer;' ms-click='pagecheck(<?= ($tmp = $pagebar[0]-6) > 0? $tmp : 1 ?>)'>...</a>
<?php } ?>

<?php foreach ($pagebar as $a) { ?>
            <a style='cursor:pointer;<?= ($pagenum == $a)? "color:red": "" ?>' ms-click='pagecheck(<?= $a ?>)' ><?= $a ?></a>
<?php } ?>

<?php if ($pagelast > $pagebar[-1]) { ?>
            <a style='cursor: pointer;' ms-click='pagecheck(<?= ($tmp = end($pagebar)+6) > $pagelast? $pagelast: $tmp ?>)'>...</a>
<?php } ?>
        </p>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>