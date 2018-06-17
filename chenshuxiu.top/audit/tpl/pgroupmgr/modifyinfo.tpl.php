<?php
$pagetitle = "修改分组";
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
                <h4 style="text-align: center">修改分组</h4>
            </div>
            <form class="" action="/pgroupmgr/modifyinfopost" method="post">
                <input type="hidden" id="pgroupid" value="<?=$pgroup->id?>" name="pgroupid"/>
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>设定本组名称：</th>
                        <td>
                            <input id="name" value="<?=$pgroup->name?>" name="name" style="width: 30%;"/>
                            <span style="color: red;"> (组创建后，组名不可修改！) </span>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>本组英文名称：</th>
                        <td>
                            <input id="ename" value="<?=$pgroup->ename?>" name="ename" style="width: 30%;"/>
                            <span style="color: red;"> (填写与本组名称意思相近的英文单词或词组！) </span>
                        </td>
                    </tr>
                    <tr>
                        <th>组分类：</th>
                        <td>
                            <?php foreach( $subtypestr_arr as $key => $name ){ ?>
                            <label>
                                <input type="radio" name="subtypestr" value="<?= $key ?>" <?= $key == $pgroup->subtypestr ? "checked" : ""?>/>
                                <span><?= $name ?></span>
                            </label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>可推荐管理组：</th>
                        <td>
                            <input id="refer_pgroupids" value="<?=$pgroup->refer_pgroupids?>" name="refer_pgroupids" style="width: 50%;"/>
                            <span> (填写组id以英文逗号分割) </span>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input class="btn btn-success" type="submit" value="修改"/>
                        </td>
                    </tr>
                </table>
                </div>
            </form>

    </section>
    </div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
