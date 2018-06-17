<?php if($xquestion->isChoice()){ ?>
<hr style="border-color: blue" />
<?php $pagetitle = "备选项 of 问题";include $tpl . "/_pagetitle.php"; ?>
<form action="/xoptionmgr/batmodifypost" method="post">
    <input type="hidden" name="xquestionid" value="<?= $xquestion->id ?>" />
    <table class="table table-bordered">
        <thead>
            <tr>
                <td width=100>选项id</td>
                <td width=100>内容</td>
                <td width=40>分值</td>
                <td width=100>默认选中</td>
                <td width=100>子模块</td>
                <td>级联隐藏显示</td>
            </tr>
        </thead>
            <?php
    foreach ($xquestion->getOptions() as $a) {
        ?>
                    <tr>
            <td><?= $a->id ?> </td>
            <td>
                <textarea rows="3" cols="30" name="options[<?= $a->id ?>][content]"><?= $a->content ?></textarea>
            </td>
            <td>
                <input type="text" name="options[<?= $a->id ?>][score]" value="<?= $a->score ?>" style="width: 40px" />
            </td>
            <td><?= HtmlCtr::getRadioCtrImp(XOption::getCheckedDescArray(), "options[{$a->id}][checked]", $a->checked,'<br/>'); ?></td>
            <td><?= HtmlCtr::getRadioCtrImp(XOption::getHaveSubDescArray(), "options[{$a->id}][havesub]", $a->havesub,'<br/>'); ?></td>
            <td>
                <?php if ($xquestion->isMultChoice()) { ?>
                <p class="alert alert-warning">复选框选中和不选中控制的都是同一项</p>
                显示/隐藏:
                <input type="text" name="options[<?= $a->id ?>][showenames]" style="width: 500px;" value="<?= $a->showenames ?>" placeholder="示例:a---z,d,e---h" />
                <?php } else { ?>
                <p class="alert alert-warning">英文逗号(,)分隔;三个减号(---)表示连续的范围控制,比如a---z表示控制a到z之间的所有元素</p>
                显示:
                <input type="text" name="options[<?= $a->id ?>][showenames]" style="width: 500px;" value="<?= $a->showenames ?>" placeholder="示例:a---z,d,e---h"/>
                <br />
                隐藏:
                <input class="push-10-t" type="text" name="options[<?= $a->id ?>][hideenames]" style="width: 500px;" value="<?= $a->hideenames ?>" placeholder="示例:a---z,d,e---h"/>
                <?php } ?>
            </td>
        </tr>

            <?php } ?>
                    <tr>
            <td colspan=10>
                <input type="submit" class="btn btn-success" value="保存备选修改" />
            </td>
        </tr>
    </table>
</form>
<hr style="border-color: blue" />
<div class="bgcolorborderbox">
<?php $pagetitle = "批量添加备选项";include $tpl . "/_pagetitle.php"; ?>
    <form action="/xoptionmgr/bataddpost" method="post">
        <input type="hidden" name="xquestionid" value="<?= $xquestion->id ?>" />
        <textarea rows="2" cols="100" name="optionstrs"></textarea>
        注: 竖线分隔备选项,如: 是|否
        <br />
        <input type="submit" class="btn btn-success" value="提交备选项" />
    </form>
</div>
<hr style="border-color: blue" />
<div class="bgcolorborderbox">
<?php $pagetitle = "逐个添加备选项";include $tpl . "/_pagetitle.php"; ?>
    <form action="/xoptionmgr/addpost" method="post">
        <input type="hidden" name="xquestionid" value="<?= $xquestion->id ?>" />
        内容:
        <input type="text" name="content" value="" style="width: 800px" />
        <br />
        <br />
        分值:
        <input type="text" name="score" value="" />
        <br />
        <br />默认:
                        <?= HtmlCtr::getRadioCtrImp(XOption::getCheckedDescArray(), 'checked', 0,' '); ?>
                         <br />
        <input type="submit" class="btn btn-success" value="提交备选项" />
    </form>
</div>
<?php } ?>
