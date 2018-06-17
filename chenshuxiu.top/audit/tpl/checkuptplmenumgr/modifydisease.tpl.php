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
            <form action="/checkuptplmenumgr/modifydiseasepost" method="post">
            <input type="hidden" name="checkuptplmenuid" value="<?=$checkupTplMenu->id?>" >
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>绑定所属疾病:</th>
                        <td id="td-disease">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseCtrArray(false), "diseaseid", $checkupTplMenu->disease->id); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>菜单</th>
                        <td id="td-menu">
                        <textarea cols=50 rows=20 name="content"><?=$checkupTplMenu->content?></textarea>
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
        $(document).on('click', '#add-menu', function(e) {
            e.preventDefault();
            var div = $('#div-menu').clone().attr('id', '');
            $('#td-div').append(div);
        })
        .on('click', '#remove-menu', function(e) {
            e.preventDefault();
            $('#td-div div:last').remove();
        })
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>