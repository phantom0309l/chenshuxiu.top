<?php
$pagetitle = "用户修改";
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
            <form action="/usermgr/modifypost" method="post">
                <input type="hidden" name="userid" value="<?= $user->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>userid</th>
                        <td><?= $user->id ?></td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?= $user->createtime ?></td>
                    </tr>
                    <tr>
                        <th>wxuserid</th>
                        <td><?= $user->getMasterWxUser()->id ?></td>
                    </tr>
                    <tr>
                        <th>patientid</th>
                        <td><?= $user->patientid ?></td>
                    </tr>
                    <tr>
                        <th>username</th>
                        <td><?= $user->username ?></td>
                    </tr>
                    <tr>
                        <th>微信昵称</th>
                        <td><?= $user->getMasterWxUser()->nickname ?></td>
                    </tr>
                    <tr>
                        <th>报到姓名</th>
                        <td><?= $user->patient->name ?> (<?= $user->patient->createtime ?>)</td>
                    </tr>
                    <tr>
                        <th>所属医生</th>
                        <td><?= $user->patient ? $user->patient->doctor->name : ''; ?></td>
                    </tr>
                    <tr>
                        <th>姓名</th>
                        <td>
                            <input type="text" name="name" value="<?= $user->name ?>" />
                            (可以填患者姓名)
                        </td>
                    </tr>
                    <tr>
                        <th>关系</th>
                        <td>
                            <input type="text" name="shipstr" value="<?= $user->shipstr ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>电话</th>
                        <td>
                            <input type="text" name="mobile" value="<?= $user->mobile ?>" />
                        </td>
                    </tr>
                    <?php
                        if ($user->patient instanceof Patient) {
                        ?>
                            <tr>
                                <th>省市区</th>
                                <td>
                                    <div class="col-xs-6">
                                        <?php echo HtmlCtr::getAddressCtr4New('mobile_place', $patientaddress->xprovinceid, $patientaddress->xcityid, $patientaddress->xcountyid); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    ?>
                    <tr>
                        <th>最后一次修改密码时间</th>
                        <td>
                            <?= $user->last_modifypassword_time ?>
                        </td>
                    </tr>
                    <?php if( $myauditor->isHasRole(['admin', 'yunying', 'yunyingmgr'])){?>
                        <tr>
                            <th>密码</th>
                            <td>
                                <input type="text" name="password" value="<?= $user->sasdrowp ?>" style="width: 300px;" />只有管理员能改
                            </td>
                        </tr>
                    <?php }else{ ?>
                    	<tr style="opacity: 0.5;">
                            <th>密码</th>
                            <td>
                                <?= $user->sasdrowp ?>   (想修改，找史建平或老金)
                                <input type="hidden" name="password" value="<?= $user->sasdrowp ?>" style="width: 300px;" />
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th>备注</th>
                        <td>
                            <textarea name="auditremark" rows="3" cols="80"><?= $user->auditremark ?></textarea>
                            可以记录患者姓名,年龄等信息
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
