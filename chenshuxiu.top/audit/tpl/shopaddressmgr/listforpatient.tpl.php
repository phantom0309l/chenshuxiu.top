<?php
$pagetitle = "配送地址 ShopAddresss";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <a class="btn btn-success" href="/shopaddressmgr/add?patientid=<?=$patient->id?>">新建地址</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td width="90">id</td>
                    <td width="140">创建时间</td>
                    <td>微信号</td>
                    <td>患者</td>
                    <td>联系人</td>
                    <td>联系电话</td>
                    <td>省</td>
                    <td>市</td>
                    <td>区</td>
                    <td>详细地址</td>
                    <td>邮编</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shopAddresss as $a) { ?>
                    <tr id="id-<?=$a->id?>">
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDayHi() ?></td>
                        <td><?= $a->wxuser->nickname?></td>
                        <td><?= $a->patient->name?></td>
                        <td><?= $a->linkman_name?></td>
                        <td><?= $a->linkman_mobile?></td>
                        <td><?= $a->xprovince->name?></td>
                        <td><?= $a->xcity->name?></td>
                        <td><?= $a->xcounty->name?></td>
                        <td><?= $a->content?></td>
                        <td><?= $a->postcode?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a class="btn btn-xs btn-default" target="_blank" href="/shopaddressmgr/modify?shopaddressid=<?=$a->id?>"><i class="fa fa-pencil"></i></a>
                                <button class="btn btn-xs btn-default delete" data-shopaddressid="<?=$a->id?>" type="button" data-toggle="tooltip" title="" data-original-title="Remove Client"><i class="fa fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<script>
    $(function () {
        $(".delete").on('click', function () {
            console.log($(this).data('shopaddressid'));

            var shopaddressid = $(this).data('shopaddressid');

            if (!confirm("确定删除吗?")) {
                return false;
            }

            $.ajax({
                'url' : '/shopaddressmgr/deletejson',
                'type' : 'get',
                'data' : {
                    shopaddressid : shopaddressid
                },
                'datatype' : 'text',
                'success' : function (result) {
                    if (result == 'success') {
                        alert("删除成功");
                        $("#id-" + shopaddressid).remove();
                    } else {
                        alert(result);
                    }
                }
            });
        })
    });
</script>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
