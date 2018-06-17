<?php
$pagetitle = "患者报到情况统计（扫码 / 报到）(月)";
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
                <form action="/doctormgr/listmonth" method="get" class="form-horizontal pr">
                    <div class="form-group">
                        <label class="col-md-2 control-label">医院：</label>
                        <div class="col-md-2">
                            <?= HtmlCtr::getSelectCtrImp(CtrHelper::toHospitalCtrArray($hospitals,true),"hospitalid",$hospitalid,'js-select2 form-control'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">市场负责人：</label>
                        <div class="col-md-2">
                            <?= HtmlCtr::getSelectCtrImp(CtrHelper::getMarketAuditorCtrArray(),"auditorid_market",$auditorid_market,'js-select2 form-control'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">市场组：</label>
                        <div class="col-md-2">
                            <?= HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorGroupCtrArray(true, 'market'),"auditorgroupid",$auditorgroupid,'js-select2 form-control'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">医生开药门诊状态：</label>
                        <div class="col-md-2">
                            <?= HtmlCtr::getSelectCtrImp(CtrHelper::getDoctorMenzhenStatusCtrArray(),"menzhen_offset_daycnt",$menzhen_offset_daycnt,'js-select2 form-control'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
					        <input type="submit" class="btn btn-primary btn-block" value="筛选">
                        </div>
                    </div>
                </form>
                <div class='mt10'>
                <?php if($isshowall){ ?>
                    <a id="isshowall_false">隐藏零患者的医生</a>
                <?php }else{ ?>
                    <a id="isshowall_true">显示全部</a>
                <?php } ?>

               	    <a class="btn btn-success" href="/doctormgr/listwoy">按周统计</a>
               	    <span class="btn btn-default marketCellBtn" href="/doctormgr/listwoy">隐藏</span>
                </div>
                <div class='mt10'>
                    颜色说明:
                    <span class="orange">橙色:市场</span>
                    <span class="red">红色:有效报到</span>
                    <span class="blue">蓝色:全部报到(有效+删除+审核中)</span>
                    <span class="green">绿色:扫码报到+仅扫码+仅报到</span>
                </div>
				<div class='mt10'>
                    医生颜色说明:医生名字标红为开通礼来医生
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>id</td>
                        <td width="60">姓名</td>
                        <td>医院</td>
                        <td>疾病</td>
                        <td>创建时间</td>
                        <td>市场责任人</td>
                        <td width="150">订单</td>
                    <?php foreach ($months as $i => $v) { ?>
                        <td><?=$months[$i] ?></td>
                    <?php } ?>
                    </tr>
                </thead>
                <tbody>
<?php
$indexnum = 1;
$_ii = 0;
foreach ($doctors as $a) {
    $_ii ++;

    $cnt0 = $patientRptGroupbyDoctorMonth_market[$a->id][$months[4]];
    $cnt1 = $patientRptGroupbyDoctorMonth[$a->id][$months[4]];
    $cnt2 = $patientRptGroupbyDoctorMonth_all[$a->id][$months[4]];
    $cnt3 = $wxuserRptGroupbyDoctorMonth[$a->id][$months[4]];
    if (empty($cnt0)) {
        $cnt0 = 0;
    }

    if (empty($cnt1)) {
        $cnt1 = 0;
    }

    if (empty($cnt2)) {
        $cnt2 = 0;
    }

    if (empty($cnt3)) {
        $cnt3 = 0;
    }
    if ((0 == $isshowall) && ($cnt2 == 0) && ($cnt3 == 0)) {
        continue;
    }
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
                        <td><?= $a->getDiseaseNamesStr() ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->marketauditor->name ?> </td>
                        <td>
                        	<?php foreach(ShopOrderDao::getIsPayShopOrderCntArrByDoctor($a) as $shopOrderCntArr){ ?>
								<p><a target="_blank" href="/shopordermgr/list?doctorid=<?= $a->id ?>&startdate=<?= $shopOrderCntArr["themonth"] ?>-01"><?= $shopOrderCntArr["themonth"] ?> -- <?= $shopOrderCntArr["cnt"]?>单</a></p>
                        	<?php } ?>
                        </td>

<?php
    for ($i = 0; $i < count($months); $i ++) {
        $m = $months[$i];

        if ($m == 'beforecnt') {
            $fromdate = "";
            $todate = $months[$i - 1] . "-01";
        } elseif ($m == 'allcnt') {
            $fromdate = "";
            $todate = "";
        } else {
            list ($year, $month) = explode("-", $m);

            if ($month == 12) {
                $monthnext = 1;
                $yearnext = $year + 1;
            } else {
                $monthnext = $month + 1;
                $yearnext = $year;
            }

            if ($monthnext < 10) {
                $monthnext = "0" . $monthnext;
            }

            $fromdate = $m . "-01";
            $todate = $yearnext . "-" . $monthnext . "-01";
        }
        $url_date = '&fromdate=' . $fromdate . '&todate=' . $todate;

        $cnt0 = $patientRptGroupbyDoctorMonth_market[$a->id][$m];
        $cnt1 = $patientRptGroupbyDoctorMonth[$a->id][$m];
        $cnt2 = $patientRptGroupbyDoctorMonth_all[$a->id][$m];
        $cnt3 = $wxuserRptGroupbyDoctorMonth[$a->id][$m];
        if (empty($cnt0)) {
            $cnt0 = 0;
        }

        if (empty($cnt1)) {
            $cnt1 = 0;
        }

        if (empty($cnt2)) {
            $cnt2 = 0;
        }

        if (empty($cnt3)) {
            $cnt3 = 0;
        }
        ?>
                        <td>
                            <a target="_blank" href="/patientmgr/listcond?doctorid=<?=$a->id ?>&statusstr=market&<?=$url_date ?>" class="orange marketCell"><?= $cnt0 ?></a>
                            <a target="_blank" href="/patientmgr/listcond?doctorid=<?=$a->id ?>&statusstr=effective&<?=$url_date ?>" class="red"><?= $cnt1 ?></a>
                            <a target="_blank" href="/patientmgr/listcond?doctorid=<?=$a->id ?>&statusstr=all&<?=$url_date ?>" class="blue"><?= $cnt2 ?></a>
                            <a target="_blank" href="/wxusermgr/list?doctorid=<?=$a->id ?>&<?=$url_date ?>" class="green"><?= $cnt3  ?></a>
                        </td>
<?php
    }
    ?>
                    </tr>
<?php
}
?>
                	<tr style="background-color: #CCFFFF">
                        <td></td>
                        <td></td>
                        <td>总计</td>
                        <td></td>
                        <td></td>
                        <td></td>
						<td></td>
                        <td></td>
<?php
for ($i = 0; $i < count($months); $i ++) {
    $m = $months[$i];

    if ($m == 'beforecnt') {
        $fromdate = "";
        $todate = $months[$i - 1] . "-01";
    } elseif ($m == 'allcnt') {
        $fromdate = "";
        $todate = "";
    } else {
        list ($year, $month) = explode("-", $m);

        if ($month == 12) {
            $monthnext = 1;
            $yearnext = $year + 1;
        } else {
            $monthnext = $month + 1;
            $yearnext = $year;
        }

        if ($monthnext < 10) {
            $monthnext = "0" . $monthnext;
        }

        $fromdate = $m . "-01";
        $todate = $yearnext . "-" . $monthnext . "-01";
    }
    $url_date = '&fromdate=' . $fromdate . '&todate=' . $todate;

    $cnt0 = $monthPatientMarketCnts[$m];
    $cnt1 = $monthPatientCnts[$m];
    $cnt2 = $monthPatientAllCnts[$m];
    $cnt3 = $monthWxUserCnts[$m];
    if (empty($cnt0)) {
        $cnt0 = 0;
    }

    if (empty($cnt1)) {
        $cnt1 = 0;
    }

    if (empty($cnt2)) {
        $cnt2 = 0;
    }

    if (empty($cnt3)) {
        $cnt3 = 0;
    }
    ?>
                        <td>
                            <a target="_blank" href="/patientmgr/listcond?statusstr=market&hospitalid=<?=$hospitalid ?>&auditorid_market=<?=$auditorid_market ?>&<?=$url_date ?>" class="orange"><?= $cnt0 ?></a>
                            <a target="_blank" href="/patientmgr/listcond?statusstr=effective&hospitalid=<?=$hospitalid ?>&auditorid_market=<?=$auditorid_market ?>&<?=$url_date ?>" class="red"><?= $cnt1 ?></a>
                            <a target="_blank" href="/patientmgr/listcond?statusstr=all&hospitalid=<?=$hospitalid ?>&auditorid_market=<?=$auditorid_market ?>&<?=$url_date ?>" class="blue"><?= $cnt2 ?></a>
                            <a target="_blank" href="/wxusermgr/list?hospitalid=<?=$hospitalid ?>&auditorid_market=<?=$auditorid_market ?>&<?=$url_date ?>" class="green"><?= $cnt3 ?></a>
                        </td>
<?php
}
?>
                	</tr>
                </tbody>
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>id</td>
                        <td width="60">姓名</td>
                        <td>医院</td>
                        <td>疾病</td>
                        <td>创建时间</td>
                        <td>市场责任人</td>
                    <?php foreach ($months as $i => $v) { ?>
                        <td><?=$months[$i] ?></td>
                    <?php } ?>
                    </tr>
                    <tr>
                        <td colspan=100 class='pagelink'>
<?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
                </thead>
            </table>
            </div>
            <div class='mt10'>
                颜色说明:
                <span class="orange">橙色:市场</span>
                <span class="red">红色:有效报到</span>
                <span class="blue">蓝色:全部报到(有效+删除+审核中)</span>
                <span class="green">绿色:扫码报到+仅扫码+仅报到</span>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $('.js-select2').select2();
    $("#isshowall_true").on("click", function () {
        var url = location.pathname + '?isshowall=1';
        window.location.href = url;
    });
    $("#isshowall_false").on("click", function(){
        var url = location.pathname + '?isshowall=0';
        window.location.href = url;
    });
	$(function(){
		$(".marketCellBtn").on("click", function(){
			$("tr").each(function(){
				var me = $(this);
				var marketCells = me.find(".marketCell");
				if(marketCells.length){
					var second = marketCells.eq(1);
					if( second.text() == "0" ){
						me.hide();
					}
				}
			});
		})
	})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
