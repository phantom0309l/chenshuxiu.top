<?php
$pagetitle = '患者列表流页面';
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . '/v5/plugin/speech/speech-input.css?v=2018022201',
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.css?v=20170829']; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . "/v3/js/amr/amrnb.js",
    $img_uri . "/v3/js/amr/amritem.js",
    $img_uri . '/v5/plugin/speech/speech-input.js?v=2018022202',
    $img_uri . "/v3/audit_patientmgr_list.js?v=2018022201",
    $img_uri . "/v5/common/wxvoicemsg_content_modify.js?v=20171208",
    $img_uri . "/v5/common/dealwithtpl.js?v=2018050401",
    $img_uri . "/v5/common/pipelevelfix.js?v=20171222",
    $img_uri . "/v5/common/setMedicineBreakDate.js?v=20171019",
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.js?v=20170829',
    $img_uri . "/v5/plugin/echarts/echarts.js"]; // 填写完整地址
/* {{{ */
$pageStyle = <<<STYLE
#chartShell {
	width: 100%;
	/* height: 800px; */
	overflow: auto;
}

.trOnSeleted {
	background-color: #e6e6fa;
}

.trOnMouseOver {
	background-color: #e6e6fa;
}

#goTop {
	position: fixed;
	bottom: 20px;
	width: 40px;
	height: 40px;
	padding: 5px 10px;
    color:#3169b1;
    cursor: pointer;
}

#pipeShell {
	padding-bottom: 30px;
}

#answersheet {
	z-index: 100
}

/*tab*/
.tab-menu {
	height: 30px;
	border-bottom: 1px solid #ddd;
	margin: 0px;
	padding: 0px;
	amrnb
}

li {
	margin: 0px;
	padding: 0px;
}

.tab-menu li {
	list-style: none;
	float: left;
	width: 100px;
	height: 30px;
	border: 1px solid #ddd;
	line-height: 30px;
	text-align: center;
	margin-left: 5px;
	background: #f5f5f5;
	cursor: pointer;
}

.tab-menu li.active {
	border-bottom: 1px solid #fff;
	background: #fff;
}

.mt10 {
	margin-top: 10px;
}

.mb10 {
	margin-bottom: 10px;
}

.ml10 {
	margin-left: 10px;
}

.remarkEventBox {
	margin-bottom: 10px;
}

.remarkEventBox-ta {
	width: 80%;
	border: 1px solid #ccc;
	padding: 5px;
	background: #fff;
	margin-bottom: 5px;
}

.contentBoxTitle {
	border-top: 1px solid #5c90d2;
	padding: 5px 10px 8px 10px;
	background: #eeeeee;
	margin-top: 10px;
}

.contentBoxBox1 {
	border-left: 1px solid #ddd;
	border-right: 1px solid #ddd;
	border-bottom: 1px solid #ddd;
	padding: 10px;
}

.colorBox {
	margin: 5px 0px;
	padding: 10px;
	background: #E6E6FA;
}

.pgroupBox {
	margin: 5px 0px;
	padding: 10px 10px 5px;
	background: #E6E6FA;
}

.pgroupBox button {
	margin: 0px 5px 5px 0px;
}

.grayBgColorBox {
	margin: 5px 0px;
	padding: 10px;
	background: #f9f9f9;
}

@media ( max-width : 600px) {
	.col-md-12,.col-md-6 {
		padding-left: 3px;
		padding-right: 3px;
	}
	.pipeeventTrigger {
		display: none;
	}
}

.remarkBox {
	margin-bottom: 10px;
}

.remarkBox-ta {
	width: 80%;
	border: 1px solid #ccc;
	padding: 5px;
	background: #f5f5f5;
	margin-bottom: 5px;
}

.showRemarkBox {
	background: #fff;
	padding: 8px 10px;
	border: 1px solid blue;
	z-index: 10;
	width: 400px;
	border-radius: 3px;
	left: 55px;
	top: 3px;
}

.search-btn {
    margin: 3px 0px;
}

.imgBrief img {
	width: 100%;
}

.pgroupid {
	width: 140px;
	border: 1px solid #ddd;
	height: 30px;
}

.content-right {
	visibility: hidden;
}

