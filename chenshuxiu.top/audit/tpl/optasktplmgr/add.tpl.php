<?php
$pagetitle = "任务新建";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/optasktplmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>任务title</th>
                        <td>
                            <div class="col-md-2">
                                <input class="form-control" id="title" type="text" name="title" />
                            </div>
                            <div class="col-md-1" style="margin-top: 6px;">
                                (必填)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>任务content</th>
                        <td>
                            <div class="col-md-6">
                                <textarea class="form-control" id="content" name="content" cols="50" rows="10"></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>所属疾病</th>
                        <td>
                            <div class="col-md-2">
                                <input class="form-control" id="diseaseids" type="text" name="diseaseids" />
                            </div>
                            <div class="col-md-2" style="margin-top: 6px;">
                                (必填,以英文,分隔)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>任务类型</th>
                        <td class="red" >
                            <div class="col-md-2">
                                <input class="form-control" id="code" type="text" name="code" />
                            </div>
                            <div class="col-md-3" style="margin-top: 6px;">
                                (不能随便修改, 需要修改对应代码)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>任务子类型</th>
                        <td class="red" >
                            <div class="col-md-2">
                                <input class="form-control" id="subcode" type="text" name="subcode" />
                            </div>
                            <div class="col-md-3" style="margin-top: 6px;">
                                (不能随便修改, 需要修改对应代码)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>对应实体类型</th>
                        <td>
                            <div class="col-md-2">
                                <input class="form-control" id="objtype" type="text" name="objtype" />
                            </div>
                            <div class="col-md-2" style="margin-top: 6px;">
                                (不影响代码)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>手动创建</th>
                        <td>
                            <div class="col-md-9">
                                <label class="css-input css-radio css-radio-warning push-10-r">
                                    <input type="radio" name="is_can_handcreate" value="1"/>
                                    <span></span>
                                    是
                                </label>
                                <label class="css-input css-radio css-radio-warning">
                                    <input type="radio" name="is_can_handcreate" value="0"/>
                                    <span></span>
                                    否
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <div class="col-md-2">
                                <input class="btn btn-success" type="submit" value="创建" />
                            </div>
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
