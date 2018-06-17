<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/7/20
 * Time: 16:27
 */
$pagetitle = "汇报模板列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <a class="btn btn-success" href="/reporttplmgr/add">新建模板</a>
        </div>
        <div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped js-dataTable-simple dataTable no-footer"
                           role="grid">
                        <thead>
                        <tr role="row">
                            <th rowspan="1" colspan="1" style="width: 95px;">创建日期</th>
                            <th rowspan="1" colspan="1">标题</th>
                            <th rowspan="1" colspan="1">简介</th>
                            <th class="text-center" style="width: 70px;" rowspan="1" colspan="1">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reporttpls as $a) { ?>
                            <tr>
                                <td><?= $a->getCreateDay() ?></td>
                                <td><?= $a->title ?></td>
                                <td><?= $a->brief ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-xs btn-default" type="button" title="修改"
                                                onclick="goModify(<?= $a->id ?>)"
                                                data-original-title="修改"><i class="fa fa-pencil"></i></button>
                                        <button class="btn btn-xs btn-default" type="button" title="删除"
                                                onclick="goDelete(<?= $a->id ?>)"
                                                data-original-title="删除"><i class="fa fa-times"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    function goModify(reporttplid) {
        window.location.href = '/reporttplmgr/modify?reporttplid=' + reporttplid;
    }

    function goDelete(reporttplid) {
        if (confirm('确定删除吗？')) {
            window.location.href = '/reporttplmgr/deletePost?reporttplid=' + reporttplid;
        }
    }
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
