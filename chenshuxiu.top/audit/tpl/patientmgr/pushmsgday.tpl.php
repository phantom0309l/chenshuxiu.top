<?php
$pagetitle = "患者列表 / 报到列表";
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

    <div class="col-md-12">
        <section class="col-md-12">
		<div class="searchBar">
                <form action="/patientmgr/pushmsgday" method="get">
                    <label>员工: </label>
                    <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAllAuditorCtrArray(),"selectauditorid",$selectauditorid,"f18");?>

					<label>日期: </label>
                    <input type="text" class="calendar" style="width: 100px" name="date" value="<?= $date ?>" />
                    <label>随机显示的条数: </label>
                    <?php
                    $arr = array(
                        '3' => 3,
                        '4' => 4,
                        '5' => 5,
                        '6' => 6,
                        '7' => 7,
                        '8' => 8,
                        '9' => 9,
                        '10' => 10);
                    echo HtmlCtr::getSelectCtrImp($arr, "pushmsgnum", $pushmsgnum, "f18");
                    ?>

                    <input type="submit" value="搜索" />
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th width="100">运营</th>
                        <th width="100">患者</th>
                        <th width="200">时间</th>
                        <th>回复内容</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($pushmsgs as $a) {
                        ?>
                    <tr>
                        <td><?=$a->sendbyobj->name ?></td>
                        <td><?=$a->patient->name ?></td>
                        <td><?=$a->createtime?></td>
                        <td><?=$a->content   ?></td>
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
$footerScript = <<<XXX
$(document).ready(function(){
	$("#cleardate").on("click",function(){
		$(".calendar").val('');
		return false;
	});
});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
