<?php
$pagetitle = "元素修改 {$fitpagetplitem->name}  of " . $fitpagetplitem->fitpagetpl->name;
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <section class="col-md-10">
                <form method="post" action="/fitpagetplitemmgr/modifypost">
                    <input type="hidden" name="fitpagetplitemid" value="<?=$fitpagetplitem->id ?>" />
                    <input type="hidden" name="fitpagetplid" value="<?=$fitpagetplitem->fitpagetplid ?>" />
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <tr>
                            <th width="90">code</th>
                            <td>
                                <?=$fitpagetplitem->code?>
                                <?php
                                    if($myauditor->isSuperman()) {
                                        ?>
                                			<input name="code" value="<?=$fitpagetplitem->code ?>">
                                		<?php
                                    }
                                ?>
                    		          </td>
                        </tr>
                        <tr>
                            <th>pos</th>
                            <td>
                                <input name="pos" value="<?=$fitpagetplitem->pos ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>name</th>
                            <td>
                                <input name="name" value="<?=$fitpagetplitem->name ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>remark</th>
                            <td>
                                <textarea name="remark" rows="10" cols="50"><?=$fitpagetplitem->remark ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="submit" value="提交">
                            </td>
                        </tr>
                    </table>
                    </div>
                </form>
    </section>
</div>
<div class="clear"></div>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
