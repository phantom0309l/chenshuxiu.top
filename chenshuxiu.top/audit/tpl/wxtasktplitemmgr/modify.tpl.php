<?php
$pagetitle = "活动修改";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "{$img_uri}/static/js/vendor/jquery.ui.widget.js",
    "{$img_uri}/static/js/vendor/jquery.iframe-transport.js",
    "{$img_uri}/static/js/vendor/jquery.fileupload.js",
]; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/wxtasktplitemmgr/modifypost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <input type="hidden" name="wxtasktplitemid" value="<?= $wxtasktplitem->id ?>"/>
                <tr>
                    <th width=140>id</th>
                    <td><?= $wxtasktplitem->id ?></td>
                </tr>
                <tr>
                    <th>创建时间</th>
                    <td><?= $wxtasktplitem->createtime ?></td>
                </tr>
                <tr>
                    <th>修改时间</th>
                    <td><?= $wxtasktplitem->updatetime ?></td>
                </tr>
                <tr>
                    <th>子任务标题</th>
                    <td>
                        <input id="title" type="text" name="title" style="width: 80%;"
                               value="<?= $wxtasktplitem->title ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>pos</th>
                    <td>
                        <input id="pos" type="text" name="pos" style="width: 80%;" value="<?= $wxtasktplitem->pos ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>ename</th>
                    <td>
                        <input id="ename" type="text" name="ename" style="width: 80%;"
                               value="<?= $wxtasktplitem->ename ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>简介</th>
                    <td>
                        <textarea id="brief" name="brief" cols="100" rows="10"><?= $wxtasktplitem->brief ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>内容</th>
                    <td>
                        <textarea id="content" name="content" cols="100"
                                  rows="10"><?= $wxtasktplitem->content ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>第一次配图</th>
                    <td>
                        <?php
                        $picture = $wxtasktplitem->picture;
                        if ($picture instanceof Picture) {
                            ?>
                            <input type="hidden" id="pictureid" name="pictureid" value="<?= $picture->id ?>"/>
                            <div>
                                <img src="<?= $picture->getSrc() ?>"/>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" id="pictureid" name="pictureid" value="0"/>
                        <?php } ?>
                        <span class="btn btn-default uploadBtn">上传</span>
                    </td>
                </tr>
                <tr>
                    <th>第二次配图</th>
                    <td>
                        <?php
                        $picture1 = $wxtasktplitem->picture1;
                        if ($picture1 instanceof Picture) {
                            ?>
                            <input type="hidden" id="picture1id" name="picture1id" value="<?= $picture1->id ?>"/>
                            <div>
                                <img src="<?= $picture1->getSrc() ?>"/>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" id="picture1id" name="picture1id" value="0"/>
                        <?php } ?>
                        <span class="btn btn-default uploadBtn">上传</span>
                    </td>
                </tr>
                <tr>
                    <th>第三次配图</th>
                    <td>
                        <?php
                        $picture2 = $wxtasktplitem->picture2;
                        if ($picture2 instanceof Picture) {
                            ?>
                            <input type="hidden" id="picture2id" name="picture2id" value="<?= $picture2->id ?>"/>
                            <div>
                                <img src="<?= $picture2->getSrc() ?>"/>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" id="picture2id" name="picture2id" value="0"/>
                        <?php } ?>
                        <span class="btn btn-default uploadBtn">上传</span>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="修改活动"/>
                    </td>
                </tr>
            </table>
            </div>
        </form>
    </section>
</div>
<input type="file" id="fileBtn" name="imgurl" style='visibility: hidden;'>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function () {
        var app = {
            init: function () {
                var self = this;
                self.uploadBtnClick($(".uploadBtn"));
            },
            initUpload: function (node) {
                var self = this;
                var file_api = new FileReader();
                $('#fileBtn').fileupload({
                    url: "/picture/uploadimagepost/?w=150&h=150&isCut=&objtype=&objid=&type=",
                    formData: {},
                    add: function (e, result) {
                        file_api.readAsDataURL(result.files[0]);
                        result.submit();
                    },
                    dataType: "json",
                    success: function (data, status) {
                        if (data.pictureid > 0) {

                            alert("上传成功");
                            node.parents("td").find("input[type='hidden']").val(data.pictureid);
                        }
                    }
                });
            },
            uploadBtnClick: function (node) {
                var self = this;
                node.on("click", function () {
                    self.initUpload($(this));
                    $("#fileBtn").click();
                });
            }
        };
        app.init();
    })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
