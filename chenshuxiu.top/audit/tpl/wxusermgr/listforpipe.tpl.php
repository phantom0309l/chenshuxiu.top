<?php
$pagetitle = "微信号列表流综合页";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "{$img_uri}/v3/js/amr/amrnb.js",
    "{$img_uri}/v3/js/amr/amritem.js",
    "{$img_uri}/v3/audit_wxusermgr_listforpipe.js?v=2018050901",
    "{$img_uri}/v5/common/wxvoicemsg_content_modify.js?v=20171208",
    "{$img_uri}/v5/common/dealwithtpl.js?v=2018050401"]; // 填写完整地址
$pageStyle = <<<STYLE
#main-container {
    background: #f5f5f5 !important;
}
.trOnSeleted {
    background-color: #e6e6fa;
}

.trOnMouseOver {
	background-color: #e6e6fa;
}

.content-right{
	visibility: hidden;
}

#goTop {
	position: fixed;
	bottom: 20px;
	width: 40px;
	height: 40px;
	background: #ddd;
	padding: 5px 10px;
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
.content-left label.control-label {
    padding-right: 0;
    font-weight: 500;
    width: 100px;
}
STYLE;
$pageScript = <<<SCRIPT
        $(function () {
            $(".showWxUserOneHtml").on("click", function () {
                $("tr.trOnSeleted").removeClass("trOnSeleted").removeClass("trOnMouseOver");
                $(this).parents("tr").addClass("trOnSeleted");
            });
        });
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12 contentShell">
    <section class="col-md-6 content-left">
        <div class="bg-white" style="padding: 10px;">
            <form action="/wxusermgr/listforpipe" method="get" class="form form-horizontal">
                <div class="form-group">
                    <label class="control-label col-md-3">关注时间 </label>
                    <div class="col-md-9">
                        <div class="input-daterange input-group">
                            <input class="form-control calendar" type="text" name="fromdate" placeholder="开始时间" value="<?=$fromdate?>">
                            <span class="input-group-addon">
                                <i class="fa fa-chevron-right"></i>
                            </span>
                            <input class="form-control calendar" type="text" name="todate" placeholder="截至时间" value="<?=$todate?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">微信号/wxuserid</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="keyword" value="<?= $keyword ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">按医生姓名</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="doctor_name" value="<?= $doctor_name ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">报到 </label>
                    <div class="col-md-9">
                    <?php
                    $arr = array(
                        'notbaodao' => '未报到',
                        'isbaodao' => '已报到');
                    echo HtmlCtr::getRadioCtrImp4OneUi($arr, 'type', $type, 'css-radio-warning');
                    ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">患者是否发送过文本消息</label>
                    <div class="col-md-9">
                    <?php
                    $arr = array(
                        'all' => '全部',
                        1 => '发送过',
                        2 => '未发送');
                    echo HtmlCtr::getRadioCtrImp4OneUi($arr, 'is_sendtxtmsg', $is_sendtxtmsg, 'css-radio-warning');
                    ?>
                    </div>
                </div>
                <div class="form-group remove-margin-b">
                    <div class="col-md-9" style="margin-left: 100px;">
                        <input class='btn btn-success btn-minw shownotgroup' type="submit" value="组合筛选" />
                    </div>
                </div>
            </form>
        </div>
        <div class="block push-10-t">
            <div class="block-content">
                <div class="table-responsive">
                    <table class="table table-hover wxuserList">
                        <thead>
                            <tr>
                                <td>微信名</td>
                                <td>
                                    关注时间
                                    <br />
                                    <span class="gray">最后活跃</span>
                                </td>
                                <td>服务号</td>
                                <td>扫码医生</td>
                <?php if ($type == 'isbaodao') { ?>
                                <td>报到患者名</td>
                <?php } ?>
                                <td>查看流</td>
                            </tr>
                        </thead>
                        <tbody>
            <?php
            foreach ($wxusers as $a) {
                ?>
                            <tr>
                                <td><?= $a->nickname ?></td>
                                <td><?= $a->getCreateDay() ?>
                                    <br />
                                    <span class='gray'><?= substr($a->lastpipe_createtime, 0, 10) ?></span>
                                </td>
                                <td><?= $a->wxshop->name ?></td>
                                <td>
                <?php
                if ($a->doctor instanceof Doctor) {
                    echo $a->doctor->name;
                    echo "<br/><span class='gray f10'>{$a->doctor->hospital->name}</span>";
                }
                ?>              </td>
                <?php if ($type == 'isbaodao') { ?>
                                <td><?= $a->user->patient->name ?></td>
                <?php } ?>
                                <td>
                        <?php if ($type == 'isbaodao') { ?>
                                    <a href="/patientmgr/list?keyword=<?= $a->user->patient->id ?>" target="_blank">查看</a>
                        <?php } else { ?>
                                    <a class="showWxUserOneHtml wxuserid-<?= $a->id ?>" href="javascript:" data-wxuserid=<?= $a->id ?>>查看</a>
                        <?php } ?>
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
            </div>
        </div>
    </section>
    <section class="col-md-6 content-right bg-white pt10">
        <?php include_once $tpl . "/_pipelayout_wxuser.php"; ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<SCRIPT
    $(function () {
        //折叠隐藏
        $("div#pipeeventShellTitle").on("click", function () {
            $("div#pipeeventShell").toggle();
        })
    });
SCRIPT;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
