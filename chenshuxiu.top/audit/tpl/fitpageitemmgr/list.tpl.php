<?php
$pagetitle = $fitpage->doctor->name . "元素配置 FitPageItem Of " . $fitpage->Code;
$cssFiles = [
    "{$img_uri}/static/css/build.css"
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <section class="col-md-12">
            	<div class="searchBar">
            		<a class="btn btn-success" href="/fitpagemgr/list?fitpagetplid=<?=$fitpagetplid ?>">返回</a>
            	</div>
            	<div class="mt10">
                	<form action="/fitpageitemmgr/configpost" method="post">
                		<input type="hidden" id="fitpageid" name="fitpageid" value="<?=$fitpage->id ?>">
                        <input type="hidden" id="fitpagetplid" name="fitpagetplid" value="<?=$fitpagetplid ?>">
                        <input type="hidden" id="fitpageitemidstr" name="fitpageitemidstr" value="<?=$fitpageitemidstr ?>">
                        <input type="hidden" id="fitpagetplitemidstr" name="fitpagetplitemidstr" value="<?=$fitpagetplitemidstr ?>">
                		<input type="hidden" id="ismuststr" name="ismuststr" value="<?=$ismuststr ?>">

                        <div class="table-responsive">
                            <table class="table table-bordered mt10" >
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>id</th>
                                    <th>code</th>
                                    <th>
                                        <a class="btn btn-default" id="all_selected" href="#">全选</a>
                                        <a class="btn btn-default" id="all_notselected" href="#">全不选</a>
                                    </th>
                                    <th>
                                        <a class="btn btn-default" id="all_must" href="#">全必填</a>
                                        <a class="btn btn-default" id="all_notmust" href="#">全不必填</a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fitpagetplitems as $a ){ ?>
                                    <tr>
                                        <td width="70">
                                            <input type="text" class="width40" name="pos[<?= $a->id ?>]" value="<?= $a->pos ?>" />
                                        </td>
                                        <td>
                                            <?=$a->id ?>
                                        </td>
                                        <td>
                                            <?=$a->code ?>
                                        </td>
                                        <td>
                   							<div class="checkbox checkbox-success">
                                                <input id="checkbox-<?=$a->id?>" class="styled radiodefault" type="checkbox" name="tplid[]" value="<?= $a->id ?>">
                                                <label for="checkbox-<?=$a->id?>">
                                                    <?=$a->name ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="inlineRadio1-<?=$a->id?>" value="1" class="must" name="ismust[<?= $a->id ?>]">
                                                <label for="inlineRadio1-<?=$a->id?>">必填</label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <input type="radio" id="inlineRadio2-<?=$a->id?>" value="0" class="notmust" name="ismust[<?= $a->id ?>]">
                                                <label for="inlineRadio2-<?=$a->id?>">不必填</label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="10">
                                        <input type="submit" class="btn btn-default" value="提交" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                	</form>
            	</div>
    </section>
</div>
<div class="clear"></div>

<?php
$footerScript = <<<XXX
    $(function(){
        init_checkbox();
        init_radio();

        $(".radiodefault").change(function(){
        	if (!$(this).is(':checked')) {
                $("#inlineRadio1-" + $(this).val()).prop("checked",false);
                $("#inlineRadio2-" + $(this).val()).prop("checked",false);
            } else {
                $("#inlineRadio2-" + $(this).val()).prop("checked",true);
            }
        });

        $("#all_selected").on('click', function(){
            $("input[type=checkbox][name='tplid[]']").each(function () {
                $(this).prop("checked",true);
            });
        });

        $("#all_notselected").on('click', function(){
            $("input[type=checkbox][name='tplid[]']").each(function () {
                $(this).prop("checked",false);
            });
        });

        $("#all_must").on('click', function(){
            $(".must").each(function () {
                $(this).prop('checked',true);
            });
        });

        $("#all_notmust").on('click', function(){
            $(".notmust").each(function () {
                $(this).prop("checked",true);
            });
        });
    });

    function init_checkbox() {
        var selectids = $("#fitpageitemidstr").val();
        if (selectids.length>0) {
            var idArr = selectids.split('|');

            $("input[type=checkbox][name='tplid[]']").each(function () {
                if ($.inArray($(this).val(), idArr) > -1) {
                    $(this).prop("checked",true);
                    $("#inlineRadio1-" + $(this).val()).prop("checked",true);
                }
            });
        }
    };

    function init_radio () {
        var selectids = $("#fitpageitemidstr").val();
        var ismusts = $("#ismuststr").val();
        if (selectids.length>0) {
            var idArr = selectids.split('|');
            var ismustArr = ismusts.split('|');
            var arr = [];
            for (var i = 0; i < ismustArr.length; i++) {
                var tmparr = ismustArr[i].split('-');
                arr["" + tmparr[0] + ""] = tmparr[1];
            }

            for (var i = 0; i < idArr.length; i++) {
                var ismust = arr[idArr[i]];
                if (ismust == 1) {
                    $("#inlineRadio1-" + idArr[i]).prop('checked',true);
                    $("#inlineRadio2-" + idArr[i]).prop('checked',false);
                } else if (ismust == 0) {
                    $("#inlineRadio2-" + idArr[i]).prop('checked',true);
                    $("#inlineRadio1-" + idArr[i]).prop('checked',false);
                }
            }
        }
    }
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
