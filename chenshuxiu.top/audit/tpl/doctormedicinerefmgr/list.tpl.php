<?php
$pagetitle = "药品库-医生定制 DoctorMedicineRefs";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
    .div10 {
        margin-bottom: 10px
    }
    .label-width{
        width: 100px;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form class="form-horizontal pr" action="/doctormedicinerefmgr/list" method="get">
                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-xs-1 control-label label-width" for="">医生</label>
                    <div class="col-xs-2">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                    <div class="col-xs-1">
                        <a target="_blank" class="btn btn-primary" href="/doctormedicinerefmgr/doctorlist">按医生汇总</a>
                    </div>
                    <label class="col-xs-1 control-label label-width" for="word">模糊查找</label>
                    <div class="col-xs-2">
                        <input class="form-control" type="text" id="word" name="word" value="<?=$word?>" placeholder="药名或展示名">
                    </div>
                    <div class="col-xs-2">
                        <button class="btn btn-sm btn-success" type="submit">组合筛选</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="searchBar">
            <span class="text-primary">分组,药名是药品公共属性</span>
            <br/>
            <span class="text-warning">医生,序号,展示名,剂量,频率,调药规则是药品在某个医生下的定制属性</span>
        </div>

        <form action="/doctormedicinerefmgr/posmodifypost" method="post">
            <input type="hidden" name="doctorid" value="<?= $doctorid ?>">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <td class="bg-warning">医生</td>
                    <td class="bg-primary">分组</td>
                    <td class="bg-warning">序号</td>
                    <td class="bg-primary">药名</td>
                    <td class="bg-warning">展示名</td>
                    <td class="bg-warning">用药时机</td>
                    <td class="bg-warning">标准用法</td>
                    <td class="bg-warning">药物剂量</td>
                    <td class="bg-warning">用药频率</td>
                    <td class="bg-warning" width=200>调药规则</td>
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
                        <td>
                            <input type="text" class="width50" name="pos[<?= $a->id ?>]"
                                   value="<?= $a->pos ?>"/>
                        </td>
                        <td><?= $a->medicine->id ?> <?= $a->medicine->name ?></td>
                        <td class="blue"><?= $a->title ?></td>
                        <td><?= empty($a->getArrDrug_timespan()) ? '' : '...' ?></td>
                        <td><?= empty($a->getArrDrug_std_dosage()) ? '' : '...' ?></td>
                        <td><?= empty($a->getArrDrug_dose()) ? '' : '...' ?></td>
                        <td><?= empty($a->getArrDrug_frequency()) ? '' : '...' ?></td>
                        <td><?= empty($a->getArrDrug_change()) ? '' : '...' ?> </td>
                        <td>
                            <a class="btn btn-primary" target="_blank"
                               href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?= $a->id ?>">修改</a>
                            <a class="delete btn btn-danger" data-doctormedicinerefid="<?= $a->id ?>">删除</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan=20>
                        <input class="btn btn-success" type="submit" value="保存序号修改">
                        <?php include $dtpl . "/pagelink.ctr.php"; ?>
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

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
