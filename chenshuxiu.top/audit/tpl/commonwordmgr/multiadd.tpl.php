<?php
$pagetitle = "批量新增常用词";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <section class="col-md-12">
        <form action="/commonwordmgr/multiaddpost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=140>医生</th>
                    <td>
                        <select id="doctor_select" name="doctorid">
                            <?php foreach (CtrHelper::getDoctorCtrArray($mydisease->id) as $k => $v) { ?>
                                <option value="<?= $k ?>" <?= $k == $doctorid ? " selected=\"selected\" " : "" ?>>
                                    <?= $v ?>
                                </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>类型用途</th>
                    <td>
                        <div class="inputbox">
                            <input type="text" name="typestr" value="symptom"/>
                            填写英文或拼音小写
                        </div>
                        <div style="margin-top: 10px;">
                            <?php foreach ($typestrs as $typestr) { ?>
                                <div class="btn btn-primary btn-xs optionbox"
                                     datavalue="<?= $typestr ?>"><?= $typestr ?></div>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>内容分类</th>
                    <td>
                        <p class="red">若添加诊断中的常用词（即‘类型用途’选择 diagnosis），此处不需选择</p>
                        <?php echo HtmlCtr::getRadioCtrImp($prtarr, "prtid", ''); ?>
                    </td>
                </tr>
                <tr>
                    <th>分组</th>
                    <td>
                        <div class="inputbox">
                            <input type="text" name="groupstr"/>
                        </div>
                        <div style="margin-top: 10px;">
                            <?php foreach ($groupstrs as $groupstr) { ?>
                                <div class="btn btn-primary btn-xs optionbox"
                                     datavalue="<?= $groupstr ?>"><?= $groupstr ?></div>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>内容/权重</th>
                    <td>
                        <div>
                            <input id="autoSort" type="checkbox" name="autoSort"> 自动权重
                        </div>
                        <div class="table-responsive">
                            <table id="mutable" class="table-bordered">
                            <thead>
                            <tr>
                                <th>内容</th>
                                <th>权重</th>
                            </tr>
                            </thead>
                            <tr>
                                <td><input type="text" placeholder="请输入内容" name="contents[]"/></td>
                                <td><input type="text" placeholder="请输入权重" name="weights[]" value="10"/></td>
                            </tr>
                        </table>
                        </div>
                        <input id="addItem" type="button" value="插入一行">
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="提交"/>
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
    $(document).ready(function () {
        $(".optionbox").on("click", function () {
            var me = $(this);
            var value = me.attr('datavalue');
            me.parent().parent().children(".inputbox").children("input").val(value);
        });
        $("#doctor_select").on("change", function () {
            var doctorid = parseInt($(this).val());
            window.location.href = "/commonwordmgr/multiadd?doctorid=" + doctorid;
        });

        $("#addItem").on("click", function() {
            var table = $("#mutable");
            var weight = 10;
            var readonly = "";
            if ($("#autoSort").is(':checked') == true) {
                weight = parseInt(table.find("input[name='weights[]']").last().val()) + 10;
                readonly = "readonly";
            }

            var row = `<tr>
            <td><input type="text" placeholder="请输入内容" name="contents[]"/></td>
            <td><input type="text" placeholder="请输入权重" name="weights[]" value="${weight}" ${readonly}/></td>
            </tr>`;
            table.append(row);
        })

        $("#autoSort").on("change", (event) => {
            var target = event.target;
            var table = $("#mutable");
            if ($(target).is(':checked') == true) {
                table.find("input[name='weights[]']").each((index, elem) => {
                    var weight = (index + 1) * 10;  // 权重从10开始,每次增加10
                    $(elem).val(weight);
                    $(elem).attr("readonly", true);
                })
            } else {
                table.find("input[name='weights[]']").attr("readonly", false);
            }
        })

        $("#autoSort").prop("checked", true);
        $("#autoSort").trigger("change");
    })
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
