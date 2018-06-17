<?php
$pagetitle = "常用词新增";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/commonwordmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>医生</th>
                        <td>
                            <select id="doctor_select" name="doctorid">
                                <?php foreach( CtrHelper::getDoctorCtrArray($mydisease->id) as $k => $v ){?>
                                    <option value="<?= $k ?>" <?= $k == $doctorid ? " selected=\"selected\" " : "" ?>>
                                            <?= $v ?>
                                    </option>
                                <?php }?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>内容分类</th>
                        <td>
                            <p class="red">若添加诊断中的常用词（即‘类型用途’选择 diagnosis），此处不需选择</p>
                            <?php echo HtmlCtr::getRadioCtrImp($prtarr,"prtid",'' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>类型用途</th>
                        <td>
                            <div class="inputbox">
                                <input type="text" name="typestr" value="symptom" />
                                填写英文或拼音小写
                            </div>
                            <div style="margin-top: 10px;">
                                <?php foreach( $typestrs as $typestr){?>
                                    <div class="btn btn-primary btn-xs optionbox" datavalue="<?=$typestr?>"><?=$typestr?></div>
                                <?php }?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>分组</th>
                        <td>
                            <div class="inputbox">
                                <input type="text" name="groupstr" />
                            </div>
                            <div style="margin-top: 10px;">
                                <?php foreach( $groupstrs as $groupstr){?>
                                    <div class="btn btn-primary btn-xs optionbox" datavalue="<?=$groupstr?>"><?=$groupstr?></div>
                                <?php }?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>内容</th>
                        <td>
                            <input type="text" name="content" />
                        </td>
                    </tr>
                    <tr>
                        <th>权重</th>
                        <td>
                            <input type="text" name="weight" value="10" />
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
    $(document).ready(function(){
        $(".optionbox").on("click",function(){
            var me = $(this);
            var value = me.attr('datavalue');
            me.parent().parent().children(".inputbox").children("input").val(value);
        });
        $("#doctor_select").on("change",function () {
            var doctorid = parseInt($(this).val());
                window.location.href = "/commonwordmgr/add?doctorid=" + doctorid;
        });
    })
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
