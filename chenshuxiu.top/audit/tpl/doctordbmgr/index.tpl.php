<?php
$pagetitle = "数据库列表";
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

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a class="btn btn-success" href="/checkuptplmenumgr/adddisease">创建疾病菜单</a>
            </div>
            <div class="searchBar">
                <form action="/doctordbmgr/index" method="get" class="pr">
<!--
                    <div class="mt10">
                        <label for="">模糊查找：</label>
                        <input type="text" name="word" value="<?= $word?>" />
                    </div>
-->
                    <div class="mt10">
                        <label class="col-md-1 col-sm-1 control-label label-width" for="">医生</label>
                        <div class="col-md-2 col-sm-1">
                            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                        </div>
                    </div>
                    <div class="mt10">
                        <input class="btn btn-success" type="submit" value="组合筛选" />
                    </div>
                </form>
            </div>
            <form action="/checkuptplmgr/posmodifypost" method="post">
                <div class="table-responsive">
                    <table class="table  table-bordered">
                    <thead>
                        <tr>
                            <td width=140>ID</td>
                            <td>创建日期</td>
                            <td>医生</td>
                            <td>疾病</td>
                            <td>检查报告模板</td>
                            <td>菜单</td>
                            <td>操作</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($doctorDiseaseRefs as $a) {
                        ?>
                        <tr>
                            <td><?= $a->id ?></td>
                            <td><?= substr($a->createtime,0,10) ?></td>
                            <td><?= $a->doctorid ?> <?= $a->doctor->name ?></td>
                            <td><?= $mydisease->name; ?></td>
                            <td><a id="a-checkuptplcnt" href="/checkuptplmgr/list?doctorid=<?=$a->doctorid?>&diseaseid=<?=$a->diseaseid?>"><?= $checkupTplCnt = CheckupTplDao::getCntByDoctorIdAndDiseaseId($a->doctorid, $a->diseaseid);?></a></td>
                            <td><?php $menuCnt = CheckupTplMenuDao::getCntByDoctorIdAndDiseaseId($a->doctorid, $a->diseaseid); if ($menuCnt) {?><a href="/checkuptplmenumgr/modify?doctorid=<?=$a->doctorid;?>&diseaseid=<?=$a->diseaseid?>">修改菜单</a><?php } else {?><a id="a-add-menu" data-checkuptplcnt="<?=$checkupTplCnt?>" href="/checkuptplmenumgr/add?doctorid=<?=$a->doctorid;?>&diseaseid=<?=$a->diseaseid?>">创建菜单</a><?php } ?></td>
                            <td>
                                无
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
    $(function(){
        $(document).on("click","#a-add-menu",function(e){
            var checkupTplCnt = $(this).data('checkuptplcnt');
            if (checkupTplCnt < 1) {
                e.preventDefault();
                alert('请先创建检查报告模板');
            }
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>