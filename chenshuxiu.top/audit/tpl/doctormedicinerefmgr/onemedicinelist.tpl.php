<?php
$pagetitle = "药品库-医生定制 DoctorMedicineRefs Of {$medicine->name}";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
        .div10 {
            margin-bottom: 10px
        }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
                <div class="searchBar">
                    <a class="btn btn-success" href="/doctormedicinerefmgr/add?medicineid=<?= $medicine->id ?>">关系新建</a>
                </div>
                <form action="/doctormedicinerefmgr/posmodifypost" method="post">
                    <input type="hidden" name="doctorid" value="<?= $doctorid ?>">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <thead>
                        <tr>
                            <td>医生</td>
                            <td>分组</td>
                            <td>药名</td>
                            <td>展示名</td>
                            <td>药物剂量</td>
                            <td>用药频率</td>
                            <td width=200>调药规则</td>
                            <td width=200>用药注意事项</td>
                            <td>操作</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($doctormedicinerefs as $a) {
                            ?>
                            <tr>
                                <td><?= $a->doctor->name ?></td>
                                <td><?= $a->medicine->groupstr ?></td>
                                <td><?= $a->medicine->id ?> <?= $a->medicine->name ?></td>
                                <td class="blue"><?= $a->title ?></td>
                                <td><?= implode('<br/>', $a->getArrDrug_dose()) ?></td>
                                <td><?= implode('<br/>', $a->getArrDrug_frequency()) ?></td>
                                <td><?= implode('<br/>', $a->getArrDrug_change()) ?> </td>
                                <td><?= $a->doctorremark ?></td>
                                <td>
                                    <a class="btn btn-primary" target="_blank"
                                       href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?= $a->id ?>">修改</a>
                                    <a class="delete btn btn-danger" data-doctormedicinerefid="<?= $a->id ?>">删除</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    </div>
                </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function () {
        $(document).on("click", ".delete", function () {
            var doctormedicinerefid = $(this).data('doctormedicinerefid');

            var tr = $(this).parents("tr");
            $.ajax({
                "type": "get",
                "data": {
                    doctormedicinerefid: doctormedicinerefid
                },
                "dataType": "html",
                "url": "/doctormedicinerefmgr/deleteJson",
                "success": function (data) {
                    if (data == 'success') {
                        alert("删除成功");
                        tr.remove();
                    } else {
                        alert("未知错误");
                    }
                }
            });
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
