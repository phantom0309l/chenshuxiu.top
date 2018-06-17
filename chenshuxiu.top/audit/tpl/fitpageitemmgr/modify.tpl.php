<?php
$pagetitle = "元素修改 {$fitpageitem->fitpagetplitem->name}  of " . $fitpageitem->fitpage->fitpagetpl->name;
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
    <form method="post" action="/fitpageitemmgr/modifypost">
                <input type="hidden" name="fitpageitemid" value="<?=$fitpageitem->id ?>" />
        <div class="table-responsive">
            <table class="table table-bordered">
                    <tr>
                        <th>name</th>
                        <td>
                            <?=$fitpageitem->fitpagetplitem->name ?>
                        </td>
                    </tr>
                    <tr>
                        <th>content</th>
                        <td>
                            <textarea name="content" rows="10" cols="50"><?=$fitpageitem->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交">
                        </td>
                    </tr>
                </table>
        </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
