<?php
$pagetitle = "任务修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/optasktplmgr/modifypost" method="post">
                <input type="hidden" name="optasktplid" value="<?= $optasktpl->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>id</th>
                        <td>
                            <div class="col-md-4">
                                <?= $optasktpl->id?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>任务title</th>
                        <td>
                            <div class="col-md-4">
                                <input class="form-control" id="title" type="text" name="title" value="<?= $optasktpl->title ?>" />
                            </div>
                            <div class="col-md-2" style="margin-top: 6px;">
                                (必填)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>任务content</th>
                        <td>
                            <div class="col-md-8">
                                <textarea class="form-control" id="content" name="content" rows="15" placeholder="备注"><?= $optasktpl->content ?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>所属疾病</th>
                        <td>
                            <div class="col-md-4">
                                <input class="form-control" id="diseaseids" type="text" name="diseaseids" value="<?= $optasktpl->diseaseids ?>" />
                            </div>
                            <div class="col-md-2" style="margin-top: 6px;">
                                (必填,以英文,分隔)
                            </div>
                        </td>
                    </tr>
                    <?php if ($optasktpl->is_auto_to_opnode == 1) { ?>
                        <tr>
                            <th>自动进入节点</th>
                            <td>
                                <div class="col-md-2">
                                    <?php echo HtmlCtr::getSelectCtrImp($opnode_kvs, 'auto_to_opnode_code', $optasktpl->auto_to_opnode_code, 'form-control'); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>自动天数进入节点</th>
                            <td>
                                <div class="col-md-1">
                                    <?php echo HtmlCtr::getSelectCtrImp(OpTaskTpl::getDayCnts(), 'auto_to_opnode_daycnt', $optasktpl->auto_to_opnode_daycnt, 'form-control'); ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th>任务类型</th>
                        <td class="red">
                            <div class="col-md-4">
                                <input class="form-control" id="code" type="text" name="code" value="<?= $optasktpl->code ?>" />
                            </div>
                            <div class="col-md-3" style="margin-top: 6px;">
                                (不能随便修改, 需要修改对应代码)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>任务子类型</th>
                        <td class="red">
                            <div class="col-md-4">
                                <input class="form-control" id="subcode" type="text" name="subcode" value="<?= $optasktpl->subcode ?>" />
                            </div>
                            <div class="col-md-3" style="margin-top: 6px;">
                                (不能随便修改, 需要修改对应代码)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>对应实体类型</th>
                        <td>
                            <div class="col-md-4">
                                <input class="form-control" id="objtype" type="text" name="objtype" value="<?= $optasktpl->objtype ?>" />
                            </div>
                            <div class="col-md-3" style="margin-top: 6px;">
                                (不影响代码)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>状态</th>
                        <td>
                            <div class="col-md-9">
                                <label class="css-input css-radio css-radio-warning push-10-r">
                                    <input type="radio" name="status" value="1" <?= $optasktpl->status == 1 ? "checked" : "" ?>/>
                                    <span></span>
                                    有效
                                </label>
                                <label class="css-input css-radio css-radio-warning">
                                    <input type="radio" name="status" value="0" <?= $optasktpl->status == 0 ? "checked" : "" ?>/>
                                    <span></span>
                                    无效
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>手动创建</th>
                        <td>
                            <div class="col-md-9">
                                <label class="css-input css-radio css-radio-warning push-10-r">
                                    <input type="radio" name="is_can_handcreate" value="1" <?= $optasktpl->is_can_handcreate == 1 ? "checked" : "" ?>/>
                                    <span></span>
                                    是
                                </label>
                                <label class="css-input css-radio css-radio-warning">
                                    <input type="radio" name="is_can_handcreate" value="0" <?= $optasktpl->is_can_handcreate == 0 ? "checked" : "" ?>/>
                                    <span></span>
                                    否
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <div class="col-md-4">
                                <input type="submit" class="btn btn-success" value="修改" />
                            </div>
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

<script>
</script>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
