<?php
$pagetitle = "量表模板列表 PaperTpls";
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
        <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
            <div class="col-sm-6 col-xs-6 success" style="float: left; padding: 0px; line-height: 2.5;">
                <a class="btn btn-sm btn-primary" target="_blank" href="/papertplmgr/add">
                    <i class="fa fa-plus push-5-r"></i>
                    量表模板新建
                </a>
                <br />
                排序:&nbsp;&nbsp;
                <?php if($orderby=='id'){ echo 'id逆序'; } else { ?><a href="/papertplmgr/list?orderby=id">id逆序</a><?php } ?>
                &nbsp;&nbsp;<?php if($orderby=='title'){ echo '标题'; } else { ?><a href="/papertplmgr/list?orderby=title">标题</a><?php } ?>
                &nbsp;&nbsp;<?php if($orderby=='groupstr'){ echo 'groupstr'; } else { ?><a href="/papertplmgr/list?orderby=groupstr">groupstr</a><?php } ?>
            </div>
            <div class="col-sm-6 col-xs-6">
                <div class="col-sm-12" style="float: right; padding-right: 0px;">
                    <form class="form-horizontal push-5-t" action="/papertplmgr/list" method="get">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" placeholder="搜索标题" name="title" class="input-search form-inline form-control" value="<?=$title?>">
                                <span class="input-group-btn" style="width: 1%; line-height: 35px;">
                                    <button type="submit" class="btn btn-primary">
                                        <span type="submit" aria-hidden="true" class="glyphicon glyphicon-search"> </span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
        <form action="/papertplmgr/posmodifypost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>创建日期</td>
                        <td>groupstr</td>
                        <td>ename</td>
                        <td>疾病</td>
                        <td>标题</td>
                        <td>问卷</td>
                        <td>量表</td>
                        <td>答卷</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($papertpls as $a) {
                    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->groupstr ?></td>
                        <td><?= $a->ename ?></td>
                        <td>
                            <?php
                    $diseasePaperTplRefs = DiseasePaperTplRefDao::getListByPapertplidDoctorid($a->id, 0);
                    foreach ($diseasePaperTplRefs as $diseasePaperTplRef) {
                        ?>
                            <a target="_blank" href="/diseasepapertplrefmgr/list?diseaseid=<?= $diseasePaperTplRef->disease->id ?>"><?= $diseasePaperTplRef->disease->name ?></a>
                            <br />
                            <?php } ?>
                        </td>
                        <td><?= $a->title ?></td>
                        <td>
                            <?php
                    if ($a->xquestionsheetid > 0) {
                        $_url = "/xquestionsheetmgr/one?xquestionsheetid={$a->xquestionsheetid}";
                        $_name = "{$a->xquestionsheet->getQuestionCnt() }个问题";
                        ?>
                            <a target="_blank" href="/xquestionsheetmgr/one?xquestionsheetid=<?= $a->xquestionsheetid ?>"><?= $a->xquestionsheet->getQuestionCnt() ?>个问题</a>
                    <?php
                    } else {
                        ?>
                            <a target="_blank" href="/xquestionsheetmgr/add?objtype=PaperTpl&objid=<?=$a->id ?>&sn=<?=$a->ename ?>&title=<?=$a->title ?>">创建问卷</a>
                            <br />
                            <a href="/papertplmgr/deletepost?papertplid=<?= $a->id ?>">慎重删除papertpl</a>
                    <?php
                    }
                    ?>
                        </td>
                        <td>
                        <?= $a->getPaperCnt() ?>个量表
                        <?php
                    if ($a->xquestionsheetid > 0) {
                        ?>
                            <a target="_blank" href="/papermgr/list?papertplid=<?= $a->id ?>"> 列表 </a>
                            <a target="_blank" href="/papermgr/listofpapertpl?papertplid=<?= $a->id ?>"> 对比 </a>
                    <?php }?>
                        </td>
                        <td>
                            <?php if ($a->xquestionsheetid > 0) { ?>
                                <a target="_blank" href="/xanswersheetmgr/list?xquestionsheetid=<?= $a->xquestionsheetid ?>"><?= $a->xquestionsheet->getAnswerSheetCnt()?>
                                    份答卷</a>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="/papertplmgr/modify?papertplid=<?= $a->id ?>">修改</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </form>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
