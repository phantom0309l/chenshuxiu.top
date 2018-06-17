<?php
$pagetitle = "详情";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/_ENTITYNAMELOWER_mgr/modifypost" method="post">
            <input type="hidden" value="<?= $_ENTITYNAMELOWERFIRST_->id ?>" name="_ID_"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                _TRSTR_
            </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
