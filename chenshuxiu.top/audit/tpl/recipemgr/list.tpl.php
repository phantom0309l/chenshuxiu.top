<?php
$pagetitle = '运营系统首页';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12" id="top">
    <section class="col-md-12 content-left">
                <div class=searchBar>
                    <form action="/recipemgr/list" method="get" class="pr">
                        <div class="form-group">
                            <label>按上传图片时间: </label>
                            从
                            <input type="text" class="calendar fromdate" style="width: 100px" name="fromdate" value="<?= $fromdate ?>" />
                            到
                            <input type="text" class="calendar todate" style="width: 100px" name="todate" value="<?= $todate ?>" />
                        </div>
                        <div class="form-group">
                            <div class="">
                                <?= HtmlCtr::getRadioCtrImp4OneUi(array(1 => "有效", 0 => "无效",),'status', $status, 'css-radio-success')?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">按患者姓名/拼音/手机/id:</label>
                            <input type="text" name="keyword" value="<?= $keyword ?>" />
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-success" value="组合刷选" />
                        </div>
                    </form>
                </div>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
                    <thead>
                    <tr>
                        <th>患者id</th>
                        <th>患者姓名</th>
                        <th>所属医生</th>
                        <th>上传时间</th>
                        <th>归档状态</th>
                        <th>缩略图</th>
                        <th>备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($recipes as $recipe ){
                        ?>
                        <tr>
                            <td><?= $recipe->patientid ?></td>
                            <td><?= $recipe->patient->name ?></td>
                            <td><?= $recipe->patient->doctor->name ?></td>
                            <td><?= $recipe->getCreateDay() ?></td>
                            <td><?= 1 == $recipe->status ? "有效" : "无效" ?></td>
                            <td>
                                <?php if( $recipe->picture instanceof Picture ){?>
                                    <img src="<?= $recipe->picture->getSrc(150, 150, false) ?>" alt="" />
                                <?php }?>
                            </td>
                            <td><?= $recipe->remark ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                    </tr>
                    </tbody>
                </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
