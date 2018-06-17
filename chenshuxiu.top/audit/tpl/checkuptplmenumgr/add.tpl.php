<?php
$pagetitle = "新建疾病菜单";
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
            <form action="/checkuptplmenumgr/addpost" method="post">
                <input type="hidden" name="diseaseid" value="<?=$disease->id?>" />
                <input type="hidden" name="doctorid" value="<?=$doctor->id?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th>绑定所属疾病:</th>
                        <td id="td-disease">
                            <?=$disease->name?>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>医生:</th>
                        <td>
                            <?=$doctor->name?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input id='input-submit' type="submit" class="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
