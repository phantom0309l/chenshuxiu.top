<?php
$pagetitle = "患者列表 / 报到列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; // 填写完整地址
$pageStyle = <<<STYLE
.register {
	color: #0066ff
}

.saoma {
	color: #0caf2f
}
.searchBar .form-group label {
    font-weight: 500;
    width: 95px;
    text-align: left;
    padding-right: 0;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<!DOCTYPE html>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });

        $('#patient-listcond-word').autoComplete({
            type: 'patient',
            partner: '#patientid',
            change: function (event, ui) {
            },
            select: function (event, ui) {
                // $('#patientid').val(ui.item.id);
            },
            close: function (event, ui) {
            }
        });
    })
</script>

<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form action="/patientmgr/listcond" method="post" class="pr form-horizontal">
                <div class="form-group">
                    <label class="col-md-2 control-label">医院 </label>
                    <div class="col-md-3">
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toHospitalCtrArray($hospitals,true),"hospitalid",$hospitalid,"form-control js-select2"); ?>
                    </div>
                    <label class="col-md-2 control-label">医生 </label>
                    <div class="col-md-3">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                    <span class="text-warning collapse" style="line-height: 2.2">如果选了医生, 疾病,医院和市场负责人选择失效</span>
                    <div class="collapse">
                        <label>市场负责人: </label>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getMarketAuditorCtrArray(),"auditorid_market",$auditorid_market,"f18");?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">报到开始时间</label>
                    <div class="col-md-3">
                        <input type="text" class="calendar form-control" name="fromdate" value="<?= $fromdate ?>" placeholder="开始时间" />
                    </div>
                    <label class="control-label col-md-2">报到截至时间</label>
                    <div class="col-md-3">
                        <input type="text" class="calendar form-control" name="todate" value="<?= $todate ?>" placeholder="截至时间" />
                    </div>
                    <span class="text-warning" style="line-height: 2.2">(不含截至时间)</span>
                    <!--<button class="btn_style4" id="cleardate">清空日期</button>-->
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">性别</label>
                    <div class="col-md-3">
                        <?= HtmlCtr::getSelectCtrImp(XConst::$SexsAll, 'sex', $sex, "form-control"); ?>
                        </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">年龄</label>
                    <div class="col-md-3">
                        <?= HtmlCtr::getSelectCtrImp($ages, 'age', $age,"form-control"); ?>
                        </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label" for="mobile_place"> 省市区</label>
                    <div class="col-md-6">
                        <?php echo HtmlCtr::getAddressCtr4New('mobile_place', $xprovinceid, $xcityid, $xcountyid); ?>
                        </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">患者分类</label>
                        <?php
                        $arr = array(
                            'all' => '全部',
                            'deleted' => '已删除',
                            'effective' => '有效的',
                            'market' => '市场');
                        ?>
                        <div class="col-md-6">
                        <?=HtmlCtr::getRadioCtrImp4OneUi($arr, 'statusstr', $statusstr, 'css-radio-warning');?>
                        </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">关注</label>
                    <div class="col-md-6">
                        <?php
                        $arr = array(
                            '-1' => '全部',
                            '1' => '关注',
                            '0' => '未关注');
                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, 'subscribetype', $subscribetype, 'css-radio-warning');
                        ?>
                        </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">患者状态</label>
                    <div class="col-md-3">
                        <?php
                        $arr = PatientStatusService::getPatientStatusDescArray();
                        echo HtmlCtr::getSelectCtrImp($arr, 'statustype', $statustype, 'form-control');
                        ?>
                        </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">患者标签</label>
                    <div class="col-md-3">
                        <?php
                        echo HtmlCtr::getSelectCtrImp($tags, 'tagid', $tagid, 'form-control');
                        ?>
                        </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">市场负责人</label>
                    <div class="col-md-3">
                        <?php
                            echo HtmlCtr::getSelectCtrImp(CtrHelper::getAllAuditorCtrArray(), 'auditorid_market', $auditorid_market, 'js-select2 form-control');
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-3">
                        <input type="submit" class="btn btn-success btn-minw" value=" 组合筛选 ">
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6 col-offset-md-6 pull-right remove-padding-r">
            <form class="form form-horizontal" action="/patientmgr/listcond" method="get">
                <div class="input-group">

                    <?php include_once $tpl . '/_search_patient.tpl.php'; ?>
                    <span class="input-group-btn">
                        <button class="btn btn-success" type="submit">
                            <i class="fa fa-search"></i>
                            搜索
                        </button>
                    </span>
                </div>
            </form>
            <p></p>
        </div>
        <div class="clearfix"></div>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th class="collapse">序号</th>
                        <th>患者id</th>
                        <th>报到时间</th>
                        <td class="collapse">关注时间/取关时间</td>
                        <th>患者姓名</th>
                        <td>状态</td>
                        <th class="collapse">首次医生</th>
                        <th>当前医生</th>
                        <th>最后活跃时间</th>
                        <th class="collapse">用药</th>
                        <th class="collapse">操作</th>
                        <th class="collapse">用户</th>
                        <th class="collapse">出生年月</th>
                        <th>年龄</th>
                        <th>性别</th>
                        <th>省</th>
                        <th>市</th>
                        <th>区/县</th>
                        <th>同名</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($patients as $i => $a) {
                    $pcard = $a->getMasterPcard();
                    ?>
                        <tr>
                        <td class="collapse"><?=$pagelink->getStartRowNum () + $i ?></td>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td class="collapse">
                                <?php
                    foreach ($a->getWxUsers() as $_wxuser) {
                        echo $_wxuser->getCreateDay() . "[关注]<br/>";
                        echo ($_wxuser->subscribe == 0) ? substr($_wxuser->unsubscribe_time, 0, 10) . "[退订]" : '';
                    }
                    ?>
                            </td>
                        <td>
                            <a target="_blank" href="/optaskmgr/listnew?patientid=<?=$a->id?>&patient_name=<?=$a->name?>&diseaseid=<?=$a->diseaseid?>"><?= $a->getMaskName()  ?></a>
                        </td>
                        <td>
                            <a target="_blank" href="/patient_status_logmgr/list?patientid=<?=$a->id ?>"><?= $a->getStatusStr(); ?></a>
                        </td>
                        <td class="collapse"><?= $a->first_doctor->name; ?>
                        </td>
                        <td style="text-align: left">
                            <?= $pcard->doctor->name ?> <span class='gray f10'>(市场：<?=$pcard->doctor->marketauditor->name?>)</span>
                            <br />
                            <span class='gray f10'><?= $pcard->doctor->hospital->name ?></span>
                        </td>
                        <td><?= $a->lastactivitydate ?> </td>
                        <td class="collapse"><?=$a->getMedicinestr(); ?></td>
                        <td class="collapse">
                            <a target="_blank" href="/patientmgr/modify?patientid=<?= $a->id ?>">修改</a>
                        </td>
                        <td class="collapse">
                            <div class="table-responsive">
                                <table>
                                	<?php
                    foreach ($a->getUsers() as $b) {
                        ?>
                                            <tr>
                                        <td><?= $b->shipstr ?> : <a target="_blank" href="/usermgr/modify?userid=<?= $b->id ?>">改</a>
                                        </td>
                                    </tr>
                                        <?php
                    }
                    ?>
                                </table>
                            </div>
                        </td>
                        <td class="collapse"><?php $a->birthday == '0000-00-00' ? $temp = '未知' : $temp = $a->birthday ?><?= $temp ?></td>
                        <td><?= $a->getAgeStr(); ?></td>
                        <td><?= $a->getSexStr(); ?></td>
                        <td><?= $a->getXprovinceStr(); ?></td>
                        <td><?= $a->getXcityStr(); ?></td>
                        <td><?= $a->getXcountyStr(); ?></td>
                        <td>
                            <a href="/patientmgr/list4bind?patientid=<?= $a->id ?>"><?= $a->getSameNamePatientCnt() ?></a>
                        </td>
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
<?php
$footerScript = <<<SCRIPT
$(function() {

});
SCRIPT;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
