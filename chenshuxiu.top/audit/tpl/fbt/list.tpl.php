<?php
$pagetitle = '方寸运营后台管理系统';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-6 content-item">
        <div class="searchBar">
            昨日活跃人数 : <?= $yesterdayupdatecount ?>
            <br/>
            培训课总人数 : <?= $xiaoers + $ketangs ?>
            来自方寸儿童管理服务平台 : <?= $xiaoers ?>
            来自方寸课堂 : <?= $ketangs ?>
            <br/>
            <form action="/fbt/list" method="get">
                (方寸号|微信昵称|患者名) 模糊搜索 :
                <input type="text" name="word" value="<?= $word ?>"/>
                <input type="submit" value="搜索"/>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <td>更新时间</td>
                <td>方寸号</td>
                <td>微信号</td>
                <td>患者名</td>
                <td>性别</td>
                <td>年龄</td>
                <td>进度</td>
                <td>查看作业</td>
            </tr>
            </thead>

            <?php

            foreach ($courseuserrefs as $a) {
                $user = $a->user;
                if (false == $user instanceof User) {
                    continue;
                }

                $patient = $user->patient;
                ?>

                <tr>
                    <td><?= substr($a->updatetime, 5, 11); ?></td>
                    <td><?= $user->xcode ?></td>
                    <td><?= $a->wxuser->nickname ?></td>
                    <?php
                    if ($patient instanceof Patient) {
                        ?>
                        <td><?= $patient->getMaskName() ?></td>
                        <td><?= $patient->getSexStr() ?></td>
                        <td><?= $patient->getAgeStr() ?> 岁</td>
                        <?php
                    } else {
                        ?>
                        <td></td>
                        <td></td>
                        <td></td>
                        <?php
                    }
                    ?>
                    <td><?= $a->pos ?></td>
                    <td>
                        <button type="button" class="courseuserref-one" data-courseuserrefid="<?= $a->id ?>">详情
                        </button>
                    </td>
                </tr>

            <?php } ?>
            <tr>
                <td colspan=9>
                    <?php include $dtpl . "/pagelink.ctr.php"; ?>
                </td>
            </tr>
        </table>
        </div>
    </section>
    <section class="col-md-6 content-right">
        <p>
            当前共关注
            <span class="red" style="font-size: 34px"><?= $subscribenum ?></span>
            人，还差
            <span class="red" style="font-size: 34px"><?= 5000 - $subscribenum ?></span>
            人 。还有：
            <span class="timer">
                    <span class="timer-d red">30</span>
                    天
                    <span class="timer-h red">12</span>
                    时
                    <span class="timer-m red">30</span>
                    分
                    <span class="timer-s red">30</span>
                    秒
                </span>
        </p>
        <p>历史周活跃率</p>
        <div class="chartShell">
            <div id="activityhistory" style="height: 250px; width: auto;"></div>
        </div>
        <p>家长进度比例</p>
        <div class="chartShell">
            <div id="weekpartition" style="height: 400px; width: auto;"></div>
        </div>
        <p>历史用户增加</p>
        <div class="chartShell">
            <div id="addhistory" style="height: 250px; width: auto;"></div>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    //timer
    $(function () {
        function setTime(endTime, startTime) {
            var all = parseInt((endTime - startTime) / 1000);
            var s = all % 60;
            var m = ( (all - s) / 60 ) % 60;
            var h = ( ( (all - s) / 60 - m ) / 60 ) % 24;
            var d = ( ( (all - s) / 60 - m ) / 60 - h  ) / 24;
            var timer = $(".timer");
            var dnode = timer.find(".timer-d").text(d);
            var hnode = timer.find(".timer-h").text(h);
            var mnode = timer.find(".timer-m").text(m);
            var snode = timer.find(".timer-s").text(s);
        }

        var endDate = new Date();
        endDate.setFullYear(2016);
        endDate.setMonth(2);
        endDate.setDate(31);
        endDate.setHours(23);
        endDate.setMinutes(59);
        endDate.setSeconds(59);

        var endTime = endDate.getTime();

        setInterval(function () {
            var d = new Date();
            setTime(endTime, d.getTime());
        }, 1000);
    });

    $(function () {
        $(".courseuserref-one").on("click", function (e) {
            var node = $(this);
            var courseuserrefid = node.data("courseuserrefid");
            $.ajax({
                "type": "get",
                "data": {courseuserrefid: courseuserrefid},
                "dataType": "html",
                "url": "/fbt/hwkhtml",
                "success": function (data) {
                    $(".content-right").html(data);
                    $(".content-right").show();
                }
            });
        });

        $(document).on("click", '.answer-one', function () {
            var me = $(this);
            var answerid = me.data("answerid");
            $.ajax({
                "type": "get",
                "data": {answerid: answerid},
                "dataType": "html",
                "url": "/fbt/answer2commentofcourseJson",
                "success": function (data) {
                    alert(data);
                }
            });
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
