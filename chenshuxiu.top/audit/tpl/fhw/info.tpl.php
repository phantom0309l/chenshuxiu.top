<?php
$pagetitle = "运营创建任务";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .form-group label {
    font-weight: 500;
    width: 9%;
    text-align: left;
}
.padding-left-right{
    padding-left: 4px;
    padding-right: 4px;
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/fhw/infopost" method="post">
                <?php echo HtmlCtr::getAddressCtr4New('hospital'); ?>
                <input type="submit" class="btn btn-success" value="提交">
            </form>
        </section>
    </div>

    <button id="btn-click" class="btn btn-minw btn-square btn-primary" type="button">Primary</button>
    <script>
        $(function () {


            $('#btn-click').on('click', function () {
                alert('================');
            });

            $('#btn-click').trigger("click");
        });
    </script>

<div class="block-content">
    <?php
    $optasktpls = OpTaskTplDao::getList();
    echo HtmlCtr::getSelectCtrImp(CtrHelper::toOptaskTplCtrArray($optasktpls), 'optasktplid', '0', 'js-select2 form-control')
    ?>
</div>

    <div class="clear"></div>

<script>
    $(function(){
        App.initHelper('select2');

        $('#optasktplid').on('change', function () {
            console.log('++++++++++++++++++');
            var optasktplid = $("#optasktplid").val();

            console.log(optasktplid, "===========");
        });

    });
</script>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
