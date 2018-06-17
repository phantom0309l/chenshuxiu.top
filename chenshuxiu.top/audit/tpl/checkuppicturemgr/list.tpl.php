<?php
$pagetitle = "报告图片量表答卷录入";
$cssFiles = [
    $img_uri . '/static/css/jquery-ui.autocomplete.min.css?v=20180208',
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/static/js/vendor/jquery-ui.autocomplete.min.js?v=20180208',
    $img_uri . '/v5/common/select_doctor.js?v=20180530',
    $img_uri . '/v5/common/search_patient.js?v=20180530',
]; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
            <div class="searchBar">
                <form action="/checkuppicturemgr/list" method="get" class="form-horizontal pr">
                    <div class="form-group">
                        <label class="col-xs-12" for="">医生</label>
                        <div class="col-xs-3">
                            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12" for="patient_name">患者姓名</label>
                        <div class="col-xs-3">
                            <?php include_once $tpl . '/_search_patient.tpl.php'; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <button class="btn btn-sm btn-success" type="submit">组合筛选</button>
                        </div>
                    </div>
                </form>
            </div>
            <section class="col-md-2">
                <div class="table-responsive">
                    <table class="table table-bordered tdcenter">
                    <thead>
                    <tr>
                        <th>医生</th>
                        <th>患者</th>
                        <th>创建日期</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($checkuppictures as $a) {
                        ?>
                        <tr>
                            <td><?= $a->doctor->name ?></td>
                            <td>
                                <?php
                                    if ($a->patient instanceof Patient) {
                                        echo $a->patient->getMaskName();
                                    }
                                ?>
                            </td>
                            <td><?= $a->getCreateDay(); ?></td>
                            <td>
                                <div class="mid-picture-box-btn btn btn-primary" data-checkuppictureid="<?= $a->id ?>">详情</div>
                                <a class="btn btn-default" href="/checkuppicturemgr/changestatusPost?checkuppictureid=<?= $a->id ?>" >
                                    <?=  $a->status == 1 ? "已录完" :  "未录";?>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </section>
            <section class="col-md-6">
                <div class="mid-picture-box"></div>
            </section>
            <section class="col-md-4">
                <div class="right-checkup-box"></div>
            </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
        $(".mid-picture-box-btn").on("click",function(){
            var me = $(this);
            $("tr.bg-info").removeClass("bg-info");
            me.parent().parent().addClass("bg-info");

            var checkuppictureid = me.data("checkuppictureid");
            $.ajax({
                "type" : "post",
                "data" : {
                    checkuppictureid : checkuppictureid
                },
                "dataType" : "html",
                "url" : "/checkuppicturemgr/onehtml4pic",
                "success" : function(data) {
                    $(".mid-picture-box").html(data);
                }
            });
            $.ajax({
                "type" : "post",
                "data" : {
                    checkuppictureid : checkuppictureid
                },
                "dataType" : "html",
                "url" : "/checkuppicturemgr/checkuphtml",
                "success" : function(data) {
                    $(".right-checkup-box").html(data);
                }
            });
        });
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
