<?php
$pagetitle = "问题修改";
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
            <form action="/xquestionmgr/modifypost" method="post">
                <input type="hidden" name="xquestionid" value="<?= $xquestion->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=100>id</th>
                        <td><?= $xquestion->id ?></td>
                    </tr>
                    <tr>
                        <th>序号</th>
                        <td><?= $xquestion->pos ?></td>
                    </tr>
                    <tr>
                        <th>类型</th>
                        <td><?= HtmlCtr::getRadioCtrImp(XQuestion::getTypeDescArray(), 'type',$xquestion->type ,' '); ?></td>
                    </tr>
                    <tr>
                        <th>编码</th>
                        <td>
                            <input type="text" name="ename" value="<?= $xquestion->ename ?>" />
                            <span class="gray">英文或拼音编码</span>
                        </td>
                    </tr>
                    <tr>
                        <th>是否是精简版问卷中的问题</th>
                        <td>
                        <?= HtmlCtr::getRadioCtrImp(array(1=>'是', 0=>'否'), 'issimple', $xquestion->issimple,' '); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>是否显示ND</th>
                        <td>
                        <?= HtmlCtr::getRadioCtrImp(array(1=>'是', 0=>'否'), 'shownd', $xquestion->isShowND(),' '); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>内容</th>
                        <td>
                            <textarea rows="4" cols="60" name="content"><?= $xquestion->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>提示/说明</th>
                        <td>
                            <textarea rows="4" cols="60" name="tip"><?= $xquestion->tip ?></textarea>
                            <br />
                            注: 或用于辅助字段
                        </td>
                    </tr>
                    <tr>
                        <th>单位</th>
                        <td>
                            <input type="text" name="units" value="<?= $xquestion->units ?>" />
                            <span class="gray">逗号分隔</span>
                        </td>
                    </tr>
                    <tr>
                        <th>定性</th>
                        <td>
                            <input type="text" name="qualitatives" value="<?= $xquestion->qualitatives ?>" />
                            <span class="gray">逗号分隔</span>
                        </td>
                    </tr>
                    <tr>
                        <th>多输入框1</th>
                        <td>
                            <input type="text" name="text11" value="<?= $xquestion->text11 ?>" />
                            <input type="text" name="content1" value="<?= $xquestion->content1 ?>" />
                            <input type="text" name="text12" value="<?= $xquestion->text12 ?>" />
                            <?= HtmlCtr::getSelectCtrImp(XQuestion::getCtypeDescArray(), 'ctype1', $xquestion->ctype1,''); ?>
                            如: [出生][1995][年][年]
                        </td>
                    </tr>
                    <tr>
                        <th>多输入框2</th>
                        <td>
                            <input type="text" name="text21" value="<?= $xquestion->text21 ?>" />
                            <input type="text" name="content2" value="<?= $xquestion->content2 ?>" />
                            <input type="text" name="text22" value="<?= $xquestion->text22 ?>" />
                            <?= HtmlCtr::getSelectCtrImp(XQuestion::getCtypeDescArray(), 'ctype2', $xquestion->ctype2,''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>多输入框3</th>
                        <td>
                            <input type="text" name="text31" value="<?= $xquestion->text31 ?>" />
                            <input type="text" name="content3" value="<?= $xquestion->content3 ?>" />
                            <input type="text" name="text32" value="<?= $xquestion->text32 ?>" />
                            <?= HtmlCtr::getSelectCtrImp(XQuestion::getCtypeDescArray(), 'ctype3', $xquestion->ctype3,''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>多输入框4</th>
                        <td>
                            <input type="text" name="text41" value="<?= $xquestion->text41 ?>" />
                            <input type="text" name="content4" value="<?= $xquestion->content4 ?>" />
                            <input type="text" name="text42" value="<?= $xquestion->text42 ?>" />
                            <?= HtmlCtr::getSelectCtrImp(XQuestion::getCtypeDescArray(), 'ctype4', $xquestion->ctype4,''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>多输入框5</th>
                        <td>
                            <input type="text" name="text51" value="<?= $xquestion->text51 ?>" />
                            <input type="text" name="content5" value="<?= $xquestion->content5 ?>" />
                            <input type="text" name="text52" value="<?= $xquestion->text52 ?>" />
                            <?= HtmlCtr::getSelectCtrImp(XQuestion::getCtypeDescArray(), 'ctype5', $xquestion->ctype5,''); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>是否子问题</th>
                        <td><?= HtmlCtr::getRadioCtrImp(XQuestion::getIsSubDescArray(), 'issub', $xquestion->issub,' '); ?> <span class="gray">(影响序号, +0.1; 如果问题类型是=段落,且不想显示序号,则选Y)</span>
                        </td>
                    </tr>
                    <tr>
                        <th>是否必填</th>
                        <td><?= HtmlCtr::getRadioCtrImp(XQuestion::getMustDescArray(), 'ismust', $xquestion->ismust,' '); ?> <span class="gray"></span>
                        </td>
                    </tr>
                    <?php if($xquestion->isSingleChoice()){ ?>
                    <tr>
                        <th>正确答案</th>
                        <td><?= HtmlCtr::getRadioCtrImp($xquestion->getOptionArray4HtmlCtr(), 'rightoptionid', $xquestion->rightoptionid,' '); ?></td>
                    </tr>
                    <?php }elseif($xquestion->isNum()) { ?>
                    <tr>
                        <th>最小值</th>
                        <td>
                            <input type="text" name="minvalue" value="<?= $xquestion->minvalue ?>" />
                            <span class="gray">数值型的正常范围下限</span>
                        </td>
                    </tr>
                    <tr>
                        <th>最大值</th>
                        <td>
                            <input type="text" name="maxvalue" value="<?= $xquestion->maxvalue ?>" />
                            <span class="gray">数值型的正常范围上限</span>
                        </td>
                    </tr>
                    <?php }elseif($xquestion->isMultChoice()) { ?>
                    <tr>
                        <th>最少选中数</th>
                        <td>
                            <input type="text" name="minvalue" value="<?= $xquestion->minvalue ?>" />
                            <span class="gray">0为没有下限</span>
                        </td>
                    </tr>
                    <tr>
                        <th>最多选中数</th>
                        <td>
                            <input type="text" name="maxvalue" value="<?= $xquestion->maxvalue ?>" />
                            <span class="gray">0为没有上限</span>
                        </td>
                    </tr>
                    <?php } ?>

                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-success" value="提交问题修改" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>

<?php include $tpl . "/xquestionmgr/_options.php"; ?>

        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>