<?php
$pagetitle = "患者编辑";
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
        <?php include_once $tpl . "/patientmgr/_menu.tpl.php"; ?>
        <div class="content-div">
        <section class="col-md-12">
            <form action="/patientmgr/modifypost" method="post">
                <input type="hidden" name="patientid" value="<?= $patient->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>patientid</th>
                        <td><?= $patient->id?></td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?= $patient->createtime ?></td>
                    </tr>
                    <tr>
                        <th>所属医生</th>
                        <td><?= $pcard->doctor->name ?>  of <?= $pcard->doctor->hospital->name ?></td>
                    </tr>
                    <tr>
                        <th>合并症/诊断</th>
                        <td>
                            <?php
                                foreach ($patient->getTagRefs('Disease') as $a) {
                                    ?>
                                    	<?= $a->tag->name ?><br />
                                	<?php
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>院内病历号</th>
                        <td>
                            <input type="text" name="out_case_no" value="<?= $pcard->out_case_no ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>院内就诊卡号：</th>
                        <td>
                            <input type="text" name="patientcardno" value="<?= $pcard->patientcardno ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>院内患者ID</th>
                        <td>
                            <input type="text" name="patientcard_id" value="<?= $pcard->patientcard_id ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>院内病案号</th>
                        <td>
                            <input type="text" name="bingan_no" value="<?= $pcard->bingan_no ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>姓名</th>
                        <td>
                            <input type="text" name="name" value="<?= $patient->name ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>妈妈的姓名</th>
                        <td>
                            <input type="text" name="mother_name" value="<?= $patient->mother_name ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>等级</th>
                        <td>
                            <input type="text" name="level" value="<?= $patient->level ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>性别</th>
                        <td><?php echo HtmlCtr::getRadioCtrImp(array('0'=>'未知','1'=>'男','2'=>'女'), 'sex', $patient->sex, ' ') ?> </td>
                    </tr>
                    <tr>
                        <th>手机号</th>
                        <td>
                            <?php
                                $linkmans = $patient->getLinkmans();
                                foreach ($linkmans as $linkman) {
                                    if ($linkmans->mobile) {
                                        $maskmobile = $linkman->getMarkMobile();
                                        $mobilestr = $maskmobile . "(" . $linkman->shipstr . ")";
                                        ?>
                                            <a href="/linkmanmgr/listofpatient?patientid=<?=$patient->id?>"><?=$mobilestr?></a><br />
                                        <?php
                                    }
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>出生日期</th>
                        <td>
                            <input type="text" name="birthday" class="calendar" value="<?= $patient->birthday ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>身份证</th>
                        <td>
                            <input type="text" name="prcrid" value="<?= $patient->prcrid ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>患者上次发作时间</th>
                        <td>
                            <input type="text" name="last_incidence_date" class="calendar" value="<?= $pcard->last_incidence_date ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>省/市/区</th>
                        <td>
                            <div class="col-xs-6">
                                <?php echo HtmlCtr::getAddressCtr4New('mobile_place', $patientaddress->xprovinceid, $patientaddress->xcityid, $patientaddress->xcountyid); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>备注</th>
                        <td>
                            <textarea name="auditremark" rows="3" cols="40"><?= $patient->auditremark ?></textarea>
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

            <!-- 状态变更log -->
            <div class="status-log-box">
                <h5 style="display:inline-block;">状态变更日志</h5>
                <div class="block-content">
                    <ul class="list list-timeline pull-t">
                        <?php foreach ($patient_status_logs as $a) { ?>
                            <li>
                                <div class="list-timeline-time"><?=$a->getCreateDay()?></div>
                                <a class="list-timeline-icon fa fa-cog bg-primary" target="_blank" href="/patientmgr/list4bind?patientid=<?=$patient->id?>" data-toggle="tooltip" title="" data-original-title="<?=$patient->getStatusStr()?>"></a>
                                <div class="list-timeline-content">
                                    <p class="font-s13">
                                        <?php
                                            $arr = json_decode($a->patient_status_old_json,true);
                                            $statusstr_old = XConst::status_withcolor($arr["status"]);
                                            $auditstatusstr_old = XConst::auditStatus($arr["auditstatus"]);
                                        ?>
                                        修改前：<?=$statusstr_old?>、<?=$auditstatusstr_old?></br>
                                    </p>
                                    <p class="font-w600">
                                        <?php
                                            $arr = json_decode($a->patient_status_json,true);
                                            $statusstr_new = XConst::status_withcolor($arr["status"]);
                                            $auditstatusstr_new = XConst::auditStatus($arr["status"]);
                                        ?>
                                        修改后：<?=$statusstr_new?>、<?=$auditstatusstr_new?></br>
                                    </p>
                                    <p class="">
                                        <?=$a->content?></br>
                                    </p>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </section>
        </div>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
