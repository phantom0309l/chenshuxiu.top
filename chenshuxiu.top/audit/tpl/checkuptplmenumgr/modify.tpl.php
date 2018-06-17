<?php
$pagetitle = "修改疾病菜单";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/checkuptplmenumgr/modifypost" method="post">
                <input type="hidden" name="diseaseid" value="<?=$checkupTplMenu->disease->id?>" />
                <input type="hidden" name="doctorid" value="<?=$checkupTplMenu->doctor->id?>" />
                <input type="hidden" name="checkuptplmenuid" value="<?=$checkupTplMenu->id?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th>绑定所属疾病:</th>
                        <td id="td-disease">
                            <?=$checkupTplMenu->disease->name?>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>医生:</th>
                        <td>
                            <?=$checkupTplMenu->doctor->name?>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>全菜单:</th>
                        <td>
                        <textarea cols=120 rows=12 name="content"><?=$checkupTplMenu->content?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>精简版菜单:</th>
                        <td>
                        <div>
                            <input class="copy btn btn-success" value="快速拷贝">
                        </div><br>
                        <div>
                            <textarea cols=120 rows=12 name="simple_content"><?=$checkupTplMenu->simple_content?></textarea>
                        </div>
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
<?php
$footerScript = <<<STYLE
    $(function(){
        $(".copy").on("click",function(){
            var content = $("textarea[name='content']").val();

            $("textarea[name='simple_content']").val(content);
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>