.typestrBox {
	margin-top: 25px;
}
.btnSection{ margin-top: 20px;}
.replySection{ margin-top: 20px;}
.reply-msg{ border:1px solid #ddd; padding: 5px; width: 100%; border-radius: 3px;}
STYLE;
/* }}} */
$pageScript = <<<SCRIPT
        function over(tr) {
            $(tr).addClass('trOnMouseOver');
        }
        function out(tr) {
            $(tr).removeClass('trOnMouseOver');
        }
        $(function () {
            $("#checkDoctor").on("change", function () {
                var val = parseInt($(this).val());
                var url = val == 0 ? location.pathname : location.pathname + '?doctorid=' + val;
                window.location.href = url;
            });
            $(".showPatientOneHtml").on("click", function () {
                $("tr.trOnSeleted").removeClass("trOnSeleted").removeClass("trOnMouseOver");
                $(this).parents("tr").addClass("trOnSeleted");
            });
        });
        $(function(){
            $('.js-select2').select2();
        })
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12 contentShell">
    <section class="col-md-5 content-left">
        <div class="searchBar">
            <form action="/patientmgr/list" method="get" class="pr">
                <label for="">按患者姓名/拼音/手机/id：</label>
                <input type="text" name="keyword" value="<?= $keyword ?>" />
                <input type="submit" value="搜索" />
            </form>
        </div>
        <div class="searchBar">
            <form action="/patientmgr/list" method="get" class="pr">
                <label for="">按医生姓名：</label>
                <input type="text" name="doctor_name" value="<?= $doctor_name ?>" />
                <input type="submit" value="搜索" />
            </form>
        </div>
            <?php if(1 == $mydisease->id){ ?>
                <div class="searchBar">
            <form action="/patientmgr/list" method="get" class="pr">
                <label>按患者报到时间: </label>
                从
                <input type="text" class="calendar fromdate" style="width: 100px" name="fromdate" value="<?= $fromdate ?>" />
                到
                <input type="text" class="calendar todate" style="width: 100px" name="todate" value="<?= $todate ?>" />
                <input type="submit" class="btn btn-success shownotgroup" value="查看" />
            </form>
        </div>
        <div class="searchBar">
            <div class="patient_stageShell">
                <label for="">患者类别：</label>
                <button class="btn search-btn patient_type <?= $patient_type == "all" ? 'btn-primary' : 'btn-default' ?>" value="all">全部</button>
                <button class="btn search-btn patient_type <?= $patient_type == "ishezuo" ? 'btn-primary' : 'btn-default' ?>" value="ishezuo">礼来项目</button>
                <button class="btn search-btn patient_type <?= $patient_type == "maybeinhezuo" ? 'btn-primary' : 'btn-default' ?>" value="maybeinhezuo">需审核至礼来项目</button>
                <button class="btn search-btn patient_type <?= $patient_type == "nothezuo" ? 'btn-primary' : 'btn-default' ?>" value="nothezuo">非礼来项目</button>
            </div>
            <div class="daycntShell">
                <label for="">报到天数：</label>
                <select class="daycnt js-select2" style="width: 160px;">
                    <option value="-1" <?= $daycnt == -1 ? "selected" : ""?>>全部</option>
                            <?php for ($i = 0; $i <= 168; $i++) { ?>
                                <option value="<?= $i ?>" <?= $daycnt == $i ? "selected" : ""?>>
                                    <?= $i ?>
                                </option>
                            <?php } ?>
                        </select>
            </div>
            <div class="patient_stageShell">
                <label for="">阶段：</label>
                <button class="btn search-btn pos <?= $pos == 0 ? 'btn-primary' : 'btn-default' ?>" value="0">全部</button>
                        <?php for ($i = 1; $i <= 7; $i++) { ?>
                            <button class="btn search-btn pos <?= $pos == $i ? 'btn-primary' : 'btn-default' ?>" value=<?= $i ?>>
                                <?= $i ?>
                            </button>
                        <?php } ?>
                    </div>
            <div class="stateShell">
                <label for="">当时跟进结果：</label>
                <button class="btn search-btn state <?= $state == "all" ? 'btn-primary' : 'btn-default' ?>" value="all">全部</button>
                <button class="btn search-btn state <?= $state == PatientDrugState::state_ondrug ? 'btn-primary' : 'btn-default' ?>" value="<?= PatientDrugState::state_ondrug ?>">服药</button>
                <button class="btn search-btn state <?= $state == PatientDrugState::state_nodrug ? 'btn-primary' : 'btn-default' ?>" value="<?= PatientDrugState::state_nodrug ?>">不服药</button>
                <button class="btn search-btn state <?= $state == PatientDrugState::state_stopdrug ? 'btn-primary' : 'btn-default' ?>" value="<?= PatientDrugState::state_stopdrug ?>">停药</button>
                <button class="btn search-btn state <?= $state == PatientDrugState::state_unknown ? 'btn-primary' : 'btn-default' ?>" value="<?= PatientDrugState::state_unknown ?>">未知</button>
            </div>
        </div>
        <div class="searchBar">
            <form action="/patientmgr/list" method="get" class="pr">
                <label>剩余药量可到: </label>
                <input type="text" class="calendar medicine_break_date" style="width: 100px" name="medicine_break_date" value="<?= $medicine_break_date ?>" />
                <input type="submit" class="btn btn-success medicine_break_date-search" value="查看" />
            </form>
        </div>
            <?php } ?>

            <div class="table-responsive">
            <table class="table border-top-blue patientList">
                <thead>
                    <tr>
                        <td>患者名</td>
                        <td>报到时间</td>
                        <td style="width: 25%">所属医生</td>
                        <td>行为</td>
                        <td>查看流</td>
                    </tr>
                </thead>
                <tbody>
            <?php

            foreach ($patients as $a) {
                $pcard = $a->getMasterPcard();
                ?>
                <tr onmouseover="over(this)" onmouseout="out(this)">
                        <td class="pr patientName">
                            <span><?= $a->getMaskName() ?></span>
                            <?php if( 1 != $a->status){?>
                                <br />
                            <span style="color: red;">
                                    <?=$a->getStatusStr()?>
                                </span>
                            <?php }?>
                            <div class="pa showRemarkBox none"><?= $a->opsremark; ?></div>
                        </td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $pcard->doctor->name ?><br />
                            <span class='gray f10'><?= $pcard->doctor->hospital->name ?></span>
                        </td>
                        <td><?= $a->getLastPipeToTagStrByUser() ?></td>
                        <td>
                            <a href="#goPatientBase" data-patientname="<?= $a->name ?>" data-patientid="<?= $a->id ?>" data-diseaseid="<?= $pcard->diseaseid ?>" data-doctorid="<?= $pcard->doctorid ?>" data-statusstr="<?=$a->getStatusStr() ?>" class="showPatientOneHtml patientid-<?= $a->id ?>">查看</a>
                        <?php
                if ($pcard->has_update == 1) {
                    ?>
                            <em class="red" id="new_<?= $a->id ?>">new!</em>
                        <?php
                } else {
                    ?>
                            <em class="" id="new_<?= $a->id ?>"></em>
                        <?php
                }
                ?>
                    </td>
                    </tr>
            <?php
            }
            ?>

                    <tr>
                        <td colspan=10>
                        <?php include $dtpl . "/pagelink.ctr.php"; ?>
                    </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <section class="col-md-7 content-right border1 pb10">
            <?php include_once $tpl . "/_pipelayout.php"; ?>
        </section>
</div>
<div class="clear"></div>
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
    <div class="slides"></div>
    <!--<h3 class="title"></h3>-->
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>
<div id='answersheet' class="col-md-4 pull-right none">
    <div class="panel panel-primary">
        <div class="panel-heading" id="answersheet-title"></div>
        <div id='details' class="panel-body"></div>
    </div>
    <span id="answersheet-close">x</span>
</div>
<div id="goTop" class="none">
    <span class="glyphicon glyphicon-plane" style="font-size:26px;"></span>
</div>
<?php include_once($tpl. "/_pipelevelfixbox.php"); ?>

<?php
$footerScript = <<<XXX
    $(function () {
        //折叠隐藏
        $("div#pipeeventShellTitle").on("click", function () {
            $("div#pipeeventShell").toggle();
        })
    });
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
