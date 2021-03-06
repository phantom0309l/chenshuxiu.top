<?php
$pagetitle = "出诊日历 Schedules";
$sideBarMini = true;
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
.searchBar .form-group label {
    font-weight: 500;
    width: 9%; 
    text-align: left;
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
            <?php if($scheduletpl){ ?>
            <div class="searchBar">
                    <a class="btn btn-success" href="/schedulemgr/BatCreateByScheduleTpl?scheduletplid=<?=$scheduletpl->id ?>">批量生成实例至2020-12-31</a>
            </div>
            <?php } ?>
            <div class="searchBar">
            <form  class="form form-horizontal" action="/schedulemgr/list" method="get" class="pr">
                <div class="form-group">
                    <label class="col-md-2 control-label">医生: </label>
                    <div class="col-md-3">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">时间</label> 
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="fromdate" value="<?=$fromdate?>" placeholder="开始时间">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="todate" value="<?=$todate?>" placeholder="结束时间">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?= $doctor->name ?> 的 模板: </label>
                    <div class="col-md-3">
                    <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toScheduleTplCtrArray($scheduletpls,true),"scheduletplid",$scheduletpl->id, "form-control"); ?>
                    </div>
                </div>
                <div class="form-group">
                <div class="col-md-3">
                <input type="submit" class="btn btn-primary btn-minw" value="筛选" />
                </div>
                </div>
            </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>模板id</th>
                        <th>医生</th>
                        <th>疾病</th>
                        <th>出诊日期</th>
                        <th>星期</th>
                        <th>时刻</th>
                        <th>类型</th>
                        <th>
                            最大
                            <br />
                            库存
                        </th>
                        <th>
                            有效
                            <br />
                            加号
                        </th>
                        <th>
                            全部
                            <br />
                            加号
                        </th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($schedules as $a) {
                        ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td>
                            <a href="/schedulemgr/list?scheduletplid=<?= $a->scheduletplid ?>"><?= $a->scheduletplid ?></a>
                        </td>
                        <td>
                            <a href="/schedulemgr/list?doctorid=<?= $a->doctorid ?>"><?= $a->doctor->name ?></a>
                        </td>
                        <td><?= $a->disease->name ?></td>
                        <td><?= $a->thedate ?></td>
                        <td><?= $a->getDowStr() ?></td>
                        <td><?= $a->getDaypartStr() ?></td>
                        <td><?= $a->getTkttypeStr() ?></td>
                        <td><?= $a->maxcnt ?></td>
                        <td><?= $a->getRevisitTktCnt(1) ?></td>
                        <td><?= $a->getRevisitTktCnt() ?></td>
                        <td><?= $a->getStatusStrWithColor() ?></td>
                        <td>
                            <a target="_blank" class="btn btn-xs btn-default" href="/schedulemgr/modify?scheduleid=<?= $a->id ?>"><i class="fa fa-pencil"></i></a>
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
