<?php
$pagetitle = "模板消息 MsgTemplates";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
.register {
	color: #0066ff
}

.saoma {
	color: #0caf2f
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<div class="col-md-12">
    <section class="col-md-12">
		<div class="searchBar" style="border:none">
			<a class="btn btn-primary" target="_blank" href="/msgtemplatemgr/add">新建模板</a>
		</div>
		<div class="searchBar" style="border:none">
            <form class="form-horizontal pr" action="/msgtemplatemgr/list" method="get">
                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-1 col-sm-1 control-label label-width" for="">医生</label>
                    <div class="col-md-2 col-sm-1">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>

					<label class="col-md-1 col-sm-1 control-label label-width" for="">疾病</label>
                    <div class="col-md-2 col-sm-1">
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toDiseaseCtrArray($diseases), 'diseaseid', $diseaseid, "form-control"); ?>
                    </div>

                    <div class="col-md-1 col-sm-1">
                        <button class="btn btn-sm btn-success" type="submit">组合筛选</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
            <thead>
                <tr>
                    <th width="100">id</th>
                    <th width="100">标题</th>
                    <th width="110">ename</th>
                    <th width="120">疾病</th>
                    <th width="80">医生</th>
                    <th>content</th>
                    <th width="100">操作</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($msgtemplates as $a) {
                ?>
                    <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->title ?></td>
                    <td>
                        <a href="/msgtemplatemgr/list?ename=<?= $a->ename ?>"><?= $a->ename ?></a>
                    </td>
                    <td>
                        <a href="/msgtemplatemgr/list?diseaseid=<?= $a->diseaseid ?>"><?= $a->disease->name ?></a>
                    </td>
                    <td>
                        <a href="/msgtemplatemgr/list?doctorid=<?= $a->doctorid ?>"><?= $a->doctor->name ?></a>
                    </td>
                    <td>
                        <div class="searchBar" style="text-align: left">
                            <?= nl2br($a->content); ?>
                        </div>
                    </td>
                    <td>
                        <a class="btn btn-primary" href="/msgtemplatemgr/modify?msgtemplateid=<?=$a->id?>" target="_blank">修改</a>
                    </td>
                </tr>
                <?php
            }
            ?>
            <?php if(!empty($pagelink)){?>
                <tr>
                    <td colspan=100 class="pagelink">
                        <?php include $dtpl."/pagelink.ctr.php";  ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>

<?php
$footerScript = <<<STYLE
STYLE;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
