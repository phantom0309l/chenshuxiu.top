<?php
$pagetitle = '运营系统首页';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.register {
	color: #0066ff
}

.saoma {
	color: #0caf2f
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12" style='margin-top:20px;'>
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>患者id</th>
                        <th>报到时间</th>
                        <td>关注时间/取关时间</td>
                        <th>患者姓名</th>
                        <td>状态</td>
                        <th>首次医生</th>
                        <th>当前医生</th>
                        <th>最后活跃时间</th>
                        <th>用户</th>
                        <th>出生年月</th>
                        <th>年龄</th>
                        <th>性别</th>
                        <th>省</th>
                        <th>市</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach ($patients as $i => $a) {
                ?>
                    <tr>
                        <td><?=$pagelink->getStartRowNum () + $i ?></td>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td>
                            <?php
                                foreach ($a->getWxUsers() as $_wxuser) {
                                    echo $_wxuser->getCreateDay()."[关注]<br/>";
                                    echo ($_wxuser->subscribe == 0) ? substr($_wxuser->unsubscribe_time,0,10)."[退订]" : '';
                                }
                            ?>
                        </td>
                        <td><?= $a->getMaskName() ?></td>
                        <td><?= $a->getStatusStr(); ?> </td>
                        <td><?= $a->first_doctor->name; ?> </td>
                        <td><?= $a->doctor->name ?></td>
                        <td><?= $a->lastactivitydate ?> </td>
                        <td>
                            <div class="table-responsive">
                                <table>
                    	<?php
                foreach ($a->getUsers() as $b) {
                    ?>
                                        <tr>
                                    <td><?= $b->shipstr ?></td>
                                </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </td>
                        <td><?php $a->birthday == '0000-00-00' ? $temp = '未知' : $temp = $a->birthday ?><?= $temp ?></td>
                        <td><?= $a->getAgeStr() ?></td>
                        <td>
                       		<?= $a->getSexStr()?>
                       	</td>
                        <td><?= $a->getXprovinceStr(); ?> </td>
                        <td><?= $a->getXcityStr(); ?> </td>
                    </tr>
                	<?php }?>
                    <tr>
                        <td colspan=100 class="pagelink">
<?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
