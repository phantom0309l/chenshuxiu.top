<?php
$pagetitle = "创建drugsheet";
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
            <form action="/drugsheetmgr/addpost" method="post" id="postForm">
                <input type="hidden" id="patientid" name="patientid" value="<?= $patient->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <td width=140>日期</td>
                        <td>
                            <input type="text" name="thedate" value="" class="calendar thedate" />
                        </td>
                    </tr>
                    <tr>
                        <td width=140>类型</td>
                        <td>
                            <label>
                                <input type="radio" name="isdrug" value="1" checked />
                                服药
                            </label>
                            <label>
                                <input type="radio" name="isdrug" value="0" />
                                不服药
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <span class="createBtn btn btn-default">创建</span>
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
        $(".createBtn").on("click", function(){
            var val = $.trim( $(".thedate").val() );
            if( val.length == 0 ){
                alert("请输入日期");
                return;
            }
            $("#postForm").submit();
        })
    })
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
