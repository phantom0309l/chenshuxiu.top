<?php
$pagetitle = "[{$optask->patient->name}] [{$optask->optasktpl->title}] 任务定时事件";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <a class="btn btn-success" href="/optaskcronmgr/break?optaskid=<?=$optask->id?>">中断自动提醒</a>
            <a target="_blank" class="btn btn-success" href="/optasktplcronmgr/listofoptasktpl?optasktplid=<?=$optask->optasktplid?>">自动化消息配置</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td width="100">id</td>
                    <td width="80">步骤</td>
                    <td width="180">plan_exe_time</td>
                    <td>发送内容</td>
                    <td>status</td>
                    <td width="200">备注</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($optaskcrons as $i => $a) {
                    ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->optasktplcron->step ?></td>
                    <td><?= $a->plan_exe_time ?></td>
                    <td>
                        <?= $a->getSendContent() ?>
                        <button class="btn btn-sm btn-primary" data-optaskcronid="<?=$a->id?>" data-content="<?=$a->getSendContent()?>" data-toggle="modal" data-target="#opnode-edit" type="button">
                            <i class="fa fa-edit push-5-r"></i>修改发送内容
                        </button>
                    </td>
                    <td>
                        <?php
                            $colors = [
                                '0' => 'label-success',
                                '1' => 'label-primary',
                                '2' => 'label-danger'
                            ];
                            $color_class_str = $colors["{$a->status}"];
                        ?>
                        <span class="label <?=$color_class_str?>"><?= $a->getStatusStr(); ?></span>
                    </td>
                    <td><?= $a->remark; ?></td>
                </tr>
                <?php } ?>
                </tbody>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>

<!-- 模态框 -->
<div class="modal" id="opnode-edit" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button">
                                <i class="si si-close"></i>
                            </button>
                        </li>
                    </ul>
                    <h3 class="block-title">修改发送内容</h3>
                </div>
                <div class="block-content">
                    <input type="hidden" id="optaskcronid" name="optaskcronid" value="">
                    <table class="table table-bordered">
                        <tr>
                            <th>内容</th>
                            <td>
                                <textarea class="form-control" rows="8" id="content" name="content"></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary" type="button" id="submit-edit" data-dismiss="modal">
                    <i class="fa fa-check"></i>
                    <span>提交</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="clear"></div>

<script>
    $(function(){
        $('#opnode-edit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            var optaskcronid = button.data('optaskcronid');
            var content = button.data('content');

            modal.find('#optaskcronid').val(optaskcronid);
            modal.find('#content').val(content);
        });

        $('#submit-edit').on('click', function () {
            var optaskcronid = $('#optaskcronid').val();
            var content = $("#content").val();

            console.log(optaskcronid, content);

            $.ajax({
                url: '/optaskcronmgr/modifycontentjson',
                type: 'get',
                dataType: 'json',
                data: {
                    optaskcronid: optaskcronid,
                    content : content
                },
                "success": function (response) {
                    alert(response.errmsg);

                    if (response.errno == '0') {
                        window.location.href = location.href;
                    }
                }
            });
        });
    });
</script>

<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
