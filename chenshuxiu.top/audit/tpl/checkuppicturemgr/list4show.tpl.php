<?php
$pagetitle = "微信服务号[{$wxshop->name}]菜单";
$cssFiles = [
    $img_uri . '/static/css/jquery-ui.autocomplete.min.css?v=20180208',
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/static/js/vendor/jquery-ui.autocomplete.min.js?v=20180208',
    $img_uri . '/v5/common/select_doctor.js?v=20180530',
    $img_uri . '/v5/common/search_patient.js?v=20180530',
]; // 填写完整地址
$pageStyle = <<<STYLE
        .trOnSeleted {
            background-color: #CCCCFF;
        }

        .trOnMouseOver {
            background-color: #CCCCFF;
        }

        .isclosed_objid {
            background-color: #e6e6e6
        }

        .isclosed {
            background-color: #eeeeee
        }

        .objid {
            background-color: #dff8df
        }

STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12" id="top">
    <section class="col-md-6 content-left">
        <?php
        $pagetitle = "报告图片列表";
        include $tpl . "/_pagetitle.php";
        ?>
        <div class=searchBar>
            <input type="hidden" name="revisitrecordid" value="<?= $revisitrecordid?>">
            <form action="/checkuppicturemgr/list4show" method="get" class="pr">
                <div style="display: inline-block;">
                    <label for="">医生：</label>
                    <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                </div>
                <div class="mt10">
                    <label for="">按患者姓名：</label>
                    <?php include_once $tpl . '/_search_patient.tpl.php'; ?>
                </div>
                <div class="mt10">
                    <input type="submit" class="btn btn-success" value="组合刷选" />
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
            <thead>
            <tr>
                <th>患者姓名</th>
                <th>所属医生</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($patients as $a ){
                ?>
                <tr onmouseover="over(this)" onmouseout="out(this)" onclick="showcheckuppicture(this)" data-patientid="<?=$a->id?>">
                    <td><?= $a->name ?></td>
                    <td>
                        <?php
                        $pcards = $a->getPcards();
                        foreach( $pcards as $pcard ){
                            echo "{$pcard->doctor->name} \n";
                        }
                        ?>
                    </td>
                    <td>
                        <a href="#top">详情(<?=$a->getOpenCheckupPictureCnt()?>未录)</a>
                    </td>
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
    <section class="col-md-6 content-right pb10">
        <div id="checkupPictureHtmlShell" class="contentBoxBox"></div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
    function over(tr){
        var className = $(tr).attr('id');
        $(tr).removeClass(className).addClass('trOnMouseOver');
    }
    function out(tr){
        var className = $(tr).attr('id');
        $(tr).removeClass('trOnMouseOver').addClass(className);
    }
    function showcheckuppicture(tr){
        $("tr.trOnSeleted").removeClass("trOnSeleted").removeClass("trOnMouseOver");
        $(tr).addClass("trOnSeleted");

        $(".content-right").show();
        var me = $(tr);
        var patientid = me.data("patientid");

        $("#checkupPictureHtmlShell").html('');
        $.ajax({
            "type" : "get",
            "data" : {
                patientid : patientid
            },
            "dataType" : "html",
            "url" : "/checkuppicturemgr/onehtml4show",
            "success" : function(data) {
                $("#checkupPictureHtmlShell").html(data);
            }
        });
    }
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
