<?php
$pagetitle = "问卷新增";
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
            <div class="tipbox searchBar">提示: 问题系统主要是提供基础存储,问卷的添加应该由各模块定制.</div>
            <form action="/xquestionsheetmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>问卷标题</th>
                        <td>
                            <input style="width: 400px" type="text" name="title" value="<?= $title ?>"/>
                            title, 一般对前台没啥意义,最后由关联对象自己来显示
                        </td>
                    </tr>
                    <tr>
                        <th>唯一码</th>
                        <td>
                            <input type="text" name="sn" value="<?= $sn ?>"/>
                            sn, 如果由各模块创建,可以为空
                        </td>
                    </tr>
                    <tr>
                        <th>关联对象类型</th>
                        <td>
                            <input type="text" name="objtype" value="<?= $objtype ?>"/>
                            objtype
                        </td>
                    </tr>
                    <tr>
                        <th>关联对象ID</th>
                        <td>
                            <input type="text" name="objid" value="<?= $objid ?>"/>
                            objid
                        </td>
                    </tr>
                    <tr>
                        <th>关联对象子编码</th>
                        <td>
                            <input type="text" name="objcode" value="<?= $objcode ?>"/>
                            objcode
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-success" value="提交"/>
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>