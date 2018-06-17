<?php
$pagetitle = "RevisitTkt 修改 ";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<!DOCTYPE html>
    <div class="col-md-12">
        <section class="col-md-12">
    <form method="post" action="/revisittktmgr/modifypost">
        <input type="hidden" name="revisittktid" value="<?=$revisittkt->id ?>" />
        <div class="table-responsive">
            <table class="table table-bordered">
                    <tr>
                        <th width="90">患者</th>
                        <td>
                            <?=$revisittkt->patient->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>所属医生</th>
                        <td>
                            <?=$revisittkt->doctor->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>预约时间</th>
                        <td>
                            <?=$revisittkt->thedate?>
                        </td>
                    </tr>
                    <tr>
                        <th>审核状态</th>
                        <td>
                        	<?php
                        if ($revisittkt->auditstatus == 0) {
                            $arr = array(
                                '0' => '待审核',
                                '1' => '通过');
                        } elseif ($revisittkt->auditstatus == 1) {
                            $arr = array(
                                '1' => '已通过',
                                '2' => '拒绝');
                        } elseif ($revisittkt->auditstatus == 2) {
                            $arr = array(
                                '1' => '通过',
                                '2' => '已拒绝');
                        }
                        echo HtmlCtr::getRadioCtrImp($arr, 'auditstatus', $revisittkt->auditstatus, "");
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <th>审核备注</th>
                        <td>
                            <textarea name="auditremark" rows="5" cols="50"><?=$revisittkt->auditremark ?></textarea>
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
