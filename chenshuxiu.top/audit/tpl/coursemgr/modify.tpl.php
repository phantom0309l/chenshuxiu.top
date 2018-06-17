<?php
$pagetitle = "课程修改";
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
        <form action="/coursemgr/modifypost" method="post">
            <input type="hidden" name="courseid" value="<?= $course->id ?>"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=140>id</th>
                    <td><?= $course->id ?></td>
                </tr>
                <tr>
                    <th>创建时间</th>
                    <td><?= $course->createtime ?></td>
                </tr>
                <tr>
                    <th>修改时间</th>
                    <td><?= $course->updatetime ?></td>
                </tr>
                <tr>
                    <th>主标题</th>
                    <td>
                        <input id="title" type="text" name="title" style="width: 80%;" value="<?= $course->title ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>副标题</th>
                    <td>
                        <input id="subtitle" type="text" name="subtitle" style="width: 80%;"
                               value="<?= $course->subtitle ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>课程所属分组</th>
                    <td>
                        <?php
                        if ($myauditor->id == 10007) {
                            ?>
                            <input id="groupstr" type="text" name="groupstr" style="width: 20%;"
                                   value="<?= $course->groupstr ?>"/>
                            (必填)
                            <?php
                        } else {
                            ?>
                            <input id="groupstr" type="text" name="groupstr" style="width: 20%;"
                                   value="<?= $course->groupstr ?>" readonly="readonly"/>
                            (groupstr : 只有老史可以修改)
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>段落标题一</th>
                    <td>
                        <input id="title1" type="text" name="title1" style="width: 80%;"
                               value="<?= $course->title1 ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>段落标题二</th>
                    <td>
                        <input id="title2" type="text" name="title2" style="width: 80%;"
                               value="<?= $course->title2 ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>段落标题三</th>
                    <td>
                        <input id="title3" type="text" name="title3" style="width: 80%;"
                               value="<?= $course->title3 ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>简介</th>
                    <td>
                        <textarea id="brief" name="brief" cols="100" rows="10"><?= $course->brief ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>配图</th>
                    <td>
                        <?php
                        $picWidth = 150;
                        $picHeight = 150;
                        $pictureInputName = "pictureid";
                        $isCut = false;
                        $picture = $course->picture;
                        require_once("$dtpl/picture.ctr.php");
                        ?>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="修改课程"/>
                    </td>
                </tr>
            </table>
            </div>
        </form>
        <?php
        $pagetitle = "所属疾病 ";
        include $tpl . "/_pagetitle.php";
        ?>
        <div class="border1 p10">
            <div style="margin-bottom: 8px;">
                <form action="/diseasecourserefmgr/addpost" method="post">
                    <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toDiseaseCtrArray($diseases), 'diseaseid', 0, 'diseasedoctorcourse_disease'); ?>
                    <select autocomplete="off" name="doctorid" id="doctorid" style="display: none">
                    </select>
                    <input type="hidden" name="courseid" value="<?= $course->id ?>"/>
                    <input type="submit" value="添加"/>
                    <span id="addcommon">
                    </span>
                    <span id="search">
                    </span>
                </form>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>疾病</th>
                        <th>医生</th>
                        <th>课程</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($diseasecourserefs as $i => $a) { ?>
                        <tr>
                            <td class="text-center"><?=$i+1?></td>
                            <td><span class="label label-info"><?=$a->disease->name ?? '通用'?></span></td>
                            <td><span class="label label-info"><?=$a->doctor->name ?? '通用'?></span></td>
                            <td><span class="label label-info"><?=$a->course->title?></span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-xs btn-default" type="button" data-toggle="tooltip" title="" data-original-title="删除"><a href="/diseasecourserefmgr/deletepost?diseasecourserefid=<?= $a->id ?>"><i class="fa fa-times"></i></a></button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <?php if (count($search_diseasecourserefs) > 0) { ?>
                <div>查询</div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>疾病</th>
                        <th>医生</th>
                        <th>课程</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($search_diseasecourserefs as $i => $a) { ?>
                        <tr>
                            <td class="text-center"><?=$i+1?></td>
                            <td><span class="label label-info"><?=$a->disease->name ?? '通用'?></span></td>
                            <td><span class="label label-info"><?=$a->doctor->name ?? '通用'?></span></td>
                            <td><span class="label label-info"><?=$a->course->title?></span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-xs btn-default" type="button" data-toggle="tooltip" title="" data-original-title="删除"><a href="/diseasecourserefmgr/deletepost?diseasecourserefid=<?= $a->id ?>"><i class="fa fa-times"></i></a></button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </section>
</div>
<div class="clear"></div>

<script>
    $(function(){
        $("#doctorid").on("change", function () {
            var diseaseid = $("#diseaseid").val();
            var doctorid = $(this).val();
            var courseid = $("input[name='courseid']").val();
            console.log(doctorid);

            $("#search").html("<a href=\"/coursemgr/modify?courseid=" + courseid + "&diseaseid=" + diseaseid + "&doctorid=" + doctorid + "\">查询</a>");

            if (doctorid > 0) {
                $("#addcommon").html("<a href=\"/diseasecourserefmgr/addcommon?diseaseid=" + diseaseid + "&doctorid=" + doctorid + "&courseid=" + courseid +"\">给医生添加疾病通用课程</a>");
                $("#addcommon").show();
            } else {
                $("#addcommon").html("");
                $("#addcommon").hide();
            }
        });
        
        $(".diseasedoctorcourse_disease").on("change", function () {
            var diseaseid = $(this).val();
            var courseid = $("input[name='courseid']").val();
            console.log(diseaseid);

            if (diseaseid > 0) {
                $("#search").html("<a href=\"/coursemgr/modify?courseid=" + courseid + "&diseaseid=" + diseaseid + "\">查询</a>");

                $("#doctorid").show();
                $.ajax({
                    type: "post",
                    url: "/doctordiseaserefmgr/getDoctoridsByDiseaseJson",
                    data:{
                        "diseaseid" : diseaseid
                    },
                    dataType: "json",
                    success : function(result){
                        var data = result.data;

                        var options = "";
                        $.each(data, function(id,name){
                            options += "<option value=\""  + id + "\">"  + name + "</option>";
                        });

                        $("#doctorid").html(options);
                    }
                });
            } else {
                $("#doctorid").hide();
            }
        });
    });
</script>

<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
