<?php
$pagetitle = "医生录症状体征分类新建";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/patientremarktplmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>医生:</th>
                        <td>
                            <input type='hidden' name="doctorid" value="<?= $doctor->id ?>" /><?= $doctor->name ?>
                        </td>
                    </tr>
                    <tr>
                        <th>标题:</th>
                        <td>
                            <input type='text' name="name" />
                        </td>
                    </tr>
                    <tr>
                        <th>内容分类</th>
                        <td>
                            <?php $typestr = array(
                                'symptom' => 'symptom',
                                'adverseevent' => 'adverseevent'
                            );
                            echo HtmlCtr::getRadioCtrImp($typestr,"typestr",'' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>排序:</th>
                        <td>
                            <input type='text' name="pos" />
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
