<?php
$pagetitle = "课程与课文关系组合表 CourseLessonRef";
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
                <label for="">按课程名筛选：</label>
            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toCourseCtrArray($courses),"courseid",$courseid); ?>
        </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>创建日期</td>
                        <td>修改日期</td>
                        <td>课文名</td>
                        <td>课程名</td>
                        <td>修改</td>
                    </tr>
                </thead>
                <tbody>
<?php

foreach ($courselessonrefs as $a) {
    ?>
                <td><?= $a->id ?></td>
                    <td><?= $a->createtime ?></td>
                    <td><?= $a->updatetime ?></td>
                    <td>
                        <a href="/lessonmgr/modify?lessonid=<?= $a->lessonid?> "><?= $a->lesson->title ?></a>
                    </td>
                    <td>
                        <a href="/courselessonrefmgr/list?courseid=<?= $a->courseid?> "><?= $a->course->title ?></a>
                    </td>
                    <td>
                        <a class="delete" data-courselessonrefid="<?=$a->id ?>" data-coursename="<?=$a->lesson->title ?>" data-lessonname="<?=$a->course->title ?>">删除</a>
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
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $("select#courseid").on("change", function () {
        var val = parseInt($(this).val());
        var url = val == 0 ? location.pathname : location.pathname + '?courseid=' + val;
        window.location.href = url;
    });
    $(function(){
        $("a.delete").on("click",function(){
            var me = $(this);

            var tr = me.parents("tr");
            var courselessonrefid = me.data("courselessonrefid");
            var lessonname = me.data("lessonname");
            var coursename = me.data("coursename");

            if(!confirm("确认删除 "+coursename+" 与 "+lessonname+" 的关系么？")){
                return false;
            }
            var url = "/courselessonrefmgr/deletepost";
            var args = {
                "time":new Date,
                "courselessonrefid":courselessonrefid
            };
            $.post(url,args,function(data){
                if(data == "1"){
                    tr.remove();
                }
            });

            return false;
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>