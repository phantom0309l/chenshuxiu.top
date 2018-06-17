<?php
$pagetitle = "修改量表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .form-group .control-label {
        width: 135px;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = true;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php"; ?>
        <div class="content-div">
            <section class="col-md-12 block-content">
                <form class="form-horizontal" action="/diseasepapertplrefmgr/modifyofdoctorpost" method="post">
                    <input type="hidden" name="diseasepapertplrefid" value="<?= $diseasepapertplref->id ?>">
                    <div class="block block-bordered">
                        <div class="block-header bg-gray-lighter">
                            <ul class="block-options">
                                <li>
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin">
                                        <input type="checkbox"
                                               name="show_in_wx" <?= $diseasepapertplref->show_in_wx ? 'checked' : '' ?>><span></span>患者可见
                                    </label>
                                </li>
                                <li>
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin">
                                        <input type="checkbox"
                                               name="show_in_audit" <?= $diseasepapertplref->show_in_audit ? 'checked' : '' ?>><span></span>运营可见
                                    </label>
                                </li>
                                <li>
                                    <button type="button" data-toggle="block-option" data-action="content_toggle"><i class="si si-arrow-up"></i></button>
                                </li>
                            </ul>
                            <h3 class="block-title"><?= $diseasepapertplref->papertpl->title ?></h3>
                        </div>
                        <div class="block-content">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>问题</th>
                                    <th class="tc" style="width: 130px;">类型</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $questions = [];
                                if ($xquestionsheet instanceof XQuestionSheet) {
                                    $questions = $xquestionsheet->getQuestions();
                                }
                                foreach ($questions as $key => $question) {
                                    $index = $key + 1
                                    ?>
                                    <tr>
                                        <td><?= $question->getTitle() ?></td>
                                        <td class="tc" style="width: 130px;"><?= $question->getTypeDesc() ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                        <div class="block-footer tc">
                            <button class="btn btn-minw btn-success" type="submit">保存</button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
