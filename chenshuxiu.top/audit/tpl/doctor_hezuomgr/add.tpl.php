<?php
$pagetitle = "新建合作医生";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/doctor_hezuomgr/addpost" method="post">
                <input type="hidden" id="doctorid" name="doctorid" value="<?= $doctor->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <td width=140>company</td>
                        <td>
                            <input id="company" type="text" name="company" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <td>doctor_code</td>
                        <td>
                            <input id="doctor_code" type="text" name="doctor_code" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <td>name(医生姓名)</td>
                        <td>
                            <input id="name" type="text" name="name" style="width: 20%;" value="<?= $doctor->name ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>sex(性别值为1时是男性，值为2时是女性，值为0时是未知)</td>
                        <td>
                            <input id="sex" type="text" name="sex" style="width: 20%;" value="<?= $doctor->sex ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th>技术职称</th>
                        <td>
                            <input id="title1" type="text" name="title1" style="width: 80%;" value="<?= $doctor->title ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th>行政职称</th>
                        <td>
                            <input id="title2" type="text" name="title2" style="width: 80%;" value="<?= $doctor->title ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th>hospital_name</th>
                        <td>
                            <input id="hospital_name" type="text" name="hospital_name" style="width: 80%;" value="<?= $doctor->hospital->name ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th>department</th>
                        <td>
                            <input id="department" type="text" name="department" style="width: 80%;" value="<?= $doctor->department ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th>json(其他相关个性字段)</th>
                        <td>
                            <textarea id="json" name="json" cols="100" rows="10"></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="创建" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
