<?php
$pagetitle = 'PGroup修改';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .box{ border:1px solid #ddd; margin-top:20px; position: relative; padding: 10px;}
    .showAdd{ position: absolute; right:0px; top:0px;}
    .createBox{ margin:20px 0px 120px 0px; border: 1px solid #ddd; padding:10px;}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <section class="col-md-5">
            <div class="searchBar" style="margin-top: -8px">
                <h4 style="text-align: center"><?= $pgroup->name?>组</h4>
            </div>
            <form class="setuppost" action="/pgroupmgr/addpost" method="post">
                <div style="margin: 6px 0 6px 0; border: 1px solid #CCC;">
                    <p>
                        设定本组名称：
                        <input id="name" value="<?= $pgroup->name?>" name="name" style="width: 30%;" readonly />
                        <span style="color: red;"> (组创建后，组名不可修改！) </span>
                    </p>
                    <p>
                        本组英文名称：
                        <input id="ename" value="<?= $pgroup->ename?>" name="ename" style="width: 30%;" readonly />
                        <span style="color: red;"> (填写与本组名称意思相近的英文单词或词组！) </span>
                    </p>
                    <p>
                        <span>所属疾病：</span><?= $pgroup->disease->name?>
                    </p>
                </div>
            </form>

            <div>
                <?php
                $course = $pgroup->course;
                $buttonname = $course instanceof Course ? "修改" : "添加";
                ?>
                <div class="box">
                    <a class="btn btn-success showAdd" data-pgroupid="<?= $pgroup->id ?>" data-addtype="addcourse">
                        + <?= $buttonname ?>课程
                    </a>
                    <div class="pageTitle">
                        <div class="pageTitleIcon"></div>
                        <div class="pageTitleStr">课程任务</div>
                        <div class="clear"></div>
                    </div>
                    <div class="box-c">
                        <p>已选课程：</p>
                        <p>
                            <?php if ( $course instanceof Course ) { ?>
                            <a href="/lessonmgr/listofcourse?courseid=<?= $course->id ?>">
                                <?= $course->title . $course->subtitle?>
                            </a>
                            <? } ?>
                        </p>
                    </div>
                </div>

                <?php
                $outpapertpl = $pgroup->outpapertpl;
                $buttonname = $outpapertpl instanceof PaperTpl ? "修改" : "添加";
                ?>
                <div class="box">
                    <a class="btn btn-success showAdd" data-pgroupid="<?= $pgroup->id ?>" data-addtype="addoutpapertpl">
                        + <?= $buttonname ?>出组量表
                    </a>
                    <div class="pageTitle">
                        <div class="pageTitleIcon"></div>
                        <div class="pageTitleStr">出组任务</div>
                        <div class="clear"></div>
                    </div>
                    <div class="box-c">
                        <p>已选出组量表：</p>
                        <p>
                            <?php if ( $outpapertpl instanceof PaperTpl ) { ?>
                            <a href="/papertplmgr/modify?papertplid=<?= $outpapertpl->id ?>">
                                <?= $outpapertpl->title?>
                            </a>
                            <? } ?>
                        </p>
                    </div>
                </div>

            </div>

    </section>
        <div class="col-md-7">
        <?php include $tpl . "/pgroupmgr/addhtml.tpl.php"; ?>
    </div>
    </div>
<script>
    $(document).on("click",".showAdd",function(){
        var me = $(this);
        var pgroupid = me.data("pgroupid");
        var addtype = me.data("addtype");

        $("#showAddShell").html('');
        $.ajax({
            "type" : "get",
            "data" : {
                pgroupid : pgroupid,
                addtype : addtype,
            },
            "dataType" : "html",
            "url" : "/pgroupmgr/addhtml",
            "success" : function(data) {
                $("#showAddShell").html(data);
            }
        });
    });

    function check(){
        var inputval = $("input[id='modify_checked']:checked").val();
        var addtype = $("#addtype").val();
        var offsetdaycnt = $("#offsetdaycnt").val();

        if(!(inputval)){
            alert("请选择一项！再提交。");
            return false;
        }
        if(!confirm("确定执行此操作吗？")){
            return false;
        }
    }
</script>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
