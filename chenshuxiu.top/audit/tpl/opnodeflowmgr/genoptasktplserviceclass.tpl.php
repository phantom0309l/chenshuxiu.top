<?php
$pagetitle = "节点流向 of {$optasktpl->title} OpNodeFlow";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="col-md-12" style="overflow-x: auto">
            <div class="form-group">
                <label class="col-xs-12" for="example-textarea-input">OpTaskTpl_<?=$optasktpl->code?>_<?=$optasktpl->subcode?>.php</label>
                <div class="col-xs-12">
                    <textarea class="form-control" id="example-textarea-input" name="example-textarea-input" rows="100" placeholder="Content.."><?= $classstr ?></textarea>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>