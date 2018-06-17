<?php
$pagetitle = "依维莫司临床实验项目 Certicans [{$patient->name}] ";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <input type="hidden" id="patientid" value="<?=$patient->id?>">
        <div class="col-md-12">
            <div class="col-sm-3 col-xs-2 success" style="float: left; padding: 0px; line-height: 2.5;">
                <button class="btn btn-sm btn-primary" data-type="add" data-toggle="modal" data-target="#opnode-edit" type="button">
                    <i class="fa fa-plus push-5-r"></i> 新建
                </button>
            </div>
            <div class="clear"></div>
        </div>
        <div class="col-md-12" style="overflow-x: auto">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 100px">id</th>
                        <th style="width: 190px">创建时间</th>
                        <th>开始日期</th>
                        <th>结束日期</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($certicans as $a) { ?>
                    	<tr id="opnode-<?=$a->id?>">
                            <td><?=$a->id?></td>
                            <td><?=$a->createtime?></td>
                            <td><?=$a->begin_date?></td>
                            <td><?= date('Y-m-d', strtotime($a->begin_date) + 3600 * 24 * 20)?></td>
                            <td><?=$a->getStatuStr()?>(<a target="_blank" href="/certicanitemmgr/list?certicanid=<?=$a->id?>"><?=$a->getDoneItemCnt()?>/<?=$a->getItemCnt()?></a>)</td>
                            <td>
			             		<?php if ($a->getDoneItemCnt() > 0) { ?>
			             			<button class="download btn btn-info push-5-r push-10" data-certicanid="<?= $a->id ?>" type="button"><i class="fa fa-download"></i> Download</button>
			             		<?php } ?> 
                           	</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
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
                    <h3 class="block-title">新建</h3>
                </div>
                <div class="block-content">
                    <input type="hidden" id="type" value="">
                    <input type="hidden" id="opnodeid" value="">
                    <input type="hidden" id="old_code" value="">
                    <div class="form-group">
                        <label class="" for="title">化疗方案</label>
                        <div class="">
                            <input class="form-control" type="text" id="title" name="title" placeholder="请输入化疗方案">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="code">程数</label>
                        <div class="">
                            <input class="form-control" type="text" id="sub_title" name="sub_title" placeholder="请输入程数">
                        </div>
                    </div>
                	<div class="form-group">
                        <label class="" for="code">开始日期</label>
                        <div class="">
                            <input class="form-control calendar" type="text" id="begin_date" name="begin_date" placeholder="请输入开始日期">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary" type="button" id="submit-edit" data-dismiss="modal">
                    <i class="fa fa-check"></i>提交
                </button>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function(){
    $('.download').on('click', function(){
        var me = $(this);
        var certicanid = me.data('certicanid');
        window.location.href = "/certicanmgr/DownloadExcelJson?certicanid=" + certicanid;
    });

    $('#submit-edit').on('click', function () {
        var title = $('#title').val();
        var sub_title = $('#sub_title').val();
        var begin_date = $('#begin_date').val();
        var patientid = $('#patientid').val();

        if (title == '') {
            alert("化疗方案不能为空!");
            return false;
        }

        if (sub_title == '') {
            alert("程数不能为空!");
            return false;
        }

        if (begin_date == '') {
            alert("开始日期不能为空!");
            return false;
        }

        if (patientid == '') {
            alert("患者不能为空!");
            return false;
        }

        var flag = 0;

        $.ajax({
            url: '/certicanmgr/addjson',
            type: 'get',
            dataType: 'text',
            async: false,
            data: {
                patientid: patientid,
                title: title,
                sub_title: sub_title,
                begin_date : begin_date
            },
            "success": function (data) {
                if (data == 'ok') {
                    alert("创建成功并已发送给患者");
                    window.location.href = window.location.href;
                } else {
                    alert("日期冲突");
                    flag = 1;
                }
            }
        });

        if (flag == 1) {
            return false;
        }
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
