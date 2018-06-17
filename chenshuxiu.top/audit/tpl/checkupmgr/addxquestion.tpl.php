<?php
$pagetitle = "检查记录新增";
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
            <form action="/checkupmgr/addxquestionPost" method="post">
                <input type="hidden" name="patientid" value="<?=$patient->id ?>" />
                <div class="searchBar">手工录入检查记录</div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>患者</th>
                        <td>
                            <?=$patient->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>所属医生</th>
                        <td>
                            <?=$patient->doctor->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>检查医院</th>
                        <td>
                            <input type="text" name="hospitalstr" value="<?=$checkup->hospitalstr ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>检查结果</th>
                        <td>
                            <textarea name="content" cols=60 rows=4><?=$checkup->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>检查日期</th>
                        <td>
                            <input type="text" class="calendar calendaraa" name="check_date" value="<?=$check_date ?>" />
                            (先选日期)
                        </td>
                    </tr>
                    <tr>
                        <th>选择检查类型</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(CheckupTpl::toArrayCtr($checkuptpls), "checkuptplid", $checkuptplid,"checkuptpl")?> (先选日期)
                        </td>
                    </tr>
                    <tr>
                        <th>checkuptpl</th>
                        <td>
                            <div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                    <tr>
                                        <td>填写问卷</td>
                                    </tr>
                                    <tr>
                                        <td>
<?php
if ($xsheet) {
    foreach ($xsheet->getItemList() as $a) {
        $defaultHide = '';
        if ($a->isDefaultHide()) {
            $defaultHide = 'style="display:none;"';
        }

        $_xquestion = null;
        if ($a instanceof XQuestion) {
            $_xquestion = $a;
        } else {
            $_xquestion = $a->xquestion;
        }
        ?>
                                            <div class='questionDiv <?=$_xquestion->ename?>' <?=$defaultHide?>>
	  	                                        <?php echo $a->getHtml (); ?>
                            		        </div>
                                            <div style="clear: both"></div>
<?php
    }
}
?>
                                		</td>
                                    </tr>
                                </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
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
		$(".checkuptpl").on("change",function(){
			var patientid = $("input[name=patientid]").val();
			var checkuptplid = $(this).val();
			var checkdate = $("input[name=check_date]").val();

			var url = location.pathname + '?patientid=' + patientid + '&checkuptplid=' + checkuptplid + '&check_date=' + checkdate;
            window.location.href = url ;
		});

		/* $(".anwser").on("click",function(){
			var patientid = $("input[name=patientid]").val();

			var url = location.pathname + '?patientid=' + patientid;

			alert(location.pathname);return false;
		}); */
	});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
