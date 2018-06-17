<?php
$pagetitle = "修改菜单 AuditMenu";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form class="form-horizontal" action="/auditmenumgr/modifypost" method="post">
                <input type="hidden" name="auditmenuid" value="<?= $auditmenu->id ?>" />
                <div class="form-group">
                    <label class="col-xs-12" for="example-text-input">父级菜单</label>
                    <div class="col-sm-9">
                        <?php $arr = AuditMenu::getParentArr(); foreach ($arr as $key => $value) { ?>
                            <label class="css-input css-radio css-radio-success push-10-r">
                                <input type="radio" name="parentmenuid" <?php if($key == $auditmenu->parentmenuid){ ?>checked="checked"<?php } ?> value="<?=$key?>"><span></span> <?=$value?>
                            </label>
                        <?php }?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12" for="">页面名称</label>
                    <div class="col-sm-9">
                        <input style="width:50%" class="form-control" type="text" name="title" value="<?=$auditmenu->title?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12" for="">url</label>
                    <div class="col-sm-9">
                        <input style="width:50%" class="form-control" type="text" name="url" value="<?=$auditmenu->url?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12" for="">auditresourceid</label>
                    <div class="col-sm-9">
                        <input style="width:50%" class="form-control" type="text" name="auditresourceid" value="<?=$auditmenu->auditresourceid?>" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <button class="btn btn-sm btn-primary" type="submit">提交</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
