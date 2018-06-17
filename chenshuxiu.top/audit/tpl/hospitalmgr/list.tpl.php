<?php
$pagetitle = "医院列表 Hospitals";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="col-md-12" style="padding-left: 0px;padding-right: 0px;">
                <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;">
                    <a class="btn btn-sm btn-primary" target="_blank" href="/jkw_hospitalmgr/list">
                        <i class="fa fa-plus push-5-r"></i>医院新建
                    </a>
                </div>

                <div class="col-sm-11 col-xs-9">
                    <div class="col-sm-3" style="float: right; padding-right: 0px;">
                        <form class="form-horizontal push-5-t" action="/hospitalmgr/list" method="get">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" placeholder="搜索医院名" name="hospital_name" class="input-search form-inline form-control" value="<?=$hospital_name?>">
                                    <span class="input-group-btn" style="width: 1%; line-height: 35px;">
                                        <button type="submit" class="btn btn-primary">
                                            <span type="submit" aria-hidden="true" class="glyphicon glyphicon-search">
                                            </span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="clear">

                </div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td width="50">id</td>
                        <td>创建日期</td>
                        <td width="110">logo</td>
                        <td width="110">名片logo</td>
                        <td>
                            医院全称
                            <br />
                            <span class="gray">医院简称</span>
                        </td>
                        <td>地址</td>
                        <td>医院等级</td>
                        <td>医生数</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($hospitals as $a) {
                    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td>
                            <?php if ($a->logo_pictureid > 0) { ?>
                            <img src="<?=$a->logo_picture->getSrc(100,100) ?>">
                            <?php }?>
                        </td>
                        <td>
                            <?php if ($a->qr_logo_pictureid > 0) { ?>
                            <img src="<?=$a->qr_logo_picture->getSrc(100,100) ?>">
                            <?php }?>
                        </td>
                        <td><?= $a->name?>
                            <br />
                            <span class="gray"><?= $a->shortname ?></span>
                        </td>
                        <td title="<?= nl2br($a->getHospitalAddressStr()) ?>"><?= mb_substr(nl2br($a->getHospitalAddressStr()), 0, 3) ?></td>
                        <td><?= $a->levelstr ?></td>
                        <td>
                            <a href="/doctormgr/list?hospitalid=<?= $a->id ?>"><?= $a->getDoctorCnt(); ?>个</a>
                            <br />
                            <button href="#" class="btn showdoctor" data-id="<?= $a->id?>">查看该院医生</button>
                            <button href="#" class="btn hidedoctor" data-id="<?= $a->id?>">收起</button>
                            <div id="hospitaldoctors-<?= $a->id?>" class="doctorlist border1-blue">
                                <?php
                    $doctors = $a->getDoctors();
                    foreach ($doctors as $doctor) {
                        ?>
                                        <li><?= $doctor->name;?></li>
                                    <?php
                    }
                    ?>
                            </div>
                        </td>
                        <td>
                            <a href="/hospitalmgr/modify?hospitalid=<?= $a->id ?>">修改</a>
                            <br />
                            <a target="_blank" href="/hospitalmgr/oneforchangeauditormarket?hospitalid=<?= $a->id ?>">变更市场负责人</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function(){
    initpage();

    $(".showdoctor").on("click",function(){
        var me = $(this);
        var id = me.data("id");
        $("#hospitaldoctors-" + id).show();
    });

    $(".hidedoctor").on("click",function(){
        var me = $(this);
        var id = me.data("id");
        $("#hospitaldoctors-" + id).hide();
    });
});
function initpage(){
    $(".doctorlist").hide();
}
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
