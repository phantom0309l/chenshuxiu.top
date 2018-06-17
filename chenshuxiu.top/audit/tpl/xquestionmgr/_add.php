
<a href="##" name="add"></a>
<hr style="border-color: blue" />
<?php $pagetitle = "添加问题";include $tpl . "/_pagetitle.php"; ?>
<form action="/xquestionmgr/addpost" method="post">
    <input type="hidden" name="xquestionsheetid" value="<?= $xquestionsheet->id ?>" />
    <table class="table table-bordered">
        <tr>
            <th width=100>序号</th>
            <td>
                <input type="text" name="pos" value="<?=$xquestionsheet->getQuestionCnt()+1 ?>" />
            </td>
        </tr>
        <tr>
            <th>类型</th>
            <td><?= HtmlCtr::getRadioCtrImp(XQuestion::getTypeDescArray(), 'type', 'Radio',' '); ?></td>
        </tr>
        <tr>
            <th>编码</th>
            <td>
                <input type="text" name="ename" value="" />
                <span class="gray">英文或拼音编码</span>
            </td>
        </tr>
        <tr>
            <th>是否是精简版问卷中的问题</th>
            <td><?= HtmlCtr::getRadioCtrImp(array(1=>'是', 0=>'否'), 'issimple', '1',' '); ?></td>
        </tr>
        <tr>
            <th>是否显示ND</th>
            <td><?= HtmlCtr::getRadioCtrImp(array(1=>'是', 0=>'否'), 'shownd', '0',' '); ?></td>
        </tr>
        <tr>
            <th>内容</th>
            <td>
                <textarea rows="4" cols="60" name="content"></textarea>
                <br />
                问题的标题
            </td>
        </tr>
        <tr>
            <th>提示/说明</th>
            <td>
                <textarea rows="4" cols="60" name="tip"></textarea>
                <br />
                注: 或用于辅助字段
            </td>
        </tr>
        <tr>
            <th>单位</th>
            <td>
                <input type="text" name="units" value="" />
                <span class="gray">逗号分隔</span>
            </td>
        </tr>
        <tr>
            <th>定性</th>
            <td>
                <input type="text" name="qualitatives" value="" />
                <span class="gray">逗号分隔</span>
            </td>
        </tr>
        <tr>
            <th>多输入框1</th>
            <td>
                <input type="text" name="text11" value="" />
                <input type="text" name="content1" value="" />
                <input type="text" name="text12" value="" />
                <?= HtmlCtr::getSelectCtrImp(XQuestion::getCtypeDescArray(), 'ctype1', 'Text',''); ?>
                如: [出生][1995][年][年]
            </td>
        </tr>
        <tr>
            <th>多输入框2</th>
            <td>
                <input type="text" name="text21" value="" />
                <input type="text" name="content2" value="" />
                <input type="text" name="text22" value="" />
                <?= HtmlCtr::getSelectCtrImp(XQuestion::getCtypeDescArray(), 'ctype2', 'Text',''); ?>
            </td>
        </tr>
        <tr>
            <th>多输入框3</th>
            <td>
                <input type="text" name="text31" value="" />
                <input type="text" name="content3" value="" />
                <input type="text" name="text32" value="" />
                <?= HtmlCtr::getSelectCtrImp(XQuestion::getCtypeDescArray(), 'ctype3', 'Text',''); ?>
            </td>
        </tr>
        <tr>
            <th>多输入框4</th>
            <td>
                <input type="text" name="text41" value="" />
                <input type="text" name="content4" value="" />
                <input type="text" name="text42" value="" />
                <?= HtmlCtr::getSelectCtrImp(XQuestion::getCtypeDescArray(), 'ctype4', 'Text',''); ?>
            </td>
        </tr>
        <tr>
            <th>多输入框5</th>
            <td>
                <input type="text" name="text51" value="" />
                <input type="text" name="content5" value="" />
                <input type="text" name="text52" value="" />
                <?= HtmlCtr::getSelectCtrImp(XQuestion::getCtypeDescArray(), 'ctype5', 'Text',''); ?>
            </td>
        </tr>
        <tr>
            <th>是否子问题</th>
            <td><?= HtmlCtr::getRadioCtrImp(XQuestion::getIsSubDescArray(), 'issub', '0',' '); ?> <span class="gray">(影响序号, Y => +0.1, N => +0.1; 如果问题类型是=段落,且不想显示序号,则选Y)</span>
            </td>
        </tr>
        <tr>
            <th>是否必填</th>
            <td><?= HtmlCtr::getRadioCtrImp(XQuestion::getMustDescArray(), 'ismust', '0',' '); ?> </td>
        </tr>
        <tr>
            <th>最小值</th>
            <td>
                <input type="text" name="minvalue" value="" />
                <span class="gray">数值型的正常范围下限 或 复选题最少选中数</span>
            </td>
        </tr>
        <tr>
            <th>最大值</th>
            <td>
                <input type="text" name="maxvalue" value="" />
                <span class="gray">数值型的正常范围上限 或 复选题最多选中数</span>
            </td>
        </tr>
        <tr>
            <th>快捷备选项</th>
            <td>
                <textarea rows="3" cols="100" name="optionstrs"></textarea>
                <br />
                注: 供选择题快捷创建备选项(请检查是否选择题), 竖线分隔备选项,如: 是|否
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <input type="submit" class="btn btn-success" value="提交" />
            </td>
        </tr>
    </table>
</form>
