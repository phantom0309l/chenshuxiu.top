<?php
$pagetitle = "重点患者备注模板管理";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<style>
    .msg-markform {
        border: 1px solid #5C90D2;
        position: absolute;
        background-color: #fff;
        padding: 15px 30px;
        display: grid;
        z-index: 10;
        top: 120px;
        left: 30%;
    }
</style>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="block">
            <div class="block-header">
                <div class="block-options" id="add_btn">
                    <button class="btn btn-info" data-toggle="modal" data-target="#modal-add" type="button">添加模板</button>
                </div>
            </div>
            <!--                模板显示-->
            <div class="block-content">
                <table class="table table-striped table-borderless table-header-bg">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th class="">ID</th>
                        <th>模板内容</th>
                        <th>所属疾病组</th>
                        <th class="tc" style="width: 80px">操作</th>
                    </tr>
                    </thead>
                    <tbody id="marktpl-tbody">
                    <?php foreach ($todaymarktpls as $k => $todaymarktpl) {
                        ?>
                        <tr>
                            <td class="text-center"><?= $k + 1 ?></td>
                            <td><?= $todaymarktpl->id ?></td>
                            <td><?= $todaymarktpl->title ?>
                            </td>
                            <td>
                                <span class="label label-info">
                                    <?= $todaymarktpl->diseasegroup->name; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button data-toggle="modal" data-marktext="<?= $todaymarktpl->title ?>"
                                            data-todaymarktplid="<?= $todaymarktpl->id ?>"
                                            data-diseasegroupid="<?= $todaymarktpl->diseasegroupid ?>"
                                            data-target="#modal-modify" class="btn btn-xs btn-default btn-modify" type="button">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <a href="/patienttodaymarktplmgr/deletepost?marktplid=<?= $todaymarktpl->id ?>">
                                        <button class="btn btn-xs btn-default btn-del" type="button" data-toggle="tooltip" title=""
                                                data-original-title="删除"><i class="fa fa-times"></i>
                                        </button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <!--   模板显示结束           -->
        </div>
    </section>
</div>
<div class="modal in" id="modal-add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">添加备注模板</h3>
                </div>
                <div class="block-content">
                    <form id="mark-form">
                        模板内容：<input class="form-control" type="text" id="mark-text" name="marktpltitle" placeholder="请填写模板内容">
                        所属疾病组：<select class="form-control" id="mark-diseasegroup" name="diseasegroupid">
                            <option value="">请选择所属疾病组</option>
                            <?php
                            foreach ($diseasegroups as $diseasegroup) {
                                ?>
                                <option value="<?= $diseasegroup->id ?>"><?= $diseasegroup->name ?></option>
                            <?php } ?>
                        </select>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="border-top: none">
                <button class="btn btn-sm btn-default" id="todaymark_cancel" type="button" data-dismiss="modal">取消</button>
                <button class="btn btn-sm btn-primary" id="todaymark_confirm" type="button" data-dismiss="modal"><i class="fa fa-check"></i> 添加
                </button>
            </div>
        </div>
    </div>
</div>

<!--修改-->
<div class="modal in" id="modal-modify" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">修改备注模板</h3>
                </div>
                <div class="block-content">
                    <form id="mark-form-modify">
                        <input type="hidden" name="marktplid" id="todaymarktplid">
                        模板内容：<input class="form-control" value="" type="text" id="mark-text-modify" name="marktpltitle" placeholder="请填写模板内容">
                        所属疾病组：<select class="form-control" id="mark-diseasegroup-modify" name="diseasegroupid">
                            <option value="">请选择所属疾病组</option>
                            <?php
                            foreach ($diseasegroups as $diseasegroup) {
                                ?>
                                <option value="<?= $diseasegroup->id ?>"><?= $diseasegroup->name ?></option>
                            <?php } ?>
                        </select>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="border-top: none">
                <button class="btn btn-sm btn-default" id="todaymark_cancel-modify" type="button" data-dismiss="modal">取消</button>
                <button class="btn btn-sm btn-primary" id="todaymark_confirm-modify" type="button" data-dismiss="modal"><i class="fa fa-check"></i> 修改
                </button>
            </div>
        </div>
    </div>
</div>
<!--修改结束-->
<div class="clear"></div>
<script>
    $(function () {
        //删除模板
        var diseasegroups = <?= json_encode($diseasegroups, JSON_UNESCAPED_UNICODE)?>;
        $('.btn-del').on('click', function () {
            if (!confirm("确认删除本模板么？")) {
                return false;
            }
        });
        //显示模态框
        $('#add_btn').on('click', function () {
            $('#msg-markform').show();
        });
        //隐藏模态框
        $("#todaymark_cancel").on('click', function () {
            $("#msg-markform").hide();
        });
        //增加模板
        $("#todaymark_confirm").on('click', function () {
            if ($('#mark-text').val() == '') {
                alert("请输入模板内容!");
                $('#mark-text').focus();
                return false;
            }
            if ($('#mark-diseasegroup').val() == '') {
                alert("请选择疾病组!");
                $('#mark-diseasegroup').focus();
                return false;
            }

            var diseasegroupid_add = $('#mark-diseasegroup').val();
            var titlte_add = $('#mark-text').val();
            console.log("meiyou");
            $.ajax({
                type: "post",
                url: "/patienttodaymarktplmgr/addpostjson",
                data: {
                    "diseasegroupid": diseasegroupid_add,
                    "marktpltitle": titlte_add
                },
                dataType: "json",
                success: function (response) {
                    if (response.errno === '0') {
                        window.location.reload();
                    } else {
                        alert(response.errmsg);
                    }
                }
            });

        });

        //修改模板
        $("#todaymark_confirm-modify").on('click', function () {
            var titlte_modify = $('#mark-text-modify').val();
            var diseasegroupid_modify = $('#mark-diseasegroup-modify').val();
            var marktplid = $('#todaymarktplid').val();

            if (titlte_modify === '') {
                alert("请输入模板内容!");
                $('#mark-text-modify').focus();
                return false;
            }

            if (diseasegroupid_modify === '') {
                alert("请选择疾病组!");
                $('#mark-diseasegroup-modify').focus();
                return false;
            }

            $.ajax({
                type: "post",
                url: "/patienttodaymarktplmgr/modifypostjson",
                data: {
                    "marktplid": marktplid,
                    "diseasegroupid": diseasegroupid_modify,
                    "marktpltitle": titlte_modify
                },
                dataType: "json",
                success: function (response) {
                    if (response.errno === '0') {
                        window.location.reload();
                    } else {
                        alert(response.errmsg);
                    }
                }
            });
        });
        //点击修改按钮时 数据回写
        $('.btn-modify').on('click', function () {
            var marktext = $(this).data('marktext');
            var diseasegroupid = $(this).data('diseasegroupid');
            var todaymarktplid = $(this).data('todaymarktplid');

            $('#mark-text-modify').val(marktext);
            $('#todaymarktplid').val(todaymarktplid);

            var option = '<option value="">请选择所属疾病组</option>';
            $('#mark-diseasegroup-modify').find('option').each(function (index, option) {
                if ($(option).val() == diseasegroupid) {
                    $(option).prop('selected', true);
                } else {
                    $(option).prop('selected', false);
                }
            });
        });

    });
</script>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
