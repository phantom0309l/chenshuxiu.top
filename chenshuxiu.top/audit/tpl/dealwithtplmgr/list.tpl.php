<?php
$pagetitle = "快捷回复模板 DealwithTpls";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="col-md-12" style="padding-left: 0px;">
            <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 10px; line-height: 2.5;">
                <a class="btn btn-sm btn-primary" target="_blank" href="/dealwithtplmgr/add">
                    <i class="fa fa-plus push-5-r"></i>
                    新建
                </a>
            </div>
            <div class="col-sm-7 col-xs-6" style="float: left; padding: 10px; line-height: 2.5;">

                筛选: 分组[<?=$diseasegroupid ?>]+疾病[<?=$diseaseid ?>]
                <br />
                排序:
                <a class="btn btn-sm btn-success" href="<?= $url."&orderby=diseasegroupid" ?>">疾病组-疾病-分组-题目</a>
                <a class="btn btn-sm btn-success" href="<?= $url."&orderby=title" ?>">题目-疾病组-疾病-分组</a>
                <a class="btn btn-sm btn-success" href="<?= $url."&orderby=groupstr" ?>">分组-疾病组-疾病-题目</a>
                <br />
                <a class="btn btn-sm btn-success" href="/dealwithtplmgr/list">清除筛选条件</a>
            </div>
            <div class="col-sm-4 col-xs-3">
                <div class="col-sm-12" style="float: right; padding-right: 0px;">
                    <form class="form-horizontal push-5-t" action="/dealwithtplmgr/list" method="get">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" placeholder="搜索題目" name="title" class="input-search form-inline form-control" value="<?=$title?>">
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
            <div class="clear"></div>
        </div>
        <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <td>#</td>
                    <td width="100">
                        tplid
                        <br />
                        创建日期
                    </td>
                    <td width="100">疾病分组</td>
                    <td>疾病</td>
                    <td>医生</td>
                    <td>分组</td>
                    <td>
                        題目
                        <br />
                        <span class="gray">关键词</span>
                    </td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($dealwithtpls as $i => $a) {

                    $_diseasegroup_str = "--";
                    $_diseasegroupid = $a->diseasegroupid;
                    if ($a->diseasegroup instanceof DiseaseGroup) {
                        $_diseasegroup_str = $a->diseasegroup->name;
                        $_diseasegroupid = $a->diseasegroupid;
                    }

                    $_disease_str = "--";
                    $_diseaseid = 0;
                    if ($a->disease instanceof Disease) {
                        $_disease_str = $a->disease->name;
                        $_diseaseid = $a->diseaseid;
                    }

                    $_doctor_str = "--";
                    $_doctorid = 0;
                    if ($a->doctor instanceof Doctor) {
                        $_doctor_str = $a->doctor->name;
                        $_doctorid = $a->doctorid;
                    }

                    ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= $a->id ?><br />
                        <span class=gray><?= $a->getCreateDay() ?></span>
                    </td>
                    <td>
                        <a href="/dealwithtplmgr/list?diseasegroupid=<?= $_diseasegroupid ?>"><?= $_diseasegroup_str ?></a>
                    </td>
                    <td>
                        <a href="/dealwithtplmgr/list?diseasegroupid=<?=$_diseasegroupid ?>&diseaseid=<?= $_diseaseid ?>"><?= $_disease_str ?></a>
                    </td>
                    <td>
                        <a href="/dealwithtplmgr/list?doctorid=<?= $_doctorid ?>"><?= $_doctor_str ?></a>
                    </td>
                    <td>
                        <?php
                    if ($a->groupstr) {
                        echo $a->groupstr;
                        ?>
                        <a href="/dealwithtplmgr/list?groupstr=<?= $a->groupstr ?>" title="全部<?=$a->groupstr ?>">(全部)</a>
                        <a href="/dealwithtplmgr/list?diseasegroupid=<?= $_diseasegroupid ?>&groupstr=<?= $a->groupstr ?>" title="<?=$a->groupstr ?>+<?=$_diseasegroup_str ?>">(+<?=$_diseasegroup_str ?>)</a>
                        <?php } ?>
                    </td>
                    <td>
                    <?= $a->title?>
                    <br />
                        <span class="gray">
                            <?= $a->keywords?>
                        </span>
                    </td>
                    <td>
                        <a href="/dealwithtplmgr/modify?dealwithtplid=<?= $a->id ?>" target="_blank" class="btn btn-default">修改</a>
                        <span class="deleteBtn btn btn-default" data-dealwithtplid="<?= $a->id ?>">删除</span>
                    </td>
                </tr>
                <?php
                }
                ?>
                <tr>
                    <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                </tr>
            </tbody>
        </table>
        </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
        $(".deleteBtn").on("click", function(){
            var me = $(this);
            var dealwithtplid = me.data("dealwithtplid");
            if( confirm("确定要删除吗？") ){
                $.ajax({
                    url: '/dealwithtplmgr/deleteJson',
                    type: 'post',
                    dataType: 'text',
                    data: {dealwithtplid: dealwithtplid}
                })
                .done(function(d) {
                    if ('ok' == d) {
                        console.log(d);
                        alert("已删除!");
                        window.location.href = window.location.href;
                    } else {
                        alert("删除失败!");
                    }
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
            }
        })
    })
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
