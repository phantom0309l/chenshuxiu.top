<?php
$pagetitle = "患者报到情况统计（扫码 / 报到）(周)";
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

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <form action="/doctormgr/list" method="get" class="pr">
                    <label>医院：</label>
                    <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toHospitalCtrArray($hospitals,true),"hospitalid",$hospitalid); ?>
                   <div class='mt10'>
                        <label>市场负责人：</label>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(),"auditorid_market",$auditorid_market);?>
                        <a class="btn btn-success" href="/doctormgr/listmonth">按月统计</a>
                    </div>
                    <div class='mt10'>
                        颜色说明:
                        <span class="red">红色:有效报到<?php ?></span>
                        <span class="blue">蓝色:全部报到(有效+删除+审核中)</span>
                        <span class="green">绿色:扫码报到+仅扫码+仅报到</span>
                    </div>
					<div class='mt10'>
	                    医生颜色说明:医生名字标红为开通礼来医生
	                </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>id</th>
                        <th width=100>姓名</th>
                        <th>医院</th>
                        <th>疾病</th>
                        <th>创建时间</th>
                        <th>市场责任人</th>

                        <?php
                        foreach ($woys as $i => $v) {
                            if ($i > 3) {
                                break;
                            }

                            $thedate = XDateTime::getDatemdByWoy($v);
                            ?>
                            <th><?=$v ?><br /><?=$thedate ?></th>
                            <?php
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
            <?php
            $indexnum = 1;
            $_ii = 0;
            foreach ($doctors as $a) {
                $_ii ++;

                ?>
                    <tr>
                        <td>#<?= $_ii; ?></td>
                        <td><?= $a->id ?></td>
						<?php if($a->isHezuo("Lilly")){
							$doctor_hezuo = Doctor_hezuoDao::getOneByCompanyDoctorid("Lilly", $a->id, " and status=1");
							?>
							<td class="red" title="<?= $a->name ?> 开通时间:<?= $doctor_hezuo->starttime ?>"><?= $a->name ?></br><?= substr($doctor_hezuo->starttime, 0, 10) ?></td>
						<?php }else { ?>
							<td><?= $a->name ?></td>
						<?php } ?>
                        <td><?= $a->hospital->shortname ?></td>
                        <td><?= $a->getDiseaseNamesStr()?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->marketauditor->name ?> </td>

                        <?php

                foreach ($woys as $i => $v) {

                    if ($i > 3) {
                        break;
                    }

                    $cnt0 = $patientRptGroupbyDoctorWoy[$a->id][$v];
                    $cnt1 = $patientRptGroupbyDoctorWoy_all[$a->id][$v];
                    $cnt2 = $wxuserRptGroupbyDoctorWoy[$a->id][$v];

                    if (empty($cnt0)) {
                        $cnt0 = 0;
                    }

                    if (empty($cnt1)) {
                        $cnt1 = 0;
                    }

                    if (empty($cnt2)) {
                        $cnt2 = 0;
                    }
                    ?>
                        <td>
                            <a target="_blank" href="/patientmgr/listcond?doctorid=<?=$a->id ?>&woy=<?=$v ?>" class="red"><?= $cnt0; ?></a>
                            <a target="_blank" href="/patientmgr/listcond?doctorid=<?=$a->id ?>&woy=<?=$v ?>" class="blue"><?= $cnt1; ?></a>
                            <a target="_blank" href="/wxusermgr/list?doctorid=<?=$a->id ?>&woy=<?=$v ?>" class="green"><?= $cnt2; ?></a>
                        </td>
                                <?php
                }
                ?>
                    </tr>
                <?php } ?>
                	<tr style="background-color: #CCFFFF">
                        <td></td>
                        <td></td>
                        <td>总计</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                		<?php
                foreach ($woys as $i => $v) {
                    if ($i > 3) {
                        break;
                    }
                    $thedate = XDateTime::getDatemdByWoy($v);
                    ?>
                        <td>
                            <a target="_blank" href="/patientmgr/listcond?woy=<?=$v ?>&auditorid_market=<?=$auditorid_market?>" class="red"><?= $woyPatientCnts[$v] ?></a>
                            <a target="_blank" href="/patientmgr/listcond?woy=<?=$v ?>&auditorid_market=<?=$auditorid_market?>" class="blue"><?= $woyPatientAllCnts[$v] ?></a>
                            <a target="_blank" href="/wxusermgr/list?woy=<?=$v ?>&auditorid_market=<?=$auditorid_market?>" class="green"><?= $woyWxUserCnts[$v] ?></a>
                        </td>
                                <?php
                }
                ?>
                	</tr>
                    <tr style="background-color: #CCFFFF">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                		<?php
                foreach ($woys as $i => $v) {
                    if ($i > 3) {
                        break;
                    }
                    $thedate = XDateTime::getDatemdByWoy($v);
                    ?>
                        <td>
                            <?=$v?>
                            <br />
                            <?=$thedate?>
                        </td>
                                <?php
                }
                ?>
                	</tr>
                </tbody>
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>id</th>
                        <th width=100>姓名</th>
                        <th>医院</th>
                        <th>疾病</th>
                        <th>创建时间</th>
                        <th>市场责任人</th>

                        <?php
                        foreach ($woys as $i => $v) {
                            if ($i > 3) {
                                break;
                            }

                            $thedate = XDateTime::getDatemdByWoy($v);
                            ?>
                            <th><?=$v ?><br /><?=$thedate ?></th>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <td colspan=100 class="pagelink">
<?php include $dtpl . "/pagelink.ctr.php"; ?>
                    </td>
                    </tr>
                </thead>
            </table>
            </div>
            <div class='mt10'>
                颜色说明:
                <span class="red">红色:有效报到<?php ?></span>
                <span class="blue">蓝色:全部报到(有效+删除+审核中)</span>
                <span class="green">绿色:扫码报到+仅扫码+仅报到</span>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $("select#hospitalid").on("change", function () {
        var val = parseInt($(this).val());
        var url = val == 0 ? location.pathname : location.pathname + '?hospitalid=' + val;
        window.location.href = url;
    });
    $("select#auditorid_market").on("change", function () {
        var val = parseInt($(this).val());
        var url = val == 0 ? location.pathname : location.pathname + '?auditorid_market=' + val;
        window.location.href = url;
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
