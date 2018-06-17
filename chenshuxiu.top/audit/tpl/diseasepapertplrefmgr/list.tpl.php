<?php
$pagetitle = "疾病量表关联 列表 DiseasePaperTplRefs";
$cssFiles = [
    $img_uri . '/static/css/jquery-ui.autocomplete.min.css?v=20180208',
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/static/js/vendor/jquery-ui.autocomplete.min.js?v=20180208',
    $img_uri . '/v5/common/select_doctor.js?v=20180530',
]; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';

$show_in_audit_arr = [];
$show_in_audit_arr['-1'] = '运营端全部';
$show_in_audit_arr['1'] = '运营端显示';
$show_in_audit_arr['0'] = '运营端隐藏';

$show_in_wx_arr = [];
$show_in_wx_arr['-1'] = '患者端全部';
$show_in_wx_arr['1'] = '患者端显示';
$show_in_wx_arr['0'] = '患者端隐藏';

?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="col-md-12">
            <div class="col-sm-8 col-xs-8">
                <form>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseCtrArray(), 'diseaseid' ,$diseaseid, "form-control")?>
                    </div>
                    <div class="col-md-2">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp($show_in_audit_arr, 'show_in_audit' ,$show_in_audit,"form-control")?>
                    </div>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp($show_in_wx_arr, 'show_in_wx' ,$show_in_wx,"form-control")?>
                    </div>
                    <input type="submit" class="btn btn-success" value="提交" />
                </form>
            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="col-sm-12" style="float: right; padding-right: 0px;">
                    <form class="form-horizontal push-5-t" action="/diseasepapertplrefmgr/list" method="get">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" placeholder="搜索量表名" name="title" class="input-search form-inline form-control" value="<?=$title?>">
                                <span class="input-group-btn" style="width: 1%; line-height: 35px;">
                                    <button type="submit" class="btn btn-primary">
                                        <span aria-hidden="true" class="glyphicon glyphicon-search"> </span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="col-md-12">
            <form action="/diseasepapertplrefmgr/posmodifypost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>#</td>
                                <td>refid</td>
                                <td>疾病</td>
                                <td>医生</td>
                                <td>量表名</td>
                                <td>运营端可见</td>
                                <td>患者端可见</td>
                    <?php if ($disease instanceof Disease) { ?>
                                <td>序号</td>
                    <?php } ?>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                $i = 0;
                foreach ($diseasepapertplrefs as $a) {
                    $i ++;
                    ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td><?= $a->id ?></td>
                                <td>
                                    <a href="/diseasepapertplrefmgr/list?diseaseid=<?= $a->diseaseid ?>"><?= $a->disease->name ?></a>
                                </td>
                                <td>
                                    <a href="/diseasepapertplrefmgr/list?diseaseid=<?= $a->diseaseid ?>&doctorid=<?= $a->doctorid ?>">
                                        <?= $a->doctor instanceof Doctor ? $a->doctor->name : "空"?>
                                    </a>
                    <?php
                    if (false == $a->haveRef_nulldoctorid()) {
                        echo '<span class="red">定制</span>';
                    }
                    ?>
                                </td>
                                <td>
                                    <a href="/diseasepapertplrefmgr/list?papertplid=<?= $a->papertplid ?>"><?= $a->papertpl->title ?></a>
                                    &nbsp;&nbsp;
                                    <a target="_blank" href="/papertplmgr/list?papertplid=<?= $a->papertplid ?>">
                                    <?= ($a->papertpl->xquestionsheet instanceof XQuestionSheet)? '['.$a->papertpl->xquestionsheet->getQuestionCnt().'个问题]':'<span class="red">[没问卷]</span>'?>
                                    </a>
                                </td>
                                <td>
                            <?= $a->show_in_audit == 1 ? '是' : "<span class='red'>否</span>"?>
                                </td>
                                <td>
                            <?= $a->show_in_wx == 1 ? '是' : "<span class='red'>否</span>"?>
                                </td>
                        <?php if ($disease instanceof Disease) { ?>
                                <td>
                                    <input type="text" class="width40" name="pos[<?= $a->id ?>]" value="<?= $a->pos ?>" />
                                </td>
                        <?php } ?>
                                <td>
                                    <a href="/diseasepapertplrefmgr/modify?diseasepapertplrefid=<?= $a->id ?>">修改</a>
                                    <a class="red" href="/diseasepapertplrefmgr/deletepost?diseasepapertplrefid=<?= $a->id ?>">删除</a>
                                </td>
                            </tr>
                <?php } ?>
                <?php if ($disease instanceof Disease) { ?>
                            <tr>
                                <td colspan=20>
                                    <input type="submit" class="btn btn-success" value="保存序号修改" />
                                    提交序号修改后,会先按序号调整顺序.
                    <?php
                    if ($doctorid == 0) {
                        ?>
                        <a target="_blank" href="/diseasepapertplrefmgr/DiseaseToDoctorsJson?diseaseid=<?=$disease->id ?>" class="btn btn-success">将疾病绑定的量表 同步到 所有医生</a>
                    <?php
                    }
                    ?>
                                </td>
                            </tr>
                <?php } ?>
                        </tbody>
                    </table>
                </div>
            </form>
        <?php
        if ($disease instanceof Disease) {

            $pagetitle = "疾病量表关联 新建";
            include $tpl . "/_pagetitle.php";
            ?>
            <form action="/diseasepapertplrefmgr/addpost" method="post">
                <input type="hidden" name="diseaseid" value='<?= $disease->id ?>' />
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="160">疾病</th>
                            <td class="blue"><?= $disease->name ?></td>
                        </tr>
                        <tr>
                            <th>量表</th>
                            <td>
                            <?= HtmlCtr::getRadioCtrImp(CtrHelper::toNotEmptyPaperTplCtrArray(PaperTplDao::getNotXquestionSheetList()), 'papertplid', ''); ?>
                        </td>
                        </tr>
                        <tr>
                            <th>是否展示在运营端</th>
                            <td>
                                <input type="radio" name="show_in_audit" value="1" checked="checked" />
                                是
                                <input type="radio" name="show_in_audit" value="0" />
                                否
                            </td>
                        </tr>
                        <tr>
                            <th>是否展示在患者端</th>
                            <td>
                                <input type="radio" name="show_in_wx" value="1" />
                                是
                                <input type="radio" name="show_in_wx" value="0" checked="checked" />
                                否
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="submit" class="btn btn-success" value="提交" />
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
        <?php } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
