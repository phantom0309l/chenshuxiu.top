<?php
$pagetitle = "新建分组";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "/static/js/jquery-1.11.1.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar" style="margin-top: -8px">
                <h4 style="text-align: center">新建分组</h4>
            </div>
            <form class="setuppost" action="/pgroupmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>设定本组名称：</th>
                        <td>
                            <input id="name" value="" name="name" style="width: 30%;" />
                            <span style="color: red;"> (组创建后，组名不可修改！) </span>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>本组英文名称：</th>
                        <td>
                            <input id="ename" value="" name="ename" style="width: 30%;"/>
                            <span style="color: red;"> (填写与本组名称意思相近的英文单词或词组！) </span>
                        </td>
                    </tr>
                    <tr>
                        <th>选择疾病：</th>
                        <td>
                            <select name="diseaseid">
                                <option value="0">请选择疾病</option>
                                <?php foreach ($diseases as $a) { ?>
                                    <option value="<?= $a->id ?>"><?= $a->name ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>选择分组：</th>
                        <td>
                            <label>
                                <input type="radio" value="manage" name="typestr" checked />
                                管理组
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>医生id：</th>
                        <td>
                            <input type="text" value="" name="doctorid"/>
                            <a href="<?= $audit_uri ?>/doctormgr/list" target="_blank">去查找</a>(非基于医生的不填写)
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input class="btn btn-success <?= $showsubmit?> setup" type="button" value="创建" style="font-size: 16px; width: 60px" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>

    </section>
    </div>
<?php
$footerScript = <<<SCRIPT
    $(document).on("click",".setup",function(){
        var name= $("#name").val();
        var ename= $("#ename").val();

        if(!(name)){
            alert("请填写组名。");
            return false;
        }
        if(!(ename)){
            alert("请填写英文组名。");
            return false;
        }

        $.ajax({
            "type" : "get",
            "data" : {
                name : name,
                ename : ename
            },
            "dataType" : "html",
            "url" : "/pgroupmgr/checkaddJson",
            "success" : function(data) {
                if(data == "ok"){
                    $(".setuppost").submit();
                }else{
                    alert("组名重复，已有此组。");
                }
            }
        });
    });
SCRIPT;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
