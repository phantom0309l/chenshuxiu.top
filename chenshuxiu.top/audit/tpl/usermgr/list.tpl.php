<?php
$pagetitle = "用户列表 Users";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
        	<div class="searchBar">
                <form action="/usermgr/list" method="get">
                    <div class="mt10">
                        <label>模糊搜索 : </label>
                        <input type="text" name="word" value="<?= $word ?>" />
                        (手机号|用户姓名|患者名|微信昵称) (不为空则其他条件失效)
                    </div>
                    <div class="mt10">
                        <label>身份: </label>
                    	<?php
                    $arr = array(
                        'all' => '全部',
                        'Patient' => '患者',
                        'Doctor' => '医生',
                        'Auditor' => '运营');
                    echo HtmlCtr::getRadioCtrImp($arr, 'usertype', $usertype, '');
                    ?>
                	</div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value="组合筛选">
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>userid</th>
                        <th>创建日期</th>
                        <th>微信</th>
                        <th>user:name</th>
                        <th>关系</th>
                        <th>报到姓名</th>
                        <th>报到医生</th>
                        <th>省/市/区</th>
                        <th>身份</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($users as $a) {
                        ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td class='green'><?php

                        foreach ($a->getWxUsers() as $w) {
                            echo $w->nickname;
                            echo "[{$w->wxshopid}]";
                            echo '<br/>';
                        }
                        ?></td>
                        <td><?= $a->name ?></td>
                        <td><?= $a->shipstr ?></td>
                        <td class="blue">
                            <?php
                                if ($a->patient instanceof Patient) {
                                    echo $a->patient->getMaskName();
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if ($a->patient instanceof Patient) {
                                    echo $a->patient->doctor->name;
                                }
                            ?>
                        </td>
                        <td><?= $a->getUserTypeStr(); ?></td>
                        <td>
                            <a target="_blank" href="/usermgr/modify?userid=<?= $a->id ?>">修改</a>
                        </td>
                    </tr>
                        <?php } ?>
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
