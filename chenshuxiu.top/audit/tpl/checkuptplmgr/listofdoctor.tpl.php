<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/7/20
 * Time: 16:27
 */
$pagetitle = "检查报告";
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
                       href="/checkuptplmgr/addofdoctor?doctorid=<?= $doctor->id ?>">添加检查报告</a>
                </div>
                <div class="searchBar">
                    <!-- 筛选begin -->
                    <div class="col-md-12 remove-padding">
                        <form class="form form-horizontal" method="get" action="/checkuptplmgr/listofdoctor">
                            <input type="hidden" name="doctorid" value="<?= $doctor->id ?>"/>
                            <div class="form-group">
                                <div class="col-md-6 remove-padding">
                                    <label class="col-md-2 control-label tc" for="keyword">标题</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" id="keyword" name="keyword"
                                               value="<?= $keyword ?>"
                                               placeholder="标题搜索...">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 remove-padding">
                                    <label class="col-md-2 control-label tc" for="diseaseid">疾病</label>
                                    <div class="col-md-10">
                                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toDiseaseCtrArray($diseases, true), "diseaseid", $diseaseid, "form-control"); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 remove-padding">
                                    <div class="col-md-offset-2 col-md-10">
                                        <button class="btn btn-sm btn-primary" type="submit">组合筛选</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- 筛选end -->
                    <div class="clear"></div>
                </div>
                <!-- 列表begin -->
                <div>
                    <div class="scroll-x">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th class="tc" style="width: 70px;">序号</th>
                                <th>标题</th>
                                <th>疾病</th>
                                <th class="tc" style="width: 70px;">问题</th>
                                <th class="tc" style="width: 70px;">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($checkuptpls as $key => $checkuptpl) {
                                $index = $key + 1;
                                $questionCnt = '';
                                $xquestionsheet = $checkuptpl->xquestionsheet;
                                if ($xquestionsheet instanceof XQuestionSheet) {
                                    $questionCnt = $xquestionsheet->getQuestionCnt();
                                }
                                $disease = $checkuptpl->disease;
                                $disease_name = '';
                                if ($disease instanceof Disease) {
                                    $disease_name = $disease->name;
                                }
                                ?>
                                <tr>
                                    <td class="tc" style="width: 70px;"><?= $index ?></td>
                                    <td><?= $checkuptpl->title ?></td>
                                    <td><?= $disease_name ?></td>
                                    <td class="tc" style="width: 70px;">
                                        <a target="_blank" href="/xquestionsheetmgr/one?xquestionsheetid=<?= $checkuptpl->xquestionsheetid ?>"><?= $questionCnt ?></a>
                                    </td>
                                    <td class="tc" style="width: 70px;">
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-default" type="button" title="修改"
                                                    onclick="goModify(<?= $checkuptpl->id ?>)"
                                                    data-original-title="修改"><i class="fa fa-pencil"></i></button>
                                            <button class="btn btn-xs btn-default" type="button" title="删除"
                                                    onclick="goDelete(<?= $checkuptpl->id ?>)"
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
    function goModify(checkuptplid) {
        window.location.href = '/checkuptplmgr/modifyofdoctor?checkuptplid=' + checkuptplid;
    }

    function goDelete(checkuptplid) {
        if (confirm('确定删除吗？')) {
            window.location.href = '/checkuptplmgr/deleteofdoctorpost?checkuptplid=' + checkuptplid;
        }
    }
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>