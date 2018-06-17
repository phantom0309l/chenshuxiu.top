<?php
$pagetitle = '近期医生门诊表';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jssFiles = []; //填写完整地址
?>
<?php include_once dirname(__FILE__) . '/../_header.new.tpl.php'; ?>
<div class="col-md-12">
<section class='col-md-12'>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width='150'>日期</th>
                    <th>上午</th>
                    <th>下午</th>
                    <th>晚上</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $am_pm_night = [];
                $am_pm_night[] = 'am';
                $am_pm_night[] = 'pm';
                $am_pm_night[] = 'night';

                foreach ($rows as $thedate => $row) {
                    $w = date('w', strtotime($thedate));
                    if ($w == 0) {
                        $w = '日';
                    }

                    $isToday = false;
                    if ($thedate == date('Y-m-d')) {
                        $isToday = true;
                    }

                    $isW = false;
                    if ($w == date('w')) {
                        $isW = true;
                    }
                    ?>
                    <tr>
                        <td class="<?= $isToday ? 'text-warning' : ''; ?> <?= ($isW && !$isToday) ? 'text-info' : ''; ?> "><?= $thedate ?> 周<?= $w ?>
                        </td>
    <?php
    foreach ($am_pm_night as $tt) {
        echo '<td>';
        if (!isset($row[$tt])) {
            $row[$tt] = [];
        }
        foreach ($row[$tt] as $schedule) {
            $scheduletpl = $schedule->scheduletpl;
            echo "<span class='text-info'>";
            echo $scheduletpl->doctor->name;
            echo " </span> ";
            echo $scheduletpl->toSimpleStr();
            echo " <span class='text-black-op'>";
            echo $scheduletpl->getScheduleAddressStr();
            echo ' <span class="text-info">';
            echo $schedule->getRevisitTktCnt();
            echo '</span> / ';
            echo $schedule->maxcnt;
            echo '</span><br/>';
        }
        echo '</td>';
    }
    ?>
                    </tr>
                    <?php } ?>
            </tbody>
        </table>
    </div>
</section>
</div>
<div class="clear"></div>
<?php include_once dirname(__FILE__) . '/../_footer.new.tpl.php'; ?>
