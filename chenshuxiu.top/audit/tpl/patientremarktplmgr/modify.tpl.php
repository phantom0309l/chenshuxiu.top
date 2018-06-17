<?php
$pagetitle = "医生录症状体征分类修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/patientremarktplmgr/modifypost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <input type='hidden' name="patientremarktplid" value="<?= $patientremarktpl->id ?>" />
                    <tr>
                        <th width=140>医生:</th>
                        <td>
                            <?= $patientremarktpl->doctor->name ?>
                        </td>
                    </tr>
                    <tr>
                        <th>标题:</th>
                        <td>
                            <input type='text' name="name" value="<?= $patientremarktpl->name ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>内容分类</th>
                        <td>
                            <?php $typestr = array(
                                'symptom' => 'symptom',
                                'adverseevent' => 'adverseevent'
                            );
                            echo HtmlCtr::getRadioCtrImp($typestr,"typestr",$patientremarktpl->typestr ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>排序:</th>
                        <td>
                            <input type='text' name="pos" value="<?= $patientremarktpl->pos ?>"  />
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
