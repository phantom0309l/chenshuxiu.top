<?php
$pagetitle = "错误日志列表 Xerrlog";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="table-responsive">
            <form action="/xerrlogmgr/modifyPost" method="post">
                <input type="hidden" name="xerrlogid" value="<?= $xerrlog->id ?>" />
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>属性</th>
                            <th>值</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="100">id</td>
                            <td><?= $xerrlog->id ?></td>
                        </tr>
                        <tr>
                            <td>createtime</td>
                            <td><?= $xerrlog->createtime ?></td>
                        </tr>
                        <tr>
                            <td>version</td>
                            <td><?= $xerrlog->version ?></td>
                        </tr>
                        <tr>
                            <td>xunitofworkid</td>
                            <td><?= $xerrlog->xunitofworkid; ?></td>
                        </tr>
                        <tr>
                            <td>状态</td>
                            <td><?= $xerrlog->getStatusDesc() ?></td>
                        </tr>
                        <tr>
                            <td>内容</td>
                            <td>
                                <pre><?= $xerrlog->content ?></pre>
                            </td>
                        </tr>
                        <tr>
                            <td>责任人</td>
                            <td>
                            <?= HtmlCtr::getSelectCtrImp(CtrHelper::getTechAuditorCtrArray(true), 'auditorid', $xerrlog->auditorid, " "); ?>
                        </td>
                        </tr>
                        <tr>
                            <td>工程师备注</td>
                            <td>
                                状态: <?= HtmlCtr::getRadioCtrImp(Xerrlog::getStatuss(), 'status', $xerrlog->status, " "); ?>
                                <br />
                                <textarea name="remark" rows="6" cols="80"><?= $xerrlog->remark ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <input type="submit" class="btn btn-success" value="提交" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
