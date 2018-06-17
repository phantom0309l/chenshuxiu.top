<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/7/20
 * Time: 16:27
 */
$pagetitle = "菜单列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .searchBar {
        padding: 20px 20px 1px;
        background-color: #f9f9f9;
        border: 1px solid #e9e9e9;
    }   
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = true;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php"; ?>
        <div class="content-div">
            <section class="col-md-12">
                <div class="searchBar">
                    <a class="btn btn-success push-20"
                       href="/checkuptplmenumgr/addofdoctor?doctorid=<?= $doctor->id ?>">创建菜单</a>
                </div>
                <!-- 列表begin -->
                <div>
                    <div class="scroll-x">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th class="tc" style="width: 70px;">序号</th>
                                <th>疾病</th>
                                <th class="tc" style="width: 90px;">检查报告</th>
                                <th class="tc" style="width: 70px;">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($checkuptplmenus as $key => $checkuptplmenu) {
                                $index = $key + 1;
                                $disease = $checkuptplmenu->disease;
                                $disease_name = '';
                                if ($disease instanceof Disease) {
                                    $disease_name = $disease->name;
                                }
                                ?>
                                <tr>
                                    <td class="tc" style="width: 70px;"><?= $index ?></td>
                                    <td><?= $disease_name ?></td>
                                    <td class="tc" style="width: 70px;">
                                        <a href="/checkuptplmgr/list?doctorid=<?= $doctor->id ?>&diseaseid=<?= $checkuptplmenu->diseaseid ?>"
                                           target="_blank">
                                            <?= $checkupTplCnt = CheckupTplDao::getCntByDoctorIdAndDiseaseId($doctor->id, $checkuptplmenu->diseaseid); ?>
                                        </a>
                                    </td>
                                    <td class="tc" style="width: 70px;">
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-default" type="button" title="修改"
                                                    onclick="goModify(<?= $checkuptplmenu->id ?>, <?= $disease->id ?>)"
                                                    data-original-title="修改"><i class="fa fa-pencil"></i></button>
                                            <button class="btn btn-xs btn-default" type="button" title="删除"
                                                    onclick="goDelete(<?= $checkuptplmenu->id ?>)"
                                                    data-original-title="删除"><i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <!-- 分页begin -->
                    <div class="mb20">
                        <?php include $dtpl . "/pagelink.ctr.php"; ?>
                    </div>
                    <!-- 分页end -->
                </div>
                <!-- 列表end -->
            </section>
        </div>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    function goModify(checkuptplmenuid, diseaseid) {
        window.location.href = '/checkuptplmenumgr/modifyofdoctor?checkuptplmenuid=' + checkuptplmenuid + '&diseaseid=' + diseaseid;
    }

    function goDelete(checkuptplmenuid) {
        if (confirm('确定删除吗？')) {
            window.location.href = '/checkuptplmenumgr/deleteofdoctorpost?checkuptplmenuid=' + checkuptplmenuid;
        }
    }
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>