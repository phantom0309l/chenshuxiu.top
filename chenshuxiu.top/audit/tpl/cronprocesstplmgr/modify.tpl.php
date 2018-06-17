<?php
$pagetitle = "修改定时任务模板 of $cronprocesstpl->title";
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
            <div class="contentBoxTitle">
                <h4>定时任务模板基本信息</h4>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <form action="/cronprocesstplmgr/modifypost">
                    <input type="hidden" name="cronprocesstplid" value="<?= $cronprocesstpl->id ?>">
                    <tr>
                        <th width=140>定时任务类名</th>
                        <td>
                            <input type="text" readonly name="tasktype" value="<?= $cronprocesstpl->tasktype ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>标题</th>
                        <td>
                            <input type="text" name="title" value="<?= $cronprocesstpl->title ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>分组</th>
                        <td>
                            <input type="text" name="groupstr" value="<?= $cronprocesstpl->groupstr ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>定时任务描述</th>
                        <td>
                            <textarea name="content" cols="60" rows="5"><?= $cronprocesstpl->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                    </tr>
                </form>
            </table>
            </div>
            <div class="contentBoxTitle">
                <h4>该模板下的变量模板</h4>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <div class="searchBar">
                    <a class="btn btn-success" href="/cronprocesstplvarmgr/add?cronprocesstplid=<?= $cronprocesstpl->id ?>">添加模板变量</a>
                </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                    <thead>
                        <th>模板变量id</th>
                        <th>变量名</th>
                        <th>变量说明</th>
                        <th>单位</th>
                        <th>相关说明</th>
                        <th>操作</th>
                    </thead>
                    <tbody>
                    <?php foreach ($cronprocesstplvars as $cronprocesstplvar) {?>
                        <tr>
                            <td><?= $cronprocesstplvar->id ?></td>
                            <td><?= $cronprocesstplvar->code ?></td>
                            <td><?= $cronprocesstplvar->name ?></td>
                            <td><?= $cronprocesstplvar->unit?></td>
                            <td><?= nl2br($cronprocesstplvar->remark) ?></td>
                            <td>
                                <a href="/cronprocesstplvarmgr/modify?cronprocesstplvarid=<?= $cronprocesstplvar->id ?>">修改</a>
                                <a href="/cronprocesstplvarmgr/deletepost?cronprocesstplvarid=<?= $cronprocesstplvar->id ?>&cronprocesstplid=<?= $cronprocesstpl->id ?>">删除</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                    </div>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>