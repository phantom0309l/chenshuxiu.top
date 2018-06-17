<?php
$pagetitle = "门诊实例修改";
$sideBarMini = true;
$breadcrumbs = [ 
    "/schedulemgr/list" => "出诊表实例",
    "/schedulemgr/list?doctorid={$doctor->id}" => "门诊实例",
];
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .form-group label {
    font-weight: 500;
    width: 9%; 
    text-align: left;
}

STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
    <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php";?>
    <div class="content-div">
        <section class="col-md-12">
            <form action="/schedulemgr/modifypost" method="post">
                <input type="hidden" name="scheduleid" value="<?= $schedule->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <td width=140>scheduleid</td>
                        <td><?= $schedule->id ?></td>
                    </tr>
                    <tr>
                        <td>创建时间</td>
                        <td><?= $schedule->createtime ?></td>
                    </tr>
                    <tr>
                        <td>模板id</td>
                        <td><?= $schedule->scheduletplid ?> <?= $schedule->scheduletpl->getStatusStrWithColor(); ?></td>
                    </tr>
                    <tr>
                        <td>医生</td>
                        <td><?= $schedule->doctor->name ?></td>
                    </tr>
                    <tr>
                        <td>医生</td>
                        <td><?= $schedule->doctor->name ?></td>
                    </tr>
                    <tr>
                        <td>出诊日期</td>
                        <td><?= $schedule->thedate ?></td>
                    </tr>
                    <tr>
                        <td>星期</td>
                        <td><?= $schedule->getDowStr() ?></td>
                    </tr>
                    <tr>
                        <td>时刻</td>
                        <td><?= $schedule->getDaypartStr() ?></td>
                    </tr>
                    <tr>
                        <td>类型</td>
                        <td><?= $schedule->getTkttypeStr() ?></td>
                    </tr>
                    <tr>
                        <td>已加号</td>
                        <td>
                            <span class="blue"><?= $schedule->getRevisitTktCnt(1); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>最大库存号数</td>
                        <td>
                            <div class="col-md-5 remove-padding">
                            <input class="form-control" type="text" name="maxcnt" value="<?= $schedule->maxcnt ?>" />
                            </div>
                            <span class="text-warning push-20-l" style="line-height: 2.2"> 0 表示没有限制</span>
                        </td>
                    </tr>
                    <tr>
                        <td>状态</td>
                        <td>
                            <?php echo HtmlCtr::getRadioCtrImp4OneUi(XConst::$Statuss, 'status', $schedule->status,'css-radio-warning '); ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input class="btn btn-primary btn-minw" type="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
